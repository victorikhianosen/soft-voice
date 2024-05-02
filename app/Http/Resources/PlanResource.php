<?php

namespace App\Http\Resources;

use Illuminate\Support\Arr;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
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
        Arr::forget($data, ['created_at', 'updated_at']);
        Arr::set($data, 'active', $data['active'] === 1);
        Arr::set($data, 'duration', $data['duration'] === -1 ? 'unlimited' : $data['duration']);
        Arr::set($data, 'restrictions', RestrictionResource::collection($this->restrictions->where('name', '!=', 'whatsapp-messaging')));
        return $data;
    }
}
