<?php

namespace App\Http\Services;

use App\Http\Resources\PaymentKeyResource;

class PaymentApiService
{
    public function getKeys()
    {
        $user = request()->user();
        return [
            'paystack' => new PaymentKeyResource($user->payStackKey),
            'stripe' => new PaymentKeyResource($user->stripeKey),
            'flutterwave' => new PaymentKeyResource($user->flutterwaveKey),
            'paypal' => new PaymentKeyResource($user->payPalKey),
        ];
    }

    public function updateKeys(array $data)
    {
        $property = $data['type'] . "Key";
        if (request()->user()->{$property}) {
            request()->user()->{$property}->update($data);
        } else {
            request()->user()->{$property}()->create($data);
        }
        return $this->getKeys();
    }
}
