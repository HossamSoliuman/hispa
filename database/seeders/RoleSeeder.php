<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only 'owner' is enforced by route middleware (routes/web.php & routes/owner.php → role:owner).
        // captain/counter/dalal are identified by users.role string column only.
        // gov uses its own guard; its 'super' role is created by GovPermissionSeeder.
        Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);

        Role::where('guard_name', 'web')
            ->where('name', '!=', 'owner')
            ->delete();
    }
}
