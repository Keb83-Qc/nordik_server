<?php

namespace App\Filament\Abf\Support;

final class Money
{
    public static function dollars(float $value, int $decimals = 2): string
    {
        return '$' . number_format($value, $decimals, '.', ' ');
    }

    /**
     * Format PDF-style "1 234 $" (0 decimals).
     */
    public static function dollars0(float $value): string
    {
        return number_format($value, 0, '.', ' ') . ' $';
    }
}
