<?php

namespace App\Http\Services;

use App\Enums\PaymentMethod;
use App\Enums\Status;
use App\Models\Invoice;
use App\Http\Traits\Paginatable;
use App\Http\Resources\InvoiceResource;
use App\Http\Repositories\InvoiceRepository;
use App\Http\Repositories\PaymentRepository;
use App\Http\Resources\PaymentResource;
use App\Models\InvoicePayment;
use App\Notifications\SendInvoiceNotification;
use App\Notifications\InvoiceReminderNotification;

class InvoiceService
{
    use Paginatable;

    public function __construct(
        protected InvoiceRepository $invoiceRepository,
        protected PaymentRepository $paymentRepository,
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
        $data['status'] = Status::DRAFT;
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

    public function duplicate(Invoice $data)
    {
        $data['invoice_id'] = "[COPY] {$data['invoice_id']}";
        while (count($this->invoiceRepository->all(['invoice_id' => $data['invoice_id']])->toArray()) > 0) {
            $data['invoice_id'] = "[COPY] {$data['invoice_id']}";
        }
        $invoice = $this->invoiceRepository->create($data->toArray());
        return new InvoiceResource($invoice);
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

    public function sendReminder(Invoice $invoice)
    {
        $this->notificationService->send(new InvoiceReminderNotification($invoice), $invoice->customer);
    }

    public function recordPayment(array $data)
    {
        $data['method'] = PaymentMethod::from($data['method']);
        $invoice = $this->paymentRepository->create($data);
        return new PaymentResource($invoice);
    }

    public function getPayments(array $data)
    {
        $invoice = $this->invoiceRepository->find($data['invoice_id']);
        return PaymentResource::collection($invoice->payments);
    }

    public function updatePayment(InvoicePayment $payment, array $data)
    {
        $this->paymentRepository->update($payment, $data);
        return new PaymentResource($payment->refresh());
    }

    public function deletePayment(InvoicePayment $payment)
    {
        $this->paymentRepository->destroy($payment);
    }
}