<?php

namespace App\Http\Resources;

use Illuminate\Support\Arr;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class ExpenseResource extends JsonResource
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
        $data['date'] = Carbon::parse($this->date)->format('Y/m/d');
        Arr::forget($data, ['customer_id']);
        $data['attachment'] = $this->attachment ? asset($this->attachment) : null;
        return $data;
    }
}
