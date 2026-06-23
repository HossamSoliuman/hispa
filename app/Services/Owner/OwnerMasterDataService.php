<?php

namespace App\Services\Owner;

use App\Models\BoatType;
use App\Models\Category;
use App\Models\Fish;
use App\Models\Governorate;
use App\Models\PaymentMethod;
use App\Models\Port;
use App\Models\Region;
use App\Models\Scopes\OwnerScope;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Seeds the default master/reference data (regions, governorates, ports, fish,
 * units, payment methods, boat types and expense categories) for a freshly
 * registered owner. Every row is stamped with the owner's id so each owner
 * controls an isolated copy that never affects another owner.
 */
class OwnerMasterDataService
{
    /**
     * Seed default master data for the given owner. Idempotent: if the owner
     * already owns master data the call is a no-op.
     */
    public function seedFor(User $owner): void
    {
        if ($owner->role !== 'owner') {
            return;
        }

        if ($this->alreadySeeded($owner->id)) {
            return;
        }

        DB::transaction(function () use ($owner): void {
            $this->seedLocations($owner->id);
            $this->seedFish($owner->id);
            $this->seedUnits($owner->id);
            $this->seedPaymentMethods($owner->id);
            $this->seedBoatTypes($owner->id);
            $this->seedCategories($owner->id);
        });
    }

    private function alreadySeeded(int $ownerId): bool
    {
        return Fish::withoutGlobalScope(OwnerScope::class)->where('owner_id', $ownerId)->exists()
            || Port::withoutGlobalScope(OwnerScope::class)->where('owner_id', $ownerId)->exists();
    }

    private function seedLocations(int $ownerId): void
    {
        $regions = [
            'makkah' => ['ar' => 'مكة المكرمة', 'en' => 'Makkah'],
            'riyadh' => ['ar' => 'الرياض', 'en' => 'Riyadh'],
            'eastern' => ['ar' => 'الشرقية', 'en' => 'Eastern Province'],
            'madinah' => ['ar' => 'المدينة المنورة', 'en' => 'Al Madinah Al Munawwarah'],
        ];

        $regionIds = [];
        foreach ($regions as $key => $region) {
            $regionIds[$key] = Region::create([
                'name' => $region['ar'],
                'name_en' => $region['en'],
                'status' => 1,
                'owner_id' => $ownerId,
            ])->id;
        }

        $governorates = [
            'jeddah' => ['ar' => 'جدة', 'en' => 'Jeddah', 'region' => 'makkah'],
            'taif' => ['ar' => 'الطائف', 'en' => 'Taif', 'region' => 'makkah'],
            'riyadh' => ['ar' => 'الرياض', 'en' => 'Riyadh', 'region' => 'riyadh'],
            'dammam' => ['ar' => 'الدمام', 'en' => 'Dammam', 'region' => 'eastern'],
            'khobar' => ['ar' => 'الخبر', 'en' => 'Khobar', 'region' => 'eastern'],
            'madinah' => ['ar' => 'المدينة المنورة', 'en' => 'Medina', 'region' => 'madinah'],
        ];

        $govIds = [];
        foreach ($governorates as $key => $gov) {
            $govIds[$key] = Governorate::create([
                'name' => $gov['ar'],
                'name_en' => $gov['en'],
                'status' => 1,
                'region_id' => $regionIds[$gov['region']],
                'owner_id' => $ownerId,
            ])->id;
        }

        $ports = [
            ['ar' => 'ميناء جدة الإسلامي', 'en' => 'Jeddah Islamic Port', 'cat_ar' => 'حكومي', 'cat_en' => 'Governmental', 'gov' => 'jeddah'],
            ['ar' => 'ميناء الملك عبدالعزيز', 'en' => 'King Abdulaziz Port', 'cat_ar' => 'حكومي', 'cat_en' => 'Governmental', 'gov' => 'dammam'],
            ['ar' => 'ميناء الملك فهد الصناعي (ينبع)', 'en' => 'King Fahd Industrial Port (Yanbu)', 'cat_ar' => 'خاص', 'cat_en' => 'Private', 'gov' => 'madinah'],
            ['ar' => 'ميناء الملك فهد الصناعي (الجبيل)', 'en' => 'King Fahd Industrial Port (Jubail)', 'cat_ar' => 'خاص', 'cat_en' => 'Private', 'gov' => 'khobar'],
        ];

        foreach ($ports as $port) {
            Port::create([
                'name' => $port['ar'],
                'name_en' => $port['en'],
                'category_ar' => $port['cat_ar'],
                'category_en' => $port['cat_en'],
                'governorate_id' => $govIds[$port['gov']],
                'status' => 1,
                'owner_id' => $ownerId,
            ]);
        }
    }

    private function seedFish(int $ownerId): void
    {
        $fish = [
            ['scientific_name' => 'Sparus aurata', 'english_name' => 'Gilt-head bream', 'local_name_primary' => 'دنيس'],
            ['scientific_name' => 'Dicentrarchus labrax', 'english_name' => 'Sea bass', 'local_name_primary' => 'قاروص'],
            ['scientific_name' => 'Mugil cephalus', 'english_name' => 'Flathead grey mullet', 'local_name_primary' => 'بوري'],
        ];

        foreach ($fish as $item) {
            Fish::create($item + ['status' => 1, 'owner_id' => $ownerId]);
        }
    }

    private function seedUnits(int $ownerId): void
    {
        $units = [
            ['name_ar' => 'كجم', 'name_en' => 'Kg', 'is_default' => true],
            ['name_ar' => 'شكه', 'name_en' => 'Shaka', 'is_default' => false],
            ['name_ar' => 'قلم', 'name_en' => 'Qalam', 'is_default' => false],
            ['name_ar' => 'بوكس', 'name_en' => 'Box', 'is_default' => false],
            ['name_ar' => 'صندوق', 'name_en' => 'Crate', 'is_default' => false],
        ];

        foreach ($units as $unit) {
            Unit::create($unit + ['status' => true, 'owner_id' => $ownerId]);
        }
    }

    private function seedPaymentMethods(int $ownerId): void
    {
        $methods = [
            ['name' => 'نقدي', 'name_en' => 'Cash'],
            ['name' => 'تحويل بنكي', 'name_en' => 'Bank Transfer'],
            ['name' => 'بطاقة ائتمان', 'name_en' => 'Credit Card'],
        ];

        foreach ($methods as $method) {
            PaymentMethod::create($method + ['icon' => null, 'status' => 1, 'owner_id' => $ownerId]);
        }
    }

    private function seedBoatTypes(int $ownerId): void
    {
        $types = [
            ['name_ar' => 'لنش', 'name_en' => 'Launch'],
            ['name_ar' => 'قارب', 'name_en' => 'Boat'],
        ];

        foreach ($types as $type) {
            BoatType::create($type + ['status' => 1, 'owner_id' => $ownerId]);
        }
    }

    private function seedCategories(int $ownerId): void
    {
        $groups = [
            'general' => [
                'parent' => ['name_ar' => 'مصاريف عامة', 'name_en' => 'General Expenses'],
                'children' => [
                    ['name_ar' => 'إيجار بيت', 'name_en' => 'House Rent'],
                    ['name_ar' => 'إيجار سيارة', 'name_en' => 'Car Rent'],
                    ['name_ar' => 'كهرباء', 'name_en' => 'Electricity'],
                    ['name_ar' => 'اتصالات', 'name_en' => 'Telecommunications'],
                    ['name_ar' => 'ماء', 'name_en' => 'Water'],
                    ['name_ar' => 'رواتب', 'name_en' => 'Salaries'],
                ],
            ],
            'operating' => [
                'parent' => ['name_ar' => 'مصاريف تشغيلية', 'name_en' => 'Operating Expenses'],
                'children' => [
                    ['name_ar' => 'أدوات صيد', 'name_en' => 'Fishing Tools'],
                    ['name_ar' => 'معدات صيد', 'name_en' => 'Fishing Equipment'],
                    ['name_ar' => 'ثلج', 'name_en' => 'Ice'],
                    ['name_ar' => 'وقود', 'name_en' => 'Fuel'],
                    ['name_ar' => 'زيت', 'name_en' => 'Oil'],
                    ['name_ar' => 'إعاشة (أكل)', 'name_en' => 'Provisions'],
                    ['name_ar' => 'ماء للشرب', 'name_en' => 'Drinking Water'],
                    ['name_ar' => 'غاز', 'name_en' => 'Gas'],
                ],
            ],
            'government' => [
                'parent' => ['name_ar' => 'مصاريف حكومية', 'name_en' => 'Government Expenses'],
                'children' => [
                    ['name_ar' => 'تأمين طبي', 'name_en' => 'Medical Insurance'],
                    ['name_ar' => 'رسوم تجديد', 'name_en' => 'Renewal Fees'],
                    ['name_ar' => 'مكتب العمل', 'name_en' => 'Labor Office Charges'],
                    ['name_ar' => 'استقدام', 'name_en' => 'Recruitment Costs'],
                    ['name_ar' => 'مصاريف أخرى', 'name_en' => 'Miscellaneous Expenses'],
                    ['name_ar' => 'غرامات', 'name_en' => 'Penalties & Fines'],
                ],
            ],
            'maintenance' => [
                'parent' => ['name_ar' => 'مصاريف صيانة', 'name_en' => 'Maintenance Expenses'],
                'children' => [
                    ['name_ar' => 'قطع غيار', 'name_en' => 'Spare Parts'],
                    ['name_ar' => 'أجور إصلاح', 'name_en' => 'Repair Costs'],
                    ['name_ar' => 'صيانة دورية', 'name_en' => 'Routine Maintenance'],
                    ['name_ar' => 'صيانة طارئة', 'name_en' => 'Emergency Maintenance'],
                    ['name_ar' => 'صيانة سنوية', 'name_en' => 'Annual Maintenance'],
                ],
            ],
        ];

        foreach ($groups as $type => $group) {
            $parent = Category::create($group['parent'] + [
                'type' => $type,
                'status' => 1,
                'parent_id' => null,
                'owner_id' => $ownerId,
            ]);

            foreach ($group['children'] as $child) {
                Category::create($child + [
                    'type' => $type,
                    'status' => 1,
                    'parent_id' => $parent->id,
                    'owner_id' => $ownerId,
                ]);
            }
        }
    }
}
