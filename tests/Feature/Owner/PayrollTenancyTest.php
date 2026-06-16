<?php

namespace Tests\Feature\Owner;

use App\Models\PayrollModel;
use App\Models\User;
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

    public function test_owner_cannot_open_another_owners_payroll(): void
    {
        $ownerA = $this->makeOwner();
        $ownerB = $this->makeOwner();

        $payroll = PayrollModel::create([
            'owner_id' => $ownerA->id,
            'year' => 2026,
            'month' => 6,
            'status' => 'draft',
            'type' => 'percentage',
        ]);

        $this->actingAs($ownerB, 'owner');

        $this->followingRedirects()
            ->get(route('owner.payrolls.edit', $payroll))
            ->assertNotFound();
    }
}
