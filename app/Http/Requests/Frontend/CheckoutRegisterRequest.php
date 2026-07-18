<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CheckoutRegisterRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $packageRules = [
            'required',
            'integer',
            Rule::exists('subscription_packages', 'id')->where('is_active', true),
        ];

        if (Auth::guard('owner')->check()) {
            return [
                'package_id' => $packageRules,
            ];
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:255', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'package_id' => $packageRules,
        ];
    }

    /**
     * Get the validation messages for checkout account creation.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => __('validation.required', ['attribute' => __('site.signup.name')]),
            'email.required' => __('validation.required', ['attribute' => __('site.signup.email')]),
            'email.email' => __('validation.email', ['attribute' => __('site.signup.email')]),
            'email.unique' => __('validation.unique', ['attribute' => __('site.signup.email')]),
            'phone.required' => __('validation.required', ['attribute' => __('site.signup.phone')]),
            'phone.unique' => __('validation.unique', ['attribute' => __('site.signup.phone')]),
            'password.required' => __('validation.required', ['attribute' => __('site.signup.password')]),
            'password.min' => __('validation.min.string', [
                'attribute' => __('site.signup.password'),
                'min' => 8,
            ]),
            'password.confirmed' => __('validation.confirmed', ['attribute' => __('site.signup.password')]),
            'package_id.required' => __('marketing.checkout.plan_unavailable'),
            'package_id.exists' => __('marketing.checkout.plan_unavailable'),
        ];
    }
}
