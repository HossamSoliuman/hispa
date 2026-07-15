<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Subscription;
use App\Models\SubscriptionPackage;
use App\Models\Unit;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterWithPlanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_registering_with_a_plan_creates_pending_subscription_and_invoice(): void
    {
        $package = SubscriptionPackage::create([
            'name_ar' => 'باقة',
            'name_en' => 'Plan',
            'boats_count' => 2,
            'original_price' => 500,
            'duration_type' => 'monthly',
            'is_active' => true,
        ]);

        $response = $this->post(route('frontend.register'), [
            'name' => 'New Owner',
            'email' => 'newowner@example.com',
            'phone' => '966500000000',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'guard' => 'owner',
            'package_id' => $package->id,
        ]);

        $response->assertRedirect(route('site.processing'));

        $owner = User::where('email', 'newowner@example.com')->firstOrFail();

        $subscription = Subscription::where('user_id', $owner->id)->firstOrFail();
        $this->assertSame('pending', $subscription->status);
        $this->assertSame($package->id, (int) $subscription->package_id);

        $invoice = Invoice::where('subscription_id', $subscription->id)->firstOrFail();
        $this->assertSame('pending', $invoice->payment_status);
        $this->assertSame('500.00', (string) $invoice->total_amount);

        // Pending subscription grants no boat quota yet.
        $this->assertSame(0, $owner->boatLimit());
    }

    public function test_registering_without_a_plan_creates_no_subscription(): void
    {
        $this->post(route('frontend.register'), [
            'name' => 'Plain Owner',
            'email' => 'plain@example.com',
            'phone' => '966511111111',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'guard' => 'owner',
        ]);

        $owner = User::where('email', 'plain@example.com')->firstOrFail();
        $this->assertSame(0, Subscription::where('user_id', $owner->id)->count());
        $this->assertSame(5, Unit::where('owner_id', $owner->id)->count());
        $this->assertSame(1, Unit::where('owner_id', $owner->id)->where('is_default', true)->count());
    }
}
