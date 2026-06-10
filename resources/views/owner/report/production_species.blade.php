@extends('owner.layouts.master')

@section('title', __('owner.analysis_reports.production_species.title'))

@section('content')
    <div class="d-flex align-items-center mb-3">
        <div>
            <h2 class="mb-1">{{ __('owner.analysis_reports.production_species.title') }}</h2>
            <p class="text-muted mb-0">{{ __('owner.analysis_reports.production_species.description') }}</p>
        </div>
    </div>

    @include('owner.report.partials.filter', [
        'action' => route('owner.reports.production-species'),
        'printAction' => 'owner.reports.production-species.print',
        'showBoat' => false,
    ])

    <div class="card shadow-sm border-0">
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('owner.analysis_reports.production_species.fish') }}</th>
                        <th class="text-end">{{ __('owner.analysis_reports.production_species.caught_weight') }}</th>
                        <th class="text-end">{{ __('owner.analysis_reports.production_species.caught_value') }}</th>
                        <th class="text-end">{{ __('owner.analysis_reports.production_species.sold_weight') }}</th>
                        <th class="text-end">{{ __('owner.analysis_reports.production_species.sold_value') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr>
                            <td>{{ $row['fish_name'] }}</td>
                            <td class="text-end">{{ number_format($row['caught_weight'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['caught_value'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['sold_weight'], 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($row['sold_value'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">{{ __('owner.analysis_reports.no_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
