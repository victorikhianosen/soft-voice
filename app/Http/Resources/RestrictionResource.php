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
        $value = $data['pivot']['value'];
        Arr::set($data, 'value', $value === -1 ? 'unlimited' : $value);
        if ($data['unit'] === 'boolean') {
            Arr::set($data, 'value', $value === 1);
        }
        Arr::forget($data, ['created_at', 'updated_at', 'pivot']);
        return $data;
    }
}
