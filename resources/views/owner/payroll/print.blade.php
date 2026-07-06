@php
    $isPercentage = $payroll->type === 'percentage';
    $typeLabel = __('owner.menu.payrolls_percentage');
    $title = $typeLabel.' - '.$payroll->month.'/'.$payroll->year;
    $subtitle = __('owner.generated.month').' '.$payroll->month.' / '.$payroll->year;

    $totalIncrease = (float) $payroll->details->sum('increase');
    $totalAdvances = (float) $payroll->details->sum('advances');
    $totalNet = (float) $payroll->details->sum('final_salary');

    $colspan = $isPercentage ? 9 : 8;
    $companyName = $settings['title'] ?? ($settings['company_name'] ?? ($settings['name'] ?? ''));
@endphp

<x-report-layout
    :title="$title"
    :document-number="(string) $payroll->id"
    :settings="$settings ?? []"
    :qr-code="$settings['qr_code'] ?? ''"
>
    <x-report-masthead :title="$title" :subtitle="$subtitle" :settings="$settings ?? []" />

    {{-- Meta strip: year / month / status --}}
    <div class="meta-row">
        <span class="meta-item">
            <span class="lbl">{{ __('owner.generated.the_year') }}:</span>
            <span class="val-box">{{ $payroll->year }}</span>
        </span>
        <span class="meta-item">
            <span class="lbl">{{ __('owner.generated.month') }}:</span>
            <span class="val-box">{{ $payroll->month }}</span>
        </span>
        <span class="meta-item">
            <span class="lbl">{{ __('owner.assets.status') }}:</span>
            <span class="val-box">{{ $payroll->status === 'approved' ? __('owner.generated.approved') : __('owner.generated.draft') }}</span>
        </span>
    </div>

    {{-- KPI cards --}}
    <x-report-stats :items="[
        ['label' => __('owner.generated.increase'), 'value' => $totalIncrease, 'money' => true],
        ['label' => __('owner.payrolls.advances_col'), 'value' => $totalAdvances, 'money' => true],
        ['label' => __('owner.generated.net'), 'value' => $totalNet, 'money' => true],
    ]" />

    {{-- Payroll detail lines --}}
    <div class="section-bar">{{ __('owner.payrolls.show.details_title') }}</div>
    <table class="report-table block">
        <thead>
            <tr>
                <th style="width:32px;">#</th>
                <th class="col-text">{{ __('owner.payrolls.show.employee') }}</th>
                @if ($isPercentage)
                    <th class="col-num">{{ __('owner.payrolls.fishermen_profits_col') }}</th>
                    <th>{{ __('owner.payrolls.special_percent_col') }}</th>
                @else
                    <th class="col-num">{{ __('owner.generated.basic_salary') }}</th>
                @endif
                <th class="col-num">{{ __('owner.generated.increase') }}</th>
                <th class="col-num">{{ __('owner.payrolls.advances_col') }}</th>
                <th class="col-text">{{ __('owner.expenses.show.notes') }}</th>
                <th class="col-num">{{ __('owner.generated.net') }}</th>
                <th>{{ __('owner.payrolls.payment_col') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($payroll->details as $detail)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="col-text">{{ $detail->user->name ?? '-' }}</td>
                    @if ($isPercentage)
                        <td class="col-num"><x-report-money :amount="$detail->captins_amount ?? 0" /></td>
                        <td>{{ $detail->custom_share_percent > 0 ? number_format((float) $detail->custom_share_percent, 2).'%' : '-' }}</td>
                    @else
                        <td class="col-num"><x-report-money :amount="$detail->base_salary ?? 0" /></td>
                    @endif
                    <td class="col-num"><x-report-money :amount="$detail->increase ?? 0" /></td>
                    <td class="col-num"><x-report-money :amount="$detail->advances ?? 0" /></td>
                    <td class="col-text">{{ $detail->note ?: '-' }}</td>
                    <td class="col-num" style="font-weight:bold;"><x-report-money :amount="$detail->final_salary ?? 0" /></td>
                    <td>
                        @if ($detail->is_paid)
                            <span class="badge bg-success">{{ __('owner.status.paid') }}</span>
                            @if ($detail->paid_at)<br><small>{{ optional($detail->paid_at)->format('Y-m-d') }}</small>@endif
                        @else
                            <span class="badge" style="background:transparent;color:#dc3545;white-space:nowrap;">{{ __('owner.status.unpaid') }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="{{ $colspan }}" class="text-center text-muted">{{ __('owner.no_data') }}</td></tr>
            @endforelse
        </tbody>
        @if ($payroll->details->isNotEmpty())
            <tfoot>
                <tr>
                    <th colspan="{{ $isPercentage ? 4 : 3 }}" class="col-text">{{ __('owner.payrolls.show.details_title') }}</th>
                    <th class="col-num"><x-report-money :amount="$totalIncrease" /></th>
                    <th class="col-num"><x-report-money :amount="$totalAdvances" /></th>
                    <th></th>
                    <th class="col-num"><x-report-money :amount="$totalNet" /></th>
                    <th></th>
                </tr>
            </tfoot>
        @endif
    </table>

    <x-report-signatures :items="[
        __('owner.reports.sig_accountant'),
        __('owner.reports.sig_financial_manager'),
        __('owner.reports.sig_general_manager'),
    ]" />

    <table class="report-footer">
        <tr>
            <td>{{ $companyName }} — {{ __('owner.reports.all_rights_reserved') }} © {{ $payroll->year }}</td>
        </tr>
    </table>
</x-report-layout>
