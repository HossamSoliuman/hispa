<?php

namespace Tests\Feature\Owner;

use App\Models\Category;
use App\Models\Fish;
use App\Models\PaymentMethod;
use App\Models\Port;
use App\Models\Scopes\OwnerScope;
use App\Models\Unit;
use App\Models\User;
use App\Services\Owner\OwnerMasterDataService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OwnerMasterDataTenancyTest extends TestCase
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

    public function test_seeding_creates_default_master_data_scoped_to_the_owner(): void
    {
        $owner = $this->makeOwner();

        app(OwnerMasterDataService::class)->seedFor($owner);

        $this->assertGreaterThan(0, Fish::withoutGlobalScope(OwnerScope::class)->where('owner_id', $owner->id)->count());
        $this->assertGreaterThan(0, Port::withoutGlobalScope(OwnerScope::class)->where('owner_id', $owner->id)->count());
        $this->assertSame(5, Unit::withoutGlobalScope(OwnerScope::class)->where('owner_id', $owner->id)->count());
        $this->assertSame(1, Unit::withoutGlobalScope(OwnerScope::class)->where('owner_id', $owner->id)->where('is_default', true)->count());

        // Every seeded row carries the owner id — none leak as global rows.
        $this->assertSame(0, Fish::withoutGlobalScope(OwnerScope::class)->whereNull('owner_id')->count());
        $this->assertSame(0, Category::withoutGlobalScope(OwnerScope::class)->whereNull('owner_id')->count());
    }

    public function test_seeding_is_idempotent(): void
    {
        $owner = $this->makeOwner();
        $service = app(OwnerMasterDataService::class);

        $service->seedFor($owner);
        $countAfterFirst = Fish::withoutGlobalScope(OwnerScope::class)->where('owner_id', $owner->id)->count();

        $service->seedFor($owner);
        $countAfterSecond = Fish::withoutGlobalScope(OwnerScope::class)->where('owner_id', $owner->id)->count();

        $this->assertSame($countAfterFirst, $countAfterSecond);
    }

    public function test_owner_only_sees_their_own_master_data(): void
    {
        $ownerA = $this->makeOwner();
        $ownerB = $this->makeOwner();

        $service = app(OwnerMasterDataService::class);
        $service->seedFor($ownerA);
        $service->seedFor($ownerB);

        $this->actingAs($ownerA, 'owner');
        $aFish = Fish::pluck('owner_id')->unique()->values();
        $this->assertEquals([$ownerA->id], $aFish->all());

        $this->actingAs($ownerB, 'owner');
        $bFish = Fish::pluck('owner_id')->unique()->values();
        $this->assertEquals([$ownerB->id], $bFish->all());
    }

    public function test_lookup_created_under_owner_auth_is_auto_stamped(): void
    {
        $owner = $this->makeOwner();
        $other = $this->makeOwner();

        $this->actingAs($owner, 'owner');
        $fish = Fish::create(['scientific_name' => 'Scoped Fish', 'status' => 1]);

        $this->assertSame($owner->id, $fish->owner_id);

        // Visible to its owner, invisible to another owner.
        $this->assertTrue(Fish::whereKey($fish->id)->exists());

        $this->actingAs($other, 'owner');
        $this->assertFalse(Fish::whereKey($fish->id)->exists());
    }

    public function test_unauthenticated_context_is_not_scoped(): void
    {
        $ownerA = $this->makeOwner();
        $ownerB = $this->makeOwner();

        $service = app(OwnerMasterDataService::class);
        $service->seedFor($ownerA);
        $service->seedFor($ownerB);

        // Admin / console context (no owner guard) sees every owner's rows.
        $this->assertSame(
            PaymentMethod::withoutGlobalScope(OwnerScope::class)->count(),
            PaymentMethod::count()
        );
    }
}
