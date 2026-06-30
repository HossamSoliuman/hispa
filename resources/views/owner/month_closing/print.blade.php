@php
    $isRtl = app()->getLocale() === 'ar';

    $arabicMonths = [
        1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
        5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
        9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر',
    ];
    $monthLabel = $isRtl
        ? ($arabicMonths[$closing->month] ?? $closing->month)
        : \Illuminate\Support\Carbon::create($closing->year, $closing->month, 1)->format('F');
    $boatLabel = $closing->boat?->name ?? __('owner.month_closing.report.all_boats');

    $dues = $closing->dues;
    $operatingExpenses = (float) $closing->general_expenses;
    $companyName = $settings['title'] ?? ($settings['company_name'] ?? ($settings['name'] ?? ''));

    $grossSales = (float) $closing->gross_sales;
    $netSales = (float) $closing->net_sales;
    if ($netSales <= 0 && $grossSales > 0) {
        $netSales = $grossSales;
    }
    $salesReturns = max($grossSales - $netSales, 0);
@endphp

<x-report-layout :title="__('owner.month_closing.title')" :document-number="''" :settings="$settings" :qr-code="$settings['qr_code'] ?? ''" :printable="true">
    <x-report-masthead :title="__('owner.month_closing.title')" :subtitle="__('owner.month_closing.subtitle')" :settings="$settings" />

    {{-- Meta strip --}}
    <div class="meta-row">
        <span class="meta-item">
            <span class="lbl">{{ __('owner.month_closing.report.filter_month') }}:</span>
            <span class="val-box">{{ $monthLabel }}</span>
        </span>
        <span class="meta-item">
            <span class="lbl">{{ __('owner.month_closing.report.filter_year') }}:</span>
            <span class="val-box">{{ $closing->year }}</span>
        </span>
        <span class="meta-item">
            <span class="lbl">{{ __('owner.month_closing.report.filter_closing_date') }}:</span>
            <span class="val-box">{{ optional($closing->closed_at)->format('d/m/Y') ?? '—' }}</span>
        </span>
        <span class="meta-item">
            <span class="lbl">{{ __('owner.month_closing.report.filter_boat') }}:</span>
            <span class="val-box">{{ $boatLabel }}</span>
        </span>
    </div>

    {{-- 5 Summary cards — real monthly waterfall --}}
    <div class="sum-cards">
        <div class="sum-heads">
            <div class="sum-head">1. {{ __('owner.month_closing.report.cards.total_sales') }}</div>
            <div class="sum-head">2. {{ __('owner.month_closing.report.cards.total_expenses') }}</div>
            <div class="sum-head">3. {{ __('owner.month_closing.report.cards.profit_before') }}</div>
            <div class="sum-head">4. {{ __('owner.month_closing.report.cards.owner_deductions') }}</div>
            <div class="sum-head">5. {{ __('owner.month_closing.report.cards.net_distributable') }}</div>
        </div>
        <div class="sum-bodies">
            {{-- Card 1 — gross sales → returns → net sales --}}
            <div class="sum-body">
                <div class="sum-row"><span class="sum-k">{{ __('owner.month_closing.report.rows.gross_sales') }}</span><span class="sum-v">{{ number_format($grossSales, 2) }}</span></div>
                <div class="sum-row"><span class="sum-k">{{ __('owner.month_closing.report.rows.returns') }}</span><span class="sum-v">{{ number_format($salesReturns, 2) }}</span></div>
                <div class="sum-row sum-total"><span class="sum-k">{{ __('owner.month_closing.report.rows.net_sales') }}</span><span class="sum-v">{{ number_format($netSales, 2) }}</span></div>
            </div>
            {{-- Card 2 — expenses --}}
            <div class="sum-body">
                <div class="sum-row"><span class="sum-k">{{ __('owner.month_closing.report.rows.trip_expenses') }}</span><span class="sum-v">{{ number_format((float) $closing->trip_expenses, 2) }}</span></div>
                <div class="sum-row"><span class="sum-k">{{ __('owner.month_closing.report.rows.operational_expenses') }}</span><span class="sum-v">{{ number_format($operatingExpenses, 2) }}</span></div>
                <div class="sum-row"><span class="sum-k">{{ __('owner.month_closing.report.rows.depreciation') }}</span><span class="sum-v">{{ number_format((float) $closing->depreciation, 2) }}</span></div>
                <div class="sum-row sum-total"><span class="sum-k">{{ __('owner.month_closing.report.rows.expenses') }}</span><span class="sum-v">{{ number_format((float) $closing->total_expenses, 2) }}</span></div>
            </div>
            {{-- Card 3 — profit before distribution --}}
            <div class="sum-body">
                <div class="sum-row"><span class="sum-k">{{ __('owner.month_closing.report.rows.net_owner_revenue') }}</span><span class="sum-v">{{ number_format((float) $closing->net_owner_revenue, 2) }}</span></div>
                <div class="sum-row"><span class="sum-k">{{ __('owner.month_closing.report.rows.total_expenses') }}</span><span class="sum-v">{{ number_format((float) $closing->total_expenses, 2) }}</span></div>
                <div class="sum-row sum-total"><span class="sum-k">{{ __('owner.month_closing.report.rows.profit_before') }}</span><span class="sum-v">{{ number_format((float) $closing->net_profit, 2) }}</span></div>
            </div>
            {{-- Card 4 — owner share --}}
            <div class="sum-body">
                <div class="sum-row"><span class="sum-k">{{ __('owner.month_closing.report.rows.profit_before') }}</span><span class="sum-v">{{ number_format((float) $closing->net_profit, 2) }}</span></div>
                <div class="sum-row"><span class="sum-k">{{ __('owner.month_closing.report.rows.owner_percent') }}</span><span class="sum-v">{{ number_format((float) $closing->owner_percent, 2) }}%</span></div>
                <div class="sum-row sum-total"><span class="sum-k">{{ __('owner.month_closing.report.rows.owner_share') }}</span><span class="sum-v">{{ number_format((float) $closing->owner_share, 2) }}</span></div>
            </div>
            {{-- Card 5 — distributable crew share --}}
            <div class="sum-body">
                <div class="sum-row"><span class="sum-k">{{ __('owner.month_closing.report.rows.profit_before') }}</span><span class="sum-v">{{ number_format((float) $closing->net_profit, 2) }}</span></div>
                <div class="sum-row"><span class="sum-k">{{ __('owner.month_closing.report.rows.owner_share') }}</span><span class="sum-v">{{ number_format((float) $closing->owner_share, 2) }}</span></div>
                <div class="sum-row sum-total"><span class="sum-k">{{ __('owner.month_closing.report.rows.final_distributable') }}</span><span class="sum-v">{{ number_format((float) $closing->crew_share, 2) }}</span></div>
            </div>
        </div>
    </div>

    {{-- Distribution table --}}
    <div class="section-bar">6. {{ __('owner.month_closing.report.distribution_title') }}</div>
    <table class="report-table" style="margin-bottom:14px;">
        <thead>
            <tr>
                <th style="width:5%">{{ __('owner.month_closing.report.dist.index') }}</th>
                <th style="width:18%">{{ __('owner.month_closing.report.dist.name') }}</th>
                <th>{{ __('owner.month_closing.report.dist.role') }}</th>
                <th>{{ __('owner.month_closing.report.dist.share') }}</th>
                <th>{{ __('owner.month_closing.report.dist.earned') }}</th>
                <th>{{ __('owner.month_closing.report.dist.advance') }}</th>
                <th>{{ __('owner.month_closing.report.dist.paid') }}</th>
                <th>{{ __('owner.month_closing.report.dist.net_due') }}</th>
                <th style="width:10%">{{ __('owner.month_closing.report.dist.signature') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($dues as $due)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="col-text">{{ $due->member_name }}</td>
                    <td>{{ $due->role }}</td>
                    <td>{{ $due->custom_share_percent !== null ? number_format((float) $due->custom_share_percent, 2).'%' : number_format((float) $due->shares, 2) }}</td>
                    <td class="col-num">{{ number_format((float) $due->due_amount, 2) }}</td>
                    <td class="col-num">{{ number_format((float) $due->advances, 2) }}</td>
                    <td class="col-num">{{ number_format((float) $due->paid_amount, 2) }}</td>
                    <td class="col-num">{{ number_format((float) $due->remaining, 2) }}</td>
                    <td style="color:#bbb;white-space:nowrap;">..........</td>
                </tr>
            @empty
                <tr><td colspan="9" class="text-center text-muted">{{ __('owner.month_closing.revenue_details.no_data') }}</td></tr>
            @endforelse
        </tbody>
        @if ($dues->isNotEmpty())
            <tfoot>
                <tr>
                    <td colspan="3" class="col-text">{{ __('owner.month_closing.report.dist.total') }}</td>
                    <td>{{ number_format((float) $closing->total_shares, 2) }}</td>
                    <td class="col-num">{{ number_format((float) $dues->sum('due_amount'), 2) }}</td>
                    <td class="col-num">{{ number_format((float) $dues->sum('advances'), 2) }}</td>
                    <td class="col-num">{{ number_format((float) $dues->sum('paid_amount'), 2) }}</td>
                    <td class="col-num">{{ number_format((float) $dues->sum('remaining'), 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        @endif
    </table>

    {{-- Closing footer: summary grid / sign-off / notes --}}
    <table class="close-footer">
        <tr>
            <td class="cf-col">
                <table class="report-table info-box">
                    <thead>
                        <tr><th colspan="2">{{ __('owner.month_closing.report.closing_summary_title') }}</th></tr>
                    </thead>
                    <tbody>
                        <tr><td class="col-text" style="width:64%">{{ __('owner.month_closing.report.crew_share') }}</td><td class="col-num" style="width:36%">{{ number_format((float) $closing->crew_share, 2) }}</td></tr>
                        <tr><td class="col-text">{{ __('owner.month_closing.report.owner_share') }}</td><td class="col-num">{{ number_format((float) $closing->owner_share, 2) }}</td></tr>
                        <tr><td class="col-text">{{ __('owner.month_closing.report.share_value') }}</td><td class="col-num">{{ number_format((float) $closing->share_value, 2) }}</td></tr>
                        <tr><td class="col-text">{{ __('owner.month_closing.report.total_advances') }}</td><td class="col-num">{{ number_format((float) $dues->sum('advances'), 2) }}</td></tr>
                        <tr><td class="col-text">{{ __('owner.month_closing.report.total_paid') }}</td><td class="col-num">{{ number_format((float) $dues->sum('paid_amount'), 2) }}</td></tr>
                        <tr class="net-row"><td class="col-text">{{ __('owner.month_closing.report.total_net_due') }}</td><td class="col-num">{{ number_format((float) $dues->sum('remaining'), 2) }}</td></tr>
                    </tbody>
                </table>
            </td>
            <td class="cf-gap"></td>
            <td class="cf-col">
                <div class="cf-card">
                    <div class="cf-head">{{ __('owner.month_closing.report.signoff_title') }}</div>
                    <div class="cf-body">
                        <div class="cf-signoff-label">{{ __('owner.month_closing.report.signoff_prepared') }}</div>
                        <div class="cf-signoff-line">..................</div>
                        <div class="cf-signoff-label">{{ __('owner.month_closing.report.signoff_accountant') }}</div>
                        <div class="cf-signoff-line">..................</div>
                        <div class="cf-signoff-label">{{ __('owner.month_closing.report.signoff_owner') }}</div>
                        <div class="cf-signoff-line">..................</div>
                        <div class="cf-signoff-label">{{ __('owner.month_closing.report.filter_closing_date') }}</div>
                        <div class="cf-signoff-date">{{ optional($closing->closed_at)->format('d/m/Y') ?? '—' }}</div>
                    </div>
                </div>
            </td>
            <td class="cf-gap"></td>
            <td class="cf-col">
                <div class="cf-card">
                    <div class="cf-head">{{ __('owner.month_closing.report.notes_title') }}</div>
                    <div class="cf-body">
                        <ul class="cf-notes">
                            @foreach (__('owner.month_closing.report.notes') as $note)
                                <li>{{ $note }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    {{-- Document footer --}}
    <table class="report-footer">
        <tr>
            <td>{{ $companyName }} — {{ __('owner.reports.all_rights_reserved') }} © {{ $closing->year }}</td>
        </tr>
    </table>
</x-report-layout>
