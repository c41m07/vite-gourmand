<?php

namespace App\Dto\Order;

use DateTimeInterface;

final readonly class OrderPreviewViewDto
{

    public function __construct(
        public ?string            $phone,
        public string             $deliveryAddress,
        public string             $deliveryPostalCode,
        public string             $deliveryCity,
        public DateTimeInterface  $serviceDate,
        public string             $serviceTime,
        public int                $peopleCount,
        public ?string            $note,
        public int                $menuSubtotal,
        public int                $discountAmount,
        public int                $deliveryPrice,
        public int                $totalPrice,
        public bool               $needEquipmentLoan,
        public ?dateTimeInterface $equipmentLoanStartAt,
        public ?dateTimeInterface $equipmentLoanEndAt,
        public ?string            $equipmentLoanNote,
    )
    {

    }

}
