<?php

namespace App\Http\Services;

use XMLWriter;
use App\Enums\Status;
use App\Models\Invoice;
use Illuminate\Support\Arr;
use App\Enums\PaymentMethod;
use App\Exports\InvoiceExport;
use App\Models\InvoicePayment;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Traits\Paginatable;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\InvoiceResource;
use App\Http\Resources\PaymentResource;

use App\Http\Repositories\InvoiceRepository;
use App\Http\Repositories\PaymentRepository;
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
        foreach ($data['items'] as $item) {
            $invoice->items()->create($item);
        }
        return new InvoiceResource($invoice);
    }

    public function update(Invoice $invoice, array $data)
    {
        $this->invoiceRepository->update($invoice, $data);
        $itemIDs = [];
        foreach ($data['items'] as $item) {
            $id = $item['id'] ?? null;
            if ($id) $itemIDs[] = $id;
            $invoice->items()->updateOrCreate(
                ['id' => $id],
                $item
            );
        }
        $invoice->items()->whereNotIn('id', $itemIDs)->delete();
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
        $dir = storage_path("app/public/{$userDirectory}");
        $invoices = request()->user()->invoices;
        if (!File::isDirectory($dir)) {
            File::makeDirectory($dir);
        }
        if ($format === 'pdf') {
            $file = time().'.pdf';
            Pdf::loadView('exports.invoice', ['invoices' => $invoices])
                ->save("{$dir}/{$file}");
        } else if ($format === 'excel') {
            $file = time().'.xlsx';
            Excel::store(new InvoiceExport($invoices), "public/{$userDirectory}/{$file}");
        } else {
            $file = time().'.xml';
            $xml = new XMLWriter();
            $xml->openURI("{$dir}/{$file}");

            $xml->startDocument('1.0');
            $xml->startElement('Invoices');
            $xml->setIndent(4);
            foreach ($invoices as $invoice){
                $xml->startElement('Invoice');
                $xml->writeElement('invoice_id', $invoice->invoice_id);
                $xml->writeElement('customer', $invoice->customer->name);
                $xml->writeElement('amount', $invoice->amount);
                $xml->writeElement('date_created', $invoice->date);
                $xml->writeElement('due_date', $invoice->due_date);
                $xml->writeElement('status', $invoice->status);
                $xml->endElement();
            }
            $xml->endElement();
            $xml->endDocument();
            $xml->flush();
        }
        return asset("storage/{$userDirectory}/{$file}");
    }
}