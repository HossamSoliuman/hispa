@extends('owner.layouts.master')
@section('title')
    {{ __('owner.generated.item_a06ee6') }}
@endsection
@section('css')
    <style>
        .table th {
            width: 150px;
            color: #0d6efd;
        }
    </style>
    @endsection
@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow border-0">
                    <div class="card-body">
                        <div class="row align-items-center">

                       <div class="col-md-3 text-center">
                          {{-- `User::getLogoAttribute()` already returns a full URL (ui-avatars or storage URL),
                              so don't wrap it with `asset()` which would produce an invalid URL. --}}
                          <img src="{{ $user->logo }}"
                              class="img-thumbnail rounded-circle mb-3"
                              style="width: 150px; height: 150px; object-fit: cover;"
                              alt="User Logo">
                                <h5 class="mt-2">{{ $user->name }}</h5>
                                <span class="badge bg-info text-dark">{{ $user->role }}</span>
                            </div>

                            <div class="col-md-9">
                                <table class="table table-sm table-borderless">
                                    <tbody>
                                    <tr>
                                        <th scope="row">{{ __('owner.generated.id_number') }}</th>
                                        <td>{{ $user->id_number ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">{{ __('owner.generated.email_address') }}</th>
                                        <td>{{ $user->email ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">{{ __('owner.generated.mobile_number') }}</th>
                                        <td>{{ $user->phone ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">{{ __('owner.generated.city') }}</th>
                                        <td>{{ $user->city->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">{{ __('owner.generated.governorate') }}</th>
                                        <td>{{ $user->governorate->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">{{ __('owner.generated.region') }}</th>
                                        <td>{{ $user->region->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">{{ __('owner.generated.address') }}</th>
                                        <td>{{ $user->address ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">{{ __('owner.generated.vat_number') }}</th>
                                        <td>{{ $user->tax_number ?? '-' }}</td>
                                    </tr>
                                    </tbody>
                                </table>

                                <a href="{{ route('frontend.profile.index') }}" class="btn btn-outline-primary mt-3">
                                    {{ __('owner.generated.edit_profile') }}</a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
@section('script')
@endsection
