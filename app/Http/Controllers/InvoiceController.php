<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\InvoiceRequest;
use App\Http\Services\InvoiceService;
use App\Http\Resources\InvoiceResource;
use App\Http\Requests\ListInvoicesRequest;
use App\Http\Traits\RespondWithHttpStatus;
use App\Http\Requests\MarkInvoiceAsRequest;

class InvoiceController extends Controller
{
    use RespondWithHttpStatus;

    public function __construct(protected InvoiceService $invoiceService)
    {
    }

    public function index(ListInvoicesRequest $request): JsonResponse
    {
        $result = $this->invoiceService->getAll($request->all());
        return $this->success(data: $result['data'], meta: $result['meta']);
    }

    public function show(Invoice $invoice): JsonResponse
    {
        return $this->success(data: new InvoiceResource($invoice));
    }

    public function store(InvoiceRequest $request): JsonResponse
    {
        $data = $this->invoiceService->store($request->all());
        return $this->success("Invoice saved", $data);
    }

    public function update(Invoice $invoice, InvoiceRequest $request): JsonResponse
    {
        $data = $this->invoiceService->update($invoice, $request->all());
        return $this->success("Invoice updated", $data);
    }

    public function delete(Invoice $invoice): JsonResponse
    {
        $this->invoiceService->delete($invoice);
        return $this->success("Invoice deleted");
    }

    public function markAs(Invoice $invoice, MarkInvoiceAsRequest $request): JsonResponse
    {
        $data = $this->invoiceService->markAs($invoice, Status::from($request->status));
        return $this->success("Invoice status updated", $data);
    }

    public function send(Invoice $invoice): JsonResponse
    {
        $this->invoiceService->send($invoice);
        return $this->success("Invoice sent");
    }
}
