<?php

namespace Tests\Feature\Owner;

use App\Models\Trip;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class GenerateTripNumberTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
    }

    private function makeTrip(string $number): Trip
    {
        return Trip::factory()->create(['number' => $number]);
    }

    public function test_it_starts_at_001_when_no_trips_exist_for_the_year(): void
    {
        $this->assertSame('TR-'.date('Y').'-001', generateTripNumber());
    }

    public function test_it_increments_from_the_latest_trip_number(): void
    {
        $year = date('Y');
        $this->makeTrip("TR-{$year}-027");

        $this->assertSame("TR-{$year}-028", generateTripNumber());
    }

    public function test_it_does_not_reuse_a_soft_deleted_trip_number(): void
    {
        $year = date('Y');
        $this->makeTrip("TR-{$year}-027");
        $deleted = $this->makeTrip("TR-{$year}-028");

        $deleted->delete();

        $this->assertSoftDeleted('trips', ['id' => $deleted->id]);
        $this->assertSame("TR-{$year}-029", generateTripNumber());
    }

    public function test_it_uses_the_highest_number_even_when_ids_are_out_of_order(): void
    {
        $year = date('Y');
        $this->makeTrip("TR-{$year}-015");
        $this->makeTrip("TR-{$year}-009");

        $this->assertSame("TR-{$year}-016", generateTripNumber());
    }
}
