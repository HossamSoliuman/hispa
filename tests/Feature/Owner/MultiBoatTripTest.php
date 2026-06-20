<?php

namespace Tests\Feature\Owner;

use App\Models\Boat;
use App\Models\Sale;
use App\Models\Trip;
use App\Models\TripBoat;
use App\Models\User;
use App\Service\Owner\TripFinancialsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MultiBoatTripTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()['cache']->forget('spatie.permission.cache');
        Notification::fake();
        Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);

        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        ]);
    }

    private function owner(): User
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $owner->assignRole('owner');

        return $owner;
    }

    private function boat(User $owner, string $number): Boat
    {
        return Boat::create([
            'owner_id' => $owner->id,
            'name_ar' => 'قارب '.$number,
            'name_en' => 'Boat '.$number,
            'number' => $number,
            'status' => 1,
        ]);
    }

    private function captain(User $owner, string $name): User
    {
        return User::factory()->create([
            'role' => 'captain',
            'owner_id' => $owner->id,
            'name' => $name,
            'salary_type' => 'percentage',
            'profit_shares' => 1,
        ]);
    }

    private function crew(User $owner, Boat $boat, string $name): User
    {
        return User::factory()->create([
            'role' => 'crew',
            'owner_id' => $owner->id,
            'boat_id' => $boat->id,
            'name' => $name,
            'salary_type' => 'percentage',
            'profit_shares' => 1,
        ]);
    }

    public function test_store_creates_multi_boat_trip_with_captains_per_boat(): void
    {
        $owner = $this->owner();
        $boatA = $this->boat($owner, 'B-A');
        $boatB = $this->boat($owner, 'B-B');
        $captain1 = $this->captain($owner, 'Captain One');
        $captain2 = $this->captain($owner, 'Captain Two');
        $captain3 = $this->captain($owner, 'Captain Three');

        $this->actingAs($owner, 'owner');

        $this->post(route('owner.trips.store'), [
            'name' => 'رحلة',
            'name_en' => 'Trip',
            'license_number' => 'LIC-MB-1',
            'start_date' => now()->format('Y-m-d\TH:i'),
            'owner_id' => $owner->id,
            'boats' => [
                ['boat_id' => $boatA->id, 'captain_ids' => [$captain1->id, $captain2->id]],
                ['boat_id' => $boatB->id, 'captain_ids' => [$captain3->id]],
            ],
        ])->assertRedirect(route('owner.trips.index'));

        $trip = Trip::where('license_number', 'LIC-MB-1')->firstOrFail();

        $this->assertSame(2, $trip->tripBoats()->count());
        $this->assertDatabaseHas('trip_boats', ['trip_id' => $trip->id, 'boat_id' => $boatA->id]);
        $this->assertDatabaseHas('trip_boats', ['trip_id' => $trip->id, 'boat_id' => $boatB->id]);

        $boatATripBoat = $trip->tripBoats()->where('boat_id', $boatA->id)->firstOrFail();
        $this->assertEqualsCanonicalizing(
            [$captain1->id, $captain2->id],
            $boatATripBoat->captains()->pluck('users.id')->all()
        );

        // The trip's primary boat/captain snapshot mirrors the first boat.
        $this->assertSame($boatA->id, $trip->boat_id);
        $this->assertSame($captain1->id, $trip->captain_id);
    }

    public function test_store_rejects_boat_not_owned_by_owner(): void
    {
        $owner = $this->owner();
        $otherOwner = $this->owner();
        $foreignBoat = $this->boat($otherOwner, 'B-X');
        $captain = $this->captain($owner, 'Cap');

        $this->actingAs($owner, 'owner');

        $this->post(route('owner.trips.store'), [
            'name' => 'رحلة',
            'name_en' => 'Trip',
            'license_number' => 'LIC-MB-2',
            'start_date' => now()->format('Y-m-d\TH:i'),
            'owner_id' => $owner->id,
            'boats' => [
                ['boat_id' => $foreignBoat->id, 'captain_ids' => [$captain->id]],
            ],
        ])->assertSessionHasErrors('boats.0.boat_id');

        $this->assertDatabaseMissing('trips', ['license_number' => 'LIC-MB-2']);
    }

    public function test_financials_are_computed_per_boat_and_summed(): void
    {
        $owner = $this->owner();
        $boatA = $this->boat($owner, 'F-A');
        $boatB = $this->boat($owner, 'F-B');

        $captainA = $this->captain($owner, 'Cap A');
        $crewA = $this->crew($owner, $boatA, 'Crew A');
        $captainB = $this->captain($owner, 'Cap B');
        $crewB = $this->crew($owner, $boatB, 'Crew B');

        $trip = Trip::factory()->create(['owner_id' => $owner->id, 'boat_id' => $boatA->id]);

        $tripBoatA = TripBoat::create(['trip_id' => $trip->id, 'boat_id' => $boatA->id, 'crew_count' => 1]);
        $tripBoatA->captains()->attach($captainA->id);
        $tripBoatB = TripBoat::create(['trip_id' => $trip->id, 'boat_id' => $boatB->id, 'crew_count' => 1]);
        $tripBoatB->captains()->attach($captainB->id);

        $this->makeSale($owner, $trip, $boatA, 1000.0);
        $this->makeSale($owner, $trip, $boatB, 2000.0);

        $financials = app(TripFinancialsService::class)->compute($trip->fresh());

        $this->assertCount(2, $financials['boats']);

        $boatAFin = $financials['boats']->firstWhere('boat_id', $boatA->id);
        $boatBFin = $financials['boats']->firstWhere('boat_id', $boatB->id);

        // Boat A: 1000 net → 500 owner / 500 crew → captainA + crewA get 250 each.
        $this->assertEqualsWithDelta(1000.0, $boatAFin['net_profit'], 0.01);
        $this->assertEqualsWithDelta(500.0, $boatAFin['owner_share'], 0.01);
        $this->assertEqualsWithDelta(500.0, $boatAFin['crew_share'], 0.01);
        $this->assertEqualsCanonicalizing(
            [$captainA->id, $crewA->id],
            $boatAFin['crew_members']->pluck('user_id')->all()
        );
        $this->assertEqualsWithDelta(250.0, $boatAFin['crew_members']->firstWhere('user_id', $crewA->id)['due'], 0.01);

        // Boat B: 2000 net → 1000 owner / 1000 crew → captainB + crewB get 500 each.
        $this->assertEqualsWithDelta(2000.0, $boatBFin['net_profit'], 0.01);
        $this->assertEqualsWithDelta(500.0, $boatBFin['crew_members']->firstWhere('user_id', $crewB->id)['due'], 0.01);

        // Boat A's payout sheet must not leak boat B's crew.
        $this->assertNull($boatAFin['crew_members']->firstWhere('user_id', $crewB->id));

        // Trip totals are the sum of the two boats.
        $this->assertEqualsWithDelta(3000.0, $financials['net_profit'], 0.01);
        $this->assertEqualsWithDelta(1500.0, $financials['owner_share'], 0.01);
        $this->assertEqualsWithDelta(1500.0, $financials['crew_share'], 0.01);
        $this->assertCount(4, $financials['crew_members']);
    }

    public function test_trip_create_page_renders_boat_picker(): void
    {
        $owner = $this->owner();
        $this->boat($owner, 'C-1');
        $this->captain($owner, 'Cap');
        $this->actingAs($owner, 'owner');

        $this->get(route('owner.trips.create'))
            ->assertOk()
            ->assertSee('boats[0][boat_id]', false)
            ->assertSee('boats[0][captain_ids][]', false);
    }

    public function test_trip_index_modal_renders_boat_picker(): void
    {
        $owner = $this->owner();
        $this->boat($owner, 'IDX-1');
        $this->captain($owner, 'Cap Idx');
        $this->actingAs($owner, 'owner');

        $this->get(route('owner.trips.index'))
            ->assertOk()
            ->assertSee('boats[0][boat_id]', false)
            ->assertSee('boats[0][captain_ids][]', false)
            ->assertSee('id="addBoatRow"', false);
    }

    public function test_trip_show_page_renders_per_boat_sections(): void
    {
        $owner = $this->owner();
        $boatA = $this->boat($owner, 'S-1');
        $captainA = $this->captain($owner, 'Cap Show');
        $trip = Trip::factory()->create(['owner_id' => $owner->id, 'boat_id' => $boatA->id]);
        $tripBoat = TripBoat::create([
            'trip_id' => $trip->id,
            'boat_id' => $boatA->id,
            'boat_name' => $boatA->name_ar,
            'boat_number' => $boatA->number,
            'crew_count' => 0,
        ]);
        $tripBoat->captains()->attach($captainA->id);

        $this->actingAs($owner, 'owner');

        $this->get(route('owner.trips.show', $trip->id))
            ->assertOk()
            ->assertSee($boatA->name_ar)
            ->assertSee($captainA->name);
    }

    private function makeSale(User $owner, Trip $trip, Boat $boat, float $total): Sale
    {
        return Sale::create([
            'number' => 'S-'.$boat->id.'-'.uniqid(),
            'seller_type' => 'owner',
            'seller_id' => $owner->id,
            'trip_id' => $trip->id,
            'boat_id' => $boat->id,
            'payment_status' => 'paid',
            'status' => 2,
            'total_price' => $total,
            'commission_amount' => 0,
            'labor_amount' => 0,
            'net_owner_amount' => $total,
            'remaining_total' => 0,
            'sale_datetime' => now(),
        ]);
    }
}
