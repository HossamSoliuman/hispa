<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Arr;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function rules(): array
    {
        return [
            'phone' => 'required',
            'password' => 'required',
            'fcm_token' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'phone.required' => trans('site.phone_required'),
            'password.required' => trans('site.password.is_required'),
            'fcm_token.required' => trans('validation.fcm_token'),

        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 422,
            'message' => Arr::first(Arr::flatten($validator->messages()->get('*'))),
            'data' => $validator->errors(),
        ], 422));

    }
}
