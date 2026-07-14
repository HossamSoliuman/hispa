<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Tests\TestCase;

class CategoryPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_categories_page_uses_the_admin_layout_and_endpoints(): void
    {
        $admin = Admin::query()->create([
            'name' => 'Category Administrator',
            'email' => 'categories-admin@example.test',
            'password' => Hash::make('password'),
            'status' => 1,
            'roles_name' => [],
        ]);

        $response = $this->withoutMiddleware([
            LocaleSessionRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ])->actingAs($admin, 'admin')
            ->get(route('admin.categories.index'));

        $response->assertOk();
        $response->assertSee('href="'.route('admin.dashboard').'"', false);
        $response->assertSee('href="'.route('admin.categories.index').'"', false);
        $response->assertSee(route('admin.getCategoriesData'), false);
        $response->assertSee(route('admin.categories.store'), false);
        $response->assertDontSee('href="'.route('owner.dashboard').'"', false);
        $response->assertDontSee(route('owner.getCategoriesData'), false);
    }
}
