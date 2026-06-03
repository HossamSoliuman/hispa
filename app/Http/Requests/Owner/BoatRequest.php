<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class BoatRequest extends FormRequest
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
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'number' => 'required|string|max:100',
            'status' => 'required|in:0,1',
            'length' => 'required|numeric',
            'width' => 'required|numeric',
            'color' => 'required|string|max:50',
            'type' => 'required|string',
            'license_region_id' => 'required|exists:regions,id',
            'license_date' => 'required|date',
            'license_date_expire' => 'required|date|after_or_equal:license_date',
            'body_number' => 'required|string|max:100',
            'body_type' => 'required|string|max:100',
            'callsign_number' => 'required|string|max:100',
            'serial_number' => 'required|string|max:100',
            'engine_type' => 'required|string|max:100',
            'engine_power' => 'required|string|max:100',
            'crew_number' => 'required|numeric:|min:0',
            'payload' => 'required|numeric|min:0',
            'region_id' => 'required|exists:regions,id',
            'governorate_id' => 'required|exists:governorates,id',
            'port_id' => 'required|exists:ports,id',
            'boat_type_id' => 'required|exists:boat_types,id',

        ];
    }
}
