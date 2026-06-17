<?php

namespace Database\Seeders;

use App\Models\Boat;
use App\Models\BoatType;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Fish;
use App\Models\FishQuantityStock;
use App\Models\Governorate;
use App\Models\Invoice;
use App\Models\Maintenance;
use App\Models\PaymentMethod;
use App\Models\Port;
use App\Models\Region;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Subscription;
use App\Models\SubscriptionPackage;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::where('email', 'owner@example.com')->firstOrFail();
        $region = Region::first();
        $governorate = Governorate::first();
        $port = Port::first();

        $boatTypes = $this->seedBoatTypes();
        $this->seedFish();
        [$captain1, $captain2, $dalal, $counter] = $this->seedSubUsers($owner, $region, $governorate, $port);
        [$boat1, $boat2, $boat3] = $this->seedBoats($owner, $boatTypes, $region, $governorate, $port);
        $this->seedCustomers($owner, $region);
        $this->seedSubscriptions($owner);
        $trips = $this->seedTrips($owner, $captain1, $captain2, $dalal, $counter, $boat1, $boat2, $boat3, $region, $governorate, $port);
        $this->seedFishQuantityStocks($trips, $boat1, $boat2, $boat3);
        $this->seedSales($owner, $trips);
        $this->seedExpenses($owner, $boat1, $boat2, $boat3);
        $this->seedMaintenance($owner, $boat1, $boat2, $boat3);
    }

    private function seedBoatTypes(): \Illuminate\Support\Collection
    {
        $types = [
            ['name_ar' => 'حسكة', 'name_en' => 'Hasaka', 'status' => 1],
            ['name_ar' => 'لنش', 'name_en' => 'Launch', 'status' => 1],
            ['name_ar' => 'فلوكة', 'name_en' => 'Felucca', 'status' => 1],
            ['name_ar' => 'شباك', 'name_en' => 'Net Boat', 'status' => 1],
        ];

        foreach ($types as $type) {
            BoatType::firstOrCreate(['name_ar' => $type['name_ar']], $type);
        }

        return BoatType::all();
    }

    private function seedFish(): void
    {
        $species = [
            ['scientific_name' => 'Epinephelus coioides', 'english_name' => 'Orange-spotted grouper', 'local_name_primary' => 'هامور', 'status' => 1],
            ['scientific_name' => 'Scomberomorus commerson', 'english_name' => 'Narrow-barred Spanish mackerel', 'local_name_primary' => 'كنعد', 'status' => 1],
            ['scientific_name' => 'Lethrinus nebulosus', 'english_name' => 'Spangled emperor', 'local_name_primary' => 'شعري', 'status' => 1],
            ['scientific_name' => 'Lutjanus bohar', 'english_name' => 'Two-spot red snapper', 'local_name_primary' => 'نقرور', 'status' => 1],
            ['scientific_name' => 'Thunnus albacares', 'english_name' => 'Yellowfin tuna', 'local_name_primary' => 'تونة', 'status' => 1],
            ['scientific_name' => 'Carangoides bajad', 'english_name' => 'Orange-spotted trevally', 'local_name_primary' => 'صافي', 'status' => 1],
            ['scientific_name' => 'Siganus rivulatus', 'english_name' => 'Marbled spinefoot', 'local_name_primary' => 'سيجان', 'status' => 1],
        ];

        foreach ($species as $fish) {
            Fish::firstOrCreate(['scientific_name' => $fish['scientific_name']], $fish);
        }
    }

    /** @return array{User, User, User, User} */
    private function seedSubUsers(User $owner, Region $region, Governorate $governorate, Port $port): array
    {
        $captain1 = User::firstOrCreate(
            ['phone' => '0500000011'],
            [
                'name' => 'أحمد الصياد',
                'email' => 'captain1@example.com',
                'password' => Hash::make('password'),
                'role' => 'captain',
                'status' => 1,
                'owner_id' => $owner->id,
                'region_id' => $region->id,
                'governorate_id' => $governorate->id,
                'port_id' => $port->id,
                'id_number' => 'CPT000001',
                'nationality' => 'سعودي',
                'crew_count' => 5,
                'fishing_license_number' => 'FL-2024-001',
                'fishing_license_expiry' => '2026-12-31',
            ]
        );

        $captain2 = User::firstOrCreate(
            ['phone' => '0500000012'],
            [
                'name' => 'خالد البحري',
                'email' => 'captain2@example.com',
                'password' => Hash::make('password'),
                'role' => 'captain',
                'status' => 1,
                'owner_id' => $owner->id,
                'region_id' => $region->id,
                'governorate_id' => $governorate->id,
                'port_id' => $port->id,
                'id_number' => 'CPT000002',
                'nationality' => 'سعودي',
                'crew_count' => 4,
                'fishing_license_number' => 'FL-2024-002',
                'fishing_license_expiry' => '2027-06-30',
            ]
        );

        $dalal = User::firstOrCreate(
            ['phone' => '0500000013'],
            [
                'name' => 'محمد الدلال',
                'email' => 'dalal@example.com',
                'password' => Hash::make('password'),
                'role' => 'dalal',
                'status' => 1,
                'owner_id' => $owner->id,
                'region_id' => $region->id,
                'governorate_id' => $governorate->id,
                'port_id' => $port->id,
                'id_number' => 'DLL000001',
                'tax_number' => '3001234567',
            ]
        );

        $counter = User::firstOrCreate(
            ['phone' => '0500000014'],
            [
                'name' => 'علي العداد',
                'email' => 'counter@example.com',
                'password' => Hash::make('password'),
                'role' => 'counter',
                'status' => 1,
                'owner_id' => $owner->id,
                'region_id' => $region->id,
                'governorate_id' => $governorate->id,
                'port_id' => $port->id,
                'id_number' => 'CNT000001',
            ]
        );

        return [$captain1, $captain2, $dalal, $counter];
    }

    /** @return array{Boat, Boat, Boat} */
    private function seedBoats(User $owner, \Illuminate\Support\Collection $boatTypes, Region $region, Governorate $governorate, Port $port): array
    {
        $type1 = $boatTypes->firstWhere('name_ar', 'لنش') ?? $boatTypes->first();
        $type2 = $boatTypes->firstWhere('name_ar', 'حسكة') ?? $boatTypes->first();
        $type3 = $boatTypes->firstWhere('name_ar', 'شباك') ?? $boatTypes->first();

        $boat1 = Boat::firstOrCreate(
            ['number' => 'B-SA-0001'],
            [
                'owner_id' => $owner->id,
                'name_ar' => 'أمواج البحر',
                'name_en' => 'Sea Waves',
                'status' => 1,
                'length' => '12.5',
                'width' => '3.8',
                'color' => 'أزرق',
                'type' => 'لنش',
                'boat_type_id' => $type1?->id,
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
            ]
        );

        $boat2 = Boat::firstOrCreate(
            ['number' => 'B-SA-0002'],
            [
                'owner_id' => $owner->id,
                'name_ar' => 'نجمة الشرق',
                'name_en' => 'Star of the East',
                'status' => 1,
                'length' => '10.0',
                'width' => '3.2',
                'color' => 'أبيض',
                'type' => 'حسكة',
                'boat_type_id' => $type2?->id,
                'license_number' => 'LIC-SA-0002',
                'license_region_id' => $region->id,
                'license_date' => '2021-06-10',
                'license_date_expire' => '2026-06-09',
                'body_number' => 'HLL-002',
                'engine_status' => 1,
                'engine_type' => 'بنزين',
                'engine_power' => '180 حصان',
                'crew_number' => 4,
                'payload' => 900,
                'region_id' => $region->id,
                'governorate_id' => $governorate->id,
                'port_id' => $port->id,
            ]
        );

        $boat3 = Boat::firstOrCreate(
            ['number' => 'B-SA-0003'],
            [
                'owner_id' => $owner->id,
                'name_ar' => 'فجر الخليج',
                'name_en' => 'Gulf Dawn',
                'status' => 1,
                'length' => '14.2',
                'width' => '4.5',
                'color' => 'أخضر',
                'type' => 'شباك',
                'boat_type_id' => $type3?->id,
                'license_number' => 'LIC-SA-0003',
                'license_region_id' => $region->id,
                'license_date' => '2023-03-20',
                'license_date_expire' => '2028-03-19',
                'body_number' => 'HLL-003',
                'engine_status' => 1,
                'engine_type' => 'ديزل',
                'engine_power' => '350 حصان',
                'crew_number' => 7,
                'payload' => 1800,
                'region_id' => $region->id,
                'governorate_id' => $governorate->id,
                'port_id' => $port->id,
            ]
        );

        return [$boat1, $boat2, $boat3];
    }

    private function seedCustomers(User $owner, Region $region): void
    {
        // Attach the two customers from DefaultMasterSeeder to this owner
        Customer::whereNull('owner_id')->update(['owner_id' => $owner->id, 'status' => 1, 'type' => 'company']);

        $newCustomers = [
            ['name' => 'سوق السمك المركزي', 'phone' => '0555001001', 'email' => 'central.fish@market.sa', 'type' => 'company'],
            ['name' => 'مطعم الصياد الذهبي', 'phone' => '0555001002', 'email' => 'golden@restaurant.sa', 'type' => 'restaurant'],
            ['name' => 'شركة التصدير البحري السعودية', 'phone' => '0555001003', 'email' => 'export@saudi-marine.sa', 'type' => 'company'],
            ['name' => 'فندق الخليج الكبير', 'phone' => '0555001004', 'email' => 'purchase@gulf-hotel.sa', 'type' => 'hotel'],
            ['name' => 'أحمد محمد - تاجر جملة', 'phone' => '0555001005', 'email' => 'ahmed.wholesale@gmail.com', 'type' => 'individual'],
            ['name' => 'سوبرماركت البحر الأحمر', 'phone' => '0555001006', 'email' => 'buying@redsea-market.sa', 'type' => 'retail'],
        ];

        foreach ($newCustomers as $data) {
            Customer::firstOrCreate(
                ['phone' => $data['phone']],
                array_merge($data, ['owner_id' => $owner->id, 'status' => 1, 'region_id' => $region->id])
            );
        }
    }

    private function seedSubscriptions(User $owner): void
    {
        $basic = SubscriptionPackage::firstOrCreate(
            ['name_ar' => 'باقة الأساسية'],
            [
                'name_en' => 'Basic Package',
                'boats_count' => 1,
                'price' => 249.00,
                'original_price' => 299.00,
                'duration_type' => 'monthly',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 1,
                'feature_ar' => json_encode(['إدارة قارب واحد', 'تسجيل الرحلات', 'متابعة المصروفات', 'دعم فني عبر الإيميل'], JSON_UNESCAPED_UNICODE),
                'feature_en' => json_encode(['Manage 1 boat', 'Trip tracking', 'Expense management', 'Email support']),
            ]
        );

        $pro = SubscriptionPackage::firstOrCreate(
            ['name_ar' => 'باقة الاحترافية'],
            [
                'name_en' => 'Pro Package',
                'boats_count' => 5,
                'price' => 699.00,
                'original_price' => 899.00,
                'duration_type' => 'monthly',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 2,
                'feature_ar' => json_encode(['إدارة 5 قوارب', 'تقارير متقدمة', 'إدارة المبيعات', 'إدارة المصروفات', 'إدارة الصيانة', 'دعم فني على مدار الساعة'], JSON_UNESCAPED_UNICODE),
                'feature_en' => json_encode(['Manage 5 boats', 'Advanced reports', 'Sales management', 'Expense management', 'Maintenance tracking', '24/7 support']),
            ]
        );

        SubscriptionPackage::firstOrCreate(
            ['name_ar' => 'باقة المؤسسات'],
            [
                'name_en' => 'Enterprise Package',
                'boats_count' => 20,
                'price' => 1799.00,
                'original_price' => 2199.00,
                'duration_type' => 'monthly',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 3,
                'feature_ar' => json_encode(['إدارة 20 قارب', 'جميع مميزات الاحترافية', 'API مخصص', 'مدير حساب مخصص', 'تدريب الفريق'], JSON_UNESCAPED_UNICODE),
                'feature_en' => json_encode(['Manage 20 boats', 'All Pro features', 'Custom API access', 'Dedicated account manager', 'Team training']),
            ]
        );

        if (Subscription::where('user_id', $owner->id)->exists()) {
            return;
        }

        $startDate = Carbon::now()->subMonths(6);
        $endDate = Carbon::now()->addMonths(6);

        $subscription = Subscription::create([
            'user_id' => $owner->id,
            'package_id' => $pro->id,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'status' => 'active',
            'is_suspended' => false,
            'renewal_count' => 1,
        ]);

        Invoice::create([
            'subscription_id' => $subscription->id,
            'user_id' => $owner->id,
            'invoice_number' => 'INV-2025-00001',
            'amount' => 699.00,
            'vat_rate' => 15,
            'vat_amount' => 104.85,
            'total_amount' => 803.85,
            'discount_amount' => 0,
            'payment_method' => 'bank_transfer',
            'payment_status' => 'paid',
            'paid_at' => $startDate->copy()->addDay(),
        ]);
    }

    /** @return array<string, Trip> */
    private function seedTrips(
        User $owner,
        User $captain1,
        User $captain2,
        User $dalal,
        User $counter,
        Boat $boat1,
        Boat $boat2,
        Boat $boat3,
        Region $region,
        Governorate $governorate,
        Port $port
    ): array {
        $portName = $port->name;
        $now = Carbon::now();

        $tripsData = [
            'new_trip' => [
                'name' => 'رحلة نجمة الشرق - المقبلة',
                'number' => 'TRIP-DEMO-008',
                'status' => 1,
                'boat' => $boat2,
                'captain' => $captain2,
                'start_date' => $now->copy()->addWeek(),
                'days' => 5,
            ],
        ];

        $trips = [];

        foreach ($tripsData as $key => $data) {
            $existing = Trip::withTrashed()->where('number', $data['number'])->first();
            if ($existing) {
                Schema::disableForeignKeyConstraints();
                foreach ($existing->sales as $sale) {
                    $sale->details()->delete();
                    $sale->forceDelete();
                }
                $existing->fishQuantityStocks()->delete();
                $existing->catches()->delete();
                $existing->forceDelete();
                Schema::enableForeignKeyConstraints();
            }

            /** @var Boat $boat */
            $boat = $data['boat'];
            /** @var User $captain */
            $captain = $data['captain'];
            $startDate = $data['start_date'];
            $endDate = $startDate->copy()->addDays($data['days']);
            $status = $data['status'];

            $trips[$key] = Trip::withoutEvents(fn () => Trip::create([
                'name' => $data['name'],
                'number' => $data['number'],
                'license_number' => 'TL-'.$data['number'],
                'status' => $status,
                'permit_type' => 'يومي',
                'owner_id' => $owner->id,
                'captain_id' => $captain->id,
                'counter_id' => $counter->id,
                'dalal_id' => $dalal->id,
                'boat_id' => $boat->id,
                'boat_name' => $boat->name_ar,
                'boat_number' => $boat->number,
                'boat_color' => $boat->color,
                'boat_length' => $boat->length,
                'boat_width' => $boat->width,
                'crew_count' => $boat->crew_number,
                'departure_time' => '05:00',
                'return_time' => '14:00',
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'actual_start_datetime' => $status >= 2 ? $startDate->copy()->setTime(5, 0)->toDateTimeString() : null,
                'actual_end_datetime' => $status >= 4 ? $endDate->copy()->setTime(14, 0)->toDateTimeString() : null,
                'region_id' => $region->id,
                'governorate_id' => $governorate->id,
                'port_id' => $port->id,
                'departure_port' => $portName,
                'return_port' => $portName,
                'notes' => $status === 8 ? 'رحلة ناجحة - صيد وفير' : null,
                'created_by' => 'system',
                'updated_by' => 'system',
            ]));
        }

        return $trips;
    }

    private function seedFishQuantityStocks(array $trips, Boat $boat1, Boat $boat2, Boat $boat3): void
    {
        $allFish = Fish::where('status', 1)->get();

        $boatMap = [
            'completed_1' => $boat1,
            'completed_2' => $boat2,
            'completed_3' => $boat3,
            'ready_to_sell' => $boat1,
        ];

        foreach ($boatMap as $key => $boat) {
            if (! isset($trips[$key])) {
                continue;
            }

            $trip = $trips[$key];

            if (FishQuantityStock::where('trip_id', $trip->id)->exists()) {
                continue;
            }

            $selectedFish = $allFish->random(min(4, $allFish->count()));

            foreach ($selectedFish as $fish) {
                FishQuantityStock::create([
                    'trip_id' => $trip->id,
                    'boat_id' => $boat->id,
                    'fish_id' => $fish->id,
                    'quantity' => rand(30, 150),
                    'price_per_kg' => rand(20, 80) + (rand(0, 99) / 100),
                ]);
            }
        }
    }

    private function seedSales(User $owner, array $trips): void
    {
        // commission_settings table was dropped but FK remains in schema; disable checks during insert
        Schema::disableForeignKeyConstraints();

        $customers = Customer::where('owner_id', $owner->id)->get();
        $paymentMethods = PaymentMethod::where('status', 1)->get();
        $allFish = Fish::where('status', 1)->get();

        $saleConfigs = [
            'completed_1' => ['count' => 2, 'status' => 2, 'payment_status' => 'paid'],
            'completed_2' => ['count' => 2, 'status' => 2, 'payment_status' => 'paid'],
            'completed_3' => ['count' => 2, 'status' => 2, 'payment_status' => 'partially_paid'],
            'ready_to_sell' => ['count' => 1, 'status' => 1, 'payment_status' => 'unpaid'],
        ];

        $saleIndex = 1;

        foreach ($saleConfigs as $key => $config) {
            if (! isset($trips[$key])) {
                continue;
            }

            $trip = $trips[$key];

            for ($s = 1; $s <= $config['count']; $s++) {
                $saleNumber = 'SALE-DEMO-'.str_pad($saleIndex++, 4, '0', STR_PAD_LEFT);

                if (Sale::where('number', $saleNumber)->exists()) {
                    continue;
                }

                $customer = $customers->isNotEmpty() ? $customers->random() : null;
                $paymentMethod = $paymentMethods->isNotEmpty() ? $paymentMethods->random() : null;
                $saleDate = Carbon::parse($trip->end_date)->addDays($s);

                $sale = Sale::withoutEvents(fn () => Sale::create([
                    'number' => $saleNumber,
                    'seller_type' => 'owner',
                    'seller_id' => $owner->id,
                    'trip_id' => $trip->id,
                    'customer_id' => $customer?->id,
                    'customer_name' => $customer?->name ?? 'عميل نقدي',
                    'payment_method_id' => $paymentMethod?->id,
                    'payment_method' => $paymentMethod?->name,
                    'commission_rate' => 5.00,
                    'commission_amount' => 0,
                    'labor_rate' => 2.00,
                    'labor_amount' => 0,
                    'total_price' => 0,
                    'net_owner_amount' => 0,
                    'remaining_total' => 0,
                    'status' => $config['status'],
                    'payment_status' => $config['payment_status'],
                    'sale_datetime' => $saleDate->toDateTimeString(),
                ]));

                $selectedFish = $allFish->random(min(rand(3, 5), $allFish->count()));
                $totalPrice = 0;

                foreach ($selectedFish as $fish) {
                    $weight = round(rand(40, 200) + (rand(0, 99) / 100), 2);
                    $pricePerKilo = round(rand(18, 75) + (rand(0, 99) / 100), 2);
                    $itemTotal = round($weight * $pricePerKilo, 2);
                    $totalPrice += $itemTotal;

                    SaleDetail::create([
                        'sale_id' => $sale->id,
                        'fish_id' => $fish->id,
                        'fish_name' => $fish->local_name_primary,
                        'quantity' => rand(5, 50),
                        'weight' => $weight,
                        'price_per_kilo' => $pricePerKilo,
                        'total_price' => $itemTotal,
                    ]);
                }

                $commissionAmount = round($totalPrice * 0.05, 2);
                $laborAmount = round($totalPrice * 0.02, 2);
                $netOwnerAmount = round($totalPrice - $commissionAmount - $laborAmount, 2);

                $sale->update([
                    'total_price' => $totalPrice,
                    'commission_amount' => $commissionAmount,
                    'labor_amount' => $laborAmount,
                    'net_owner_amount' => $netOwnerAmount,
                    'remaining_total' => $config['payment_status'] === 'unpaid' ? $totalPrice : 0,
                ]);
            }
        }

        Schema::enableForeignKeyConstraints();
    }

    private function seedExpenses(User $owner, Boat $boat1, Boat $boat2, Boat $boat3): void
    {
        $paymentMethods = PaymentMethod::where('status', 1)->get();

        $fuelCategory = Category::where('name_en', 'Fuel')->first();
        $iceCategory = Category::where('name_en', 'Ice')->first();
        $renewalCategory = Category::where('name_en', 'Renewal Fees')->first();
        $sparePartsCategory = Category::where('name_en', 'Spare Parts')->first();

        $expenses = [
            ['boat' => $boat1, 'date' => Carbon::now()->subMonths(3), 'category' => $fuelCategory, 'total' => 850.00, 'notes' => 'وقود ديزل - رحلة مارس'],
            ['boat' => $boat1, 'date' => Carbon::now()->subMonths(3)->addDays(2), 'category' => $iceCategory, 'total' => 120.00, 'notes' => 'ثلج للحفاظ على الأسماك'],
            ['boat' => $boat1, 'date' => Carbon::now()->subMonths(2), 'category' => $renewalCategory, 'total' => 500.00, 'notes' => 'تجديد رخصة القارب السنوية'],
            ['boat' => $boat2, 'date' => Carbon::now()->subMonths(2), 'category' => $fuelCategory, 'total' => 620.00, 'notes' => 'وقود بنزين - رحلة أبريل'],
            ['boat' => $boat2, 'date' => Carbon::now()->subWeeks(6), 'category' => $sparePartsCategory, 'total' => 1350.00, 'notes' => 'قطع غيار محرك'],
            ['boat' => $boat2, 'date' => Carbon::now()->subWeeks(4), 'category' => $iceCategory, 'total' => 95.00, 'notes' => 'ثلج للأسماك'],
            ['boat' => $boat3, 'date' => Carbon::now()->subWeeks(6), 'category' => $fuelCategory, 'total' => 1100.00, 'notes' => 'وقود ديزل - رحلة مايو'],
            ['boat' => $boat3, 'date' => Carbon::now()->subWeeks(5), 'category' => $iceCategory, 'total' => 180.00, 'notes' => 'ثلج وتبريد'],
            ['boat' => $boat3, 'date' => Carbon::now()->subWeeks(3), 'category' => $renewalCategory, 'total' => 750.00, 'notes' => 'رسوم تجديد تصريح الصيد'],
        ];

        $expenseIndex = 1;

        foreach ($expenses as $data) {
            $expenseNumber = 'EXP-DEMO-'.str_pad($expenseIndex++, 4, '0', STR_PAD_LEFT);

            if (Expense::withoutGlobalScopes()->where('number', $expenseNumber)->exists()) {
                continue;
            }

            /** @var Boat $boat */
            $boat = $data['boat'];
            $total = $data['total'];

            Expense::create([
                'date' => $data['date']->toDateString(),
                'number' => $expenseNumber,
                'notes' => $data['notes'],
                'owner_id' => $owner->id,
                'boat_id' => $boat->id,
                'total_price' => $total,
                'discount_type' => null,
                'discount_value' => 0,
                'final_price' => $total,
                'status' => 'paid',
                'payment_method_id' => $paymentMethods->isNotEmpty() ? $paymentMethods->random()->id : null,
                'category_id' => $data['category']?->id,
                'vat_rate' => 0,
            ]);
        }
    }

    private function seedMaintenance(User $owner, Boat $boat1, Boat $boat2, Boat $boat3): void
    {
        $records = [
            [
                'boat' => $boat1,
                'date' => Carbon::now()->subMonths(3)->addDays(7),
                'category_name' => 'Routine Maintenance',
                'cost' => 800.00,
                'description' => 'صيانة دورية للمحرك وتغيير الزيت وفلاتر الوقود',
                'technician' => 'أحمد الميكانيكي',
            ],
            [
                'boat' => $boat2,
                'date' => Carbon::now()->subMonths(2)->addDays(5),
                'category_name' => 'Spare Parts',
                'cost' => 1500.00,
                'description' => 'استبدال قطع غيار المحرك - بلوجات وأحزمة',
                'technician' => 'محمد الفني',
            ],
            [
                'boat' => $boat3,
                'date' => Carbon::now()->subWeeks(5),
                'category_name' => 'Repair Costs',
                'cost' => 2200.00,
                'description' => 'إصلاح هيكل القارب وطلاء الجسم الخارجي',
                'technician' => 'خالد النجار',
            ],
            [
                'boat' => $boat1,
                'date' => Carbon::now()->subWeeks(3),
                'category_name' => 'Emergency Maintenance',
                'cost' => 950.00,
                'description' => 'إصلاح عطل طارئ في نظام التبريد',
                'technician' => 'أحمد الميكانيكي',
            ],
            [
                'boat' => $boat2,
                'date' => Carbon::now()->subWeeks(2),
                'category_name' => 'Routine Maintenance',
                'cost' => 600.00,
                'description' => 'صيانة الأجهزة الملاحية والكهرباء',
                'technician' => 'سالم الكهربائي',
            ],
        ];

        foreach ($records as $data) {
            /** @var Boat $boat */
            $boat = $data['boat'];
            $category = Category::where('name_en', $data['category_name'])->first();

            $existing = Maintenance::withoutGlobalScopes()
                ->where('boat_id', $boat->id)
                ->where('description', $data['description'])
                ->exists();

            if ($existing) {
                continue;
            }

            Maintenance::create([
                'date' => $data['date']->toDateString(),
                'category_id' => $category?->id,
                'boat_id' => $boat->id,
                'owner_id' => $owner->id,
                'estimated_cost' => $data['cost'],
                'description' => $data['description'],
                'technician' => $data['technician'],
            ]);
        }
    }
}
