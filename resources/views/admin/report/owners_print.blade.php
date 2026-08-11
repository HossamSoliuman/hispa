<x-report-layout
    :title="__('admin.report.owners.print_title')"
    :title-en="trans('admin.report.owners.print_title', [], 'en')"
    :document-number="$documentNumber"
    :settings="$settings"
    :qr-code="$settings['qr_code'] ?? null">

    <x-report-header
        :document-number="$documentNumber"
        :settings="$settings"
        :title="__('admin.report.owners.print_title')"
        :title-en="trans('admin.report.owners.print_title', [], 'en')" />

    <table class="info-bar">
        <tr>
            <td>
                <span class="ib-label">{{ __('admin.report.owners.filters.document_number') }}</span>
                <span class="ib-value">{{ $documentNumber }}</span>
            </td>
            <td>
                <span class="ib-label">{{ __('admin.report.owners.filters.status') }}</span>
                <span class="ib-value">
                    {{ $appliedFilters['status'] ? __('admin.report.owners.status_labels.'.$appliedFilters['status']) : __('admin.report.owners.filters.all_statuses') }}
                </span>
            </td>
            @if($appliedFilters['from_date'])
                <td>
                    <span class="ib-label">{{ __('admin.report.owners.filters.from_date') }}</span>
                    <span class="ib-value">{{ $appliedFilters['from_date'] }}</span>
                </td>
            @endif
            @if($appliedFilters['to_date'])
                <td>
                    <span class="ib-label">{{ __('admin.report.owners.filters.to_date') }}</span>
                    <span class="ib-value">{{ $appliedFilters['to_date'] }}</span>
                </td>
            @endif
            <td>
                <span class="ib-label">{{ __('admin.report.owners.filters.generated_at') }}</span>
                <span class="ib-value">{{ $appliedFilters['generated_at']->format('Y-m-d H:i') }}</span>
            </td>
        </tr>
    </table>

    <x-report-stats :items="[
        ['label' => __('admin.report.owners.kpi.total_owners'), 'value' => $totalOwners],
        ['label' => __('admin.report.owners.kpi.active_owners'), 'value' => $activeOwners, 'color' => '#16a085'],
        ['label' => __('admin.report.owners.kpi.total_boats'), 'value' => $totalBoats],
        ['label' => __('admin.report.owners.kpi.total_quota'), 'value' => $totalQuota],
    ]" />

    @if($rows->isEmpty())
        <div class="alert alert-warning">
            <strong>{{ __('admin.report.owners.no_data') }}</strong>
            <p class="mb-0 text-muted">{{ __('admin.report.owners.try_adjust_filters') }}</p>
        </div>
    @else
        <table class="report-table block">
            <thead>
                <tr>
                    <th class="col-num" style="width:5%;">#</th>
                    <th class="col-text" style="width:18%;">{{ __('admin.report.owners.owner') }}</th>
                    <th class="col-text" style="width:13%;">{{ __('admin.report.owners.phone') }}</th>
                    <th class="col-text" style="width:14%;">{{ __('admin.report.owners.plan') }}</th>
                    <th class="col-text" style="width:10%;">{{ __('admin.report.owners.status') }}</th>
                    <th class="col-num" style="width:8%;">{{ __('admin.report.owners.boats_used') }}</th>
                    <th class="col-num" style="width:8%;">{{ __('admin.report.owners.quota') }}</th>
                    <th class="col-text" style="width:12%;">{{ __('admin.report.owners.start_date') }}</th>
                    <th class="col-text" style="width:12%;">{{ __('admin.report.owners.end_date') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $index => $row)
                    @php
                        $statusClass = match ($row->status) {
                            'active' => 'text-success',
                            'expired', 'suspended' => 'text-danger',
                            default => 'text-warning',
                        };
                    @endphp
                    <tr>
                        <td class="col-num">{{ $index + 1 }}</td>
                        <td class="col-text">{{ $row->name ?? '---' }}</td>
                        <td class="col-text">{{ $row->phone ?? '---' }}</td>
                        <td class="col-text">{{ $row->plan_name ?? '---' }}</td>
                        <td class="col-text">
                            <span class="{{ $statusClass }}">
                                {{ $row->status ? __('admin.report.owners.status_labels.'.$row->status) : __('admin.report.owners.status_labels.none') }}
                            </span>
                        </td>
                        <td class="col-num">{{ $row->boats_used }}</td>
                        <td class="col-num">{{ $row->quota }}</td>
                        <td class="col-text">{{ $row->start_date ? \Illuminate\Support\Carbon::parse($row->start_date)->format('Y-m-d') : '---' }}</td>
                        <td class="col-text">{{ $row->end_date ? \Illuminate\Support\Carbon::parse($row->end_date)->format('Y-m-d') : '---' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td class="col-num">{{ $totalOwners }}</td>
                    <td class="col-text" colspan="4">{{ __('admin.report.owners.kpi.total_owners') }}</td>
                    <td class="col-num">{{ $totalBoats }}</td>
                    <td class="col-num">{{ $totalQuota }}</td>
                    <td class="col-text" colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    @endif

    <table class="report-footer">
        <tr>
            <td class="rf-text">
                {{ $settings['company_name'] ?? $settings['title'] ?? config('app.name') }} &mdash;
                {{ __('admin.report.owners.all_rights_reserved') }} {{ date('Y') }}
            </td>
        </tr>
    </table>
</x-report-layout>
