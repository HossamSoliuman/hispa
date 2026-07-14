<?php

namespace Tests\Feature\Owner;

use App\Enums\TripStatus;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TripDataTableTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app()['cache']->forget('spatie.permission.cache');
        Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
    }

    public function test_running_trips_stat_counts_only_non_terminal_trips_for_the_owner(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $owner->assignRole('owner');

        $otherOwner = User::factory()->create(['role' => 'owner']);

        foreach ([TripStatus::New, TripStatus::InProgress, TripStatus::Finished, TripStatus::Counted, TripStatus::ReadyToSell] as $status) {
            Trip::factory()->create([
                'owner_id' => $owner->id,
                'status' => $status,
            ]);
        }
        Trip::factory()->create([
            'owner_id' => $owner->id,
            'status' => TripStatus::Sold,
        ]);
        Trip::factory()->create([
            'owner_id' => $owner->id,
            'status' => TripStatus::Cancelled,
        ]);
        Trip::factory()->create([
            'owner_id' => $otherOwner->id,
            'status' => TripStatus::InProgress,
        ]);

        $this->actingAs($owner, 'owner')
            ->followingRedirects()
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->getJson(route('owner.getTripData', [
                'draw' => 1,
                'start' => 0,
                'length' => 10,
            ]))
            ->assertOk()
            ->assertJsonPath('trip_waiting_status', 5);
    }
}
