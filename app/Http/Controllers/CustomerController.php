<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Http\Requests\ListAllRequest;
use App\Http\Resources\CustomerResource;
use App\Http\Services\CustomerService;
use App\Http\Traits\RespondWithHttpStatus;
use App\Models\Customer;

class CustomerController extends Controller
{
    use RespondWithHttpStatus;

    public function __construct(protected CustomerService $customerService)
    {
    }

    public function index(ListAllRequest $request)
    {
        $result = $this->customerService->getAll($request->all());
        return $this->success(data: $result['data'], meta: $result['meta']);
    }

    public function show(Customer $customer)
    {
        return $this->success(data: new CustomerResource($customer));
    }

    public function store(CustomerRequest $request)
    {
        $customer = $this->customerService->store($request->all());
        return $this->success("Customer created", $customer);
    }

    public function update(Customer $customer, CustomerRequest $request)
    {
        $customer = $this->customerService->update($customer, $request->all());
        return $this->success("Customer updated", $customer);
    }

    public function delete(Customer $customer)
    {
        $customer = $this->customerService->delete($customer);
        return $this->success("Customer deleted");
    }
}
