<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomerExport implements FromArray, WithHeadings
{
    protected $customers;

    public function __construct(Collection $customers)
    {
        $this->customers = $customers;
    }

    public function array(): array
    {
        return $this->customers->map(fn($customer) => [
            'name'       => $customer->name,
            'email'      => $customer->email,
            'phone'      => $customer->phone,
            'address'    => $customer->address,
            'category'   => $customer->category,
            'website'    => $customer->website,
            'country'    => $customer->country,
            'state'      => $customer->state,
            'date_added' => $customer->created_at,
        ])->toArray();
    }

    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Phone',
            'Address',
            'Category',
            'Website',
            'Country',
            'State',
            'Date Added',
        ];
    }
}
