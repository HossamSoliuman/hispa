@php
    $isRtl = app()->getLocale() == 'ar';
    $startAlign = $isRtl ? 'right' : 'left';
    $endAlign = $isRtl ? 'left' : 'right';
    $cur = __('owner.reports.report_currency');

    if ($trip) {
        $reportTitle = __('owner.reports.trip_report').' #'.$trip->number;
        $depart = $trip->start_date ? $trip->start_date->format('Y-m-d') : null;
        $returnDate = $trip->end_date ? $trip->end_date->format('Y-m-d') : null;
        $reportSubtitle = $depart && $returnDate && $depart !== $returnDate
            ? __('owner.reports.from_date').' '.$depart.' '.__('owner.reports.to_date').' '.$returnDate
            : ($depart ?? '');
    } else {
        $reportTitle = __('owner.reports.all_trips_report');
        $reportSubtitle = $fromDate || $toDate
            ? __('owner.reports.from_date').' '.($fromDate ?? '—').' '.__('owner.reports.to_date').' '.($toDate ?? '—')
            : '';
    }
@endphp

<x-report-layout
    :title="$reportTitle"
    :settings="$settings"
    :qr-code="$qrCode">

<style>
    /* Clean key-fact line — no boxes, no fill (data tables inherit the shared grid) */
    table.facts { width: 100%; border-collapse: collapse; margin: 0 0 12px; }
    table.facts td { border: none; padding: 1px 14px 1px 0; vertical-align: top; }
    .fact-label { font-size: 8pt; color: #888; display: block; margin-bottom: 1px; }
    .fact-value { font-size: 9.5pt; font-weight: 700; color: #1a1a1a; }

    .summary-table { width: 60%; }
    .empty { color: #888; }
</style>

    <x-report-masthead
        :title="$reportTitle"
        :subtitle="$reportSubtitle"
        :settings="$settings" />

    @if(isset($trips) && $trips->isEmpty())
        <p class="empty">{{ __('owner.reports.no_data_found') }}</p>
    @endif

    @if($trip)
        @php
            $f = $financials[$trip->id];
            $catchWeightDisplay = $f['catch_weight_by_unit']->isNotEmpty()
                ? $f['catch_weight_by_unit']
                    ->map(fn ($weight, $unit) => number_format(round($weight), 0).' '.$unit)
                    ->implode('، ')
                : '0';
            $catchDetails = $trip->catches?->details ?? collect();
        @endphp

        {{-- Key facts — clean, borderless --}}
        <table class="facts">
            <tr>
                <td>
                    <span class="fact-label">{{ __('owner.trips.show.captain') }}</span>
                    <span class="fact-value">{{ $trip->captain?->name ?? __('owner.trips.no_captain') }}</span>
                </td>
                <td>
                    <span class="fact-label">{{ __('owner.trips.status') }}</span>
                    <span class="fact-value">{{ $trip->status->label() }}</span>
                </td>
                <td>
                    <span class="fact-label">{{ __('owner.reports.catch_weight') }}</span>
                    <span class="fact-value">{{ $catchWeightDisplay }}</span>
                </td>
                @if($trip->port)
                    <td>
                        <span class="fact-label">{{ __('owner.reports.port') }}</span>
                        <span class="fact-value">{{ $trip->port->name }}</span>
                    </td>
                @endif
            </tr>
        </table>

        {{-- Catch breakdown --}}
        <div class="block">
            <div class="section-title">{{ __('owner.reports.catch_breakdown') }}</div>
            <table class="report-table">
                <thead>
                    <tr>
                        <th class="col-text" style="width:34%;">{{ __('owner.reports.fish_name') }}</th>
                        <th style="width:16%;">{{ __('owner.reports.weight') }}</th>
                        <th style="width:14%;">{{ __('owner.catch.unit') }}</th>
                        <th class="col-num" style="width:18%;">{{ __('owner.reports.price_per_kg') }}</th>
                        <th class="col-num" style="width:18%;">{{ __('owner.reports.total_value') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($catchDetails as $detail)
                        <tr>
                            <td class="col-text">{{ $detail->fish->name ?? ($detail->fish_name ?? __('owner.reports.unknown_fish')) }}</td>
                            <td>{{ number_format(round($detail->weight), 0) }}</td>
                            <td>{{ $detail->unit->name ?: __('owner.units.kg') }}</td>
                            <td class="col-num">{{ number_format($detail->price_per_kg, 2) }}</td>
                            <td class="col-num">{{ number_format($detail->total_price, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="empty">{{ __('owner.trips.show.no_catch_data') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Sales breakdown --}}
        <div class="block">
            <div class="section-title">{{ __('owner.reports.sales_breakdown') }}</div>
            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width:20%;">{{ __('owner.reports.sale_number') }}</th>
                        <th class="col-text" style="width:42%;">{{ __('owner.reports.customer') }}</th>
                        <th class="col-num" style="width:20%;">{{ __('owner.reports.amount') }}</th>
                        <th style="width:18%;">{{ __('owner.reports.payment_status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trip->sales as $sale)
                        <tr>
                            <td>{{ $sale->number }}</td>
                            <td class="col-text">{{ $sale->customer_name ?? ($sale->customer->name ?? '-') }}</td>
                            <td class="col-num">{{ number_format($sale->total_price, 2) }}</td>
                            <td>{{ \App\Models\Sale::paymentStatusText($sale->payment_status) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="empty">{{ __('owner.reports.no_sales') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Expenses breakdown --}}
        <div class="block">
            <div class="section-title">{{ __('owner.reports.expenses_breakdown') }}</div>
            <table class="report-table">
                <thead>
                    <tr>
                        <th class="col-text" style="width:70%;">{{ __('owner.reports.expense_category') }}</th>
                        <th class="col-num" style="width:30%;">{{ __('owner.reports.amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($f['expenses'] as $expense)
                        <tr>
                            <td class="col-text">{{ $expense->category_id ? $expense->category->name : __('owner.reports.not_available') }}</td>
                            <td class="col-num">{{ number_format($expense->final_price, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="empty">{{ __('owner.reports.no_expenses') }}</td></tr>
                    @endforelse
                </tbody>
                @if($f['expenses']->isNotEmpty())
                    <tfoot>
                        <tr>
                            <td class="col-text">{{ __('owner.reports.total_costs') }}</td>
                            <td class="col-num">{{ number_format($f['total_costs'], 2) }} {{ $cur }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        {{-- Financial summary --}}
        <div class="block">
            <div class="section-title">{{ __('owner.reports.financial_summary') }}</div>
            <table class="report-table summary-table">
                <tbody>
                    <tr>
                        <th class="col-text" style="width:62%;">{{ __('owner.reports.total_income') }}</th>
                        <td class="col-num" style="width:38%;">{{ number_format($f['total_income'], 2) }} {{ $cur }}</td>
                    </tr>
                    <tr>
                        <th class="col-text">{{ __('owner.reports.total_costs') }}</th>
                        <td class="col-num">{{ number_format($f['total_costs'], 2) }} {{ $cur }}</td>
                    </tr>
                    <tr class="net-row">
                        <th class="col-text">{{ __('owner.reports.net_profit') }}</th>
                        <td class="col-num">{{ number_format($f['net_profit'], 2) }} {{ $cur }}</td>
                    </tr>
                    <tr>
                        <th class="col-text">{{ __('owner.reports.owner_share') }} (50%)</th>
                        <td class="col-num">{{ number_format($f['owner_share'], 2) }} {{ $cur }}</td>
                    </tr>
                    <tr>
                        <th class="col-text">{{ __('owner.reports.crew_share') }} (50%)</th>
                        <td class="col-num">{{ number_format($f['crew_share'], 2) }} {{ $cur }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Crew payout sheet --}}
        <div class="block">
            <div class="section-title">{{ __('owner.reports.crew_salaries') }}</div>
            <table class="report-table">
                <thead>
                    <tr>
                        <th class="col-text" style="width:34%;">{{ __('owner.reports.member_name') }}</th>
                        <th style="width:16%;">{{ __('owner.reports.percentage') }}</th>
                        <th class="col-num" style="width:22%;">{{ __('owner.reports.amount') }}</th>
                        <th style="width:28%;">{{ __('owner.reports.signature') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($f['crew_members'] as $member)
                        <tr>
                            <td class="col-text">{{ $member['name'] }}</td>
                            <td>{{ number_format($member['percent'], 2) }}%</td>
                            <td class="col-num">{{ number_format($member['due'], 2) }}</td>
                            <td></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="empty">{{ __('owner.reports.no_crew') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($trip->notes)
            <div class="block">
                <div class="section-title">{{ __('owner.reports.notes') }}</div>
                <p style="color:#5a6c7d; font-size:9pt;">{!! nl2br(e($trip->notes)) !!}</p>
            </div>
        @endif
    @else
        {{-- ── All-trips listing ── --}}
        <table class="facts">
            <tr>
                <td>
                    <span class="fact-label">{{ __('owner.reports.total_trips') }}</span>
                    <span class="fact-value">{{ $statistics['total_trips'] }}</span>
                </td>
                <td>
                    <span class="fact-label">{{ __('owner.reports.completed_trips') }}</span>
                    <span class="fact-value">{{ $statistics['completed_trips'] }}</span>
                </td>
                <td>
                    <span class="fact-label">{{ __('owner.reports.total_catch') }}</span>
                    <span class="fact-value">{{ number_format(round($statistics['total_catch']), 0) }} {{ __('owner.units.kg') }}</span>
                </td>
                <td>
                    <span class="fact-label">{{ __('owner.reports.net_profit') }}</span>
                    <span class="fact-value">{{ number_format($statistics['net_profit'], 2) }} {{ $cur }}</span>
                </td>
            </tr>
        </table>

        <table class="report-table block">
            <thead>
                <tr>
                    <th style="width:5%;">#</th>
                    <th style="width:14%;">{{ __('owner.trips.trip_number') }}</th>
                    <th class="col-text" style="width:17%;">{{ __('owner.trips.captain_name') }}</th>
                    <th style="width:13%;">{{ __('owner.trips.departure_date') }}</th>
                    <th style="width:11%;">{{ __('owner.trips.status') }}</th>
                    <th style="width:12%;">{{ __('owner.trips.total_catch') }}</th>
                    <th class="col-num" style="width:14%;">{{ __('owner.reports.total_revenue') }}</th>
                    <th class="col-num" style="width:14%;">{{ __('owner.reports.net_profit') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($trips as $i => $tripItem)
                    @php $tf = $financials[$tripItem->id]; @endphp
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>#{{ $tripItem->number }}</td>
                        <td class="col-text">{{ $tripItem->captain->name ?? __('owner.trips.no_captain') }}</td>
                        <td>{{ $tripItem->start_date ? $tripItem->start_date->format('Y-m-d') : '-' }}</td>
                        <td>{{ $tripItem->status->label() }}</td>
                        <td>{{ number_format(round($tf['catch_weight']), 0) }} {{ __('owner.units.kg') }}</td>
                        <td class="col-num">{{ number_format($tf['gross_revenue'], 2) }}</td>
                        <td class="col-num">{{ number_format($tf['net_profit'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="col-text">{{ __('owner.reports.financial_summary') }}</td>
                    <td>{{ number_format(round($statistics['total_catch']), 0) }} {{ __('owner.units.kg') }}</td>
                    <td class="col-num">{{ number_format($statistics['total_revenue'], 2) }} {{ $cur }}</td>
                    <td class="col-num">{{ number_format($statistics['net_profit'], 2) }} {{ $cur }}</td>
                </tr>
            </tfoot>
        </table>
    @endif

</x-report-layout>
