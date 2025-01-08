<?php

namespace App\Enums;

use App\Enums\Currency;

enum Currency: string
{
    case NGN = 'Nigeria Naira';
    case USD = 'United States Dollars';


    public static function currencies(): array
    {
        return [
            self::NGN->name,
            self::USD->name
        ];
    }

    public static function currenciesName(): array
    {
        return [
            self::NGN->value,
            self::USD->value
        ];
    }
}
