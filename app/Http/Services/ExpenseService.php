<?php

namespace App\Http\Services;

use App\Enums\Status;
use App\Models\Expense;
use App\Exports\ExpenseExport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Traits\Paginatable;
use App\Traits\UploadsFile;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\ExpenseResource;
use App\Http\Repositories\ExpenseRepository;
use App\Http\Requests\ExpenseRequest;
use App\Traits\HasRestrictions;

class ExpenseService
{
    use Paginatable, UploadsFile, HasRestrictions;

    public function __construct(
        protected ExpenseRepository $expenseRepository,
    )
    {
    }

    public function getAll(array $data, int $userId): array
    {
        $result = $this->expenseRepository->findAll(
            $data['current_page'] ?? 1,
            $data['per_page'] ?? 50,
            $data['query'] ?? '',
            ['user_id' => $userId],
            $data['sort']['column'] ?? 'created_at',
            $data['sort']['dir'] ?? 'desc',
            $data['filter'] ?? []
        );
        return [
            'data' => ExpenseResource::collection($result['data']),
            'meta' => $this->getMeta($data, $result['total'])
        ];
    }

    public function store(ExpenseRequest $data): ExpenseResource
    {
        if ($this->hasExhaustedCount('expenses')) {
            abort(400, "You have exceeded you monthly expense limit for your plan");
        }

        $file = $data->file('attachment');
        $data = $data->all();
        $data['user_id'] = request()->user()->id;
        $data['status'] = Status::PENDING;
        if ($file) {
            $data['attachment'] = $this->saveFIle($file, 'attachments');
        }
        $expense = $this->expenseRepository->create($data);
        return new ExpenseResource($expense);
    }

    public function update(Expense $expense, ExpenseRequest $data)
    {
        $file = $data->file('attachment');
        $data = $data->all();
        if ($file) {
            $data['attachment'] = $this->saveFIle($file, 'attachments');
        }
        $this->expenseRepository->update($expense, $data);
        return new ExpenseResource($expense->refresh());
    }

    public function delete(Expense $expense)
    {
        $this->expenseRepository->destroy($expense);
    }

    public function duplicate(Expense $data)
    {
        $estimate = $this->expenseRepository->create($data->toArray());
        return new ExpenseResource($estimate);
    }

    public function markAs(Expense $expense, Status $status)
    {
        $this->expenseRepository->update($expense, ['status' => $status]);
        return new ExpenseResource($expense->refresh());
    }

    public function export(string $format): string
    {
        if ($this->isRestricted('data-export')) {
			abort(400, "You current plan doesn't support this feature");
		}

        $userDirectory = 'exports/'.request()->user()->id;
        $dir = $this->getExportDirectory($userDirectory);
        $expenses = request()->user()->expenses;
        if ($format === 'pdf') {
            $file = time().'.pdf';
            Pdf::loadView('exports.expense', ['expenses' => $expenses])->save("{$dir}/{$file}");
        } else if ($format === 'excel') {
            $file = time().'.xlsx';
            Excel::store(new ExpenseExport($expenses), "public/{$userDirectory}/{$file}");
        } else {
            $file = time().'.xml';
            $xml = $this->prepareXML("{$dir}/{$file}", "Expenses");
            foreach ($expenses as $expense) {
                $xml->startElement('Expense');
                $xml->writeElement('customer', $expense->customer->name);
                $xml->writeElement('amount', $expense->amount);
                $xml->writeElement('date', $expense->date);
                $xml->writeElement('recurring', $expense->recurring == 1 ? "Yes" : "No");
                $xml->writeElement('status', $expense->status);
                $xml->endElement();
            }
            $this->closeXML($xml);
        }
        return asset("/storage/{$userDirectory}/{$file}");
    }
}
