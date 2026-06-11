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
            return view('owner.expenses.print', compact('expense', 'settings', 'qrCode'));
        }

        return view('owner.expenses.show', compact('expense'));
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
            ->select('id', 'name_ar')
            ->get();

        return response()->json($boats);
    }

    public function getAvailableMaintenances(Request $request)
    {
        $boatId = $request->get('boat_id');
        $maintenances = $this->expenseRepository->availableMaintenances($boatId, auth()->id());

        return response()->json($maintenances);
    }

    // reuse simple company settings & QR helpers (kept local to controller for convenience)
    private function getCompanySettings()
    {
        $user = auth()->user();
        $logoPath = public_path('default-logo.png');

        return [
            'title' => $user->company_name ?? $user->name ?? config('app.name'),
            'company_name' => $user->company_name ?? $user->name ?? config('app.name'),
            'logo' => $logoPath,
            'watermark' => $logoPath,
            'phone' => $user->phone ?? '',
            'email' => $user->email ?? '',
            'address' => $user->address ?? '',
        ];
    }

    private function generateQRCodeImage($url)
    {
        return app(\App\Service\Owner\ReportQrService::class)->dataUri($url) ?? '';
    }
}
