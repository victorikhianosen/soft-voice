<?php

namespace App\Enums;

enum Notification: string
{
    case INVOICE_VIEWED = 'invoice-viewed';
    case INVOICE_PAID = 'invoice-paid';
    case INVOICE_RECURRING = 'invoice-recurring';
    case INVOICE_OVERDUE = 'invoice-overdue';

    case ESTIMATE_VIEWED = 'estimate-viewed';
    case ESTIMATE_ACCEPTED_OR_DECLINED = 'estimate-accepted-or-declined';
    case ESTIMATE_RECURRING = 'estimate-recurring';
    case ESTIMATE_EXPIRED = 'estimate-expired';


    public static function notifications(): array
    {
        return [
            self::INVOICE_VIEWED->name,
            self::INVOICE_PAID->name,
            self::INVOICE_RECURRING->name,
            self::INVOICE_OVERDUE->name,
            self::ESTIMATE_VIEWED->name,
            self::ESTIMATE_ACCEPTED_OR_DECLINED->name,
            self::ESTIMATE_RECURRING->name,
            self::ESTIMATE_EXPIRED->name,
        ];
    }

    public static function notificationsName(): array
    {
        return [
            self::INVOICE_VIEWED->value,
            self::INVOICE_PAID->value,
            self::INVOICE_RECURRING->value,
            self::INVOICE_OVERDUE->value,
            self::ESTIMATE_VIEWED->value,
            self::ESTIMATE_ACCEPTED_OR_DECLINED->value,
            self::ESTIMATE_RECURRING->value,
            self::ESTIMATE_EXPIRED->value,
        ];
    }
}
