<?php

namespace Tests\Feature\Owner;

use App\Models\Boat;
use App\Models\PayrollDetailsModel;
use App\Models\PayrollModel;
use App\Models\User;
use App\Service\Owner\PayrollService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PayrollPaymentTest extends TestCase
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

    /**
     * Build a single-person crew percentage payroll whose per-head share equals
     * $perHead (captins_amount / captins_count).
     *
     * @return array{0: PayrollModel, 1: PayrollDetailsModel}
     */
    private function makePercentagePayroll(User $owner, float $perHead): array
    {
        $crew = User::factory()->create([
            'role' => 'crew',
            'owner_id' => $owner->id,
            'salary_type' => 'percentage',
            'salary_amount' => 20,
        ]);

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
            'base_salary' => $perHead,
            'percentage' => 20,
            'sales_amount' => 0,
            'final_salary' => $perHead,
            'captins_amount' => $perHead,
            'captins_count' => 1,
        ]);

        return [$payroll, $detail];
    }

    public function test_pay_detail_marks_person_paid_and_freezes_net_amount(): void
    {
        $owner = $this->makeOwner();
        [$payroll, $detail] = $this->makePercentagePayroll($owner, 3000);

        $this->assertFalse((bool) $detail->is_paid);

        $this->actingAs($owner, 'owner')
            ->post(route('owner.payrolls.payDetail', $detail), [
                'increase' => 100,
                'deduction' => 50,
            ])
            ->assertOk()
            ->assertJson(['final_salary' => 3050.0, 'paid_amount' => 3050.0, 'payroll_fully_paid' => true]);

        $detail->refresh();
        $this->assertTrue((bool) $detail->is_paid);
        $this->assertSame(3050.0, (float) $detail->final_salary);
        $this->assertSame(3050.0, (float) $detail->paid_amount);
        $this->assertNotNull($detail->paid_at);

        // Single-detail payroll becomes fully paid.
        $this->assertSame(1, (int) $payroll->fresh()->is_paid);
        $this->assertNotNull($payroll->fresh()->paid_at);
    }

    public function test_percentage_detail_pays_per_head_share(): void
    {
        $owner = $this->makeOwner();
        $captain = User::factory()->create([
            'role' => 'captain',
            'owner_id' => $owner->id,
            'salary_type' => 'percentage',
            'salary_amount' => 20,
        ]);

        $payroll = PayrollModel::create([
            'owner_id' => $owner->id,
            'year' => 2026,
            'month' => 6,
            'status' => 'draft',
            'type' => 'percentage',
        ]);
        $detail = PayrollDetailsModel::create([
            'payroll_id' => $payroll->id,
            'user_id' => $captain->id,
            'base_salary' => 300, // 900 / 3
            'percentage' => 20,
            'sales_amount' => 0,
            'final_salary' => 300,
            'captins_amount' => 900,
            'captins_count' => 3,
        ]);

        $this->actingAs($owner, 'owner')
            ->post(route('owner.payrolls.payDetail', $detail))
            ->assertOk()
            ->assertJson(['final_salary' => 300.0, 'paid_amount' => 300.0]);

        $detail->refresh();
        $this->assertTrue((bool) $detail->is_paid);
        $this->assertSame(300.0, (float) $detail->paid_amount);
    }

    public function test_pay_detail_does_not_withhold_depreciation_from_net(): void
    {
        $owner = $this->makeOwner();
        [, $detail] = $this->makePercentagePayroll($owner, 3000);
        // Depreciation is a fleet-level cost (shown only in the summary cards) and
        // is no longer withheld per person, so any stored value is ignored.
        $detail->update(['depreciation' => 250]);

        // 3000 (per head) + 100 increase - 50 deduction = 3050 (depreciation ignored).
        $this->actingAs($owner, 'owner')
            ->post(route('owner.payrolls.payDetail', $detail), [
                'increase' => 100,
                'deduction' => 50,
            ])
            ->assertOk()
            ->assertJson(['final_salary' => 3050.0, 'paid_amount' => 3050.0]);
    }

    public function test_already_paid_detail_is_rejected(): void
    {
        $owner = $this->makeOwner();
        [, $detail] = $this->makePercentagePayroll($owner, 1500);
        $detail->update(['is_paid' => true, 'paid_at' => now(), 'paid_amount' => 1500]);

        $this->actingAs($owner, 'owner')
            ->post(route('owner.payrolls.payDetail', $detail))
            ->assertStatus(422);
    }

    public function test_owner_cannot_pay_another_owners_detail(): void
    {
        $ownerA = $this->makeOwner();
        $ownerB = $this->makeOwner();
        [, $detail] = $this->makePercentagePayroll($ownerA, 2000);

        $this->actingAs($ownerB, 'owner')
            ->post(route('owner.payrolls.payDetail', $detail))
            ->assertForbidden();

        $this->assertFalse((bool) $detail->fresh()->is_paid);
    }

    public function test_update_does_not_overwrite_a_paid_row(): void
    {
        $owner = $this->makeOwner();
        [$payroll, $detail] = $this->makePercentagePayroll($owner, 4000);
        $detail->update(['is_paid' => true, 'paid_at' => now(), 'paid_amount' => 4000, 'final_salary' => 4000]);

        $this->actingAs($owner, 'owner')
            ->put(route('owner.payrolls.update', $payroll), [
                'id' => $payroll->id,
                'status' => 'approved',
                'details' => [
                    ['id' => $detail->id, 'increase' => 999, 'deduction' => 0, 'note' => 'should be ignored'],
                ],
            ]);

        $detail->refresh();
        $this->assertSame(4000.0, (float) $detail->final_salary);
        $this->assertSame(0.0, (float) $detail->increase);
    }

    public function test_percentage_member_base_honors_custom_share_percent(): void
    {
        $owner = $this->makeOwner();
        $boat = Boat::create(['owner_id' => $owner->id, 'name_ar' => 'قارب', 'number' => 'B-1']);

        $captain = User::factory()->create([
            'role' => 'captain', 'owner_id' => $owner->id, 'boat_id' => $boat->id,
            'salary_type' => 'percentage', 'salary_amount' => 0, 'custom_share_percent' => 20,
        ]);
        $crewA = User::factory()->create([
            'role' => 'crew', 'owner_id' => $owner->id, 'boat_id' => $boat->id,
            'salary_type' => 'percentage', 'salary_amount' => 0, 'custom_share_percent' => null,
        ]);
        $crewB = User::factory()->create([
            'role' => 'crew', 'owner_id' => $owner->id, 'boat_id' => $boat->id,
            'salary_type' => 'percentage', 'salary_amount' => 0, 'custom_share_percent' => null,
        ]);

        $service = new PayrollService;

        // The captain takes 20% of the 1,000 pool off the top (200); the remaining
        // 800 is split equally between the two plain crew (400 each).
        $this->assertSame(200.0, $service->percentageMemberBase($captain, 1000.0, $owner->id));
        $this->assertSame(400.0, $service->percentageMemberBase($crewA, 1000.0, $owner->id));
        $this->assertSame(400.0, $service->percentageMemberBase($crewB, 1000.0, $owner->id));
    }

    public function test_monthly_payroll_summary_reflects_payments(): void
    {
        $owner = $this->makeOwner();
        [, $detail] = $this->makePercentagePayroll($owner, 2500);

        $summary = (new PayrollService)->monthlyPayrollSummary($owner->id, 2026, 6);
        $this->assertSame('unpaid', $summary['percentage']['status']);
        $this->assertSame(0.0, $summary['percentage']['paid_amount']);

        $detail->update(['is_paid' => true, 'paid_at' => now(), 'paid_amount' => 2500]);

        $summary = (new PayrollService)->monthlyPayrollSummary($owner->id, 2026, 6);
        $this->assertSame('fully_paid', $summary['percentage']['status']);
        $this->assertSame(2500.0, $summary['percentage']['paid_amount']);
        $this->assertSame(1, $summary['percentage']['paid_count']);
    }
}
