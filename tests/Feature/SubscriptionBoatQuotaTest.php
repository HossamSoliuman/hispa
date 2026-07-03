<?php

namespace Tests\Feature;

use App\Models\Boat;
use App\Models\SubscriptionPackage;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionBoatQuotaTest extends TestCase
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

    private function makePackage(int $boats): SubscriptionPackage
    {
        return SubscriptionPackage::create([
            'name_ar' => 'باقة',
            'name_en' => 'Plan',
            'boats_count' => $boats,
            'original_price' => 500,
            'duration_type' => 'monthly',
            'is_active' => true,
        ]);
    }

    private function addBoat(User $owner): Boat
    {
        return Boat::create([
            'owner_id' => $owner->id,
            'name_ar' => 'قارب',
            'number' => (string) random_int(1000, 9999),
        ]);
    }

    public function test_owner_without_subscription_has_zero_quota(): void
    {
        $owner = $this->makeOwner();

        $this->assertSame(0, $owner->boatLimit());
        $this->assertFalse($owner->canCreateBoat());
    }

    public function test_pending_subscription_does_not_grant_quota(): void
    {
        $owner = $this->makeOwner();
        $package = $this->makePackage(3);

        app(SubscriptionService::class)->create([
            'user_id' => $owner->id,
            'package_id' => $package->id,
            'start_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        $this->assertSame(0, $owner->boatLimit());
        $this->assertFalse($owner->canCreateBoat());
    }

    public function test_activating_subscription_grants_plan_quota_and_dates(): void
    {
        $owner = $this->makeOwner();
        $package = $this->makePackage(2);

        $subscription = app(SubscriptionService::class)->create([
            'user_id' => $owner->id,
            'package_id' => $package->id,
            'start_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        app(SubscriptionService::class)->activate($subscription);

        $subscription->refresh();
        $this->assertSame('active', $subscription->status);
        $this->assertTrue($subscription->end_date->greaterThan(now()));
        $this->assertSame(2, $owner->boatLimit());
        $this->assertTrue($owner->canCreateBoat());
    }

    public function test_owner_can_create_boats_up_to_plan_limit(): void
    {
        $owner = $this->makeOwner();
        $package = $this->makePackage(2);

        $subscription = app(SubscriptionService::class)->create([
            'user_id' => $owner->id,
            'package_id' => $package->id,
            'start_date' => now()->toDateString(),
            'status' => 'pending',
        ]);
        app(SubscriptionService::class)->activate($subscription);

        $this->addBoat($owner);
        $this->assertTrue($owner->canCreateBoat());
        $this->assertSame(1, $owner->remainingBoatSlots());

        $this->addBoat($owner);
        $this->assertFalse($owner->canCreateBoat());
        $this->assertSame(0, $owner->remainingBoatSlots());
    }
}
