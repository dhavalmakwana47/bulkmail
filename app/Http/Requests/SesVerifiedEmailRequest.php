<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SesVerifiedEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $emailRule = 'required|email|max:255|unique:ses_verified_emails,email';

        if ($this->isMethod('PUT')) {
            $emailRule .= ',' . $this->route('ses_verified_email')->id;
        }

        return [
            'email' => $emailRule,
            'active_status' => 'required|in:Y,N',
        ];
    }
}
