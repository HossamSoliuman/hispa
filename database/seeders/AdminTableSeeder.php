<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::firstOrCreate([
            'name' => 'owner',
            'guard_name' => 'admin',
        ]);

        $admin = Admin::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Super Admin',
                'status' => '1',
                'roles_name' => [$role->name],
                'password' => Hash::make('123456'),
            ]
        );

        $admin->syncRoles([$role->name]);
    }
}
