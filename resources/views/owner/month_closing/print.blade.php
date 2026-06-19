<x-report-layout
    :title="__('owner.month_closing.report_title').' '.sprintf('%02d/%d', $closing->month, $closing->year)"
    :titleEn="app()->getLocale() === 'ar' ? 'Monthly Profit Distribution' : ''"
    :documentNumber="'MC-'.$closing->year.str_pad($closing->month, 2, '0', STR_PAD_LEFT)"
    :settings="$settings ?? []"
>
    <x-slot name="extraStyles">
        .dues-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .dues-table th, .dues-table td { border: 1px solid #e2e8f0; padding: 8px; text-align: center; font-size: 12px; }
        .dues-table th { background: #f1f5f9; }
        .dues-table tfoot td { font-weight: 700; background: #f8f9fa; }
    </x-slot>

    <x-report-header
        :documentNumber="'MC-'.$closing->year.str_pad($closing->month, 2, '0', STR_PAD_LEFT)"
        :title="__('owner.month_closing.report_title').' '.sprintf('%02d/%d', $closing->month, $closing->year)"
        :titleEn="app()->getLocale() === 'ar' ? 'Monthly Profit Distribution' : ''"
        :settings="$settings ?? []"
    />

    <x-report-info :settings="$settings ?? []" />

    <x-report-stats :items="[
        ['label' => __('owner.profit_loss.net_sales'), 'value' => number_format($closing->net_sales, 2), 'color' => '#16a34a', 'accent' => '#16a34a'],
        ['label' => __('owner.profit_loss.total_expenses'), 'value' => number_format($closing->total_expenses, 2), 'color' => '#dc2626', 'accent' => '#dc2626'],
        ['label' => __('owner.profit_loss.net_profit'), 'value' => number_format($closing->net_profit, 2)],
        ['label' => __('owner.generated.owner_ratio'), 'value' => number_format($closing->owner_share, 2), 'color' => '#2563eb', 'accent' => '#2563eb'],
        ['label' => __('owner.profit_loss.crew_share'), 'value' => number_format($closing->crew_share, 2), 'color' => '#d97706', 'accent' => '#d97706'],
    ]" />

    <h3 style="margin-top:25px;">{{ __('owner.month_closing.revenue_details.title') }}</h3>
    <table class="dues-table">
        <thead>
            <tr>
                <th>{{ __('owner.month_closing.revenue_details.date') }}</th>
                <th>{{ __('owner.month_closing.revenue_details.number') }}</th>
                <th>{{ __('owner.month_closing.revenue_details.customer') }}</th>
                <th>{{ __('owner.month_closing.revenue_details.total') }}</th>
                <th>{{ __('owner.month_closing.revenue_details.commission_labor') }}</th>
                <th>{{ __('owner.month_closing.revenue_details.net_owner') }}</th>
                <th>{{ __('owner.month_closing.revenue_details.remaining') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($details['sales'] as $sale)
                <tr>
                    <td>{{ optional($sale->sale_datetime)->format('Y-m-d') }}</td>
                    <td>{{ $sale->number }}</td>
                    <td>{{ $sale->customer_name ?: $sale->customer->name }}</td>
                    <td>{{ number_format((float) $sale->total_price, 2) }}</td>
                    <td>{{ number_format((float) $sale->commission_amount + (float) $sale->labor_amount, 2) }}</td>
                    <td>{{ number_format((float) $sale->net_owner_amount, 2) }}</td>
                    <td>{{ number_format((float) $sale->remaining_total, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">{{ __('owner.month_closing.revenue_details.no_data') }}</td>
                </tr>
            @endforelse
        </tbody>
        @if ($details['sales']->isNotEmpty())
            <tfoot>
                <tr>
                    <td colspan="3">{{ __('owner.month_closing.revenue_details.total_label') }}</td>
                    <td>{{ number_format((float) $details['sales']->sum('total_price'), 2) }}</td>
                    <td>{{ number_format($details['sales']->sum(fn ($s) => (float) $s->commission_amount + (float) $s->labor_amount), 2) }}</td>
                    <td>{{ number_format((float) $details['sales']->sum('net_owner_amount'), 2) }}</td>
                    <td>{{ number_format((float) $details['sales']->sum('remaining_total'), 2) }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

    <h3 style="margin-top:25px;">{{ __('owner.month_closing.expense_details.title') }}</h3>
    <table class="dues-table">
        <thead>
            <tr>
                <th>{{ __('owner.month_closing.expense_details.date') }}</th>
                <th>{{ __('owner.month_closing.expense_details.number') }}</th>
                <th>{{ __('owner.month_closing.expense_details.category') }}</th>
                <th>{{ __('owner.month_closing.expense_details.vendor') }}</th>
                <th>{{ __('owner.month_closing.expense_details.total') }}</th>
                <th>{{ __('owner.month_closing.expense_details.discount') }}</th>
                <th>{{ __('owner.month_closing.expense_details.final') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($details['expenses'] as $expense)
                <tr>
                    <td>{{ \Illuminate\Support\Carbon::parse($expense->date)->format('Y-m-d') }}</td>
                    <td>{{ $expense->number }}</td>
                    <td>{{ $expense->category->name }}</td>
                    <td>{{ optional($expense->vendor)->name ?: '-' }}</td>
                    <td>{{ number_format((float) $expense->total_price, 2) }}</td>
                    <td>{{ number_format((float) $expense->calculated_discount, 2) }}</td>
                    <td>{{ number_format((float) $expense->final_price, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">{{ __('owner.month_closing.expense_details.no_data') }}</td>
                </tr>
            @endforelse
        </tbody>
        @if ($details['expenses']->isNotEmpty())
            <tfoot>
                <tr>
                    <td colspan="4">{{ __('owner.month_closing.expense_details.total_label') }}</td>
                    <td>{{ number_format((float) $details['expenses']->sum('total_price'), 2) }}</td>
                    <td>{{ number_format($details['expenses']->sum(fn ($e) => (float) $e->calculated_discount), 2) }}</td>
                    <td>{{ number_format((float) $details['expenses']->sum('final_price'), 2) }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

    <h3 style="margin-top:25px;">{{ __('owner.month_closing.distribution') }}</h3>
    <table class="dues-table">
        <thead>
            <tr>
                <th>{{ __('owner.month_closing.columns.member') }}</th>
                <th>{{ __('owner.month_closing.columns.role') }}</th>
                <th>{{ __('owner.month_closing.columns.shares') }}</th>
                <th>{{ __('owner.month_closing.columns.custom_percent') }}</th>
                <th>{{ __('owner.month_closing.columns.share_value') }}</th>
                <th>{{ __('owner.month_closing.columns.due') }}</th>
                <th>{{ __('owner.month_closing.columns.advances') }}</th>
                <th>{{ __('owner.month_closing.columns.paid') }}</th>
                <th>{{ __('owner.month_closing.columns.remaining') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($closing->dues as $due)
                <tr>
                    <td>{{ $due->member_name }}</td>
                    <td>{{ $due->role }}</td>
                    <td>{{ $due->custom_share_percent !== null ? '-' : number_format($due->shares, 2) }}</td>
                    <td>{{ $due->custom_share_percent !== null ? number_format($due->custom_share_percent, 2) . '%' : '-' }}</td>
                    <td>{{ number_format($due->share_value, 2) }}</td>
                    <td>{{ number_format($due->due_amount, 2) }}</td>
                    <td>{{ number_format($due->advances, 2) }}</td>
                    <td>{{ number_format($due->paid_amount, 2) }}</td>
                    <td>{{ number_format($due->remaining, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">{{ __('owner.month_closing.status_closed') }}</td>
                <td>{{ number_format($closing->total_shares, 2) }}</td>
                <td></td>
                <td>{{ number_format($closing->share_value, 2) }}</td>
                <td>{{ number_format($closing->dues->sum('due_amount'), 2) }}</td>
                <td>{{ number_format($closing->dues->sum('advances'), 2) }}</td>
                <td>{{ number_format($closing->dues->sum('paid_amount'), 2) }}</td>
                <td>{{ number_format($closing->dues->sum('remaining'), 2) }}</td>
            </tr>
        </tfoot>
    </table>

    @isset($payrollSummary)
        <h3 style="margin-top:25px;">{{ __('owner.month_closing.payroll_summary.title') }}</h3>
        <table class="dues-table">
            <thead>
                <tr>
                    <th>{{ __('owner.month_closing.payroll_summary.type') }}</th>
                    <th>{{ __('owner.month_closing.payroll_summary.people') }}</th>
                    <th>{{ __('owner.month_closing.payroll_summary.net_total') }}</th>
                    <th>{{ __('owner.month_closing.payroll_summary.paid') }}</th>
                    <th>{{ __('owner.month_closing.payroll_summary.status') }}</th>
                    <th>{{ __('owner.month_closing.payroll_summary.paid_at') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach (['percentage' => 'percentage'] as $key => $label)
                    @php $row = $payrollSummary[$key]; @endphp
                    <tr>
                        <td>{{ __('owner.month_closing.payroll_summary.'.$label) }}</td>
                        <td>{{ $row['paid_count'] }} / {{ $row['count'] }}</td>
                        <td>{{ number_format($row['net_total'], 2) }}</td>
                        <td>{{ number_format($row['paid_amount'], 2) }}</td>
                        <td>{{ __('owner.month_closing.payroll_summary.'.$row['status']) }}</td>
                        <td>{{ optional($row['paid_at'])->format('Y-m-d') ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endisset
</x-report-layout>
