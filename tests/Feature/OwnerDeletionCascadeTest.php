<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Invoice;
use App\Models\Subscription;
use App\Models\SubscriptionPackage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnerDeletionCascadeTest extends TestCase
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

    private function makePackage(): SubscriptionPackage
    {
        return SubscriptionPackage::create([
            'name_ar' => 'باقة',
            'name_en' => 'Plan',
            'boats_count' => 2,
            'original_price' => 500,
            'duration_type' => 'monthly',
            'is_active' => true,
        ]);
    }

    private function makeSubscriptionWithInvoice(User $owner, SubscriptionPackage $package): Subscription
    {
        $subscription = Subscription::create([
            'user_id' => $owner->id,
            'package_id' => $package->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addMonth()->toDateString(),
            'status' => 'active',
        ]);

        Invoice::create([
            'subscription_id' => $subscription->id,
            'user_id' => $owner->id,
            'amount' => 500,
            'total_amount' => 500,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
        ]);

        return $subscription;
    }

    public function test_deleting_owner_cascades_subscriptions_invoices_and_company(): void
    {
        $owner = $this->makeOwner();
        $package = $this->makePackage();
        $subscription = $this->makeSubscriptionWithInvoice($owner, $package);

        Company::create([
            'owner_id' => $owner->id,
            'name_ar' => 'شركة',
            'name_en' => 'Company',
        ]);

        $ownerId = $owner->id;
        $subscriptionId = $subscription->id;

        $owner->delete();

        $this->assertDatabaseMissing('users', ['id' => $ownerId]);
        $this->assertDatabaseMissing('subscriptions', ['user_id' => $ownerId]);
        $this->assertDatabaseMissing('invoices', ['user_id' => $ownerId]);
        $this->assertDatabaseMissing('invoices', ['subscription_id' => $subscriptionId]);
        $this->assertDatabaseMissing('companies', ['owner_id' => $ownerId]);
        // The plan itself is shared master data and must survive owner deletion.
        $this->assertDatabaseHas('subscription_packages', ['id' => $package->id]);
    }

    public function test_deleting_invoice_owner_removes_invoice_linked_only_via_subscription(): void
    {
        $owner = $this->makeOwner();
        $package = $this->makePackage();

        $subscription = Subscription::create([
            'user_id' => $owner->id,
            'package_id' => $package->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addMonth()->toDateString(),
            'status' => 'active',
        ]);

        // Invoice whose direct user_id is stale/null but still belongs to the
        // owner through the subscription — must still be cascaded.
        $invoice = Invoice::create([
            'subscription_id' => $subscription->id,
            'user_id' => 999999,
            'amount' => 200,
            'total_amount' => 200,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
        ]);

        $owner->delete();

        $this->assertDatabaseMissing('invoices', ['id' => $invoice->id]);
        $this->assertDatabaseMissing('subscriptions', ['id' => $subscription->id]);
    }

    public function test_deleting_subscription_directly_removes_its_invoices(): void
    {
        $owner = $this->makeOwner();
        $package = $this->makePackage();
        $subscription = $this->makeSubscriptionWithInvoice($owner, $package);

        $subscriptionId = $subscription->id;

        $subscription->delete();

        $this->assertDatabaseMissing('subscriptions', ['id' => $subscriptionId]);
        $this->assertDatabaseMissing('invoices', ['subscription_id' => $subscriptionId]);
        // Only the subscription and its invoices go; the owner and shared plan survive.
        $this->assertDatabaseHas('users', ['id' => $owner->id]);
        $this->assertDatabaseHas('subscription_packages', ['id' => $package->id]);
    }

    public function test_deleting_non_owner_does_not_touch_billing(): void
    {
        $owner = $this->makeOwner();
        $package = $this->makePackage();
        $subscription = $this->makeSubscriptionWithInvoice($owner, $package);

        $captain = User::create([
            'name' => 'Captain',
            'phone' => (string) random_int(100000000, 999999999),
            'role' => 'captain',
            'owner_id' => $owner->id,
            'status' => 1,
            'password' => bcrypt('password'),
        ]);

        $captain->delete();

        $this->assertDatabaseMissing('users', ['id' => $captain->id]);
        $this->assertDatabaseHas('subscriptions', ['id' => $subscription->id]);
        $this->assertDatabaseHas('invoices', ['subscription_id' => $subscription->id]);
    }
}
