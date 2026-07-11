<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Subscription;
use App\Models\SubscriptionPackage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Admin (SaaS operator) overview: subscriptions health, recurring revenue,
     * invoices/collections, and the two actionable queues — invoices awaiting
     * payment confirmation and subscriptions expiring soon.
     */
    public function index(): View
    {
        $today = Carbon::today();

        // ===== Subscriptions health =====
        $totalSubscriptions = Subscription::count();
        $activeSubscriptions = $this->activeSubscriptionsQuery()->count();
        $pendingSubscriptions = Subscription::where('status', 'pending')->count();
        $trialSubscriptions = Subscription::where('status', 'trial')->count();
        $expiredSubscriptions = Subscription::where(function (Builder $query) use ($today) {
            $query->where('status', 'expired')
                ->orWhere(function (Builder $inner) use ($today) {
                    $inner->where('status', 'active')->where('end_date', '<', $today);
                });
        })->count();

        // ===== Revenue (subscription invoices) =====
        $collectedRevenue = (float) Invoice::where('payment_status', 'paid')->sum('total_amount');
        $pendingRevenue = (float) Invoice::where('payment_status', 'pending')->sum('total_amount');
        $pendingInvoicesCount = Invoice::where('payment_status', 'pending')->count();

        $currentMonthRevenue = (float) Invoice::where('payment_status', 'paid')
            ->whereBetween('paid_at', [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()])
            ->sum('total_amount');
        $previousMonthRevenue = (float) Invoice::where('payment_status', 'paid')
            ->whereBetween('paid_at', [
                $today->copy()->subMonth()->startOfMonth(),
                $today->copy()->subMonth()->endOfMonth(),
            ])->sum('total_amount');
        $revenueGrowth = $this->percentageChange($currentMonthRevenue, $previousMonthRevenue);

        // ===== Customers & plans =====
        $totalOwners = User::where('role', 'owner')->count();
        $activePackages = SubscriptionPackage::where('is_active', true)->count();

        // ===== Recurring revenue =====
        $mrr = $this->monthlyRecurringRevenue($this->activeSubscriptionsQuery()->with('package')->get());
        $mrrHistory = $this->mrrHistory();
        $revenueHistory = $this->collectedRevenueHistory();

        // ===== Subscriptions grouped by plan (doughnut) =====
        $subscriptionsByPlan = Subscription::query()
            ->select('package_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('package_id')
            ->groupBy('package_id')
            ->orderByDesc('total')
            ->with('package:id,name_ar,name_en,boats_count')
            ->get()
            ->map(fn (Subscription $row): array => [
                'name' => $row->package?->name ?? __('admin.dashboard.no_data'),
                'boats_count' => (int) ($row->package?->boats_count ?? 0),
                'total' => (int) $row->total,
            ]);

        // ===== Action needed =====
        $pendingInvoices = Invoice::where('payment_status', 'pending')
            ->with(['user:id,name,phone', 'subscription.package:id,name_ar,name_en'])
            ->latest()
            ->limit(8)
            ->get();

        $expiringSoon = $this->activeSubscriptionsQuery()
            ->whereBetween('end_date', [$today, $today->copy()->addDays(7)])
            ->with(['user:id,name,phone', 'package:id,name_ar,name_en'])
            ->orderBy('end_date')
            ->limit(8)
            ->get();

        return view('admin.dashboard.index', compact(
            'totalSubscriptions',
            'activeSubscriptions',
            'pendingSubscriptions',
            'trialSubscriptions',
            'expiredSubscriptions',
            'collectedRevenue',
            'pendingRevenue',
            'pendingInvoicesCount',
            'currentMonthRevenue',
            'previousMonthRevenue',
            'revenueGrowth',
            'totalOwners',
            'activePackages',
            'mrr',
            'mrrHistory',
            'revenueHistory',
            'subscriptionsByPlan',
            'pendingInvoices',
            'expiringSoon',
        ));
    }

    public function statistics(): RedirectResponse
    {
        return redirect()->route('admin.dashboard');
    }

    /**
     * Base query for subscriptions that are currently live (active, not
     * suspended, still within their period).
     */
    private function activeSubscriptionsQuery(): Builder
    {
        return Subscription::where('status', 'active')
            ->where('is_suspended', false)
            ->where('end_date', '>=', Carbon::today());
    }

    /**
     * Normalize a package's effective price to a monthly figure for MRR.
     */
    private function monthlyValue(SubscriptionPackage $package): float
    {
        $price = (float) $package->effective_price;

        return match ($package->duration_type) {
            'quarterly' => $price / 3,
            'yearly' => $price / 12,
            default => $price,
        };
    }

    /**
     * Monthly Recurring Revenue for a set of active subscriptions.
     *
     * @param  Collection<int, Subscription>  $subscriptions
     */
    private function monthlyRecurringRevenue(Collection $subscriptions): float
    {
        return round($subscriptions->reduce(
            fn (float $carry, Subscription $subscription): float => $subscription->package
                ? $carry + $this->monthlyValue($subscription->package)
                : $carry,
            0.0
        ), 2);
    }

    /**
     * MRR for each of the last 6 months, for the trend chart.
     *
     * @return array<int, array{label: string, mrr: float}>
     */
    private function mrrHistory(): array
    {
        $history = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = $monthStart->copy()->endOfMonth();

            $subscriptions = Subscription::where('status', 'active')
                ->where('is_suspended', false)
                ->where('start_date', '<=', $monthEnd)
                ->where('end_date', '>=', $monthStart)
                ->with('package')
                ->get();

            $history[] = [
                'label' => $monthStart->translatedFormat('M Y'),
                'mrr' => $this->monthlyRecurringRevenue($subscriptions),
            ];
        }

        return $history;
    }

    /**
     * Collected (paid) invoice revenue for each of the last 6 months.
     *
     * @return array<int, array{label: string, revenue: float}>
     */
    private function collectedRevenueHistory(): array
    {
        $history = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = $monthStart->copy()->endOfMonth();

            $revenue = (float) Invoice::where('payment_status', 'paid')
                ->whereBetween('paid_at', [$monthStart, $monthEnd])
                ->sum('total_amount');

            $history[] = [
                'label' => $monthStart->translatedFormat('M Y'),
                'revenue' => round($revenue, 2),
            ];
        }

        return $history;
    }

    /**
     * Month-over-month percentage change; a non-zero current against a zero
     * baseline reads as a full +100% rather than a misleading 0%.
     */
    private function percentageChange(float $current, float $previous): float
    {
        if ($previous > 0.0) {
            return round((($current - $previous) / $previous) * 100, 1);
        }

        return $current > 0.0 ? 100.0 : 0.0;
    }
}
