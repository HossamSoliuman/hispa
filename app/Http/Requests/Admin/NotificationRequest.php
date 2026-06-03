<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class NotificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'userType' => 'required|in:user,admin',
            'role' => 'nullable|string',
            'recipientType' => 'required|in:all,specific',
            'recipient_ids' => 'nullable|array',
            'recipient_ids.*' => 'integer',
        ];
    }
}
