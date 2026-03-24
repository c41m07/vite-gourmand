<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\CustomerOrderRepository;
use App\Repository\MenuRepository;
use App\Repository\ReviewRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin', name: 'app_admin')]
final class AdminController extends AbstractController
{
    #[Route('', name: '_dashboard', methods: ['GET'])]
    public function dashboard(
        Request                 $request,
        CustomerOrderRepository $customerOrderRepository,
        MenuRepository          $menuRepository,
        ReviewRepository        $reviewRepository,
        UserRepository          $userRepository
    ): Response
    {
        $activeTab = $request->query->get('tab', 'dashboard');
        if (!in_array($activeTab, ['dashboard', 'employees'], true)) {
            $activeTab = 'dashboard';
        }

        $orders = $customerOrderRepository->findBy([], ['serviceDate' => 'DESC']);
        $menus = $menuRepository->findBy([], ['id' => 'DESC']);
        $reviews = $reviewRepository->findAll();
        $users = $userRepository->findAll();

        $employees = array_values(array_filter($users, static function (User $user): bool {
            $roles = $user->getRoles();
            return in_array('ROLE_ADMIN', $roles, true) || in_array('ROLE_WORKER', $roles, true);
        }));

        $clientsCount = count(array_filter($users, static function (User $user): bool {
            $roles = $user->getRoles();
            return !in_array('ROLE_ADMIN', $roles, true) && !in_array('ROLE_WORKER', $roles, true);
        }));

        $ordersInProgress = 0;
        foreach ($orders as $order) {
            $histories = $order->getCustomerOrderStatusHistories();
            if ($histories->count() === 0) {
                continue;
            }
            $lastHistory = $histories->last();
            if (!$lastHistory || !$lastHistory->getOrderStatus()) {
                continue;
            }
            $code = $lastHistory->getOrderStatus()->getCode();
            if (!in_array($code, ['completed', 'cancelled'], true)) {
                $ordersInProgress++;
            }
        }

        $revenueTotal = 0;
        foreach ($orders as $order) {
            $revenueTotal += (int)($order->getTotalPrice() ?? 0);
        }

        $avgRating = 0.0;
        if (count($reviews) > 0) {
            $ratingSum = 0;
            foreach ($reviews as $review) {
                $ratingSum += (int)($review->getRating() ?? 0);
            }
            $avgRating = $ratingSum / count($reviews);
        }

        $activeMenus = count(array_filter($menus, static fn($menu): bool => (bool)$menu->isActive()));

        return $this->render('admin/dashboard.html.twig', [
            'activeTab' => $activeTab,
            'orders' => $orders,
            'employees' => $employees,
            'clientsCount' => $clientsCount,
            'ordersInProgress' => $ordersInProgress,
            'revenueTotal' => $revenueTotal,
            'avgRating' => $avgRating,
            'activeMenus' => $activeMenus,
        ]);
    }
}
