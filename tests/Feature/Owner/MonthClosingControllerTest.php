<?php

namespace Tests\Feature\Owner;

use App\Models\MonthClosing;
use App\Models\PayrollDetailsModel;
use App\Models\PayrollModel;
use App\Models\Sale;
use App\Models\User;
use App\Service\Owner\MonthClosingService;
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

        // mcamara localization redirects non-prefixed GETs in tests; bypass it so
        // the show route exercises the real controller + view, not the redirect.
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

    public function test_show_reflects_percentage_payroll_payments_in_dues(): void
    {
        $owner = $this->makeOwner();
        $captain = User::factory()->create([
            'role' => 'captain',
            'owner_id' => $owner->id,
            'salary_type' => 'percentage',
            'profit_shares' => 1.0,
        ]);
        Sale::create([
            'number' => 'S-'.uniqid(),
            'seller_type' => 'owner',
            'seller_id' => $owner->id,
            'total_price' => 100000,
            'net_owner_amount' => 100000,
            'sale_datetime' => '2026-06-15 10:00:00',
            'status' => 1,
        ]);

        // crew_share = 50% of 100000 net profit; single share => due = 50000.
        $closing = app(MonthClosingService::class)->close($owner->id, 2026, 6);

        // Crew salary actually disbursed through the percentage payroll.
        $payroll = PayrollModel::create([
            'owner_id' => $owner->id,
            'year' => 2026,
            'month' => 6,
            'status' => 'approved',
            'type' => 'percentage',
        ]);
        PayrollDetailsModel::create([
            'payroll_id' => $payroll->id,
            'user_id' => $captain->id,
            'base_salary' => 0,
            'percentage' => 0,
            'sales_amount' => 0,
            'final_salary' => 30000,
            'is_paid' => true,
            'paid_at' => now(),
            'paid_amount' => 30000,
        ]);

        $response = $this->actingAs($owner, 'owner')
            ->get(route('owner.month-closing.show', $closing));

        $response->assertOk();

        $due = $response->viewData('closing')->dues->firstWhere('user_id', $captain->id);
        $this->assertSame(30000.0, (float) $due->paid_amount);
        $this->assertSame(20000.0, (float) $due->remaining); // 50000 due - 0 advances - 30000 paid
    }

    public function test_owner_can_print_month_closing_report(): void
    {
        $owner = $this->makeOwner();
        User::factory()->create([
            'role' => 'captain',
            'owner_id' => $owner->id,
            'salary_type' => 'percentage',
            'profit_shares' => 1.0,
        ]);
        Sale::create([
            'number' => 'S-'.uniqid(),
            'seller_type' => 'owner',
            'seller_id' => $owner->id,
            'total_price' => 80000,
            'net_owner_amount' => 80000,
            'sale_datetime' => '2026-06-15 10:00:00',
            'status' => 1,
        ]);

        $closing = app(MonthClosingService::class)->close($owner->id, 2026, 6);

        $response = $this->actingAs($owner, 'owner')
            ->get(route('owner.month-closing.print', $closing));

        $response->assertOk();
        $this->assertSame('application/pdf', $response->headers->get('content-type'));
    }
}
