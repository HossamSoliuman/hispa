@extends('frontend.layouts.master-auth')
@section('title')
    تسجيل دخول
@endsection
@section('css')
    <style>
        .invalid-feedback,
        .text-danger {
            color: red;
        }
    </style>
@endsection
@section('content')
    <!-- BEGIN login -->
    <div class="login">
        <!-- BEGIN login-content -->
        <div class="login-content">
            <form action="{{ route('frontend.login') }}" method="POST" name="login_form">
                @csrf
                 @php
                    $locale = app()->getLocale();
                    // Use the same locale-specific logo approach as owner/dalal dashboards
                    $logoPath = $locale === 'ar'
                        ? asset('logo/arabic/main.png')
                        : asset('logo/english/main.png');
                @endphp
                <img src="{{ $logoPath }}" alt="{{ $settings['title'] ?? 'حسبة' }}" style="height: 200px; width: auto; margin:auto;display:block;">
                
                <h1 class="text-center">تسجيل دخول</h1>
                <div class="text-inverse text-opacity-50 text-center mb-4">
                    لحمايتك، يرجى التحقق من هويتك.
                </div>
                <div class="mb-3">
                    <label class="form-label">البريد الاكتروني<span class="text-danger">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                        class="form-control form-control-lg bg-inverse bg-opacity-5 @error('email') is-invalid @enderror"
                        placeholder="البريد الاكتروني">

                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="mb-3">
                    <div class="d-flex">
                        <label class="form-label">كلمة المرور <span class="text-danger">*</span></label>
                        {{-- <a href="#" class="ms-auto text-inverse text-decoration-none text-opacity-50">Forgot --}}
                        {{-- password?</a> --}}
                    </div>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        class="form-control form-control-lg bg-inverse bg-opacity-5 @error('password') is-invalid @enderror"
                        placeholder="كلمة المرور">
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" value="1" id="remember"
                            {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">تذكرني</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-outline-theme btn-lg d-block w-100 fw-500 mb-3">تسجيل
                    دخول</button>
                <div class="text-center text-inverse text-opacity-50">
                    ليس لديك حساب حتى الآن؟ <a href="{{ route('frontend.register') }}">تسجيل جديد</a>.
                </div>

                @env('local')
                <div class="mt-4 pt-3" style="border-top: 1px dashed rgba(255,255,255,0.2);">
                    <p class="text-center text-inverse text-opacity-50 small mb-2">تسجيل دخول سريع (بيئة التطوير)</p>
                    <div class="d-flex gap-2 justify-content-center flex-wrap">
                        <x-login-link
                            email="owner@example.com"
                            label="Owner"
                            :redirect-url="route('owner.dashboard')"
                            guard="owner"
                        />
                        <x-login-link
                            email="dalal@example.com"
                            label="Dalal"
                            guard="dalal"
                        />
                        <x-login-link
                            email="counter@example.com"
                            label="Counter"
                            guard="counter"
                        />
                    </div>
                </div>
                @endenv
            </form>
        </div>
        <!-- END login-content -->
    </div>
    <!-- END login -->
@endsection
