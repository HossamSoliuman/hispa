<x-report-layout :title="$title" :document-number="'ST-'.$project->id" :settings="$settings" :qr-code="$settings['qr_code'] ?? null">
    <x-report-masthead :title="$title" :subtitle="$project->name" :settings="$settings" />

    <table class="info-bar">
        <tr>
            <td><span class="ib-label">{{ __('owner.startup.name') }}</span><span class="ib-value">{{ $project->name }}</span></td>
            <td><span class="ib-label">{{ __('owner.startup.start_date') }}</span><span class="ib-value">{{ $project->start_date->format('Y-m-d') }}</span></td>
            <td><span class="ib-label">{{ __('owner.startup.status') }}</span><span class="ib-value">{{ __('owner.startup.statuses.'.$project->status) }}</span></td>
            @if(! empty($partner))
                <td><span class="ib-label">{{ __('owner.startup.columns.partner') }}</span><span class="ib-value">{{ $partner->name }}</span></td>
            @endif
        </tr>
    </table>

    @if(! empty(array_filter($filters ?? [])))
        <p class="section-title">
            {{ __('owner.startup.active_filters') }}:
            @foreach(array_filter($filters) as $key => $value)
                <span>{{ __('owner.startup.filter_names.'.$key) }}: {{ $value }}</span>@if(! $loop->last) | @endif
            @endforeach
        </p>
    @endif

    @isset($summary)
        <x-report-stats :items="[
            ['label' => __('owner.startup.total_cost'), 'value' => number_format($summary['total_cost'], 2)],
            ['label' => __('owner.startup.project_expenses'), 'value' => number_format($summary['project_expenses'], 2)],
            ['label' => __('owner.startup.contributions'), 'value' => number_format($summary['contributions'], 2)],
            ['label' => __('owner.startup.loans_total'), 'value' => number_format($summary['loans_total'], 2)],
            ['label' => __('owner.startup.loans_paid'), 'value' => number_format($summary['loans_paid'], 2)],
            ['label' => __('owner.startup.loans_remaining'), 'value' => number_format($summary['loans_remaining'], 2)],
            ['label' => __('owner.startup.partners_count'), 'value' => $summary['partners_count']],
            ['label' => __('owner.startup.status'), 'value' => __('owner.startup.statuses.'.$summary['status'])],
        ]" />
    @endisset

    @if(! empty($statementSummary))
        <x-report-stats :items="[
            ['label' => __('owner.startup.required'), 'value' => number_format($statementSummary['required'], 2)],
            ['label' => __('owner.startup.paid'), 'value' => number_format($statementSummary['paid'], 2)],
            ['label' => __('owner.startup.balance'), 'value' => number_format($statementSummary['balance'], 2)],
        ]" />
    @endif

    @isset($rows)
        @if($rows->isNotEmpty())
            <table class="report-table">
                <thead><tr>@foreach($columns as $column)<th>{{ __('owner.startup.columns.'.$column) }}</th>@endforeach</tr></thead>
                <tbody>
                    @foreach($rows as $row)
                        <tr>@foreach($columns as $column)<td>{{ is_numeric($row[$column] ?? null) ? number_format((float) $row[$column], 2) : ($row[$column] ?? '') }}</td>@endforeach</tr>
                    @endforeach
                </tbody>
                @if(! empty($totals))
                    <tfoot><tr>@foreach($columns as $column)<th>{{ is_numeric($totals[$column] ?? null) ? number_format((float) $totals[$column], 2) : ($totals[$column] ?? '') }}</th>@endforeach</tr></tfoot>
                @endif
            </table>
        @else
            <p class="section-title">{{ __('owner.startup.empty') }}</p>
        @endif
    @endisset

    @isset($partnerRows)
        <p class="section-title">{{ __('owner.startup.partners') }}</p>
        <table class="report-table">
            <thead><tr>@foreach(['partner', 'share', 'required', 'paid', 'balance'] as $column)<th>{{ __('owner.startup.columns.'.$column) }}</th>@endforeach</tr></thead>
            <tbody>
                @foreach($partnerRows as $row)
                    <tr>@foreach(['partner', 'share', 'required', 'paid', 'balance'] as $column)<td>{{ is_numeric($row[$column] ?? null) ? number_format((float) $row[$column], 2) : ($row[$column] ?? '') }}</td>@endforeach</tr>
                @endforeach
            </tbody>
            <tfoot><tr>@foreach(['partner', 'share', 'required', 'paid', 'balance'] as $column)<th>{{ is_numeric($partnerTotals[$column] ?? null) ? number_format((float) $partnerTotals[$column], 2) : ($partnerTotals[$column] ?? '') }}</th>@endforeach</tr></tfoot>
        </table>
    @endisset

    @isset($loanRows)
        <p class="section-title">{{ __('owner.startup.loans') }}</p>
        <table class="report-table">
            <thead><tr>@foreach(['loan', 'principal', 'paid', 'remaining', 'installment', 'status'] as $column)<th>{{ __('owner.startup.columns.'.$column) }}</th>@endforeach</tr></thead>
            <tbody>
                @foreach($loanRows as $row)
                    <tr>@foreach(['loan', 'principal', 'paid', 'remaining', 'installment', 'status'] as $column)<td>{{ is_numeric($row[$column] ?? null) ? number_format((float) $row[$column], 2) : ($row[$column] ?? '') }}</td>@endforeach</tr>
                @endforeach
            </tbody>
            <tfoot><tr>@foreach(['loan', 'principal', 'paid', 'remaining', 'installment', 'status'] as $column)<th>{{ is_numeric($loanTotals[$column] ?? null) ? number_format((float) $loanTotals[$column], 2) : ($loanTotals[$column] ?? '') }}</th>@endforeach</tr></tfoot>
        </table>
    @endisset

    <table class="report-footer"><tr><td>{{ $settings['company_name'] ?? config('app.name') }}</td><td>{{ now()->year }}</td></tr></table>
</x-report-layout>
