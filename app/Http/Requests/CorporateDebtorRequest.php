<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CorporateDebtorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'type' => 'required|in:0,1,2',
            'ses_email_id' => 'nullable|exists:ses_verified_emails,id',
        ];

        if ($this->isMethod('post')) {
            $rules['email'] = 'required|email|unique:users,email';
            $rules['password'] = 'required|string|min:6|confirmed';
        } else {
            $rules['email'] = 'required|email|unique:users,email,' . $this->route('corporate_debtor');
            $rules['password'] = 'nullable|string|min:6|confirmed';
        }

        return $rules;
    }
}
