<?php

namespace App\Controller\Worker;

use App\Repository\CustomerOrderRepository;
use App\Repository\MenuRepository;
use App\Repository\OrderStatusRepository;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_WORKER')]
#[Route('/worker', name: 'app_worker')]
final class WorkerController extends AbstractController
{
    #[Route('', name: '_dashboard', methods: ['GET'])]
    public function dashboard(
        Request $request,
        CustomerOrderRepository $customerOrderRepository,
        MenuRepository $menuRepository,
        ReviewRepository $reviewRepository,
        OrderStatusRepository $orderStatusRepository,
    ): Response {
        $activeTab = $request->query->get('tab', 'orders');
        if (!in_array($activeTab, ['orders', 'menus', 'reviews'], true)) {
            $activeTab = 'orders';
        }

        $orders = $customerOrderRepository->findBy([], ['serviceDate' => 'DESC']);
        $menus = $menuRepository->findBy([], ['id' => 'DESC']);
        $pendingReviews = $reviewRepository->findBy(['validated' => false], ['createdAt' => 'DESC']);
        $publishedReviews = $reviewRepository->findBy(['validated' => true], ['createdAt' => 'DESC']);
        $orderStatuses = $orderStatusRepository->findBy([], ['id' => 'ASC']);

        return $this->render('worker/dashboard.html.twig', [
            'activeTab' => $activeTab,
            'orders' => $orders,
            'menus' => $menus,
            'pendingReviews' => $pendingReviews,
            'publishedReviews' => $publishedReviews,
            'orderStatuses' => $orderStatuses,
        ]);
    }
}
