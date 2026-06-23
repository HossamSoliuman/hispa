@php
    $isRtl = app()->getLocale() === 'ar';
    $reportTitle = __('owner.month_closing.report_title').' '.sprintf('%02d/%d', $closing->month, $closing->year);
    $closedAt = optional($closing->closed_at)->format('Y-m-d') ?? now()->format('Y-m-d');

    $fmt = fn ($value) => number_format((float) $value, 2);

    $returns = max((float) $closing->gross_sales - (float) $closing->net_sales, 0);
    $totalEarned = $closing->dues->sum('due_amount');
    $totalAdvances = $closing->dues->sum('advances');
    $totalNetDue = $closing->dues->sum('remaining');

    $r = 'owner.month_closing.report';
    // mPDF reverses table-column order for RTL, so order the footer cells per
    // direction to keep the closing summary on the start side everywhere.
    $footerCells = $isRtl ? ['summary', 'gap', 'signoff', 'gap', 'notes'] : ['notes', 'gap', 'signoff', 'gap', 'summary'];
@endphp

<x-report-layout
    :title="$reportTitle"
    :documentNumber="'MC-'.$closing->year.str_pad($closing->month, 2, '0', STR_PAD_LEFT)"
    :settings="$settings ?? []"
>
    <x-report-masthead
        :title="$reportTitle"
        :subtitle="__('owner.month_closing.subtitle')"
        :settings="$settings ?? []" />

    {{-- Filter badges --}}
    <table class="info-bar">
        <tr>
            <td>
                <span class="ib-label">{{ __("$r.filter_month") }}</span>
                <span class="ib-value">{{ sprintf('%02d', $closing->month) }}</span>
            </td>
            <td>
                <span class="ib-label">{{ __("$r.filter_year") }}</span>
                <span class="ib-value">{{ $closing->year }}</span>
            </td>
            <td>
                <span class="ib-label">{{ __("$r.filter_closing_date") }}</span>
                <span class="ib-value">{{ $closedAt }}</span>
            </td>
            <td>
                <span class="ib-label">{{ __("$r.filter_boat") }}</span>
                <span class="ib-value">{{ $closing->boat?->name ?? __("$r.all_boats") }}</span>
            </td>
        </tr>
    </table>

    {{-- Summary cards --}}
    @php
        $summaryCards = [
            [
                'head' => __("$r.cards.total_sales"),
                'rows' => [
                    [__("$r.rows.gross_sales"), $fmt($closing->gross_sales)],
                    [__("$r.rows.returns"), $fmt($returns)],
                ],
                'total' => [__("$r.rows.net_sales"), $fmt($closing->net_sales)],
            ],
            [
                'head' => __("$r.cards.total_expenses"),
                'rows' => [
                    [__("$r.rows.trip_expenses"), $fmt($closing->trip_expenses)],
                    [__("$r.rows.operational_expenses"), $fmt($closing->general_expenses)],
                    [__("$r.rows.depreciation"), $fmt($closing->depreciation)],
                ],
                'total' => [__("$r.rows.total_expenses"), $fmt($closing->total_expenses)],
            ],
            [
                'head' => __("$r.cards.profit_before"),
                'rows' => [
                    [__("$r.rows.net_owner_revenue"), $fmt($closing->net_owner_revenue)],
                    [__("$r.rows.total_expenses"), $fmt($closing->total_expenses)],
                ],
                'total' => [__("$r.rows.profit_before"), $fmt($closing->net_profit)],
            ],
            [
                'head' => __("$r.cards.owner_deductions"),
                'rows' => [
                    [__("$r.rows.profit_before"), $fmt($closing->net_profit)],
                    [__("$r.rows.owner_percent"), $fmt($closing->owner_percent).'%'],
                ],
                'total' => [__("$r.rows.total_deductions"), $fmt($closing->owner_share)],
            ],
            [
                'head' => __("$r.cards.net_distributable"),
                'rows' => [
                    [__("$r.rows.profit_before"), $fmt($closing->net_profit)],
                    [__("$r.rows.total_deductions"), $fmt($closing->owner_share)],
                ],
                'total' => [__("$r.rows.final_distributable"), $fmt($closing->crew_share)],
            ],
        ];
        // Equal-height cards: pad every body to the tallest card's row count so
        // the bordered boxes match and the total rows align along the bottom.
        $maxRows = max(array_map(fn ($card) => count($card['rows']), $summaryCards));
    @endphp
    <table class="sum-cards">
        <tr>
            @foreach ($summaryCards as $card)
                <td class="sum-head">{{ $card['head'] }}</td>
            @endforeach
        </tr>
        <tr>
            @foreach ($summaryCards as $card)
                <td class="sum-card">
                    <table class="sum-body">
                        @foreach ($card['rows'] as [$label, $value])
                            <tr><td class="sum-k">{{ $label }}</td><td class="sum-v">{{ $value }}</td></tr>
                        @endforeach
                        @for ($i = count($card['rows']); $i < $maxRows; $i++)
                            <tr><td class="sum-k">&nbsp;</td><td class="sum-v">&nbsp;</td></tr>
                        @endfor
                        <tr class="sum-total"><td class="sum-k">{{ $card['total'][0] }}</td><td class="sum-v">{{ $card['total'][1] }}</td></tr>
                    </table>
                </td>
            @endforeach
        </tr>
    </table>

    {{-- Sailor profit distribution --}}
    <div class="section-bar">{{ __("$r.distribution_title") }}</div>
    <table class="report-table">
        <thead>
            <tr>
                <th style="width:5%;">{{ __("$r.dist.index") }}</th>
                <th class="col-text" style="width:22%;">{{ __("$r.dist.name") }}</th>
                <th style="width:13%;">{{ __("$r.dist.role") }}</th>
                <th style="width:12%;">{{ __("$r.dist.share") }}</th>
                <th class="col-num" style="width:13%;">{{ __("$r.dist.earned") }}</th>
                <th class="col-num" style="width:12%;">{{ __("$r.dist.advance") }}</th>
                <th class="col-num" style="width:13%;">{{ __("$r.dist.net_due") }}</th>
                <th style="width:10%;">{{ __("$r.dist.signature") }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($closing->dues as $i => $due)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td class="col-text">{{ $due->member_name }}</td>
                    <td>{{ $due->role }}</td>
                    <td>{{ $due->custom_share_percent !== null ? $fmt($due->custom_share_percent).'%' : $fmt($due->shares) }}</td>
                    <td class="col-num">{{ $fmt($due->due_amount) }}</td>
                    <td class="col-num">{{ $fmt($due->advances) }}</td>
                    <td class="col-num">{{ $fmt($due->remaining) }}</td>
                    <td>&nbsp;</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="col-text">{{ __("$r.dist.total") }}</th>
                <th>{{ $fmt($closing->total_shares) }}</th>
                <th class="col-num">{{ $fmt($totalEarned) }}</th>
                <th class="col-num">{{ $fmt($totalAdvances) }}</th>
                <th class="col-num">{{ $fmt($totalNetDue) }}</th>
                <th>&nbsp;</th>
            </tr>
        </tfoot>
    </table>

    {{-- Closing footer: summary grid / sign-off / notes --}}
    <table class="close-footer">
        <tr>
            @foreach ($footerCells as $cell)
                @if ($cell === 'gap')
                    <td class="cf-gap"></td>
                @elseif ($cell === 'summary')
                    <td class="cf-col">
                        <div class="cf-card">
                            <div class="cf-head">{{ __("$r.closing_summary_title") }}</div>
                            <div class="cf-body">
                                <table class="cf-kv">
                                    <tr><td class="cf-k">{{ __("$r.rows.net_sales") }}</td><td class="cf-v">{{ $fmt($closing->net_sales) }}</td></tr>
                                    <tr><td class="cf-k">{{ __("$r.rows.total_expenses") }}</td><td class="cf-v">{{ $fmt($closing->total_expenses) }}</td></tr>
                                    <tr><td class="cf-k">{{ __("$r.rows.profit_before") }}</td><td class="cf-v">{{ $fmt($closing->net_profit) }}</td></tr>
                                    <tr><td class="cf-k">{{ __("$r.rows.owner_share") }}</td><td class="cf-v">{{ $fmt($closing->owner_share) }}</td></tr>
                                    <tr><td class="cf-k">{{ __("$r.rows.final_distributable") }}</td><td class="cf-v">{{ $fmt($closing->crew_share) }}</td></tr>
                                    <tr class="cf-total"><td class="cf-k">{{ __("$r.total_net_due") }}</td><td class="cf-v">{{ $fmt($totalNetDue) }}</td></tr>
                                </table>
                            </div>
                        </div>
                    </td>
                @elseif ($cell === 'signoff')
                    <td class="cf-col">
                        <div class="cf-card">
                            <div class="cf-head">{{ __("$r.signoff_title") }}</div>
                            <div class="cf-body">
                                <div class="cf-signoff-label">{{ __('owner.reports.sig_responsible_manager') }}</div>
                                <div class="cf-signoff-line">.............................</div>
                                <div class="cf-signoff-label">{{ __('owner.reports.signature') }}</div>
                                <div class="cf-signoff-line">.............................</div>
                                <div class="cf-signoff-date">{{ __('owner.reports.date_label') }}: {{ $closedAt }}</div>
                            </div>
                        </div>
                    </td>
                @else
                    <td class="cf-col">
                        <div class="cf-card">
                            <div class="cf-head">{{ __("$r.notes_title") }}</div>
                            <div class="cf-body">
                                <ul class="cf-notes">
                                    @foreach (__("$r.notes") as $note)
                                        <li>{{ $note }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </td>
                @endif
            @endforeach
        </tr>
    </table>

    <table class="report-footer">
        <tr>
            <td>{{ $settings['title'] ?? $settings['company_name'] ?? '' }} — {{ __('owner.reports.all_rights_reserved') }} © {{ date('Y') }}</td>
        </tr>
    </table>
</x-report-layout>
