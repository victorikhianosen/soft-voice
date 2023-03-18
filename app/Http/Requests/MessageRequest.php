<?php

namespace App\Http\Requests;

use App\Rules\ValidRecipients;
use Illuminate\Foundation\Http\FormRequest;

class MessageRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "recipients" => ["required", new ValidRecipients],
            "subject"    => ["required", "string"],
            "body"       => ["required", "string"],
            "inapp_notification" => ["required", "boolean"],
        ];
    }
}
