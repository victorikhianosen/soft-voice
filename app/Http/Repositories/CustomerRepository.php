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
}
