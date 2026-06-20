<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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

        $ownerId = auth()->id();

        return [

            'name' => 'required|max:255',
            'name_en' => 'required|max:255',
            'license_number' => 'required|max:255|unique:trips,license_number,'.$id,
            'start_date' => 'required|date_format:Y-m-d\TH:i',
            'duration' => 'nullable|integer|min:1',
            'end_date' => 'nullable|date_format:Y-m-d\TH:i|after_or_equal:start_date',
            'owner_id' => 'required|integer|exists:users,id',
            'notes' => 'nullable|max:255',

            'boats' => 'required|array|min:1',
            'boats.*.boat_id' => [
                'required', 'integer', 'distinct',
                Rule::exists('boats', 'id')->where('owner_id', $ownerId),
            ],
            'boats.*.captain_ids' => 'required|array|min:1',
            'boats.*.captain_ids.*' => [
                'required', 'integer',
                Rule::exists('users', 'id')->where('owner_id', $ownerId)->where('role', 'captain'),
            ],

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
            'boats.required' => 'يجب اختيار قارب واحد على الأقل',
            'boats.min' => 'يجب اختيار قارب واحد على الأقل',
            'boats.*.boat_id.required' => 'القارب مطلوب',
            'boats.*.boat_id.distinct' => 'لا يمكن تكرار نفس القارب في الرحلة',
            'boats.*.boat_id.exists' => 'القارب غير صحيح',
            'boats.*.captain_ids.required' => 'يجب اختيار قائد واحد على الأقل لكل قارب',
            'boats.*.captain_ids.min' => 'يجب اختيار قائد واحد على الأقل لكل قارب',
            'boats.*.captain_ids.*.exists' => 'القائد غير صحيح',
            'notes.required' => 'الملاحظات مطلوبة',
        ];
    }
}
