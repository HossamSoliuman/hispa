<?php

namespace Tests\Feature\Admin;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class AdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_settings_are_saved_from_the_admin_company_tab(): void
    {
        $this->withoutMiddleware();

        $response = $this->post(route('admin.settings.company'), [
            'title_en' => 'Hispa Platform',
            'title' => 'حسبة',
            'commercial_registration_no' => 'CR-123',
            'agri_record_no' => 'AG-456',
            'email' => 'admin@hispa.test',
            'phone' => '0590000000',
            'address' => 'Jeddah Port',
            'domain' => 'hispa.test',
        ]);

        $response->assertRedirect(route('admin.settings.index', ['tab' => 'company']));

        $settings = Setting::query()->pluck('value', 'key');

        $this->assertSame('Hispa Platform', $settings['title_en']);
        $this->assertSame('حسبة', $settings['title']);
        $this->assertSame('حسبة', $settings['site_name']);
        $this->assertSame('CR-123', $settings['commercial_registration_no']);
        $this->assertSame('AG-456', $settings['agri_record_no']);
        $this->assertSame('admin@hispa.test', $settings['email']);
        $this->assertSame('0590000000', $settings['phone']);
        $this->assertSame('Jeddah Port', $settings['address']);
        $this->assertSame('hispa.test', $settings['domain']);
    }

    public function test_admin_settings_page_no_longer_renders_the_general_settings_tab(): void
    {
        $settingsIndex = file_get_contents(resource_path('views/admin/settings/index.blade.php'));
        $companyTab = file_get_contents(resource_path('views/admin/settings/tabs/company.blade.php'));

        $this->assertStringNotContainsString('href="?tab=general"', $settingsIndex);
        $this->assertStringNotContainsString("include('admin.settings.tabs.general')", $settingsIndex);
        $this->assertStringNotContainsString("__('admin.settings.tabs.general')", $settingsIndex);
        $this->assertStringNotContainsString("__('admin.settings.tax_number')", $companyTab);
        $this->assertStringNotContainsString('name="tax_number"', $companyTab);
    }

    public function test_admin_sidebar_no_longer_renders_notifications_menu_item(): void
    {
        $html = Blade::render(file_get_contents(resource_path('views/admin/partial/sidebar.blade.php')));

        $this->assertStringNotContainsString('href="'.route('admin.notifications').'"', $html);
        $this->assertStringNotContainsString('bi bi-bell-fill', $html);
    }
}
