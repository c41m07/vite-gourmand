<?php

namespace App\Service\User;

use App\Dto\User\ProfileAccountViewDto;
use App\Entity\CustomerOrder;
use App\Entity\User;

final class ProfileAccountViewBuilder
{
    private const DEFAULT_TAB = 'orders';

    /**
     * @var list<string>
     */
    private const ALLOWED_TABS = ['orders', 'profile', 'reviews'];

    public function build(User $user, ?string $requestedTab, ?string $requestedStatus): ProfileAccountViewDto
    {
        $activeTab = $this->resolveActiveTab($requestedTab);
        $orders = $user->getCustomerOrders()->toArray();

        usort($orders, static function (CustomerOrder $left, CustomerOrder $right): int {
            $leftOrderedAt = $left->getOrderedAt()?->getTimestamp() ?? 0;
            $rightOrderedAt = $right->getOrderedAt()?->getTimestamp() ?? 0;

            if ($leftOrderedAt === $rightOrderedAt) {
                return ($right->getId() ?? 0) <=> ($left->getId() ?? 0);
            }

            return $rightOrderedAt <=> $leftOrderedAt;
        });

        $orderStatusFilters = $this->buildOrderStatusFilters($orders);
        $orderStatusFilter = $this->resolveOrderStatusFilter($requestedStatus, $orderStatusFilters);

        if ('all' !== $orderStatusFilter) {
            $orders = array_values(array_filter(
                $orders,
                static fn (CustomerOrder $order): bool => $order->getCurrentStatusCode() === $orderStatusFilter
            ));
        }

        return new ProfileAccountViewDto(
            activeTab: $activeTab,
            orders: $orders,
            orderStatusFilter: $orderStatusFilter,
            orderStatusFilters: $orderStatusFilters,
            reviews: $user->getReviews(),
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
     * @param list<CustomerOrder> $orders
     *
     * @return array<string, array{label: string, count: int}>
     */
    private function buildOrderStatusFilters(array $orders): array
    {
        $filters = [
            'all' => [
                'label' => 'Toutes',
                'count' => count($orders),
            ],
        ];

        foreach ($orders as $order) {
            $statusCode = $order->getCurrentStatusCode() ?? 'unknown';

            if (!isset($filters[$statusCode])) {
                $filters[$statusCode] = [
                    'label' => $order->getCurrentStatusLabel(),
                    'count' => 0,
                ];
            }

            ++$filters[$statusCode]['count'];
        }

        return $filters;
    }

    /**
     * @param array<string, array{label: string, count: int}> $availableFilters
     */
    private function resolveOrderStatusFilter(?string $requestedStatus, array $availableFilters): string
    {
        $requestedStatus ??= 'all';

        if (isset($availableFilters[$requestedStatus])) {
            return $requestedStatus;
        }

        return 'all';
    }
}
