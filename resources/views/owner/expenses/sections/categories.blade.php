@php
    $totalAll = $categoriesRate->sum('total_amount');
@endphp

<div class="card mt-4 shadow-sm border-0">
    <div class="card-header bg-white">
    <h5 class="mb-0">{{ __('owner.expenses.sections.categories.title') }}</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-bordered mb-0 text-center align-middle">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('owner.expenses.sections.categories.table.category') }}</th>
                        <th>{{ __('owner.expenses.sections.categories.table.expenses_count') }}</th>
                        <th>{{ __('owner.expenses.sections.categories.table.total_amount') }}</th>
{{--                        <th>{{ __('owner.generated.average') }}</th>--}}
                        <th>{{ __('owner.expenses.sections.categories.table.percentage') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categoriesRate as $category)
                        @php
                            $percentage = $totalAll > 0 ? ($category->total_amount / $totalAll) * 100 : 0;
                        @endphp
                        <tr>
                            <td>{{ $category->name }}</td>
                            <td>{{ $category->expenses_count }}</td>
                            <td>{!! number_format($category->total_amount, 2) . ' <span class="unit">' . view('components.riyal-icon', ['size' => 'sm'])->render() . '</span>' !!}</td>
{{--                            <td>{{ number_format($category->total_amount / $category->expenses_count{{ __('owner.generated.total_2') }}) }} {{ __('owner.units.sar') }}</td>--}}
                            <td>{{ number_format($percentage, 1) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
