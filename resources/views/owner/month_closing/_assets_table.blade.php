{{-- Assets & depreciation charged for the month (straight-line). --}}
@if (! empty($assets))
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('owner.month_closing.assets.title') }}</h5>
            <small class="text-muted">{{ __('owner.month_closing.assets.subtitle') }}</small>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('owner.month_closing.assets.asset') }}</th>
                        <th class="text-end">{{ __('owner.month_closing.assets.cost') }}</th>
                        <th class="text-end">{{ __('owner.month_closing.assets.annual') }}</th>
                        <th class="text-end">{{ __('owner.month_closing.assets.monthly') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($assets as $row)
                        <tr>
                            <td>{{ $row['name'] }}</td>
                            <td class="text-end">{{ number_format((float) $row['purchase_cost'], 2) }}</td>
                            <td class="text-end">{{ number_format((float) $row['annual'], 2) }}</td>
                            <td class="text-end">{{ number_format((float) $row['monthly'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-light fw-bold">
                        <td colspan="3">{{ __('owner.month_closing.assets.total') }}</td>
                        <td class="text-end">{{ number_format((float) $total, 2) }} <x-riyal-icon size="sm" /></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endif
