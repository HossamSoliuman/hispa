@php
    $reportScope = $reportScope ?? 'owner';
    $showOwnerColumn = (bool) ($showOwnerColumn ?? false);
    $showPlatformOwner = $reportScope === 'platform' && $showOwnerColumn;
    $isRtl = app()->getLocale() == 'ar';
    $startAlign = $isRtl ? 'right' : 'left';
    $endAlign = $isRtl ? 'left' : 'right';

    $formatByUnit = function ($byUnit) {
        return $byUnit->isNotEmpty()
            ? $byUnit->map(fn ($weight, $unit) => number_format(round($weight), 0).' '.$unit)->implode('، ')
            : '0';
    };

    if ($trip) {
        $reportTitle = ($reportScope === 'platform'
            ? __('admin.report.trips.platform_print_title')
            : __('owner.reports.trip_report')).' #'.$trip->number;
        $depart = $trip->start_date ? $trip->start_date->format('Y-m-d') : null;
        $returnDate = $trip->end_date ? $trip->end_date->format('Y-m-d') : null;
        $reportSubtitle = $depart && $returnDate && $depart !== $returnDate
            ? __('owner.reports.from_date').' '.$depart.' '.__('owner.reports.to_date').' '.$returnDate
            : ($depart ?? '');
    } else {
        $reportTitle = $reportScope === 'platform'
            ? __('admin.report.trips.platform_print_title')
            : __('owner.reports.all_trips_report');
        $reportSubtitle = $fromDate || $toDate
            ? __('owner.reports.from_date').' '.($fromDate ?? '—').' '.__('owner.reports.to_date').' '.($toDate ?? '—')
            : '';
    }

    $statusFilter = filled($filters['status'] ?? null)
        ? (\App\Enums\TripStatus::tryFrom($filters['status'])?->label() ?? $filters['status'])
        : null;
    $boatFilter = filled($filters['boat_id'] ?? null)
        ? ($trips->firstWhere('boat_id', $filters['boat_id'])?->boat?->name ?? '#'.$filters['boat_id'])
        : null;
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
</style>

    <x-report-masthead
        :title="$reportTitle"
        :subtitle="$reportSubtitle"
        :settings="$settings" />

    @if(collect($filters)->filter(fn ($value) => filled($value))->isNotEmpty())
        <table class="info-bar">
            <tr>
                @if(filled($filters['trip_id'] ?? null))
                    <td>
                        <span class="ib-label">{{ __('owner.trips.trip_number') }}</span>
                        <span class="ib-value">#{{ $trip?->number ?? $filters['trip_id'] }}</span>
                    </td>
                @endif
                @if(filled($filters['from_date'] ?? null))
                    <td>
                        <span class="ib-label">{{ __('owner.reports.from_date') }}</span>
                        <span class="ib-value">{{ $filters['from_date'] }}</span>
                    </td>
                @endif
                @if(filled($filters['to_date'] ?? null))
                    <td>
                        <span class="ib-label">{{ __('owner.reports.to_date') }}</span>
                        <span class="ib-value">{{ $filters['to_date'] }}</span>
                    </td>
                @endif
                @if($statusFilter)
                    <td>
                        <span class="ib-label">{{ __('owner.trips.status') }}</span>
                        <span class="ib-value">{{ $statusFilter }}</span>
                    </td>
                @endif
                @if($boatFilter)
                    <td>
                        <span class="ib-label">{{ __('owner.trips.boat_name') }}</span>
                        <span class="ib-value">{{ $boatFilter }}</span>
                    </td>
                @endif
            </tr>
        </table>
    @endif

    @if(isset($trips) && $trips->isEmpty())
        <p class="empty">{{ __('owner.reports.no_data_found') }}</p>
    @endif

    @if($trip)
        @php
            $f = $financials[$trip->id];
            $catchWeightDisplay = $formatByUnit($f['catch_weight_by_unit']);
            $catchDetails = $trip->catches?->details ?? collect();
        @endphp

        {{-- Key facts — clean, borderless --}}
        <table class="facts">
            <tr>
                @if($reportScope === 'platform')
                    <td>
                        <span class="fact-label">{{ __('admin.report.trips.owner') }}</span>
                        <span class="fact-value">{{ $trip->owner?->name ?? __('owner.reports.not_available') }}</span>
                    </td>
                @endif
                <td>
                    <span class="fact-label">{{ __('owner.trips.show.captain') }}</span>
                    <span class="fact-value">{{ $trip->captain?->name ?? __('owner.trips.no_captain') }}</span>
                </td>
                <td>
                    <span class="fact-label">{{ __('owner.trips.status') }}</span>
                    <span class="fact-value">{{ $trip->status->label() }}</span>
                </td>
                @if($trip->duration_text)
                    <td>
                        <span class="fact-label">{{ __('owner.reports.duration') }}</span>
                        <span class="fact-value">{{ $trip->duration_text }}</span>
                    </td>
                @endif
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
                            <td class="col-num"><x-report-money :amount="$detail->price_per_kg" /></td>
                            <td class="col-num"><x-report-money :amount="$detail->total_price" /></td>
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
                            <td class="col-num"><x-report-money :amount="$sale->total_price" /></td>
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
                            <td class="col-num"><x-report-money :amount="$expense->final_price" /></td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="empty">{{ __('owner.reports.no_expenses') }}</td></tr>
                    @endforelse
                </tbody>
                @if($f['expenses']->isNotEmpty())
                    <tfoot>
                        <tr>
                            <td class="col-text">{{ __('owner.reports.total_costs') }}</td>
                            <td class="col-num"><x-report-money :amount="$f['total_costs']" /></td>
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
                        <td class="col-num" style="width:38%;"><x-report-money :amount="$f['total_income']" /></td>
                    </tr>
                    <tr>
                        <th class="col-text">{{ __('owner.reports.total_costs') }}</th>
                        <td class="col-num"><x-report-money :amount="$f['total_costs']" /></td>
                    </tr>
                    <tr class="net-row">
                        <th class="col-text">{{ __('owner.reports.net_profit') }}</th>
                        <td class="col-num"><x-report-money :amount="$f['net_profit']" /></td>
                    </tr>
                    <tr>
                        <th class="col-text">{{ __('owner.reports.owner_share') }} (50%)</th>
                        <td class="col-num"><x-report-money :amount="$f['owner_share']" /></td>
                    </tr>
                    <tr>
                        <th class="col-text">{{ __('owner.reports.crew_share') }} (50%)</th>
                        <td class="col-num"><x-report-money :amount="$f['crew_share']" /></td>
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
                            <td class="col-num"><x-report-money :amount="$member['due']" /></td>
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
                    <span class="fact-value">{{ $formatByUnit($statistics['total_catch_by_unit']) }}</span>
                </td>
                <td>
                    <span class="fact-label">{{ __('owner.reports.net_profit') }}</span>
                    <span class="fact-value"><x-report-money :amount="$statistics['net_profit']" /></span>
                </td>
            </tr>
        </table>

        <table class="report-table block">
            <thead>
                @if($showPlatformOwner)
                    <tr>
                        <th style="width:4%;">#</th>
                        <th style="width:10%;">{{ __('owner.trips.trip_number') }}</th>
                        <th class="col-text" style="width:14%;">{{ __('admin.report.trips.owner') }}</th>
                        <th class="col-text" style="width:13%;">{{ __('owner.trips.captain_name') }}</th>
                        <th style="width:10%;">{{ __('owner.trips.departure_date') }}</th>
                        <th style="width:8%;">{{ __('owner.reports.duration') }}</th>
                        <th style="width:9%;">{{ __('owner.trips.status') }}</th>
                        <th style="width:12%;">{{ __('owner.trips.total_catch') }}</th>
                        <th class="col-num" style="width:10%;">{{ __('owner.reports.total_revenue') }}</th>
                        <th class="col-num" style="width:10%;">{{ __('owner.reports.net_profit') }}</th>
                    </tr>
                @else
                    <tr>
                        <th style="width:5%;">#</th>
                        <th style="width:12%;">{{ __('owner.trips.trip_number') }}</th>
                        <th class="col-text" style="width:15%;">{{ __('owner.trips.captain_name') }}</th>
                        <th style="width:11%;">{{ __('owner.trips.departure_date') }}</th>
                        <th style="width:9%;">{{ __('owner.reports.duration') }}</th>
                        <th style="width:10%;">{{ __('owner.trips.status') }}</th>
                        <th style="width:14%;">{{ __('owner.trips.total_catch') }}</th>
                        <th class="col-num" style="width:12%;">{{ __('owner.reports.total_revenue') }}</th>
                        <th class="col-num" style="width:12%;">{{ __('owner.reports.net_profit') }}</th>
                    </tr>
                @endif
            </thead>
            <tbody>
                @foreach($trips as $i => $tripItem)
                    @php $tf = $financials[$tripItem->id]; @endphp
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>#{{ $tripItem->number }}</td>
                        @if($showPlatformOwner)
                            <td class="col-text">{{ $tripItem->owner?->name ?? __('owner.reports.not_available') }}</td>
                        @endif
                        <td class="col-text">{{ $tripItem->captain->name ?? __('owner.trips.no_captain') }}</td>
                        <td>{{ $tripItem->start_date ? $tripItem->start_date->format('Y-m-d') : '-' }}</td>
                        <td>{{ $tripItem->duration_text ?? '-' }}</td>
                        <td>{{ $tripItem->status->label() }}</td>
                        <td>{{ $formatByUnit($tf['catch_weight_by_unit']) }}</td>
                        <td class="col-num"><x-report-money :amount="$tf['gross_revenue']" /></td>
                        <td class="col-num"><x-report-money :amount="$tf['net_profit']" /></td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="{{ $showPlatformOwner ? 7 : 6 }}" class="col-text">{{ __('owner.reports.financial_summary') }}</td>
                    <td>{{ $formatByUnit($statistics['total_catch_by_unit']) }}</td>
                    <td class="col-num"><x-report-money :amount="$statistics['total_revenue']" /></td>
                    <td class="col-num"><x-report-money :amount="$statistics['net_profit']" /></td>
                </tr>
            </tfoot>
        </table>
    @endif

    <table class="report-footer">
        <tr>
            <td class="rf-text">
                {{ $settings['company_name'] ?? $settings['title'] ?? '' }} &mdash; {{ __('owner.reports.all_rights_reserved') }} &copy; {{ date('Y') }}
            </td>
        </tr>
    </table>

</x-report-layout>
