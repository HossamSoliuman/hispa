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
        $this->call(GovPermissionSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(PermitTypeSeeder::class);
        $this->call(PageSeeder::class);

        // Per-owner master data (ports, regions, fish, units, payment methods,
        // boat types, expense categories). Each owner gets an isolated copy.
        $this->call(OwnerMasterDataSeeder::class);

        // Minimal working setup (boat, vendor, crew people) with no financial
        // data — a clean starting point for testing the financial workflow.
        // $this->call(BasicWorkflowSeeder::class);

        // Demo finance & trip data — disabled so a fresh seed leaves only the
        // reference data (ports, cities, fish, categories, payment methods, users).
        // Re-enable these to restore the demo dataset.
        // $this->call(DemoDataSeeder::class);
        // $this->call(AccountingErrorsDemoSeeder::class);
    }
}
