<?php

namespace App\Http\Resources;

use App\Models\Admin;
use App\Models\BankAccount;
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
        if (!request()->user()) {
            $data['user'] = new UserResource($this->user, $this->user->setting);
            if ($this->user->setting['preferred_bank_id']) {
                $data['preferred_bank'] = BankAccount::find($this->user->setting['preferred_bank_id']);
            }
        }

        if (request()->user() && request()->user() instanceof Admin) {
            if ($this->user->setting['preferred_bank_id']) {
                $data['preferred_bank'] = BankAccount::find($this->user->setting['preferred_bank_id']);
            }
        }

        Arr::forget($data, ['customer_id']);
        $data['signature'] = $this->signature ? asset($this->signature) : null;
        return $data;
    }
}
