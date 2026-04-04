<?php

namespace App\Service\Admin;

use App\Dto\Admin\AdminDashboardViewDto;
use App\Entity\CustomerOrder;
use App\Entity\Menu;
use App\Entity\Review;
use App\Entity\User;
use App\Repository\CustomerOrderRepository;
use App\Repository\MenuRepository;
use App\Repository\ReviewRepository;
use App\Repository\UserRepository;

final readonly class AdminDashboardViewBuilder
{
    private const DEFAULT_TAB = 'dashboard';

    /**
     * @var list<string>
     */
    private const ALLOWED_TABS = ['dashboard', 'employees'];

    public function __construct(
        private CustomerOrderRepository $customerOrderRepository,
        private MenuRepository $menuRepository,
        private ReviewRepository $reviewRepository,
        private UserRepository $userRepository,
    ) {
    }

    public function build(?string $requestedTab): AdminDashboardViewDto
    {
        $activeTab = $this->resolveActiveTab($requestedTab);
        $orders = $this->customerOrderRepository->findBy([], ['serviceDate' => 'DESC']);
        $menus = $this->menuRepository->findBy([], ['id' => 'DESC']);
        $reviews = $this->reviewRepository->findAll();
        $users = $this->userRepository->findAll();
        $employees = $this->filterEmployees($users);

        return new AdminDashboardViewDto(
            activeTab: $activeTab,
            employees: $employees,
            recentOrders: array_slice($orders, 0, 4),
            totalOrders: count($orders),
            clientsCount: $this->countClients($users),
            ordersInProgress: $this->countOrdersInProgress($orders),
            revenueTotal: $this->calculateRevenueTotal($orders),
            avgRating: $this->calculateAverageRating($reviews),
            activeMenus: $this->countActiveMenus($menus),
        );
    }

    private function resolveActiveTab(?string $requestedTab): string
    {
        if (null !== $requestedTab && in_array($requestedTab, self::ALLOWED_TABS, true)) {
            return $requestedTab;
        }

        return self::DEFAULT_TAB;
    }

    /**
     * @param list<User> $users
     *
     * @return list<User>
     */
    private function filterEmployees(array $users): array
    {
        return array_values(array_filter(
            $users,
            fn (User $user): bool => $this->isEmployee($user)
        ));
    }

    /**
     * @param list<User> $users
     */
    private function countClients(array $users): int
    {
        return count(array_filter(
            $users,
            fn (User $user): bool => !$this->isEmployee($user)
        ));
    }

    /**
     * @param list<CustomerOrder> $orders
     */
    private function countOrdersInProgress(array $orders): int
    {
        return count(array_filter(
            $orders,
            fn (CustomerOrder $order): bool => $this->isOrderInProgress($order)
        ));
    }

    /**
     * @param list<CustomerOrder> $orders
     */
    private function calculateRevenueTotal(array $orders): int
    {
        return array_sum(array_map(
            static fn (CustomerOrder $order): int => (int) ($order->getTotalPrice() ?? 0),
            $orders
        ));
    }

    /**
     * @param list<Review> $reviews
     */
    private function calculateAverageRating(array $reviews): float
    {
        if ([] === $reviews) {
            return 0.0;
        }

        $ratingSum = array_sum(array_map(
            static fn (Review $review): int => $review->getNormalizedRating(),
            $reviews
        ));

        return $ratingSum / count($reviews);
    }

    /**
     * @param list<Menu> $menus
     */
    private function countActiveMenus(array $menus): int
    {
        return count(array_filter(
            $menus,
            static fn (Menu $menu): bool => (bool) $menu->isActive()
        ));
    }

    private function isEmployee(User $user): bool
    {
        $roles = $user->getRoles();

        return in_array('ROLE_ADMIN', $roles, true) || in_array('ROLE_WORKER', $roles, true);
    }

    private function isOrderInProgress(CustomerOrder $order): bool
    {
        $statusCode = $order->getLatestStatusHistory()?->getOrderStatus()?->getCode();
        if (null === $statusCode) {
            return false;
        }

        return !in_array($statusCode, ['completed', 'cancelled'], true);
    }
}
