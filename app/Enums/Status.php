<?php

namespace App\Enums;

enum Status: string
{
    case DRAFT          = 'draft';
    case SENT           = 'sent';
    case VIEWED         = 'viewed';
    case PAID           = 'paid';
    case PARTIALLY_PAID = 'partially_paid';
    case RECURRING      = 'recurring';
    case OVERDUE        = 'overdue';
    case REFUNDED       = 'refunded';

    public static function types(): array
    {
        return [
            self::DRAFT->value,
            self::SENT->value,
            self::VIEWED->value,
            self::PAID->value,
            self::PARTIALLY_PAID->value,
            self::RECURRING->value,
            self::OVERDUE->value,
            self::REFUNDED->value
        ];
    }
}
