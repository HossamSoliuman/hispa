<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Arr;

class FishDalalStockRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function rules(): array
    {
        return [
            'dalal_id' => 'required|exists:users,id',
            'trip_id' => 'required|exists:trips,id',
            'fish_id' => 'required|exists:fish,id',
            'fish_name' => 'nullable|string',
            'weight' => 'required|numeric|min:0.01',
            'quantity' => 'nullable|integer|min:1',
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
