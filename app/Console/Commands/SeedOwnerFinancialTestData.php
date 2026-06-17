<?php

namespace App\Console\Commands;

use App\Models\Boat;
use App\Models\BoatType;
use App\Models\CatchDetail;
use App\Models\CatchModel;
use App\Models\CrewAdvance;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Fish;
use App\Models\FishQuantityStock;
use App\Models\Governorate;
use App\Models\Maintenance;
use App\Models\MonthClosing;
use App\Models\MonthClosingDue;
use App\Models\PaymentMethod;
use App\Models\Payroll;
use App\Models\PayrollDetail;
use App\Models\PayrollDetailsModel;
use App\Models\PayrollModel;
use App\Models\Port;
use App\Models\Region;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Trip;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SeedOwnerFinancialTestData extends Command
{
    /**
     * @var string
     */
    protected $signature = 'owner:seed-financial-test {email=owner@example.com}';

    /**
     * @var string
     */
    protected $description = 'Reset an owner: wipe all his financial/transactional data, then seed a fresh boat, captain and crew so a trip can be created immediately (catch & sales start empty).';

    public function handle(): int
    {
        $email = (string) $this->argument('email');

        $owner = User::where('email', $email)->where('role', 'owner')->first();

        if (! $owner) {
            $this->error("No owner found with email [{$email}].");

            return self::FAILURE;
        }

        $this->info("👤 Resetting financial test data for: {$owner->name} <{$owner->email}>");

        DB::transaction(function () use ($owner): void {
            $this->wipeOwnerData($owner);

            $region = Region::first() ?? Region::create(['name' => 'المنطقة الافتراضية', 'name_en' => 'Default Region', 'status' => 1]);
            $governorate = Governorate::first() ?? Governorate::create(['name' => 'المحافظة الافتراضية', 'name_en' => 'Default Governorate', 'region_id' => $region->id, 'status' => 1]);
            $port = Port::first() ?? Port::create(['name' => 'الميناء الافتراضي', 'name_en' => 'Default Port', 'governorate_id' => $governorate->id, 'status' => 1]);

            $this->ensureReferenceData();

            $boat = $this->seedBoat($owner, $region, $governorate, $port);
            $captain = $this->seedCaptain($owner, $region, $governorate, $port, $boat);
            $this->seedCrew($owner, $captain, $boat);
            $this->seedSupportingPeople($owner, $region, $governorate, $port);
            $this->seedCustomers($owner, $region);
        });

        $this->newLine();
        $this->info('✅ Done. The owner now has one boat, one captain and crew ready.');
        $this->info('   Trips, catch, fish stock and sales are empty — create a trip to start testing the financials.');

        return self::SUCCESS;
    }

    /**
     * Remove every transactional / financial record and the existing sub-users
     * & boats tied to this owner so the dataset is fully rebuilt from scratch.
     */
    private function wipeOwnerData(User $owner): void
    {
        $this->info('🧹 Wiping existing data...');

        $tripIds = Trip::withTrashed()->where('owner_id', $owner->id)->pluck('id');
        $catchIds = CatchModel::where('owner_id', $owner->id)->pluck('id');
        $saleIds = Sale::withTrashed()
            ->where(fn ($q) => $q->whereIn('trip_id', $tripIds)->orWhere('seller_id', $owner->id))
            ->pluck('id');

        SaleDetail::whereIn('sale_id', $saleIds)->delete();
        Sale::withTrashed()->whereIn('id', $saleIds)->forceDelete();

        FishQuantityStock::whereIn('trip_id', $tripIds)->orWhereIn('catch_id', $catchIds)->delete();
        CatchDetail::whereIn('catch_id', $catchIds)->delete();
        CatchModel::whereIn('id', $catchIds)->delete();
        Trip::withTrashed()->whereIn('id', $tripIds)->forceDelete();

        $payrollIds = Payroll::withTrashed()->where('owner_id', $owner->id)->pluck('id');
        PayrollDetail::whereIn('payroll_id', $payrollIds)->delete();
        Payroll::withTrashed()->whereIn('id', $payrollIds)->forceDelete();

        $payrollModelIds = PayrollModel::where('owner_id', $owner->id)->pluck('id');
        PayrollDetailsModel::whereIn('payroll_id', $payrollModelIds)->delete();
        PayrollModel::whereIn('id', $payrollModelIds)->delete();

        $closingIds = MonthClosing::where('owner_id', $owner->id)->pluck('id');
        MonthClosingDue::whereIn('month_closing_id', $closingIds)->delete();
        MonthClosing::whereIn('id', $closingIds)->delete();

        Expense::withTrashed()->where('owner_id', $owner->id)->forceDelete();
        Maintenance::withTrashed()->where('owner_id', $owner->id)->forceDelete();
        CrewAdvance::where('owner_id', $owner->id)->delete();
        Customer::where('owner_id', $owner->id)->delete();

        User::where('owner_id', $owner->id)->delete();
        Boat::where('owner_id', $owner->id)->delete();
    }

    /**
     * Make sure the global reference data a catch/sale needs exists.
     */
    private function ensureReferenceData(): void
    {
        if (Fish::count() === 0) {
            Fish::insert([
                ['scientific_name' => 'Sparus aurata', 'english_name' => 'Gilt-head bream', 'local_name_primary' => 'دنيس', 'status' => 1],
                ['scientific_name' => 'Dicentrarchus labrax', 'english_name' => 'Sea bass', 'local_name_primary' => 'قاروص', 'status' => 1],
                ['scientific_name' => 'Mugil cephalus', 'english_name' => 'Flathead grey mullet', 'local_name_primary' => 'بوري', 'status' => 1],
            ]);
        }

        if (Unit::count() === 0) {
            Unit::insert([
                ['name_ar' => 'كجم', 'name_en' => 'Kg', 'is_default' => true, 'status' => true],
                ['name_ar' => 'شكه', 'name_en' => 'Shaka', 'is_default' => false, 'status' => true],
                ['name_ar' => 'بوكس', 'name_en' => 'Box', 'is_default' => false, 'status' => true],
            ]);
        }

        if (PaymentMethod::count() === 0) {
            PaymentMethod::insert([
                ['name' => 'نقدي', 'status' => 1],
                ['name' => 'تحويل بنكي', 'status' => 1],
            ]);
        }
    }

    private function seedBoat(User $owner, Region $region, Governorate $governorate, Port $port): Boat
    {
        $boatType = BoatType::firstOrCreate(['name_ar' => 'لنش'], ['name_en' => 'Launch', 'status' => 1]);

        return Boat::create([
            'owner_id' => $owner->id,
            'name_ar' => 'أمواج البحر',
            'name_en' => 'Sea Waves',
            'number' => 'B-SA-0001',
            'status' => 1,
            'length' => '12.5',
            'width' => '3.8',
            'color' => 'أزرق',
            'type' => 'لنش',
            'boat_type_id' => $boatType->id,
            'license_number' => 'LIC-SA-0001',
            'license_region_id' => $region->id,
            'license_date' => '2022-01-15',
            'license_date_expire' => '2027-01-14',
            'body_number' => 'HLL-001',
            'engine_status' => 1,
            'engine_type' => 'ديزل',
            'engine_power' => '250 حصان',
            'crew_number' => 5,
            'payload' => 1200,
            'region_id' => $region->id,
            'governorate_id' => $governorate->id,
            'port_id' => $port->id,
        ]);
    }

    private function seedCaptain(User $owner, Region $region, Governorate $governorate, Port $port, Boat $boat): User
    {
        return User::create([
            'name' => 'أحمد الصياد',
            'email' => 'captain1@example.com',
            'phone' => '0500000011',
            'password' => Hash::make('password'),
            'role' => 'captain',
            'status' => 1,
            'owner_id' => $owner->id,
            'boat_id' => $boat->id,
            'id_number' => 'CPT000001',
            'nationality' => 'سعودي',
            'crew_count' => 5,
            'salary_type' => 'percentage',
            'profit_shares' => 2,
            'fishing_license_number' => 'FL-2024-001',
            'fishing_license_expiry' => '2026-12-31',
            'region_id' => $region->id,
            'governorate_id' => $governorate->id,
            'port_id' => $port->id,
        ]);
    }

    private function seedCrew(User $owner, User $captain, Boat $boat): void
    {
        $names = ['سعيد البحار', 'خالد النوخذة', 'يوسف الصياد', 'ماجد الغواص', 'فهد البحري'];

        foreach ($names as $index => $name) {
            $sequence = str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT);

            User::create([
                'name' => $name,
                'email' => "crew{$sequence}@example.com",
                'phone' => '05010000'.$sequence,
                'password' => Hash::make('password'),
                'role' => 'crew',
                'status' => 1,
                'owner_id' => $owner->id,
                'captain_id' => $captain->id,
                'boat_id' => $boat->id,
                'id_number' => 'CRW'.$sequence,
                'nationality' => 'سعودي',
                'salary_type' => 'percentage',
                'profit_shares' => 1,
            ]);
        }
    }

    private function seedSupportingPeople(User $owner, Region $region, Governorate $governorate, Port $port): void
    {
        $people = [
            [
                'name' => 'محمد الدلال',
                'email' => 'dalal@example.com',
                'phone' => '0500000013',
                'role' => 'dalal',
                'id_number' => 'DLL000001',
                'tax_number' => '3001234567',
            ],
            [
                'name' => 'علي العداد',
                'email' => 'counter@example.com',
                'phone' => '0500000014',
                'role' => 'counter',
                'id_number' => 'CNT000001',
            ],
            [
                'name' => 'سالم المورد',
                'email' => 'vendor1@example.com',
                'phone' => '0500000021',
                'role' => 'vendor',
                'company_name' => 'شركة الإمداد البحري',
                'tax_number' => '3009876543',
            ],
        ];

        foreach ($people as $person) {
            User::create(array_merge($person, [
                'password' => Hash::make('password'),
                'status' => 1,
                'owner_id' => $owner->id,
                'region_id' => $region->id,
                'governorate_id' => $governorate->id,
                'port_id' => $port->id,
            ]));
        }
    }

    private function seedCustomers(User $owner, Region $region): void
    {
        $customers = [
            ['name' => 'مطعم المرسى', 'phone' => '0599876543', 'slug' => 'marssa'],
            ['name' => 'سوق السمك المركزي', 'phone' => '0599234567', 'slug' => 'central-market'],
            ['name' => 'شركة البحر الأبيض', 'phone' => '0599123456', 'slug' => 'white-sea'],
        ];

        foreach ($customers as $customer) {
            Customer::create([
                'name' => $customer['name'],
                'phone' => $customer['phone'],
                'email' => "{$customer['slug']}.owner{$owner->id}@example.com",
                'status' => 1,
                'owner_id' => $owner->id,
                'region_id' => $region->id,
            ]);
        }
    }
}
