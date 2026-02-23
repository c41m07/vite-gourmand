<?php

namespace App\Controller\User;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileController extends AbstractController
{
    #[Route('/user/profile', name: 'app_user_profile', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $activeTab = $request->query->get('tab', 'orders');
        if (!in_array($activeTab, ['orders', 'profile', 'reviews'], true)) {
            $activeTab = 'orders';
        }

        $orders = [];
        $reviews = [];
        $user = $this->getUser();
        if ($user instanceof User) {
            $orders = $user->getCustomerOrders();
            $reviews = $user->getReviews();
        }

        return $this->render('user/account.html.twig', [
            'activeTab' => $activeTab,
            'orders' => $orders,
            'reviews' => $reviews,
        ]);
    }
}
