<?php

namespace App\Http\Requests\Owner;

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
        $id = $this->route('trip');

        return [

            'name' => 'required|max:255',
            'name_en' => 'required|max:255',
            'license_number' => 'required|max:255|unique:trips,license_number,'.$id,
            'start_date' => 'required|date_format:Y-m-d\TH:i',
            'duration' => 'nullable|integer|min:1',
            'end_date' => 'nullable|date_format:Y-m-d\TH:i|after_or_equal:start_date',
            'owner_id' => 'required|integer|exists:users,id',
            'captain_id' => 'required|integer|exists:users,id',
            'boat_id' => 'required|numeric|exists:boats,id',
            'boat_name' => 'nullable|max:255',
            'notes' => 'nullable|max:255',

            'quick_expenses' => 'nullable|array',
            'quick_expenses.*.category_id' => 'nullable|integer|exists:categories,id',
            'quick_expenses.*.vendor_id' => 'nullable|integer|exists:users,id',
            'quick_expenses.*.amount' => 'nullable|numeric|min:0',
            'quick_expenses_status' => 'nullable|in:paid,pending',

        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'الاسم مطلوب',
            'name_en.required' => 'الاسم باللغة الإنجليزية مطلوب',
            'license_number.required' => 'رقم الترخيص مطلوب',
            'start_date.required' => 'تاريخ البدء مطلوب',
            'duration.min' => 'مدة الرحلة يجب أن تكون يوماً واحداً على الأقل',
            'end_date.after_or_equal' => 'تاريخ الانتهاء يجب أن يكون بعد تاريخ البدء',
            'owner_id.required' => 'الصيّاد مطلوب',
            'captain_id.required' => 'القائد مطلوب',
            'boat_id.required' => 'القارب مطلوب',
            'boat_name.required' => 'اسم القارب مطلوب',
            'notes.required' => 'الملاحظات مطلوبة',
        ];
    }
}
