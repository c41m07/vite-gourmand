<?php

namespace App\Tests\Support;

trait GeneratesTestPasswordsTrait
{
    private static function generateTestPassword(): string
    {
        return 'Aa1!' . substr(hash('sha256', uniqid('', true)), 0, 12);
    }
}
