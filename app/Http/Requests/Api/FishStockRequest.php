<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Arr;

class FishStockRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function rules(): array
    {
        return [
            'trip_id' => 'required|exists:trips,id',
            'fish_id' => 'required|exists:fish,id',
            'fish_name' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0',
            'quantity' => 'nullable|integer|min:1',
            'quantity_captain' => 'nullable|integer|min:0',
            'weight_captain' => 'nullable|numeric|min:0',
            'quantity_counter' => 'nullable|integer|min:0',
            'weight_counter' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'notes_by_counter' => 'nullable|string',
        ];
    }

    //    public function messages()
    //    {
    //        return[
    //            'phone.required'=>trans('site.phone_required'),
    //            'password.required'=>trans('site.password.is_required'),
    //            'fcm_token.required'=>trans('validation.fcm_token'),
    //
    //        ];
    //    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 422,
            'message' => Arr::first(Arr::flatten($validator->messages()->get('*'))),
            'data' => $validator->errors(),
        ], 422));

    }
}
