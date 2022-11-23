<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case BANK_TRANSFER = 'bank_transfer';
    case CASH          = 'cash';
    case OTHERS        = 'others';

    public static function types(): array
    {
        return [
            self::BANK_TRANSFER->value,
            self::CASH->value,
            self::OTHERS->value,
        ];
    }
}
