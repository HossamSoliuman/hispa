<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $permissions = [
        //     // الصلاحيات القديمة كلها
        //     'read_dashboard',
        //     'read_statistics',
        //     'read_stocks',
        //     'read_owner-stock',
        //     'read_dalal-stock',
        //     'create_trips',
        //     'read_trips',
        //     'update_trips',
        //     'delete_trips',
        //     'read_sales',
        //     'read_fish_stock_history_report',
        //     'read_trip_report',
        //     'read_dalal_stock_report',
        //     'read_stock_report',
        //     'read_sales_report',
        //     'read_user_request',
        //     'update_user_request',
        //     'create_owner',
        //     'read_owner',
        //     'update_owner',
        //     'delete_owner',
        //     'create_captain',
        //     'read_captain',
        //     'update_captain',
        //     'delete_captain',
        //     'create_counter',
        //     'read_counter',
        //     'update_counter',
        //     'delete_counter',
        //     'create_commission_settings',
        //     'read_commission_settings',
        //     'update_commission_settings',
        //     'delete_commission_settings',
        //     'create_dalal',
        //     'read_dalal',
        //     'update_dalal',
        //     'delete_dalal',
        //     'create_fish',
        //     'read_fish',
        //     'update_fish',
        //     'delete_fish',
        //     'create_payment_methods',
        //     'read_payment_methods',
        //     'update_payment_methods',
        //     'delete_payment_methods',
        //     'create_customers',
        //     'read_customers',
        //     'update_customers',
        //     'delete_customers',
        //     'create_contacts',
        //     'read_contacts',
        //     'update_contacts',
        //     'delete_contacts',
        //     'create_gov',
        //     'read_gov',
        //     'update_gov',
        //     'delete_gov',
        //     'create_ports',
        //     'read_ports',
        //     'update_ports',
        //     'delete_ports',
        //     'create_cities',
        //     'read_cities',
        //     'update_cities',
        //     'delete_cities',
        //     'create_governorates',
        //     'read_governorates',
        //     'update_governorates',
        //     'delete_governorates',
        //     'create_regions',
        //     'read_regions',
        //     'update_regions',
        //     'delete_regions',
        //     'create_pages',
        //     'read_pages',
        //     'update_pages',
        //     'delete_pages',
        //     'create_settings',
        //     'read_settings',
        //     'update_settings',
        //     'delete_settings',
        //     'create_notifications',
        //     'read_notifications',
        //     'create_admins',
        //     'read_admins',
        //     'update_admins',
        //     'delete_admins',
        //     'create_roles',
        //     'read_roles',
        //     'update_roles',
        //     'delete_roles',
        //     'create_boats',
        //     'read_boats',
        //     'update_boats',
        //     'delete_boats',
        //     'create_crews',
        //     'read_crews',
        //     'update_crews',
        //     'delete_crews',

        //     // الصلاحيات الجديدة
        //     'create_boat_types',
        //     'read_boat_types',
        //     'update_boat_types',
        //     'delete_boat_types',
        //     'create_gov-roles',
        //     'read_gov-roles',
        //     'update_gov-roles',
        //     'delete_gov-roles',
        // ];

        // $adminRole = Role::firstWhere('guard_name', 'admin');

        // foreach ($permissions as $permission) {
        //     Permission::firstOrCreate([
        //         'guard_name' => 'admin',
        //         'name' => $permission
        //     ]);
        //     $adminRole->givePermissionTo($permission);
        // }

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
