<?php

namespace App\Http\Services;

use XMLWriter;
use App\Enums\Status;
use App\Models\Invoice;
use App\Enums\PaymentMethod;
use App\Exports\InvoiceExport;
use App\Models\InvoicePayment;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Traits\Paginatable;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\InvoiceRequest;
use App\Http\Resources\InvoiceResource;

use App\Http\Resources\PaymentResource;
use App\Http\Repositories\InvoiceRepository;
use App\Http\Repositories\PaymentRepository;
use App\Http\Traits\UploadsFile;
use App\Notifications\SendInvoiceNotification;
use App\Notifications\InvoiceReminderNotification;

class InvoiceService
{
    use Paginatable, UploadsFile;

    public function __construct(
        protected InvoiceRepository $invoiceRepository,
        protected PaymentRepository $paymentRepository,
        protected NotificationService $notificationService
    )
    {
    }

    public function getAll(array $data, int $userId): array
    {
        $result = $this->invoiceRepository->findAll(
            $data['current_page'] ?? 1,
            $data['per_page'] ?? 50,
            $data['query'] ?? '',
            ['user_id' => $userId],
            $data['sort']['column'] ?? 'created_at',
            $data['sort']['dir'] ?? 'desc',
            $data['filter'] ?? []
        );
        return [
            'data' => InvoiceResource::collection($result['data']),
            'meta' => $this->getMeta($data, $result['total'])
        ];
    }

    public function store(InvoiceRequest $data): InvoiceResource
    {
        $file = $data->file('signature');
        $data = $data->all();
        $data['user_id'] = request()->user()->id;
        $data['status'] = Status::DRAFT;
        if ($file) {
            $data['signature'] = $this->saveFIle($file, 'signatures');
        }
        $invoice = $this->invoiceRepository->create($data);
        foreach ($data['items'] as $item) {
            $invoice->items()->create($item);
        }
        return new InvoiceResource($invoice);
    }

    public function update(Invoice $invoice, InvoiceRequest $data)
    {
        $file = $data->file('signature');
        $data = $data->all();
        if ($file) {
            $data['signature'] = $this->saveFIle($file, 'signatures');
        }
        $this->invoiceRepository->update($invoice, $data);

        $ids = [];
        foreach ($data['items'] as $item) {
            if (isset($item['id'])) $ids[] = $item['id'];
        }
        $invoice->items()->whereNotIn('id', $ids)->delete();
        foreach ($data['items'] as $item) {
            $invoice->items()->updateOrCreate(['id' => ($item['id'] ?? null)], $item);
        }
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

    public function export(string $format): string
    {
        $userDirectory = 'exports/'.request()->user()->id;
        $dir = $this->getExportDirectory($userDirectory);
        $invoices = request()->user()->invoices;
        if ($format === 'pdf') {
            $file = time().'.pdf';
            Pdf::loadView('exports.invoice', ['invoices' => $invoices])->save("{$dir}/{$file}");
        } else if ($format === 'excel') {
            $file = time().'.xlsx';
            Excel::store(new InvoiceExport($invoices), "public/{$userDirectory}/{$file}");
        } else {
            $file = time().'.xml';
            $xml = $this->prepareXML("{$dir}/{$file}", "Invoices");
            foreach ($invoices as $invoice) {
                $xml->startElement('Invoice');
                $xml->writeElement('invoice_id', $invoice->invoice_id);
                $xml->writeElement('customer', $invoice->customer->name);
                $xml->writeElement('amount', $invoice->amount);
                $xml->writeElement('date_created', $invoice->date);
                $xml->writeElement('due_date', $invoice->due_date);
                $xml->writeElement('status', $invoice->status);
                $xml->endElement();
            }
            $this->closeXML($xml);
        }
        return asset("/storage/{$userDirectory}/{$file}");
    }
}