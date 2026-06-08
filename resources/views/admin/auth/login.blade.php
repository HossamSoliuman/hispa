@extends('admin.layouts.master-auth')

@section('title')
    {{ __('admin.dashboard.title') }} - {{ __('admin.actions.login') }}
@endsection

@section('css')
    <style>
        .invalid-feedback { display: block; }
        .auth-card {
            max-width: 440px;
            margin: 0 auto;
        }
        .login-page {
            min-height: 100vh;
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 35%, #3b82f6 65%, #60a5fa 100%);
            position: relative;
            overflow: hidden;
        }
        .login-page::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.06'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.5;
        }
        .login-content {
            position: relative;
            z-index: 1;
        }
        .login-card {
            border: 0;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
        }
        .login-card .card-body {
            padding: 2.25rem 2rem;
            background: #fff;
        }
        .login-logo-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
        }
        .login-logo {
            max-height: 72px;
            width: auto;
            object-fit: contain;
            display: block;
        }
        .login-admin-badge {
            display: inline-block;
            background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
            color: #fff;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.35rem 0.85rem;
            border-radius: 50px;
            margin-bottom: 1rem;
            letter-spacing: 0.02em;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.35);
        }
        .login-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e40af;
            margin-bottom: 0.35rem;
        }
        .login-subtitle {
            color: #64748b;
            font-size: 0.95rem;
            margin-bottom: 1.5rem;
        }
        .login-page .form-control {
            border-radius: 10px;
            padding: 0.6rem 0.9rem;
            border: 1px solid #e2e8f0;
        }
        .login-page .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }
        .login-page .btn-primary {
            border-radius: 10px;
            padding: 0.65rem 1rem;
            font-weight: 600;
            background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
            border: 0;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.4);
        }
        .login-page .btn-primary:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.45);
            transform: translateY(-1px);
        }
        .login-page .form-check-input:checked {
            background-color: #2563eb;
            border-color: #2563eb;
        }
        .login-page .form-label {
            font-weight: 500;
            color: #334155;
        }
    </style>
@endsection

@section('content')
    <div class="login-page d-flex align-items-center justify-content-center py-4">
        <div class="login-content w-100 auth-card px-3">
            <div class="card login-card shadow">
                <div class="card-body">
                    <div class="login-logo-wrap">
                        <span class="login-admin-badge">{{ __('admin.auth.admin_panel') }}</span>
                        <img src="{{ asset('logo/' . (app()->getLocale() == 'ar' ? 'arabic' : 'english') . '/main.png') }}" alt="{{ __('admin.dashboard.title') }}" class="login-logo" />
                    </div>
                    <h1 class="login-title text-center">{{ __('admin.auth.login') }}</h1>
                    <p class="login-subtitle text-center">{{ __('admin.auth.login_subtitle') }}</p>

                    <form method="POST" action="{{ route('admin.login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('admin.auth.email') }}</label>
                            <input id="email"
                                   type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   name="email"
                                   value="{{ old('email') }}"
                                   required
                                   autocomplete="email"
                                   autofocus
                                   placeholder="{{ __('admin.auth.email_placeholder') }}">

                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('admin.auth.password') }}</label>
                            <input id="password"
                                   type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   name="password"
                                   required
                                   autocomplete="current-password"
                                   placeholder="{{ __('admin.auth.password_placeholder') }}">

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    {{ __('admin.auth.remember_me') }}
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            {{ __('admin.auth.login_button') }}
                        </button>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


