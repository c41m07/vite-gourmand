<?php

namespace App\Service\Order;

use App\Dto\Order\OrderPricingDataDto;
use App\Entity\Menu;

final class OrderPricingCalculator
{
    public function calculate(Menu $menu, int $peopleCount, string $deliveryCity, int $distanceKm): OrderPricingDataDto
    {
        $unitPrice = (int) ($menu->getBasePrice() ?? 0);
        $minimumPeople = (int) ($menu->getMinPeople() ?? 0);
        $normalizedPeopleCount = max(0, $peopleCount);
        $menuSubtotal = $unitPrice * $normalizedPeopleCount;
        $discountAmount = 0;

        if ($normalizedPeopleCount >= ($minimumPeople + 5)) {
            $discountAmount = (int) round($menuSubtotal * 0.10);
        }

        $normalizedDeliveryCity = mb_strtolower(trim($deliveryCity));
        $normalizedDistanceKm = max(0, $distanceKm);
        $deliveryPrice = 'bordeaux' === $normalizedDeliveryCity
            ? 0
            : 500 + ($normalizedDistanceKm * 59);

        return new OrderPricingDataDto(
            unitPrice: $unitPrice,
            peopleCount: $normalizedPeopleCount,
            menuSubtotal: $menuSubtotal,
            discountAmount: $discountAmount,
            deliveryPrice: $deliveryPrice,
            totalPrice: $menuSubtotal - $discountAmount + $deliveryPrice,
        );
    }
}
