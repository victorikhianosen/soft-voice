<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class UserResource extends JsonResource
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
        Arr::set($data, 'subscription', new SubscriptionResource($this->activeSubscription));
        return $data;
    }
}
