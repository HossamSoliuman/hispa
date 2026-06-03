<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('model_has_roles')->where('model_type', User::class)->delete();
        DB::table('users')->truncate();
        Schema::enableForeignKeyConstraints();

        // Actual login roles from routes:
        //   admin  → Auth::guard('admin')  → /admin/*   (seeded in AdminTableSeeder)
        //   owner  → Auth::guard('owner')  → /owner/*   (seeded here)
        //
        // captain/counter/dalal/gov are data columns (users.role) managed within
        // the owner panel — they have no dedicated route panel of their own.
        $owner = User::create([
            'name' => 'Owner User',
            'email' => 'owner@example.com',
            'phone' => '0500000001',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'status' => 1,
        ]);

        $owner->assignRole('owner');
    }
}
