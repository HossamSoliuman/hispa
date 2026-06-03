<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Arr;

class ProfileUserRequest extends FormRequest
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
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,'.$this->user()->id,
            'phone' => 'nullable|string|unique:users,phone,'.$this->user()->id,
            'region_id' => 'nullable|exists:regions,id',
            'governorate_id' => 'nullable|exists:governorates,id',
            //            'city_id' => 'nullable|exists:cities,id',
            'port_id' => 'nullable|exists:ports,id',
            'address' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'record_type' => 'nullable|string|max:100',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
            'id_number' => 'nullable|string|unique:users,id_number,'.$this->user()->id,
            'tax_number' => 'nullable|string|max:100',
            'nationality' => 'nullable|string|max:100',
            'boat_name' => 'nullable|string|max:100',
            'boat_number' => 'nullable|string|max:100',
            'crew_count' => 'nullable|integer|min:1',
        ];
    }

    //    public function messages()
    //    {
    //        return[
    //            'name.required'=>trans('site.name_required'),
    //            'email.required'=>trans('site.email.is_required'),
    //            'address.required'=>trans('site.address_required'),
    //            'id_card.required'=>trans('site.id_card_required'),
    //            'phone.required'=>trans('site.phone_required'),
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
