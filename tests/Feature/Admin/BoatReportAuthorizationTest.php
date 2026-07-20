<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BoatReportAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_admin_can_access_the_boat_report(): void
    {
        $this->seed(PermissionSeeder::class);

        $admin = Admin::query()->create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'status' => true,
            'roles_name' => ['owner'],
        ]);
        $admin->assignRole(Role::findByName('owner', 'admin'));

        $this->withoutMiddleware([
            LocaleSessionRedirect::class,
            LaravelLocalizationRedirectFilter::class,
            LaravelLocalizationViewPath::class,
        ])->actingAs($admin, 'admin')
            ->get(route('admin.boat-report'))
            ->assertOk();
    }
}
