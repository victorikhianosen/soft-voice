<?php

namespace App\Http\Resources;

use Illuminate\Support\Arr;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
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
        $data['title'] = $data['data']['title'];
        $data['body'] = $data['data']['body'];
        $data['read'] = $data['read_at'] !== null;
        Arr::forget($data, ['type', 'notifiable_id', 'updated_at', 'notifiable_type', 'data', 'read_at']);
        return $data;
    }
}
