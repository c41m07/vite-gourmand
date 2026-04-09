<?php

namespace App\Service\Order;

use App\Dto\Order\OrderPricingResultDto;
use App\Entity\Menu;

final class OrderPricingService
{

    public function calculate(
        Menu $menu,
        int $peopleCount,
        string $deliveryCity,
        int $distanceKm,
        bool $needEquipmentLoan = false,
    ): OrderPricingResultDto
    {
        $unitPrice = (int)$menu->getBasePrice();
        $minPeople = (int)$menu->getMinPeople();
        $normalizedPeopleCount = max(0, $peopleCount);

        $normalizedCity = trim($deliveryCity);
        $normalizedDistanceKm = max(0, $distanceKm);

        $isBordeaux = mb_strtolower($normalizedCity) === 'bordeaux';

        $menuSubtotal = $unitPrice * $normalizedPeopleCount;

        $discountApplied = $normalizedPeopleCount >= ($minPeople + 5);
        $discountAmount = $discountApplied ? (int)round($menuSubtotal * 0.10) : 0;

        $deliveryPrice = $isBordeaux ? 0 : 500 + ($normalizedDistanceKm * 59);

        $totalPrice = $menuSubtotal + $deliveryPrice - $discountAmount;


        return new OrderPricingResultDto(
            unitPrice: $unitPrice,
            peopleCount: $normalizedPeopleCount,
            menuSubtotal: $menuSubtotal,
            discountAmount: $discountAmount,
            deliveryPrice: $deliveryPrice,
            totalPrice: $totalPrice,
            deliveryCity: $normalizedCity,
            distanceKm: $isBordeaux ? 0 : $normalizedDistanceKm,
            isBordeaux: $isBordeaux,
            discountApplied: $discountApplied,
            needEquipmentLoan: $needEquipmentLoan
        );
    }
}
