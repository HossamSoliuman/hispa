<x-report-layout
    :title="__('owner.dalal.report')"
    :titleEn="'Broker Report'"
    :documentNumber="'DLR-' . now()->format('Ymd-His')"
    :settings="$settings ?? []"
>
    <x-slot name="extraStyles">
        .table thead th { text-align: center; }
        .table tbody td { text-align: center; }
    </x-slot>

    <x-report-header :documentNumber="'DLR-' . now()->format('Ymd-His')" :title="__('owner.dalal.report')" :titleEn="'Broker Report'" :settings="$settings ?? []" />

    <x-report-info :settings="$settings ?? []">
        <x-slot name="additionalInfo">
            <p><strong>{{ __('owner.reports.owner_id') }}:</strong> #{{ auth()->id() }}</p>
            <p><strong>{{ __('owner.reports.owner_name') }}:</strong> {{ $settings['company_name'] ?? auth()->user()->name ?? '' }}</p>
            <p><strong>{{ __('owner.reports.report_date') }}:</strong> @hijri(now())</p>
        </x-slot>
    </x-report-info>

    <div class="metadata">
        <div class="meta-item">
            <span class="meta-label">{{ __('owner.dalal.table.name') }}:</span>
            <span class="meta-value">{{ $dalal->name ?? '-' }}</span>
        </div>
        <div class="meta-item">
            <span class="meta-label">{{ __('owner.dalal.table.contact') }}:</span>
            <span class="meta-value">{{ $dalal->phone ?? '-' }}</span>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('owner.dalal.table.date') }}</th>
                <th>{{ __('owner.dalal.table.sales_count') }}</th>
                <th>{{ __('owner.dalal.table.total_dalal_commission') }}</th>
                <th>{{ __('owner.dalal.table.payment_status') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales as $sale)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ optional($sale->created_at)->format('Y-m-d') }}</td>
                <td>1</td>
                <td>{{ number_format($sale->remaining_total ?? 0, 2) }} <span class="currency-symbol"><x-riyal-icon size="sm" /></span></td>
                <td>{{ $sale->dalal_payment_status == 'paid' ? __('owner.status.paid') : __('owner.status.unpaid') }}</td>
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
            <span>{{ __('owner.dalal.cards.amount_due') }}</span>
            <span>{{ number_format($totalDue ?? 0, 2) }} <span class="currency-symbol"><x-riyal-icon size="sm" /></span></span>
        </div>
        <div class="summary-row">
            <span>{{ __('owner.dalal.cards.paid') }}</span>
            <span>{{ number_format($totalPaid ?? 0, 2) }} <span class="currency-symbol"><x-riyal-icon size="sm" /></span></span>
        </div>
        <div class="summary-row">
            <span>{{ __('owner.dalal.table.total_dalal_commission') }}</span>
            <span>{{ number_format($totalCommission ?? 0, 2) }} <span class="currency-symbol"><x-riyal-icon size="sm" /></span></span>
        </div>
    </x-report-summary>

    <div class="footer">
        <p>{{ __('owner.reports.all_rights_reserved') }} © {{ now()->year }}</p>
    </div>

</x-report-layout>
