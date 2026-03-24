<?php

namespace App\Controller\Order;

use App\Entity\CustomerOrder;
use App\Entity\CustomerOrderMenu;
use App\Entity\CustomerOrderStatusHistory;
use App\Entity\EquipmentLoan;
use App\Entity\Menu;
use App\Entity\User;
use App\Form\Order\OrderFormType;
use App\Repository\EquipmentLoanStatusRepository;
use App\Repository\OrderStatusRepository;
use App\Service\Mail\EmailFactory;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted('ROLE_USER')]
#[Route('/order', name: 'app_order')]
final class OrderController extends AbstractController
{
    #[Route('/new/{id}', name: '_new', methods: ['GET', 'POST'])]
    public function new(Request                       $request,
                        Menu                          $menu,
                        EntityManagerInterface        $em,
                        OrderStatusRepository         $orderStatusRepository,
                        EquipmentLoanStatusRepository $equipmentLoanStatusRepository,
                        MailerInterface               $mailer,
                        EmailFactory                  $factory): Response
    {
        if (!$menu->isActive()) {
            throw $this->createNotFoundException();
        }

        if (($menu->getStock() ?? 0) <= 0) {
            $this->addFlash('error', 'Ce menu n est plus disponible a la commande.');

            return $this->redirectToRoute('app_menu_index');
        }

        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(OrderFormType::class, null, [
            'user' => $user,
            'menu' => $menu
        ]);


        $form->handleRequest($request);
        $orderPreview = null;

        if ($form->isSubmitted()) {
            $data = $form->getData();
            $peopleCount = (int)($data['peopleCount'] ?? 0);
            $minpeople = (int)$menu->getMinPeople();
            $stock = (int)$menu->getStock();
            $needEquipmentLoan = (bool)$form->get('needEquipmentLoan')->getData();

            if ($peopleCount < $minpeople || $peopleCount > $stock) {
                $form->get('peopleCount')->addError(new FormError(sprintf('Le nombre de personnes doit etre compris entre %d et %d', $minpeople,
                    $stock)));
            }

            if ($needEquipmentLoan) {
                $loanStartAt = $form->get('equipmentLoanStartAt')->getData();
                $loanEndAt = $form->get('equipmentLoanEndAt')->getData();

                if (!$loanStartAt instanceof DateTimeInterface) {
                    $form->get('equipmentLoanStartAt')->addError(new FormError('La date de debut du pret est obligatoire.'));
                }

                if (!$loanEndAt instanceof DateTimeInterface) {
                    $form->get('equipmentLoanEndAt')->addError(new FormError('La date de fin du pret est obligatoire.'));
                }

                if ($loanStartAt instanceof DateTimeInterface && $loanEndAt instanceof DateTimeInterface && $loanEndAt < $loanStartAt) {
                    $form->get('equipmentLoanEndAt')->addError(new FormError('La fin du pret doit etre posterieure au debut du pret.'));
                }
            }

            if ($form->isValid()) {
                $basePrice = (int)($menu->getBasePrice() ?? 0);
                $menuSubtotal = $basePrice * $peopleCount;
                $discountAmount = 0;
                if ($peopleCount >= ($minpeople + 5)) {
                    $discountAmount = (int)round($menuSubtotal * 0.10);
                }

                $menuPrice = $menuSubtotal - $discountAmount;
                $deliveryCity = trim((string)($data['deliveryCity'] ?? ''));
                $distancekm = (int)($data['distancekm'] ?? 0);
                $isBordeaux = mb_strtolower($deliveryCity) === 'bordeaux';
                $deliveryPrice = 0;
                if (!$isBordeaux) {
                    $deliveryPrice = 500 + ($distancekm * 59);
                }

                $totalPrice = $menuPrice + $deliveryPrice;

                $pendingStatus = $orderStatusRepository->findOneBy(['code' => 'pending']);
                if ($pendingStatus === null) {
                    throw new RuntimeException('Pending order status not found');
                }

                $equipmentLoan = null;
                if ($needEquipmentLoan) {
                    $equipmentLoanStatus = $equipmentLoanStatusRepository->findOneBy(['status' => 'Emprunte']);
                    if ($equipmentLoanStatus === null) {
                        throw new RuntimeException('Equipment loan status not found');
                    }

                    $loanStartAt = $form->get('equipmentLoanStartAt')->getData();
                    $loanEndAt = $form->get('equipmentLoanEndAt')->getData();

                    if (!$loanStartAt instanceof DateTimeInterface || !$loanEndAt instanceof DateTimeInterface) {
                        throw new RuntimeException('Equipment loan dates are invalid');
                    }

                    $equipmentLoan = (new EquipmentLoan())
                        ->setLoanStartAt(DateTime::createFromInterface($loanStartAt))
                        ->setLoanEndAt(DateTime::createFromInterface($loanEndAt))
                        ->setNote($form->get('equipmentLoanNote')->getData())
                        ->setStatus($equipmentLoanStatus);
                }

                $serviceDate = $data['serviceDate'] ?? null;
                if (!$serviceDate instanceof DateTimeInterface) {
                    throw new RuntimeException('La date de service est invalide');
                }

                $now = new DateTime();
                $order = (new CustomerOrder())
                    ->setUser($user)
                    ->setOrderedAt(clone $now)
                    ->setServiceDate(DateTime::createFromInterface($serviceDate))
                    ->setserviceTime((string)($data['serviceTime'] ?? ''))
                    ->setPeopleCount($peopleCount)
                    ->setPhone($data['phone'] ?? null)
                    ->setDeliveryAddress((string)($data['deliveryAddress'] ?? ''))
                    ->setDeliveryCity($deliveryCity)
                    ->setDeliveryPostalCode((string)($data['deliveryPostalCode'] ?? ''))
                    ->setDeliveryPrice($deliveryPrice)
                    ->setDiscountAmount($discountAmount)
                    ->setTotalPrice($totalPrice)
                    ->setNote($data['note'] ?? null)
                    ->setCreatedAt(clone $now)
                    ->setUpdatedAt(clone $now)
                    ->setEquipmentLoan($equipmentLoan);

                $orderMenu = (new CustomerOrderMenu())
                    ->setCustomerOrder($order)
                    ->setMenu($menu)
                    ->setQuantity(1)
                    ->setUnitPrice($basePrice)
                    ->setLineTotal($menuSubtotal);

                $statushistory = (new CustomerOrderStatusHistory())
                    ->setCustomerOrder($order)
                    ->setOrderStatus($pendingStatus)
                    ->setChangedByUser($user)
                    ->setChangedAt(clone $now)
                    ->setComment('Commande creee depuis le formulaire client.');

                if ($equipmentLoan !== null) {
                    $em->persist($equipmentLoan);
                }
                $em->persist($order);
                $em->persist($orderMenu);
                $em->persist($statushistory);
                $em->flush();
                $mailer->send($factory->createOrderConfirmationEmail($order, (string)$menu->getTitle()));
                $this->addFlash('success', 'Votre commande a bien ete enregistree. Nous vous remercions de votre confiance.');
                return $this->redirectToRoute('app_user_profile', ['tab' => 'orders']);
            }
        }

        return $this->render('order/index.html.twig', [
            'menu' => $menu,
            'orderForm' => $form,
            'orderPreview' => $orderPreview,
        ]);
    }
}
