<?php

namespace Database\Seeders;

use App\Models\Governorate;
use App\Models\Port;
use App\Models\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('regions')->truncate();
        DB::table('governorates')->truncate();
        DB::table('ports')->truncate();
        Schema::enableForeignKeyConstraints();

        // Regions (AR/EN)
        $regions = [
            ['ar' => 'مكة المكرمة', 'en' => 'Makkah'],
            ['ar' => 'الرياض', 'en' => 'Riyadh'],
            ['ar' => 'الشرقية', 'en' => 'Eastern Province'],
            ['ar' => 'المدينة المنورة', 'en' => 'Al Madinah Al Munawwarah'],
        ];

        $map = [];

        foreach ($regions as $r) {
            $map[$r['ar']] = Region::create(['name' => $r['ar'], 'name_en' => $r['en'], 'status' => 1]);
        }

        // المحافظات
        $governorates = [
            ['ar' => 'جدة', 'en' => 'Jeddah', 'region' => 'مكة المكرمة'],
            ['ar' => 'الطائف', 'en' => 'Taif', 'region' => 'مكة المكرمة'],
            ['ar' => 'الرياض', 'en' => 'Riyadh', 'region' => 'الرياض'],
            ['ar' => 'الدمام', 'en' => 'Dammam', 'region' => 'الشرقية'],
            ['ar' => 'الخبر', 'en' => 'Khobar', 'region' => 'الشرقية'],
            ['ar' => 'المدينة المنورة', 'en' => 'Medina', 'region' => 'المدينة المنورة'],
        ];

        $govMap = [];
        foreach ($governorates as $gov) {
            $govMap[$gov['ar']] = Governorate::create([
                'name' => $gov['ar'],
                'name_en' => $gov['en'],
                'status' => 1,
                'region_id' => $map[$gov['region']]->id,
            ]);
        }

        // الموانئ
        $ports = [
            ['ar' => 'ميناء جدة الإسلامي', 'en' => 'Jeddah Islamic Port', 'category_ar' => 'حكومي', 'category_en' => 'Governmental', 'city' => 'جدة'],
            ['ar' => 'ميناء الملك عبدالعزيز', 'en' => 'King Abdulaziz Port', 'category_ar' => 'حكومي', 'category_en' => 'Governmental', 'city' => 'الدمام'],
            ['ar' => 'ميناء الملك فهد الصناعي (ينبع)', 'en' => 'King Fahd Industrial Port (Yanbu)', 'category_ar' => 'خاص', 'category_en' => 'Private', 'city' => 'المدينة المنورة'],
            ['ar' => 'ميناء الملك فهد الصناعي (الجبيل)', 'en' => 'King Fahd Industrial Port (Jubail)', 'category_ar' => 'خاص', 'category_en' => 'Private', 'city' => 'الخبر'],
        ];

        foreach ($ports as $p) {
            Port::create([
                'name' => $p['ar'],
                'name_en' => $p['en'],
                'category_ar' => $p['category_ar'],
                'category_en' => $p['category_en'],
                'status' => 1,
            ]);
        }
    }
}
