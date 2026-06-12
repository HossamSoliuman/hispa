<x-report-layout
    :title="__('owner.month_closing.report_title').' '.sprintf('%02d/%d', $closing->month, $closing->year)"
    :titleEn="'Monthly Profit Distribution'"
    :documentNumber="'MC-'.$closing->year.str_pad($closing->month, 2, '0', STR_PAD_LEFT)"
    :settings="$settings ?? []"
>
    <x-slot name="extraStyles">
        .summary-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 12px; margin: 20px 0; }
        .summary-card { background: #f8f9fa; padding: 12px; border-radius: 8px; border-left: 4px solid #16a34a; }
        .summary-card .label { font-size: 11px; color: #64748b; font-weight: 600; margin-bottom: 6px; }
        .summary-card .amount { font-size: 16px; font-weight: 700; }
        .dues-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .dues-table th, .dues-table td { border: 1px solid #e2e8f0; padding: 8px; text-align: center; font-size: 12px; }
        .dues-table th { background: #f1f5f9; }
        .dues-table tfoot td { font-weight: 700; background: #f8f9fa; }
    </x-slot>

    <x-report-header
        :documentNumber="'MC-'.$closing->year.str_pad($closing->month, 2, '0', STR_PAD_LEFT)"
        :title="__('owner.month_closing.report_title').' '.sprintf('%02d/%d', $closing->month, $closing->year)"
        :titleEn="'Monthly Profit Distribution'"
        :settings="$settings ?? []"
    />

    <x-report-info :settings="$settings ?? []" />

    <div class="summary-grid">
        <div class="summary-card">
            <div class="label">{{ __('owner.profit_loss.net_sales') }}</div>
            <div class="amount" style="color:#16a34a;">{{ number_format($closing->net_sales, 2) }}</div>
        </div>
        <div class="summary-card" style="border-color:#dc2626;">
            <div class="label">{{ __('owner.profit_loss.total_expenses') }}</div>
            <div class="amount" style="color:#dc2626;">{{ number_format($closing->total_expenses, 2) }}</div>
        </div>
        <div class="summary-card">
            <div class="label">{{ __('owner.profit_loss.net_profit') }}</div>
            <div class="amount">{{ number_format($closing->net_profit, 2) }}</div>
        </div>
        <div class="summary-card" style="border-color:#2563eb;">
            <div class="label">{{ __('owner.generated.owner_ratio') }}</div>
            <div class="amount" style="color:#2563eb;">{{ number_format($closing->owner_share, 2) }}</div>
        </div>
        <div class="summary-card" style="border-color:#d97706;">
            <div class="label">{{ __('owner.profit_loss.crew_share') }}</div>
            <div class="amount" style="color:#d97706;">{{ number_format($closing->crew_share, 2) }}</div>
        </div>
    </div>

    <h3 style="margin-top:25px;">{{ __('owner.month_closing.distribution') }}</h3>
    <table class="dues-table">
        <thead>
            <tr>
                <th>{{ __('owner.month_closing.columns.member') }}</th>
                <th>{{ __('owner.month_closing.columns.role') }}</th>
                <th>{{ __('owner.month_closing.columns.shares') }}</th>
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
                    <td>{{ number_format($due->shares, 2) }}</td>
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
                @foreach (['salary' => 'fixed', 'percentage' => 'percentage'] as $key => $label)
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
