<?php

namespace App\Http\Repositories;

use App\Models\InvoicePayment;

class PaymentRepository extends AbstractRepository
{
    public function __construct(InvoicePayment $payment)
    {
        parent::__construct($payment);
    }

    public function model()
    {
        return app(InvoicePayment::class);
    }
}
