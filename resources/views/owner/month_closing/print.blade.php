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
@endphp

<x-report-layout :title="__('owner.month_closing.title')" :document-number="''" :settings="$settings" :qr-code="$settings['qr_code'] ?? ''">
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
    <table class="sum-cards">
        <tr>
            <td class="sum-head">1. {{ __('owner.month_closing.report.cards.total_sales') }}</td>
            <td class="sum-head">2. {{ __('owner.month_closing.report.cards.total_expenses') }}</td>
            <td class="sum-head">3. {{ __('owner.month_closing.report.cards.profit_before') }}</td>
            <td class="sum-head">4. {{ __('owner.month_closing.report.cards.owner_deductions') }}</td>
            <td class="sum-head">5. {{ __('owner.month_closing.report.cards.net_distributable') }}</td>
        </tr>
        <tr>
            {{-- Card 1 — sales → net owner revenue --}}
            <td class="sum-card">
                <table class="sum-body">
                    <tr><td class="sum-k">{{ __('owner.month_closing.report.rows.gross_sales') }}</td><td class="sum-v">{{ number_format((float) $closing->gross_sales, 2) }}</td></tr>
                    <tr><td class="sum-k">&nbsp;</td><td class="sum-v"></td></tr>
                    <tr><td class="sum-k">&nbsp;</td><td class="sum-v"></td></tr>
                </table>
            </td>
            {{-- Card 2 — expenses --}}
            <td class="sum-card">
                <table class="sum-body">
                    <tr><td class="sum-k">{{ __('owner.month_closing.report.rows.trip_expenses') }}</td><td class="sum-v">{{ number_format((float) $closing->trip_expenses, 2) }}</td></tr>
                    <tr><td class="sum-k">{{ __('owner.month_closing.report.rows.operational_expenses') }}</td><td class="sum-v">{{ number_format($operatingExpenses, 2) }}</td></tr>
                    <tr><td class="sum-k">{{ __('owner.month_closing.report.rows.depreciation') }}</td><td class="sum-v">{{ number_format((float) $closing->depreciation, 2) }}</td></tr>
                    <tr class="sum-total"><td class="sum-k">{{ __('owner.month_closing.report.rows.total_expenses') }}</td><td class="sum-v">{{ number_format((float) $closing->total_expenses, 2) }}</td></tr>
                </table>
            </td>
            {{-- Card 3 — profit before distribution --}}
            <td class="sum-card">
                <table class="sum-body">
                    <tr><td class="sum-k">{{ __('owner.month_closing.report.rows.net_owner_revenue') }}</td><td class="sum-v">{{ number_format((float) $closing->net_owner_revenue, 2) }}</td></tr>
                    <tr><td class="sum-k">{{ __('owner.month_closing.report.rows.total_expenses') }}</td><td class="sum-v">{{ number_format((float) $closing->total_expenses, 2) }}</td></tr>
                    <tr><td class="sum-k">&nbsp;</td><td class="sum-v"></td></tr>
                    <tr class="sum-total"><td class="sum-k">{{ __('owner.month_closing.report.rows.profit_before') }}</td><td class="sum-v">{{ number_format((float) $closing->net_profit, 2) }}</td></tr>
                </table>
            </td>
            {{-- Card 4 — owner share --}}
            <td class="sum-card">
                <table class="sum-body">
                    <tr><td class="sum-k">{{ __('owner.month_closing.report.rows.profit_before') }}</td><td class="sum-v">{{ number_format((float) $closing->net_profit, 2) }}</td></tr>
                    <tr><td class="sum-k">{{ __('owner.month_closing.report.rows.owner_percent') }}</td><td class="sum-v">{{ number_format((float) $closing->owner_percent, 2) }}%</td></tr>
                    <tr><td class="sum-k">&nbsp;</td><td class="sum-v"></td></tr>
                    <tr class="sum-total"><td class="sum-k">{{ __('owner.month_closing.report.rows.owner_share') }}</td><td class="sum-v">{{ number_format((float) $closing->owner_share, 2) }}</td></tr>
                </table>
            </td>
            {{-- Card 5 — distributable crew share --}}
            <td class="sum-card">
                <table class="sum-body">
                    <tr><td class="sum-k">{{ __('owner.month_closing.report.rows.profit_before') }}</td><td class="sum-v">{{ number_format((float) $closing->net_profit, 2) }}</td></tr>
                    <tr><td class="sum-k">{{ __('owner.month_closing.report.rows.owner_share') }}</td><td class="sum-v">{{ number_format((float) $closing->owner_share, 2) }}</td></tr>
                    <tr><td class="sum-k">&nbsp;</td><td class="sum-v"></td></tr>
                    <tr class="sum-total"><td class="sum-k">{{ __('owner.month_closing.report.rows.final_distributable') }}</td><td class="sum-v">{{ number_format((float) $closing->crew_share, 2) }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

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
                <div class="cf-card">
                    <div class="cf-head">{{ __('owner.month_closing.report.closing_summary_title') }}</div>
                    <div class="cf-body">
                        <table class="cf-kv">
                            <tr><td class="cf-k">{{ __('owner.month_closing.report.crew_share') }}</td><td class="cf-v">{{ number_format((float) $closing->crew_share, 2) }}</td></tr>
                            <tr><td class="cf-k">{{ __('owner.month_closing.report.owner_share') }}</td><td class="cf-v">{{ number_format((float) $closing->owner_share, 2) }}</td></tr>
                            <tr><td class="cf-k">{{ __('owner.month_closing.report.share_value') }}</td><td class="cf-v">{{ number_format((float) $closing->share_value, 2) }}</td></tr>
                            <tr><td class="cf-k">{{ __('owner.month_closing.report.total_advances') }}</td><td class="cf-v">{{ number_format((float) $dues->sum('advances'), 2) }}</td></tr>
                            <tr><td class="cf-k">{{ __('owner.month_closing.report.total_paid') }}</td><td class="cf-v">{{ number_format((float) $dues->sum('paid_amount'), 2) }}</td></tr>
                            <tr class="cf-total"><td class="cf-k">{{ __('owner.month_closing.report.total_net_due') }}</td><td class="cf-v">{{ number_format((float) $dues->sum('remaining'), 2) }}</td></tr>
                        </table>
                    </div>
                </div>
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
