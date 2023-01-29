<?php

namespace App\Http\Services;

use App\Models\Address;
use App\Models\Customer;
use Illuminate\Support\Arr;
use App\Exceptions\CustomerException;
use App\Http\Resources\CustomerResource;
use App\Http\Repositories\CustomerRepository;
use App\Http\Traits\Paginatable;

class CustomerService
{
    use Paginatable;

    public function __construct(protected CustomerRepository $customerRepository)
    {
    }

    public function getAll(array $data)
    {
        $result = $this->customerRepository->findAll(
            $data['current_page'] ?? 1,
            $data['per_page'] ?? 50,
            $data['query'] ?? '',
            ['user_id' => request()->user()->id],
            $data['sort']['column'] ?? null,
            $data['sort']['dir'] ?? null,
        );
        return [
            'data' => CustomerResource::collection($result['data']),
            'meta' => $this->getMeta($data, $result['total'])
        ];
    }

    public function store(array $data): CustomerResource
    {
        $data['user_id'] = request()->user()->id;
        $customer = $this->customerRepository->create($data);
        return new CustomerResource($customer);
    }

    public function update(Customer $customer, array $data): CustomerResource
    {
        $this->customerRepository->update($customer->id, $data);
        return new CustomerResource($customer->refresh());
    }

    public function delete(Customer $customer)
    {
        $this->customerRepository->destroy($customer);
    }

    public function getHistory(int $id): Customer
    {
        return $this->customerRepository->getHistoryById($id);
    }
}
