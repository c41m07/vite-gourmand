<?php

namespace App\Handler\Order;

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
use App\Service\Order\OrderPricingCalculator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Mailer\MailerInterface;

final readonly class CreateOrderHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private OrderStatusRepository $orderStatusRepository,
        private EquipmentLoanStatusRepository $equipmentLoanStatusRepository,
        private OrderPricingCalculator $orderPricingCalculator,
        private MailerInterface $mailer,
        private EmailFactoryService $emailFactory,
    ) {
    }

    public function handle(FormInterface $form, Menu $menu, User $user): ?CustomerOrder
    {
        $data = $form->getData();
        if (!is_array($data)) {
            throw new \RuntimeException('Les données de commande sont invalides.');
        }

        $peopleCount = (int) ($data['peopleCount'] ?? 0);
        $needEquipmentLoan = (bool) $form->get('needEquipmentLoan')->getData();

        $this->validatePeopleCount($form, $menu, $peopleCount);
        [$loanStartAt, $loanEndAt] = $this->validateEquipmentLoan($form, $needEquipmentLoan);

        if (!$form->isValid()) {
            return null;
        }

        $serviceDate = $data['serviceDate'] ?? null;
        if (!$serviceDate instanceof \DateTimeInterface) {
            throw new \RuntimeException('La date de service est invalide.');
        }

        $deliveryCity = trim((string) ($data['deliveryCity'] ?? ''));
        $pricing = $this->orderPricingCalculator->calculate(
            $menu,
            $peopleCount,
            $deliveryCity,
            (int) ($data['distancekm'] ?? 0),
        );

        $equipmentLoan = $needEquipmentLoan
            ? $this->createEquipmentLoan(
                $loanStartAt,
                $loanEndAt,
                $form->get('equipmentLoanNote')->getData(),
            )
            : null;

        $now = new \DateTime();
        $order = (new CustomerOrder())
            ->setUser($user)
            ->setOrderedAt(clone $now)
            ->setServiceDate(\DateTime::createFromInterface($serviceDate))
            ->setserviceTime((string) ($data['serviceTime'] ?? ''))
            ->setPeopleCount($pricing->peopleCount)
            ->setPhone($data['phone'] ?? null)
            ->setDeliveryAddress((string) ($data['deliveryAddress'] ?? ''))
            ->setDeliveryCity($deliveryCity)
            ->setDeliveryPostalCode((string) ($data['deliveryPostalCode'] ?? ''))
            ->setDeliveryPrice($pricing->deliveryPrice)
            ->setDiscountAmount($pricing->discountAmount)
            ->setTotalPrice($pricing->totalPrice)
            ->setNote($data['note'] ?? null)
            ->setCreatedAt(clone $now)
            ->setUpdatedAt(clone $now)
            ->setEquipmentLoan($equipmentLoan);

        $orderMenu = (new CustomerOrderMenu())
            ->setCustomerOrder($order)
            ->setMenu($menu)
            ->setQuantity(1)
            ->setUnitPrice($pricing->unitPrice)
            ->setLineTotal($pricing->menuSubtotal);

        $statusHistory = (new CustomerOrderStatusHistory())
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
            $this->emailFactory->createOrderConfirmationEmail($order, (string) $menu->getTitle())
        );

        return $order;
    }

    private function validatePeopleCount(FormInterface $form, Menu $menu, int $peopleCount): void
    {
        $minimumPeople = (int) ($menu->getMinPeople() ?? 0);
        $stock = (int) ($menu->getStock() ?? 0);

        if ($peopleCount < $minimumPeople || $peopleCount > $stock) {
            $form->get('peopleCount')->addError(new FormError(sprintf(
                'Le nombre de personnes doit être compris entre %d et %d.',
                $minimumPeople,
                $stock,
            )));
        }
    }

    /**
     * @return array{0: ?\DateTimeInterface, 1: ?\DateTimeInterface}
     */
    private function validateEquipmentLoan(FormInterface $form, bool $needEquipmentLoan): array
    {
        if (!$needEquipmentLoan) {
            return [null, null];
        }

        $loanStartAt = $form->get('equipmentLoanStartAt')->getData();
        $loanEndAt = $form->get('equipmentLoanEndAt')->getData();

        if (!$loanStartAt instanceof \DateTimeInterface) {
            $form->get('equipmentLoanStartAt')->addError(new FormError('La date de début du prêt est obligatoire.'));
        }

        if (!$loanEndAt instanceof \DateTimeInterface) {
            $form->get('equipmentLoanEndAt')->addError(new FormError('La date de fin du prêt est obligatoire.'));
        }

        if ($loanStartAt instanceof \DateTimeInterface && $loanEndAt instanceof \DateTimeInterface && $loanEndAt < $loanStartAt) {
            $form->get('equipmentLoanEndAt')->addError(new FormError('La fin du prêt doit être postérieure au début du prêt.'));
        }

        return [$loanStartAt, $loanEndAt];
    }

    private function createEquipmentLoan(
        ?\DateTimeInterface $loanStartAt,
        ?\DateTimeInterface $loanEndAt,
        mixed $loanNote,
    ): EquipmentLoan {
        if (!$loanStartAt instanceof \DateTimeInterface || !$loanEndAt instanceof \DateTimeInterface) {
            throw new \RuntimeException('Les dates du prêt de matériel sont invalides.');
        }

        return (new EquipmentLoan())
            ->setLoanStartAt(\DateTime::createFromInterface($loanStartAt))
            ->setLoanEndAt(\DateTime::createFromInterface($loanEndAt))
            ->setNote(is_string($loanNote) ? $loanNote : null)
            ->setStatus($this->getBorrowedEquipmentLoanStatus());
    }

    private function getPendingStatus(): OrderStatus
    {
        $pendingStatus = $this->orderStatusRepository->findOneBy(['code' => 'pending']);
        if (null === $pendingStatus) {
            throw new \RuntimeException('Pending order status not found');
        }

        return $pendingStatus;
    }

    private function getBorrowedEquipmentLoanStatus(): EquipmentLoanStatus
    {
        $equipmentLoanStatus = $this->equipmentLoanStatusRepository->findOneBy(['status' => 'Emprunte']);
        if (null === $equipmentLoanStatus) {
            throw new \RuntimeException('Equipment loan status not found');
        }

        return $equipmentLoanStatus;
    }
}
