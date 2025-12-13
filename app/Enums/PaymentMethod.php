<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case CASH = 'cash';
    case UPI = 'upi';
    case BANK_TRANSFER = 'bank_transfer';
    case CHEQUE = 'cheque';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::CASH => 'Cash',
            self::UPI => 'UPI',
            self::BANK_TRANSFER => 'Bank Transfer',
            self::CHEQUE => 'Cheque',
            self::OTHER => 'Other',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::CASH => 'banknotes',
            self::UPI => 'device-phone-mobile',
            self::BANK_TRANSFER => 'building-library',
            self::CHEQUE => 'document-text',
            self::OTHER => 'credit-card',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
