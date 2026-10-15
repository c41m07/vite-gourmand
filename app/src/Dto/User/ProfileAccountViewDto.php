<?php

namespace App\Dto\User;

use App\Entity\CustomerOrder;

final readonly class ProfileAccountViewDto
{
    /**
     * @param list<CustomerOrder>                             $orders
     * @param array<string, array{label: string, count: int}> $orderStatusFilters
     */
    public function __construct(
        public string $activeTab,
        public array $orders,
        public string $orderStatusFilter,
        public array $orderStatusFilters,
        public iterable $reviews,
    ) {
    }
}
