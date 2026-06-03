<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class GovPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $permissions = [

            // dashboard
            'read_dashboard',
            // analytics
            'read_analytics',
            // region_production
            'read_region_production',

            // seasons
            'create_seasons',
            'read_seasons',
            'update_seasons',
            'delete_seasons',

            // read_calendar_seasons
            'read_calendar_seasons',

            // fish_report
            'read_fish_report',
            // read_captains
            'read_captains',

            // read_fishing_equipment
            'read_fishing_equipment',

            // read_sales_report
            'read_sales_report',
            // read_stock_report
            'read_stock_report',
            // read_trip_report

            // read_trip_report
            'read_trip_report',

            // violations
            'create_violations',
            'read_violations',
            'update_violations',
            'delete_violations',

            // ports
            'read_ports',
            'read_ports_gov',
            'read_ports_private',

            // create gov
            'create_gov',
            'read_gov',
            'update_gov',
            'delete_gov',

            // create roles
            'create_roles',
            'read_roles',
            'update_roles',
            'delete_roles',
            //
        ];

        $role = Role::firstOrCreate(['guard_name' => 'gov', 'name' => 'super']);

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['guard_name' => 'gov', 'name' => $permission]);
        }

        // ✅ Assign all permissions to the 'super' role
        $role->syncPermissions($permissions);
    }
}
