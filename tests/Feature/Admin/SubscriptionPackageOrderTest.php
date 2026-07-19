<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\SubscriptionPackage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Tests\TestCase;

class SubscriptionPackageOrderTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->withoutMiddleware([
            LaravelLocalizationRedirectFilter::class,
            LaravelLocalizationViewPath::class,
            LocaleSessionRedirect::class,
        ]);

        $this->admin = Admin::query()->create([
            'name' => 'Plans Administrator',
            'email' => 'plans-admin@example.test',
            'password' => Hash::make('password'),
            'status' => 1,
            'roles_name' => [],
        ]);
    }

    public function test_admin_can_set_and_update_a_package_public_order(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.subscription-packages.store'), $this->packageData([
                'name_en' => 'Fleet',
                'sort_order' => 7,
            ]));

        $response->assertRedirect(route('admin.subscription-packages.index'));

        $package = SubscriptionPackage::query()->where('name_en', 'Fleet')->firstOrFail();

        $this->assertSame(7, $package->sort_order);

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.subscription-packages.update', $package), $this->packageData([
                'name_en' => 'Fleet',
                'sort_order' => 2,
            ]));

        $response->assertRedirect(route('admin.subscription-packages.index'));
        $this->assertDatabaseHas('subscription_packages', [
            'id' => $package->id,
            'sort_order' => 2,
        ]);
    }

    public function test_new_package_form_suggests_the_next_public_order(): void
    {
        $this->createPackage(['sort_order' => 4]);
        $this->createPackage(['sort_order' => 9]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.subscription-packages.create'));

        $response->assertOk();
        $response->assertSee('name="sort_order"', false);
        $response->assertSee('value="10"', false);
        $response->assertSee(__('admin.subscription_packages.sort_order_hint'));
    }

    public function test_package_public_order_must_be_a_non_negative_integer(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->from(route('admin.subscription-packages.create'))
            ->post(route('admin.subscription-packages.store'), $this->packageData([
                'sort_order' => -1,
            ]));

        $response->assertRedirect(route('admin.subscription-packages.create'));
        $response->assertSessionHasErrors('sort_order');
        $this->assertDatabaseCount('subscription_packages', 0);
    }

    public function test_public_pricing_page_displays_packages_in_admin_order(): void
    {
        app()->setLocale('en');

        $this->createPackage(['name_en' => 'Third Plan', 'sort_order' => 30]);
        $this->createPackage(['name_en' => 'First Plan', 'sort_order' => 10]);
        $this->createPackage(['name_en' => 'Second Plan', 'sort_order' => 20]);

        $response = $this->get(route('site.pricing'));

        $response->assertOk();
        $response->assertSeeTextInOrder([
            'First Plan',
            'Second Plan',
            'Third Plan',
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    private function packageData(array $attributes = []): array
    {
        return array_merge([
            'name_ar' => 'باقة',
            'name_en' => 'Plan',
            'boats_count' => 2,
            'original_price' => 2500,
            'price' => '',
            'duration_type' => 'yearly',
            'is_active' => 1,
            'is_featured' => 0,
            'sort_order' => 1,
        ], $attributes);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createPackage(array $attributes = []): SubscriptionPackage
    {
        return SubscriptionPackage::query()->create(array_merge([
            'name_ar' => 'باقة',
            'name_en' => 'Plan',
            'boats_count' => 2,
            'original_price' => 2500,
            'price' => null,
            'duration_type' => 'yearly',
            'is_active' => true,
            'is_featured' => false,
            'sort_order' => 1,
        ], $attributes));
    }
}
