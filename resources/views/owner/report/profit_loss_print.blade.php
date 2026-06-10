<x-report-layout
    :title="__('owner.profit_loss.title')"
    :titleEn="'Profit & Loss Report'"
    :documentNumber="'PL-' . now()->format('YmdHis')"
    :settings="$settings ?? []"
>
    <x-slot name="extraStyles">
        .summary-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin: 20px 0; }
        .summary-card { background: #f8f9fa; padding: 15px; border-radius: 8px; border-left: 4px solid; }
        .summary-card.revenue { border-color: #16a34a; }
        .summary-card.expense { border-color: #dc2626; }
        .summary-card.payroll { border-color: #d97706; }
        .summary-card.profit { border-color: #16a34a; }
        .summary-card.loss { border-color: #dc2626; }
        .summary-card .label { font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 8px; }
        .summary-card .amount { font-size: 20px; font-weight: 700; }
        .currency-symbol { display: inline-flex; align-items: center; gap: 3px; }
        .currency-symbol svg { width: 14px; height: 14px; fill: currentColor; }
        .period-info { background: #f1f5f9; padding: 12px; border-radius: 6px; margin: 15px 0; }
        .period-info strong { color: #1e293b; }
    </x-slot>

    <x-report-header
        :documentNumber="'PL-' . now()->format('YmdHis')"
        :title="__('owner.profit_loss.title')"
        :titleEn="'Profit & Loss Report'"
        :settings="$settings ?? []"
    />

    <x-report-info :settings="$settings ?? []">
        <x-slot name="additionalInfo">
            <div class="period-info">
                <strong>{{ __('owner.profit_loss.from_date') }}:</strong> {{ $from }} <small class="text-muted">(@hijri($from))</small>
                <strong style="margin-inline-start: 20px;">{{ __('owner.profit_loss.to_date') }}:</strong> {{ $to }} <small class="text-muted">(@hijri($to))</small>
                @if($boatId && isset($boats))
                    @php $selectedBoat = $boats->firstWhere('id', $boatId); @endphp
                    @if($selectedBoat)
                        <br><strong>{{ __('owner.profit_loss.boat') }}:</strong> {{ $selectedBoat->name ?? $selectedBoat->name_ar }}
                    @endif
                @endif
            </div>
        </x-slot>
    </x-report-info>

    {{-- Summary Cards --}}
    @php $netProfit = (float) $f['net_profit']; @endphp
    <div class="summary-grid">
        <div class="summary-card revenue">
            <div class="label">{{ __('owner.profit_loss.net_sales') }}</div>
            <div class="amount" style="color: #16a34a;">
                {{ number_format($f['net_sales'], 2) }}
                <span class="currency-symbol"><x-riyal-icon size="sm" /></span>
            </div>
        </div>

        <div class="summary-card expense">
            <div class="label">{{ __('owner.profit_loss.total_expenses') }}</div>
            <div class="amount" style="color: #dc2626;">
                {{ number_format($f['total_expenses'], 2) }}
                <span class="currency-symbol"><x-riyal-icon size="sm" /></span>
            </div>
        </div>

        <div class="summary-card payroll">
            <div class="label">{{ __('owner.profit_loss.crew_share') }}</div>
            <div class="amount" style="color: #d97706;">
                {{ number_format($f['crew_share'], 2) }}
                <span class="currency-symbol"><x-riyal-icon size="sm" /></span>
            </div>
        </div>

        <div class="summary-card {{ $netProfit >= 0 ? 'profit' : 'loss' }}">
            <div class="label">{{ __('owner.profit_loss.net_profit_loss') }}</div>
            <div class="amount" style="color: {{ $netProfit >= 0 ? '#16a34a' : '#dc2626' }};">
                {{ number_format($netProfit, 2) }}
                <span class="currency-symbol"><x-riyal-icon size="sm" /></span>
            </div>
        </div>
    </div>

    {{-- Summary Table --}}
    <x-report-summary :qr-code="$settings['qr_code'] ?? null">
        <x-report-summary-row
            :label="__('owner.profit_loss.total_sales')"
            :value="$f['gross_sales']"
            valueClass="text-success"
            :showCurrency="true"
        />
        <x-report-summary-row
            :label="__('owner.profit_loss.return')"
            :value="$f['returns'] * -1"
            valueClass="text-danger"
            :showMinus="true"
            :showCurrency="true"
        />
        <x-report-summary-row
            :label="__('owner.profit_loss.net_owner_revenue')"
            :value="$f['net_owner_revenue']"
            valueClass="text-success"
            :showCurrency="true"
        />
        <x-report-summary-row
            :label="__('owner.profit_loss.trip_expenses')"
            :value="$f['trip_expenses'] * -1"
            valueClass="text-danger"
            :showMinus="true"
            :showCurrency="true"
        />
        <x-report-summary-row
            :label="__('owner.profit_loss.general_expenses')"
            :value="$f['general_expenses'] * -1"
            valueClass="text-danger"
            :showMinus="true"
            :showCurrency="true"
        />
        <x-report-summary-row
            :label="__('owner.profit_loss.fixed_salaries')"
            :value="$f['fixed_salaries'] * -1"
            valueClass="text-warning"
            :showMinus="true"
            :showCurrency="true"
        />
        <x-report-summary-row
            :label="__('owner.profit_loss.depreciation')"
            :value="$f['depreciation'] * -1"
            valueClass="text-danger"
            :showMinus="true"
            :showCurrency="true"
        />
        <x-report-summary-row
            :label="__('owner.profit_loss.net_profit_loss')"
            :value="$netProfit"
            :valueClass="$netProfit >= 0 ? 'text-success' : 'text-danger'"
            :showCurrency="true"
            isBold
        />
        <x-report-summary-row
            :label="__('owner.generated.owner_ratio')"
            :value="$f['owner_share']"
            valueClass="text-success"
            :showCurrency="true"
        />
        <x-report-summary-row
            :label="__('owner.profit_loss.crew_share')"
            :value="$f['crew_share']"
            valueClass="text-warning"
            :showCurrency="true"
        />
    </x-report-summary>

    <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px; text-align: center;">
        <small style="color: #64748b; font-weight: 500;">{{ __('owner.profit_loss.formula_note') }}</small>
    </div>

</x-report-layout>
