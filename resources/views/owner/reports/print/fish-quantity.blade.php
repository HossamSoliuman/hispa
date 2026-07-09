<x-report-layout
    :title="__('owner.sales.fish_quntity')"
    title-en="Fish Stock Report"
    :settings="$settings"
    :qr-code="$settings['qr_code'] ?? null">

    <x-report-masthead
        :title="__('owner.sales.fish_quntity')"
        :settings="$settings" />

    {{-- Applied filters strip --}}
    <table class="info-bar">
        <tr>
            <td><span class="ib-label">{{ __('owner.profit_loss.from_date') }}</span><span class="ib-value">{{ $filters['from'] }}</span></td>
            <td><span class="ib-label">{{ __('owner.profit_loss.to_date') }}</span><span class="ib-value">{{ $filters['to'] }}</span></td>
            <td><span class="ib-label">{{ __('owner.profit_loss.boat') }}</span><span class="ib-value">{{ $filters['boat'] ?? __('owner.stock_report.all') }}</span></td>
            <td><span class="ib-label">{{ __('owner.sales.trip') }}</span><span class="ib-value">{{ $filters['trip'] ?? __('owner.stock_report.all') }}</span></td>
            <td><span class="ib-label">{{ __('owner.sales.fish') }}</span><span class="ib-value">{{ $filters['fish'] ?? __('owner.stock_report.all') }}</span></td>
        </tr>
    </table>

    <x-report-stats :items="[
        ['label' => __('owner.stock_report.total_fish_types'), 'value' => $stocks->pluck('fish_id')->unique()->count()],
        ['label' => __('owner.catch.weight'), 'value' => formatWeightByUnit($stocks)],
        ['label' => __('owner.catch.total_price'), 'value' => $totalPrice, 'money' => true],
    ]" />

    @if($stocks->isEmpty())
        <div class="alert alert-warning">
            <strong>{{ __('owner.reports.no_data_found') }}</strong>
            <p class="mb-0 text-muted">{{ __('owner.reports.try_adjust_filters') }}</p>
        </div>
    @else
        <table class="report-table block">
            <thead>
                <tr>
                    <th style="width:6%;">#</th>
                    <th class="col-text" style="width:30%;">{{ __('owner.catch.fish') }}</th>
                    <th class="col-num" style="width:16%;">{{ __('owner.catch.weight') }}</th>
                    <th style="width:16%;">{{ __('owner.catch.unit') }}</th>
                    <th class="col-num" style="width:16%;">{{ __('owner.sales.price_per_kilo') }}</th>
                    <th class="col-num" style="width:16%;">{{ __('owner.catch.total_price') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stocks as $stock)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="col-text">{{ $stock->fish->name ?? '—' }}</td>
                        <td class="col-num">{{ number_format($stock->weight, 2) }}</td>
                        <td>{{ $stock->unit->name ?? '—' }}</td>
                        <td class="col-num"><x-report-money :amount="$stock->price_per_kg" /></td>
                        <td class="col-num"><x-report-money :amount="$stock->weight * $stock->price_per_kg" /></td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                @foreach($stocks->groupBy(fn ($s) => $s->unit->name ?? '—') as $unitName => $group)
                    <tr>
                        <td colspan="2">{{ __('owner.catch.total') }} ({{ $unitName }})</td>
                        <td class="col-num">{{ number_format($group->sum('weight'), 2) }}</td>
                        <td colspan="3">{{ $unitName }}</td>
                    </tr>
                @endforeach
                <tr class="net-row">
                    <td colspan="5">{{ __('owner.catch.total_price') }}</td>
                    <td class="col-num"><x-report-money :amount="$totalPrice" /></td>
                </tr>
            </tfoot>
        </table>
    @endif

    <table class="report-footer">
        <tr>
            <td class="rf-text">
                {{ $settings['company_name'] ?? $settings['title'] ?? '' }} — {{ __('owner.reports.all_rights_reserved') }} © {{ date('Y') }}
            </td>
        </tr>
    </table>

</x-report-layout>
