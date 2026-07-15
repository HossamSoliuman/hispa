<?php

namespace Tests\Feature;

use App\Models\SubscriptionPackage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Tests\TestCase;

class PublicSubscriptionExperienceTest extends TestCase
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

    public function test_homepage_focuses_on_real_owner_features_and_live_pricing(): void
    {
        $package = $this->createPackage();

        $response = $this->get(route('landing-page'));

        $response->assertOk();
        $response->assertSee(__('marketing.hero.title'));
        $response->assertSee(__('marketing.features.trip_title'));
        $response->assertSee(__('marketing.features.reports_title'));
        $response->assertSee($package->name);
        $response->assertSee('2,500');
        $response->assertSee(route('site.order-review', ['package_id' => $package->id]), false);
        $response->assertSee('site/assets/owner-dashboard.png', false);
        $response->assertSee('family=Tajawal', false);
        $response->assertSee('data-theme-toggle', false);
        $response->assertSee('public_theme', false);
        $response->assertSee('/en', false);
        $response->assertDontSee('family=Alexandria', false);
        $response->assertDontSee('id="about"', false);
        $response->assertDontSee('id="forwho"', false);
        $this->assertFileExists(public_path('site/assets/owner-dashboard.png'));
    }

    public function test_order_review_preserves_the_selected_plan_for_account_creation(): void
    {
        $this->createPackage();
        $selectedPackage = $this->createPackage([
            'name_ar' => 'أسطول',
            'name_en' => 'Fleet',
            'boats_count' => 4,
            'original_price' => 6200,
            'sort_order' => 2,
        ]);

        $response = $this->get(route('site.order-review', ['package_id' => $selectedPackage->id]));

        $response->assertOk();
        $response->assertSee($selectedPackage->name);
        $response->assertSee('6,200');
        $response->assertSee('package_id='.$selectedPackage->id, false);
        $response->assertSee('guard=owner', false);
        $response->assertSee(__('marketing.checkout.activation_title'));
        $response->assertSee('checkout-summary', false);
        $response->assertSee('checkout-summary-action', false);
        $response->assertDontSee('bg-sand', false);
        $response->assertDontSee('المستندات المطلوبة');
    }

    public function test_english_public_pages_use_the_owner_interface_font(): void
    {
        app()->setLocale('en');

        $response = $this->get(route('landing-page'));

        $response->assertOk();
        $response->assertSee('family=Roboto', false);
        $response->assertSee('/ar', false);
        $response->assertDontSee('family=Tajawal', false);
    }

    public function test_signup_shows_and_submits_the_selected_subscription_package(): void
    {
        $package = $this->createPackage();

        $response = $this->get(route('frontend.show_register_form', [
            'package_id' => $package->id,
            'guard' => 'owner',
        ]));

        $response->assertOk();
        $response->assertSee($package->name);
        $response->assertSee('name="package_id" value="'.$package->id.'"', false);
        $response->assertSee('name="guard" value="owner"', false);
        $response->assertSee(__('marketing.signup.submit_with_plan'));
        $response->assertSee(__('marketing.signup.privacy_note'));
    }

    public function test_pricing_page_has_a_helpful_empty_state_when_no_plan_is_active(): void
    {
        $this->createPackage(['is_active' => false]);

        $response = $this->get(route('site.pricing'));

        $response->assertOk();
        $response->assertSee(__('marketing.pricing.empty_title'));
        $response->assertSee(route('site.contact'), false);
    }

    public function test_login_guides_new_owners_to_choose_a_subscription_plan(): void
    {
        $response = $this->get(route('frontend.show_login_form'));

        $response->assertOk();
        $response->assertSee(__('marketing.hero.title'));
        $response->assertSee(route('site.pricing'), false);
        $response->assertSee(__('marketing.nav.plans'));
    }

    public function test_processing_page_clearly_shows_the_pending_activation_state(): void
    {
        $response = $this->withSession([
            'pending_subscription' => [
                'package' => 'Hawat',
                'duration' => 'yearly',
                'boats_count' => 1,
                'invoice_number' => 'INV-1001',
            ],
        ])->get(route('site.processing'));

        $response->assertOk();
        $response->assertSee(__('marketing.processing.title'));
        $response->assertSee(__('marketing.processing.pending'));
        $response->assertSee('INV-1001');
        $response->assertSee(route('owner.dashboard'), false);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createPackage(array $attributes = []): SubscriptionPackage
    {
        return SubscriptionPackage::query()->create(array_merge([
            'name_ar' => 'حوات',
            'name_en' => 'Hawat',
            'boats_count' => 1,
            'price' => null,
            'original_price' => 2500,
            'duration_type' => 'yearly',
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 1,
        ], $attributes));
    }
}
