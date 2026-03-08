<?php

namespace App\Controller\Order;

use App\Entity\Menu;
use App\Form\Order\OrderFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/order', name: 'app_order')]
final class OrderController extends AbstractController
{
    #[Route('/new/{id}', name: '_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Menu $menu): Response
    {
        if (!$menu->isActive()) {
            throw $this->createNotFoundException();
        }

        if (($menu->getStock() ?? 0) <= 0) {
            $this->addFlash('error', 'Ce menu n est plus disponible a la commande.');

            return $this->redirectToRoute('app_menu_index');
        }

        $user = $this->getUser();
        if (!$user) {
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
            $peopleCount = $data['peopleCount'];
            $minpeople = $menu->getMinPeople();
            $stock = $menu->getStock();

            if ($peopleCount < $minpeople || $peopleCount > $stock) {
                $form->get('peopleCount')->addError(new FormError(sprintf('Le nombre de personnes doit être compris entre %d et %d', $minpeople,
                    $stock)));
            }

            if ($form->isValid()) {
                $basePrice = $menu->getBasePrice();
                $menuSubtotal = $basePrice * $peopleCount;
                $discountAmount = 0;
                if ($peopleCount >= ($minpeople + 5)) {
                    $discountAmount = (int)round($menuSubtotal * 0.10);
                }

                $menuPrice = $menuSubtotal - $discountAmount;
                $deliveryCity = trim((string)$data['deliveryCity']);
                $distancekm = (int)($data['distancekm']);
                $isBordeaux = strtolower($deliveryCity) === 'bordeaux';
                $deliveryPrice = 0;
                if (!$isBordeaux) {
                    $deliveryPrice = 500 + ($distancekm * 59);
                }

                $totalPrice = $menuPrice + $deliveryPrice;

                $orderPreview = [
                    'firstName' => $data['firstName'] ?? '',
                    'lastName' => $data['lastName'] ?? '',
                    'email' => $data['email'] ?? '',
                    'phone' => $data['phone'] ?? '',
                    'deliveryAddress' => $data['deliveryAddress'] ?? '',
                    'deliveryCity' => $data['deliveryCity'] ?? '',
                    'deliveryPostalCode' => $data['deliveryPostalCode'] ?? '',
                    'serviceDate' => $data['serviceDate'] ?? null,
                    'serviceTime' => $data['serviceTime'] ?? '',
                    'peopleCount' => $data['peopleCount'] ?? null,
                    'menuBasePrice' => $basePrice,
                    'menuSubtotal' => $menuSubtotal,
                    'discountAmount' => $discountAmount,
                    'menuPrice' => $menuPrice,
                    'deliveryPrice' => $deliveryPrice,
                    'totalPrice' => $totalPrice,
                    'distancekm' => $distancekm,
                ];
            }
        }

        return $this->render('order/index.html.twig', [
            'menu' => $menu,
            'orderForm' => $form,
            'orderPreview' => $orderPreview,
        ]);


    }
}
