<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Arr;

class UpdateDalalSaleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'commission_rate' => 'nullable|numeric|min:0',
            'labor_rate' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            //            'fish_id' => 'required|exists:fish,id',
            //            'fish_name' => 'required|string',
            'quantity' => 'nullable|integer|min:1',
            'weight' => 'required|numeric|min:0.01',
            'price_per_kilo' => 'required|numeric|min:0.01',

        ];
    }

    public function messages()
    {
        return [];
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
