<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('users')->truncate();
        Schema::enableForeignKeyConstraints();

        $owner = User::create([
            'name' => 'Owner User',
            'phone' => '0500000001',
            'email' => 'owner@example.com',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'status' => 1,
        ]);

        $captain = User::create([
            'name' => 'Captain User',
            'phone' => '0500000002',
            'email' => 'captain@example.com',
            'password' => Hash::make('password'),
            'role' => 'captain',
            'status' => 1,
            'owner_id' => $owner->id,
            'id_number' => 'CPT123456',
            'nationality' => 'Saudi',
            'boat_name' => 'Al Bahar',
            'boat_number' => 'BOAT987',
            'crew_count' => 5,
        ]);

        $counter = User::create([
            'name' => 'Counter User',
            'phone' => '0500000003',
            'email' => 'counter@example.com',
            'password' => Hash::make('password'),
            'role' => 'counter',
            'status' => 1,
        ]);

        $dalal = User::create([
            'name' => 'Dalal User',
            'phone' => '0500000004',
            'email' => 'dalal@example.com',
            'password' => Hash::make('password'),
            'role' => 'dalal',
            'status' => 1,
        ]);

        $gov = User::create([
            'name' => 'Gov User',
            'phone' => '0500000005',
            'email' => 'gov@example.com',
            'password' => Hash::make('password'),
            'role' => 'gov',
            'status' => 1,
        ]);

        $owner->assignRole('owner');
        $captain->assignRole('captain');
        $counter->assignRole('counter');
        $dalal->assignRole('dalal');

        // ✅ Assign 'super' role from 'gov' guard (which has all gov permissions)
        $govSuperRole = Role::where('guard_name', 'gov')->where('name', 'super')->first();
        if ($govSuperRole) {
            $gov->assignRole($govSuperRole);
        }
    }
}
