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

    public function test_current_layouts_use_the_managed_favicon(): void
    {
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
            $this->assertStringContainsString('$platformFaviconUrl', $layout);
            $this->assertStringContainsString('$platformAppleTouchIconUrl', $layout);
            $this->assertStringNotContainsString('storage/uploads/favicon.ico', $layout);
            $this->assertStringNotContainsString('rel="icon" href="{{ asset(', $layout);
        }
    }

    public function test_branding_falls_back_to_the_shipped_defaults(): void
    {
        $branding = $this->brandingFor(view('site.partials.header'));

        $this->assertSame(asset('site/assets/hisbah-huwat-logo.png'), $branding['platformLogoUrl']);
        $this->assertSame(asset('site/assets/hisbah-huwat-logo-white.png'), $branding['platformLogoOnDarkUrl']);
        $this->assertSame(asset('site/assets/hisbah-huwat-favicon.png'), $branding['platformFaviconUrl']);
        $this->assertSame(asset('site/assets/hisbah-huwat-apple-touch-icon.png'), $branding['platformAppleTouchIconUrl']);
        $this->assertSame(config('seo.default_image_path'), $branding['platformLogoSeoPath']);

        foreach (['logo', 'logo-white', 'favicon', 'apple-touch-icon'] as $asset) {
            $this->assertFileExists(public_path("site/assets/hisbah-huwat-{$asset}.png"));
        }
    }

    public function test_each_branding_asset_is_resolved_from_its_own_setting(): void
    {
        $this->createImageSetting('logo', 'uploads/settings/main.png');
        $this->createImageSetting('logo_dark', 'uploads/settings/dark.png');
        $this->createImageSetting('favicon', 'uploads/settings/icon.png');

        $branding = $this->brandingFor(view('site.partials.header'));

        $this->assertSame(asset(Storage::url('uploads/settings/main.png')), $branding['platformLogoUrl']);
        $this->assertSame(asset(Storage::url('uploads/settings/dark.png')), $branding['platformLogoOnDarkUrl']);
        $this->assertSame(asset(Storage::url('uploads/settings/icon.png')), $branding['platformFaviconUrl']);
        $this->assertSame(asset(Storage::url('uploads/settings/icon.png')), $branding['platformAppleTouchIconUrl']);
        $this->assertSame(Storage::url('uploads/settings/main.png'), $branding['platformLogoSeoPath']);
    }

    public function test_the_dark_logo_falls_back_to_the_main_logo_when_not_uploaded(): void
    {
        $this->createImageSetting('logo', 'uploads/settings/main.png');

        $branding = $this->brandingFor(view('site.partials.header'));

        $this->assertSame(asset(Storage::url('uploads/settings/main.png')), $branding['platformLogoOnDarkUrl']);
        $this->assertSame(asset('site/assets/hisbah-huwat-favicon.png'), $branding['platformFaviconUrl']);
    }

    public function test_the_favicon_is_independent_of_the_logo(): void
    {
        $this->createImageSetting('favicon', 'uploads/settings/icon.png');

        $branding = $this->brandingFor(view('site.partials.header'));

        $this->assertSame(asset('site/assets/hisbah-huwat-logo.png'), $branding['platformLogoUrl']);
        $this->assertSame(asset(Storage::url('uploads/settings/icon.png')), $branding['platformFaviconUrl']);
    }

    private function createImageSetting(string $key, string $path): void
    {
        Setting::query()->create(['key' => $key, 'value' => $path, 'type' => 'image']);
    }

    /**
     * Render a view and read back the branding variables the composer shared with it.
     *
     * @return array<string, string>
     */
    private function brandingFor(\Illuminate\Contracts\View\View $view): array
    {
        $view->render();

        return array_intersect_key($view->getData(), array_flip([
            'platformLogoUrl',
            'platformLogoOnDarkUrl',
            'platformFaviconUrl',
            'platformAppleTouchIconUrl',
            'platformLogoSeoPath',
        ]));
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
