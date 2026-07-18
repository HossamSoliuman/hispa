<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\SubscriptionPackage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PublicPaymentStepTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app()['cache']->forget('spatie.permission.cache');
        Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);

        $this->withoutVite();
        $this->withoutMiddleware([
            LaravelLocalizationRedirectFilter::class,
            LaravelLocalizationViewPath::class,
            LocaleSessionRedirect::class,
        ]);
    }

    public function test_checkout_resumes_at_payment_with_admin_configured_bank_details(): void
    {
        Setting::query()->create(['key' => 'bank_name', 'value' => 'Al Rajhi Bank', 'type' => 'text']);
        Setting::query()->create(['key' => 'bank_account_number', 'value' => 'SA0380000000608010167519', 'type' => 'text']);

        [$owner, $invoice] = $this->ownerWithPendingInvoice();

        $response = $this->actingAs($owner, 'owner')->get(route('site.checkout'));

        $response->assertOk();
        $response->assertSee('Al Rajhi Bank');
        $response->assertSee('SA0380000000608010167519');
        $response->assertSee($invoice->subscription->package->name);
        $response->assertSee(route('site.checkout.payment'), false);
        $response->assertSee('data-start-step="2"', false);
    }

    public function test_checkout_refreshes_and_retries_a_stale_csrf_token_before_showing_an_error(): void
    {
        [$owner] = $this->ownerWithPendingInvoice();

        $response = $this->actingAs($owner, 'owner')->get(route('site.checkout'));

        $response->assertOk();
        $response->assertSee('X-CSRF-TOKEN', false);
        $response->assertSee('response.status === 419', false);
        $response->assertSee('refreshCsrfToken', false);
    }

    public function test_checkout_creates_owner_subscription_and_invoice_without_leaving_the_page(): void
    {
        $package = $this->createPackage();

        $response = $this->postJson(route('site.checkout.register'), [
            'name' => 'New Owner',
            'email' => 'newowner@example.com',
            'phone' => '966500000000',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'package_id' => $package->id,
        ]);

        $response->assertOk()
            ->assertJsonPath('package_id', $package->id)
            ->assertJsonPath('package', $package->name)
            ->assertJsonPath('total', 1499);

        $owner = User::query()->where('email', 'newowner@example.com')->firstOrFail();

        $this->assertAuthenticatedAs($owner, 'owner');
        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $owner->id,
            'package_id' => $package->id,
            'status' => 'pending',
        ]);
        $this->assertDatabaseHas('invoices', [
            'user_id' => $owner->id,
            'payment_status' => 'pending',
        ]);
    }

    public function test_authenticated_owner_can_continue_with_only_the_selected_plan(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $owner->assignRole('owner');
        $package = $this->createPackage();

        $response = $this->actingAs($owner, 'owner')->postJson(route('site.checkout.register'), [
            'package_id' => $package->id,
        ]);

        $response->assertOk()
            ->assertJsonPath('package_id', $package->id);

        $this->assertSame(1, User::query()->count());
        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $owner->id,
            'package_id' => $package->id,
            'status' => 'pending',
        ]);
    }

    public function test_submitting_receipt_marks_invoice_as_bank_transfer_and_returns_confirmation(): void
    {
        Storage::fake('public');

        [$owner, $invoice] = $this->ownerWithPendingInvoice();

        $response = $this->actingAs($owner, 'owner')->postJson(route('site.checkout.payment'), [
            'bank_transfer_receipt' => UploadedFile::fake()->image('receipt.jpg'),
        ]);

        $response->assertOk()
            ->assertJsonPath('invoice_number', $invoice->invoice_number)
            ->assertJsonPath('package', $invoice->subscription->package->name);

        $invoice->refresh();
        $this->assertSame('bank_transfer', $invoice->payment_method);
        $this->assertNotNull($invoice->bank_transfer_receipt);
        $this->assertSame('pending', $invoice->payment_status);
        Storage::disk('public')->assertExists($invoice->bank_transfer_receipt);

        $this->actingAs($owner, 'owner')
            ->get(route('site.processing'))
            ->assertOk()
            ->assertSee('data-start-step="3"', false);
    }

    public function test_receipt_is_required_to_complete_payment(): void
    {
        [$owner] = $this->ownerWithPendingInvoice();

        $response = $this->actingAs($owner, 'owner')->postJson(route('site.checkout.payment'), []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('bank_transfer_receipt');
    }

    /**
     * @return array{0: User, 1: Invoice}
     */
    private function ownerWithPendingInvoice(): array
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $owner->assignRole('owner');

        $package = $this->createPackage();

        $subscription = Subscription::query()->create([
            'user_id' => $owner->id,
            'package_id' => $package->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addYear()->toDateString(),
            'status' => 'pending',
        ]);

        $invoice = Invoice::query()->create([
            'subscription_id' => $subscription->id,
            'user_id' => $owner->id,
            'amount' => 1499,
            'total_amount' => 1499,
            'payment_status' => 'pending',
        ]);

        return [$owner, $invoice];
    }

    private function createPackage(): SubscriptionPackage
    {
        return SubscriptionPackage::query()->create([
            'name_ar' => 'باقة',
            'name_en' => 'Pro Plan',
            'boats_count' => 3,
            'original_price' => 1499,
            'duration_type' => 'yearly',
            'is_active' => true,
        ]);
    }
}
