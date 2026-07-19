<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlatformBrandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_platform_navigation_uses_the_uploaded_admin_logo(): void
    {
        $settings = ['logo' => 'storage/uploads/settings/platform-logo.png'];
        $logoUrl = asset($settings['logo']);

        $siteLogoUrl = $logoUrl;
        $publicHeader = view('site.partials.header', compact('siteLogoUrl'))->render();
        $admin = Admin::query()->create([
            'name' => 'Administrator',
            'email' => 'admin@example.test',
            'password' => 'password',
        ]);
        $this->actingAs($admin, 'admin');
        $adminHeader = view('admin.partial.header', compact('settings'))->render();
        $owner = User::factory()->create();
        $this->actingAs($owner, 'owner');
        $ownerHeader = view('owner.partial.header', compact('settings'))->render();

        $this->assertStringContainsString($logoUrl, $publicHeader);
        $this->assertStringContainsString($logoUrl, $adminHeader);
        $this->assertStringContainsString($logoUrl, $ownerHeader);
    }

    public function test_owner_navigation_does_not_use_the_owner_company_logo(): void
    {
        $ownerHeader = file_get_contents(resource_path('views/owner/partial/header.blade.php'));

        $this->assertIsString($ownerHeader);
        $this->assertStringContainsString("\$settings['logo']", $ownerHeader);
        $this->assertStringNotContainsString('ownerCompanySettings', $ownerHeader);
        $this->assertStringNotContainsString('currentCompany', $ownerHeader);
    }
}
