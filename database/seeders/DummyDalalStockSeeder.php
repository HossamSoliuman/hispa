<?php

namespace Database\Seeders;

use App\Models\DalalStock;
use App\Models\DalalStockDetail;
use App\Models\Fish;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyDalalStockSeeder extends Seeder
{
    public function run()
    {
        // Create or get a dalal user (by email or phone to avoid unique constraint errors)
        $dalal = User::where('email', 'dalal@example.com')
            ->orWhere('phone', '0500000001')
            ->first();

        if (! $dalal) {
            $dalal = User::create([
                'name' => 'Dummy Dalal',
                'email' => 'dalal@example.com',
                'phone' => '0500000001',
                'role' => 'dalal',
                'password' => Hash::make('password'),
            ]);
        } else {
            // Don't overwrite existing email to avoid unique constraint conflicts.
            $dalal->update([
                'role' => 'dalal',
                'name' => $dalal->name ?: 'Dummy Dalal',
            ]);
        }

        // Create or get an owner user (by email or phone)
        $owner = User::where('email', 'owner@example.test')
            ->orWhere('phone', '0500000002')
            ->first();

        if (! $owner) {
            $owner = User::create([
                'name' => 'Dummy Owner',
                'email' => 'owner@example.test',
                'phone' => '0500000002',
                'role' => 'owner',
                'password' => Hash::make('password'),
            ]);
        } else {
            // Avoid changing email to prevent duplicate email errors
            $owner->update([
                'role' => 'owner',
                'name' => $owner->name ?: 'Dummy Owner',
            ]);
        }

        // Create or get a fish
        $fish = Fish::firstOrCreate(
            ['scientific_name' => 'Testus fishus'],
            [
                'english_name' => 'Test Fish',
                'local_name_primary' => 'سمكة تجريبية',
                'status' => 1,
            ]
        );

        // Create dalal stock (without trip)
        // Use withoutEvents to avoid firing observers that expect an authenticated user
        $dalalStock = DalalStock::withoutEvents(function () use ($owner, $dalal) {
            return DalalStock::create([
                'owner_id' => $owner->id,
                'dalal_id' => $dalal->id,
                'trip_id' => null,
                'status' => 1,
                'total_weight' => 120.50,
            ]);
        });

        // Create stock details
        DalalStockDetail::create([
            'dalal_stock_id' => $dalalStock->id,
            'fish_id' => $fish->id,
            'fish_name' => $fish->scientific_name,
            'weight' => 80.25,
            'quantity' => 10,
        ]);

        DalalStockDetail::create([
            'dalal_stock_id' => $dalalStock->id,
            'fish_id' => $fish->id,
            'fish_name' => $fish->scientific_name,
            'weight' => 40.25,
            'quantity' => 5,
        ]);

        $this->command->info('Dummy dalal stock inserted. Dalal login: dalal@example.test / password');
    }
}
