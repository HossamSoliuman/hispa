<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class OwnerRequest extends FormRequest
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
        $id = $this->route('owner'); // اسم الباراميتر في Route

        return [
            'name' => 'required|max:255',
            'phone' => 'required|max:255|unique:users,phone,'.$id,
            'email' => 'required|email|max:255|unique:users,email,'.$id,
            'id_number' => 'required|max:255|unique:users,id_number,'.$id,
            'record_number' => 'required',
            'tax_number' => 'required',
            'region_id' => 'required|exists:regions,id',
            'governorate_id' => 'required|exists:governorates,id',
            //            'city_id'=>'required|exists:cities,id',
            'logo' => 'mimes:jpeg,jpg,png|max:2048',
            'attachment' => 'mimes:jpeg,jpg,png,pdf|max:2048',

        ];
    }
}
