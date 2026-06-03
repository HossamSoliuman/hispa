<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSubscriptionPackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'boats_count' => ['required', 'integer', 'min:1'],
            'original_price' => ['required', 'numeric', 'min:0'],
            'price' => [
                'nullable',
                'numeric',
                'min:0',
                'lt:original_price',
            ],
            'duration_type' => ['required', Rule::in(['monthly', 'quarterly', 'yearly'])],
            'features' => ['nullable', 'array'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'sort_order' => ['nullable', 'integer'],
            'feature_ar' => ['nullable', 'array'],
            'feature_ar.*' => ['nullable', 'string'],
            'feature_en' => ['nullable', 'array'],
            'feature_en.*' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'original_price' => __('admin.subscription_packages.original_price'),
            'price' => __('admin.subscription_packages.offer_price'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'price.lt' => __('admin.subscription_packages.offer_must_be_less_than_original'),
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'is_featured' => $this->boolean('is_featured'),
        ]);
    }
}
