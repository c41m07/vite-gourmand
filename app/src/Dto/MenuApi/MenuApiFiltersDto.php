<?php

namespace App\Dto\MenuApi;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final readonly class MenuApiFiltersDto
{
    public function __construct(
        #[Assert\PositiveOrZero]
        public ?int $minPrice = null,
        #[Assert\PositiveOrZero]
        public ?int $maxPrice = null,
        #[Assert\Positive]
        public ?int $theme = null,
        #[Assert\Positive]
        public ?int $diet = null,
        #[Assert\Positive]
        public ?int $minPersons = null,
        #[Assert\PositiveOrZero]
        public ?int $stock = null,
    ) {
    }

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        if ($this->minPrice !== null && $this->maxPrice !== null && $this->minPrice > $this->maxPrice) {
            $context->buildViolation('Le prix minimum ne peut etre superieur au prix maximum.')
                ->atPath('minPrice')
                ->addViolation();
        }
    }
}
