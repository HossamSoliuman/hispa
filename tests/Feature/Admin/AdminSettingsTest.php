<?php

namespace Tests\Feature\Admin;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Storage;
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

    public function test_bank_transfer_settings_are_saved_from_the_admin_payment_tab(): void
    {
        $this->withoutMiddleware();

        $response = $this->post(route('admin.settings.payment'), [
            'bank_name' => 'Al Rajhi Bank',
            'bank_account_name' => 'Hesba Platform',
            'bank_account_number' => 'SA0380000000608010167519',
            'payment_instructions' => 'Transfer then upload the receipt.',
        ]);

        $response->assertRedirect(route('admin.settings.index', ['tab' => 'payment']));

        $settings = Setting::query()->pluck('value', 'key');

        $this->assertSame('Al Rajhi Bank', $settings['bank_name']);
        $this->assertSame('Hesba Platform', $settings['bank_account_name']);
        $this->assertSame('SA0380000000608010167519', $settings['bank_account_number']);
        $this->assertSame('Transfer then upload the receipt.', $settings['payment_instructions']);
    }

    public function test_each_branding_asset_is_uploaded_to_its_own_setting(): void
    {
        $this->withoutMiddleware();
        Storage::fake('public');

        $response = $this->post(route('admin.settings.company'), [
            'title' => 'حسبة',
            'logo' => UploadedFile::fake()->image('main.png'),
            'logo_dark' => UploadedFile::fake()->image('dark.png'),
            'favicon' => UploadedFile::fake()->image('icon.png'),
        ]);

        $response->assertRedirect(route('admin.settings.index', ['tab' => 'company']));

        foreach (['logo', 'logo_dark', 'favicon'] as $key) {
            $setting = Setting::query()->where('key', $key)->first();

            $this->assertNotNull($setting, "missing setting: {$key}");
            $this->assertSame('image', $setting->type);
            Storage::disk('public')->assertExists($setting->getRawOriginal('value'));
        }

        $paths = Setting::query()->whereIn('key', ['logo', 'logo_dark', 'favicon'])
            ->get()
            ->map(fn (Setting $setting): string => $setting->getRawOriginal('value'));

        $this->assertCount(3, $paths->unique(), 'each asset must be stored separately');
    }

    public function test_a_branding_asset_can_be_cleared_back_to_the_shipped_default(): void
    {
        $this->withoutMiddleware();
        Storage::fake('public');

        $this->post(route('admin.settings.company'), [
            'logo' => UploadedFile::fake()->image('main.png'),
            'favicon' => UploadedFile::fake()->image('icon.png'),
        ]);

        $faviconPath = Setting::query()->where('key', 'favicon')->first()->getRawOriginal('value');
        $logoPath = Setting::query()->where('key', 'logo')->first()->getRawOriginal('value');

        $this->post(route('admin.settings.company'), ['remove_favicon' => '1']);

        $this->assertSame('', Setting::query()->where('key', 'favicon')->first()->getRawOriginal('value'));
        Storage::disk('public')->assertMissing($faviconPath);

        $this->assertSame($logoPath, Setting::query()->where('key', 'logo')->first()->getRawOriginal('value'));
        Storage::disk('public')->assertExists($logoPath);
    }

    public function test_replacing_a_branding_asset_deletes_the_previous_file(): void
    {
        $this->withoutMiddleware();
        Storage::fake('public');

        $this->post(route('admin.settings.company'), ['logo' => UploadedFile::fake()->image('first.png')]);
        $firstPath = Setting::query()->where('key', 'logo')->first()->getRawOriginal('value');

        $this->post(route('admin.settings.company'), ['logo' => UploadedFile::fake()->image('second.png')]);
        $secondPath = Setting::query()->where('key', 'logo')->first()->getRawOriginal('value');

        $this->assertNotSame($firstPath, $secondPath);
        Storage::disk('public')->assertMissing($firstPath);
        Storage::disk('public')->assertExists($secondPath);
    }

    public function test_saving_the_company_tab_without_files_keeps_the_existing_branding(): void
    {
        $this->withoutMiddleware();
        Storage::fake('public');

        $this->post(route('admin.settings.company'), ['logo' => UploadedFile::fake()->image('main.png')]);
        $logoPath = Setting::query()->where('key', 'logo')->first()->getRawOriginal('value');

        $this->post(route('admin.settings.company'), ['title' => 'حسبة حوات']);

        $this->assertSame($logoPath, Setting::query()->where('key', 'logo')->first()->getRawOriginal('value'));
        Storage::disk('public')->assertExists($logoPath);
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
