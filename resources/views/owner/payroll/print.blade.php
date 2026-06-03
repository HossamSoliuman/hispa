<x-report-layout
    :title="__('owner.payrolls.show.header', ['boat' => $payroll->boat->name ?? ''])"
    :titleEn="'Payroll Report'"
    :documentNumber="$payroll->id"
    :settings="$settings ?? []"
>
    <x-slot name="extraStyles">
        .table thead th { text-align: center; font-weight: bold; }
        .table tbody td { text-align: center; }
        .unit svg { width: 14px; height: 14px; display: inline-block; vertical-align: middle; }
        .metadata { margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px; }
        .meta-item { display: inline-block; width: 48%; margin-bottom: 10px; }
        .meta-label { font-weight: bold; color: #495057; }
        .meta-value { color: #212529; }
        .badge { padding: 5px 12px; border-radius: 15px; font-size: 12px; }
        .bg-success { background-color: #28a745; color: white; }
        .bg-warning { background-color: #ffc107; color: #212529; }
        h5 { margin-top: 25px; margin-bottom: 15px; color: #2c3e50; font-weight: bold; border-bottom: 2px solid #3498db; padding-bottom: 8px; }
    </x-slot>

    <x-report-header
        :documentNumber="$payroll->id"
        :title="__('owner.payrolls.show.header', ['boat' => $payroll->boat->name ?? ''])"
        :titleEn="'Payroll Report'"
        :settings="$settings ?? []"
    />

    <x-report-info :settings="$settings ?? []">
        <x-slot name="additionalInfo">
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <div style="flex: 1;">
                    <p style="margin: 5px 0;"><strong>{{ __('owner.payrolls.show.period') }}:</strong> {{ $payroll->period_from }} - {{ $payroll->period_to }} <small class="text-muted">(@hijri($payroll->period_from) - @hijri($payroll->period_to))</small></p>
                    <p style="margin: 5px 0;"><strong>{{ __('owner.payrolls.show.owner_percentage') }}:</strong> {{ $payroll->owner_percentage }}%</p>
                </div>
                <div style="flex: 1; text-align: left;">
                    <p style="margin: 5px 0;"><strong>{{ __('owner.payrolls.show.total_revenues') }}:</strong> {{ number_format($payroll->total_revenues,2) }} <span class="currency-symbol"><x-riyal-icon size="sm" /></span></p>
                    <p style="margin: 5px 0;"><strong>{{ __('owner.payrolls.show.total_expenses') }}:</strong> {{ number_format($payroll->total_expenses,2) }} <span class="currency-symbol"><x-riyal-icon size="sm" /></span></p>
                </div>
            </div>
        </x-slot>
    </x-report-info>

    @if($payroll->notes)
    <div class="metadata" style="margin-top: 15px;">
        <div style="width: 100%;">
            <span class="meta-label">{{ __('owner.payrolls.show.remarks') }}:</span>
            <p class="meta-value" style="margin-top: 5px;">{{ $payroll->notes }}</p>
        </div>
    </div>
    @endif

    <h5>{{ __('owner.payrolls.show.details_title') }}</h5>
    <x-report-table>
        <thead>
            <tr>
                <th style="width:40px;">#</th>
                <th style="text-align: start;">{{ __('owner.payrolls.show.employee') }}</th>
                <th style="width:120px;">{{ __('owner.payrolls.show.salary_type') }}</th>
                <th style="width:120px;">{{ __('owner.payrolls.show.fixed_salary') }}</th>
                <th style="width:100px;">{{ __('owner.payrolls.show.percentage') }}</th>
                <th style="width:140px;">{{ __('owner.payrolls.show.calculated_salary') }}</th>
                <th style="width:80px;">{{ __('owner.payrolls.show.captain') }}</th>
                <th style="width:80px;">{{ __('owner.payrolls.show.crew') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payroll->details as $detail)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td style="text-align: start;">{{ $detail->user->name ?? '-' }}</td>
                    <td>{{ $detail->salary_type }}</td>
                    <td>{{ number_format($detail->fixed_amount ?? 0, 2) }} <span class="currency-symbol"><x-riyal-icon size="sm" /></span></td>
                    <td>{{ $detail->percentage ?? 0 }}%</td>
                    <td style="font-weight: bold;">{{ number_format($detail->calculated_salary ?? 0, 2) }} <span class="currency-symbol"><x-riyal-icon size="sm" /></span></td>
                    <td>{!! $detail->is_captain ? '<span class="badge bg-success">'.__('owner.payrolls.show.captain').'</span>' : '-' !!}</td>
                    <td>{!! $detail->is_crew ? '<span class="badge bg-warning">'.__('owner.payrolls.show.crew').'</span>' : '-' !!}</td>
                </tr>
            @endforeach
        </tbody>
    </x-report-table>

    <x-report-summary :qrCode="$qrCode ?? ''">
        <div class="summary-row">
            <span>{{ __('owner.payrolls.show.total_revenues') }}:</span>
            <span>{{ number_format($payroll->total_revenues,2) }} <span class="currency-symbol"><x-riyal-icon size="sm" /></span></span>
        </div>

        <div class="summary-row">
            <span>{{ __('owner.payrolls.show.total_expenses') }}:</span>
            <span>- {{ number_format($payroll->total_expenses,2) }} <span class="currency-symbol"><x-riyal-icon size="sm" /></span></span>
        </div>

        <div class="summary-row">
            <span>{{ __('owner.payrolls.show.owner_profit') }}:</span>
            <span>{{ number_format($payroll->owner_profit,2) }} <span class="currency-symbol"><x-riyal-icon size="sm" /></span></span>
        </div>

        <div class="summary-row">
            <span>{{ __('owner.payrolls.show.crew_total') }}:</span>
            <span>{{ number_format($payroll->crew_total,2) }} <span class="currency-symbol"><x-riyal-icon size="sm" /></span></span>
        </div>

        <div class="summary-row">
            <span>{{ __('owner.payrolls.table.carry_over') }}:</span>
            <span>{{ number_format($payroll->carry_over ?? 0,2) }} <span class="currency-symbol"><x-riyal-icon size="sm" /></span></span>
        </div>

        <div class="summary-row">
            <span>{{ __('owner.payrolls.table.surplus') }}:</span>
            <span>{{ number_format($payroll->surplus ?? 0,2) }} <span class="currency-symbol"><x-riyal-icon size="sm" /></span></span>
        </div>

        <div class="summary-row">
            <span>{{ __('owner.payrolls.table.deficit') }}:</span>
            <span>{{ number_format($payroll->deficit ?? 0,2) }} <span class="currency-symbol"><x-riyal-icon size="sm" /></span></span>
        </div>

    </x-report-summary>

</x-report-layout>
