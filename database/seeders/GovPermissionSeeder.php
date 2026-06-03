<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class GovPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'read_dashboard',
            'read_analytics',
            'read_region_production',

            'create_seasons',
            'read_seasons',
            'update_seasons',
            'delete_seasons',
            'read_calendar_seasons',

            'read_fish_report',
            'read_captains',
            'read_fishing_equipment',
            'read_sales_report',
            'read_stock_report',
            'read_trip_report',

            'create_violations',
            'read_violations',
            'update_violations',
            'delete_violations',

            'read_ports',
            'read_ports_gov',
            'read_ports_private',

            'create_gov',
            'read_gov',
            'update_gov',
            'delete_gov',

            'create_roles',
            'read_roles',
            'update_roles',
            'delete_roles',
        ];

        $role = Role::firstOrCreate(['guard_name' => 'gov', 'name' => 'super']);

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['guard_name' => 'gov', 'name' => $permission]);
        }

        // ✅ Assign all permissions to the 'super' role
        $role->syncPermissions($permissions);
    }
}
