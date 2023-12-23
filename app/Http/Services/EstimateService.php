<?php

namespace App\Http\Services;

use App\Enums\Status;
use App\Models\Invoice;
use App\Models\Estimate;
use App\Traits\Paginatable;
use App\Traits\UploadsFile;
use Illuminate\Support\Arr;
use App\Exports\EstimateExport;
use App\Traits\HasRestrictions;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\InvoiceRequest;
use App\Http\Resources\EstimateResource;
use App\Http\Repositories\EstimateRepository;
use App\Http\Services\NotificationService\CustomerNotificationService;

class EstimateService
{
    use Paginatable, UploadsFile, HasRestrictions;

    public function __construct(
        protected InvoiceService $invoiceService,
        protected EstimateRepository $estimateRepository,
        protected CustomerNotificationService $customerNotificationService
    )
    {
    }

    public function getAll(array $data, int $userId): array
    {
        $result = $this->estimateRepository->findAll(
            $data['current_page'] ?? 1,
            $data['per_page'] ?? 50,
            $data['query'] ?? '',
            ['user_id' => $userId],
            $data['sort']['column'] ?? 'created_at',
            $data['sort']['dir'] ?? 'desc',
            $data['filter'] ?? []
        );
        return [
            'data' => EstimateResource::collection($result['data']),
            'meta' => $this->getMeta($data, $result['total'])
        ];
    }

    public function store(array $data): EstimateResource
    {
        if ($this->hasExhaustedCount('estimates')) {
            abort(400, "You have exceeded you monthly estimate limit for your plan");
        }

        $data += [
            'code'           => Estimate::getUniqueCode(),
            'status'         => Status::DRAFT,
            'user_id'        => request()->user()->id,
            'tax_percentage' => request()->user()->setting->tax_percentage,
        ];
        $estimate = $this->estimateRepository->create($data);
        foreach ($data['items'] as $item) {
            $estimate->items()->create($item);
        }
        return new EstimateResource($estimate->refresh());
    }

    public function update(Estimate $estimate, array $data)
    {
        $this->estimateRepository->update($estimate, $data);
        $ids = [];
        foreach ($data['items'] as $item) {
            if (isset($item['id'])) $ids[] = $item['id'];
        }
        $estimate->items()->whereNotIn('id', $ids)->delete();
        foreach ($data['items'] as $item) {
            $estimate->items()->updateOrCreate(['id' => ($item['id'] ?? null)], $item);
        }
        return new EstimateResource($estimate->refresh());
    }

    public function delete(Estimate $estimate)
    {
        $this->estimateRepository->destroy($estimate);
    }

    public function duplicate(Estimate $data)
    {
        if ($this->hasExhaustedCount('estimates')) {
            abort(400, "You have exceeded you monthly estimate limit for your plan");
        }

        $data = $data->toArray();
        Arr::forget($data, ['code', 'status', 'estimate_id', 'tax_percentage']);
        $data += [
            'code'           => Estimate::getUniqueCode(),
            'status'         => Status::DRAFT,
            'estimate_id'    => Estimate::nextId(),
            'tax_percentage' => request()->user()->setting->tax_percentage,
        ];
        $estimate = $this->estimateRepository->create($data);
        foreach ($data['items'] as $item) {
            $estimate->items()->create($item);
        }
        return new EstimateResource($estimate->fresh());
    }

    public function markAs(Estimate $estimate, Status $status)
    {
        $this->estimateRepository->update($estimate, ['status' => $status]);
        return new EstimateResource($estimate->refresh());
    }

    public function send(Estimate $estimate)
    {
        $this->estimateRepository->update($estimate, ['status' => 'sent']);
        $this->customerNotificationService->sendEstimateToCustomer($estimate);
    }

    public function export(string $format): string
    {
        if ($this->isRestricted('data-export')) {
			abort(400, "You current plan doesn't support this feature");
		}

        $userDirectory = 'exports/'.request()->user()->id;
        $dir = $this->getExportDirectory($userDirectory);
        $estimates = request()->user()->estimates;
        if ($format === 'pdf') {
            $file = time().'.pdf';
            Pdf::loadView('exports.estimate', ['estimates' => $estimates])->save("{$dir}/{$file}");
        } else if ($format === 'excel') {
            $file = time().'.xlsx';
            Excel::store(new EstimateExport($estimates), "public/{$userDirectory}/{$file}");
        } else {
            $file = time().'.xml';
            $xml = $this->prepareXML("{$dir}/{$file}", "Estimates");
            foreach ($estimates as $estimate) {
                $xml->startElement('Estimate');
                $xml->writeElement('estimate_id', $estimate->estimate_id);
                $xml->writeElement('customer', $estimate->customer->name);
                $xml->writeElement('total_amount', $estimate->total_amount);
                $xml->writeElement('date_created', $estimate->date);
                $xml->writeElement('due_date', $estimate->due_date);
                $xml->writeElement('status', $estimate->status);
                $xml->endElement();
            }
            $this->closeXML($xml);
        }
        return asset("/storage/{$userDirectory}/{$file}");
    }

    public function convertToInvoice(Estimate $estimate)
    {
        $data = $estimate->toArray();
        Arr::forget($data, ['id', 'code', 'created_at', 'updated_at']);

        $data += [
            'recurring' => false,
            'invoice_id' => Invoice::nextId(),
        ];

        return $this->invoiceService->store(new InvoiceRequest($data));
    }
}
