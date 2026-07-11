<?php

namespace App\Http\Requests\Owner\Startup;

use Illuminate\Foundation\Http\FormRequest;

class ExpenseCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['name_ar' => ['required', 'string', 'max:255'], 'name_en' => ['nullable', 'string', 'max:255'], 'is_active' => ['nullable', 'boolean']];
    }

    public function messages(): array
    {
        return ['name_ar.required' => __('owner.startup.validation.name_required')];
    }
}
