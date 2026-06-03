<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $crudResources = [
            'admins',
            'boats',
            'boat_types',
            'captain',
            'categories',
            'cities',
            'commission_settings',
            'contacts',
            'counter',
            'crews',
            'customers',
            'dalal',
            'fish',
            'gov',
            'governorates',
            'gov-roles',
            'notifications',
            'owner',
            'pages',
            'payment_methods',
            'ports',
            'regions',
            'roles',
            'sales',
            'settings',
            'trips',
            'user_request',
        ];

        $permissions = [];

        foreach ($crudResources as $resource) {
            foreach (['create', 'read', 'update', 'delete'] as $action) {
                $permissions[] = sprintf('%s_%s', $action, $resource);
            }
        }

        $permissions = array_merge($permissions, [
            'read_dashboard',
            'read_statistics',
            'read_stocks',
            'read_owner-stock',
            'read_dalal-stock',
            'read_dalal_stock_report',
            'read_stock_report',
            'read_fish_stock_history_report',
            'read_trip_report',
            'read_sales_report',
        ]);

        $permissions = array_values(array_unique($permissions));

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'guard_name' => 'admin',
                'name' => $permission,
            ]);
        }

        $adminRole = Role::firstOrCreate([
            'name' => 'owner',
            'guard_name' => 'admin',
        ]);

        $adminRole->syncPermissions(Permission::where('guard_name', 'admin')->get());
    }
}
