<?php

namespace App\Dto\Order;

final readonly class OrderPricingResultDto
{
    public function __construct(
        public int    $unitPrice,
        public int    $peopleCount,
        public int    $menuSubtotal,
        public int    $discountAmount,
        public int    $deliveryPrice,
        public int    $totalPrice,
        public string $deliveryCity,
        public int    $distanceKm,
        public bool   $isBordeaux,
        public bool   $discountApplied,
        public bool   $needEquipmentLoan,
    )
    {
    }
}
