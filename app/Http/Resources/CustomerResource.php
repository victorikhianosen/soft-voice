<?php

namespace App\Http\Resources;

use Illuminate\Support\Arr;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = parent::toArray($request);
        if (Arr::has($data, 'invoices')) {
            $invoices = $data['invoices'];
            Arr::forget($data, 'invoices');
        } else {
            $invoices = $this->invoices->toArray();
        }
        $data['amount_due'] = array_reduce($invoices, function($sum, $invoice) {
            return $sum + $invoice['amount_due'];
        }, 0);
        return $data;
    }
}