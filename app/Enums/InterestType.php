<?php

namespace App\Enums;

enum InterestType: string
{
    case FIXED = 'fixed';
    case PERCENTAGE = 'percentage';

    public function label(): string
    {
        return match ($this) {
            self::FIXED => 'Fixed Amount',
            self::PERCENTAGE => 'Percentage',
        };
    }

    public function calculateInterest(float $principal, float $rate): float
    {
        return match ($this) {
            self::FIXED => $rate,
            self::PERCENTAGE => ($principal * $rate) / 100,
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
