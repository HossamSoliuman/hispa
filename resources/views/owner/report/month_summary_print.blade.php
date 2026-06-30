@php
    $grossSales = (float) $f['gross_sales'];
    $tripExpenses = (float) $f['trip_expenses'];
    $generalExpenses = (float) $f['general_expenses'];
    $depreciation = (float) $f['depreciation'];
    $totalExpenses = (float) $f['total_expenses'];
    $netProfit = (float) $f['net_profit'];
    $netOwnerRevenue = (float) ($f['net_owner_revenue'] ?? ($grossSales - $totalExpenses));
    $ownerPercent = (float) $f['owner_percent'];
    $ownerShare = (float) $f['owner_share'];
    $crewShare = (float) $f['crew_share'];
    $crewCount = (int) $f['crew_count'];
    $perFisherman = (float) $f['per_fisherman'];

    $selectedBoat = ($boatId && isset($boats)) ? $boats->firstWhere('id', $boatId) : null;
    $boatLabel = $selectedBoat ? ($selectedBoat->name ?? $selectedBoat->name_ar) : __('owner.month_summary.all_boats');

    $subtitle = __('owner.month_summary.from_date').' '.$from.' '.__('owner.month_summary.to_date').' '.$to
        .' — '.__('owner.month_summary.boat').': '.$boatLabel;

    $companyName = $settings['title'] ?? ($settings['company_name'] ?? ($settings['name'] ?? ''));
@endphp

<x-report-layout
    :title="__('owner.month_summary.title')"
    :document-number="'MS-' . \Illuminate\Support\Carbon::parse($from)->format('Ym')"
    :settings="$settings ?? []"
    :qr-code="$settings['qr_code'] ?? ''"
>
    <x-report-masthead
        :title="__('owner.month_summary.title')"
        :subtitle="$subtitle"
        :settings="$settings ?? []" />

    {{-- Meta strip --}}
    <div class="meta-row">
        <span class="meta-item">
            <span class="lbl">{{ __('owner.month_summary.from_date') }}:</span>
            <span class="val-box">{{ $from }}</span>
        </span>
        <span class="meta-item">
            <span class="lbl">{{ __('owner.month_summary.to_date') }}:</span>
            <span class="val-box">{{ $to }}</span>
        </span>
        <span class="meta-item">
            <span class="lbl">{{ __('owner.month_summary.boat') }}:</span>
            <span class="val-box">{{ $boatLabel }}</span>
        </span>
    </div>

    {{-- KPI cards --}}
    <x-report-stats :items="[
        ['label' => __('owner.month_summary.total_sales'), 'value' => number_format($grossSales, 2)],
        ['label' => __('owner.month_summary.net_owner_revenue'), 'value' => number_format($netOwnerRevenue, 2)],
        ['label' => __('owner.month_summary.total_expenses'), 'value' => number_format($totalExpenses, 2)],
        ['label' => __('owner.month_summary.net_profit_loss'), 'value' => number_format($netProfit, 2), 'color' => $netProfit < 0 ? '#dc3545' : null],
        ['label' => __('owner.month_summary.owner_share'), 'value' => number_format($ownerShare, 2)],
        ['label' => __('owner.month_summary.crew_share'), 'value' => number_format($crewShare, 2)],
    ]" />

    <table class="dual">
        <tr>
            {{-- Profit & loss statement --}}
            <td class="dual-col" style="width:58%;">
                <div class="section-bar">{{ __('owner.month_summary.statement_title') }}</div>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th class="col-text" style="width:70%;">{{ __('owner.month_summary.item') }}</th>
                            <th class="col-num">{{ __('owner.month_summary.amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th class="col-text" colspan="2">{{ __('owner.month_summary.revenue') }}</th>
                        </tr>
                        <tr>
                            <td class="col-text" style="padding-inline-start:18px;">{{ __('owner.month_summary.total_sales') }}</td>
                            <td class="col-num">{{ number_format($grossSales, 2) }}</td>
                        </tr>

                        <tr>
                            <th class="col-text" colspan="2">{{ __('owner.month_summary.operating_expenses') }}</th>
                        </tr>
                        @forelse ($expenses['operating'] as $row)
                            <tr>
                                <td class="col-text" style="padding-inline-start:18px;">{{ $row['category'] }}</td>
                                <td class="col-num">({{ number_format($row['amount'], 2) }})</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="col-text" style="padding-inline-start:18px;color:#888;">{{ __('owner.month_summary.no_expenses') }}</td>
                                <td class="col-num" style="color:#888;">0.00</td>
                            </tr>
                        @endforelse
                        <tr>
                            <th class="col-text">{{ __('owner.month_summary.total_operating_expenses') }}</th>
                            <th class="col-num">({{ number_format($tripExpenses, 2) }})</th>
                        </tr>

                        <tr>
                            <th class="col-text" colspan="2">{{ __('owner.month_summary.general_expenses') }}</th>
                        </tr>
                        @forelse ($expenses['general'] as $row)
                            <tr>
                                <td class="col-text" style="padding-inline-start:18px;">{{ $row['category'] }}</td>
                                <td class="col-num">({{ number_format($row['amount'], 2) }})</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="col-text" style="padding-inline-start:18px;color:#888;">{{ __('owner.month_summary.no_expenses') }}</td>
                                <td class="col-num" style="color:#888;">0.00</td>
                            </tr>
                        @endforelse
                        <tr>
                            <th class="col-text">{{ __('owner.month_summary.total_general_expenses') }}</th>
                            <th class="col-num">({{ number_format($generalExpenses, 2) }})</th>
                        </tr>

                        <tr>
                            <th class="col-text" colspan="2">{{ __('owner.month_summary.depreciation') }}</th>
                        </tr>
                        <tr>
                            <td class="col-text" style="padding-inline-start:18px;">{{ __('owner.month_summary.depreciation') }}</td>
                            <td class="col-num">({{ number_format($depreciation, 2) }})</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="col-text">{{ __('owner.month_summary.total_expenses') }}</th>
                            <th class="col-num">({{ number_format($totalExpenses, 2) }})</th>
                        </tr>
                        <tr class="net-row">
                            <th class="col-text">{{ __('owner.month_summary.net_profit_loss') }}</th>
                            <th class="col-num">{{ number_format($netProfit, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </td>
            <td class="dual-gap"></td>

            {{-- Profit distribution --}}
            <td class="dual-col" style="width:42%;">
                <div class="section-bar">{{ __('owner.month_summary.distribution') }}</div>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th class="col-text" style="width:62%;">{{ __('owner.month_summary.item') }}</th>
                            <th class="col-num">{{ __('owner.month_summary.amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="col-text">{{ __('owner.month_summary.owner_share') }} ({{ number_format($ownerPercent, 0) }}%)</td>
                            <td class="col-num">{{ number_format($ownerShare, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="col-text">{{ __('owner.month_summary.crew_share') }} ({{ number_format(100 - $ownerPercent, 0) }}%)</td>
                            <td class="col-num">{{ number_format($crewShare, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="col-text">{{ __('owner.month_summary.crew_count') }}</td>
                            <td class="col-num">{{ number_format($crewCount, 0) }}</td>
                        </tr>
                        <tr class="net-row">
                            <th class="col-text">{{ __('owner.month_summary.per_fisherman') }}</th>
                            <th class="col-num">{{ number_format($perFisherman, 2) }}</th>
                        </tr>
                    </tbody>
                </table>

                <table class="info-bar" style="margin-top:10px;">
                    <tr>
                        <td>
                            <span class="ib-label">{{ __('owner.month_summary.net_owner_revenue') }}</span>
                            <span class="ib-value">{{ number_format($netOwnerRevenue, 2) }}</span>
                        </td>
                        <td>
                            <span class="ib-label">{{ __('owner.month_summary.net_profit_loss') }}</span>
                            <span class="ib-value">{{ number_format($netProfit, 2) }}</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <x-report-signatures :items="[
        __('owner.reports.sig_accountant'),
        __('owner.reports.sig_financial_manager'),
        __('owner.reports.sig_general_manager'),
    ]" />

    <table class="report-footer">
        <tr>
            <td>{{ $companyName }} — {{ __('owner.reports.all_rights_reserved') }} © {{ date('Y') }} · {{ __('owner.month_summary.currency_note') }}</td>
        </tr>
    </table>
</x-report-layout>
