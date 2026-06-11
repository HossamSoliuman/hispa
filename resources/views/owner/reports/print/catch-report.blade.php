<x-report-layout :title="$catch
    ? __('owner.reports.catch_report') . ' #' . $catch->trip->name
    : __('owner.reports.all_catchs_report')" :title-en="$catch ? 'catch Report #' . $catch->number : 'All catchs Report'" :document-number="0">

    <style>
        /* Currency symbol styling */
        .currency-symbol {
            display: inline-flex;
            align-items: center;
            justify-content: flex-end;
            gap: .18rem;
            font-size: 0.95rem;
        }

        .currency-symbol x-riyal-icon,
        .currency-symbol svg {
            width: 0.8rem;
            height: auto;
            display: inline-block;
            vertical-align: middle;
        }

        /* Table layout */
        table.report-table {
            table-layout: auto;
            width: 100%;
        }

        table.report-table th,
        table.report-table td {
            word-wrap: break-word;
            overflow-wrap: break-word;
            vertical-align: middle;
            white-space: normal;
            padding: .45rem .8rem;
        }

        /* Right-align numeric columns */
        table.report-table th:nth-child(5),
        table.report-table td:nth-child(5),
        table.report-table th:nth-child(6),
        table.report-table td:nth-child(6) {
            text-align: right;
        }
    </style>

    <x-report-header :document-number="'#' . str_pad(0, 8, '0', STR_PAD_LEFT)" :title="$catch ? $catch->trip->name : __('owner.reports.all_catchs_report')" :title-en="$catch ? '{{ __('owner.menu.catches') }}' : 'All catchs Report'" :settings="$settings" />

    <x-report-info>
        <x-slot:additionalInfo>
            @if ($catch)
                @if ($catch->trip?->boat)
                    <p><strong>{{ __('owner.generated.the_boat') }}</strong> {{ $catch->trip?->boat?->name }}</p>
                @endif
                @if ($catch->created_at)
                    <p><strong>{{ __('owner.generated.catch_date') }}</strong> {{ $catch->created_at->format('Y-m-d') }}
                    </p>
                @endif
            @endif
        </x-slot:additionalInfo>
    </x-report-info>



    @if ($catch)
        {{-- Single catch detailed breakdown --}}
        <div class="summary-section mt-4">
            <h5 class="fw-bold mb-3">{{ __('owner.reports.catch_breakdown') }}</h5>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('owner.catch.fish') }}</th>
                        <th>{{ __('owner.catch.weight') }}</th>
                        <th>{{ __('owner.catch.unit') }}</th>
                        <th>{{ __('owner.sales.price_per_kilo') }}</th>
                        <th>{{ __('owner.catch.total_price') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($catch->details as $detail)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $detail->fish->scientific_name }}</td>
                            <td>{{ number_format($detail->weight, 2) }}</td>
                            <td>{{ $detail->unit->name ?? '—' }}</td>
                            <td>{{ number_format($detail->price_per_kg, 2) }}</td>
                            <td>{{ number_format($detail->total_price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif


    <div class="footer">
        <p>{{ ' ' }} - {{ __('owner.reports.all_rights_reserved') }} © {{ date('Y') }}</p>
        <p>{{ __('owner.reports.thank_you') }}</p>
    </div>

</x-report-layout>
