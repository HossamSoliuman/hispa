<?php

namespace Tests\Feature\Owner;

use App\Models\Boat;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TripQuickExpensesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()['cache']->forget('spatie.permission.cache');
        Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
    }

    /**
     * @return array{0: User, 1: Boat, 2: User, 3: Category}
     */
    private function makeOwnerSetup(): array
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $owner->assignRole('owner');
        $captain = User::factory()->create(['role' => 'captain', 'owner_id' => $owner->id]);
        $boat = Boat::create([
            'name_ar' => 'قارب اختبار',
            'name_en' => 'Test Boat',
            'number' => 'B-1',
            'status' => 1,
            'owner_id' => $owner->id,
        ]);
        $parent = Category::create([
            'name_ar' => 'مصاريف تشغيلية',
            'name_en' => 'Operating Expenses',
            'type' => 'operating',
            'status' => 1,
            'parent_id' => null,
        ]);
        $ice = Category::create([
            'name_ar' => 'ثلج',
            'name_en' => 'Ice',
            'type' => 'operating',
            'status' => 1,
            'parent_id' => $parent->id,
        ]);

        return [$owner, $boat, $captain, $ice];
    }

    private function tripPayload(User $owner, Boat $boat, User $captain): array
    {
        return [
            'name' => 'رحلة',
            'name_en' => 'Trip',
            'license_number' => 'LIC-1',
            'start_date' => now()->format('Y-m-d\TH:i'),
            'owner_id' => $owner->id,
            'captain_id' => $captain->id,
            'boat_id' => $boat->id,
        ];
    }

    public function test_quick_expenses_create_expense_records_for_owner(): void
    {
        [$owner, $boat, $captain, $ice] = $this->makeOwnerSetup();
        $vendor = User::factory()->create(['role' => 'vendor', 'owner_id' => $owner->id]);
        $this->actingAs($owner, 'owner');

        $payload = $this->tripPayload($owner, $boat, $captain) + [
            'quick_expenses' => [
                ['category_id' => $ice->id, 'vendor_id' => $vendor->id, 'amount' => 150.50],
            ],
            'quick_expenses_status' => 'paid',
        ];

        $this->post(route('owner.trips.store'), $payload)->assertRedirect();

        $trip = Trip::where('owner_id', $owner->id)->firstOrFail();

        $this->assertDatabaseHas('expenses', [
            'owner_id' => $owner->id,
            'boat_id' => $boat->id,
            'category_id' => $ice->id,
            'vendor_id' => $vendor->id,
            'final_price' => 150.50,
            'status' => 'paid',
        ]);

        $expense = Expense::withoutGlobalScopes()->where('category_id', $ice->id)->firstOrFail();
        $this->assertSame($trip->start_date->toDateString(), \Illuminate\Support\Carbon::parse($expense->date)->toDateString());
    }

    public function test_zero_and_blank_quick_expenses_are_skipped(): void
    {
        [$owner, $boat, $captain, $ice] = $this->makeOwnerSetup();
        $this->actingAs($owner, 'owner');

        $payload = $this->tripPayload($owner, $boat, $captain) + [
            'quick_expenses' => [
                ['category_id' => $ice->id, 'vendor_id' => null, 'amount' => 0],
                ['category_id' => '', 'vendor_id' => '', 'amount' => ''],
            ],
        ];

        $this->post(route('owner.trips.store'), $payload)->assertRedirect();

        $this->assertSame(0, Expense::withoutGlobalScopes()->count());
    }

    public function test_trip_creates_without_quick_expenses(): void
    {
        [$owner, $boat, $captain] = $this->makeOwnerSetup();
        $this->actingAs($owner, 'owner');

        $this->post(route('owner.trips.store'), $this->tripPayload($owner, $boat, $captain))
            ->assertRedirect();

        $this->assertDatabaseHas('trips', ['owner_id' => $owner->id, 'boat_id' => $boat->id]);
        $this->assertSame(0, Expense::withoutGlobalScopes()->count());
    }
}
