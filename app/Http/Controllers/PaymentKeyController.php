<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentKeyRequest;
use App\Http\Services\PaymentApiService;
use App\Traits\RespondWithHttpStatus;

class PaymentKeyController extends Controller
{
    use RespondWithHttpStatus;

    public function __construct(
        protected PaymentApiService $paymentApiService
    )
    {
    }

    public function getKeys()
    {
        $data = $this->paymentApiService->getKeys();
        return $this->success(data: $data);
    }

    public function updateKeys(PaymentKeyRequest $request)
    {
        $data = $this->paymentApiService->updateKeys($request->all());
        return $this->success("Payment key updated successfully", $data);
    }
}
