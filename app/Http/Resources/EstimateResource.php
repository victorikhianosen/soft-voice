<?php

namespace App\Http\Resources;

use Illuminate\Support\Arr;
use Illuminate\Http\Resources\Json\JsonResource;

class EstimateResource extends JsonResource
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
        if (!request()->user()) {
            $data['user'] = new UserResource($this->user, $this->user->setting, $this->user->bank_accounts);
        }
        Arr::forget($data, ['customer_id']);
        return $data;
    }
}
