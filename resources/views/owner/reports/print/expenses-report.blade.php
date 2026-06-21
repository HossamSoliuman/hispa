<x-report-layout
    :title="__('owner.expenses.print.report_title')"
    title-en="Expenses Report"
    :document-number="'#' . str_pad($statistics['total_count'], 8, '0', STR_PAD_LEFT)"
    :settings="$settings"
    :qr-code="$settings['qr_code'] ?? null">

<style>
    /* ── Report standard: dense, bordered, horizontal "grid" layout ──
       Designed for mPDF (no flex/grid): tables, bordered cells, tight spacing. */
    body { font-size: 9pt; line-height: 1.5; }

    table.info-bar { width: 100%; border-collapse: collapse; margin: 0 0 10px; }
    table.info-bar td { border: 1px solid #e0e0e0; padding: 3px 6px; vertical-align: middle; text-align: center; }
    .ib-label { font-size: 8pt; color: #95a5a6; display: block; margin-bottom: 2px; }
    .ib-value { font-size: 9.5pt; font-weight: 700; color: #2c3e50; }

    table.report-table { table-layout: fixed; width: 100%; border-collapse: collapse; margin: 0; }
    table.report-table th, table.report-table td {
        border: none; border-bottom: 1px solid #e5e5e5; padding: 4px 5px; font-size: 8.5pt;
        word-wrap: break-word; overflow-wrap: break-word; vertical-align: middle; text-align: center;
    }
    table.report-table thead th { background: #1a1a1a; color: #fff; font-weight: 700; border-bottom: none; }
    table.report-table tfoot td, table.report-table tfoot th {
        background: transparent; font-weight: 700; color: #1a1a1a;
        border-top: 2px solid #1a1a1a; border-bottom: none;
    }
    .col-text { text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}; }
    .col-num  { text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}; white-space: nowrap; }

    .report-stats { border-spacing: 6px 0; margin: 0 0 10px; }
    .report-stats td.report-stat-card { border: 1px solid #e0e0e0; border-radius: 4px; padding: 8px 6px; }
    .report-stat-value { font-size: 13pt; }
    .report-stat-label { margin-bottom: 4px; font-size: 8.5pt; }

    .block { margin: 0 0 10px; }

    table.report-footer { width: 100%; border-collapse: collapse; margin: 6px 0 0; border-top: 1px solid #e0e0e0; }
    table.report-footer td { border: none; padding: 8px 4px 0; vertical-align: middle; }
    td.rf-text { text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}; font-size: 8pt; color: #95a5a6; }
</style>

    <x-report-header
        :document-number="'#' . str_pad($statistics['total_count'], 8, '0', STR_PAD_LEFT)"
        :title="__('owner.expenses.print.report_title')"
        title-en="Expenses Report"
        :settings="$settings" />

    {{-- Applied filters / key facts strip --}}
    <table class="info-bar">
        <tr>
            @if($filters['category'])
                <td><span class="ib-label">{{ __('owner.expenses.filters.category') }}</span><span class="ib-value">{{ $filters['category'] }}</span></td>
            @endif
            @if($filters['boat'])
                <td><span class="ib-label">{{ __('owner.expenses.filters.boat') }}</span><span class="ib-value">{{ $filters['boat'] }}</span></td>
            @endif
            @if($filters['status'])
                <td><span class="ib-label">{{ __('owner.expenses.filters.status') }}</span><span class="ib-value">{{ $filters['status'] === 'paid' ? __('owner.paid') : __('owner.pending') }}</span></td>
            @endif
            @if($filters['from_date'])
                <td><span class="ib-label">{{ __('owner.reports.from_date') }}</span><span class="ib-value">{{ $filters['from_date'] }}</span></td>
            @endif
            @if($filters['to_date'])
                <td><span class="ib-label">{{ __('owner.reports.to_date') }}</span><span class="ib-value">{{ $filters['to_date'] }}</span></td>
            @endif
            <td><span class="ib-label">{{ __('owner.expenses.cards.total_count') }}</span><span class="ib-value">{{ $statistics['total_count'] }}</span></td>
        </tr>
    </table>

    <x-report-stats :items="[
        ['label' => __('owner.expenses.cards.total_count'), 'value' => $statistics['total_count']],
        ['label' => __('owner.expenses.cards.total_amount'), 'value' => number_format($statistics['total_amount'], 2)],
        ['label' => __('owner.expenses.cards.paid_amount'), 'value' => number_format($statistics['paid_amount'], 2)],
        ['label' => __('owner.expenses.cards.pending_amount'), 'value' => number_format($statistics['pending_amount'], 2)],
    ]" />

    @if($expenses->isEmpty())
        <div class="alert alert-warning">
            <strong>{{ __('owner.reports.no_data_found') }}</strong>
            <p class="mb-0 text-muted">{{ __('owner.reports.try_adjust_filters') }}</p>
        </div>
    @else
        <table class="report-table block">
            <thead>
                <tr>
                    <th style="width:4%;">#</th>
                    <th class="col-text" style="width:14%;">{{ __('owner.expenses.table.expense_number') }}</th>
                    <th style="width:11%;">{{ __('owner.expenses.table.date') }}</th>
                    <th class="col-text" style="width:17%;">{{ __('owner.expenses.table.category') }}</th>
                    <th class="col-text" style="width:12%;">{{ __('owner.expenses.table.boat') }}</th>
                    <th class="col-text" style="width:12%;">{{ __('owner.expenses.table.vendor') }}</th>
                    <th class="col-num" style="width:11%;">{{ __('owner.expenses.table.total_amount') }}</th>
                    <th class="col-num" style="width:11%;">{{ __('owner.expenses.table.final_amount') }}</th>
                    <th style="width:8%;">{{ __('owner.expenses.table.status') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expenses as $i => $expense)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td class="col-text">{{ $expense->number }}</td>
                        <td>{{ $expense->date }}</td>
                        <td class="col-text">{{ optional($expense->category->parent)->name ? optional($expense->category->parent)->name . ' / ' : '' }}{{ $expense->category->name ?? '---' }}</td>
                        <td class="col-text">{{ $expense->boat->name ?? __('owner.general') }}</td>
                        <td class="col-text">{{ $expense->vendor->name ?? '---' }}</td>
                        <td class="col-num">{{ number_format($expense->total_price, 2) }} <x-riyal-icon /></td>
                        <td class="col-num">{{ number_format($expense->final_price, 2) }} <x-riyal-icon /></td>
                        <td>{{ $expense->status === 'paid' ? __('owner.paid') : __('owner.pending') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6">{{ __('owner.expenses.print.total') }}</td>
                    <td class="col-num"></td>
                    <td class="col-num">{{ number_format($statistics['total_amount'], 2) }} <x-riyal-icon /></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    @endif

    <table class="report-footer">
        <tr>
            <td class="rf-text">
                {{ $settings['company_name'] ?? $settings['title'] ?? '' }} — {{ __('owner.reports.all_rights_reserved') }} © {{ date('Y') }}
            </td>
        </tr>
    </table>

</x-report-layout>
