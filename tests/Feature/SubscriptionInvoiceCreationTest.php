<?php

namespace Tests\Feature;

use App\Models\SubscriptionPackage;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionInvoiceCreationTest extends TestCase
{
    use RefreshDatabase;

    private function makeOwner(): User
    {
        return User::create([
            'name' => 'Owner '.uniqid(),
            'email' => uniqid().'@example.com',
            'phone' => (string) random_int(100000000, 999999999),
            'role' => 'owner',
            'status' => 1,
            'password' => bcrypt('password'),
        ]);
    }

    private function makePackage(float $original = 500, ?float $offer = null): SubscriptionPackage
    {
        return SubscriptionPackage::create([
            'name_ar' => 'باقة',
            'name_en' => 'Plan',
            'boats_count' => 2,
            'original_price' => $original,
            'price' => $offer,
            'duration_type' => 'monthly',
            'is_active' => true,
        ]);
    }

    public function test_creating_subscription_also_creates_matching_invoice(): void
    {
        $owner = $this->makeOwner();
        $package = $this->makePackage(500, 400);

        $subscription = app(SubscriptionService::class)->create([
            'user_id' => $owner->id,
            'package_id' => $package->id,
            'start_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('invoices', [
            'subscription_id' => $subscription->id,
            'user_id' => $owner->id,
            'amount' => 400,
            'total_amount' => 400,
            'payment_status' => 'pending',
        ]);
        $this->assertCount(1, $subscription->invoices()->get());
    }

    public function test_active_subscription_invoice_is_marked_paid(): void
    {
        $owner = $this->makeOwner();
        $package = $this->makePackage(500);

        $subscription = app(SubscriptionService::class)->create([
            'user_id' => $owner->id,
            'package_id' => $package->id,
            'start_date' => now()->toDateString(),
            'status' => 'active',
        ]);

        $invoice = $subscription->invoices()->first();

        $this->assertNotNull($invoice);
        $this->assertSame('paid', $invoice->payment_status);
        $this->assertNotNull($invoice->paid_at);
        $this->assertEquals(500, (float) $invoice->total_amount);
    }
}
