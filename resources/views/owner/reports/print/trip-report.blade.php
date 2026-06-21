<x-report-layout
    :title="$trip ? __('owner.reports.trip_report') . ' #' . $trip->number : __('owner.reports.all_trips_report')"
    :title-en="$trip ? 'Trip Report #' . $trip->number : 'All Trips Report'"
    :document-number="'#' . str_pad($statistics['total_trips'], 8, '0', STR_PAD_LEFT)"
    :settings="$settings"
    :qr-code="$qrCode">

<style>
    /* ── Report standard: dense, bordered, horizontal "grid" layout ──
       Designed for mPDF (no flex/grid): everything is laid out with tables,
       bordered cells and tight spacing so the page reads as a compact grid
       with no large empty areas. */
    body { font-size: 9pt; line-height: 1.5; }

    .currency-symbol { display: inline-flex; align-items: center; justify-content: flex-end; gap: .15rem; }
    .currency-symbol svg { width: .75rem; height: auto; vertical-align: middle; }

    /* Horizontal key-fact strip — one bordered cell per fact */
    table.info-bar { width: 100%; border-collapse: collapse; margin: 0 0 10px; }
    table.info-bar td { border: 1px solid #e0e0e0; padding: 3px 6px; vertical-align: middle; text-align: center; }
    .ib-label { font-size: 8pt; color: #95a5a6; display: block; margin-bottom: 2px; }
    .ib-value { font-size: 9.5pt; font-weight: 700; color: #2c3e50; }

    /* Section heading */
    .section-title { font-size: 10pt; font-weight: 700; color: #1a1a1a; margin: 0 0 5px; padding-bottom: 3px; }

    /* Two sections side-by-side — fixed geometry so both dual rows line up.
       Aligning off the cell class (not a `> tbody >` child selector) so mPDF
       reliably applies vertical-align:top and the two columns start on the
       same line regardless of which one is taller. */
    table.dual { width: 100%; table-layout: fixed; border-collapse: collapse; margin: 0 0 10px; }
    td.dual-col, td.dual-gap { vertical-align: top; padding: 0; border: none; }
    td.dual-gap { width: 16px; }

    /* Clean horizontal-rule data tables (matching the product-sales summary PDF):
       solid black header bar, no vertical borders, hairline row separators,
       a heavier rule above the totals row. */
    table.report-table { table-layout: fixed; width: 100%; border-collapse: collapse; margin: 0; }
    table.report-table th, table.report-table td {
        border: none; border-bottom: 1px solid #e5e5e5; padding: 3px 5px; font-size: 8.5pt;
        word-wrap: break-word; overflow-wrap: break-word; vertical-align: middle; text-align: center;
    }
    table.report-table thead th { background: #1a1a1a; color: #fff; font-weight: 700; border-bottom: none; }
    table.report-table tbody th { font-weight: 700; color: #2c3e50; }
    table.report-table tfoot td, table.report-table tfoot th {
        background: transparent; font-weight: 700; color: #1a1a1a;
        border-top: 2px solid #1a1a1a; border-bottom: none;
    }

    /* Column alignment helpers: text labels start-aligned, numbers end-aligned */
    .col-text { text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}; }
    .col-num  { text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}; white-space: nowrap; }
    .num { text-align: center; }

    /* KPI cards */
    .report-stats { border-spacing: 6px 0; margin: 0 0 10px; }
    .report-stats td.report-stat-card { border: 1px solid #e0e0e0; border-radius: 4px; padding: 8px 6px; }
    .report-stat-value { font-size: 13pt; }
    .report-stat-label { margin-bottom: 4px; font-size: 8.5pt; }

    .block { margin: 0 0 10px; }

    /* Compact footer: QR + copyright on a single row */
    table.report-footer { width: 100%; border-collapse: collapse; margin: 6px 0 0; border-top: 1px solid #e0e0e0; }
    table.report-footer td { border: none; padding: 8px 4px 0; vertical-align: middle; }
    td.rf-qr { width: 86px; }
    td.rf-text { text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}; font-size: 8pt; color: #95a5a6; }
</style>

    <x-report-header
        :document-number="'#' . str_pad($statistics['total_trips'], 8, '0', STR_PAD_LEFT)"
        :title="$trip ? __('owner.reports.trip_report') : __('owner.reports.all_trips_report')"
        :title-en="$trip ? 'Trip Report' : 'All Trips Report'"
        :settings="$settings" />

    @if(isset($trips) && $trips->isEmpty())
        <div class="alert alert-warning">
            <h5 class="mb-2">{{ __('owner.reports.no_data_found') }}</h5>
            <p class="mb-1"><strong>{{ __('owner.reports.applied_filters') }}:</strong>
                {{ __('owner.reports.from_date') }}: {{ $filters['from_date'] ?? '-' }} —
                {{ __('owner.reports.to_date') }}: {{ $filters['to_date'] ?? '-' }} —
                {{ __('owner.trips.status') }}: {{ $filters['status'] ?? '-' }}
            </p>
            <p class="mb-0 small text-muted">{{ __('owner.reports.try_adjust_filters') }}</p>
        </div>
    @endif

    @if($trip)
        @php
            $f = $financials[$trip->id];
            $catchWeightDisplay = $f['catch_weight_by_unit']->isNotEmpty()
                ? $f['catch_weight_by_unit']
                    ->map(fn ($weight, $unit) => number_format(round($weight), 0) . ' ' . $unit)
                    ->implode('، ')
                : '0';
            $catchDetails = $trip->catches?->details ?? collect();
            $depart = $trip->start_date ? $trip->start_date->format('Y-m-d') : '-';
            $returnDate = $trip->end_date ? $trip->end_date->format('Y-m-d') : '-';
        @endphp

        {{-- Key facts — horizontal strip, only the data that matters per trip --}}
        <table class="info-bar">
            <tr>
                <td>
                    <span class="ib-label">{{ __('owner.trips.trip_number') }}</span>
                    <span class="ib-value">#{{ $trip->number }}</span>
                </td>
                <td>
                    <span class="ib-label">{{ __('owner.reports.departure') }}</span>
                    <span class="ib-value">{{ $depart }}</span>
                </td>
                <td>
                    <span class="ib-label">{{ __('owner.reports.return') }}</span>
                    <span class="ib-value">{{ $returnDate }}</span>
                </td>
                <td>
                    <span class="ib-label">{{ __('owner.trips.show.captain') }}</span>
                    <span class="ib-value">{{ $trip->captain?->name ?? __('owner.trips.no_captain') }}</span>
                </td>
                <td>
                    <span class="ib-label">{{ __('owner.trips.status') }}</span>
                    <span class="ib-value">{{ $trip->status->label() }}</span>
                </td>
                @if($trip->port)
                    <td>
                        <span class="ib-label">{{ __('owner.reports.port') }}</span>
                        <span class="ib-value">{{ $trip->port->name }}</span>
                    </td>
                @endif
            </tr>
        </table>

        {{-- Financial KPIs --}}
        <x-report-stats :items="[
            ['label' => __('owner.reports.catch_weight'), 'value' => $catchWeightDisplay],
            ['label' => __('owner.reports.gross_revenue'), 'value' => number_format($f['total_income'], 2)],
            ['label' => __('owner.reports.total_costs'), 'value' => number_format($f['total_costs'], 2)],
            ['label' => __('owner.reports.net_profit'), 'value' => number_format($f['net_profit'], 2), 'color' => $f['net_profit'] >= 0 ? '#16a34a' : '#dc2626'],
        ]" />

        {{-- Catch + Sales stacked in the left column; Expenses in the right column.
             Both columns are top-aligned so the tables start on the same line. --}}
        <table class="dual">
            <tr>
                <td class="dual-col" valign="top">
                    <div class="section-title">{{ __('owner.reports.catch_breakdown') }}</div>
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th class="col-text" style="width:30%;">{{ __('owner.reports.fish_name') }}</th>
                                <th style="width:14%;">{{ __('owner.reports.weight') }}</th>
                                <th style="width:14%;">{{ __('owner.catch.unit') }}</th>
                                <th class="col-num" style="width:21%;">{{ __('owner.reports.price_per_kg') }}</th>
                                <th class="col-num" style="width:21%;">{{ __('owner.reports.total_value') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($catchDetails as $detail)
                                <tr>
                                    <td class="col-text">{{ $detail->fish->name ?? ($detail->fish_name ?? __('owner.reports.unknown_fish')) }}</td>
                                    <td>{{ number_format(round($detail->weight), 0) }}</td>
                                    <td>{{ $detail->unit->name ?: __('owner.units.kg') }}</td>
                                    <td class="col-num">{{ number_format($detail->price_per_kg, 2) }} <x-riyal-icon /></td>
                                    <td class="col-num">{{ number_format($detail->total_price, 2) }} <x-riyal-icon /></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" style="color:#95a5a6;">{{ __('owner.trips.show.no_catch_data') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="section-title" style="margin-top:7px;">{{ __('owner.reports.sales_breakdown') }}</div>
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th style="width:22%;">{{ __('owner.reports.sale_number') }}</th>
                                <th class="col-text" style="width:33%;">{{ __('owner.reports.customer') }}</th>
                                <th class="col-num" style="width:25%;">{{ __('owner.reports.amount') }}</th>
                                <th style="width:20%;">{{ __('owner.reports.payment_status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($trip->sales as $sale)
                                <tr>
                                    <td>{{ $sale->number }}</td>
                                    <td class="col-text">{{ $sale->customer_name ?? ($sale->customer->name ?? '-') }}</td>
                                    <td class="col-num">{{ number_format($sale->total_price, 2) }} <x-riyal-icon /></td>
                                    <td>{{ \App\Models\Sale::paymentStatusText($sale->payment_status) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" style="color:#95a5a6;">{{ __('owner.reports.no_sales') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </td>
                <td class="dual-gap"></td>
                <td class="dual-col" valign="top">
                    <div class="section-title">{{ __('owner.reports.expenses_breakdown') }}</div>
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th class="col-text" style="width:62%;">{{ __('owner.reports.expense_category') }}</th>
                                <th class="col-num" style="width:38%;">{{ __('owner.reports.amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($f['expenses'] as $expense)
                                <tr>
                                    <td class="col-text">{{ $expense->category_id ? $expense->category->name : __('owner.reports.not_available') }}</td>
                                    <td class="col-num">{{ number_format($expense->final_price, 2) }} <x-riyal-icon /></td>
                                </tr>
                            @empty
                                <tr><td colspan="2" style="color:#95a5a6;">{{ __('owner.reports.no_expenses') }}</td></tr>
                            @endforelse
                        </tbody>
                        @if($f['expenses']->isNotEmpty())
                            <tfoot>
                                <tr>
                                    <td class="col-text">{{ __('owner.reports.total_costs') }}</td>
                                    <td class="col-num">{{ number_format($f['total_costs'], 2) }} <x-riyal-icon /></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </td>
            </tr>
        </table>

        {{-- Financial details below: summary + profit split (left) and crew payout sheet (right) --}}
        <table class="dual">
            <tr>
                <td class="dual-col" valign="top">
                    <div class="section-title">{{ __('owner.reports.financial_summary') }}</div>
                    <table class="report-table">
                        <tbody>
                            <tr>
                                <th class="col-text" style="width:62%;">{{ __('owner.reports.total_income') }}</th>
                                <td class="col-num" style="width:38%;">{{ number_format($f['total_income'], 2) }} <x-riyal-icon /></td>
                            </tr>
                            <tr>
                                <th class="col-text">{{ __('owner.reports.total_costs') }}</th>
                                <td class="col-num">{{ number_format($f['total_costs'], 2) }} <x-riyal-icon /></td>
                            </tr>
                            <tr style="background:#34495e; color:#fff;">
                                <th class="col-text" style="background:#34495e; color:#fff;">{{ __('owner.reports.net_profit') }}</th>
                                <td class="col-num" style="color:#fff; font-weight:700;">{{ number_format($f['net_profit'], 2) }} <x-riyal-icon /></td>
                            </tr>
                            <tr>
                                <th class="col-text">{{ __('owner.reports.owner_share') }} (50%)</th>
                                <td class="col-num">{{ number_format($f['owner_share'], 2) }} <x-riyal-icon /></td>
                            </tr>
                            <tr>
                                <th class="col-text">{{ __('owner.reports.crew_share') }} (50%)</th>
                                <td class="col-num">{{ number_format($f['crew_share'], 2) }} <x-riyal-icon /></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td class="dual-gap"></td>
                <td class="dual-col" valign="top">
                    <div class="section-title">{{ __('owner.reports.crew_salaries') }}</div>
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th class="col-text" style="width:30%;">{{ __('owner.reports.member_name') }}</th>
                                <th style="width:14%;">{{ __('owner.reports.percentage') }}</th>
                                <th class="col-num" style="width:22%;">{{ __('owner.reports.amount') }}</th>
                                <th style="width:34%;">{{ __('owner.reports.signature') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($f['crew_members'] as $member)
                                <tr>
                                    <td class="col-text">{{ $member['name'] }}</td>
                                    <td>{{ number_format($member['percent'], 2) }}%</td>
                                    <td class="col-num">{{ number_format($member['due'], 2) }} <x-riyal-icon /></td>
                                    <td></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" style="color:#95a5a6;">{{ __('owner.reports.no_crew') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>

        @if($trip->notes)
            <div class="block">
                <div class="section-title">{{ __('owner.reports.notes') }}</div>
                <p style="color:#5a6c7d; font-size:8.5pt;">{!! nl2br(e($trip->notes)) !!}</p>
            </div>
        @endif

        {{-- Footer --}}
        <table class="report-footer">
            <tr>
                <td class="rf-text">
                    {{ $settings['title'] ?? '' }} — {{ __('owner.reports.all_rights_reserved') }} © {{ date('Y') }}
                </td>
            </tr>
        </table>
    @else
        {{-- ── All-trips listing ── --}}
        <table class="info-bar">
            <tr>
                @if($fromDate)
                    <td><span class="ib-label">{{ __('owner.reports.from_date') }}</span><span class="ib-value">{{ $fromDate }}</span></td>
                @endif
                @if($toDate)
                    <td><span class="ib-label">{{ __('owner.reports.to_date') }}</span><span class="ib-value">{{ $toDate }}</span></td>
                @endif
                <td><span class="ib-label">{{ __('owner.reports.total_trips') }}</span><span class="ib-value">{{ $statistics['total_trips'] }}</span></td>
                <td><span class="ib-label">{{ __('owner.reports.completed_trips') }}</span><span class="ib-value">{{ $statistics['completed_trips'] }}</span></td>
                <td><span class="ib-label">{{ __('owner.reports.total_catch') }}</span><span class="ib-value">{{ number_format(round($statistics['total_catch']), 0) }} {{ __('owner.units.kg') }}</span></td>
            </tr>
        </table>

        <x-report-stats :items="[
            ['label' => __('owner.reports.gross_revenue'), 'value' => number_format($statistics['total_revenue'], 2)],
            ['label' => __('owner.reports.total_costs'), 'value' => number_format($statistics['total_costs'], 2)],
            ['label' => __('owner.reports.net_profit'), 'value' => number_format($statistics['net_profit'], 2), 'color' => $statistics['net_profit'] >= 0 ? '#16a34a' : '#dc2626'],
            ['label' => __('owner.reports.outstanding'), 'value' => number_format($statistics['total_outstanding'], 2)],
        ]" />

        <table class="report-table block">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('owner.trips.trip_number') }}</th>
                    <th>{{ __('owner.trips.captain_name') }}</th>
                    <th>{{ __('owner.trips.departure_date') }}</th>
                    <th>{{ __('owner.trips.status') }}</th>
                    <th>{{ __('owner.trips.total_catch') }}</th>
                    <th>{{ __('owner.reports.total_revenue') }}</th>
                    <th>{{ __('owner.reports.total_costs') }}</th>
                    <th>{{ __('owner.reports.net_profit') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($trips as $i => $tripItem)
                    @php $tf = $financials[$tripItem->id]; @endphp
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>#{{ $tripItem->number }}</td>
                        <td>{{ $tripItem->captain->name ?? __('owner.trips.no_captain') }}</td>
                        <td>{{ $tripItem->start_date ? $tripItem->start_date->format('Y-m-d') : '-' }}</td>
                        <td>{{ $tripItem->status->label() }}</td>
                        <td>{{ number_format(round($tf['catch_weight']), 0) }} {{ __('owner.units.kg') }}</td>
                        <td>{{ number_format($tf['gross_revenue'], 2) }} <x-riyal-icon /></td>
                        <td>{{ number_format($tf['total_costs'], 2) }} <x-riyal-icon /></td>
                        <td>{{ number_format($tf['net_profit'], 2) }} <x-riyal-icon /></td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5">{{ __('owner.reports.financial_summary') }}</td>
                    <td>{{ number_format(round($statistics['total_catch']), 0) }} {{ __('owner.units.kg') }}</td>
                    <td>{{ number_format($statistics['total_revenue'], 2) }} <x-riyal-icon /></td>
                    <td>{{ number_format($statistics['total_costs'], 2) }} <x-riyal-icon /></td>
                    <td>{{ number_format($statistics['net_profit'], 2) }} <x-riyal-icon /></td>
                </tr>
            </tfoot>
        </table>

        <table class="report-footer">
            <tr>
                <td class="rf-text">
                    {{ $settings['title'] ?? '' }} — {{ __('owner.reports.all_rights_reserved') }} © {{ date('Y') }}
                </td>
            </tr>
        </table>
    @endif

</x-report-layout>
