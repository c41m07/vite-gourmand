<?php

namespace App\Dto\Admin;

use App\Entity\CustomerOrder;
use App\Entity\User;

final readonly class AdminDashboardViewDto
{
    /**
     * @param list<User>          $employees
     * @param list<CustomerOrder> $recentOrders
     */
    public function __construct(
        public string $activeTab,
        public array $employees,
        public array $recentOrders,
        public int $totalOrders,
        public int $clientsCount,
        public int $ordersInProgress,
        public int $revenueTotal,
        public float $avgRating,
        public int $activeMenus,
    ) {
    }
}
