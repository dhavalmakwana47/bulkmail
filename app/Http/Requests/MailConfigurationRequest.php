<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MailConfigurationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'from_name' => 'required|string|max:255',
            'reply_email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'exists:debtor_attachments,id',
            'send_type' => 'required|in:NOW,SCHEDULED',
            'scheduled_at' => 'nullable|required_if:send_type,SCHEDULED|date|after:now',
        ];
    }
}
