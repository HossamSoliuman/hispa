<?php

namespace Tests\Feature\Owner;

use App\Models\CatchDetail;
use App\Models\CatchModel;
use App\Models\Expense;
use App\Models\Fish;
use App\Models\FishQuantityStock;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Trip;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TripDeleteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        app()['cache']->forget('spatie.permission.cache');
        Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
    }

    private function makeOwner(): User
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $owner->assignRole('owner');

        return $owner;
    }

    /**
     * Build a trip with a catch, sale and expense so we can assert the full cascade.
     */
    private function seedTripWithRelations(User $owner): Trip
    {
        $trip = Trip::factory()->create(['owner_id' => $owner->id]);

        $fish = Fish::create(['scientific_name' => 'Test Fish', 'status' => 1, 'owner_id' => $owner->id]);
        $unit = Unit::create(['name_ar' => 'كجم', 'is_default' => true, 'status' => true, 'owner_id' => $owner->id]);

        $catch = CatchModel::create([
            'trip_id' => $trip->id,
            'owner_id' => $owner->id,
            'catch_date' => now(),
            'total_weight' => 10,
            'total_amount' => 0,
        ]);
        CatchDetail::create([
            'catch_id' => $catch->id,
            'fish_id' => $fish->id,
            'unit_id' => $unit->id,
            'weight' => 10,
        ]);
        FishQuantityStock::create([
            'fish_id' => $fish->id,
            'unit_id' => $unit->id,
            'catch_id' => $catch->id,
            'trip_id' => $trip->id,
            'boat_id' => $trip->boat_id,
            'quantity' => 10,
        ]);

        $sale = Sale::create([
            'number' => 'S-1',
            'seller_type' => 'owner',
            'seller_id' => $owner->id,
            'trip_id' => $trip->id,
            'catch_id' => $catch->id,
            'status' => 2,
            'total_price' => 500,
            'net_owner_amount' => 500,
            'remaining_total' => 0,
            'sale_datetime' => now(),
        ]);
        SaleDetail::create([
            'sale_id' => $sale->id,
            'fish_id' => $fish->id,
            'unit_id' => $unit->id,
            'fish_name' => $fish->scientific_name,
            'weight' => 10,
            'price_per_kilo' => 50,
            'total_price' => 500,
        ]);

        Expense::create([
            'date' => now(),
            'number' => 'E-1',
            'owner_id' => $owner->id,
            'boat_id' => $trip->boat_id,
            'trip_id' => $trip->id,
            'total_price' => 100,
            'final_price' => 100,
            'status' => 'paid',
        ]);

        return $trip;
    }

    public function test_owner_can_delete_trip_and_all_related_records_are_purged(): void
    {
        $owner = $this->makeOwner();
        $trip = $this->seedTripWithRelations($owner);
        $this->actingAs($owner, 'owner');

        $this->delete(route('owner.trips.destroy', $trip->id))
            ->assertOk()
            ->assertJsonStructure(['message']);

        $this->assertDatabaseMissing('trips', ['id' => $trip->id]);

        $this->assertDatabaseMissing('catch_models', ['trip_id' => $trip->id]);
        $this->assertSame(0, CatchDetail::count());
        $this->assertDatabaseMissing('fish_quantity_stocks', ['trip_id' => $trip->id]);
        $this->assertSame(0, Sale::withTrashed()->where('trip_id', $trip->id)->count());
        $this->assertSame(0, SaleDetail::count());
        $this->assertSame(0, Expense::withTrashed()->withoutGlobalScopes()->where('trip_id', $trip->id)->count());
    }

    public function test_owner_cannot_delete_another_owners_trip(): void
    {
        $owner = $this->makeOwner();
        $otherOwner = $this->makeOwner();
        $trip = Trip::factory()->create(['owner_id' => $otherOwner->id]);

        $this->actingAs($owner, 'owner');

        $this->delete(route('owner.trips.destroy', $trip->id))->assertNotFound();

        $this->assertDatabaseHas('trips', ['id' => $trip->id, 'deleted_at' => null]);
    }
}
