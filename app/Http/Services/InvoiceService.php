<?php

namespace App\Http\Services;

use App\Http\Repositories\InvoiceRepository;

class InvoiceService
{
    public function __construct(protected InvoiceRepository $invoiceRepository)
    {
    }
}
