<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Http\Requests\ListCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Http\Services\CustomerService;
use App\Http\Traits\RespondWithHttpStatus;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    use RespondWithHttpStatus;

    public function __construct(protected CustomerService $customerService)
    {
    }

    public function index(ListCustomerRequest $request): JsonResponse
    {
        $result = $this->customerService->getAll($request->all(), request()->user()->id);
        return $this->success(data: $result['data'], meta: $result['meta']);
    }

    public function show(Customer $customer): JsonResponse
    {
        $this->authorize('process', $customer);
        return $this->success(data: new CustomerResource($customer));
    }

    public function store(CustomerRequest $request): JsonResponse
    {
        $customer = $this->customerService->store($request->all());
        return $this->success("Customer created", $customer);
    }

    public function getHistory(Customer $customer): JsonResponse
    {
        $this->authorize('process', $customer);
        $data = $this->customerService->getHistory($customer->id);
        return $this->success(data: $data);
    }

    public function update(Customer $customer, CustomerRequest $request): JsonResponse
    {
        $this->authorize('process', $customer);
        $customer = $this->customerService->update($customer, $request->all());
        return $this->success("Customer updated", $customer);
    }

    public function delete(Customer $customer): JsonResponse
    {
        $this->authorize('process', $customer);
        $customer = $this->customerService->delete($customer);
        return $this->success("Customer deleted");
    }
}
