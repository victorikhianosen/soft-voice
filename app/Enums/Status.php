<?php

namespace App\Enums;

enum Status: string
{
    case DRAFT          = 'draft';
    case SENT           = 'sent';
    case VIEWED         = 'viewed';
    case PAID           = 'paid';
    case UNPAID         = 'unpaid';
    case PARTIALLY_PAID = 'partially_paid';
    case RECURRING      = 'recurring';
    case OVERDUE        = 'overdue';
    case REFUNDED       = 'refunded';
    case ACCEPTED       = 'accepted';
    case REJECTED       = 'rejected';
    case EXPIRED        = 'expired';

    public static function types(): array
    {
        return [
            self::DRAFT->value,
            self::SENT->value,
            self::VIEWED->value,
            self::PAID->value,
            self::UNPAID->value,
            self::PARTIALLY_PAID->value,
            self::RECURRING->value,
            self::OVERDUE->value,
            self::REFUNDED->value,
            self::ACCEPTED->value,
            self::REJECTED->value,
            self::EXPIRED->value,
        ];
    }

    public static function invoiceTypes(): array
    {
        return [
            self::DRAFT->value,
            self::SENT->value,
            self::VIEWED->value,
            self::PAID->value,
            self::UNPAID->value,
            self::PARTIALLY_PAID->value,
            self::RECURRING->value,
            self::OVERDUE->value,
            self::REFUNDED->value
        ];
    }

    public static function estimateTypes(): array
    {
        return [
            self::DRAFT->value,
            self::SENT->value,
            self::VIEWED->value,
            self::ACCEPTED->value,
            self::REJECTED->value,
            self::EXPIRED->value,
        ];
    }
}
