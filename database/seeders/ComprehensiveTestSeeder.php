<?php

namespace Database\Seeders;

use App\Models\Boat;
use App\Models\BoatType;
use App\Models\Category;
use App\Models\CommissionSetting;
use App\Models\Customer;
use App\Models\DalalStock;
use App\Models\DalalStockDetail;
use App\Models\Expense;
use App\Models\Fish;
use App\Models\FishingEquipment;
use App\Models\FishStock;
use App\Models\Governorate;
use App\Models\Maintenance;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Payroll;
use App\Models\Port;
use App\Models\Region;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * ComprehensiveTestSeeder
 *
 * Creates realistic test data for the entire fishing management system.
 * Run with: php artisan db:seed --class=ComprehensiveTestSeeder
 *
 * Data structure:
 * - Users (owners, captains, dalals, counters, suppliers)
 * - Boats with types and equipment
 * - Trips spanning last 3 months
 * - Fish stocks from trips
 * - Dalal stocks distribution
 * - Sales transactions
 * - Customers and payments
 * - Expenses and maintenance
 * - Payroll records
 */
class ComprehensiveTestSeeder extends Seeder
{
    // Configuration
    private $startDate;

    private $endDate;

    // Cached data
    private $regions;

    private $governorates;

    private $cities;

    private $ports;

    private $fish;

    private $paymentMethods;

    private $commissionSettings;

    private $categories;

    // Users collections
    private $owners;

    private $captains;

    private $dalals;

    private $counters;

    // Boats and trips
    private $boats;

    private $trips;

    public function __construct()
    {
        // Data will cover last 3 months
        $this->endDate = Carbon::now();
        $this->startDate = Carbon::now()->subMonths(3);
    }

    public function run(): void
    {
        $this->command->info('🚀 Starting Comprehensive Test Data Seeding...');

        DB::beginTransaction();

        try {
            // Step 1: Cache reference data
            $this->command->info('📍 Loading reference data (locations, fish, payment methods)...');
            $this->loadReferenceData();

            // Step 2: Create additional fish species if needed
            $this->command->info('🐟 Creating fish species...');
            $this->seedFishSpecies();

            // Step 3: Create boat types
            $this->command->info('🚤 Creating boat types...');
            $this->seedBoatTypes();

            // Step 4: Create categories for expenses
            $this->command->info('📁 Creating expense categories...');
            $this->seedCategories();

            // Step 5: Create users (owners, captains, dalals, counters)
            $this->command->info('👥 Creating users (owners, captains, dalals, counters)...');
            $this->seedUsers();

            // Step 6: Create customers
            $this->command->info('🛒 Creating customers...');
            $this->seedCustomers();

            // Step 7: Create boats
            $this->command->info('⛵ Creating boats...');
            $this->seedBoats();

            // Step 8: Create fishing equipment
            $this->command->info('🎣 Creating fishing equipment...');
            $this->seedFishingEquipment();

            // Step 9: Create trips
            $this->command->info('🌊 Creating trips...');
            $this->seedTrips();

            // Step 10: Create fish stocks from trips
            $this->command->info('📦 Creating fish stocks...');
            $this->seedFishStocks();

            // Step 11: Create dalal stocks
            $this->command->info('🤝 Creating dalal stocks...');
            $this->seedDalalStocks();

            // Step 12: Create sales
            $this->command->info('💰 Creating sales transactions...');
            $this->seedSales();

            // Step 13: Create payments
            $this->command->info('💳 Creating payments...');
            $this->seedPayments();

            // Step 14: Create expenses
            $this->command->info('💸 Creating expenses...');
            $this->seedExpenses();

            // Step 15: Create maintenance records
            $this->command->info('🔧 Creating maintenance records...');
            $this->seedMaintenance();

            // Step 16: Create payroll records
            $this->command->info('💼 Creating payroll records...');
            $this->seedPayrolls();

            DB::commit();

            $this->command->info('');
            $this->command->info('✅ Comprehensive test data seeded successfully!');
            $this->command->info('');
            $this->printSummary();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Seeding failed: ' . $e->getMessage());
            $this->command->error($e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Load existing reference data (locations, fish, payment methods)
     */
    private function loadReferenceData(): void
    {
        $this->regions = Region::all();
        $this->governorates = Governorate::all();
        $this->ports = Port::where('status', 1)->get();
        $this->fish = Fish::where('status', 1)->get();
        $this->paymentMethods = PaymentMethod::where('status', 1)->get();
        $this->commissionSettings = CommissionSetting::where('status', 1)->get();

        // If no locations exist, create default ones
        if ($this->regions->isEmpty()) {
            $region = Region::create(['name' => 'غزة', 'name_en' => 'Gaza', 'status' => 1]);
            $this->regions = collect([$region]);
        }

        if ($this->governorates->isEmpty()) {
            $gov = Governorate::create([
                'name' => 'محافظة غزة',
                'name_en' => 'Gaza Governorate',
                'region_id' => $this->regions->first()->id,
                'status' => 1,
            ]);
            $this->governorates = collect([$gov]);
        }


        if ($this->ports->isEmpty()) {
            $port = Port::create([
                'name' => 'ميناء غزة',
                'name_en' => 'Gaza Port',
                'governorate_id' => $this->governorates->first()->id,
                'status' => 1,
            ]);
            $this->ports = collect([$port]);
        }
    }

    /**
     * Seed fish species
     */
    private function seedFishSpecies(): void
    {
        $fishSpecies = [
            ['scientific_name' => 'Sparus aurata', 'english_name' => 'Gilt-head bream', 'local_name_primary' => 'دنيس'],
            ['scientific_name' => 'Dicentrarchus labrax', 'english_name' => 'Sea bass', 'local_name_primary' => 'قاروص'],
            ['scientific_name' => 'Mugil cephalus', 'english_name' => 'Flathead grey mullet', 'local_name_primary' => 'بوري'],
            ['scientific_name' => 'Sardinella aurita', 'english_name' => 'Round sardinella', 'local_name_primary' => 'سردين'],
            ['scientific_name' => 'Liza ramada', 'english_name' => 'Thinlip grey mullet', 'local_name_primary' => 'بوري رمادي'],
            ['scientific_name' => 'Scomber scombrus', 'english_name' => 'Atlantic mackerel', 'local_name_primary' => 'ماكريل'],
            ['scientific_name' => 'Solea solea', 'english_name' => 'Common sole', 'local_name_primary' => 'موسى'],
            ['scientific_name' => 'Merluccius merluccius', 'english_name' => 'European hake', 'local_name_primary' => 'مرلوسة'],
        ];

        foreach ($fishSpecies as $fish) {
            Fish::firstOrCreate(
                ['scientific_name' => $fish['scientific_name']],
                array_merge($fish, ['status' => 1])
            );
        }

        $this->fish = Fish::where('status', 1)->get();
    }

    /**
     * Seed boat types
     */
    private function seedBoatTypes(): void
    {
        $boatTypes = [
            ['name_ar' => 'حسكة', 'name_en' => 'Hasaka'],
            ['name_ar' => 'لنش', 'name_en' => 'Launch'],
            ['name_ar' => 'فلوكة', 'name_en' => 'Felucca'],
            ['name_ar' => 'صنارة', 'name_en' => 'Fishing rod boat'],
            ['name_ar' => 'شباك', 'name_en' => 'Net boat'],
        ];

        foreach ($boatTypes as $type) {
            BoatType::firstOrCreate(
                ['name_ar' => $type['name_ar']],
                array_merge($type, ['status' => 1])
            );
        }
    }

    /**
     * Seed expense categories
     */
    private function seedCategories(): void
    {
        $categories = [
            // General
            ['name_ar' => 'مصاريف عامة', 'name_en' => 'General Expenses', 'type' => 'general', 'parent_id' => null],
            // Operating
            ['name_ar' => 'مصاريف تشغيلية', 'name_en' => 'Operating Expenses', 'type' => 'operating', 'parent_id' => null],
            // Maintenance
            ['name_ar' => 'صيانة', 'name_en' => 'Maintenance', 'type' => 'maintenance', 'parent_id' => null],
            // Government
            ['name_ar' => 'رسوم حكومية', 'name_en' => 'Government Fees', 'type' => 'government', 'parent_id' => null],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['name_ar' => $cat['name_ar'], 'type' => $cat['type']],
                array_merge($cat, ['status' => 1])
            );
        }

        $this->categories = Category::whereNull('parent_id')->get();
    }

    /**
     * Seed users (owners, captains, dalals, counters)
     */
    private function seedUsers(): void
    {
        $region = $this->regions->first();
        $governorate = $this->governorates->first();
        $port = $this->ports->first();

        // Create Owners (5 owners)
        $this->owners = collect();
        for ($i = 1; $i <= 5; $i++) {
            $owner = User::firstOrCreate(
                ['phone' => "059900000$i"],
                [
                    'name' => "صيّاد رقم $i",
                    'email' => "owner$i@hawat.test",
                    'password' => Hash::make('password'),
                    'role' => 'owner',
                    'status' => 1,
                    'region_id' => $region->id,
                    'governorate_id' => $governorate->id,
                    'port_id' => $port->id,
                    'tax_number' => '100' . str_pad($i, 6, '0', STR_PAD_LEFT),
                ]
            );

            // Assign role if not already assigned
            if (! $owner->hasRole('owner')) {
                $owner->assignRole('owner');
            }

            $this->owners->push($owner);
        }

        // Create Captains (15 captains - distributed among owners)
        $this->captains = collect();
        for ($i = 1; $i <= 15; $i++) {
            $owner = $this->owners->random();
            $captain = User::firstOrCreate(
                ['phone' => "059910000$i"],
                [
                    'name' => "قبطان رقم $i",
                    'email' => "captain$i@hawat.test",
                    'password' => Hash::make('password'),
                    'role' => 'captain',
                    'status' => 1,
                    'owner_id' => $owner->id,
                    'id_number' => 'CPT' . str_pad($i, 6, '0', STR_PAD_LEFT),
                    'nationality' => 'فلسطيني',
                    'crew_count' => rand(3, 8),
                    'region_id' => $region->id,
                    'governorate_id' => $governorate->id,
                    'port_id' => $port->id,
                ]
            );

            // Assign role if not already assigned
            if (! $captain->hasRole('captain')) {
                $captain->assignRole('captain');
            }

            $this->captains->push($captain);
        }

        // Create Dalals (5 dalals)
        $this->dalals = collect();
        for ($i = 1; $i <= 5; $i++) {
            $dalal = User::firstOrCreate(
                ['phone' => "059920000$i"],
                [
                    'name' => "دلال رقم $i",
                    'email' => "dalal$i@hawat.test",
                    'password' => Hash::make('password'),
                    'role' => 'dalal',
                    'status' => 1,
                    'region_id' => $region->id,
                    'governorate_id' => $governorate->id,
                    'port_id' => $port->id,
                    'tax_number' => '200' . str_pad($i, 6, '0', STR_PAD_LEFT),
                ]
            );

            // Assign role if not already assigned
            if (! $dalal->hasRole('dalal')) {
                $dalal->assignRole('dalal');
            }

            $this->dalals->push($dalal);
        }

        // Create Counters (3 counters)
        $this->counters = collect();
        for ($i = 1; $i <= 3; $i++) {
            $counter = User::firstOrCreate(
                ['phone' => "059930000$i"],
                [
                    'name' => "عداد رقم $i",
                    'email' => "counter$i@hawat.test",
                    'password' => Hash::make('password'),
                    'role' => 'counter',
                    'status' => 1,
                ]
            );

            // Assign role if not already assigned
            if (! $counter->hasRole('counter')) {
                $counter->assignRole('counter');
            }

            $this->counters->push($counter);
        }
    }

    /**
     * Seed customers
     */
    private function seedCustomers(): void
    {
        $customers = [
            ['name' => 'شركة البحر الأبيض', 'phone' => '0599123456', 'email' => 'white@sea.com'],
            ['name' => 'مطعم المرسى', 'phone' => '0599876543', 'email' => 'marssa@fish.ps'],
            ['name' => 'سوق السمك المركزي', 'phone' => '0599234567', 'email' => 'central@market.ps'],
            ['name' => 'مطعم الصياد', 'phone' => '0599345678', 'email' => 'sayyad@restaurant.ps'],
            ['name' => 'شركة التصدير البحري', 'phone' => '0599456789', 'email' => 'export@marine.com'],
            ['name' => 'فندق البحر', 'phone' => '0599567890', 'email' => 'sea@hotel.ps'],
            ['name' => 'مطعم الموج', 'phone' => '0599678901', 'email' => 'mawj@restaurant.ps'],
            ['name' => 'سوبر ماركت الشاطئ', 'phone' => '0599789012', 'email' => 'beach@super.ps'],
            ['name' => 'محمد علي - تاجر جملة', 'phone' => '0599890123', 'email' => 'mali@gmail.com'],
            ['name' => 'أحمد حسن - تاجر تجزئة', 'phone' => '0599901234', 'email' => 'ahmad@gmail.com'],
        ];

        foreach ($customers as $customer) {
            Customer::firstOrCreate(
                ['phone' => $customer['phone']],
                $customer
            );
        }
    }

    /**
     * Seed boats
     */
    private function seedBoats(): void
    {
        $this->boats = collect();
        $boatTypes = BoatType::all();
        $boatNames = [
            'أمواج غزة',
            'نسيم البحر',
            'الصياد الماهر',
            'بحر العرب',
            'المجد',
            'الفجر الجديد',
            'النصر',
            'الأمل',
            'السلام',
            'العودة',
            'الحرية',
            'فلسطين',
            'الكرامة',
            'الصمود',
            'البطل',
        ];

        foreach ($this->owners as $index => $owner) {
            // Each owner has 2-4 boats
            $boatCount = rand(2, 4);
            for ($i = 0; $i < $boatCount; $i++) {
                $boatType = $boatTypes->random();
                $boatName = $boatNames[($index * 3 + $i) % count($boatNames)];
                $boatNumber = 'B-' . str_pad(($index * 10 + $i + 1), 4, '0', STR_PAD_LEFT);

                $boat = Boat::create([
                    'owner_id' => $owner->id,
                    'name_ar' => $boatName,
                    'name_en' => $boatName,
                    'number' => $boatNumber,
                    'status' => 1,
                    'length' => rand(8, 15) . '.' . rand(0, 9),
                    'width' => rand(2, 5) . '.' . rand(0, 9),
                    'color' => ['أزرق', 'أبيض', 'أخضر', 'أحمر'][rand(0, 3)],
                    'type' => $boatType->name_ar,
                    'license_number' => 'LIC-' . $boatNumber,
                    'license_region_id' => $this->regions->first()->id,
                    'license_date' => Carbon::now()->subYears(rand(1, 5))->format('Y-m-d'),
                    'license_date_expire' => Carbon::now()->addYears(rand(1, 3))->format('Y-m-d'),
                    'engine_status' => 1,
                    'engine_type' => ['ديزل', 'بنزين'][rand(0, 1)],
                    'engine_power' => rand(100, 500) . ' حصان',
                    'crew_number' => rand(3, 8),
                    'payload' => rand(500, 2000),
                    'region_id' => $this->regions->first()->id,
                    'governorate_id' => $this->governorates->first()->id,
                    'port_id' => $this->ports->random()->id,
                ]);

                $this->boats->push($boat);
            }
        }
    }

    /**
     * Seed fishing equipment
     */
    private function seedFishingEquipment(): void
    {
        $equipmentTypes = [
            ['name' => 'شباك صيد', 'name_en' => 'Fishing nets'],
            ['name' => 'صنارة', 'name_en' => 'Fishing rod'],
            ['name' => 'طعم', 'name_en' => 'Bait'],
            ['name' => 'حبال', 'name_en' => 'Ropes'],
            ['name' => 'صناديق ثلج', 'name_en' => 'Ice boxes'],
        ];

        foreach ($this->owners as $owner) {
            foreach ($equipmentTypes as $equipment) {
                FishingEquipment::create([
                    'owner_id' => $owner->id,
                    'name' => $equipment['name'],
                    'name_en' => $equipment['name_en'],
                    'status' => 1,
                ]);
            }
        }
    }

    /**
     * Seed trips (40-60 trips over last 3 months)
     */
    private function seedTrips(): void
    {
        $this->trips = collect();
        $tripCount = rand(40, 60);

        for ($i = 1; $i <= $tripCount; $i++) {
            $boat = $this->boats->random();
            $owner = $boat->owner;

            // Get captains for this owner, or any captain if none available
            $ownerCaptains = $this->captains->where('owner_id', $owner->id);
            $captain = $ownerCaptains->isNotEmpty() ? $ownerCaptains->random() : $this->captains->random();

            $dalal = $this->dalals->random();
            $counter = $this->counters->random();
            $port = $this->ports->random();

            // Random start date within the last 3 months
            $startDate = Carbon::instance($this->startDate)->addDays(rand(0, 90));
            $endDate = (clone $startDate)->addDays(rand(1, 7));

            // Trip status: 1=pending, 2=active, 8=completed
            $status = $endDate->isPast() ? 8 : rand(1, 2);

            $trip = Trip::withoutEvents(function () use ($boat, $i, $status, $owner, $captain, $counter, $dalal, $startDate, $endDate, $port) {
                return Trip::create([
                    'name' => 'رحلة ' . $boat->name_ar . ' - ' . $i,
                    'number' => 'TRIP-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                    'license_number' => 'TL-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                    'status' => $status,
                    'permit_type' => ['يومي', 'أسبوعي', 'شهري'][rand(0, 2)],
                    'owner_id' => $owner->id,
                    'captain_id' => $captain->id,
                    'counter_id' => $counter->id,
                    'dalal_id' => $dalal->id,
                    'boat_name' => $boat->name_ar,
                    'boat_number' => $boat->number,
                    'boat_color' => $boat->color,
                    'boat_length' => $boat->length,
                    'boat_width' => $boat->width,
                    'departure_time' => '05:00',
                    'return_time' => '14:00',
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                    'actual_start_datetime' => $status >= 2 ? $startDate->setTime(5, 0)->toDateTimeString() : null,
                    'actual_end_datetime' => $status == 8 ? $endDate->setTime(14, 0)->toDateTimeString() : null,
                    'region_id' => $this->regions->first()->id,
                    'governorate_id' => $this->governorates->first()->id,
                    'port_id' => $port->id,
                    'departure_port' => $port->name,
                    'return_port' => $port->name,
                    'notes' => $i % 5 == 0 ? 'رحلة ممتازة - صيد وفير' : null,
                    'created_by' => $owner->name,
                    'updated_by' => $owner->name,
                ]);
            });

            $this->trips->push($trip);
        }
    }

    /**
     * Seed fish stocks from completed trips
     */
    private function seedFishStocks(): void
    {
        $completedTrips = $this->trips->where('status', 8);

        foreach ($completedTrips as $trip) {
            // Each trip has 2-5 fish types
            $fishCount = rand(2, 5);
            $fishTypes = $this->fish->random($fishCount);

            foreach ($fishTypes as $fish) {
                // Realistic catch weights
                $weight = rand(50, 500) + (rand(0, 99) / 100);
                $quantity = rand(10, 100);

                FishStock::withoutEvents(function () use ($trip, $fish, $weight, $quantity) {
                    return FishStock::create([
                        'trip_id' => $trip->id,
                        'fish_id' => $fish->id,
                        'fish_name' => $fish->local_name_primary,
                        'weight' => $weight,
                        'quantity' => $quantity,
                        'quantity_captain' => $quantity,
                        'weight_captain' => $weight,
                        'quantity_counter' => $quantity,
                        'weight_counter' => $weight,
                        'added_by' => $trip->captain_id,
                        'corrected_by' => $trip->counter_id,
                        'notes' => null,
                    ]);
                });
            }
        }
    }

    /**
     * Seed dalal stocks (distribute fish from trips to dalals)
     */
    private function seedDalalStocks(): void
    {
        $completedTrips = $this->trips->where('status', 8);

        foreach ($completedTrips as $trip) {
            $fishStocks = FishStock::where('trip_id', $trip->id)->get();

            if ($fishStocks->isEmpty()) {
                continue;
            }

            // Create dalal stock
            $totalWeight = $fishStocks->sum('weight');

            $dalalStock = DalalStock::withoutEvents(function () use ($trip, $totalWeight) {
                return DalalStock::create([
                    'owner_id' => $trip->owner_id,
                    'dalal_id' => $trip->dalal_id,
                    'trip_id' => $trip->id,
                    'status' => 1,
                    'total_weight' => $totalWeight,
                ]);
            });

            // Create dalal stock details from fish stocks
            foreach ($fishStocks as $fishStock) {
                DalalStockDetail::create([
                    'dalal_stock_id' => $dalalStock->id,
                    'fish_id' => $fishStock->fish_id,
                    'fish_name' => $fishStock->fish_name,
                    'weight' => $fishStock->weight,
                    'quantity' => $fishStock->quantity,
                ]);
            }
        }
    }

    /**
     * Seed sales (from dalal stocks)
     */
    private function seedSales(): void
    {
        $dalalStocks = DalalStock::with('details')->get();
        $customers = Customer::all();
        $commissionSetting = $this->commissionSettings->first();

        foreach ($dalalStocks as $dalalStock) {
            // Each dalal stock generates 1-3 sales
            $salesCount = rand(1, 3);

            for ($s = 0; $s < $salesCount; $s++) {
                $customer = $customers->random();
                $paymentMethod = $this->paymentMethods->random();

                $saleDate = Carbon::parse($dalalStock->created_at)->addDays(rand(0, 3));

                $sale = Sale::create([
                    'number' => 'SALE-' . $dalalStock->id . '-' . ($s + 1) . '-' . time(),
                    'seller_type' => 'dalal',
                    'seller_id' => $dalalStock->dalal_id,
                    'trip_id' => $dalalStock->trip_id,
                    'customer_id' => $customer->id,
                    'customer_name' => $customer->name,
                    'payment_method_id' => $paymentMethod->id,
                    'payment_method' => $paymentMethod->name,
                    'commission_setting_id' => $commissionSetting ? $commissionSetting->id : null,
                    'commission_rate' => $commissionSetting ? $commissionSetting->commission_rate : 5.00,
                    'commission_amount' => 0, // Will be calculated
                    'labor_rate' => $commissionSetting ? $commissionSetting->labor_rate : 2.00,
                    'labor_amount' => 0, // Will be calculated
                    'total_price' => 0, // Will be calculated
                    'net_owner_amount' => 0, // Will be calculated
                    'sale_datetime' => $saleDate->toDateTimeString(),
                    'notes' => null,
                ]);

                // Create sale details
                $totalPrice = 0;
                $detailsCount = rand(1, $dalalStock->details->count());
                $selectedDetails = $dalalStock->details->random(min($detailsCount, $dalalStock->details->count()));

                foreach ($selectedDetails as $detail) {
                    $pricePerKilo = rand(10, 50) + (rand(0, 99) / 100);
                    $soldWeight = $detail->weight / $salesCount; // Distribute weight across sales
                    $soldQuantity = (int) ($detail->quantity / $salesCount);
                    $itemTotal = $soldWeight * $pricePerKilo;

                    SaleDetail::create([
                        'sale_id' => $sale->id,
                        'fish_id' => $detail->fish_id,
                        'fish_name' => $detail->fish_name,
                        'quantity' => $soldQuantity,
                        'weight' => $soldWeight,
                        'price_per_kilo' => $pricePerKilo,
                        'total_price' => $itemTotal,
                    ]);

                    $totalPrice += $itemTotal;
                }

                // Update sale totals
                $commissionAmount = $totalPrice * ($sale->commission_rate / 100);
                $laborAmount = $totalPrice * ($sale->labor_rate / 100);
                $netOwnerAmount = $totalPrice - $commissionAmount - $laborAmount;

                $sale->update([
                    'total_price' => $totalPrice,
                    'commission_amount' => $commissionAmount,
                    'labor_amount' => $laborAmount,
                    'net_owner_amount' => $netOwnerAmount,
                ]);
            }
        }
    }

    /**
     * Seed payments for sales
     */
    private function seedPayments(): void
    {
        $sales = Sale::all();

        foreach ($sales as $sale) {
            // 70% of sales have full payment, 20% partial, 10% no payment yet
            $paymentChance = rand(1, 100);

            if ($paymentChance <= 70) {
                // Full payment
                Payment::create([
                    'number' => 'PAY-' . $sale->id . '-' . time(),
                    'sale_id' => $sale->id,
                    'owner_id' => $sale->trip ? $sale->trip->owner_id : null,
                    'seller_id' => $sale->seller_id,
                    'amount' => $sale->total_price,
                    'payment_method_id' => $sale->payment_method_id,
                    'paid_at' => Carbon::parse($sale->sale_datetime)->addHours(rand(1, 24)),
                    'notes' => 'دفعة كاملة',
                ]);
            } elseif ($paymentChance <= 90) {
                // Partial payment (50-80%)
                $partialPercentage = rand(50, 80) / 100;
                Payment::create([
                    'number' => 'PAY-' . $sale->id . '-' . time(),
                    'sale_id' => $sale->id,
                    'owner_id' => $sale->trip ? $sale->trip->owner_id : null,
                    'seller_id' => $sale->seller_id,
                    'amount' => $sale->total_price * $partialPercentage,
                    'payment_method_id' => $sale->payment_method_id,
                    'paid_at' => Carbon::parse($sale->sale_datetime)->addHours(rand(1, 24)),
                    'notes' => 'دفعة جزئية - ' . ($partialPercentage * 100) . '%',
                ]);
            }
            // 10% no payment yet
        }
    }

    /**
     * Seed expenses
     */
    private function seedExpenses(): void
    {
        $generalCategory = $this->categories->where('type', 'general')->first();

        foreach ($this->owners as $owner) {
            // Each owner has 5-10 expenses over the period
            $expenseCount = rand(5, 10);

            for ($i = 0; $i < $expenseCount; $i++) {
                $boat = $this->boats->where('owner_id', $owner->id)->random();
                $date = Carbon::instance($this->startDate)->addDays(rand(0, 90));
                $totalPrice = rand(100, 1000) + (rand(0, 99) / 100);

                Expense::create([
                    'date' => $date->format('Y-m-d'),
                    'number' => 'EXP-' . $owner->id . '-' . time() . '-' . $i,
                    'notes' => 'مصروف ' . ['وقود', 'صيانة', 'تجهيزات'][rand(0, 2)],
                    'owner_id' => $owner->id,
                    'boat_id' => $boat->id,
                    'total_price' => $totalPrice,
                    'discount_type' => null,
                    'discount_value' => 0,
                    'final_price' => $totalPrice,
                    'status' => ['paid', 'pending'][rand(0, 1)],
                    'vendor_id' => null,
                    'payment_method_id' => $this->paymentMethods->random()->id,
                    'category_id' => $generalCategory->id,
                    'vat_rate' => 0,
                ]);
            }
        }
    }

    /**
     * Seed maintenance records
     */
    private function seedMaintenance(): void
    {
        $maintenanceCategory = $this->categories->where('type', 'maintenance')->first();

        foreach ($this->boats as $boat) {
            // Each boat has 1-3 maintenance records
            $maintenanceCount = rand(1, 3);

            for ($i = 0; $i < $maintenanceCount; $i++) {
                $date = Carbon::instance($this->startDate)->addDays(rand(0, 90));

                Maintenance::create([
                    'date' => $date->format('Y-m-d'),
                    'category_id' => $maintenanceCategory->id,
                    'boat_id' => $boat->id,
                    'owner_id' => $boat->owner_id,
                    'estimated_cost' => rand(200, 1500),
                    'description' => 'صيانة ' . ['دورية', 'طارئة', 'شاملة'][rand(0, 2)] . ' للمحرك والمعدات',
                    'technician' => 'فني ' . ['أحمد', 'محمد', 'خالد'][rand(0, 2)],
                ]);
            }
        }
    }

    /**
     * Seed payroll records
     */
    private function seedPayrolls(): void
    {
        foreach ($this->boats as $boat) {
            // Each boat has 1-2 payroll records (monthly)
            $payrollCount = rand(1, 2);

            for ($i = 0; $i < $payrollCount; $i++) {
                $periodFrom = Carbon::instance($this->startDate)->addMonths($i);
                $periodTo = (clone $periodFrom)->endOfMonth();

                // Calculate totals from trips in this period
                $trips = Trip::where('boat_id', $boat->id)
                    ->whereBetween('start_date', [$periodFrom, $periodTo])
                    ->where('status', 8)
                    ->get();

                $totalRevenues = $trips->flatMap->sales->sum('total_price');
                $totalExpenses = Expense::where('boat_id', $boat->id)
                    ->whereBetween('date', [$periodFrom, $periodTo])
                    ->sum('final_price');

                $ownerPercentage = 40;
                $crewPercentage = 60;
                $netProfit = $totalRevenues - $totalExpenses;
                $ownerProfit = $netProfit * ($ownerPercentage / 100);
                $crewTotal = $netProfit * ($crewPercentage / 100);

                Payroll::create([
                    'owner_id' => $boat->owner_id,
                    'boat_id' => $boat->id,
                    'period_from' => $periodFrom->format('Y-m-d'),
                    'period_to' => $periodTo->format('Y-m-d'),
                    'total_revenues' => $totalRevenues,
                    'total_expenses' => $totalExpenses,
                    'owner_profit' => $ownerProfit,
                    'crew_total' => $crewTotal,
                    'carry_over' => 0,
                    'surplus' => $netProfit > 0 ? $netProfit : 0,
                    'deficit' => $netProfit < 0 ? abs($netProfit) : 0,
                    'notes' => 'كشف راتب شهر ' . $periodFrom->format('Y-m'),
                    'owner_percentage' => $ownerPercentage,
                    'status' => ['open', 'closed'][rand(0, 1)],
                ]);
            }
        }
    }

    /**
     * Print summary of seeded data
     */
    private function printSummary(): void
    {
        $this->command->table(
            ['Entity', 'Count'],
            [
                ['Owners', User::where('role', 'owner')->count()],
                ['Captains', User::where('role', 'captain')->count()],
                ['Dalals', User::where('role', 'dalal')->count()],
                ['Counters', User::where('role', 'counter')->count()],
                ['Boats', Boat::count()],
                ['Trips', Trip::count()],
                ['Completed Trips', Trip::where('status', 8)->count()],
                ['Fish Species', Fish::count()],
                ['Fish Stocks', FishStock::count()],
                ['Dalal Stocks', DalalStock::count()],
                ['Customers', Customer::count()],
                ['Sales', Sale::count()],
                ['Sale Details', SaleDetail::count()],
                ['Payments', Payment::count()],
                ['Expenses', Expense::count()],
                ['Maintenance Records', Maintenance::count()],
                ['Payroll Records', Payroll::count()],
            ]
        );

        $this->command->info('');
        $this->command->info('📧 Sample Login Credentials:');
        $this->command->info('  Owner:   owner1@hawat.test / password');
        $this->command->info('  Captain: captain1@hawat.test / password');
        $this->command->info('  Dalal:   dalal1@hawat.test / password');
        $this->command->info('  Counter: counter1@hawat.test / password');
        $this->command->info('');
        $this->command->info('📅 Data Period: ' . $this->startDate->format('Y-m-d') . ' to ' . $this->endDate->format('Y-m-d'));
    }
}
