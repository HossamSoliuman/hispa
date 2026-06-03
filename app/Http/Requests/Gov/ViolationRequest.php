<?php

namespace App\Http\Requests\Gov;

use Illuminate\Foundation\Http\FormRequest;

class ViolationRequest extends FormRequest
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
            'id_number' => 'required|numeric:',
            'description' => 'required|string|max:500',
            'violation_date' => 'required|date',
            'violation_time' => 'required',
            'region_id' => 'required|exists:regions,id',
            'governorate_id' => 'required|exists:governorates,id',
            //            'city_id'=>'required|exists:cities,id',
            'port_id' => 'nullable|exists:ports,id',
            //            'location'=>'required',
            'fine_amount' => 'required|numeric',

        ];
    }
}
