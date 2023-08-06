<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Http\Requests\InvoicePaymentRequest;
use App\Http\Requests\ListInvoicePaymentRequest;
use App\Http\Services\InvoiceService;
use App\Http\Resources\PaymentResource;
use App\Traits\RespondWithHttpStatus;

class PaymentController extends Controller
{
    use RespondWithHttpStatus;

    public function __construct(protected InvoiceService $invoiceService)
    {
    }

    public function index(ListInvoicePaymentRequest $request)
    {
        $data = $this->invoiceService->getPayments($request->all());
        return $this->success(data: $data);
    }

    public function show(InvoicePayment $payment)
    {
        return $this->success(data: new PaymentResource($payment));
    }

    public function store(InvoicePaymentRequest $request)
    {
        $data = $this->invoiceService->recordPayment($request->all());
        return $this->success("Invoice payment recorded", $data);
    }

    public function update(InvoicePayment $payment, InvoicePaymentRequest $request)
    {
        $data = $this->invoiceService->updatePayment($payment, $request->all());
        return $this->success("Invoice payment updated", $data);
    }

    public function delete(InvoicePayment $payment)
    {
        $this->invoiceService->deletePayment($payment);
        return $this->success("Invoice payment deleted");
    }
}
