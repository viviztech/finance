<?php

namespace App\Enums;

enum LoanFrequency: string
{
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case BIWEEKLY = 'biweekly';
    case MONTHLY = 'monthly';

    public function label(): string
    {
        return match ($this) {
            self::DAILY => 'Daily',
            self::WEEKLY => 'Weekly',
            self::BIWEEKLY => 'Bi-Weekly',
            self::MONTHLY => 'Monthly',
        };
    }

    public function addInterval(\Carbon\Carbon $date): \Carbon\Carbon
    {
        return match ($this) {
            self::DAILY => $date->addDay(),
            self::WEEKLY => $date->addWeek(),
            self::BIWEEKLY => $date->addWeeks(2),
            self::MONTHLY => $date->addMonth(),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
