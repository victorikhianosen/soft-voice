<?php

namespace App\Http\Resources;

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

        $data['auto_enable_bank_account'] = $data['auto_enable_bank_account'] === 1;
        $data['payment_history_on_invoice_page'] = $data['payment_history_on_invoice_page'] === 1;
        $data['auto_enable_tax'] = $data['auto_enable_tax'] === 1;
        $data['auto_payment_reminder'] = $data['auto_payment_reminder'] === 1;
        $data['signature'] = $data['signature'] ? asset($data['signature']) : "";

        return $data;
    }
}
