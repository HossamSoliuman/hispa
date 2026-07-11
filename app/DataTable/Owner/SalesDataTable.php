<?php

namespace App\DataTable\Owner;

use App\Models\Sale;
use App\Service\Owner\MonthClosingService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Yajra\DataTables\DataTables;

class SalesDataTable extends DataTables
{
    /**
     * Sum weight per unit and render it as a comma-separated breakdown
     * (e.g. "100.00 كجم، 20.00 شكه"), since weights are never converted between units.
     */
    private function weightBreakdown(Collection $details): string
    {
        $breakdown = $details
            ->groupBy(fn ($detail) => $detail->unit->name ?: __('owner.units.kg'))
            ->map(fn (Collection $group, $unitName) => number_format($group->sum('weight'), 2).' '.$unitName)
            ->implode('، ');

        return $breakdown ?: number_format(0, 2);
    }

    /**
     * The weight-bearing rows for a sale: its own line items when present,
     * otherwise the linked catch's details (older/imported sales carry no line items,
     * so their weight is taken from the catch they were sold from).
     */
    private function effectiveDetails(Sale $sale): Collection
    {
        if ($sale->details->isNotEmpty()) {
            return $sale->details;
        }

        $catch = $sale->catch ?? $sale->trip->catches;

        return $catch?->details ?? collect();
    }

    public function getData(Request $request)
    {

        if ($request->ajax()) {
            $hasFilters = $request->filled('fish_id')
                || $request->filled('from_date')
                || $request->filled('to_date')
                || filled($request->input('search.value'));

            $query = Sale::where('seller_type', 'owner')
                ->where('seller_id', auth()->user()->getAuthIdentifier())
                ->with(['details.unit', 'paymentMethod', 'catch.details.unit', 'trip.catches.details.unit']);

            if (! $hasFilters) {
                app(MonthClosingService::class)->excludeClosedMonths($query, 'sale_datetime', (int) auth()->id());
            }

            if ($request->filled('from_date')) {
                $query->whereDate('sale_datetime', '>=', $request->from_date);
            }

            if ($request->filled('to_date')) {
                $query->whereDate('sale_datetime', '<=', $request->to_date);
            }

            if ($request->filled('fish_id')) {
                $query->whereHas('details', fn ($q) => $q->where('fish_id', $request->fish_id));
            }

            // حساب مجموع السعر
            $totalAmount = $query->sum('total_price');

            // جلب البيانات للـ DataTables
            $sales = $query->get();

            $totalItems = $sales->count();
            $total_sales = $sales->count();

            $allDetails = $sales->flatMap(fn (Sale $sale) => $this->effectiveDetails($sale));
            $total_weight = $allDetails->sum('weight');

            $summary = [
                'total_trips' => $total_sales,
                'total_fish_types' => 0,
                'total_revenue' => $totalAmount,
                'avg_revenue_per_trip' => $total_sales > 0 ? $totalAmount / $total_sales : 0,
                'total_weight_kg' => $total_weight,
                'total_weight_breakdown' => $this->weightBreakdown($allDetails),
                'avg_weight_per_trip_kg' => $total_sales > 0 ? $total_weight / $total_sales : 0,
                'avg_price_per_kg' => $total_weight > 0 ? $totalAmount / $total_weight : 0,
            ];

            return DataTables::of($sales)
                ->addIndexColumn()

                ->addColumn('number', fn ($row) => $row->number)

                ->addColumn('status', function ($row) {
                    $text = Sale::statusText($row->status);
                    $class = match ($row->status) {
                        1 => 'badge bg-warning',
                        2 => 'badge bg-success',
                        default => 'badge bg-secondary',
                    };

                    return '<span class="'.$class.'">'.$text.'</span>';
                })

                ->addColumn('payment_status', function ($row) {
                    $text = Sale::paymentStatusText($row->payment_status);
                    $class = match ($row->payment_status) {
                        'unpaid' => 'badge bg-danger',
                        'partially_paid' => 'badge bg-warning',
                        'paid' => 'badge bg-success',
                        default => 'badge bg-secondary',
                    };

                    return '<span class="'.$class.'">'.$text.'</span>';
                })

                ->addColumn('seller', function ($row) {
                    $name = optional($row->seller)->name ?? '---';
                    $type = $row->seller_type;

                    return match ($type) {
                        'dalal' => '<span class="badge bg-info">'.$name.' - دلال</span>',
                        'owner' => '<span class="badge bg-primary">'.$name.' - صيّاد</span>',
                        default => '<span class="badge bg-secondary">'.$name.' - غير معروف</span>',
                    };
                })

                ->addColumn('customer', fn ($row) => $row->customer_name ?? optional($row->customer)->name)

                ->addColumn('payment_method', fn ($row) => optional($row->paymentMethod)->name)

                ->addColumn('total_weight', fn ($row) => $this->weightBreakdown($this->effectiveDetails($row)))

                ->addColumn('commission_rate', fn ($row) => $row->commission_rate.'%')
                ->addColumn('labor_rate', fn ($row) => $row->labor_rate.'%')

                ->addColumn('total_price', function ($row) {
                    $icon = view('components.riyal-icon', ['size' => 'sm'])->render();

                    return number_format($row->total_price, 2).' <span class="unit">'.$icon.'</span>';
                })
                ->addColumn('net_owner_amount', fn ($row) => number_format($row->net_owner_amount, 2))
                ->addColumn('remaining_total', fn ($row) => number_format($row->remaining_total, 2))

                ->addColumn('date', function ($row) {
                    return $row->sale_datetime
                        ? Carbon::parse($row->sale_datetime)->format('Y-m-d H:i')
                        : '---';
                })

                ->addColumn('actions', function ($row) {
                    return '<div class="d-flex justify-content-center gap-1 text-nowrap">
                            <a href="'.route('owner.sales.show', $row->id).'" class="btn btn-sm btn-outline-info" title="'.__('owner.actions.show').'"><i class="bi bi-eye"></i></a>
                            <a href="'.route('owner.sales.edit', $row->id).'" class="btn btn-sm btn-outline-warning" title="'.__('owner.actions.edit').'"><i class="bi bi-pencil"></i></a>
                            <a href="'.route('owner.sales.print', $row->id).'" target="_blank" class="btn btn-sm btn-outline-secondary" title="'.__('owner.sales.print').'"><i class="bi bi-printer"></i></a>
                            <a href="#" onclick="deleteSaleRecord('.$row->id.'); return false;" class="btn btn-sm btn-outline-danger" title="'.__('owner.actions.delete').'"><i class="bi bi-trash"></i></a>
                        </div>';
                })
                ->with([
                    'total_items' => $totalItems,
                    'total_weight' => round($total_weight, 2),
                    'total_amount' => round($totalAmount, 2),
                    'summary' => $summary,
                ])
                ->rawColumns(['status', 'payment_status', 'seller', 'actions', 'total_price'])
                ->make(true);
        }
    }
}
