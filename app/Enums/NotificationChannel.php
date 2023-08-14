<?php

namespace App\Enums;

enum NotificationChannel: string
{
    case MAIL = 'mail';
    case SMS = 'sms';
    case WHATSAPP = 'whatsapp';
    case DATABASE = 'database';

    public static function channels(): array
    {
        return [
            self::MAIL->value,
            self::SMS->value,
            self::WHATSAPP->value,
            self::DATABASE->value
        ];
    }
}
