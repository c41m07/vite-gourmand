<?php

namespace App\Controller\Order;

use App\Entity\CustomerOrder;
use App\Entity\Menu;
use App\Entity\User;
use App\Form\Order\OrderFormType;
use App\Handler\Order\CreateOrderHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/order', name: 'app_order')]
final class OrderController extends AbstractController
{
    #[Route('/new/{id}', name: '_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Menu $menu, CreateOrderHandler $createOrderHandler): Response
    {
        if (!$menu->isActive()) {
            throw $this->createNotFoundException();
        }

        if (($menu->getStock() ?? 0) <= 0) {
            $this->addFlash('error', 'Ce menu n\'est plus disponible à la commande.');

            return $this->redirectToRoute('app_menu_index');
        }

        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(OrderFormType::class, null, [
            'user' => $user,
            'menu' => $menu,
        ]);

        $form->handleRequest($request);
        $orderPreview = null;

        if ($form->isSubmitted()) {
            $order = $createOrderHandler->handle($form, $menu, $user);

            if ($order instanceof CustomerOrder) {
                $this->addFlash('success', 'Votre commande a bien été enregistrée. Nous vous remercions de votre confiance.');

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
