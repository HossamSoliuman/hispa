<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'package_id' => 'required|exists:subscription_packages,id',
            'start_date' => 'required|date',
            'duration_type' => 'nullable|in:monthly,quarterly,yearly',
            'is_trial' => 'boolean',
            'trial_days' => 'nullable|integer|min:1|max:30',
        ];
    }
}
