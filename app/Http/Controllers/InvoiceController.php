<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ExportRequest;
use App\Http\Requests\InvoiceRequest;
use App\Http\Services\InvoiceService;
use App\Http\Resources\InvoiceResource;
use App\Http\Requests\ListInvoiceRequest;
use App\Http\Traits\RespondWithHttpStatus;
use App\Http\Requests\MarkInvoiceAsRequest;
use App\Http\Repositories\InvoiceRepository;

class InvoiceController extends Controller
{
    use RespondWithHttpStatus;

    public function __construct(
        protected InvoiceService $invoiceService,
        protected InvoiceRepository $invoiceRepository
    )
    {
    }

    public function index(ListInvoiceRequest $request): JsonResponse
    {
        $result = $this->invoiceService->getAll($request->all(), request()->user()->id);
        return $this->success(data: $result['data'], meta: $result['meta']);
    }

    public function show(Invoice $invoice): JsonResponse
    {
        $this->authorize('process', $invoice);
        return $this->success(data: new InvoiceResource($invoice));
    }

    public function showPublic(Invoice $invoice): JsonResponse
    {
        return $this->success(data: new InvoiceResource($invoice));
    }

    public function store(InvoiceRequest $request): JsonResponse
    {
        $data = $this->invoiceService->store($request);
        return $this->success("Invoice saved", $data);
    }

    public function update(Invoice $invoice, InvoiceRequest $request): JsonResponse
    {
        $this->authorize('process', $invoice);
        $data = $this->invoiceService->update($invoice, $request);
        return $this->success("Invoice updated", $data);
    }

    public function delete(Invoice $invoice): JsonResponse
    {
        $this->authorize('process', $invoice);
        $this->invoiceService->delete($invoice);
        return $this->success("Invoice deleted");
    }

    public function duplicate(Invoice $invoice): JsonResponse
    {
        $this->authorize('process', $invoice);
        $data = $this->invoiceService->duplicate($invoice);
        return $this->success("Invoice duplicated", $data);
    }

    public function markAs(Invoice $invoice, MarkInvoiceAsRequest $request): JsonResponse
    {
        $this->authorize('process', $invoice);
        $data = $this->invoiceService->markAs($invoice, Status::from($request->status));
        return $this->success("Invoice status updated", $data);
    }

    public function send(Invoice $invoice): JsonResponse
    {
        $this->authorize('process', $invoice);
        $this->invoiceService->send($invoice);
        return $this->success("Invoice sent");
    }

    public function sendReminder(Invoice $invoice): JsonResponse
    {
        $this->authorize('process', $invoice);
        $this->invoiceService->sendReminder($invoice);
        return $this->success("Invoice reminder sent");
    }

    public function export(ExportRequest $request): JsonResponse
    {
        $url = $this->invoiceService->export($request['format']);
        return $this->success(
            "Temporary file generated, this file will be deleted in 10 minutes",
            ['url' => $url]
        );
    }

    public function getSummary()
    {
        $data = $this->invoiceRepository->getSummary();
        return $this->success(data: $data);
    }
}
