<?php

namespace App\Http\Requests;

use App\Enums\Notification;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SettingNotificationsUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'notifications' => ['sometimes', 'array'],
            'notifications.*' => ['required', Rule::in(Notification::notificationsName())],
        ];
    }
}
