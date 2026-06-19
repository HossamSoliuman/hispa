<x-report-layout :settings="$settings ?? []">

    <x-report-header
        :documentNumber="'#' . str_pad($catch->id ?? 0, 8, '0', STR_PAD_LEFT)"
        :title="$catch ? (__('owner.reports.catch_report') . ' — ' . ($catch->trip?->number ?? '')) : __('owner.reports.all_catchs_report')"
        titleEn="{{ $catch ? 'Catch Report — ' . ($catch->trip?->number ?? '') : 'All Catches Report' }}"
        :settings="$settings ?? []"
    />

    <x-report-info :settings="$settings ?? []">
        <x-slot:additionalInfo>
            @if ($catch)
                <div class="info-row">
                    @if ($catch->trip?->boat)
                        <div class="info-item">
                            <span class="label">{{ __('owner.generated.the_boat') }}</span>
                            <span class="value">{{ $catch->trip->boat->name }}</span>
                        </div>
                    @endif
                    @if ($catch->trip?->number)
                        <div class="info-item">
                            <span class="label">{{ __('owner.trips.trip_number') }}:</span>
                            <span class="value">#{{ $catch->trip->number }}</span>
                        </div>
                    @endif
                    @if ($catch->created_at)
                        <div class="info-item">
                            <span class="label">{{ __('owner.generated.catch_date') }}</span>
                            <span class="value">{{ $catch->created_at->format('Y-m-d') }}</span>
                        </div>
                    @endif
                </div>
            @endif
        </x-slot:additionalInfo>
    </x-report-info>

    @if ($catch)
        @php
            $totalWeight = $catch->details->sum('weight');
            $totalPrice  = $catch->details->sum('total_price');
        @endphp

        <x-report-stats :items="[
            ['label' => __('owner.catch.items_count'), 'value' => $catch->details->count()],
            ['label' => __('owner.catch.weight'), 'value' => number_format($totalWeight, 2)],
            ['label' => __('owner.catch.total_price'), 'value' => number_format($totalPrice, 2)],
        ]" />

        <x-report-table :headers="[
            '#',
            __('owner.catch.fish'),
            __('owner.catch.weight'),
            __('owner.catch.unit'),
            __('owner.sales.price_per_kilo'),
            __('owner.catch.total_price'),
        ]" :data="$catch->details">
            @foreach ($catch->details as $detail)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $detail->fish?->scientific_name ?? '—' }}</td>
                    <td>{{ number_format($detail->weight, 2) }}</td>
                    <td>{{ $detail->unit?->name ?? '—' }}</td>
                    <td style="text-align:end;">{{ number_format($detail->price_per_kg, 2) }}</td>
                    <td style="text-align:end;font-weight:700;">{{ number_format($detail->total_price, 2) }}</td>
                </tr>
            @endforeach
        </x-report-table>

        <x-report-summary :qr-code="$settings['qr_code'] ?? null">
            <x-report-summary-row
                :label="__('owner.catch.items_count')"
                :value="$catch->details->count()"
            />
            <x-report-summary-row
                :label="__('owner.catch.weight')"
                :value="number_format($totalWeight, 2)"
            />
            <x-report-summary-row
                :label="__('owner.catch.total_price')"
                :value="number_format($totalPrice, 2)"
                :showCurrency="true"
                :highlight="true"
            />
        </x-report-summary>
    @endif

    <div class="footer">
        <p>{{ $settings['company_name'] ?? $settings['name'] ?? '' }} — {{ __('owner.reports.all_rights_reserved') }} © {{ date('Y') }}</p>
        <p>{{ __('owner.reports.thank_you') }}</p>
    </div>

</x-report-layout>
