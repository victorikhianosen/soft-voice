<?php

namespace App\Http\Services;

use App\Exports\CustomerExport;
use App\Models\Customer;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Traits\Paginatable;
use App\Traits\UploadsFile;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\CustomerResource;
use App\Http\Repositories\CustomerRepository;
use App\Traits\HasRestrictions;

class CustomerService
{
    use Paginatable, UploadsFile, HasRestrictions;

    public function __construct(protected CustomerRepository $customerRepository)
    {
    }

    public function getAll(array $data, int $userId)
    {
        $result = $this->customerRepository->findAll(
            $data['current_page'] ?? 1,
            $data['per_page'] ?? 50,
            $data['query'] ?? '',
            ['user_id' => $userId],
            $data['sort']['column'] ?? 'created_at',
            $data['sort']['dir'] ?? 'desc',
            with: ['invoices']
        );
        return [
            'data' => CustomerResource::collection($result['data']),
            'meta' => $this->getMeta($data, $result['total'])
        ];
    }

    public function store(array $data): CustomerResource
    {
        if ($this->hasExhaustedCount('customers')) {
            abort(400, "You have exceeded you monthly expense limit for your plan");
        }

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
        $data = $this->customerRepository->getHistoryById($id);
        $data['summary'] = [
            'total' => $data->invoices->sum('total_amount'),
            'amount_paid' => $data->invoices->whereNotIn('status', ['refunded', 'pending'])->sum('amount_paid'),
            'amount_due' => $data->invoices->whereNotIn('status', ['refunded', 'pending'])->sum('amount_due'),
            'pending' => $data->invoices->where('status', 'pending')->sum('total_amount'),
            'refunded' => $data->invoices->where('status', 'refunded')->sum('total_amount'),
        ];
        return $data;
    }

    public function export(string $format, int $userId, string|int $dirName): string
    {
        if ($this->isRestricted('data-export')) {
			abort(400, "You current plan doesn't support this feature");
		}

        $directory = 'exports/'.$dirName;
        $dir = $this->getExportDirectory($directory);
        $customers = $this->customerRepository->getAll('*', conditions: ['user_id' => $userId]);
        if ($format === 'pdf') {
            $file = time().'.pdf';
            Pdf::loadView('exports.customer', ['customers' => $customers])->save("{$dir}/{$file}");
        } else if ($format === 'excel') {
            $file = time().'.xlsx';
            Excel::store(new CustomerExport($customers), "public/{$directory}/{$file}");
        } else {
            $file = time().'.xml';
            $xml = $this->prepareXML("{$dir}/{$file}", "Customers");
            foreach ($customers as $customer) {
                $xml->startElement('Customer');
                $xml->writeElement('customer_id', $customer->id);
                $xml->writeElement('name', $customer->name);
                $xml->writeElement('email', $customer->email);
                $xml->writeElement('phone', $customer->phone);
                $xml->writeElement('address', $customer->address);
                $xml->writeElement('category', $customer->category);
                $xml->writeElement('website', $customer->website);
                $xml->writeElement('country', $customer->country);
                $xml->writeElement('state', $customer->state);
                $xml->writeElement('date_created', $customer->created_at);
                $xml->endElement();
            }
            $this->closeXML($xml);
        }
        return asset("/storage/{$directory}/{$file}");
    }
}
