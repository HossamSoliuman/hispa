<?php

namespace App\Http\Controllers\Owner\Startup;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\Startup\ExpenseCategoryRequest;
use App\Models\Startup\ExpenseCategory;
use App\Service\Owner\Startup\StartupDefaultsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ExpenseCategoryController extends Controller
{
    public function __construct(private readonly StartupDefaultsService $defaults) {}

    public function index(): View
    {
        $this->defaults->seed();
        $categories = ExpenseCategory::latest()->paginate(25);

        return view('owner.startup.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return $this->index();
    }

    public function store(ExpenseCategoryRequest $r): RedirectResponse
    {
        ExpenseCategory::create($r->validated() + ['is_active' => $r->boolean('is_active')]);

        return back()->with('success', __('owner.startup.saved'));
    }

    public function edit(ExpenseCategory $category): View
    {
        $this->defaults->seed();
        $categories = ExpenseCategory::latest()->paginate(25);

        return view('owner.startup.categories.index', compact('categories', 'category'));
    }

    public function update(ExpenseCategoryRequest $r, ExpenseCategory $category): RedirectResponse
    {
        $category->update($r->validated() + ['is_active' => $r->boolean('is_active')]);

        return redirect()->route('owner.startup.categories.index')->with('success', __('owner.startup.saved'));
    }

    public function destroy(ExpenseCategory $category): RedirectResponse
    {
        if ($category->expenses()->exists()) {
            return back()->withErrors(['category' => __('owner.startup.category_in_use')]);
        }$category->delete();

        return back()->with('success', __('owner.startup.deleted'));
    }
}
