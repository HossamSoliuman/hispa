<?php

namespace App\Service\Owner\Startup;

use App\Models\Startup\ExpenseCategory;

class StartupDefaultsService
{
    public function seed(): void
    {
        $defaults = [['شراء أصل', 'Asset purchase'], ['عمالة', 'Labor'], ['رسوم حكومية', 'Government fees'], ['تجهيزات', 'Equipment'], ['إيجارات', 'Rent'], ['تسويق', 'Marketing'], ['تشغيل أولي', 'Initial operations'], ['مصاريف إدارية', 'Administrative'], ['أخرى', 'Other']];
        foreach ($defaults as [$ar, $en]) {
            ExpenseCategory::firstOrCreate(['name_ar' => $ar], ['name_en' => $en, 'is_active' => true]);
        }
    }
}
