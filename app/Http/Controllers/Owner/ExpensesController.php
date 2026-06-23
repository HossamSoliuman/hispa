<?php

namespace App\Http\Controllers\Owner;

use App\DataTable\Owner\ExpensesDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\Expense\StoreExpenseRequest;
use App\Http\Requests\Owner\Expense\UpdateExpenseRequest;
use App\Models\Boat;
use App\Models\Category;
use App\Models\Expense;
use App\Repository\Owner\ExpenseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpensesController extends Controller
{
    private $datatable;

    private $expenseRepository;

    public function __construct()
    {
        $this->datatable = new ExpensesDataTable;
        $this->expenseRepository = new ExpenseRepository;
    }

    public function index()
    {
        $metrics = $this->expenseRepository->indexMetrics();

        $categories = Category::active()
            ->where(function ($query) {
                $query->whereNull('parent_id')
                    ->whereIn('type', ['general', 'government', 'maintenance', 'operating']);
            })
            ->orderBy('parent_id', 'ASC')
            ->orderBy('id', 'ASC')
            ->get();

        $boats = Boat::where('owner_id', auth()->id())->get();

        $analytics = $this->expenseRepository->analytics();

        return view('owner.expenses.index', array_merge($metrics, compact(
            'categories',
            'boats',
            'analytics'
        )));
    }

    public function create()
    {
        $lookups = $this->expenseRepository->createLookups(Auth::id());

        return view('owner.expenses.create', $lookups);
    }

    public function show($id)
    {
        $expense = Expense::where('owner_id', auth()->id())->findOrFail($id);

        return view('owner.expenses.show', compact('expense'));
    }

    public function edit($id)
    {
        $expense = Expense::with(['category', 'boat', 'vendor', 'paymentMethod', 'details.expenseable'])
            ->where('owner_id', auth()->id())
            ->findOrFail($id);

        $lookups = $this->expenseRepository->editLookups($expense, Auth::id());

        return view('owner.expenses.edit', array_merge(
            compact('expense'),
            $lookups
        ));
    }

    public function print(Expense $expense)
    {
        abort_if($expense->owner_id !== (int) auth()->id(), 403);

        // Load company settings and generate QR code (link to the printable expense URL)
        $settings = $this->getCompanySettings();
        $qrCode = $this->generateQRCodeImage(route('owner.expenses.print', $expense->id));

        // If a printable report view exists, render it. Otherwise fall back to the show page.
        if (view()->exists('owner.expenses.print')) {
            return pdf_report(view('owner.expenses.print', compact('expense', 'settings', 'qrCode')), [], 'expense.pdf');
        }

        return view('owner.expenses.show', compact('expense'));
    }

    /**
     * Print the currently filtered expenses listing as a single PDF report.
     * Mirrors the filters applied on the expenses management listing.
     */
    public function printReport(Request $request): \Illuminate\Http\Response
    {
        $expenses = Expense::with(['boat', 'category.parent', 'vendor', 'paymentMethod'])
            ->when($request->filled('boat_id'), fn ($q) => $q->where('boat_id', $request->boat_id))
            ->when($request->filled('category_id'), function ($q) use ($request) {
                $q->whereHas('category', function ($query) use ($request) {
                    $query->where('id', $request->category_id)
                        ->orWhere('parent_id', $request->category_id);
                });
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('from_date'), fn ($q) => $q->whereDate('date', '>=', $request->from_date))
            ->when($request->filled('to_date'), fn ($q) => $q->whereDate('date', '<=', $request->to_date))
            ->orderByDesc('date')
            ->get();

        $totalAmount = $expenses->sum('final_price');

        $statistics = [
            'total_count' => $expenses->count(),
            'total_amount' => $totalAmount,
            'paid_amount' => $expenses->where('status', 'paid')->sum('final_price'),
            'pending_amount' => $expenses->where('status', 'pending')->sum('final_price'),
        ];

        $filters = [
            'category' => $request->filled('category_id')
                ? optional(Category::find($request->category_id))->name
                : null,
            'boat' => $request->filled('boat_id')
                ? optional(Boat::where('owner_id', auth()->id())->find($request->boat_id))->name
                : null,
            'status' => $request->filled('status') ? $request->status : null,
            'from_date' => $request->filled('from_date') ? $request->from_date : null,
            'to_date' => $request->filled('to_date') ? $request->to_date : null,
        ];

        $settings = $this->reportSettings();

        $filename = 'expenses-report-'.($filters['from_date'] ?? 'all').'-to-'.($filters['to_date'] ?? 'all').'.pdf';
        $disposition = $request->boolean('download') ? 'attachment' : 'inline';

        return pdf_report(view('owner.reports.print.expenses-report', compact(
            'expenses',
            'statistics',
            'filters',
            'settings'
        )), [], $filename, $disposition);
    }

    public function store(StoreExpenseRequest $request)
    {
        try {
            $expense = $this->expenseRepository->store($request->validated(), $request->expense_type);

            return response()->json([
                'success' => true,
                'message' => 'تم إضافة المصروف بنجاح',
                'expense_id' => $expense->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إضافة المصروف',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateExpenseRequest $request, $id)
    {
        try {
            $expense = Expense::where('owner_id', auth()->id())->findOrFail($id);
            $expenseType = optional($expense->category->parent)->type ?? $expense->category->type;

            $this->expenseRepository->update($expense, $request->validated(), $expenseType);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث المصروف بنجاح',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث المصروف',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $expense = Expense::where('owner_id', auth()->id())->findOrFail($id);
            $this->expenseRepository->delete($expense);

            return response()->json([
                'success' => true,
                'message' => 'تم حذف المصروف بنجاح',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف المصروف',
            ], 500);
        }
    }

    public function changeStatus(Request $request, Expense $expense)
    {
        abort_if($expense->owner_id !== (int) auth()->id(), 403);

        $request->validate([
            'status' => 'required|in:paid,pending',
        ]);

        $this->expenseRepository->changeStatus($expense, $request->status);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة المصروف بنجاح',
        ]);
    }

    public function getExpenseData(Request $request)
    {
        $query = $this->expenseRepository->expensesQueryForDataTable($request);

        return $this->datatable->getData($query);
    }

    public function getBoats()
    {
        $boats = Boat::where('owner_id', auth()->id())
            ->select('id', 'name_ar', 'name_en')
            ->get();

        return response()->json($boats);
    }

    public function getAvailableMaintenances(Request $request)
    {
        $boatId = $request->get('boat_id');
        $maintenances = $this->expenseRepository->availableMaintenances($boatId, auth()->id());

        return response()->json($maintenances);
    }

    /**
     * Build the company settings array shared by the expenses PDF reports.
     *
     * @return array<string, mixed>
     */
    private function reportSettings(): array
    {
        $companyName = currentCompany()?->name ?: 'حسبة';

        return ownerCompanySettings([
            'qr_code' => app(\App\Service\Owner\ReportQrService::class)->dataUri("Company: {$companyName}"),
        ]);
    }

    // reuse simple company settings & QR helpers (kept local to controller for convenience)
    private function getCompanySettings()
    {
        return ownerCompanySettings([
            'watermark' => public_path('default-logo.png'),
        ]);
    }

    private function generateQRCodeImage($url)
    {
        return app(\App\Service\Owner\ReportQrService::class)->dataUri($url) ?? '';
    }
}
