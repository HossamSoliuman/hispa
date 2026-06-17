<?php

namespace Database\Seeders;

use App\Models\Boat;
use App\Models\BoatType;
use App\Models\Governorate;
use App\Models\Port;
use App\Models\Region;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeds a minimal working setup (one boat, one vendor, and the basic crew
 * people) without any financial data — trips, sales, expenses, maintenance.
 * Intended as a clean starting point for manually testing the financial
 * workflow end to end.
 */
class BasicWorkflowSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::where('email', 'owner@example.com')->firstOrFail();
        $region = Region::first();
        $governorate = Governorate::first();
        $port = Port::first();

        $boat = $this->seedBoat($owner, $region, $governorate, $port);
        $this->seedPeople($owner, $region, $governorate, $port, $boat);
        $this->seedVendor($owner, $region, $governorate);
    }

    private function seedBoat(User $owner, Region $region, Governorate $governorate, Port $port): Boat
    {
        $boatType = BoatType::firstOrCreate(
            ['name_ar' => 'لنش'],
            ['name_en' => 'Launch', 'status' => 1]
        );

        return Boat::firstOrCreate(
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
            ]
        );
    }

    private function seedPeople(User $owner, Region $region, Governorate $governorate, Port $port, Boat $boat): void
    {
        $people = [
            [
                'phone' => '0500000011',
                'name' => 'أحمد الصياد',
                'email' => 'captain1@example.com',
                'role' => 'captain',
                'boat_id' => $boat->id,
                'id_number' => 'CPT000001',
                'nationality' => 'سعودي',
                'crew_count' => 5,
                'fishing_license_number' => 'FL-2024-001',
                'fishing_license_expiry' => '2026-12-31',
            ],
            [
                'phone' => '0500000013',
                'name' => 'محمد الدلال',
                'email' => 'dalal@example.com',
                'role' => 'dalal',
                'id_number' => 'DLL000001',
                'tax_number' => '3001234567',
            ],
            [
                'phone' => '0500000014',
                'name' => 'علي العداد',
                'email' => 'counter@example.com',
                'role' => 'counter',
                'id_number' => 'CNT000001',
            ],
        ];

        foreach ($people as $person) {
            User::firstOrCreate(
                ['phone' => $person['phone']],
                array_merge($person, [
                    'password' => Hash::make('password'),
                    'status' => 1,
                    'owner_id' => $owner->id,
                    'region_id' => $region->id,
                    'governorate_id' => $governorate->id,
                    'port_id' => $port->id,
                ])
            );
        }
    }

    private function seedVendor(User $owner, Region $region, Governorate $governorate): void
    {
        User::firstOrCreate(
            ['phone' => '0500000021'],
            [
                'name' => 'سالم المورد',
                'company_name' => 'شركة الإمداد البحري',
                'email' => 'vendor1@example.com',
                'password' => Hash::make('password'),
                'role' => 'vendor',
                'status' => 1,
                'owner_id' => $owner->id,
                'region_id' => $region->id,
                'governorate_id' => $governorate->id,
                'tax_number' => '3009876543',
                'address' => 'المنطقة الصناعية',
                'bank_name' => 'البنك الأهلي',
                'account_number' => '1234567890',
                'IBAN' => 'SA0380000000608010167519',
            ]
        );
    }
}
