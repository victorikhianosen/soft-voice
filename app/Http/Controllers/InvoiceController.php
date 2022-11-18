<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceRequest;
use App\Http\Services\InvoiceService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct(protected InvoiceService $invoiceService)
    {
    }

    public function index()
    {

    }

    public function show()
    {

    }

    public function store(InvoiceRequest $request)
    {
        return $request;
    }

    public function update()
    {

    }

    public function delete()
    {

    }
}
