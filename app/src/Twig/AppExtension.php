<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('price_cents', [$this, 'formatPriceFromCents']),
        ];
    }

    public function formatPriceFromCents(?int $amount, string $fallback = '-'): string
    {
        if ($amount === null) {
            return $fallback;
        }

        return number_format($amount / 100, 2, ',', ' ') . ' €';
    }
}
