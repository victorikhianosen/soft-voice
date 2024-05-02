<?php

namespace App\Http\Resources;

use App\Models\BankAccount;
use Illuminate\Support\Arr;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
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
        Arr::forget($data, ['id', 'user_id', 'updated_at', 'created_at']);

        $data['preferred_bank'] = BankAccountResource::make(
            $this->user->bank_accounts()->where('id', $data['preferred_bank_id'])->first()
        );

        return $data;
    }
}
