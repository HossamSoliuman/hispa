<?php

namespace App\Repository\Owner;

use App\Models\Boat;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Expenseable;
use App\Models\ExpenseFishingEquipment;
use App\Models\FishingEquipment;
use App\Models\Maintenance;
use App\Models\PaymentMethod;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ExpenseRepository
{
    public function indexMetrics(): array
    {
        $count = Expense::count();
        $totalAmount = Expense::sum('final_price');
        $paidAmount = Expense::where('status', 'paid')->sum('final_price');
        $pendingAmount = Expense::where('status', 'pending')->sum('final_price');
        $avgPerExpense = $count > 0 ? $totalAmount / $count : 0;

        $topCategory = Expense::select('category_id')
            ->selectRaw('SUM(final_price) as total, COUNT(*) as expenses_count, AVG(final_price) as avg')
            ->with('category:id,name_ar,name_en')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->first();

        $topBoat = Expense::select('boat_id')
            ->selectRaw('SUM(final_price) as total, COUNT(*) as expenses_count')
            ->with('boat:id,name_ar,name_en')
            ->groupBy('boat_id')
            ->orderByDesc('total')
            ->first();

        $topStatus = Expense::select('status')
            ->selectRaw('COUNT(*) as count, SUM(final_price) as total')
            ->groupBy('status')
            ->orderByDesc('count')
            ->first();

        $categoriesCount = Expense::distinct('category_id')->count('category_id');

        $paymentCompletionRate = $totalAmount > 0
            ? round(($paidAmount / $totalAmount) * 100, 2)
            : 0;
        $categoriesRate = Category::whereNull('parent_id')
            ->withCount('expenses as direct_expenses_count')
            ->withCount('children as children_count')
            ->withCount('allExpenses as expenses_count')
            ->withSum('allExpenses as total_amount', 'final_price')
            ->get();

        return compact(
            'count',
            'totalAmount',
            'paidAmount',
            'pendingAmount',
            'avgPerExpense',
            'topCategory',
            'topBoat',
            'topStatus',
            'categoriesCount',
            'paymentCompletionRate',
            'categoriesRate'
        );
    }

    public function createLookups(int $ownerId): array
    {
        $boats = Boat::where('owner_id', $ownerId)->get();
        $categories = Category::active()
            ->whereNull('parent_id')
            ->whereIn('type', ['general', 'operating', 'government', 'maintenance'])
            ->get();
        $categories_general = $this->getChildrenByParentType('general');
        $categories_government = $this->getChildrenByParentType('government');
        $categories_operating = $this->getChildrenByParentType('operating');
        $paymentMethods = PaymentMethod::active()->get();
        $vendors = User::where('role', 'vendor')->where('owner_id', $ownerId)->get(['id', 'name']);

        return compact('boats', 'categories', 'categories_general', 'categories_government', 'categories_operating', 'paymentMethods', 'vendors');
    }

    public function getChildrenByParentType($type)
    {
        $parentIds = Category::where('type', $type)
            ->whereNull('parent_id')
            ->pluck('id');

        return Category::whereIn('parent_id', $parentIds)->get();
    }

    public function editLookups(Expense $expense, int $ownerId): array
    {
        $categories = collect();
        $fishingEquipments = collect();
        $maintenances = collect();

        if ($expense->category->type === 'general') {
            $categories = Category::active()
                ->where('type', 'general')
                ->whereNotNull('parent_id')
                ->get();
        } elseif ($expense->category->type === 'equipment') {
            $fishingEquipments = FishingEquipment::where('owner_id', $ownerId)->active()->get();
        } elseif ($expense->category->type === 'maintenance') {
            $maintenances = Maintenance::where('owner_id', $ownerId)
                ->where('boat_id', $expense->boat_id)
                ->get();
        }

        $vendors = User::where('role', 'vendor')
            ->where('owner_id', $ownerId)
            ->get();

        $paymentMethods = PaymentMethod::active()->get();
        $boats = Boat::where('owner_id', $ownerId)->get();

        return compact('categories', 'fishingEquipments', 'maintenances', 'vendors', 'paymentMethods', 'boats');
    }

    public function availableMaintenances(?int $boatId, int $ownerId)
    {
        if (! $boatId || $boatId === 'general') {
            return collect([]);
        }

        return Maintenance::with('category')
            ->where('owner_id', $ownerId)
            ->where('boat_id', $boatId)
            ->get();
    }

    /**
     * Create expense records from the quick-expense rows entered while creating a trip.
     *
     * @param  array<int, array{category_id?: int|string|null, vendor_id?: int|string|null, amount?: mixed}>  $rows
     */
    public function createQuickExpensesForTrip(Trip $trip, array $rows, string $status = 'pending'): void
    {
        $date = $trip->start_date?->toDateString() ?? now()->toDateString();
        $status = in_array($status, ['paid', 'pending'], true) ? $status : 'pending';

        DB::transaction(function () use ($trip, $rows, $status, $date) {
            foreach ($rows as $row) {
                $amount = (float) ($row['amount'] ?? 0);

                if ($amount <= 0) {
                    continue;
                }

                Expense::create([
                    'date' => $date,
                    'number' => $this->generateExpenseNumber(),
                    'notes' => __('owner.trips.quick_expenses.title').' - '.$trip->number,
                    'owner_id' => $trip->owner_id,
                    'boat_id' => $trip->boat_id,
                    'trip_id' => $trip->id,
                    'category_id' => ! empty($row['category_id']) ? (int) $row['category_id'] : null,
                    'vendor_id' => ! empty($row['vendor_id']) ? (int) $row['vendor_id'] : null,
                    'total_price' => $amount,
                    'discount_type' => null,
                    'discount_value' => 0,
                    'final_price' => $amount,
                    'vat_rate' => 0,
                    'status' => $status,
                ]);
            }
        });
    }

    public function store(array $data, string $expenseType): Expense
    {
        return DB::transaction(function () use ($data, $expenseType) {
            $expenseNumber = $this->generateExpenseNumber();

            $expenseData = [
                'date' => $data['date'],
                'number' => $expenseNumber,
                'notes' => $data['notes'] ?? null,
                'owner_id' => auth()->user()->id,
                'boat_id' => $data['boat_id'] ?? null,
                'vendor_id' => $data['vendor_id'] ?? null,
                'payment_method_id' => $data['payment_method_id'] ?? null,
                'status' => $data['status'] ?? 'pending',
            ];

            if (! empty($data['attachment'])) {
                $path = UploadFile($data['attachment'], 'uploads/expense/attachments');
                $expenseData['attachment'] = $path;
            }

            return match ($expenseType) {
                'general' => $this->handleGeneral($data, $expenseData),
                'government' => $this->handleGeneral($data, $expenseData),
                // 'equipment'   => $this->handleEquipment($data, $expenseData),
                'operating' => $this->handleOperating($data, $expenseData),
                'maintenance' => $this->handleMaintenance($data, $expenseData),
                default => throw new \InvalidArgumentException('Invalid expense type'),
            };
        });
    }

    public function update(Expense $expense, array $data, string $expenseType): void
    {
        DB::transaction(function () use ($expense, $data, $expenseType) {

            $expenseData = [
                'date' => $data['date'],
                'notes' => $data['notes'] ?? null,
                'vendor_id' => $data['vendor_id'] ?? null,
                'payment_method_id' => $data['payment_method_id'] ?? null,
                'status' => $data['status'] ?? 'pending',
            ];

            if (! empty($data['attachment'])) {
                if (! is_null($expense->getRawOriginal('attachment'))) {
                    deleteFile($expense->getRawOriginal('attachment'));
                }
                $path = UploadFile($data['attachment'], 'uploads/expense/attachments');
                $expenseData['attachment'] = $path;
            }

            if ($expenseType === 'general' || $expenseType === 'government' || ($expenseType === 'operating' && $expense->category->type != 'operating-equipments')) {
                $total = (float) ($data['total_price'] ?? 0);
                $calc = $this->calculateFinalPrice($total, $data['discount_type'] ?? null, (float) ($data['discount_value'] ?? 0));

                $expense->update(array_merge($expenseData, [
                    'total_price' => $total,
                    'discount_type' => ($data['discount_type'] ?? 'none') !== 'none' ? $data['discount_type'] : null,
                    'discount_value' => ($data['discount_type'] ?? 'none') !== 'none' ? (float) ($data['discount_value'] ?? 0) : 0,
                    'final_price' => $calc['final_price'],
                    'vat_rate' => $calc['vat_rate'],
                ]));
            } elseif ($expenseType === 'maintenance') {
                $total = (float) ($data['estimated_cost'] ?? 0);
                $calc = $this->calculateFinalPrice($total, $data['discount_type'] ?? null, (float) ($data['discount_value'] ?? 0));

                $expense->update(array_merge($expenseData, [
                    'total_price' => $total,
                    'discount_type' => ($data['discount_type'] ?? 'none') !== 'none' ? $data['discount_type'] : null,
                    'discount_value' => ($data['discount_type'] ?? 'none') !== 'none' ? (float) ($data['discount_value'] ?? 0) : 0,
                    'final_price' => $calc['final_price'],
                    'vat_rate' => $calc['vat_rate'],
                ]));

                if (! empty($data['maintenance_id'])) {
                    $maintenance = Maintenance::findOrFail($data['maintenance_id']);
                    $maintenance->update([
                        'estimated_cost' => $data['estimated_cost'] ?? 0,
                    ]);
                }
            } elseif ($expenseType === 'operating' && $expense->category->type == 'operating-equipments') {
                $grandTotal = 0;
                $items = [];

                $ids = $data['fishing_equipment_id'] ?? [];
                $quant = $data['quantity'] ?? [];
                $unitPrice = $data['unit_price'] ?? [];

                foreach ($ids as $i => $equipId) {
                    $q = (float) ($quant[$i] ?? 0);
                    $p = (float) ($unitPrice[$i] ?? 0);
                    $totalPrice = $q * $p;
                    $grandTotal += $totalPrice;

                    $items[] = [
                        'fishing_equipment_id' => $equipId,
                        'quantity' => $q,
                        'unit_price' => $p,
                        'total_price' => $totalPrice,
                    ];
                }
                $calc = $this->calculateFinalPrice($grandTotal, $data['discount_type'] ?? null, (float) ($data['discount_value'] ?? 0));

                foreach ($expense->details as $detail) {
                    $detail->expenseable()->delete();
                    $detail->delete();
                }
                $expense->update(array_merge($expenseData, [
                    'total_price' => $grandTotal,
                    'discount_type' => ($data['discount_type'] ?? 'none') !== 'none' ? $data['discount_type'] : null,
                    'discount_value' => ($data['discount_type'] ?? 'none') !== 'none' ? (float) ($data['discount_value'] ?? 0) : 0,
                    'final_price' => $calc['final_price'],
                    'vat_rate' => $calc['vat_rate'],
                ]));
                foreach ($items as $item) {
                    $equipment = ExpenseFishingEquipment::create($item);
                    Expenseable::create([
                        'expense_id' => $expense->id,
                        'expenseable_type' => ExpenseFishingEquipment::class,
                        'expenseable_id' => $equipment->id,
                    ]);
                }
            }
        });
    }

    public function delete(Expense $expense): void
    {
        DB::transaction(function () use ($expense) {
            if ($expense->details()->exists()) {

                foreach ($expense->details as $detail) {
                    $detail->expenseable()->delete();
                    $detail->delete();
                }
            }
            $expense->delete();
        });
    }

    public function expensesQueryForDataTable($request)
    {
        return Expense::with(['boat', 'category', 'vendor', 'paymentMethod'])
            ->when($request->boat_id, fn ($q) => $q->where('boat_id', $request->boat_id))
            ->when($request->category_id, function ($q) use ($request) {
                $q->whereHas('category', function ($query) use ($request) {
                    $query->where('id', $request->category_id)
                        ->orWhere('parent_id', $request->category_id);
                });
            })
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->from_date, fn ($q) => $q->whereDate('date', '>=', $request->from_date))
            ->when($request->to_date, fn ($q) => $q->whereDate('date', '<=', $request->to_date))
            ->orderByDesc('created_at');
    }

    public function changeStatus(Expense $expense, string $status): void
    {
        $expense->update(['status' => $status]);
    }

    private function handleGeneral(array $data, array $expenseData): Expense
    {
        $total = (float) ($data['total_price'] ?? 0);
        $calc = $this->calculateFinalPrice($total, $data['discount_type'] ?? null, (float) ($data['discount_value'] ?? 0));

        return Expense::create(array_merge($expenseData, [
            'category_id' => $data['category_id'],
            'total_price' => $total,
            'discount_type' => ($data['discount_type'] ?? 'none') !== 'none' ? $data['discount_type'] : null,
            'discount_value' => ($data['discount_type'] ?? 'none') !== 'none' ? (float) ($data['discount_value'] ?? 0) : 0,
            'final_price' => $calc['final_price'],
            'vat_rate' => $calc['vat_rate'],
        ]));
    }

    private function handleOperating(array $data, array $expenseData): Expense
    {
        if (! empty($data['fishing_equipment_id'])) {
            return $this->handleOperatingEquipment($data, $expenseData);
        }

        $total = (float) ($data['total_price_operating'] ?? 0);

        $calc = $this->calculateFinalPrice(
            $total,
            $data['discount_type_operating'] ?? null,
            (float) ($data['discount_value_operating'] ?? 0)
        );

        return Expense::create(array_merge($expenseData, [
            'category_id' => $data['category_id'],
            'total_price' => $total,
            'discount_type' => ($data['discount_type_operating'] ?? 'none') !== 'none' ? $data['discount_type_operating'] : null,
            'discount_value' => ($data['discount_type_operating'] ?? 'none') !== 'none' ? (float) ($data['discount_value_operating'] ?? 0) : 0,
            'final_price' => $calc['final_price'],
            'vat_rate' => $calc['vat_rate'],
        ]));
    }

    private function handleOperatingEquipment(array $data, array $expenseData): Expense
    {
        $grandTotal = 0;
        $items = [];

        $ids = $data['fishing_equipment_id'] ?? [];
        $quant = $data['quantity'] ?? [];
        $unitPrice = $data['unit_price'] ?? [];

        foreach ($ids as $i => $id) {
            $q = (float) ($quant[$i] ?? 0);
            $p = (float) ($unitPrice[$i] ?? 0);
            $totalPrice = $q * $p;
            $grandTotal += $totalPrice;

            $items[] = [
                'fishing_equipment_id' => $id,
                'quantity' => $q,
                'unit_price' => $p,
                'total_price' => $totalPrice,
            ];
        }

        $calc = $this->calculateFinalPrice(
            $grandTotal,
            $data['discount_type_operating'] ?? null,
            (float) ($data['discount_value_operating'] ?? 0)
        );

        $expense = Expense::create(array_merge($expenseData, [
            'category_id' => $data['category_id'],
            'total_price' => $grandTotal,
            'discount_type' => ($data['discount_type_operating'] ?? 'none') !== 'none' ? $data['discount_type_operating'] : null,
            'discount_value' => ($data['discount_type_operating'] ?? 'none') !== 'none'
                ? (float) ($data['discount_value_operating'] ?? 0) : 0,
            'final_price' => $calc['final_price'],
            'vat_rate' => $calc['vat_rate'],
        ]));

        foreach ($items as $item) {
            $equipment = ExpenseFishingEquipment::create($item);
            Expenseable::create([
                'expense_id' => $expense->id,
                'expenseable_type' => ExpenseFishingEquipment::class,
                'expenseable_id' => $equipment->id,
            ]);
        }

        return $expense;
    }

    private function handleMaintenance(array $data, array $expenseData): Expense
    {
        $ids = $data['selected_maintenances'] ?? [];
        $maintenances = Maintenance::whereIn('id', $ids)->get();
        $totalCost = $maintenances->sum('estimated_cost');

        $calc = $this->calculateFinalPrice(
            $totalCost,
            $data['discount_type_maintenance'] ?? null,
            (float) ($data['discount_value_maintenance'] ?? 0)
        );

        // $category = Category::where('type', 'maintenance')->whereNull('parent_id')->first();
        $category_id = $maintenances->first()->category_id;

        $expense = Expense::create(array_merge($expenseData, [
            'total_price' => $totalCost,
            'category_id' => $category_id,
            'discount_type' => ($data['discount_type_maintenance'] ?? 'none') !== 'none' ? $data['discount_type_maintenance'] : null,
            'discount_value' => ($data['discount_value_maintenance'] ?? 0) && ($data['discount_type_maintenance'] ?? 'none') !== 'none'
                ? (float) $data['discount_value_maintenance'] : 0,
            'final_price' => $calc['final_price'],
            'vat_rate' => $calc['vat_rate'],
        ]));

        return $expense;
    }

    private function calculateFinalPrice(float $total, ?string $type, float $value): array
    {
        // Apply discount
        if ($type === 'percentage') {
            $total -= ($total * $value / 100);
        } elseif ($type === 'fixed') {
            $total -= $value;
        }

        return [
            'final_price' => round($total, 2),
            'vat_rate' => 0,
        ];
    }

    private function generateExpenseNumber(): string
    {
        $prefix = 'EXP-'.date('Y').'-';
        $last = Expense::where('number', 'like', "$prefix%")->orderBy('id', 'desc')->first();
        $num = $last ? (int) substr($last->number, strlen($prefix)) + 1 : 1;

        return $prefix.str_pad((string) $num, 4, '0', STR_PAD_LEFT);
    }

    public function analytics(): array
    {
        $expensesByCategory = Expense::with('category.parent')
            ->select('category_id', DB::raw('SUM(final_price) as total'))
            ->groupBy('category_id')
            ->get()
            ->groupBy(fn ($row) => $row->category?->parent?->id ?? $row->category?->id)
            ->map(function ($group) {
                $parent = $group->first()->category->parent ?? $group->first()->category;

                return [
                    'category' => $parent->name ?: 'غير محدد',
                    'total' => $group->sum('total'),
                ];
            })
            ->values();

        $expensesByBoat = Expense::with('boat:id,name_ar,name_en')
            ->select('boat_id', DB::raw('SUM(final_price) as total'))
            ->groupBy('boat_id')
            ->get()
            ->map(fn ($row) => [
                'boat' => $row->boat?->name ?? 'عام',
                'total' => $row->total,
            ]);

        $isMySQL = DB::connection()->getDriverName() === 'mysql';
        $monthExpr = $isMySQL
            ? "DATE_FORMAT(date, '%Y-%m') as month"
            : "strftime('%Y-%m', date) as month";

        $monthlyTrends = Expense::select(
            DB::raw($monthExpr),
            DB::raw('SUM(final_price) as total'),
            DB::raw('COUNT(*) as count')
        )
            ->groupBy(DB::raw($isMySQL ? "DATE_FORMAT(date, '%Y-%m')" : "strftime('%Y-%m', date)"))
            ->orderBy('month')
            ->get();

        return compact('expensesByCategory', 'expensesByBoat', 'monthlyTrends');
    }
}
