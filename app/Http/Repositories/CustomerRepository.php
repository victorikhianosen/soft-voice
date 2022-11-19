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

    public function findAll(
        int $page = 1,
        int $perPage = 50,
        string $query = '',
        array $conditions = [],
        string $order = 'created_at',
        string $dir = 'desc',
    ): array
    {
        $data = $this->model->where($conditions)
                            ->when($query, function ($q) use ($query) {
                                $q->where('name', 'LIKE', '%'.$query.'%')
                                    ->orWhere('email', 'LIKE', '%'.$query.'%')
                                    ->orWhere('phone', 'LIKE', '%'.$query.'%');
                            })
                            ->orderBy($order, $dir);

        $total = $data->count();
        return [
            'data'  => $data->offset(($page - 1) * $perPage)->limit($perPage)->get(),
            'total' => $total
        ];
    }
}
