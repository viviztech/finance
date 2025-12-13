<?php

namespace App\Enums;

enum PenaltyType: string
{
    case FIXED = 'fixed';
    case PERCENTAGE = 'percentage';

    public function label(): string
    {
        return match ($this) {
            self::FIXED => 'Fixed Amount',
            self::PERCENTAGE => 'Percentage of Due',
        };
    }

    public function calculatePenalty(float $amountDue, float $rate): float
    {
        return match ($this) {
            self::FIXED => $rate,
            self::PERCENTAGE => ($amountDue * $rate) / 100,
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
