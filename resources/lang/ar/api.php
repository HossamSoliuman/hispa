<?php

return [
    'getData' => 'تم جلب البيانات بنجاح',
    'unauthorized_trip' => 'غير مصرح لك بإضافة بيانات هذه الرحلة',
    'invalid_trip_status_add' => 'لا يمكن إضافة بيانات للأسماك في هذه المرحلة من الرحلة',
    'duplicate_fish' => 'تم إدخال هذا الصنف مسبقًا لهذه الرحلة',
    'fish_added' => 'تم إضافة صنف السمك بنجاح',
    'fish_not_found' => 'الصنف غير موجود',
    'trip_not_found' => 'الرحلة غير موجودة',
    'invalid_trip_status_update' => 'لا يمكن تعديل بيانات الأسماك في هذه المرحلة من الرحلة',
    'unauthorized_update' => 'غير مصرح لك بتعديل هذا الصنف',
    'invalid_counter_status' => 'لا يمكن تعديل بيانات الأسماك بعد إكمال العد',
    'no_permission_role' => 'دورك لا يملك صلاحية التعديل على هذا الصنف',
    'fish_updated' => 'تم تعديل بيانات صنف السمك بنجاح',
    'unauthorized_delete' => 'غير مصرح لك بحذف هذا الصنف',
    'fish_deleted' => 'تم حذف صنف السمك بنجاح',

    // عام
    'unknown' => 'غير معروف',
    'status_completed' => 'مكتملة',
    'no_notes' => 'لا يوجد ملاحظات',

    // صلاحيات/وصول
    'unauthorized_view' => 'غير مصرح لك بعرض البيانات',
    'unauthorized_create' => 'غير مصرح لك بإنشاء عملية بيع',
    'unauthorized_update_sale' => 'غير مصرح لك بتعديل هذا البيع',
    'unauthorized_delete_data' => 'غير مصرح لك بحذف البيانات',
    'not_authorized_delete_invoice' => 'ليس لديك صلاحية حذف هذه الفاتورة',

    // قوائم/جلب بيانات
    'no_sales_for_stock' => 'لا يوجد مبيعات لهذا المخزون',
    'invoices_fetched' => 'تم جلب الفواتير بنجاح',
    'details_fetched' => 'تم جلب تفاصيل الفاتورة بنجاح',

    // فواتير
    'invoice_not_found_or_forbidden' => 'الفاتورة غير موجودة أو لا تملك صلاحية تعديلها',
    'invoice_not_found_or_not_yours' => 'الفاتورة غير موجودة أو غير تابعة لك',
    'invoice_already_closed' => 'تم إنهاء هذه الفاتورة مسبقاً ولا يمكن تعديلها',
    'invoice_no_items' => 'لا يمكن إنهاء الفاتورة لأنها لا تحتوي على أي صنف',
    'invoice_closed_successfully' => 'تم إنهاء الفاتورة بنجاح',
    'invoice_id_required' => 'رقم الفاتورة مطلوب',
    'cannot_delete_completed_invoice' => 'لا يمكنك الحذف من هذه الفاتورة. الفاتورة مكتملة',
    'invoice_has_items' => 'لا يمكن حذف الفاتورة لأنها تحتوي على أصناف',
    'invoice_deleted' => 'تم حذف الفاتورة بنجاح',

    // إعدادات/مرجعيات
    'no_commission_setting' => 'لا يوجد نسبة مضافة لك يرجى التواصل مع المسؤول',
    'customer_not_found' => 'العميل غير موجود',
    'payment_method_not_found' => 'وسيلة الدفع غير موجودة',

    // المخزون/العناصر
    'stock_not_found_for_fish' => 'لا يوجد مخزون متاح للسمكة: :fish في السجل المحدد.',
    'fish_already_added' => 'تمت إضافة هذه السمكة مسبقاً في نفس الفاتورة: :fish',
    'requested_exceeds_stock' => 'الكمية أو الوزن المطلوب يتجاوز المخزون المتاح للسمكة: :fish في هذا السجل.',
    'stock_detail_missing' => 'لم يتم العثور على سجل المخزون المرتبط.',
    'new_requested_exceeds_stock' => 'الكمية أو الوزن الجديد يتجاوز المتاح في المخزون.',
    'dalal_stock_not_found' => 'مخزون الدلال غير موجود.',
    'invoice_details_not_linked_to_dalal_stock' => 'تفاصيل الفاتورة غير مرتبطة بمخزون دلال.',
    'insufficient_stock' => 'لا يوجد كمية كافية في مخزون الصيّاد لهذا الصنف.',
    'exceeds_stock' => 'الوزن الجديد مع السابق يتجاوز المخزون: :fish',

    // عمليات على الأصناف
    'item_added' => 'تمت إضافة الصنف بنجاح إلى الفاتورة',
    'item_updated' => 'تم تعديل الصنف بنجاح',
    'item_deleted_updated_stock' => 'تم حذف الصنف بنجاح وتحديث المخزون',

    'stocks_fetched' => 'تم جلب بيانات المخزون بنجاح',
    'stock_not_found' => 'المخزون غير موجود أو لا يتبع هذا المستخدم',
    'stock_details_fetched' => 'تم جلب تفاصيل المخزون بنجاح',

    // رسائل النجاح
    'list_success' => 'تم جلب مخزون الدلالين بنجاح',
    'status_updated' => 'تم تحديث حالة المخزون بنجاح.',
    'detail_fetched' => 'تم جلب مخزون الدلال بنجاح',
    'item_added' => 'تمت إضافة الصنف بنجاح وتم خصم الكمية من مخزون الصيّاد.',
    'item_updated' => 'تم تحديث بيانات صنف السمك بنجاح.',
    'item_deleted' => 'تم حذف صنف السمك بنجاح واسترجاع الكمية للمخزون الأصلي.',

    // رسائل الفشل
    'stock_not_found' => 'مخزون الدلال غير موجود.',
    'cannot_set_status' => 'لا يمكنك تغيير الحالة إلى 1 بدون إضافة كميات للأسماك.',
    'insufficient_stock' => 'لا يوجد كمية كافية في مخزون الصيّاد لهذا الصنف.',
    'item_exists' => 'هذا الصنف مضاف مسبقًا لهذا الدلال في هذه الرحلة. الوزن الحالي: :weight كغ',
    'not_owner' => 'دورك لا يملك صلاحية تعديل هذا الصنف.',
    'detail_not_found' => 'تفاصيل الصنف غير موجودة.',
    'cannot_edit_item' => 'لا يمكنك تعديل صنف لا يخصك.',
    'fishstock_not_found' => 'لا يوجد مخزون لهذا الصنف في مخزون الصيّاد.',
    'insufficient_stock_update' => 'لا يوجد كمية كافية في مخزون الصيّاد لتعديل الوزن.',
    'cannot_delete_item' => 'لا يمكنك حذف صنف لا يخصك.',

    // أخطاء عامة
    'error_saving' => 'حدث خطأ أثناء الحفظ: :error',
    'error_updating' => 'حدث خطأ أثناء التحديث: :error',
    'error_deleting' => 'حدث خطأ أثناء الحذف: :error',

    // رسائل النجاح
    'list_sale_success' => 'تم جلب المبيعات بنجاح',
    'sale_completed' => 'تم إنهاء الفاتورة بنجاح',
    'detail_fetched' => 'تم جلب الفاتورة بنجاح',
    'item_added' => 'تمت إضافة الصنف بنجاح إلى الفاتورة',
    'item_updated' => 'تم تعديل بيانات الصنف بنجاح',
    'sale_deleted' => 'تم حذف الفاتورة بنجاح',
    'detail_deleted' => 'تم حذف الصنف بنجاح',

    // رسائل الفشل
    'unauthorized_list' => 'غير مصرح لك بعرض المبيعات',
    'sale_not_found' => 'الفاتورة غير موجودة',
    'sale_already_completed' => 'تم إنهاء هذه الفاتورة مسبقاً ولا يمكن تعديلها',
    'empty_sale' => 'لا يمكن إنهاء الفاتورة لأنها لا تحتوي على أي صنف',
    'unauthorized_view' => 'غير مصرح لك بعرض هذه الفاتورة',
    'unauthorized_add' => 'غير مصرح لك بإنشاء عملية بيع',
    'trip_not_found' => 'الرحلة غير موجودة',
    'trip_completed' => 'لا يمكن الإضافة. الرحلة مكتملة بالفعل',
    'customer_not_found' => 'العميل غير موجود',
    'payment_method_not_found' => 'وسيلة الدفع غير موجودة',
    'item_exists' => 'تمت إضافة هذه السمك مسبقاً في نفس الفاتورة: :fish',
    'insufficient_stock' => 'الوزن المطلوب (:weight كجم) + المباع سابقاً (:already) يتجاوز المخزون المتاح (:stock كجم) للسمكة: :fish',
    'unauthorized_edit' => 'غير مصرح لك بتعديل عملية البيع',
    'item_not_found' => 'الصنف المطلوب غير موجود',
    'unauthorized_delete' => 'ليس لديك صلاحية حذف هذه الفاتورة',
    'sale_has_items' => 'لا يمكن حذف الفاتورة لأنها تحتوي على اصناف',
    'cannot_delete_detail' => 'لا يمكنك حذف من هذه الفاتورة الفاتورة مكتملة',
    'trip_completed_delete' => 'لا يمكن الحذف. الرحلة مكتملة بالفعل',

    'boat_added' => 'تم إضافة القارب بنجاح',
    'boat_updated' => 'تم تحديث بيانات القارب بنجاح',
    'boat_deleted' => 'تم حذف القارب بنجاح',
    'boat_not_found' => 'القارب غير موجود',
    'error_saving' => 'حدث خطأ أثناء حفظ البيانات: :error',
    'error_updating' => 'حدث خطأ أثناء تحديث البيانات: :error',
    'error_deleting' => 'حدث خطأ أثناء حذف البيانات: :error',

    'captain_added' => 'تم إضافة الكابتن بنجاح',
    'captain_updated' => 'تم تحديث بيانات الكابتن بنجاح',
    'captain_deleted' => 'تم حذف الكابتن بنجاح',
    'captain_not_found' => 'الكابتن غير موجود',
    'captain_has_trips' => 'لا يمكن حذف الكابتن لأنه مرتبط برحلات. يرجى حذف أو نقل رحلاته أولاً',

    'crew_added' => 'تم إضافة الطاقم بنجاح',
    'crew_updated' => 'تم تحديث بيانات الطاقم بنجاح',
    'crew_deleted' => 'تم حذف الطاقم بنجاح',
    'crew_not_found' => 'الطاقم غير موجود',

    'trip_canceled' => 'تم إلغاء الرحلة بنجاح',
    'trip_completed' => 'لا يمكن الإضافة. الرحلة مكتملة بالفعل',
    'trip_invalid_transition' => 'لا يمكن الانتقال من هذه الحالة إلى الحالة المطلوبة',
    'trip_invalid_owner_transition' => 'فقط الصيّاد يمكنه تحويل الرحلة من الحالة 6 إلى 7',
    'trip_invalid_captain_cancel' => 'لا يمكنك إلغاء الرحلة بعد أن بدأت فعليًا',
    'cancel_reason_required' => 'سبب الإلغاء مطلوب',
    'trip_invalid_captain_end' => 'يجب إدخال كميات الأسماك قبل إنهاء الرحلة',
    'trip_counter_assigned' => 'هذه الرحلة تم تعيينها لعداد آخر بالفعل',
    'trip_updated' => 'تم تحديث حالة الرحلة بنجاح',

    'employee_added' => 'تم إضافة الموظف بنجاح',
    'employee_updated' => 'تم تحديث بيانات الموظف بنجاح',
    'employee_deleted' => 'تم حذف الموظف بنجاح',
    'employee_not_found' => 'الموظف غير موجود',
];
