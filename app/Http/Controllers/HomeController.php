<?php

namespace App\Http\Controllers;

use App\Enums\Currency;
use App\Traits\RespondWithHttpStatus;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    use RespondWithHttpStatus;

    public function getCurrencies()
    {
        $keys = Currency::currencies();
        $values = Currency::currenciesName();
        return $this->success(data: array_combine($keys, $values));
    }
}
