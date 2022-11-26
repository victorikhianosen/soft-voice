<?php

namespace App\Http\Resources;

use App\Enums\Status;
use Illuminate\Support\Arr;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
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
        $data['customer'] = new CustomerResource($this->customer);
        Arr::forget($data, ['customer_id']);
        $data['amount'] = $this->amount;
        return $data;
    }
}
