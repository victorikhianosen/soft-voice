<?php

namespace App\Http\Services;

use App\Enums\Status;
use App\Models\Invoice;
use App\Http\Traits\Paginatable;
use App\Http\Resources\InvoiceResource;
use App\Http\Repositories\InvoiceRepository;
use App\Notifications\SendInvoiceNotification;

class InvoiceService
{
    use Paginatable;

    public function __construct(
        protected InvoiceRepository $invoiceRepository,
        protected NotificationService $notificationService
    )
    {
    }

    public function getAll(array $data): array
    {
        $result = $this->invoiceRepository->findAll(
            $data['current_page'] ?? 1,
            $data['per_page'] ?? 50,
            $data['query'] ?? '',
            ['user_id' => request()->user()->id],
            $data['sort']['column'] ?? null,
            $data['sort']['dir'] ?? null,
            $data['filter'] ?? []
        );
        return [
            'data' => InvoiceResource::collection($result['data']),
            'meta' => $this->getMeta($data, $result['total'])
        ];
    }

    public function store(array $data): InvoiceResource
    {
        $data['user_id'] = request()->user()->id;
        $data['status'] = Status::DRAFT->value;
        $invoice = $this->invoiceRepository->create($data);
        return new InvoiceResource($invoice);
    }

    public function update(Invoice $invoice, array $data): InvoiceResource
    {
        $this->invoiceRepository->update($invoice, $data);
        return new InvoiceResource($invoice->refresh());
    }

    public function delete(Invoice $invoice)
    {
        $this->invoiceRepository->destroy($invoice);
    }

    public function markAs(Invoice $invoice, Status $status)
    {
        $this->invoiceRepository->update($invoice, ['status' => $status]);
        return new InvoiceResource($invoice->refresh());
    }

    public function send(Invoice $invoice)
    {
        $this->notificationService->send(new SendInvoiceNotification($invoice), $invoice->customer);
    }
}