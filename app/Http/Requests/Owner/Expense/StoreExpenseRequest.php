<?php

namespace App\Http\Requests\Owner\Expense;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'date' => 'required|date',
            'expense_type' => 'required|in:general,government,operating,maintenance',
            'boat_id' => 'required|exists:boats,id',
            'vendor_id' => 'required|exists:users,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'status' => 'required|in:paid,pending',
            'notes' => 'nullable|string|max:1000',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
        ];

        if ($this->expense_type === 'general' || $this->expense_type === 'government') {
            $rules = array_merge($rules, [
                'category_id' => 'required|exists:categories,id',
                'description' => ['required', 'string', 'max:255'],
                'total_price' => 'required|numeric|min:0',
                'discount_type' => 'required|in:none,percentage,fixed',
                'discount_value' => 'nullable|numeric|min:0',
            ]);
            if ($this->discount_type === 'percentage') {
                $rules['discount_value'] .= '|max:100';
            }
        }

        if ($this->expense_type === 'maintenance') {
            $rules = array_merge($rules, [
                'selected_maintenances' => 'required|array|min:1',
                'selected_maintenances.*' => 'required|exists:maintenances,id',
                'discount_type_maintenance' => 'required|in:none,percentage,fixed',
                'discount_value_maintenance' => 'nullable|numeric|min:0',
            ]);
            if ($this->discount_type_maintenance === 'percentage') {
                $rules['discount_value_maintenance'] .= '|max:100';
            }
        }

        // تشغيلية
        if ($this->expense_type === 'operating') {
            $rules = array_merge($rules, [
                'category_id' => 'required|exists:categories,id',
            ]);

            // إذا الفئة معدات صيد
            if ($this->has('fishing_equipment_id')) {
                $rules = array_merge($rules, [
                    'fishing_equipment_id' => 'required|array|min:1',
                    'fishing_equipment_id.*' => 'exists:fishing_equipment,id',
                    'quantity' => 'required|array',
                    'quantity.*' => 'integer|min:1',
                    'unit_price' => 'required|array',
                    'unit_price.*' => 'numeric|min:0',
                    // 'total_price_equipment' => 'nullable|array',
                    // 'total_price_equipment.*' => 'numeric|min:0',
                    'discount_type_operating' => 'required|in:none,percentage,fixed',
                    'discount_value_operating' => 'nullable|numeric|min:0',
                    'final_price_operating' => 'required|numeric|min:0',
                ]);
                if ($this->discount_type_operating === 'percentage') {
                    $rules['discount_value_operating'] .= '|max:100';
                }
            } else {
                $rules = array_merge($rules, [
                    'description_operating' => 'required|string|max:255',
                    'total_price_operating' => 'required|numeric|min:0',
                    'discount_type_operating' => 'required|in:none,percentage,fixed',
                    'discount_value_operating' => 'nullable|numeric|min:0',
                    'final_price_operating' => 'required|numeric|min:0',
                ]);
                if ($this->discount_type_operating === 'percentage') {
                    $rules['discount_value_operating'] .= '|max:100';
                }
            }
        }

        return $rules;
    }

    protected function prepareForValidation()
    {
        if ($this->boat_id === 'general') {
            $this->merge(['boat_id' => null]);
        }
        if ($this->discount_type === 'none') {
            $this->merge(['discount_value' => 0]);
        }
        if ($this->discount_type_operating === 'none') {
            $this->merge(['discount_value_operating' => 0]);
        }
        if ($this->discount_type_maintenance === 'none') {
            $this->merge(['discount_value_maintenance' => 0]);
        }

        if ($this->has('description')) {
            $this->merge(['notes' => $this->description]);
        }
        if ($this->has('description_operating')) {
            $this->merge(['notes' => $this->description_operating]);
        }
        if ($this->has('description_maintenance')) {
            $this->merge(['notes' => $this->description_maintenance]);
        }
    }

    public function messages()
    {
        return [
            'date.required' => 'يجب إدخال تاريخ المصروف',
            'date.date' => 'صيغة التاريخ غير صحيحة',

            'expense_type.required' => 'نوع المصروف مطلوب',
            'expense_type.in' => 'نوع المصروف غير صحيح',

            'boat_id.required' => 'يجب اختيار قارب',
            'boat_id.exists' => 'القارب المحدد غير موجود',
            'vendor_id.required' => 'المورد مطلوب',
            'vendor_id.exists' => 'المورد المحدد غير موجود',
            'payment_method_id.required' => 'طريقة الدفع مطلوبة',
            'payment_method_id.exists' => 'طريقة الدفع غير صحيحة',

            'status.required' => 'حالة المصروف مطلوبة',
            'status.in' => 'القيمة المدخلة لحالة المصروف غير صحيحة',

            'notes.string' => 'الملاحظات يجب أن تكون نصًا',
            'notes.max' => 'الملاحظات لا يجب أن تتجاوز 1000 حرف',

            // عام
            'category_id.required' => 'يجب اختيار الفئة',
            'category_id.exists' => 'الفئة المحددة غير صحيحة',
            'description.required' => 'الوصف مطلوب',
            'description.max' => 'الوصف لا يجب أن يتجاوز 255 حرفًا',
            'total_price.required' => 'إجمالي المبلغ مطلوب',
            'total_price.numeric' => 'إجمالي المبلغ يجب أن يكون رقمًا',
            'total_price.min' => 'إجمالي المبلغ لا يمكن أن يكون سالبًا',
            'discount_type.required' => 'نوع الخصم مطلوب',
            'discount_type.in' => 'نوع الخصم غير صحيح',
            'discount_value.numeric' => 'قيمة الخصم يجب أن تكون رقمًا',
            'discount_value.min' => 'قيمة الخصم لا يمكن أن تكون سالبة',
            'discount_value.max' => 'قيمة الخصم بالنسبة المئوية يجب ألا تتجاوز 100%',

            // معدات
            'fishing_equipment_id.required' => 'يجب اختيار معدات واحدة على الأقل',
            'fishing_equipment_id.*.exists' => 'أحد المعدات المختارة غير موجود',
            'quantity.required' => 'الكمية مطلوبة',
            'quantity.*.integer' => 'الكمية يجب أن تكون عددًا صحيحًا',
            'quantity.*.min' => 'الكمية يجب أن تكون على الأقل 1',
            'unit_price.required' => 'سعر الوحدة مطلوب',
            'unit_price.*.numeric' => 'سعر الوحدة يجب أن يكون رقمًا',
            'unit_price.*.min' => 'سعر الوحدة لا يمكن أن يكون سالبًا',
            'discount_type_operating.required' => 'نوع الخصم للمعدات مطلوب',
            'discount_type_operating.in' => 'نوع الخصم للمعدات غير صحيح',
            'discount_value_operating.numeric' => 'قيمة الخصم للمعدات يجب أن تكون رقمًا',
            'discount_value_operating.min' => 'قيمة الخصم للمعدات لا يمكن أن تكون سالبة',
            'discount_value_operating.max' => 'قيمة الخصم بالنسبة المئوية للمعدات يجب ألا تتجاوز 100%',

            // صيانة
            'selected_maintenances.required' => 'يجب اختيار عملية صيانة واحدة على الأقل',
            'selected_maintenances.*.exists' => 'إحدى عمليات الصيانة غير صحيحة',
            'discount_type_maintenance.required' => 'نوع الخصم للصيانة مطلوب',
            'discount_type_maintenance.in' => 'نوع الخصم للصيانة غير صحيح',
            'discount_value_maintenance.numeric' => 'قيمة الخصم للصيانة يجب أن تكون رقمًا',
            'discount_value_maintenance.min' => 'قيمة الخصم للصيانة لا يمكن أن تكون سالبة',
            'discount_value_maintenance.max' => 'قيمة الخصم بالنسبة المئوية للصيانة يجب ألا تتجاوز 100%',
        ];
    }
}
