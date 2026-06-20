<?php

namespace Tests\Feature\Owner;

use App\Enums\AlertSeverity;
use App\Enums\AlertType;
use App\Enums\TripStatus;
use App\Models\Boat;
use App\Models\Inspection;
use App\Models\Maintenance;
use App\Models\Trip;
use App\Models\User;
use App\Service\Owner\OwnerAlertService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OwnerAlertServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()['cache']->forget('spatie.permission.cache');
        Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);

        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        ]);
    }

    public function test_trip_overdue_only_fires_for_new_and_in_progress(): void
    {
        $owner = $this->owner();
        $boat = $this->boat($owner);

        foreach ([TripStatus::New, TripStatus::InProgress] as $status) {
            $this->overdueTrip($owner, $boat, $status, now()->subDays(2));
        }
        foreach ([TripStatus::Sold, TripStatus::Cancelled, TripStatus::Finished] as $status) {
            $this->overdueTrip($owner, $boat, $status, now()->subDays(2));
        }

        $overdue = $this->alertsFor($owner)->where('type', AlertType::TripOverdue);

        $this->assertCount(2, $overdue);
    }

    public function test_trip_overdue_escalates_to_critical_after_threshold(): void
    {
        $owner = $this->owner();
        $boat = $this->boat($owner);

        $this->overdueTrip($owner, $boat, TripStatus::InProgress, now()->subHour());   // ~1h -> warning
        $this->overdueTrip($owner, $boat, TripStatus::InProgress, now()->subDays(5));   // 120h -> critical

        $severities = $this->alertsFor($owner)
            ->where('type', AlertType::TripOverdue)
            ->map(fn ($alert) => $alert->severity);

        $this->assertTrue($severities->contains(AlertSeverity::Warning));
        $this->assertTrue($severities->contains(AlertSeverity::Critical));
    }

    public function test_license_expiry_produces_warning_critical_and_nothing_outside_window(): void
    {
        $owner = $this->owner();
        $this->captain($owner, ['fishing_license_expiry' => now()->addDays(20)->toDateString()]); // warning
        $this->captain($owner, ['fishing_license_expiry' => now()->addDays(3)->toDateString()]);  // critical
        $this->captain($owner, ['fishing_license_expiry' => now()->subDays(5)->toDateString()]);  // critical (expired)
        $this->captain($owner, ['fishing_license_expiry' => now()->addDays(60)->toDateString()]); // none

        $alerts = $this->alertsFor($owner)->where('type', AlertType::CaptainFishingLicense);

        $this->assertCount(3, $alerts);
        $this->assertSame(1, $alerts->where('severity', AlertSeverity::Warning)->count());
        $this->assertSame(2, $alerts->where('severity', AlertSeverity::Critical)->count());
    }

    public function test_boat_license_expiry_fires_within_window(): void
    {
        $owner = $this->owner();
        $this->boat($owner, ['license_date_expire' => now()->addDays(10)->toDateString()]);
        $this->boat($owner, ['license_date_expire' => now()->addDays(90)->toDateString()]); // outside

        $alerts = $this->alertsFor($owner)->where('type', AlertType::BoatLicense);

        $this->assertCount(1, $alerts);
    }

    public function test_inspection_due_uses_latest_current_inspection_per_boat(): void
    {
        $owner = $this->owner();
        $boat = $this->boat($owner);

        // Older inspection would be due soon, but the latest one is far in the future.
        Inspection::create(['boat_id' => $boat->id, 'status' => 'current', 'check_date' => now()->subDays(400), 'next_check' => now()->addDays(2)]);
        Inspection::create(['boat_id' => $boat->id, 'status' => 'current', 'check_date' => now()->subDay(), 'next_check' => now()->addDays(120)]);

        $this->assertCount(0, $this->alertsFor($owner)->where('type', AlertType::InspectionDue));

        // A boat whose latest current inspection is imminent does fire (critical).
        $boat2 = $this->boat($owner);
        Inspection::create(['boat_id' => $boat2->id, 'status' => 'current', 'check_date' => now()->subDays(300), 'next_check' => now()->addDays(2)]);

        $alerts = $this->alertsFor($owner)->where('type', AlertType::InspectionDue);
        $this->assertCount(1, $alerts);
        $this->assertSame(AlertSeverity::Critical, $alerts->first()->severity);
    }

    public function test_maintenance_due_fires_on_next_maintenance_date(): void
    {
        $owner = $this->owner();
        $boat = $this->boat($owner);

        Maintenance::create([
            'owner_id' => $owner->id,
            'boat_id' => $boat->id,
            'date' => now()->subDays(30)->toDateString(),
            'next_maintenance_date' => now()->addDays(2)->toDateString(),
        ]);

        $alerts = $this->alertsFor($owner)->where('type', AlertType::MaintenanceDue);

        $this->assertCount(1, $alerts);
        $this->assertSame(AlertSeverity::Critical, $alerts->first()->severity);
    }

    public function test_alerts_are_scoped_to_their_owner(): void
    {
        $ownerA = $this->owner();
        $ownerB = $this->owner();
        $boatA = $this->boat($ownerA);
        $this->overdueTrip($ownerA, $boatA, TripStatus::InProgress, now()->subDays(2));

        $this->assertCount(1, $this->alertsFor($ownerA)->where('type', AlertType::TripOverdue));
        $this->assertCount(0, $this->alertsFor($ownerB));
    }

    public function test_garbage_or_empty_residence_date_is_ignored_without_error(): void
    {
        $owner = $this->owner();
        $this->crew($owner, ['residence_end_date' => 'not-a-date']);
        $this->crew($owner, ['residence_end_date' => '']);
        $this->crew($owner, ['residence_end_date' => now()->addDays(10)->toDateString()]); // valid -> alert

        $alerts = $this->alertsFor($owner)->where('type', AlertType::CrewResidence);

        $this->assertCount(1, $alerts);
    }

    public function test_alerts_sorted_by_severity_then_soonest_due(): void
    {
        $owner = $this->owner();
        $this->captain($owner, ['fishing_license_expiry' => now()->addDays(20)->toDateString()]); // warning
        $this->boat($owner, ['license_date_expire' => now()->subDays(3)->toDateString()]);  // critical, due -3
        $this->boat($owner, ['license_date_expire' => now()->subDays(15)->toDateString()]); // critical, due -15

        $alerts = $this->alertsFor($owner);

        $this->assertSame(AlertSeverity::Critical, $alerts[0]->severity);
        $this->assertSame(AlertSeverity::Critical, $alerts[1]->severity);
        $this->assertTrue($alerts[0]->dueAt->lt($alerts[1]->dueAt));
        $this->assertSame(AlertSeverity::Warning, $alerts->last()->severity);
    }

    public function test_dashboard_index_renders_the_alerts_card_with_rows(): void
    {
        $owner = $this->owner();
        $owner->assignRole('owner');
        $boat = $this->boat($owner);
        $this->overdueTrip($owner, $boat, TripStatus::InProgress, now()->subDays(5));

        $this->actingAs($owner, 'owner');

        $this->get(route('owner.dashboard'))
            ->assertOk()
            ->assertViewHas('alerts')
            ->assertViewHas('alertSummary')
            ->assertSee(__('owner.alerts.title'))
            ->assertSee(__('owner.alerts.trip_overdue.title'));
    }

    public function test_alerts_data_endpoint_returns_json_for_the_owner(): void
    {
        $owner = $this->owner();
        $owner->assignRole('owner');
        $boat = $this->boat($owner);
        $this->overdueTrip($owner, $boat, TripStatus::InProgress, now()->subDays(5));

        $this->actingAs($owner, 'owner');

        $response = $this->getJson(route('owner.alerts.data'));

        $response->assertOk()->assertJsonStructure([
            'alerts' => [['type', 'severity', 'severity_color', 'icon', 'title', 'message', 'url', 'due_at', 'due_for_humans']],
            'summary' => ['total', 'critical', 'warning', 'info'],
        ]);
        $this->assertGreaterThanOrEqual(1, count($response->json('alerts')));
    }

    public function test_alerts_data_endpoint_requires_authentication(): void
    {
        $this->get(route('owner.alerts.data'))->assertRedirect();
    }

    private function owner(): User
    {
        return User::factory()->create(['role' => 'owner']);
    }

    /**
     * @param  array<string, mixed>  $attrs
     */
    private function boat(User $owner, array $attrs = []): Boat
    {
        return Boat::create(array_merge([
            'owner_id' => $owner->id,
            'name_ar' => 'قارب',
            'name_en' => 'Boat',
            'number' => 'B-'.uniqid(),
            'status' => 1,
        ], $attrs));
    }

    /**
     * @param  array<string, mixed>  $attrs
     */
    private function captain(User $owner, array $attrs = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'captain',
            'owner_id' => $owner->id,
            'status' => 1,
        ], $attrs));
    }

    /**
     * @param  array<string, mixed>  $attrs
     */
    private function crew(User $owner, array $attrs = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'crew',
            'owner_id' => $owner->id,
            'status' => 1,
        ], $attrs));
    }

    private function overdueTrip(User $owner, Boat $boat, TripStatus $status, \Illuminate\Support\Carbon $endDate): Trip
    {
        return Trip::factory()->create([
            'owner_id' => $owner->id,
            'boat_id' => $boat->id,
            'status' => $status,
            'end_date' => $endDate,
        ]);
    }

    private function alertsFor(User $owner): Collection
    {
        return (new OwnerAlertService)->for($owner->id);
    }
}
