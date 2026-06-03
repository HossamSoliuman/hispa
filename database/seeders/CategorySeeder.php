<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CategorySeeder extends Seeder
{
    public function run(): void
    {

        Schema::disableForeignKeyConstraints();
        DB::table('categories')->truncate();
        Schema::enableForeignKeyConstraints();

        // --------- General Expenses ---------
        $general = Category::create([
            'name_ar' => 'مصاريف عامة',
            'name_en' => 'General Expenses',
            'type' => 'general',
            'status' => 1,
            'parent_id' => null,
        ]);

        Category::insert([
            ['name_ar' => 'إيجار بيت', 'name_en' => 'House Rent', 'type' => 'general', 'status' => 1, 'parent_id' => $general->id],
            ['name_ar' => 'إيجار سيارة', 'name_en' => 'Car Rent', 'type' => 'general', 'status' => 1, 'parent_id' => $general->id],
            ['name_ar' => 'كهرباء', 'name_en' => 'Electricity', 'type' => 'general', 'status' => 1, 'parent_id' => $general->id],
            ['name_ar' => 'اتصالات', 'name_en' => 'Telecommunications', 'type' => 'general', 'status' => 1, 'parent_id' => $general->id],
            ['name_ar' => 'ماء', 'name_en' => 'Water', 'type' => 'general', 'status' => 1, 'parent_id' => $general->id],
            ['name_ar' => 'رواتب', 'name_en' => 'Salaries', 'type' => 'general', 'status' => 1, 'parent_id' => $general->id],
        ]);

        // --------- Operating Expenses ---------
        $operating = Category::create([
            'name_ar' => 'مصاريف تشغيلية',
            'name_en' => 'Operating Expenses',
            'type' => 'operating',
            'status' => 1,
            'parent_id' => null,
        ]);

        Category::insert([
            ['name_ar' => 'أدوات صيد', 'name_en' => 'Fishing Tools', 'type' => 'operating', 'status' => 1, 'parent_id' => $operating->id],
            ['name_ar' => 'معدات صيد', 'name_en' => 'Fishing Equipment', 'type' => 'operating', 'status' => 1, 'parent_id' => $operating->id],
            ['name_ar' => 'ثلج', 'name_en' => 'Ice', 'type' => 'operating', 'status' => 1, 'parent_id' => $operating->id],
            ['name_ar' => 'وقود', 'name_en' => 'Fuel', 'type' => 'operating', 'status' => 1, 'parent_id' => $operating->id],
            ['name_ar' => 'زيت', 'name_en' => 'Oil', 'type' => 'operating', 'status' => 1, 'parent_id' => $operating->id],
            ['name_ar' => 'إعاشة (أكل)', 'name_en' => 'Provisions', 'type' => 'operating', 'status' => 1, 'parent_id' => $operating->id],
            ['name_ar' => 'ماء للشرب', 'name_en' => 'Drinking Water', 'type' => 'operating', 'status' => 1, 'parent_id' => $operating->id],
            ['name_ar' => 'غاز', 'name_en' => 'Gas', 'type' => 'operating', 'status' => 1, 'parent_id' => $operating->id],
        ]);

        // --------- Government Expenses ---------
        $government = Category::create([
            'name_ar' => 'مصاريف حكومية',
            'name_en' => 'Government Expenses',
            'type' => 'government',
            'status' => 1,
            'parent_id' => null,
        ]);

        Category::insert([
            ['name_ar' => 'تأمين طبي', 'name_en' => 'Medical Insurance', 'type' => 'government', 'status' => 1, 'parent_id' => $government->id],
            ['name_ar' => 'رسوم تجديد', 'name_en' => 'Renewal Fees', 'type' => 'government', 'status' => 1, 'parent_id' => $government->id],
            ['name_ar' => 'مكتب العمل', 'name_en' => 'Labor Office Charges', 'type' => 'government', 'status' => 1, 'parent_id' => $government->id],
            ['name_ar' => 'استقدام', 'name_en' => 'Recruitment Costs', 'type' => 'government', 'status' => 1, 'parent_id' => $government->id],
            ['name_ar' => 'مصاريف أخرى', 'name_en' => 'Miscellaneous Expenses', 'type' => 'government', 'status' => 1, 'parent_id' => $government->id],
            ['name_ar' => 'غرامات', 'name_en' => 'Penalties & Fines', 'type' => 'government', 'status' => 1, 'parent_id' => $government->id],
        ]);

        // --------- Maintenance Expenses ---------
        $maintenance = Category::create([
            'name_ar' => 'مصاريف صيانة',
            'name_en' => 'Maintenance Expenses',
            'type' => 'maintenance',
            'status' => 1,
            'parent_id' => null,
        ]);

        Category::insert([
            ['name_ar' => 'قطع غيار', 'name_en' => 'Spare Parts', 'type' => 'maintenance', 'status' => 1, 'parent_id' => $maintenance->id],
            ['name_ar' => 'أجور إصلاح', 'name_en' => 'Repair Costs', 'type' => 'maintenance', 'status' => 1, 'parent_id' => $maintenance->id],
            ['name_ar' => 'صيانة دورية', 'name_en' => 'Routine Maintenance', 'type' => 'maintenance', 'status' => 1, 'parent_id' => $maintenance->id],
            ['name_ar' => 'صيانة طارئة', 'name_en' => 'Emergency Maintenance', 'type' => 'maintenance', 'status' => 1, 'parent_id' => $maintenance->id],
            ['name_ar' => 'صيانة سنوية', 'name_en' => 'Annual Maintenance', 'type' => 'maintenance', 'status' => 1, 'parent_id' => $maintenance->id],
        ]);
    }
}
