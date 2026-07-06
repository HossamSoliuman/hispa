@php
    /**
     * Special payroll statement for a single captain / crew member (fisher):
     * every monthly payroll entry with its net due, split into paid and unpaid,
     * plus running totals. Inherits the shared report look — no per-view styles.
     */
    $docNumber = '#' . str_pad($user->id, 8, '0', STR_PAD_LEFT);

    $period = fn ($d) => sprintf('%02d / %04d', (int) ($d->payroll?->month ?? 0), (int) ($d->payroll?->year ?? 0));
    $dueOf = fn ($d) => (float) ($d->final_salary ?? 0);
    $paidOf = fn ($d) => $d->is_paid ? (float) ($d->paid_amount ?? $d->final_salary ?? 0) : 0.0;
    $unpaidOf = fn ($d) => $d->is_paid ? 0.0 : (float) ($d->final_salary ?? 0);

    $totalDue = (float) $details->sum($dueOf);
    $totalPaid = (float) $details->sum($paidOf);
    $totalUnpaid = (float) $details->sum($unpaidOf);

    $companyName = $settings['title'] ?? ($settings['company_name'] ?? ($settings['name'] ?? ''));
@endphp

<x-report-layout
    :title="$title"
    :document-number="$docNumber"
    :settings="$settings ?? []"
    :qr-code="$settings['qr_code'] ?? ''"
>
    <x-report-masthead
        :title="$title"
        :subtitle="__('owner.payrolls.statement.subtitle')"
        :settings="$settings ?? []" />

    {{-- Key facts: registration no, name, boat --}}
    <table class="info-bar">
        <tr>
            <td>
                <span class="ib-label">{{ __('owner.payrolls.statement.registration_no') }}</span>
                <span class="ib-value">{{ $docNumber }}</span>
            </td>
            <td>
                <span class="ib-label">{{ __('owner.assets.name') }}</span>
                <span class="ib-value">{{ $user->name }}</span>
            </td>
            <td>
                <span class="ib-label">{{ __('owner.sales.boat') }}</span>
                <span class="ib-value">{{ $user->boat?->name ?? '—' }}</span>
            </td>
        </tr>
    </table>

    {{-- KPI cards --}}
    <x-report-stats :items="[
        ['label' => __('owner.payrolls.statement.total_due'), 'value' => $totalDue, 'money' => true],
        ['label' => __('owner.payrolls.statement.total_paid'), 'value' => $totalPaid, 'money' => true, 'color' => '#198754'],
        ['label' => __('owner.payrolls.statement.total_unpaid'), 'value' => $totalUnpaid, 'money' => true, 'color' => '#dc3545'],
        ['label' => __('owner.payrolls.statement.months_count'), 'value' => (string) $details->count()],
    ]" />

    {{-- Statement lines --}}
    <div class="section-bar">{{ __('owner.payrolls.statement.lines_title') }}</div>
    <table class="report-table block">
        <thead>
            <tr>
                <th style="width:32px;">#</th>
                <th>{{ __('owner.payrolls.statement.period') }}</th>
                <th class="col-num">{{ __('owner.payrolls.statement.due') }}</th>
                <th class="col-num">{{ __('owner.payrolls.statement.paid') }}</th>
                <th class="col-num">{{ __('owner.payrolls.statement.unpaid') }}</th>
                <th>{{ __('owner.payrolls.statement.paid_date') }}</th>
                <th class="col-text">{{ __('owner.expenses.show.notes') }}</th>
                <th>{{ __('owner.payrolls.payment_col') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($details as $detail)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $period($detail) }}</td>
                    <td class="col-num"><x-report-money :amount="$dueOf($detail)" /></td>
                    <td class="col-num"><x-report-money :amount="$paidOf($detail)" /></td>
                    <td class="col-num"><x-report-money :amount="$unpaidOf($detail)" /></td>
                    <td>{{ $detail->is_paid && $detail->paid_at ? optional($detail->paid_at)->format('Y-m-d') : '—' }}</td>
                    <td class="col-text">{{ $detail->note ?: '—' }}</td>
                    <td>
                        @if ($detail->is_paid)
                            <span class="badge bg-success">{{ __('owner.status.paid') }}</span>
                        @else
                            <span class="badge" style="background:transparent;color:#dc3545;white-space:nowrap;">{{ __('owner.status.unpaid') }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted">{{ __('owner.payrolls.statement.no_data') }}</td></tr>
            @endforelse
        </tbody>
        @if ($details->isNotEmpty())
            <tfoot>
                <tr class="net-row">
                    <th colspan="2" class="col-text">{{ __('owner.payrolls.statement.totals') }}</th>
                    <th class="col-num"><x-report-money :amount="$totalDue" /></th>
                    <th class="col-num"><x-report-money :amount="$totalPaid" /></th>
                    <th class="col-num"><x-report-money :amount="$totalUnpaid" /></th>
                    <th colspan="3"></th>
                </tr>
            </tfoot>
        @endif
    </table>

    {{-- Remaining (unpaid) amount in words --}}
    @if ($details->isNotEmpty())
        <x-report-amount-words
            :label="__('owner.payrolls.statement.remaining_in_words')"
            :words="amount_to_words($totalUnpaid)" />
    @endif

    <x-report-signatures :items="[
        __('owner.payrolls.statement.sig_person'),
        __('owner.reports.sig_accountant'),
        __('owner.reports.sig_general_manager'),
    ]" />

    <table class="report-footer">
        <tr>
            <td>{{ $companyName }} — {{ __('owner.reports.all_rights_reserved') }} © {{ date('Y') }}</td>
        </tr>
    </table>
</x-report-layout>
