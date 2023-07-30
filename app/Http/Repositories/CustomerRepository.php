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

    public function getHistoryById(int $id)
    {
        return $this->model()->where('id', $id)
                    ->with(['invoices', 'expenses', 'estimates'])
                    ->first();
    }

    public function getUserCustomersCount()
    {
        return request()->user()->customers()->count();
    }
}
