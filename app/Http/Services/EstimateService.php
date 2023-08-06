<?php

namespace App\Http\Services;

use App\Enums\Status;
use App\Models\Estimate;
use App\Exports\EstimateExport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Traits\Paginatable;
use App\Traits\UploadsFile;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\EstimateResource;
use App\Http\Repositories\EstimateRepository;
use App\Notifications\SendEstimateNotification;

class EstimateService
{
    use Paginatable, UploadsFile;

    public function __construct(
        protected EstimateRepository $estimateRepository,
        protected NotificationService $notificationService
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
        return new EstimateResource($estimate);
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
        $data = $data->toArray();
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
        $this->notificationService->send(new SendEstimateNotification($estimate), $estimate->customer);
    }

    public function export(string $format): string
    {
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
                $xml->writeElement('amount', $estimate->amount);
                $xml->writeElement('date_created', $estimate->date);
                $xml->writeElement('due_date', $estimate->due_date);
                $xml->writeElement('status', $estimate->status);
                $xml->endElement();
            }
            $this->closeXML($xml);
        }
        return asset("/storage/{$userDirectory}/{$file}");
    }
}
