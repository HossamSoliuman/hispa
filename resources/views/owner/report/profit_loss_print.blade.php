@php
    $cur = __('owner.reports.report_currency');
    $netProfit = (float) $f['net_profit'];

    $selectedBoat = ($boatId && isset($boats)) ? $boats->firstWhere('id', $boatId) : null;
    $boatLabel = $selectedBoat ? ($selectedBoat->name ?? $selectedBoat->name_ar) : __('owner.profit_loss.all_boats');

    $subtitle = __('owner.reports.from_date').' '.$from.' '.__('owner.reports.to_date').' '.$to.' — '.__('owner.profit_loss.boat').': '.$boatLabel;

    $pct = fn ($value, $base) => $base > 0 ? number_format($value / $base * 100, 2).'' : '—';
    $revenueBase = (float) $f['gross_sales'];
    $expenseBase = (float) $f['total_expenses'];
@endphp

<x-report-layout
    :title="__('owner.profit_loss.title')"
    :documentNumber="'PL-' . now()->format('YmdHis')"
    :settings="$settings ?? []"
>
    {{-- Scoped cleanups for this report only: borderless KPI text and softer
         section bars. The table header keeps the shared solid-black bar. --}}
    <x-slot:extraStyles>
        .report-stat-value { border: none; padding: 2px 4px; }
        .section-bar { background: #f2f2f2; color: #1a1a1a; border: 1px solid #cfcfcf; font-size: 9pt; }
        table.report-table tr.net-row th, table.report-table tr.net-row td { background: #e2e2e2; color: #1a1a1a; border-color: #cfcfcf; }
    </x-slot:extraStyles>

    <x-report-masthead
        :title="__('owner.profit_loss.title')"
        :subtitle="$subtitle"
        :settings="$settings ?? []" />

    {{-- KPI cards (borderless, text only) --}}
    <x-report-stats :items="[
        ['label' => __('owner.profit_loss.total_sales'), 'value' => number_format($f['gross_sales'], 2)],
        ['label' => __('owner.profit_loss.net_owner_revenue'), 'value' => number_format($f['net_owner_revenue'], 2)],
        ['label' => __('owner.profit_loss.total_expenses'), 'value' => number_format($f['total_expenses'], 2)],
        ['label' => __('owner.profit_loss.net_profit_loss'), 'value' => number_format($netProfit, 2)],
        ['label' => __('owner.profit_loss.owner_share'), 'value' => number_format($f['owner_share'], 2)],
        ['label' => __('owner.profit_loss.crew_share'), 'value' => number_format($f['crew_share'], 2)],
    ]" />

    {{-- Revenues / Expenses / Profit summary — three columns on a single row --}}
    <table class="dual">
        <tr>
            <td class="dual-col" style="width:33%;">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th class="col-text" style="width:56%;">{{ __('owner.reports.expense_category') }}</th>
                            <th class="col-num" style="width:26%;">{{ __('owner.reports.amount') }}</th>
                            <th style="width:18%;">{{ __('owner.reports.percentage') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="col-text">{{ __('owner.profit_loss.total_sales') }}</td>
                            <td class="col-num">{{ number_format($f['gross_sales'], 2) }}</td>
                            <td>{{ $pct($f['gross_sales'], $revenueBase) }}</td>
                        </tr>
                        <tr>
                            <td class="col-text">{{ __('owner.profit_loss.commission_labor') }}</td>
                            <td class="col-num">({{ number_format($f['commission_labor'], 2) }})</td>
                            <td>{{ $pct($f['commission_labor'], $revenueBase) }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="col-text">{{ __('owner.profit_loss.net_owner_revenue') }}</th>
                            <th class="col-num">{{ number_format($f['net_owner_revenue'], 2) }}</th>
                            <th>{{ $pct($f['net_owner_revenue'], $revenueBase) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </td>
            <td class="dual-gap"></td>
            <td class="dual-col" style="width:33%;">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th class="col-text" style="width:56%;">{{ __('owner.reports.expense_category') }}</th>
                            <th class="col-num" style="width:26%;">{{ __('owner.reports.amount') }}</th>
                            <th style="width:18%;">{{ __('owner.reports.percentage') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="col-text">{{ __('owner.profit_loss.trip_expenses') }}</td>
                            <td class="col-num">{{ number_format($f['trip_expenses'], 2) }}</td>
                            <td>{{ $pct($f['trip_expenses'], $expenseBase) }}</td>
                        </tr>
                        <tr>
                            <td class="col-text">{{ __('owner.profit_loss.general_expenses') }}</td>
                            <td class="col-num">{{ number_format($f['general_expenses'], 2) }}</td>
                            <td>{{ $pct($f['general_expenses'], $expenseBase) }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="col-text">{{ __('owner.profit_loss.total_expenses') }}</th>
                            <th class="col-num">{{ number_format($f['total_expenses'], 2) }}</th>
                            <th>100.00</th>
                        </tr>
                    </tfoot>
                </table>
            </td>
            <td class="dual-gap"></td>
            <td class="dual-col" style="width:33%;">
                <table class="report-table">
                    <tbody>
                        <tr>
                            <th class="col-text" style="width:62%;">{{ __('owner.profit_loss.net_owner_revenue') }}</th>
                            <td class="col-num">{{ number_format($f['net_owner_revenue'], 2) }}</td>
                        </tr>
                        <tr>
                            <th class="col-text">{{ __('owner.profit_loss.total_expenses') }}</th>
                            <td class="col-num">({{ number_format($f['total_expenses'], 2) }})</td>
                        </tr>
                        <tr class="net-row">
                            <th class="col-text">{{ __('owner.profit_loss.net_profit_loss') }}</th>
                            <td class="col-num">{{ number_format($netProfit, 2) }}</td>
                        </tr>
                        <tr>
                            <th class="col-text">{{ __('owner.profit_loss.owner_share') }} ({{ rtrim(rtrim(number_format($f['owner_percent'], 2), '0'), '.') }})</th>
                            <td class="col-num">{{ number_format($f['owner_share'], 2) }}</td>
                        </tr>
                        <tr>
                            <th class="col-text">{{ __('owner.profit_loss.crew_share') }}</th>
                            <td class="col-num">{{ number_format($f['crew_share'], 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    @if (!empty($f['crew_distribution']) && count($f['crew_distribution']) > 0)
        <table class="report-table block">
            <thead>
                <tr>
                    <th class="col-text" style="width:34%;">{{ __('owner.month_closing.columns.member') }}</th>
                    <th style="width:18%;">{{ __('owner.month_closing.columns.role') }}</th>
                    <th style="width:16%;">{{ __('owner.month_closing.columns.custom_percent') }}</th>
                    <th style="width:14%;">{{ __('owner.month_closing.columns.shares') }}</th>
                    <th class="col-num" style="width:18%;">{{ __('owner.month_closing.columns.due') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($f['crew_distribution'] as $member)
                    <tr>
                        <td class="col-text">{{ $member['name'] }}</td>
                        <td>{{ $member['role'] }}</td>
                        <td>{{ $member['custom_percent'] !== null ? number_format($member['custom_percent'], 2) . '' : '-' }}</td>
                        <td>{{ $member['custom_percent'] !== null ? '-' : number_format($member['shares'], 2) }}</td>
                        <td class="col-num">{{ number_format($member['due'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="col-text">{{ __('owner.profit_loss.crew_share') }}</th>
                    <th class="col-num">{{ number_format(collect($f['crew_distribution'])->sum('due'), 2) }} {{ $cur }}</th>
                </tr>
            </tfoot>
        </table>
    @endif

    <x-report-signatures :items="[
        __('owner.reports.sig_accountant'),
        __('owner.reports.sig_financial_manager'),
        __('owner.reports.sig_general_manager'),
    ]" />

    <table class="report-footer">
        <tr>
            <td>{{ $settings['title'] ?? $settings['company_name'] ?? '' }} — {{ __('owner.reports.all_rights_reserved') }} © {{ date('Y') }}</td>
        </tr>
    </table>
</x-report-layout>
