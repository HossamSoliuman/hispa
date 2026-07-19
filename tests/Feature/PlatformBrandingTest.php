<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Tests\TestCase;

class PlatformBrandingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->withoutMiddleware([
            LaravelLocalizationRedirectFilter::class,
            LaravelLocalizationViewPath::class,
            LocaleSessionRedirect::class,
        ]);
    }

    public function test_platform_navigation_uses_the_uploaded_admin_logo(): void
    {
        $logoPath = 'uploads/settings/platform-logo.png';
        Setting::query()->create([
            'key' => 'logo',
            'value' => $logoPath,
            'type' => 'image',
        ]);
        $logoUrl = asset(Storage::url($logoPath));

        $publicHeader = view('site.partials.header')->render();
        $ownerLogin = $this->get(route('frontend.show_login_form'));
        $ownerRegister = $this->get(route('frontend.show_register_form'));
        $adminLogin = $this->get(route('admin.show_login_form'));
        $admin = Admin::query()->create([
            'name' => 'Administrator',
            'email' => 'admin@example.test',
            'password' => 'password',
            'roles_name' => [],
        ]);
        $this->actingAs($admin, 'admin');
        $adminHeader = view('admin.partial.header')->render();
        $owner = User::factory()->create(['role' => 'owner']);
        $this->actingAs($owner, 'owner');
        $ownerHeader = view('owner.partial.header')->render();

        $ownerLogin->assertOk();
        $ownerRegister->assertOk();
        $adminLogin->assertOk();
        $this->assertStringContainsString($logoUrl, $publicHeader);
        $this->assertStringContainsString($logoUrl, $adminHeader);
        $this->assertStringContainsString($logoUrl, $ownerHeader);
        $ownerLogin->assertSee($logoUrl, false);
        $ownerRegister->assertSee($logoUrl, false);
        $adminLogin->assertSee($logoUrl, false);
    }

    public function test_owner_navigation_does_not_use_the_owner_company_logo(): void
    {
        $ownerHeader = file_get_contents(resource_path('views/owner/partial/header.blade.php'));

        $this->assertIsString($ownerHeader);
        $this->assertStringContainsString('$platformLogoOnDarkUrl', $ownerHeader);
        $this->assertStringNotContainsString('ownerCompanySettings', $ownerHeader);
        $this->assertStringNotContainsString('currentCompany', $ownerHeader);
    }

    public function test_current_layouts_use_the_hard_coded_new_brand_favicon(): void
    {
        $faviconReference = "asset('site/assets/hisbah-huwat-logo.png')";
        $layoutPaths = [
            resource_path('views/site/layouts/app.blade.php'),
            resource_path('views/site/layouts/auth.blade.php'),
            resource_path('views/admin/layouts/master.blade.php'),
            resource_path('views/admin/layouts/master-auth.blade.php'),
            resource_path('views/owner/layouts/master.blade.php'),
            resource_path('views/owner/layouts/master-auth.blade.php'),
        ];

        foreach ($layoutPaths as $layoutPath) {
            $layout = file_get_contents($layoutPath);

            $this->assertIsString($layout);
            $this->assertStringContainsString($faviconReference, $layout);
            $this->assertStringNotContainsString('storage/uploads/favicon.ico', $layout);
        }
    }

    public function test_dashboard_headers_do_not_recolor_uploaded_logos(): void
    {
        $dashboardLayoutPaths = [
            resource_path('views/admin/layouts/master.blade.php'),
            resource_path('views/owner/layouts/master.blade.php'),
        ];

        foreach ($dashboardLayoutPaths as $layoutPath) {
            $layout = file_get_contents($layoutPath);

            $this->assertIsString($layout);
            $this->assertStringNotContainsString('filter: brightness(0) invert(1)', $layout);
        }
    }
}
