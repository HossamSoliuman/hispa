{{-- Assets & depreciation charged for the month (straight-line), plus deficit carry-forward. --}}
@php
    $assets = $assets ?? [];
    $ownTotal = collect($assets)->sum(fn ($row) => (float) $row['monthly']);
    $broughtForward = (float) ($broughtForward ?? 0);
    $deferred = (float) ($deferred ?? 0);
    $consideredTotal = round($ownTotal + $broughtForward, 2);
    $charged = isset($charged) ? (float) $charged : $consideredTotal;
    $hasDeferral = $broughtForward > 0 || $deferred > 0;
@endphp

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
                            <td class="text-end">{{ number_format((float) $row['purchase_cost'], 2) }} <x-riyal-icon size="sm" /></td>
                            <td class="text-end">{{ number_format((float) $row['annual'], 2) }} <x-riyal-icon size="sm" /></td>
                            <td class="text-end">{{ number_format((float) $row['monthly'], 2) }} <x-riyal-icon size="sm" /></td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-light fw-bold">
                        <td colspan="3">{{ __('owner.month_closing.assets.total') }}</td>
                        <td class="text-end">{{ number_format($ownTotal, 2) }} <x-riyal-icon size="sm" /></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endif

@if ($hasDeferral)
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('owner.month_closing.deferral.title') }}</h5>
            <small class="text-muted">{{ __('owner.month_closing.deferral.subtitle') }}</small>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <tbody>
                    <tr>
                        <td>{{ __('owner.month_closing.deferral.brought_forward') }}</td>
                        <td class="text-end">{{ number_format($broughtForward, 2) }} <x-riyal-icon size="sm" /></td>
                    </tr>
                    <tr>
                        <td>{{ __('owner.month_closing.deferral.own') }}</td>
                        <td class="text-end">{{ number_format($ownTotal, 2) }} <x-riyal-icon size="sm" /></td>
                    </tr>
                    <tr class="table-light fw-bold">
                        <td>{{ __('owner.month_closing.deferral.considered') }}</td>
                        <td class="text-end">{{ number_format($consideredTotal, 2) }} <x-riyal-icon size="sm" /></td>
                    </tr>
                    <tr>
                        <td>{{ __('owner.month_closing.deferral.charged') }}</td>
                        <td class="text-end">{{ number_format($charged, 2) }} <x-riyal-icon size="sm" /></td>
                    </tr>
                    <tr class="{{ $deferred > 0 ? 'table-warning' : 'table-light' }} fw-bold">
                        <td>{{ __('owner.month_closing.deferral.deferred') }}</td>
                        <td class="text-end">{{ number_format($deferred, 2) }} <x-riyal-icon size="sm" /></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endif
