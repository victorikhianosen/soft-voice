<?php

namespace App\Http\Resources;

use Illuminate\Support\Arr;
use Illuminate\Http\Resources\Json\JsonResource;

class RestrictionResource extends JsonResource
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
        Arr::set($data, 'value', $data['pivot']['value']);
        Arr::forget($data, ['created_at', 'updated_at', 'pivot']);
        return $data;
    }
}
