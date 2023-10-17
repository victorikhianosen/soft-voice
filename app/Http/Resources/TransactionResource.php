<?php

namespace App\Http\Resources;

use App\Models\Admin;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
        if ($request->user() instanceof Admin) {
            $data['user'] = $this->user->only(['id', 'name', 'email']);
            unset($data['user_id']);
        }
        return $data;
    }
}
