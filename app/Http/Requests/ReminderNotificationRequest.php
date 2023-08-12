<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Enums\NotificationChannel;

class ReminderNotificationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'channels' => ['required', 'array'],
            'channels.*' => ['required', 'string', Rule::in(NotificationChannel::channels())]
        ];
    }
}
