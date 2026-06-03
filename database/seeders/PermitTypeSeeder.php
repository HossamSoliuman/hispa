<?php

namespace Database\Seeders;

use App\Models\Trip;
use Illuminate\Database\Seeder;

class PermitTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permitTypeMap = [
            'الصيد الحرفي' => 'artisanal',
            'الصيد التجاري' => 'commercial',
            'قارب نزهة' => 'leisure',
            'قارب صيد' => 'fishing',
            'نقل بحري' => 'transport',
            'استزراع مائي' => 'aquaculture',
            'استكشاف بحري' => 'exploration',
            'غوص بحري' => 'diving',
        ];

        foreach ($permitTypeMap as $oldValue => $newKey) {
            Trip::where('permit_type', $oldValue)->update(['permit_type' => $newKey]);
        }
    }
}
