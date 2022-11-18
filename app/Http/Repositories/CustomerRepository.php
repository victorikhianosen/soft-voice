<?php

namespace App\Http\Repositories;

use App\Models\Customer;

class CustomerRepository extends AbstractRepository
{
    public function __construct(Customer $customer)
    {
        parent::__construct($customer);
    }

    public function model()
    {
        return app(Customer::class);
    }

    public function search(
        int $page = 1,
        int $perPage = 50,
        string $query = '',
        array $conditions = [],
        string $order = 'created_at',
        string $dir = 'desc',
    ): array
    {
        $data = $this->model->where($conditions);
        $total = $data->count();
        $data = $data->orderBy($order, $dir)
                    ->where(function($q) use ($query) {
                        $q->where('name', 'LIKE', '%'.$query.'%')
                            ->orWhere('email', 'LIKE', '%'.$query.'%')
                            ->orWhere('phone', 'LIKE', '%'.$query.'%');
                    })
                    ->offset(($page - 1) * $perPage)
                    ->limit($perPage)
                    ->get();
        return [
            'data'  => $data,
            'total' => $total
        ];
    }
}
