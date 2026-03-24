<?php

namespace App\Service\Order;

use App\Dto\Order\OrderPricingResultDto;
use App\Entity\Menu;

final class OrderPricingService
{

    public function calculate(Menu $menu, int $peopleCount, string $deliveryCity, int $distanceKm, bool $needEquipmentLoan): OrderPricingResultDto
    {
        $basePrice = (int)$menu->getBasePrice();
        $minPeople = (int)$menu->getMinPeople();

        $normalizedCity = trim($deliveryCity);
        $normalizedDistanceKm = max(0, $distanceKm);

        $isBordeaux = mb_strtolower($normalizedCity) === 'bordeaux';

        $menuSubtotal = $basePrice * $peopleCount;

        $discountApplied = $peopleCount >= ($minPeople + 5);
        $discountAmount = $discountApplied ? (int)round($menuSubtotal * 0.10) : 0;

        $deliveryPrice = $isBordeaux ? 0 : 500 + ($normalizedDistanceKm * 59);

        $totalPrice = $menuSubtotal + $deliveryPrice - $discountAmount;


        return new OrderPricingResultDto(
            basePrice: $basePrice,
            peopleCount: $peopleCount,
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
