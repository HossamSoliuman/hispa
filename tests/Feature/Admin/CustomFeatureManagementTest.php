<?php

namespace Tests\Feature\Admin;

use App\Enums\CustomFeature;
use App\Models\Admin;
use App\Models\CustomFeatureAccess;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CustomFeatureManagementTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        app()->setLocale('en');
        app()['cache']->forget('spatie.permission.cache');
        Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        ]);

        $this->admin = Admin::query()->create([
            'name' => 'Feature Administrator',
            'email' => 'features-admin@example.test',
            'password' => Hash::make('password'),
            'status' => 1,
            'roles_name' => [],
        ]);
        $this->actingAs($this->admin, 'admin');
    }

    public function test_admin_can_open_the_feature_card_and_grant_access_by_owner_email(): void
    {
        $owner = $this->createOwner('owner@example.test');

        $this->get(route('admin.custom-features.index'))
            ->assertOk()
            ->assertSee('Custom Features')
            ->assertSee('Startup Management');

        $this->get(route('admin.custom-features.show', CustomFeature::BusinessStartup))
            ->assertOk()
            ->assertSee('No owners have access yet');

        $this->post(route('admin.custom-features.access.store', CustomFeature::BusinessStartup), [
            'email' => ' OWNER@EXAMPLE.TEST ',
        ])->assertRedirect();

        $this->assertDatabaseHas('custom_feature_accesses', [
            'user_id' => $owner->id,
            'feature' => CustomFeature::BusinessStartup->value,
            'status' => 'active',
            'granted_by_admin_id' => $this->admin->id,
        ]);

        $this->actingAs($owner, 'owner');
        $this->get(route('owner.dashboard'))
            ->assertOk()
            ->assertSee('Business Startup');
        $this->get(route('owner.startup.projects.index'))->assertOk();
    }

    public function test_admin_can_pause_resume_and_delete_an_owner_feature_access(): void
    {
        $owner = $this->createOwner('controlled-owner@example.test');
        $access = CustomFeatureAccess::factory()->create([
            'user_id' => $owner->id,
            'feature' => CustomFeature::BusinessStartup,
        ]);

        $this->patch(route('admin.custom-features.access.pause', [CustomFeature::BusinessStartup, $access]))
            ->assertRedirect();

        $this->assertDatabaseHas('custom_feature_accesses', [
            'id' => $access->id,
            'status' => 'paused',
        ]);

        $this->actingAs($owner, 'owner');
        $this->get(route('owner.dashboard'))->assertDontSee('Business Startup');
        $this->get(route('owner.startup.projects.index'))->assertNotFound();

        $this->actingAs($this->admin, 'admin');
        $this->patch(route('admin.custom-features.access.resume', [CustomFeature::BusinessStartup, $access]))
            ->assertRedirect();

        $this->actingAs($owner, 'owner');
        $this->get(route('owner.startup.projects.index'))->assertOk();

        $this->actingAs($this->admin, 'admin');
        $this->delete(route('admin.custom-features.access.destroy', [CustomFeature::BusinessStartup, $access]))
            ->assertRedirect();

        $this->assertDatabaseMissing('custom_feature_accesses', ['id' => $access->id]);

        $this->actingAs($owner, 'owner');
        $this->get(route('owner.startup.projects.index'))->assertNotFound();
    }

    public function test_grant_validation_accepts_only_an_existing_owner_email(): void
    {
        User::factory()->create([
            'role' => 'captain',
            'email' => 'captain@example.test',
        ]);

        $this->post(route('admin.custom-features.access.store', CustomFeature::BusinessStartup), [
            'email' => 'missing@example.test',
        ])->assertInvalid(['email']);

        $this->post(route('admin.custom-features.access.store', CustomFeature::BusinessStartup), [
            'email' => 'captain@example.test',
        ])->assertInvalid(['email']);

        $this->assertDatabaseCount('custom_feature_accesses', 0);
    }

    public function test_quick_search_returns_available_owners_and_excludes_existing_access(): void
    {
        $availableOwner = $this->createOwner('available-owner@example.test', 'Available Harbour Owner');
        $grantedOwner = $this->createOwner('granted-owner@example.test', 'Granted Harbour Owner');
        CustomFeatureAccess::factory()->create([
            'user_id' => $grantedOwner->id,
            'feature' => CustomFeature::BusinessStartup,
        ]);

        $this->getJson(route('admin.custom-features.owners.search', [
            'feature' => CustomFeature::BusinessStartup,
            'query' => 'Harbour',
        ]))
            ->assertOk()
            ->assertJsonPath('owners.0.id', $availableOwner->id)
            ->assertJsonMissing(['email' => $grantedOwner->email]);
    }

    public function test_custom_feature_admin_ui_uses_neutral_icons_and_transparent_cards(): void
    {
        $layout = file_get_contents(resource_path('views/admin/layouts/master.blade.php'));
        $featureViews = file_get_contents(resource_path('views/admin/custom-features/index.blade.php'))
            .file_get_contents(resource_path('views/admin/custom-features/show.blade.php'));

        $this->assertStringContainsString('background: transparent !important;', $layout);
        $this->assertStringContainsString('bi bi-diagram-3', $featureViews);
        $this->assertStringNotContainsString('bi-rocket', $featureViews);
    }

    private function createOwner(string $email, string $name = 'Startup Owner'): User
    {
        $owner = User::factory()->create([
            'name' => $name,
            'role' => 'owner',
            'email' => $email,
            'status' => 1,
        ]);
        $owner->assignRole('owner');

        return $owner;
    }
}
