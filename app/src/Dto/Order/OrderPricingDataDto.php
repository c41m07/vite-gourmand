<?php

namespace App\Dto\Order;

final readonly class OrderPricingDataDto
{
    public function __construct(
        public int $unitPrice,
        public int $peopleCount,
        public int $menuSubtotal,
        public int $discountAmount,
        public int $deliveryPrice,
        public int $totalPrice,
    ) {
    }
}
