<?php

namespace Tests\Feature\Owner\Startup;

use App\Enums\CustomFeature;
use App\Models\CustomFeatureAccess;
use App\Models\Startup\Expense;
use App\Models\Startup\ExpenseCategory;
use App\Models\Startup\Loan;
use App\Models\Startup\Partner;
use App\Models\Startup\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StartupProjectWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        app()->setLocale('en');
        app()['cache']->forget('spatie.permission.cache');
        Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        ]);

        $this->owner = User::factory()->create([
            'role' => 'owner',
            'email' => 'startup-owner@example.test',
        ]);
        CustomFeatureAccess::factory()->create([
            'user_id' => $this->owner->id,
            'feature' => CustomFeature::BusinessStartup,
        ]);
        $this->owner->assignRole('owner');
        $this->actingAs($this->owner, 'owner');
    }

    public function test_owner_can_create_update_and_review_every_project_field(): void
    {
        $response = $this->post(route('owner.startup.projects.store'), [
            'name' => 'Harbour Workshop',
            'type' => 'service',
            'start_date' => '2026-07-11',
            'status' => 'setup',
            'description' => 'Repairs and marine supplies.',
            'notes' => 'Launch before the autumn season.',
        ]);

        $project = Project::query()->firstOrFail();
        $response->assertRedirect(route('owner.startup.projects.show', $project));

        $this->get(route('owner.startup.projects.show', $project))
            ->assertOk()
            ->assertSee('Harbour Workshop')
            ->assertSee('2026-07-11')
            ->assertSee('Repairs and marine supplies.')
            ->assertSee('Launch before the autumn season.');

        Partner::factory()->create([
            'project_id' => $project->id,
            'owner_id' => $this->owner->id,
            'share_percent' => '100.00',
        ]);

        $this->put(route('owner.startup.projects.update', $project), [
            'name' => 'Harbour Workshop Ltd',
            'type' => 'commercial',
            'start_date' => '2026-07-12',
            'status' => 'active',
            'description' => 'Updated description.',
            'notes' => 'Updated notes.',
        ])->assertRedirect(route('owner.startup.projects.show', $project));

        $this->assertDatabaseHas('startup_projects', [
            'id' => $project->id,
            'name' => 'Harbour Workshop Ltd',
            'type' => 'commercial',
            'status' => 'active',
            'description' => 'Updated description.',
            'notes' => 'Updated notes.',
        ]);
    }

    public function test_project_validation_and_owner_tenant_isolation_are_enforced(): void
    {
        $this->post(route('owner.startup.projects.store'), [
            'name' => 'Invalid project',
            'type' => 'unknown',
            'start_date' => '2026-07-11',
            'status' => 'active',
        ])->assertInvalid(['type', 'status']);

        $project = Project::factory()->create(['owner_id' => $this->owner->id]);
        $otherOwner = User::factory()->create(['role' => 'owner']);
        $otherOwner->assignRole(Role::findByName('owner', 'web'));

        $this->actingAs($otherOwner, 'owner')
            ->get(route('owner.startup.projects.show', $project))
            ->assertNotFound();
    }

    public function test_ownership_draft_requires_exactly_one_hundred_percent_before_financial_activity(): void
    {
        $project = Project::factory()->create(['owner_id' => $this->owner->id]);
        $category = ExpenseCategory::factory()->create(['owner_id' => $this->owner->id]);

        $firstPartner = Partner::factory()->create([
            'project_id' => $project->id,
            'owner_id' => $this->owner->id,
            'share_percent' => '99.99',
        ]);

        $this->post(route('owner.startup.projects.expenses.store', $project), $this->expensePayload($category))
            ->assertInvalid(['ownership']);

        $this->put(route('owner.startup.projects.update', $project), [
            'name' => $project->name,
            'type' => $project->type,
            'start_date' => $project->start_date->format('Y-m-d'),
            'status' => 'active',
        ])->assertInvalid(['status']);

        $secondPartner = Partner::factory()->create([
            'project_id' => $project->id,
            'owner_id' => $this->owner->id,
            'share_percent' => '0.01',
        ]);

        $this->post(route('owner.startup.projects.expenses.store', $project), $this->expensePayload($category))
            ->assertSessionHasNoErrors();

        $this->delete(route('owner.startup.partners.destroy', $secondPartner))
            ->assertInvalid(['partner']);

        $this->put(route('owner.startup.partners.update', $firstPartner), [
            'name' => $firstPartner->name,
            'share_percent' => '100.00',
            'partner_type' => $firstPartner->partner_type,
        ])->assertInvalid(['share_percent']);

        $this->post(route('owner.startup.projects.partners.store', $project), [
            'name' => 'Excess partner',
            'share_percent' => '0.01',
            'partner_type' => 'investor',
        ])->assertInvalid(['share_percent']);
    }

    public function test_omitted_shared_value_defaults_to_yes_but_explicit_false_is_preserved(): void
    {
        $project = $this->completeProject();
        $category = ExpenseCategory::factory()->create(['owner_id' => $this->owner->id]);

        $this->post(route('owner.startup.projects.expenses.store', $project), $this->expensePayload($category, ['name' => 'Default shared']))
            ->assertSessionHasNoErrors();

        $this->post(route('owner.startup.projects.expenses.store', $project), $this->expensePayload($category, [
            'name' => 'Private expense',
            'is_shared' => '0',
        ]))->assertSessionHasNoErrors();

        $this->assertTrue(Expense::query()->where('name', 'Default shared')->firstOrFail()->is_shared);
        $this->assertFalse(Expense::query()->where('name', 'Private expense')->firstOrFail()->is_shared);
    }

    public function test_loan_repayments_cannot_exceed_principal_and_paid_status_is_derived(): void
    {
        $project = $this->completeProject();
        $loan = Loan::factory()->create([
            'project_id' => $project->id,
            'owner_id' => $this->owner->id,
            'amount' => '100.00',
            'status' => 'active',
        ]);

        $this->post(route('owner.startup.loans.payments.store', $loan), $this->paymentPayload('80.00'))
            ->assertSessionHasNoErrors();

        $this->post(route('owner.startup.loans.payments.store', $loan), $this->paymentPayload('20.01'))
            ->assertInvalid(['amount']);

        $loan->update(['status' => 'defaulted']);
        $this->post(route('owner.startup.loans.payments.store', $loan), $this->paymentPayload('20.00'))
            ->assertSessionHasNoErrors();

        $this->assertSame('paid', $loan->refresh()->status);
        $this->assertEquals(100.0, (float) $loan->payments()->sum('amount'));
    }

    public function test_project_expense_filters_render_the_filtered_collection(): void
    {
        $project = $this->completeProject();
        $travel = ExpenseCategory::factory()->create(['owner_id' => $this->owner->id, 'name_en' => 'Travel']);
        $licenses = ExpenseCategory::factory()->create(['owner_id' => $this->owner->id, 'name_en' => 'Licenses']);

        Expense::factory()->create([
            'project_id' => $project->id,
            'owner_id' => $this->owner->id,
            'category_id' => $travel->id,
            'name' => 'Visible travel expense',
        ]);
        Expense::factory()->create([
            'project_id' => $project->id,
            'owner_id' => $this->owner->id,
            'category_id' => $licenses->id,
            'name' => 'Hidden license expense',
        ]);

        $response = $this->get(route('owner.startup.projects.show', [
            'project' => $project,
            'tab' => 'expenses',
            'exp_category' => $travel->id,
        ]));

        $response->assertOk();
        $this->assertSame(['Visible travel expense'], $response->viewData('expenses')->pluck('name')->all());
        $response
            ->assertSee('Visible travel expense')
            ->assertDontSee('Hidden license expense');
    }

    public function test_expense_report_filters_totals_and_exports_keep_the_same_filter_set(): void
    {
        $project = $this->completeProject();
        $partner = $project->partners()->firstOrFail();
        $category = ExpenseCategory::factory()->create(['owner_id' => $this->owner->id, 'name_en' => 'Licensing']);
        Expense::factory()->create([
            'project_id' => $project->id,
            'owner_id' => $this->owner->id,
            'category_id' => $category->id,
            'date' => '2026-07-11',
            'name' => 'Filtered report expense',
            'amount' => '125.50',
            'payer_type' => 'partner',
            'partner_id' => $partner->id,
            'payment_method' => 'transfer',
            'is_shared' => false,
            'attachment' => 'startup/expenses/invoice.pdf',
        ]);

        $filters = [
            'from' => '2026-07-01',
            'to' => '2026-07-31',
            'category_id' => $category->id,
            'partner_id' => $partner->id,
            'payment_method' => 'transfer',
            'payer_type' => 'partner',
            'is_shared' => '0',
            'invoice' => 'with',
        ];

        $response = $this->get(route('owner.startup.reports.expenses', ['project' => $project] + $filters));

        $response->assertOk()->assertSee('Filtered report expense')->assertSee('125.50');
        $this->assertCount(1, $response->viewData('rows'));
        $this->assertEquals(125.5, $response->viewData('totals')['amount']);
        foreach (array_keys($filters) as $filterKey) {
            $response->assertSee($filterKey.'=', false);
        }

        $this->get(route('owner.startup.reports.expenses', [
            'project' => $project,
            'from' => '2026-07-31',
            'to' => '2026-07-01',
        ]))->assertInvalid(['to']);

        Excel::fake();
        $this->get(route('owner.startup.reports.excel', ['project' => $project, 'report' => 'summary']))->assertOk();
        Excel::assertDownloaded('summary-'.$project->id.'.xlsx');
    }

    private function completeProject(): Project
    {
        $project = Project::factory()->create(['owner_id' => $this->owner->id]);
        Partner::factory()->create([
            'project_id' => $project->id,
            'owner_id' => $this->owner->id,
            'share_percent' => '100.00',
        ]);

        return $project;
    }

    /** @param array<string, mixed> $overrides */
    private function expensePayload(ExpenseCategory $category, array $overrides = []): array
    {
        return array_merge([
            'date' => '2026-07-11',
            'name' => 'Founding expense',
            'description' => 'Initial setup cost',
            'amount' => '100.00',
            'category_id' => $category->id,
            'payer_type' => 'project',
            'payment_method' => 'cash',
            'notes' => 'Recorded by test',
        ], $overrides);
    }

    /** @return array<string, string> */
    private function paymentPayload(string $amount): array
    {
        return [
            'date' => '2026-07-11',
            'amount' => $amount,
            'payer_type' => 'project',
            'payment_method' => 'cash',
            'notes' => 'Scheduled payment',
        ];
    }
}
