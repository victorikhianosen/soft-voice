<?php

namespace App\Http\Repositories;

use Carbon\Carbon;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Builder;

class InvoiceRepository extends AbstractRepository
{
    public function __construct(Invoice $invoice)
    {
        parent::__construct($invoice);
    }

    public function model()
    {
        return app(Invoice::class);
    }

    public function findAll(
        int $page = 1,
        int $perPage = 50,
        string $query = '',
        array $conditions = [],
        string $order = 'created_at',
        string $dir = 'desc',
        array $filter = []
    ): mixed
    {
        $status = $filter['status'] ?? null;
        $date = $filter['date'] ?? null;
        $customers = $filter['customer'] ?? null;
        $data = $this->model->where($conditions)
                            ->when($status, fn($q) => $q->whereIn('status', $status))
                            ->when($customers, fn($q) => $q->whereIn('customer_id', $customers))
                            ->when($date && isset($date['mode']), function ($q) use ($date) {
                                $q->when($date['mode'] === "range", function ($q) use ($date) {
                                    $q->whereDate('created_at', '>=', $date['range']['from'])
                                        ->whereDate('created_at', '<=',  $date['range']['to']);
                                });
                                $q->when($date['mode'] === "fixed", function ($q) use ($date) {
                                    $today = Carbon::today();
                                    $q->when($date['fixed'] === 'today', fn($q) => $q->whereDate('created_at', $today));
                                    $q->when($date['fixed'] === 'yesterday', fn($q) => $q->whereDate('created_at', $today->subDay()));
                                    $q->when($date['fixed'] === 'last_7_days', function ($q) use ($today) {
                                        $q->whereDate('created_at', '<=',  $today)
                                            ->whereDate('created_at', '>=', $today->subDays(7));
                                    });
                                    $q->when($date['fixed'] === 'this_month', fn($q) => $q->whereMonth('created_at', $today->month));
                                    $q->when($date['fixed'] === 'last_month', fn($q) => $q->whereMonth('created_at', $today->subMonth()->month));
                                });
                            })
                            ->when($query, function ($q) use ($query) {
                                $q->where('invoice_id', 'LIKE', '%'.$query.'%')
                                    ->orWhere('currency', 'LIKE', '%'.$query.'%')
                                    ->orWhere('terms', 'LIKE', '%'.$query.'%')
                                    ->orWhere('notes', 'LIKE', '%'.$query.'%')
                                    ->orWhere('signatory_name', 'LIKE', '%'.$query.'%')
                                    ->orWhere('status', 'LIKE', '%'.$query.'%');
                            })
                            ->orderBy($order, $dir);
        $total = $data->count();
        return [
            'data'  => $data->offset(($page - 1) * $perPage)->limit($perPage)->get(),
            'total' => $total
        ];
    }
}
