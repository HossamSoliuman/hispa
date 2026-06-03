<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 🐟 الأسماك
        DB::table('fish')->insert([
            ['scientific_name' => 'Sparus aurata', 'english_name' => 'Gilt-head bream', 'local_name_primary' => 'دنيس', 'status' => 1],
            ['scientific_name' => 'Dicentrarchus labrax', 'english_name' => 'Sea bass', 'local_name_primary' => 'قاروص', 'status' => 1],
            ['scientific_name' => 'Mugil cephalus', 'english_name' => 'Flathead grey mullet', 'local_name_primary' => 'بوري', 'status' => 1],
        ]);

        // 👥 العملاء
        DB::table('customers')->insert([
            ['name' => 'شركة البحر الأبيض', 'phone' => '0599123456', 'email' => 'white@sea.com'],
            ['name' => 'مطعم المرسى', 'phone' => '0599876543', 'email' => 'marssa@fish.ps'],
        ]);


        // 💳 طرق الدفع
        DB::table('payment_methods')->insert([
            ['name' => 'نقدي', 'icon' => null, 'status' => 1],
            ['name' => 'تحويل بنكي', 'icon' => null, 'status' => 1],
            ['name' => 'بطاقة ائتمان', 'icon' => null, 'status' => 1],
        ]);

        // 🚤 رحلة تجريبية
        DB::table('trips')->insert([
            'name' => 'رحلة الصيف',
            'number' => 'TRIP001',
            'license_number' => 'LIC123',
            'cancel_reason' => '',
            'status' => 1,
            'permit_type' => 'يومي',
            'owner_id' => 1,
            'captain_id' => 2,
            'boat_name' => 'أمواج غزة',
            'boat_number' => 'BO123',
            'boat_color' => 'أزرق',
            'boat_length' => 10.5,
            'boat_width' => 3.2,
            'departure_time' => '05:00',
            'return_time' => '14:00',
            'start_date' => '2025-04-09',
            'end_date' => '2025-04-12',
            'actual_start_datetime' => null,
            'actual_end_datetime' => null,
            'region_id' => 1,
            'governorate_id' => 1,
            'port_id' => 1,
            'departure_port' => 'ميناء غزة',
            'return_port' => 'ميناء غزة',
            'dalal_id' => null,
            'license_attachment' => null,
            'notes' => 'رحلة تجريبية',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
