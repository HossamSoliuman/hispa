<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DefaultMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('trips')->truncate();
        DB::table('customers')->truncate();
        DB::table('payment_methods')->truncate();
        DB::table('fish')->truncate();
        Schema::enableForeignKeyConstraints();

        // 🐟 الأسماك
        DB::table('fish')->insert([
            ['scientific_name' => 'Sparus aurata', 'english_name' => 'Gilt-head bream', 'local_name_primary' => 'دنيس', 'status' => 1],
            ['scientific_name' => 'Dicentrarchus labrax', 'english_name' => 'Sea bass', 'local_name_primary' => 'قاروص', 'status' => 1],
            ['scientific_name' => 'Mugil cephalus', 'english_name' => 'Flathead grey mullet', 'local_name_primary' => 'بوري', 'status' => 1],
        ]);

        // 👥 العملاء
        // DB::table('customers')->insert([
        //     ['name' => 'شركة البحر الأبيض', 'phone' => '0599123456', 'email' => 'white@sea.com'],
        //     ['name' => 'مطعم المرسى', 'phone' => '0599876543', 'email' => 'marssa@fish.ps'],
        // ]);

        // 💳 طرق الدفع
        DB::table('payment_methods')->insert([
            ['name' => 'نقدي', 'icon' => null, 'status' => 1],
            ['name' => 'تحويل بنكي', 'icon' => null, 'status' => 1],
            ['name' => 'بطاقة ائتمان', 'icon' => null, 'status' => 1],
        ]);

        // No demo trip inserted here — BasicWorkflowSeeder creates the boat and
        // people so trips can be created properly through the owner workflow.
    }
}
