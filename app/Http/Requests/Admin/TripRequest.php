<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TripRequest extends FormRequest
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
        $id = $this->route('trip'); // اسم الباراميتر في Route

        return [

            'name' => 'required|max:255',
            'name_en' => 'required|max:255',
            'license_number' => 'required|max:255|unique:trips,license_number,'.$id,
            'permit_type' => 'required|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'departure_time' => 'required',
            'return_time' => 'required',
            'owner_id' => 'required|integer|exists:users,id',
            'captain_id' => 'required|integer|exists:users,id',
            'boat_id' => 'required|numeric|exists:boats,id',
            'boat_name' => 'nullable|max:255',
            //            'boat_number'=>'required|max:255',
            //            'boat_color'=>'required|max:255',
            //            'boat_length'=>'required|max:255',
            //            'boat_width'=>'required|max:255',
            //            'crew_count'=>'required|numeric|min:1',
            'region_id' => 'required|exists:regions,id',
            'governorate_id' => 'required|exists:governorates,id',
            //            'city_id'=>'required|exists:cities,id',
            'port_id' => 'required|exists:ports,id',
            'notes' => 'nullable|max:255',
            'license_attachment' => 'nullable|mimes:jpeg,jpg,png,pdf|max:4096',

        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'الاسم مطلوب',
            'name_en.required' => 'الاسم باللغة الإنجليزية مطلوب',
            'license_number.required' => 'رقم الترخيص مطلوب',
            'permit_type.required' => 'نوع الترخيص مطلوب',
            'start_date.required' => 'تاريخ البدء مطلوب',
            'end_date.required' => 'تاريخ الانتهاء مطلوب',
            'departure_time.required' => 'وقت البدء مطلوب',
            'return_time.required' => 'وقت الانتهاء مطلوب',
            'owner_id.required' => 'الصيّاد مطلوب',
            'captain_id.required' => 'القائد مطلوب',
            'boat_id.required' => 'القارب مطلوب',
            'boat_name.required' => 'اسم القارب مطلوب',
            'region_id.required' => 'المنطقة مطلوبة',
            'governorate_id.required' => 'المحافظة مطلوبة',
            'port_id.required' => 'الميناء مطلوب',
            'notes.required' => 'الملاحظات مطلوبة',
            'license_attachment.required' => 'الترخيص مطلوب',
        ];
    }
}
