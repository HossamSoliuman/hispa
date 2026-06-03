<x-report-layout
    :title="__('owner.vendors.report')"
    :titleEn="'Vendor Report'"
    :documentNumber="'VDR-' . now()->format('Ymd-His')"
    :settings="$settings ?? []"
>
    <x-slot name="extraStyles">
        .table thead th { text-align: center; }
        .table tbody td { text-align: center; }
    </x-slot>

    <x-report-header :documentNumber="'VDR-' . now()->format('Ymd-His')" :title="__('owner.vendors.report')" :titleEn="'Vendor Report'" :settings="$settings ?? []" />

    <x-report-info :settings="$settings ?? []">
        <x-slot name="additionalInfo">
            <p><strong>{{ __('owner.reports.owner_id') }}:</strong> #{{ auth()->id() }}</p>
            <p><strong>{{ __('owner.reports.owner_name') }}:</strong> {{ $settings['company_name'] ?? auth()->user()->name ?? '' }}</p>
            <p><strong>{{ __('owner.reports.report_date') }}:</strong> @hijri(now())</p>
        </x-slot>
    </x-report-info>

    <div class="metadata">
        <div class="meta-item">
            <span class="meta-label">{{ __('owner.reports.owner_name') }}:</span>
            <span class="meta-value">{{ $vendor->name ?? '-' }}</span>
        </div>
        <div class="meta-item">
            <span class="meta-label">{{ __('owner.reports.applied_filters') }}:</span>
            <span class="meta-value">-</span>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('owner.vendors.table.description') ?? 'Description' }}</th>
                <th>{{ __('owner.vendors.table.amount') ?? 'Amount' }}</th>
                <th>{{ __('owner.vendors.table.status') ?? 'Status' }}</th>
                <th>{{ __('owner.vendors.table.date') ?? 'Date' }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenses as $exp)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $exp->description ?? '-' }}</td>
                <td>{{ number_format($exp->final_price ?? 0, 2) }} <span class="currency-symbol"><x-riyal-icon size="sm" /></span></td>
                <td>{{ ucfirst($exp->status ?? '-') }}</td>
                <td>{{ optional($exp->created_at)->format('Y-m-d') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">{{ __('owner.reports.no_data_found') }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <x-report-summary :qrCode="$qrCode ?? ''">
        <div class="summary-row">
            <span>{{ __('owner.vendors.cards.pending_amount') }}</span>
            <span>{{ number_format($totalDue ?? 0, 2) }} <span class="currency-symbol"><x-riyal-icon size="sm" /></span></span>
        </div>
        <div class="summary-row">
            <span>{{ __('owner.vendors.cards.total_expenses') }}</span>
            <span>{{ number_format($totalExpenses ?? 0, 2) }} <span class="currency-symbol"><x-riyal-icon size="sm" /></span></span>
        </div>
    </x-report-summary>

    <div class="footer">
        <p>{{ __('owner.reports.all_rights_reserved') }} © {{ now()->year }}</p>
    </div>

</x-report-layout>
