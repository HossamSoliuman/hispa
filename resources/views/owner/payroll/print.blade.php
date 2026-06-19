@php
    $isPercentage = $payroll->type === 'percentage';
    $typeLabel = __('owner.menu.payrolls_percentage');
    $title = $typeLabel.' - '.$payroll->month.'/'.$payroll->year;
    $totalIncrease = (float) $payroll->details->sum('increase');
    $totalDeduction = (float) $payroll->details->sum('deduction');
    $totalNet = (float) $payroll->details->sum('final_salary');
@endphp
<x-report-layout
    :title="$title"
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
        h5 { margin-top: 25px; margin-bottom: 15px; color: #2c3e50; font-weight: bold; border-bottom: 2px solid #3498db; padding-bottom: 8px; }
    </x-slot>

    <x-report-header
        :documentNumber="$payroll->id"
        :title="$title"
        :titleEn="'Payroll Report'"
        :settings="$settings ?? []"
    />

    <x-report-info :settings="$settings ?? []">
        <x-slot name="additionalInfo">
            <table style="width: 100%; border: none; margin-bottom: 10px;">
                <tr>
                    <td style="border: none; vertical-align: top; text-align: start;">
                    <p style="margin: 5px 0;"><strong>{{ __('owner.generated.the_year') }}:</strong> {{ $payroll->year }}</p>
                    <p style="margin: 5px 0;"><strong>{{ __('owner.generated.month') }}:</strong> {{ $payroll->month }}</p>
                    <p style="margin: 5px 0;"><strong>{{ __('owner.payrolls.show.salary_type') }}:</strong> {{ $typeLabel }}</p>
                    </td>
                    <td style="border: none; vertical-align: top; text-align: end;">
                    <p style="margin: 5px 0;">
                        <strong>{{ __('owner.assets.status') }}:</strong>
                        @if ($payroll->status === 'approved')
                            <span class="badge bg-success">{{ __('owner.generated.approved') }}</span>
                        @else
                            <span class="badge bg-warning">{{ __('owner.generated.draft') }}</span>
                        @endif
                    </p>
                    <p style="margin: 5px 0;">
                        <strong>{{ __('owner.sales.payment_status') }}:</strong>
                        @if ($payroll->is_paid)
                            <span class="badge bg-success">{{ __('owner.status.paid') }}</span>
                        @else
                            <span class="badge bg-danger">{{ __('owner.status.unpaid') }}</span>
                        @endif
                    </p>
                    @if ($payroll->paid_at)
                        <p style="margin: 5px 0;"><strong>{{ __('owner.generated.payment_date') }}:</strong> {{ $payroll->paid_at }}</p>
                    @endif
                    </td>
                </tr>
            </table>
        </x-slot>
    </x-report-info>

    <h5>{{ __('owner.payrolls.show.details_title') }}</h5>
    <x-report-table>
        <thead>
            <tr>
                <th style="width:40px;">#</th>
                <th style="text-align: start;">{{ __('owner.payrolls.show.employee') }}</th>
                @if ($isPercentage)
                    <th>{{ __('owner.generated.total_fishermen_profits') }}</th>
                    <th>{{ __('owner.generated.fishermen_count') }}</th>
                @else
                    <th>{{ __('owner.generated.basic_salary') }}</th>
                @endif
                <th>{{ __('owner.generated.increase') }}</th>
                <th>{{ __('owner.generated.deduction') }}</th>
                <th>{{ __('owner.expenses.show.notes') }}</th>
                <th>{{ __('owner.generated.net') }}</th>
                <th>{{ __('owner.payrolls.payment_col') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payroll->details as $detail)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td style="text-align: start;">{{ $detail->user->name ?? '-' }}</td>
                    @if ($isPercentage)
                        <td>{{ number_format($detail->captins_amount ?? 0, 2) }} <span class="currency-symbol"><x-riyal-icon size="sm" /></span></td>
                        <td>{{ $detail->captins_count ?? 0 }}</td>
                    @else
                        <td>{{ number_format($detail->base_salary ?? 0, 2) }} <span class="currency-symbol"><x-riyal-icon size="sm" /></span></td>
                    @endif
                    <td class="text-success">{{ number_format($detail->increase ?? 0, 2) }}</td>
                    <td class="text-danger">{{ number_format($detail->deduction ?? 0, 2) }}</td>
                    <td>{{ $detail->note ?: '-' }}</td>
                    <td style="font-weight: bold;">{{ number_format($detail->final_salary ?? 0, 2) }} <span class="currency-symbol"><x-riyal-icon size="sm" /></span></td>
                    <td>
                        @if ($detail->is_paid)
                            <span class="badge bg-success">{{ __('owner.status.paid') }}</span><br>
                            <small>{{ optional($detail->paid_at)->format('Y-m-d') }}</small>
                        @else
                            <span class="badge bg-danger">{{ __('owner.status.unpaid') }}</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </x-report-table>

    <x-report-summary :qrCode="$qrCode ?? ''">
        <div class="summary-row">
            <span>{{ __('owner.generated.increase') }}:</span>
            <span>{{ number_format($totalIncrease, 2) }} <span class="currency-symbol"><x-riyal-icon size="sm" /></span></span>
        </div>

        <div class="summary-row">
            <span>{{ __('owner.generated.deduction') }}:</span>
            <span>- {{ number_format($totalDeduction, 2) }} <span class="currency-symbol"><x-riyal-icon size="sm" /></span></span>
        </div>

        <div class="summary-row">
            <span>{{ __('owner.generated.net') }}:</span>
            <span>{{ number_format($totalNet, 2) }} <span class="currency-symbol"><x-riyal-icon size="sm" /></span></span>
        </div>
    </x-report-summary>

</x-report-layout>
