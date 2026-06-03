<?php

namespace App\Http\Requests\Gov;

use Illuminate\Foundation\Http\FormRequest;

class SeasonRequest extends FormRequest
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
            'name' => 'required|max:255',
            'licenses_count' => 'required|numeric',
            'status' => 'required|in:1,2,3,0',
            'region_id' => 'required|numeric|exists:regions,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            //            'min_limit' => 'required|numeric',
            //            'max_limit' => 'required|numeric',
            'rules' => 'required|string|max:500',
            'fishing_tool.*' => 'exists:fishing_tools,id',

        ];
    }
}
