<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AssetDepreciation;
use App\Models\Boat;
use App\Models\Coupon;
use App\Models\Fish;
use App\Models\Invoice;
use App\Models\Region;
use App\Models\Sale;
use App\Models\Subscription;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

/**
 * Stages the precise data needed to reproduce — by eye, in the running app —
 * the accounting findings documented in docs/accounting-review-report.md.
 *
 * It is intentionally separate from DemoDataSeeder: this seeder adds nothing a
 * real operator would create on purpose; every row exists to make a specific
 * miscalculation visible on screen. Each method maps to one finding.
 *
 * Where to look after seeding (owner panel, logged in as owner@example.com):
 *  - Finding 2 & 3  Assets / depreciation  → /owner/assets and /owner/asset_depreciation
 *  - Finding 5      Cross-tenant revenue   → /owner dashboard (totals include owner #2)
 *  - Finding 7      Payroll 50% / div-by-0 → /owner/payrolls (percentage crew)
 *  - Finding 8      Invoice discount/VAT   → /admin/invoices (VAT charged on pre-discount base)
 *  - Finding 1 & 4  P&L double-count       → /owner/profit-loss (already visible from DemoDataSeeder)
 */
class AccountingErrorsDemoSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::where('email', 'owner@example.com')->firstOrFail();

        $this->seedAssets();
        $this->seedPayrollCrew($owner);
        $this->seedDiscountedInvoice($owner);
        $this->seedSecondOwnerSales();
    }

    /**
     * Findings 2 & 3 — depreciation.
     *
     * The "old" asset matches the report's worked example (cost 100,000,
     * salvage 10,000, 5-yr life, bought 2018). Running the depreciation command
     * posts a row for every year 2018→now with no cap, so accumulated
     * depreciation blows past the 90,000 depreciable base and book value goes
     * negative — visible on /owner/asset_depreciation.
     */
    private function seedAssets(): void
    {
        $boats = Boat::orderBy('id')->get();
        if ($boats->isEmpty()) {
            return;
        }

        $assets = [
            [
                'boat' => $boats->get(0),
                'name' => 'محرك ديزل رئيسي (مثال الإهلاك السالب)',
                'asset_type' => 'boat',
                'purchase_date' => '2018-01-15',
                'purchase_cost' => 100000,
                'salvage_value' => 10000,
                'useful_life_years' => 5,
                'depreciation_method' => 'straight_line',
                'depreciation_rate' => 0,
                'notes' => 'عمره الإنتاجي انتهى منذ سنوات — يجب أن يتوقف الإهلاك عند 90,000.',
            ],
            [
                'boat' => $boats->get(1) ?? $boats->get(0),
                'name' => 'شباك ومعدات صيد',
                'asset_type' => 'fishing_equipment',
                'purchase_date' => '2024-03-01',
                'purchase_cost' => 35000,
                'salvage_value' => 3000,
                'useful_life_years' => 8,
                'depreciation_method' => 'straight_line',
                'depreciation_rate' => 0,
                'notes' => 'أصل حديث ضمن العمر الإنتاجي.',
            ],
            [
                'boat' => $boats->get(2) ?? $boats->get(0),
                'name' => 'نظام تبريد (نسبة متناقصة)',
                'asset_type' => 'fishing_equipment',
                'purchase_date' => '2020-06-01',
                'purchase_cost' => 48000,
                'salvage_value' => 4000,
                'useful_life_years' => 6,
                'depreciation_method' => 'percentage',
                'depreciation_rate' => 20,
                'notes' => 'طريقة النسبة المتناقصة.',
            ],
        ];

        foreach ($assets as $data) {
            /** @var Boat $boat */
            $boat = $data['boat'];

            if (Asset::where('boat_id', $boat->id)->where('name', $data['name'])->exists()) {
                continue;
            }

            Asset::create([
                'boat_id' => $boat->id,
                'name' => $data['name'],
                'asset_type' => $data['asset_type'],
                'description' => $data['name'],
                'purchase_date' => $data['purchase_date'],
                'purchase_cost' => $data['purchase_cost'],
                'salvage_value' => $data['salvage_value'],
                'depreciation_method' => $data['depreciation_method'],
                'useful_life_years' => $data['useful_life_years'],
                'depreciation_rate' => $data['depreciation_rate'],
                'status' => 'active',
                'notes' => $data['notes'],
            ]);
        }

        // Post the (buggy, uncapped) depreciation rows so the negative book value
        // is visible on /owner/asset_depreciation without a manual command run.
        if (AssetDepreciation::count() === 0) {
            Artisan::call('app:assets-depreciation-run');
        }
    }

    /**
     * Finding 7 — payroll hard-codes a 50% split and divides by zero.
     *
     * Attaches captains + percentage/fixed crew to boats so the percentage
     * payroll flow has something to distribute. With this data the generated
     * crew share is a flat 50% of trip income (ignoring any configured owner
     * percentage). An "employee" paid by percentage is included to reproduce
     * the division-by-zero branch (no captain/crew on its boat ⇒ count = 0).
     */
    private function seedPayrollCrew(User $owner): void
    {
        $boats = Boat::where('owner_id', $owner->id)->orderBy('id')->get();
        $boat1 = $boats->get(0);
        $boat2 = $boats->get(1) ?? $boat1;

        if (! $boat1) {
            return;
        }

        $captain1 = User::where('phone', '0500000011')->first(); // أحمد الصياد
        $captain2 = User::where('phone', '0500000012')->first(); // خالد البحري

        // Captain on boat 1 paid by percentage, captain on boat 2 paid a fixed salary.
        $captain1?->update(['boat_id' => $boat1->id, 'salary_type' => 'percentage', 'salary_amount' => 40]);
        $captain2?->update(['boat_id' => $boat2->id, 'salary_type' => 'salary', 'salary_amount' => 3500]);

        $region = Region::first();

        $crew = [
            ['phone' => '0500000021', 'name' => 'سعيد البحار', 'boat_id' => $boat1->id, 'role' => 'crew', 'salary_type' => 'percentage', 'salary_amount' => 30],
            ['phone' => '0500000022', 'name' => 'ماجد النوخذة', 'boat_id' => $boat1->id, 'role' => 'crew', 'salary_type' => 'percentage', 'salary_amount' => 30],
            ['phone' => '0500000023', 'name' => 'فهد العامل', 'boat_id' => $boat1->id, 'role' => 'crew', 'salary_type' => 'salary', 'salary_amount' => 2000],
            // Employee paid by percentage with no crew/captain peers counted on its boat
            // → reproduces the division-by-zero in PayrollService::calculateMonthlyPayrollPercentage().
            ['phone' => '0500000024', 'name' => 'موظف بالنسبة (قسمة على صفر)', 'boat_id' => $boat2->id, 'role' => 'employee', 'salary_type' => 'percentage', 'salary_amount' => 25],
        ];

        foreach ($crew as $member) {
            User::firstOrCreate(
                ['phone' => $member['phone']],
                [
                    'name' => $member['name'],
                    'email' => 'crew'.substr($member['phone'], -2).'@example.com',
                    'password' => Hash::make('password'),
                    'role' => $member['role'],
                    'status' => 1,
                    'owner_id' => $owner->id,
                    'boat_id' => $member['boat_id'],
                    'salary_type' => $member['salary_type'],
                    'salary_amount' => $member['salary_amount'],
                    'region_id' => $region?->id,
                ]
            );
        }
    }

    /**
     * Finding 8 — subscription invoice ignores the discount and mis-bases VAT.
     *
     * Seeds a coupon and a second invoice whose numbers mirror the buggy app
     * math: VAT is computed on the full pre-discount amount and the discount is
     * never subtracted from the total, so the customer is over-charged.
     */
    private function seedDiscountedInvoice(User $owner): void
    {
        $coupon = Coupon::firstOrCreate(
            ['code' => 'WELCOME100'],
            [
                'name' => 'خصم ترحيبي',
                'type' => Coupon::TYPE_FIXED,
                'value' => 100,
                'usage_limit' => 100,
                'times_used' => 1,
                'valid_from' => Carbon::now()->subMonths(2),
                'valid_until' => Carbon::now()->addMonths(6),
                'is_active' => true,
                'description_ar' => 'خصم 100 ريال على الاشتراك',
            ]
        );

        $subscription = Subscription::where('user_id', $owner->id)->first();
        if (! $subscription || Invoice::where('invoice_number', 'INV-2025-00002')->exists()) {
            return;
        }

        $amount = 699.00;       // package price
        $discount = 100.00;     // coupon value the operator intended to grant
        $vatRate = 15;

        // Buggy basis (matches app): VAT on the PRE-discount amount, discount never subtracted.
        $vatAmount = round(($amount * $vatRate) / 100, 2);        // 104.85
        $totalAmount = round($amount + $vatAmount, 2);            // 803.85 — discount ignored

        Invoice::create([
            'subscription_id' => $subscription->id,
            'user_id' => $owner->id,
            'coupon_id' => $coupon->id,
            'invoice_number' => 'INV-2025-00002',
            'amount' => $amount,
            'vat_rate' => $vatRate,
            'vat_amount' => $vatAmount,
            'total_amount' => $totalAmount,
            'discount_amount' => $discount,
            'payment_method' => 'bank_transfer',
            'payment_status' => 'paid',
            'paid_at' => Carbon::now()->subMonth(),
        ]);
    }

    /**
     * Finding 5 — owner dashboard sums every owner's sales (no tenant scope).
     *
     * Adds a SECOND owner with their own boat, trip and sales. Because `Sale`
     * has no owner global scope, owner #1's dashboard `Sale::sum('total_price')`
     * now silently includes owner #2's revenue.
     */
    private function seedSecondOwnerSales(): void
    {
        $region = Region::first();
        $governorate = \App\Models\Governorate::first();
        $port = \App\Models\Port::first();

        $owner2 = User::firstOrCreate(
            ['email' => 'owner2@example.com'],
            [
                'name' => 'مالك ثانٍ (لاختبار العزل)',
                'phone' => '0500000099',
                'password' => Hash::make('password'),
                'role' => 'owner',
                'status' => 1,
                'region_id' => $region?->id,
            ]
        );

        $boat = Boat::firstOrCreate(
            ['number' => 'B-SA-9001'],
            [
                'owner_id' => $owner2->id,
                'name_ar' => 'قارب المالك الثاني',
                'name_en' => 'Second Owner Boat',
                'status' => 1,
                'type' => 'لنش',
                'crew_number' => 4,
                'region_id' => $region?->id,
                'governorate_id' => $governorate?->id,
                'port_id' => $port?->id,
            ]
        );

        $captain = User::firstOrCreate(
            ['phone' => '0500000098'],
            [
                'name' => 'قبطان المالك الثاني',
                'email' => 'captain.owner2@example.com',
                'password' => Hash::make('password'),
                'role' => 'captain',
                'status' => 1,
                'owner_id' => $owner2->id,
                'boat_id' => $boat->id,
                'region_id' => $region?->id,
            ]
        );

        $trip = Trip::withTrashed()->where('number', 'TRIP-OWNER2-001')->first();
        if (! $trip) {
            $trip = Trip::withoutEvents(fn () => Trip::create([
                'name' => 'رحلة المالك الثاني',
                'number' => 'TRIP-OWNER2-001',
                'license_number' => 'TL-TRIP-OWNER2-001',
                'status' => 8,
                'owner_id' => $owner2->id,
                'captain_id' => $captain->id,
                'boat_id' => $boat->id,
                'boat_name' => $boat->name_ar,
                'boat_number' => $boat->number,
                'crew_count' => $boat->crew_number,
                'start_date' => Carbon::now()->subWeeks(5)->toDateString(),
                'end_date' => Carbon::now()->subWeeks(5)->addDays(4)->toDateString(),
                'region_id' => $region?->id,
                'governorate_id' => $governorate?->id,
                'port_id' => $port?->id,
                'created_by' => 'system',
            ]));
        }

        Schema::disableForeignKeyConstraints();

        $allFish = Fish::where('status', 1)->get();

        for ($i = 1; $i <= 2; $i++) {
            $saleNumber = 'SALE-OWNER2-'.str_pad((string) $i, 4, '0', STR_PAD_LEFT);
            if (Sale::where('number', $saleNumber)->exists()) {
                continue;
            }

            $totalPrice = round(rand(15000, 25000) + (rand(0, 99) / 100), 2);

            Sale::withoutEvents(fn () => Sale::create([
                'number' => $saleNumber,
                'seller_type' => 'owner',
                'seller_id' => $owner2->id,
                'trip_id' => $trip->id,
                'customer_name' => 'عميل المالك الثاني',
                'commission_rate' => 5,
                'commission_amount' => round($totalPrice * 0.05, 2),
                'labor_rate' => 2,
                'labor_amount' => round($totalPrice * 0.02, 2),
                'total_price' => $totalPrice,
                'net_owner_amount' => round($totalPrice * 0.93, 2),
                'remaining_total' => 0,
                'status' => 2,
                'payment_status' => 'paid',
                'sale_datetime' => Carbon::now()->subWeeks(4)->toDateTimeString(),
            ]));
        }

        Schema::enableForeignKeyConstraints();
    }
}
