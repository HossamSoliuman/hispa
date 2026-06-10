<?php

return [
    'statuses' => [
        '1' => 'رحلة جديدة',
        '2' => 'الرحلة جارية',
        '3' => 'ملغاة',
        '4' => 'مكتملة - بانتظار العد',
        '5' => 'جارية للعد',
        '6' => 'مكتملة العد بانتظار البيع',
        '7' => 'جاهزة للبيع',
        '8' => 'مكتملة',
    ],

    'status_keys' => [
        '1' => 'new',
        '2' => 'in_progress',
        '3' => 'cancelled',
        '4' => 'captain_done',
        '5' => 'progress_count',
        '6' => 'counter_done',
        '7' => 'ready_to_sell',
        '8' => 'completed',
    ],

    // تسميات الحالات
    'status_labels' => [
        'new' => 'رحلة جديدة',
        'in_progress' => 'جارية',
        'cancelled' => 'ملغاة',
        'captain_done' => 'اكتملت من الكابتن',
        'progress_count' => 'جارية للعد',
        'counter_done' => 'مكتملة العد',
        'ready_to_sell' => 'جاهزة للبيع',
        'completed' => 'تمت بنجاح',
    ],

    'roles' => [
        'captain' => [
            'new' => 'رحلة جديدة بانتظارك',
            'in_progress' => 'الرحلة جارية',
            'cancelled' => 'ملغاة',
            'captain_done' => 'مكتملة - بانتظار العد',
            'progress_count' => 'جارية للعد',
            'counter_done' => 'مكتملة العد بانتظار البيع',
            'ready_to_sell' => 'جاهزة للبيع',
            'completed' => 'مكتملة',
        ],
        'counter' => [
            'new' => 'رحلة جديدة (غير مخصصة لك)',
            'in_progress' => 'الرحلة جارية (غير مخصصة لك)',
            'cancelled' => 'ملغاة',
            'captain_done' => 'مكتملة عند الكابتن - بانتظار العداد',
            'progress_count' => 'جاري العد',
            'counter_done' => 'العد مكتمل',
            'ready_to_sell' => 'تم العد بنجاح - جاهزة للبيع',
            'completed' => 'تم البيع بنجاح',
        ],
        'owner' => [
            'new' => 'رحلة جديدة',
            'in_progress' => 'الرحلة جارية',
            'cancelled' => 'ملغاة',
            'captain_done' => 'مكتملة من قبل الكابتن',
            'progress_count' => 'جاهزة للعد',
            'counter_done' => 'مكتملة العد بانتظار البيع',
            'ready_to_sell' => 'جاري البيع',
            'completed' => 'تم بيع كل الكمية',
        ],
        'broker' => [
            'new' => 'رحلة جديدة',
            'in_progress' => 'الرحلة جارية',
            'cancelled' => 'ملغاة',
            'captain_done' => 'مكتملة',
            'progress_count' => 'جاهزة للعد',
            'counter_done' => 'مكتملة العد بانتظار البيع',
            'ready_to_sell' => 'جاهزة للبيع',
            'completed' => 'تم البيع بنجاح',
        ],
    ],

    'actions' => [
        'start_trip' => 'بدء الرحلة',
        'finish_trip' => 'إنهاء الرحلة',
        'start_counting' => 'بدء العد',
        'finish_counting' => 'إنهاء العد',
        'mark_ready' => 'تحديد جاهز للبيع',
        'mark_sold' => 'تحديد مباع',
        'cancel_trip' => 'إلغاء الرحلة',
    ],

    'errors' => [
        'trip_terminal' => 'لا يمكن تغيير حالة رحلة مكتملة أو ملغاة.',
        'invalid_transition' => 'الانتقال غير مسموح به من الحالة الحالية.',
        'cancel_reason_required' => 'سبب الإلغاء مطلوب.',
        'catch_required' => 'يجب إضافة بيانات الصيد قبل بدء العد.',
    ],

    'duration' => [
        'day_singular' => 'يوم',
        'day_plural' => 'أيام',
    ],

    'choose_permit_type' => 'اختر نوع التصريح',
    'permit_types' => [
        'artisanal' => 'الصيد الحرفي (صياد فردي)',
        'commercial' => 'الصيد التجاري (قارب كبير)',
        'leisure' => 'قارب نزهة (خاص)',
        'fishing' => 'قارب صيد',
        'transport' => 'نقل بحري (بضائع/أشخاص)',
        'aquaculture' => 'استزراع مائي (مزرعة أسماك/روبيان)',
        'exploration' => 'استكشاف الموارد البحرية',
        'diving' => 'غوص بحري (غير تجاري)',
    ],
];
