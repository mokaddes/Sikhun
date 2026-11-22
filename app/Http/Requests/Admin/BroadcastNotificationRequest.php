<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BroadcastNotificationRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'type' => ['nullable', 'in:admin_broadcast,special_offer'],
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:1000'],
            'action_url' => ['nullable', 'string', 'max:500'],
            'send_email' => ['nullable', 'boolean'],
            'send_now' => ['nullable', 'boolean'],
            'target_audience' => ['required', 'in:all,ssc,hsc,admission,university,job'],
            'scheduled_for' => ['nullable', 'date'],
        ];
    }
}
