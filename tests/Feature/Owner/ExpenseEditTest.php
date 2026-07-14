<?php

namespace Tests\Feature\Owner;

use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ExpenseEditTest extends TestCase
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

    public function test_owner_can_edit_an_expense_in_a_top_level_category(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $owner->assignRole('owner');
        $category = Category::create([
            'name_ar' => 'مصروفات عامة',
            'name_en' => 'General expenses',
            'type' => 'general',
            'status' => 1,
            'owner_id' => $owner->id,
        ]);
        $expense = Expense::create([
            'date' => now()->toDateString(),
            'number' => 'EXP-'.uniqid(),
            'owner_id' => $owner->id,
            'category_id' => $category->id,
            'total_price' => 100,
            'final_price' => 100,
            'status' => 'pending',
        ]);

        $this->actingAs($owner, 'owner');

        $this->get(route('owner.expenses.edit', $expense))
            ->assertOk()
            ->assertSee('EXP-');
    }
}
