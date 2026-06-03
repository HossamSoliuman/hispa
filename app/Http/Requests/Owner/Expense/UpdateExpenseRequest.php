<?php

namespace App\Http\Requests\Owner\Expense;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpenseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'date' => 'required|date',
            'vendor_id' => 'required|exists:users,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'status' => 'required|in:paid,pending',
            'notes' => 'nullable|string|max:1000',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
        ];

        $expense = $this->route('expense');
        if (is_string($expense)) {
            $expense = \App\Models\Expense::with('category.parent')->find($expense);
        }

        $expenseType = optional($expense->category->parent)->type
            ?? optional($expense->category)->type
            ?? null;

        if ($expenseType === 'general' || $expenseType === 'government' || ($expenseType === 'operating' && $expense->category->type != 'operating-equipments')) {
            $rules = array_merge($rules, [
                'description' => ['nullable', 'string', 'max:255'],
                'total_price' => 'required|numeric|min:0',
                'discount_type' => 'required|in:none,percentage,fixed',
                'discount_value' => 'nullable|numeric|min:0',
            ]);
            if ($this->discount_type === 'percentage') {
                $rules['discount_value'] .= '|max:100';
            }
        }

        if ($expenseType === 'operating' && $expense->category->type == 'operating-equipments') {
            $rules = array_merge($rules, [
                'fishing_equipment_id' => 'required|array|min:1',
                'fishing_equipment_id.*' => 'exists:fishing_equipment,id',
                'quantity' => 'required|array',
                'quantity.*' => 'integer|min:1',
                'unit_price' => 'required|array',
                'unit_price.*' => 'numeric|min:0',
                'discount_type' => 'required|in:none,percentage,fixed',
                'discount_value' => 'nullable|numeric|min:0',
            ]);
            if ($this->discount_type === 'percentage') {
                $rules['discount_value'] .= '|max:100';
            }
        }

        if ($expenseType === 'maintenance') {
            $rules = array_merge($rules, [
                'estimated_cost' => 'required|numeric|min:0',
                'maintenance_id' => 'required|numeric|min:1',
                'maintenance_id.*' => 'exists:maintenances,id',
                'discount_type' => 'required|in:none,percentage,fixed',
                'discount_value' => 'nullable|numeric|min:0',
            ]);
            if ($this->discount_type === 'percentage') {
                $rules['discount_value'] .= '|max:100';
            }
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'date.required' => 'يجب إدخال تاريخ المصروف',
            'date.date' => 'صيغة التاريخ غير صحيحة',

            'vendor_id.required' => 'المورد مطلوب',
            'vendor_id.exists' => 'المورد المحدد غير موجود',
            'payment_method_id.required' => 'طريقة الدفع مطلوبة',
            'payment_method_id.exists' => 'طريقة الدفع غير صحيحة',

            'status.required' => 'حالة المصروف مطلوبة',
            'status.in' => 'القيمة المدخلة لحالة المصروف غير صحيحة',

            'notes.string' => 'الملاحظات يجب أن تكون نصًا',
            'notes.max' => 'الملاحظات لا يجب أن تتجاوز 1000 حرف',

            // عام
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

            // صيانة

            'maintenance_id.required' => 'يجب اختيار عمليات صيانة ',
            'maintenance_id.*.exists' => 'رقم الصيانة غير موجود',
        ];
    }
}
