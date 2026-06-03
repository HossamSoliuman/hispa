@extends('owner.layouts.master')
@section('title')
    {{ __('owner.payrolls.create.title') }}
@endsection
@section('css')
    <link href="{{asset('dashboard/assets/plugins/tag-it/css/jquery.tagit.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

@endsection
@section('content')

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">{{ __('owner.payrolls.create.title') }}</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">{{ __('owner.payrolls.manage_title') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ __('owner.payrolls.create.breadcrumb') }}</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card mb-4">
            <div class="px-3 py-2">{{ __('owner.payrolls.create.card_header') }}</div>
            <div class="card-body">

            @if(isset($payroll) && !empty($payroll->details))
            <hr>

            <form method="POST" action="{{ route('owner.payrolls.store') }}">
                @csrf

                <input type="number" readonly name="year" value="{{ $year }}">
                <input type="number" readonly name="month" value="{{ $month }}">

                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th>{{ __('owner.payrolls.show.employee') }}</th>
                            <th>{{ __('owner.payrolls.show.salary_type') }}</th>
                            <th>{{ __('owner.generated.basic_salary') }}</th>
                            <th>{{ __('owner.generated.increase') }}</th>
                            <th>{{ __('owner.generated.deduction') }}</th>
                            <th>{{ __('owner.expenses.show.notes') }}</th>
                            <th>{{ __('owner.generated.net') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($payroll->details as $i => $d)
                        <tr>
                            <td>
                                {{ $d->user->name }}
                                <input type="hidden" name="details[{{ $i }}][id]" value="{{ $d->id }}" />
                                <input type="hidden" name="details[{{ $i }}][user_id]" value="{{ $d->user_id }}" />
                            </td>
                            
                            <td>
                                <input type="text" disabled readonly class="form-control base" value="{{ $d->user->salary_type }}" />
                            </td>
                            <td>
                                <input type="text" disabled readonly class="form-control base" value="@if($d->user->salary_type == 'percentage') {{ $d->percentage }} @else {{ $d->base_salary }} @endif" />
                            </td>               

                            <td>
                                <input type="number" name="details[{{ $i }}][increase]" class="form-control increase" value="0">
                            </td>
                            <td>
                                <input type="number" name="details[{{ $i }}][deduction]" class="form-control deduction" value="0">
                            </td>
                            <td>
                                <input type="text" name="details[{{ $i }}][note]" class="form-control">
                            </td>

                            <td>
                                <input type="text" disabled readonly class="form-control base" value="{{ $d->final_salary }}" />
                            </td>

                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <button class="btn btn-success">
                    {{ __('owner.generated.save_draft') }}</button>
            </form>
            @else

            <form method="POST" action="{{ route('owner.payrolls.percentageCheck') }}">
                @csrf
                

                <div class="row">
                    <div class="col-md-4">
                        <label>{{ __('owner.generated.the_year') }}</label>
                        <input type="number" name="year"
                            class="form-control"
                            value="{{ old('year', $year ?? now()->year) }}"
                            required>
                    </div>

                    <div class="col-md-4">
                        <label>{{ __('owner.generated.month') }}</label>
                        <select name="month" class="form-control" required>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}"
                                    @selected(($month ?? now()->month) == $m)>
                                    {{ $m }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                        <button class="btn btn-primary w-100">
                            {{ __('owner.generated.continue') }}</button>
                    </div>
                </div>
            </form>

            @endif
                

            </div>
            <div class="card-arrow">
                <div class="card-arrow-top-left"></div>
                <div class="card-arrow-top-right"></div>
                <div class="card-arrow-bottom-left"></div>
                <div class="card-arrow-bottom-right"></div>
            </div>
        

    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
@endsection
