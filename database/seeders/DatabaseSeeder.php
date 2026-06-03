<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AdminTableSeeder::class);
        $this->call(RoleSeeder::class);
        // ✅ Run GovPermissionSeeder BEFORE UserSeeder to ensure 'super' role exists
        $this->call(GovPermissionSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(PermitTypeSeeder::class);
        $this->call(PageSeeder::class);
        $this->call(LocationSeeder::class);
        $this->call(DefaultMasterSeeder::class);

        // Uncomment below to seed comprehensive test data
        // $this->call(ComprehensiveTestSeeder::class);
    }
}
