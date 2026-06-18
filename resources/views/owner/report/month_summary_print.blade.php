<x-report-layout
    :title="__('owner.month_summary.title')"
    titleEn="Monthly Financial Summary"
    :documentNumber="'MS-' . \Illuminate\Support\Carbon::parse($from)->format('Ym')"
    :settings="$settings ?? []"
>
    <x-slot name="extraStyles">
        .ms-statement { width: 100%; border-collapse: collapse; margin: 18px 0; font-size: 10pt; }
        .ms-statement th, .ms-statement td { padding: 9px 14px; }
        .ms-statement thead th { background: #34495e; color: #fff; text-align: start; font-weight: 600; font-size: 10pt; border: none; }
        .ms-period-cell { font-weight: 500 !important; }
        .ms-section td { background: #f1f5f9; color: #1e293b; font-weight: 700; font-size: 10.5pt; border-top: 1px solid #cbd5e1; border-bottom: 1px solid #cbd5e1; }
        .ms-line td { border-bottom: 1px solid #eef2f6; color: #475569; }
        .ms-label { text-align: start; }
        .ms-indent { padding-inline-start: 34px !important; }
        .ms-amount { text-align: end; font-variant-numeric: tabular-nums; white-space: nowrap; }
        .ms-pos { color: #16a34a; }
        .ms-neg { color: #dc2626; }
        .ms-muted { color: #94a3b8; }
        .ms-subtotal td { font-weight: 700; color: #1e293b; border-top: 1px solid #cbd5e1; border-bottom: 2px solid #cbd5e1; background: #fafbfc; }
        .ms-subtotal-light td { background: #fff; border-bottom: 1px solid #e2e8f0; font-weight: 600; }
        .ms-total td { font-weight: 700; font-size: 12pt; padding: 13px 14px; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .ms-total-profit td { background: #16a34a; color: #fff; }
        .ms-total-loss td { background: #dc2626; color: #fff; }
        .ms-statement-distribution { margin-top: 6px; }
        .ms-statement-distribution thead th { background: #d97706; }
        .ms-currency { font-size: 8.5pt; color: #94a3b8; }
        @media print { .ms-statement { page-break-inside: avoid; } }
    </x-slot>

    <x-report-header
        :documentNumber="'MS-' . \Illuminate\Support\Carbon::parse($from)->format('Ym')"
        :title="__('owner.month_summary.title')"
        titleEn="Monthly Financial Summary"
        :settings="$settings ?? []"
    />

    <x-report-info :settings="$settings ?? []">
        <x-slot name="additionalInfo">
            <div class="period-info" style="background:#f1f5f9;padding:12px;border-radius:6px;margin:15px 0;">
                <strong>{{ __('owner.month_summary.period_label') }}:</strong>
                {{ $from }} <small class="text-muted">(@hijri($from))</small>
                <strong style="margin-inline-start:20px;">{{ __('owner.month_summary.to') }}:</strong>
                {{ $to }} <small class="text-muted">(@hijri($to))</small>
                @if ($boatId && isset($boats))
                    @php $selectedBoat = $boats->firstWhere('id', $boatId); @endphp
                    @if ($selectedBoat)
                        <br><strong>{{ __('owner.month_summary.boat') }}:</strong> {{ $selectedBoat->name ?? $selectedBoat->name_ar }}
                    @endif
                @endif
            </div>
        </x-slot>
    </x-report-info>

    @include('owner.report._month_summary_statement', ['f' => $f, 'expenses' => $expenses, 'from' => $from, 'to' => $to])

    <div style="margin-top:18px;padding:12px 16px;background:#f8f9fa;border-radius:8px;text-align:center;">
        <small style="color:#64748b;font-weight:500;">
            {{ __('owner.month_summary.currency_note') }} · {{ __('owner.profit_loss.formula_note') }}
        </small>
    </div>
</x-report-layout>
