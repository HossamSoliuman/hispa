<?php

namespace Tests\Feature\Owner;

use App\Models\MonthClosing;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MonthClosingControllerTest extends TestCase
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

    public function test_owner_can_close_a_month(): void
    {
        $owner = $this->makeOwner();
        Sale::create([
            'number' => 'S-'.uniqid(),
            'seller_type' => 'owner',
            'seller_id' => $owner->id,
            'total_price' => 50000,
            'net_owner_amount' => 50000,
            'sale_datetime' => '2026-05-15 10:00:00',
            'status' => 1,
        ]);
        User::factory()->create(['role' => 'crew', 'owner_id' => $owner->id, 'salary_type' => 'percentage', 'profit_shares' => 1.0]);

        $this->actingAs($owner, 'owner');

        $response = $this->post(route('owner.month-closing.close'), ['year' => 2026, 'month' => 5]);

        $closing = MonthClosing::where('owner_id', $owner->id)->where('year', 2026)->where('month', 5)->first();
        $this->assertNotNull($closing);
        $response->assertRedirect(route('owner.month-closing.show', $closing));
    }

    public function test_closing_twice_redirects_with_error(): void
    {
        $owner = $this->makeOwner();
        $this->actingAs($owner, 'owner');

        $this->post(route('owner.month-closing.close'), ['year' => 2026, 'month' => 5]);
        $response = $this->post(route('owner.month-closing.close'), ['year' => 2026, 'month' => 5]);

        $response->assertRedirect(route('owner.month-closing.index'));
        $response->assertSessionHas('error');
    }
}
