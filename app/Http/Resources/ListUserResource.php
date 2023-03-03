<?php

namespace App\Http\Resources;

use Illuminate\Support\Arr;
use Illuminate\Http\Resources\Json\JsonResource;

class ListUserResource extends JsonResource
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
        Arr::forget($data, [
            'email_verified_at', 'email_verification_token',
            'email_verification_token_expiry', 'updated_at'
        ]);
        $data['active'] = $data['active'] === 1;
        $data['wallet_balance'] = $this->wallet->balance;
        return $data;
    }
}
