<?php

namespace Tests\Feature\Owner;

use App\Models\Company;
use App\Models\Scopes\OwnerScope;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CompanyTenancyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()['cache']->forget('spatie.permission.cache');
        Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
    }

    private function makeOwner(): User
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $owner->assignRole('owner');

        return $owner;
    }

    public function test_current_company_is_created_and_scoped_to_the_authenticated_owner(): void
    {
        $owner = $this->makeOwner();
        $this->actingAs($owner, 'owner');

        $company = currentCompany();

        $this->assertInstanceOf(Company::class, $company);
        $this->assertSame($owner->id, $company->owner_id);

        // Subsequent calls return the same row rather than creating duplicates.
        $this->assertSame($company->id, currentCompany()->id);
        $this->assertSame(1, Company::withoutGlobalScope(OwnerScope::class)->where('owner_id', $owner->id)->count());
    }

    public function test_each_owner_only_sees_their_own_company(): void
    {
        $ownerA = $this->makeOwner();
        $ownerB = $this->makeOwner();

        $this->actingAs($ownerA, 'owner');
        currentCompany()->update(['name_ar' => 'شركة أ', 'name_en' => 'Company A']);

        $this->actingAs($ownerB, 'owner');
        currentCompany()->update(['name_ar' => 'شركة ب', 'name_en' => 'Company B']);

        $this->actingAs($ownerA, 'owner');
        $this->assertSame('Company A', currentCompany()->name_en);

        $this->actingAs($ownerB, 'owner');
        $this->assertSame('Company B', currentCompany()->name_en);
    }

    public function test_owner_company_settings_reflect_the_owner_company(): void
    {
        app()->setLocale('en');
        $owner = $this->makeOwner();
        $this->actingAs($owner, 'owner');

        currentCompany()->update([
            'name_en' => 'Sea Catch Co.',
            'address' => '123 Harbour Rd',
            'phone' => '0590000000',
            'email' => 'info@seacatch.test',
            'cr_number' => 'CR-1',
        ]);

        $settings = ownerCompanySettings(['qr_code' => 'X']);

        $this->assertSame('Sea Catch Co.', $settings['title']);
        $this->assertSame('123 Harbour Rd', $settings['address']);
        $this->assertSame('info@seacatch.test', $settings['email']);
        $this->assertSame('CR-1', $settings['cr_number']);
        $this->assertSame('X', $settings['qr_code']);
    }

    public function test_update_company_persists_fields_and_logo_for_the_owner(): void
    {
        Storage::fake('public');
        $owner = $this->makeOwner();

        $response = $this->actingAs($owner, 'owner')->post(route('owner.settings.company'), [
            'name_en' => 'Fish House',
            'name_ar' => 'بيت السمك',
            'cr_number' => '4603007827',
            'record_number' => 'AG-9',
            'email' => 'owner@fishhouse.test',
            'phone' => '0595233393',
            'address' => 'Al Qunfudah',
            'website' => 'www.fishhouse.test',
            'logo' => UploadedFile::fake()->image('logo.png'),
        ]);

        $response->assertRedirect();

        $company = Company::withoutGlobalScope(OwnerScope::class)->where('owner_id', $owner->id)->firstOrFail();

        $this->assertSame('Fish House', $company->name_en);
        $this->assertSame('بيت السمك', $company->name_ar);
        $this->assertSame('4603007827', $company->cr_number);
        $this->assertSame('AG-9', $company->record_number);
        $this->assertNotNull($company->logo);
        Storage::disk('public')->assertExists($company->logo);
    }
}
