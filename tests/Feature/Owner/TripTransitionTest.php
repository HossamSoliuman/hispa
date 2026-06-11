<?php

namespace Tests\Feature\Owner;

use App\Enums\TripStatus;
use App\Models\CatchModel;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TripTransitionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()['cache']->forget('spatie.permission.cache');
        Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
    }

    private function makeOwnerWithTrip(TripStatus $status = TripStatus::New): array
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $owner->assignRole('owner');
        $captain = User::factory()->create(['role' => 'captain']);
        $trip = Trip::factory()->create([
            'owner_id' => $owner->id,
            'captain_id' => $captain->id,
            'status' => $status,
        ]);

        return [$owner, $trip];
    }

    private function transitionUrl(Trip $trip): string
    {
        return route('owner.trips.transition', $trip);
    }

    public function test_owner_can_walk_full_lifecycle(): void
    {
        [$owner, $trip] = $this->makeOwnerWithTrip(TripStatus::New);
        $this->actingAs($owner, 'owner');

        // New → InProgress
        $this->postJson($this->transitionUrl($trip), ['to' => 2])->assertOk();
        $this->assertSame(TripStatus::InProgress, $trip->fresh()->status);

        // InProgress → Finished
        $this->postJson($this->transitionUrl($trip), ['to' => 4])->assertOk();
        $this->assertSame(TripStatus::Finished, $trip->fresh()->status);

        // Create a catch so the trip can move on to selling
        CatchModel::create(['trip_id' => $trip->id, 'owner_id' => $owner->id]);

        // Finished → ReadyToSell
        $this->postJson($this->transitionUrl($trip), ['to' => 7])->assertOk();
        $this->assertSame(TripStatus::ReadyToSell, $trip->fresh()->status);

        // ReadyToSell → Sold
        $this->postJson($this->transitionUrl($trip), ['to' => 8])->assertOk();
        $this->assertSame(TripStatus::Sold, $trip->fresh()->status);
    }

    public function test_finished_to_ready_to_sell_fails_without_catch(): void
    {
        [$owner, $trip] = $this->makeOwnerWithTrip(TripStatus::Finished);
        $this->actingAs($owner, 'owner');

        $this->postJson($this->transitionUrl($trip), ['to' => 7])
            ->assertStatus(422)
            ->assertJsonFragment(['message' => __('trips.errors.catch_required')]);
    }

    public function test_cancel_from_new_requires_reason(): void
    {
        [$owner, $trip] = $this->makeOwnerWithTrip(TripStatus::New);
        $this->actingAs($owner, 'owner');

        $this->postJson($this->transitionUrl($trip), ['to' => 3])
            ->assertStatus(422);
    }

    public function test_cancel_from_new_with_reason_succeeds(): void
    {
        [$owner, $trip] = $this->makeOwnerWithTrip(TripStatus::New);
        $this->actingAs($owner, 'owner');

        $this->postJson($this->transitionUrl($trip), ['to' => 3, 'cancel_reason' => 'Weather conditions'])
            ->assertOk();
        $this->assertSame(TripStatus::Cancelled, $trip->fresh()->status);
    }

    public function test_cancel_from_in_progress_with_reason_succeeds(): void
    {
        [$owner, $trip] = $this->makeOwnerWithTrip(TripStatus::InProgress);
        $this->actingAs($owner, 'owner');

        $this->postJson($this->transitionUrl($trip), ['to' => 3, 'cancel_reason' => 'Emergency'])
            ->assertOk();
        $this->assertSame(TripStatus::Cancelled, $trip->fresh()->status);
    }

    public function test_cancel_from_finished_is_rejected(): void
    {
        [$owner, $trip] = $this->makeOwnerWithTrip(TripStatus::Finished);
        $this->actingAs($owner, 'owner');

        $this->postJson($this->transitionUrl($trip), ['to' => 3, 'cancel_reason' => 'Some reason'])
            ->assertStatus(422)
            ->assertJsonFragment(['message' => __('trips.errors.invalid_transition')]);
    }

    public function test_transition_from_terminal_sold_is_rejected(): void
    {
        [$owner, $trip] = $this->makeOwnerWithTrip(TripStatus::Sold);
        $this->actingAs($owner, 'owner');

        $this->postJson($this->transitionUrl($trip), ['to' => 7])
            ->assertStatus(422)
            ->assertJsonFragment(['message' => __('trips.errors.trip_terminal')]);
    }

    public function test_transition_from_terminal_cancelled_is_rejected(): void
    {
        [$owner, $trip] = $this->makeOwnerWithTrip(TripStatus::Cancelled);
        $this->actingAs($owner, 'owner');

        $this->postJson($this->transitionUrl($trip), ['to' => 1, 'cancel_reason' => 'x'])
            ->assertStatus(422);
    }

    public function test_owner_cannot_transition_another_owners_trip(): void
    {
        [, $trip] = $this->makeOwnerWithTrip(TripStatus::New);
        $otherOwner = User::factory()->create(['role' => 'owner']);
        $otherOwner->assignRole('owner');
        $this->actingAs($otherOwner, 'owner');

        $this->postJson($this->transitionUrl($trip), ['to' => 2])
            ->assertStatus(403);
    }
}
