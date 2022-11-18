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
        $result = $this->customerRepository->search(
            $data['current_page'] ?? 1,
            $data['per_page'] ?? 50,
            $data['query'] ?? '',
            ['user_id' => request()->user()->id]
        );
        return [
            'data' => CustomerResource::collection($result['data']),
            'meta' => $this->getMeta($data, $result['total'])
        ];
    }

    public function store(array $data): CustomerResource
    {
        $customer = request()->user()->customers()->create(Arr::only($data, [
            'name', 'email', 'phone', 'address',
            'category', 'website', 'country', 'state'
        ]));
        return new CustomerResource($customer);
    }

    public function update(Customer $customer, array $data): CustomerResource
    {
        $customer->update(Arr::only($data, [
            'name', 'email', 'phone', 'address',
            'category', 'website', 'country', 'state'
        ]));
        return new CustomerResource($customer);
    }

    public function delete(Customer $customer)
    {
        $customer->delete();
    }

    protected function getAddresses(array $data): array
    {
        $shippingAddress = Arr::first($data["addresses"], fn($adr) => $adr['type'] === 'shipping');
        $billiingAddress = Arr::first($data["addresses"], fn($adr) => $adr['type'] === 'billing');

        if (!($shippingAddress && $billiingAddress)) {
            throw new CustomerException("Shipping or billing address not present", 400);
        }

        return [
            'shipping' => $shippingAddress,
            'billing'  => $billiingAddress
        ];
    }
}
