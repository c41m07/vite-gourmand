<?php

namespace App\Handler\Order;

use App\Dto\Order\OrderPreviewViewDto;
use App\Entity\CustomerOrder;
use App\Entity\CustomerOrderMenu;
use App\Entity\CustomerOrderStatusHistory;
use App\Entity\EquipmentLoan;
use App\Entity\EquipmentLoanStatus;
use App\Entity\Menu;
use App\Entity\OrderStatus;
use App\Entity\User;
use App\Repository\EquipmentLoanStatusRepository;
use App\Repository\OrderStatusRepository;
use App\Service\Mail\EmailFactoryService;
use App\Service\Order\OrderPricingService;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Mailer\MailerInterface;

final readonly class CreateOrderHandler
{
    public function __construct(
        private EntityManagerInterface        $entityManager,
        private OrderStatusRepository         $orderStatusRepository,
        private EquipmentLoanStatusRepository $equipmentLoanStatusRepository,
        private OrderPricingService           $orderPricingService,
        private MailerInterface               $mailer,
        private EmailFactoryService           $emailFactory,
    )
    {
    }

    public function handle(FormInterface $form, Menu $menu, User $user): ?CustomerOrder
    {
        $prepared = $this->prepareOrderData($form, $menu);

        if (null === $prepared) {
            return null;
        }

        /** @var CustomerOrder $order */
        $order = $prepared['order'];
        $pricing = $prepared['pricing'];
        $needEquipmentLoan = $prepared['needEquipmentLoan'];
        $loanStartAt = $prepared['loanStartAt'];
        $loanEndAt = $prepared['loanEndAt'];

        $equipmentLoan = $needEquipmentLoan
            ? $this->createEquipmentLoan(
                $loanStartAt,
                $loanEndAt,
                $form->get('equipmentLoanNote')->getData(),
            )
            : null;

        $now = new DateTime();
        $order
            ->setUser($user)
            ->setOrderedAt(clone $now)
            ->setPeopleCount($pricing->peopleCount)
            ->setPhone($this->normalizeNullableString($order->getPhone()))
            ->setDeliveryAddress(trim((string)$order->getDeliveryAddress()))
            ->setDeliveryCity($prepared['deliveryCity'])
            ->setDeliveryPostalCode(trim((string)$order->getDeliveryPostalCode()))
            ->setDeliveryPrice($pricing->deliveryPrice)
            ->setDiscountAmount($pricing->discountAmount)
            ->setTotalPrice($pricing->totalPrice)
            ->setNote($this->normalizeNullableString($order->getNote()))
            ->setCreatedAt(clone $now)
            ->setUpdatedAt(clone $now)
            ->setEquipmentLoan($equipmentLoan);

        $orderMenu = new CustomerOrderMenu()
            ->setCustomerOrder($order)
            ->setMenu($menu)
            ->setQuantity(1)
            ->setUnitPrice($pricing->unitPrice)
            ->setLineTotal($pricing->menuSubtotal);

        $statusHistory = new CustomerOrderStatusHistory()
            ->setCustomerOrder($order)
            ->setOrderStatus($this->getPendingStatus())
            ->setChangedByUser($user)
            ->setChangedAt(clone $now)
            ->setComment('Commande créée depuis le formulaire client.');

        if (null !== $equipmentLoan) {
            $this->entityManager->persist($equipmentLoan);
        }

        $this->entityManager->persist($order);
        $this->entityManager->persist($orderMenu);
        $this->entityManager->persist($statusHistory);
        $this->entityManager->flush();

        $this->mailer->send(
            $this->emailFactory->createOrderConfirmationEmail($order, (string)$menu->getTitle())
        );

        return $order;
    }

    private function prepareOrderData(FormInterface $form, Menu $menu): ?array
    {
        $order = $form->getData();

        if (!$order instanceof CustomerOrder) {
            throw new RuntimeException('Les données de commande sont invalides.');
        }

        $peopleCount = (int)($order->getPeopleCount() ?? 0);
        $needEquipmentLoan = (bool)$form->get('needEquipmentLoan')->getData();

        $this->validatePeopleCount($form, $menu, $peopleCount);
        [$loanStartAt, $loanEndAt] = $this->validateEquipmentLoan($form, $needEquipmentLoan);

        if (!$form->isValid()) {
            return null;
        }

        $serviceDate = $order->getServiceDate();
        if (!$serviceDate instanceof DateTimeInterface) {
            throw new RuntimeException('La date de service est invalide.');
        }

        $deliveryCity = trim((string)($order->getDeliveryCity() ?? ''));
        $distanceKm = (int)($form->get('distancekm')->getData() ?? 0);
        $pricing = $this->orderPricingService->calculate(
            $menu,
            $peopleCount,
            $deliveryCity,
            $distanceKm,
            $needEquipmentLoan,
        );

        return [
            'order' => $order,
            'pricing' => $pricing,
            'needEquipmentLoan' => $needEquipmentLoan,
            'loanStartAt' => $loanStartAt instanceof DateTimeInterface ? DateTime::createFromInterface($loanStartAt) : null,
            'loanEndAt' => $loanEndAt instanceof DateTimeInterface ? DateTime::createFromInterface($loanEndAt) : null,
            'deliveryCity' => $deliveryCity,
            'serviceDate' => DateTime::createFromInterface($serviceDate),
        ];
    }

    private function validatePeopleCount(FormInterface $form, Menu $menu, int $peopleCount): void
    {
        $minimumPeople = (int)($menu->getMinPeople() ?? 0);
        $stock = (int)($menu->getStock() ?? 0);

        if ($peopleCount < $minimumPeople || $peopleCount > $stock) {
            $form->get('peopleCount')->addError(new FormError(sprintf(
                'Le nombre de personnes doit être compris entre %d et %d.',
                $minimumPeople,
                $stock,
            )));
        }
    }

    /**
     * @return array{0: ?DateTimeInterface, 1: ?DateTimeInterface}
     */
    private function validateEquipmentLoan(FormInterface $form, bool $needEquipmentLoan): array
    {
        if (!$needEquipmentLoan) {
            return [null, null];
        }

        $loanStartAt = $form->get('equipmentLoanStartAt')->getData();
        $loanEndAt = $form->get('equipmentLoanEndAt')->getData();

        if (!$loanStartAt instanceof DateTimeInterface) {
            $form->get('equipmentLoanStartAt')->addError(new FormError('La date de début du prêt est obligatoire.'));
        }

        if (!$loanEndAt instanceof DateTimeInterface) {
            $form->get('equipmentLoanEndAt')->addError(new FormError('La date de fin du prêt est obligatoire.'));
        }

        if ($loanStartAt instanceof DateTimeInterface && $loanEndAt instanceof DateTimeInterface && $loanEndAt < $loanStartAt) {
            $form->get('equipmentLoanEndAt')->addError(new FormError('La fin du prêt doit être postérieure au début du prêt.'));
        }

        return [$loanStartAt, $loanEndAt];
    }

    private function createEquipmentLoan(
        ?DateTimeInterface $loanStartAt,
        ?DateTimeInterface $loanEndAt,
        mixed              $loanNote,
    ): EquipmentLoan
    {
        if (!$loanStartAt instanceof DateTimeInterface || !$loanEndAt instanceof DateTimeInterface) {
            throw new RuntimeException('Les dates du prêt de matériel sont invalides.');
        }

        return (new EquipmentLoan())
            ->setLoanStartAt(DateTime::createFromInterface($loanStartAt))
            ->setLoanEndAt(DateTime::createFromInterface($loanEndAt))
            ->setNote($this->normalizeNullableString($loanNote))
            ->setStatus($this->getBorrowedEquipmentLoanStatus());
    }

    private function normalizeNullableString(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        $normalizedValue = trim($value);

        return '' === $normalizedValue ? null : $normalizedValue;
    }

    private function getBorrowedEquipmentLoanStatus(): EquipmentLoanStatus
    {
        $equipmentLoanStatus = $this->equipmentLoanStatusRepository->findOneBy(['status' => 'Emprunte']);
        if (null === $equipmentLoanStatus) {
            throw new RuntimeException('Le statut de prêt de matériel est introuvable.');
        }

        return $equipmentLoanStatus;
    }

    private function getPendingStatus(): OrderStatus
    {
        $pendingStatus = $this->orderStatusRepository->findOneBy(['code' => 'pending']);
        if (null === $pendingStatus) {
            throw new RuntimeException('Le statut initial de commande est introuvable.');
        }

        return $pendingStatus;
    }

    public function preview(FormInterface $form, Menu $menu): ?OrderPreviewViewDto
    {
        $prepared = $this->prepareOrderData($form, $menu);

        if (null === $prepared) {
            return null;
        }

        /** @var CustomerOrder $order */
        $order = $prepared['order'];
        $pricing = $prepared['pricing'];

        return new OrderPreviewViewDto(
            phone: $this->normalizeNullableString($order->getPhone()),
            deliveryAddress: trim((string)$order->getDeliveryAddress()),
            deliveryPostalCode: trim((string)$order->getDeliveryPostalCode()),
            deliveryCity: $prepared['deliveryCity'],
            serviceDate: $prepared['serviceDate'],
            serviceTime: (string)($order->getserviceTime() ?? ''),
            peopleCount: $pricing->peopleCount,
            note: $this->normalizeNullableString($order->getNote()),
            menuSubtotal: $pricing->menuSubtotal,
            discountAmount: $pricing->discountAmount,
            deliveryPrice: $pricing->deliveryPrice,
            totalPrice: $pricing->totalPrice,
            needEquipmentLoan: $prepared['needEquipmentLoan'],
            equipmentLoanStartAt: $prepared['loanStartAt'],
            equipmentLoanEndAt: $prepared['loanEndAt'],
            equipmentLoanNote: $this->normalizeNullableString($form->get('equipmentLoanNote')->getData()),
        );
    }
}
