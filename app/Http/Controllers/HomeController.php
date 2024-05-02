<?php

namespace App\Http\Controllers;

use App\Enums\Currency;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Traits\RespondWithHttpStatus;

class HomeController extends Controller
{
    use RespondWithHttpStatus;

    public function getCurrencies()
    {
        $keys = Currency::currencies();
        $values = Currency::currenciesName();
        return $this->success(data: array_combine($keys, $values));
    }

    public function getBankList()
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('payment.paystack.secret_key')
        ])->get(config('payment.paystack.base_url') . '/bank?country=nigeria');

        return $this->success(data: $response['data'] ?? []);
    }

    public function resolveBankAccount(Request $request)
    {
        $this->validate($request, [
            'account_number' => 'required|string',
            'bank_code'      => 'required|string',
        ]);

        $bankCode = $request['bank_code'];
        $accountNumber = $request['account_number'];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('payment.paystack.secret_key')
        ])->get(config('payment.paystack.base_url') . "/bank/resolve?account_number=$accountNumber&bank_code=$bankCode");

        if (Arr::get($response, 'status')) {
            return $this->success(data: $response['data'] ?? []);
        }
        return $this->failure(Arr::get($response, 'message'));
    }
}
