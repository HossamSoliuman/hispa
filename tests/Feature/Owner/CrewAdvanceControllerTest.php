<?php

namespace Tests\Feature\Owner;

use App\Models\CrewAdvance;
use App\Models\PayrollDetailsModel;
use App\Models\PayrollModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CrewAdvanceControllerTest extends TestCase
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

    private function makeOwner(): User
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $owner->assignRole('owner');

        return $owner;
    }

    public function test_owner_can_record_an_advance_for_their_crew(): void
    {
        $owner = $this->makeOwner();
        $crew = User::factory()->create(['role' => 'crew', 'owner_id' => $owner->id]);
        $this->actingAs($owner, 'owner');

        $response = $this->post(route('owner.crew-advances.store'), [
            'user_id' => $crew->id,
            'amount' => 250.50,
            'date' => '2026-06-10',
            'notes' => 'فطار',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('crew_advances', [
            'user_id' => $crew->id,
            'owner_id' => $owner->id,
            'amount' => 250.50,
        ]);
    }

    public function test_owner_cannot_record_an_advance_for_another_owners_crew(): void
    {
        $owner = $this->makeOwner();
        $otherOwner = $this->makeOwner();
        $foreignCrew = User::factory()->create(['role' => 'crew', 'owner_id' => $otherOwner->id]);
        $this->actingAs($owner, 'owner');

        $response = $this->post(route('owner.crew-advances.store'), [
            'user_id' => $foreignCrew->id,
            'amount' => 100,
            'date' => '2026-06-10',
        ]);

        $response->assertSessionHasErrors('user_id');
        $this->assertDatabaseMissing('crew_advances', ['user_id' => $foreignCrew->id]);
    }

    public function test_amount_must_be_positive(): void
    {
        $owner = $this->makeOwner();
        $crew = User::factory()->create(['role' => 'crew', 'owner_id' => $owner->id]);
        $this->actingAs($owner, 'owner');

        $response = $this->post(route('owner.crew-advances.store'), [
            'user_id' => $crew->id,
            'amount' => 0,
            'date' => '2026-06-10',
        ]);

        $response->assertSessionHasErrors('amount');
    }

    public function test_owner_can_delete_their_own_advance(): void
    {
        $owner = $this->makeOwner();
        $crew = User::factory()->create(['role' => 'crew', 'owner_id' => $owner->id]);
        $advance = CrewAdvance::create([
            'user_id' => $crew->id,
            'owner_id' => $owner->id,
            'amount' => 300,
            'date' => '2026-06-10',
        ]);
        $this->actingAs($owner, 'owner');

        $response = $this->delete(route('owner.crew-advances.destroy', $advance->id));

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('crew_advances', ['id' => $advance->id]);
    }

    public function test_owner_cannot_delete_another_owners_advance(): void
    {
        $owner = $this->makeOwner();
        $otherOwner = $this->makeOwner();
        $foreignCrew = User::factory()->create(['role' => 'crew', 'owner_id' => $otherOwner->id]);
        $advance = CrewAdvance::create([
            'user_id' => $foreignCrew->id,
            'owner_id' => $otherOwner->id,
            'amount' => 300,
            'date' => '2026-06-10',
        ]);
        $this->actingAs($owner, 'owner');

        $response = $this->delete(route('owner.crew-advances.destroy', $advance->id));

        $response->assertForbidden();
        $this->assertDatabaseHas('crew_advances', ['id' => $advance->id]);
    }

    public function test_advances_are_deducted_from_unpaid_percentage_payroll_net(): void
    {
        $owner = $this->makeOwner();
        $crew = User::factory()->create(['role' => 'crew', 'owner_id' => $owner->id, 'salary_type' => 'percentage']);

        $payroll = PayrollModel::create([
            'owner_id' => $owner->id,
            'year' => 2026,
            'month' => 6,
            'status' => 'draft',
            'type' => 'percentage',
        ]);
        // Per-head share = 1000 / 2 = 500.
        $detail = PayrollDetailsModel::create([
            'payroll_id' => $payroll->id,
            'user_id' => $crew->id,
            'captins_amount' => 1000,
            'captins_count' => 2,
            'increase' => 0,
            'deduction' => 0,
            'advances' => 0,
            'final_salary' => 500,
            'is_paid' => false,
        ]);

        CrewAdvance::create([
            'user_id' => $crew->id,
            'owner_id' => $owner->id,
            'amount' => 200,
            'date' => '2026-06-15',
        ]);

        $this->actingAs($owner, 'owner');
        $this->get(route('owner.payrolls.edit', $payroll->id))->assertOk();

        $detail->refresh();
        $this->assertEquals(200.0, (float) $detail->advances);
        $this->assertEquals(300.0, (float) $detail->final_salary);
    }

    public function test_paid_payroll_rows_are_not_touched_by_new_advances(): void
    {
        $owner = $this->makeOwner();
        $crew = User::factory()->create(['role' => 'crew', 'owner_id' => $owner->id, 'salary_type' => 'percentage']);

        $payroll = PayrollModel::create([
            'owner_id' => $owner->id,
            'year' => 2026,
            'month' => 6,
            'status' => 'draft',
            'type' => 'percentage',
        ]);
        $detail = PayrollDetailsModel::create([
            'payroll_id' => $payroll->id,
            'user_id' => $crew->id,
            'captins_amount' => 1000,
            'captins_count' => 2,
            'advances' => 0,
            'final_salary' => 500,
            'is_paid' => true,
            'paid_amount' => 500,
            'paid_at' => '2026-06-30 00:00:00',
        ]);

        CrewAdvance::create([
            'user_id' => $crew->id,
            'owner_id' => $owner->id,
            'amount' => 200,
            'date' => '2026-06-15',
        ]);

        $this->actingAs($owner, 'owner');
        $this->get(route('owner.payrolls.edit', $payroll->id))->assertOk();

        $detail->refresh();
        $this->assertEquals(0.0, (float) $detail->advances);
        $this->assertEquals(500.0, (float) $detail->final_salary);
    }
}
