<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\Enums\NotificationChannel;
use Illuminate\Foundation\Http\FormRequest;

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
            'channels' => ['required', 'array', Rule::in(NotificationChannel::channels())]
        ];
    }
}
