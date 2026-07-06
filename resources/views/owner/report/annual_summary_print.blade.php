@php
    $t = $summary['totals'];
    $months = $summary['months'];
    $monthNames = __('owner.annual_summary.months');

    $a = $analysis['analysis'];
    $trips = $analysis['trips'];
    $payroll = $analysis['payroll'];
    $expensesByCategory = $analysis['expenses_by_category'];
    $expensesByType = $analysis['expenses_by_type'];
    $salesData = $analysis['sales'];
    $catchData = $analysis['catch'];
    $crewMembers = $analysis['crew_members'];

    $selectedBoat = isset($boats) ? $boats->firstWhere('id', $boatId) : null;
    $boatLabel = $selectedBoat ? ($selectedBoat->name ?? $selectedBoat->name_ar) : __('owner.annual_summary.all_boats');
    $companyName = $settings['title'] ?? ($settings['company_name'] ?? ($settings['name'] ?? ''));

    $isRtl = app()->getLocale() === 'ar';
    $listPad = $isRtl ? 'right' : 'left';

    $reportTitle = __('owner.annual_summary.title') . ' — ' . $year;
    $phMeta = $boatLabel . ' · ' . $summary['closed_count'] . '/12 · ' . ($a['is_profitable'] ? __('owner.annual_summary.verdict_profit') : __('owner.annual_summary.verdict_loss'));

    // Best / weakest month labels for the KPI band.
    $bestLabel = $a['best'] ? $monthNames[$a['best']['month']] : '—';
    $bestNet = $a['best'] ? (float) $a['best']['net'] : 0.0;
    $worstLabel = $a['worst'] ? $monthNames[$a['worst']['month']] : '—';
    $worstNet = $a['worst'] ? (float) $a['worst']['net'] : 0.0;

    // Quarterly roll-up from the frozen monthly closings (reconciles with totals).
    $quarters = [];
    foreach ([1, 2, 3, 4] as $q) {
        $qSales = 0.0; $qExpenses = 0.0; $qNet = 0.0; $qHas = false;
        foreach ([($q - 1) * 3 + 1, ($q - 1) * 3 + 2, ($q - 1) * 3 + 3] as $qm) {
            $closing = $months[$qm] ?? null;
            if ($closing) {
                $qHas = true;
                $qSales += (float) $closing->gross_sales;
                $qExpenses += (float) $closing->total_expenses;
                $qNet += (float) $closing->net_profit;
            }
        }
        $quarters[$q] = [
            'sales' => $qSales,
            'expenses' => $qExpenses,
            'net' => $qNet,
            'margin' => $qSales > 0 ? round($qNet / $qSales * 100, 1) : 0.0,
            'has' => $qHas,
        ];
    }

    // Peak magnitude for the monthly net-profit trend bars.
    $closedMonthsList = collect($months)->filter()->all();
    $trendMax = 0.0;
    foreach ($closedMonthsList as $closing) {
        $trendMax = max($trendMax, abs((float) $closing->net_profit));
    }

    $green = '#198754';
    $red = '#dc3545';
@endphp

<x-report-layout
    :title="$reportTitle"
    :document-number="'AS-' . $year"
    :settings="$settings"
    :qr-code="$settings['qr_code'] ?? ''"
    :printable="true"
    orientation="landscape"
>
    {{-- ══════════════════ PAGE 1 — Executive overview ══════════════════ --}}
    <x-report-masthead :title="$reportTitle" :subtitle="__('owner.annual_summary.subtitle')" :settings="$settings" />

    {{-- Meta strip --}}
    <div class="meta-row">
        <span class="meta-item">
            <span class="lbl">{{ __('owner.annual_summary.year') }}:</span>
            <span class="val-box">{{ $year }}</span>
        </span>
        <span class="meta-item">
            <span class="lbl">{{ __('owner.annual_summary.boat') }}:</span>
            <span class="val-box">{{ $boatLabel }}</span>
        </span>
        <span class="meta-item">
            <span class="lbl">{{ __('owner.annual_summary.stats.closed_months') }}:</span>
            <span class="val-box">{{ $summary['closed_count'] }} / 12</span>
        </span>
        <span class="meta-item">
            <span class="lbl">{{ __('owner.annual_summary.columns.status') }}:</span>
            <span class="val-box">{{ $a['is_profitable'] ? __('owner.annual_summary.verdict_profit') : __('owner.annual_summary.verdict_loss') }}</span>
        </span>
    </div>

    {{-- Year KPI cards — row 1: headline figures --}}
    <x-report-stats :items="[
        ['label' => __('owner.annual_summary.stats.sales'), 'value' => $t['gross_sales'], 'money' => true],
        ['label' => __('owner.annual_summary.stats.revenue'), 'value' => $t['net_owner_revenue'], 'money' => true],
        ['label' => __('owner.annual_summary.stats.expenses'), 'value' => $t['total_expenses'], 'money' => true],
        ['label' => __('owner.annual_summary.stats.depreciation'), 'value' => $t['depreciation'], 'money' => true],
        ['label' => __('owner.annual_summary.stats.net_profit'), 'value' => $t['net_profit'], 'money' => true, 'color' => $t['net_profit'] >= 0 ? '#198754' : '#dc3545'],
        ['label' => __('owner.annual_summary.analysis.margin'), 'value' => number_format($a['margin'], 1) . '%'],
    ]" />

    {{-- Year KPI cards — row 2: performance figures --}}
    <x-report-stats :items="[
        ['label' => __('owner.annual_summary.kpi.avg_profit'), 'value' => $a['avg_net'], 'money' => true],
        ['label' => __('owner.annual_summary.kpi.best_month') . ' · ' . $bestLabel, 'value' => $bestNet, 'money' => true, 'color' => '#198754'],
        ['label' => __('owner.annual_summary.kpi.worst_month') . ' · ' . $worstLabel, 'value' => $worstNet, 'money' => true, 'color' => $worstNet < 0 ? '#dc3545' : '#1a1a1a'],
        ['label' => __('owner.annual_summary.kpi.profitable_months'), 'value' => $a['profitable_months'] . ' / ' . $a['closed_count']],
        ['label' => __('owner.annual_summary.kpi.owner_share'), 'value' => $t['owner_share'], 'money' => true],
        ['label' => __('owner.annual_summary.kpi.crew_share'), 'value' => $t['crew_share'], 'money' => true],
    ]" />

    {{-- Financial waterfall — from gross sales down to the owner/crew split --}}
    <div class="section-bar">{{ __('owner.annual_summary.waterfall_title') }}</div>
    <table class="dual" style="margin-bottom:14px;">
        <tr>
            <td class="dual-col" style="width:58%">
                <table class="report-table" style="margin-top:8px;">
                    <thead>
                        <tr>
                            <th class="col-text">{{ __('owner.annual_summary.cols.item') }}</th>
                            <th style="width:32%">{{ __('owner.annual_summary.cols.value') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="col-text">{{ __('owner.annual_summary.waterfall.gross_sales') }}</td><td class="col-num"><x-report-money :amount="$t['gross_sales']" /></td></tr>
                        <tr><td class="col-text">{{ __('owner.annual_summary.waterfall.trip_expenses') }}</td><td class="col-num">(<x-report-money :amount="$t['trip_expenses']" />)</td></tr>
                        <tr><td class="col-text">{{ __('owner.annual_summary.waterfall.general_expenses') }}</td><td class="col-num">(<x-report-money :amount="$t['general_expenses']" />)</td></tr>
                        <tr><td class="col-text">{{ __('owner.annual_summary.waterfall.depreciation') }}</td><td class="col-num">(<x-report-money :amount="$t['depreciation']" />)</td></tr>
                        <tr class="net-row"><td class="col-text">{{ __('owner.annual_summary.waterfall.net_profit') }}</td><td class="col-num"><x-report-money :amount="$t['net_profit']" /></td></tr>
                        <tr><td class="col-text">{{ __('owner.annual_summary.waterfall.owner_share') }}</td><td class="col-num"><x-report-money :amount="$t['owner_share']" /></td></tr>
                        <tr><td class="col-text">{{ __('owner.annual_summary.waterfall.crew_share') }}</td><td class="col-num"><x-report-money :amount="$t['crew_share']" /></td></tr>
                    </tbody>
                </table>
            </td>
            <td class="dual-gap"></td>
            <td class="dual-col">
                <table class="report-table info-box" style="margin-top:8px;">
                    <thead>
                        <tr><th colspan="2">{{ __('owner.annual_summary.payroll_title') }}</th></tr>
                    </thead>
                    <tbody>
                        <tr><td class="col-text">{{ __('owner.annual_summary.payroll.crew_count') }}</td><td class="col-num">{{ $payroll['crew_count'] }}</td></tr>
                        <tr><td class="col-text">{{ __('owner.annual_summary.payroll.crew_pool') }}</td><td class="col-num"><x-report-money :amount="$payroll['crew_pool']" /></td></tr>
                        <tr><td class="col-text">{{ __('owner.annual_summary.payroll.owner_share') }}</td><td class="col-num"><x-report-money :amount="$payroll['owner_share']" /></td></tr>
                        <tr><td class="col-text">{{ __('owner.annual_summary.payroll.advances') }}</td><td class="col-num"><x-report-money :amount="$payroll['advances']" /></td></tr>
                        <tr><td class="col-text">{{ __('owner.annual_summary.payroll.paid') }}</td><td class="col-num"><x-report-money :amount="$payroll['paid']" /></td></tr>
                        <tr class="net-row"><td class="col-text">{{ __('owner.annual_summary.payroll.remaining') }}</td><td class="col-num"><x-report-money :amount="$payroll['remaining']" /></td></tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <x-report-amount-words :words="amount_to_words($t['net_profit'])" />

    {{-- ══════════════════ PAGE 2 — Monthly breakdown ══════════════════ --}}
    <div class="page-break">
        <div class="page-head">
            <span class="ph-title">{{ __('owner.annual_summary.breakdown_title') }}</span>
            <span class="ph-meta">{{ $reportTitle }} · {{ $phMeta }}</span>
        </div>

        <table class="report-table" style="margin-bottom:14px;">
            <thead>
                <tr>
                    <th class="col-text" style="width:9%">{{ __('owner.annual_summary.columns.month') }}</th>
                    <th style="width:8%">{{ __('owner.annual_summary.columns.status') }}</th>
                    <th>{{ __('owner.annual_summary.columns.sales') }}</th>
                    <th>{{ __('owner.annual_summary.columns.revenue') }}</th>
                    <th>{{ __('owner.annual_summary.columns.trip_expenses') }}</th>
                    <th>{{ __('owner.annual_summary.columns.general_expenses') }}</th>
                    <th>{{ __('owner.annual_summary.columns.depreciation') }}</th>
                    <th>{{ __('owner.annual_summary.columns.deferred') }}</th>
                    <th>{{ __('owner.annual_summary.columns.expenses') }}</th>
                    <th>{{ __('owner.annual_summary.columns.net_profit') }}</th>
                    <th>{{ __('owner.annual_summary.columns.crew_share') }}</th>
                    <th>{{ __('owner.annual_summary.columns.owner_share') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($months as $m => $closing)
                    <tr>
                        <td class="col-text">{{ $monthNames[$m] }}</td>
                        @if ($closing)
                            <td>{{ __('owner.month_closing.status_closed') }}</td>
                            <td class="col-num"><x-report-money :amount="(float) $closing->gross_sales" /></td>
                            <td class="col-num"><x-report-money :amount="(float) $closing->net_owner_revenue" /></td>
                            <td class="col-num"><x-report-money :amount="(float) $closing->trip_expenses" /></td>
                            <td class="col-num"><x-report-money :amount="(float) $closing->general_expenses" /></td>
                            <td class="col-num"><x-report-money :amount="(float) $closing->depreciation" /></td>
                            <td class="col-num"><x-report-money :amount="(float) $closing->depreciation_deferred" /></td>
                            <td class="col-num"><x-report-money :amount="(float) $closing->total_expenses" /></td>
                            <td class="col-num" style="color:{{ (float) $closing->net_profit >= 0 ? $green : $red }};"><x-report-money :amount="(float) $closing->net_profit" /></td>
                            <td class="col-num"><x-report-money :amount="(float) $closing->crew_share" /></td>
                            <td class="col-num"><x-report-money :amount="(float) $closing->owner_share" /></td>
                        @else
                            <td style="color:#999;">{{ __('owner.annual_summary.not_closed') }}</td>
                            @for ($i = 0; $i < 10; $i++)
                                <td class="col-num" style="color:#bbb;">—</td>
                            @endfor
                        @endif
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="net-row">
                    <td class="col-text" colspan="2">{{ __('owner.annual_summary.columns.total') }}</td>
                    <td class="col-num"><x-report-money :amount="$t['gross_sales']" /></td>
                    <td class="col-num"><x-report-money :amount="$t['net_owner_revenue']" /></td>
                    <td class="col-num"><x-report-money :amount="$t['trip_expenses']" /></td>
                    <td class="col-num"><x-report-money :amount="$t['general_expenses']" /></td>
                    <td class="col-num"><x-report-money :amount="$t['depreciation']" /></td>
                    <td class="col-num"><x-report-money :amount="$t['depreciation_deferred']" /></td>
                    <td class="col-num"><x-report-money :amount="$t['total_expenses']" /></td>
                    <td class="col-num"><x-report-money :amount="$t['net_profit']" /></td>
                    <td class="col-num"><x-report-money :amount="$t['crew_share']" /></td>
                    <td class="col-num"><x-report-money :amount="$t['owner_share']" /></td>
                </tr>
            </tfoot>
        </table>

        {{-- Quarterly performance · monthly net-profit trend --}}
        <table class="dual">
            <tr>
                <td class="dual-col" style="width:46%">
                    <div class="section-title" style="margin-top:4px;">{{ __('owner.annual_summary.quarterly_title') }}</div>
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th class="col-text" style="width:34%">{{ __('owner.annual_summary.cols.quarter') }}</th>
                                <th>{{ __('owner.annual_summary.cols.sales') }}</th>
                                <th>{{ __('owner.annual_summary.cols.expenses') }}</th>
                                <th>{{ __('owner.annual_summary.cols.net_profit') }}</th>
                                <th style="width:13%">{{ __('owner.annual_summary.cols.margin') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($quarters as $q => $qd)
                                <tr>
                                    <td class="col-text">{{ __('owner.annual_summary.quarters.' . $q) }}</td>
                                    @if ($qd['has'])
                                        <td class="col-num"><x-report-money :amount="$qd['sales']" /></td>
                                        <td class="col-num"><x-report-money :amount="$qd['expenses']" /></td>
                                        <td class="col-num" style="color:{{ $qd['net'] >= 0 ? $green : $red }};"><x-report-money :amount="$qd['net']" /></td>
                                        <td class="col-num">{{ number_format($qd['margin'], 1) }}%</td>
                                    @else
                                        <td class="col-num" style="color:#bbb;">—</td>
                                        <td class="col-num" style="color:#bbb;">—</td>
                                        <td class="col-num" style="color:#bbb;">—</td>
                                        <td class="col-num" style="color:#bbb;">—</td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="net-row">
                                <td class="col-text">{{ __('owner.annual_summary.columns.total') }}</td>
                                <td class="col-num"><x-report-money :amount="$t['gross_sales']" /></td>
                                <td class="col-num"><x-report-money :amount="$t['total_expenses']" /></td>
                                <td class="col-num"><x-report-money :amount="$t['net_profit']" /></td>
                                <td class="col-num">{{ number_format($a['margin'], 1) }}%</td>
                            </tr>
                        </tfoot>
                    </table>
                </td>
                <td class="dual-gap"></td>
                <td class="dual-col">
                    <div class="section-title" style="margin-top:4px;">{{ __('owner.annual_summary.trend_title') }}</div>
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th class="col-text" style="width:20%">{{ __('owner.annual_summary.columns.month') }}</th>
                                <th class="col-text">{{ __('owner.annual_summary.columns.net_profit') }}</th>
                                <th style="width:22%">{{ __('owner.annual_summary.cols.value') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($closedMonthsList as $m => $closing)
                                @php
                                    $net = (float) $closing->net_profit;
                                    $pct = $trendMax > 0 ? round(abs($net) / $trendMax * 100) : 0;
                                    $barColor = $net >= 0 ? $green : $red;
                                @endphp
                                <tr>
                                    <td class="col-text">{{ $monthNames[$m] }}</td>
                                    <td style="padding:5px 6px;">
                                        <div class="bar-track"><div class="bar-fill" style="width:{{ $pct }}%;background:{{ $barColor }};"></div></div>
                                    </td>
                                    <td class="col-num" style="color:{{ $barColor }};"><x-report-money :amount="$net" /></td>
                                </tr>
                            @empty
                                <tr><td colspan="3" style="color:#999;">{{ __('owner.annual_summary.no_data') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    {{-- ══════════════════ PAGE 3 — Sales & catch ══════════════════ --}}
    <div class="page-break">
        <div class="page-head">
            <span class="ph-title">{{ __('owner.annual_summary.sales_analysis_title') }}</span>
            <span class="ph-meta">{{ $reportTitle }} · {{ $phMeta }}</span>
        </div>

        <x-report-stats :items="[
            ['label' => __('owner.annual_summary.sales.gross'), 'value' => $salesData['totals']['gross'], 'money' => true],
            ['label' => __('owner.annual_summary.sales.net_owner'), 'value' => $salesData['totals']['net_owner'], 'money' => true],
            ['label' => __('owner.annual_summary.sales.invoices'), 'value' => number_format($salesData['totals']['invoices'])],
            ['label' => __('owner.annual_summary.sales.avg_invoice'), 'value' => $salesData['totals']['avg_invoice'], 'money' => true],
        ]" />

        <table class="dual" style="margin-bottom:14px;">
            <tr>
                <td class="dual-col" style="width:52%">
                    <div class="section-title" style="margin-top:4px;">{{ __('owner.annual_summary.by_fish_title') }}</div>
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th class="col-text">{{ __('owner.annual_summary.cols.species') }}</th>
                                <th style="width:20%">{{ __('owner.annual_summary.cols.quantity') }}</th>
                                <th style="width:20%">{{ __('owner.annual_summary.cols.weight') }}</th>
                                <th style="width:24%">{{ __('owner.annual_summary.cols.revenue') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($salesData['by_fish'] as $fish)
                                <tr>
                                    <td class="col-text">{{ $fish['name'] }}</td>
                                    <td class="col-num">{{ number_format($fish['quantity'], 2) }}</td>
                                    <td class="col-num">{{ number_format($fish['weight'], 2) }}@if (! empty($fish['unit_name'])) {{ $fish['unit_name'] }}@endif</td>
                                    <td class="col-num"><x-report-money :amount="$fish['total']" /></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" style="color:#999;">{{ __('owner.annual_summary.no_data') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </td>
                <td class="dual-gap"></td>
                <td class="dual-col">
                    <div class="section-title" style="margin-top:4px;">{{ __('owner.annual_summary.top_customers_title') }}</div>
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th class="col-text">{{ __('owner.annual_summary.cols.customer') }}</th>
                                <th style="width:24%">{{ __('owner.annual_summary.cols.invoices') }}</th>
                                <th style="width:30%">{{ __('owner.annual_summary.cols.total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($salesData['top_customers'] as $customer)
                                <tr>
                                    <td class="col-text">{{ $customer['name'] }}</td>
                                    <td class="col-num">{{ number_format($customer['invoices']) }}</td>
                                    <td class="col-num"><x-report-money :amount="$customer['total']" /></td>
                                </tr>
                            @empty
                                <tr><td colspan="3" style="color:#999;">{{ __('owner.annual_summary.no_data') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>

        @if (! empty($salesData['by_boat']))
            <div class="section-title">{{ __('owner.annual_summary.by_boat_title') }}</div>
            <table class="report-table" style="margin-bottom:14px;">
                <thead>
                    <tr>
                        <th class="col-text">{{ __('owner.annual_summary.cols.boat') }}</th>
                        <th style="width:20%">{{ __('owner.annual_summary.cols.invoices') }}</th>
                        <th style="width:24%">{{ __('owner.annual_summary.cols.total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($salesData['by_boat'] as $boatRow)
                        <tr>
                            <td class="col-text">{{ $boatRow['name'] }}</td>
                            <td class="col-num">{{ number_format($boatRow['invoices']) }}</td>
                            <td class="col-num"><x-report-money :amount="$boatRow['total']" /></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- Catch production --}}
        <div class="section-bar">{{ __('owner.annual_summary.catch_title') }}</div>
        <table class="info-bar" style="margin-top:8px;">
            <tr>
                <td>
                    <span class="ib-label">{{ __('owner.annual_summary.catch.trips_with_catch') }}</span>
                    <span class="ib-value">{{ number_format($catchData['trips_with_catch']) }}</span>
                </td>
                <td>
                    <span class="ib-label">{{ __('owner.annual_summary.catch.total_weight') }}</span>
                    <span class="ib-value">{{ number_format($catchData['total_weight'], 2) }}</span>
                </td>
                <td>
                    <span class="ib-label">{{ __('owner.annual_summary.catch.total_amount') }}</span>
                    <span class="ib-value"><x-report-money :amount="$catchData['total_amount']" /></span>
                </td>
            </tr>
        </table>

        <div class="section-title">{{ __('owner.annual_summary.catch.by_species_title') }}</div>
        <table class="report-table">
            <thead>
                <tr>
                    <th class="col-text">{{ __('owner.annual_summary.cols.species') }}</th>
                    <th style="width:26%">{{ __('owner.annual_summary.cols.weight') }}</th>
                    <th style="width:26%">{{ __('owner.annual_summary.cols.value') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($catchData['by_species'] as $species)
                    <tr>
                        <td class="col-text">{{ $species['name'] }}</td>
                        <td class="col-num">{{ number_format($species['weight'], 2) }}@if (! empty($species['unit_name'])) {{ $species['unit_name'] }}@endif</td>
                        <td class="col-num"><x-report-money :amount="$species['total']" /></td>
                    </tr>
                @empty
                    <tr><td colspan="3" style="color:#999;">{{ __('owner.annual_summary.no_data') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ══════════════════ PAGE 4 — Costs, crew & verdict ══════════════════ --}}
    <div class="page-break">
        <div class="page-head">
            <span class="ph-title">{{ __('owner.annual_summary.analysis_title') }}</span>
            <span class="ph-meta">{{ $reportTitle }} · {{ $phMeta }}</span>
        </div>

        {{-- Expenses by category · by type --}}
        <table class="dual" style="margin-bottom:14px;">
            <tr>
                <td class="dual-col" style="width:52%">
                    <table class="report-table info-box">
                        <thead>
                            <tr>
                                <th class="col-text">{{ __('owner.annual_summary.expenses_title') }}</th>
                                <th style="width:40%">{{ __('owner.annual_summary.columns.expenses') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($expensesByCategory as $cat)
                                <tr>
                                    <td class="col-text">{{ $cat['name'] }}</td>
                                    <td class="col-num"><x-report-money :amount="$cat['total']" /></td>
                                </tr>
                            @empty
                                <tr><td colspan="2" style="color:#999;">{{ __('owner.annual_summary.no_closings') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </td>
                <td class="dual-gap"></td>
                <td class="dual-col">
                    <table class="report-table info-box">
                        <thead>
                            <tr>
                                <th class="col-text">{{ __('owner.annual_summary.expenses_type_title') }}</th>
                                <th style="width:40%">{{ __('owner.annual_summary.columns.expenses') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($expensesByType as $type)
                                <tr>
                                    <td class="col-text">{{ $type['label'] }}</td>
                                    <td class="col-num"><x-report-money :amount="$type['total']" /></td>
                                </tr>
                            @empty
                                <tr><td colspan="2" style="color:#999;">{{ __('owner.annual_summary.no_closings') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>

        {{-- Crew earnings (annual) --}}
        <div class="section-bar">{{ __('owner.annual_summary.crew_earnings_title') }}</div>
        <table class="report-table" style="margin-top:8px;margin-bottom:14px;">
            <thead>
                <tr>
                    <th style="width:5%">#</th>
                    <th class="col-text" style="width:24%">{{ __('owner.annual_summary.cols.name') }}</th>
                    <th>{{ __('owner.annual_summary.cols.role') }}</th>
                    <th style="width:9%">{{ __('owner.annual_summary.cols.months') }}</th>
                    <th>{{ __('owner.annual_summary.cols.earned') }}</th>
                    <th>{{ __('owner.annual_summary.cols.advances') }}</th>
                    <th>{{ __('owner.annual_summary.cols.paid') }}</th>
                    <th>{{ __('owner.annual_summary.cols.remaining') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($crewMembers as $member)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="col-text">{{ $member['name'] }}</td>
                        <td>{{ $member['role'] }}</td>
                        <td class="col-num">{{ $member['months'] }}</td>
                        <td class="col-num"><x-report-money :amount="$member['earned']" /></td>
                        <td class="col-num"><x-report-money :amount="$member['advances']" /></td>
                        <td class="col-num"><x-report-money :amount="$member['paid']" /></td>
                        <td class="col-num"><x-report-money :amount="$member['remaining']" /></td>
                    </tr>
                @empty
                    <tr><td colspan="8" style="color:#999;">{{ __('owner.annual_summary.no_data') }}</td></tr>
                @endforelse
            </tbody>
            @if (! empty($crewMembers))
                <tfoot>
                    <tr class="net-row">
                        <td class="col-text" colspan="4">{{ __('owner.annual_summary.columns.total') }}</td>
                        <td class="col-num"><x-report-money :amount="$payroll['crew_pool']" /></td>
                        <td class="col-num"><x-report-money :amount="$payroll['advances']" /></td>
                        <td class="col-num"><x-report-money :amount="$payroll['paid']" /></td>
                        <td class="col-num"><x-report-money :amount="$payroll['remaining']" /></td>
                    </tr>
                </tfoot>
            @endif
        </table>

        {{-- Annual analysis: trip activity · insights · recommendations --}}
        <div class="section-bar">{{ __('owner.annual_summary.analysis_title') }}</div>
        <table class="dual" style="margin-top:8px;margin-bottom:14px;">
            <tr>
                <td class="dual-col" style="width:24%">
                    <table class="report-table info-box">
                        <thead>
                            <tr><th colspan="2">{{ __('owner.annual_summary.trips_title') }}</th></tr>
                        </thead>
                        <tbody>
                            <tr><td class="col-text">{{ __('owner.annual_summary.trips.total') }}</td><td class="col-num">{{ $trips['total'] }}</td></tr>
                            <tr><td class="col-text">{{ __('owner.annual_summary.trips.sold') }}</td><td class="col-num">{{ $trips['sold'] }}</td></tr>
                            <tr><td class="col-text">{{ __('owner.annual_summary.trips.active') }}</td><td class="col-num">{{ $trips['active'] }}</td></tr>
                            <tr><td class="col-text">{{ __('owner.annual_summary.trips.cancelled') }}</td><td class="col-num">{{ $trips['cancelled'] }}</td></tr>
                        </tbody>
                    </table>
                </td>
                <td class="dual-gap"></td>
                <td class="dual-col">
                    <div class="section-title" style="margin-top:4px;">{{ __('owner.annual_summary.insights_title') }}</div>
                    <ul class="cf-notes" style="padding-{{ $listPad }}:16px;">
                        @foreach ($a['insights'] as $insight)
                            <li>{{ $insight }}</li>
                        @endforeach
                    </ul>
                </td>
                <td class="dual-gap"></td>
                <td class="dual-col">
                    <div class="section-title" style="margin-top:4px;">{{ __('owner.annual_summary.recommendations_title') }}</div>
                    <ul class="cf-notes" style="padding-{{ $listPad }}:16px;">
                        @foreach ($a['recommendations'] as $rec)
                            <li>{{ $rec }}</li>
                        @endforeach
                    </ul>
                </td>
            </tr>
        </table>

        @if ($t['depreciation_deferred'] > 0)
            <p style="font-size:11px;color:#555;margin:0 0 10px;">
                {{ __('owner.annual_summary.deferred_note') }}
            </p>
        @endif

        <x-report-signatures :items="[
            __('owner.annual_summary.signatures.prepared_by'),
            __('owner.annual_summary.signatures.reviewed_by'),
            __('owner.annual_summary.signatures.owner'),
        ]" />
    </div>

    <table class="report-footer">
        <tr>
            <td>{{ $companyName }} — {{ __('owner.reports.all_rights_reserved') }} © {{ $year }}</td>
        </tr>
    </table>
</x-report-layout>
