<?php

namespace Tests\Feature\Owner;

use App\Models\PayrollModel;
use App\Models\User;
use App\Service\Owner\PayrollService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PayrollTenancyTest extends TestCase
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

    public function test_monthly_payroll_only_includes_own_staff(): void
    {
        $ownerA = $this->makeOwner();
        $ownerB = $this->makeOwner();

        $staffA = User::factory()->create([
            'role' => 'employee',
            'owner_id' => $ownerA->id,
            'salary_type' => 'salary',
            'salary_amount' => 3000,
        ]);
        User::factory()->create([
            'role' => 'employee',
            'owner_id' => $ownerB->id,
            'salary_type' => 'salary',
            'salary_amount' => 9000,
        ]);

        $payroll = (new PayrollService)->calculateMonthlyPayroll($ownerA->id, 2026, 6);

        $this->assertSame($ownerA->id, (int) $payroll->owner_id);
        $this->assertCount(1, $payroll->details);
        $this->assertSame($staffA->id, (int) $payroll->details->first()->user_id);
        $this->assertSame(3000.0, (float) $payroll->details->first()->final_salary);
    }

    public function test_owner_cannot_open_another_owners_payroll(): void
    {
        $ownerA = $this->makeOwner();
        $ownerB = $this->makeOwner();

        $payroll = PayrollModel::create([
            'owner_id' => $ownerA->id,
            'year' => 2026,
            'month' => 6,
            'status' => 'draft',
            'type' => 'salary',
        ]);

        $this->actingAs($ownerB, 'owner');

        $this->followingRedirects()
            ->get(route('owner.payrolls.edit', $payroll))
            ->assertNotFound();
    }

    public function test_payroll_check_does_not_reuse_another_owners_payroll(): void
    {
        $ownerA = $this->makeOwner();
        $ownerB = $this->makeOwner();

        PayrollModel::create([
            'owner_id' => $ownerA->id,
            'year' => 2026,
            'month' => 6,
            'status' => 'draft',
            'type' => 'salary',
        ]);

        $this->actingAs($ownerB, 'owner');
        $this->post(route('owner.payrolls.check'), ['year' => 2026, 'month' => 6]);

        $this->assertSame(1, PayrollModel::where('owner_id', $ownerB->id)->count());
        $this->assertSame(1, PayrollModel::where('owner_id', $ownerA->id)->count());
    }
}
