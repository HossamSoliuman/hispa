@extends('frontend.layouts.master-auth')
@section('title')
    حسبة | تسجيل جديد
@endsection
@section('css')
    <style>
        .invalid-feedback,.text-danger{
            color: red;
        }
    </style>
@endsection
@section('content')

    <!-- BEGIN login -->
    <div class="login">
        <!-- BEGIN login-content -->
        <div class="login-content">
            <form action="{{ route('frontend.register') }}" method="POST" name="login_form">
                @csrf
                @php
                    $locale = app()->getLocale();
                    $logoPath = $locale === 'ar'
                        ? asset('logo/arabic/main.png')
                        : asset('logo/english/main.png');
                @endphp
                <img src="{{ $logoPath }}" alt="{{ $settings['title'] ?? 'حسبة' }}" style="height: 200px; width: auto; margin:auto;display:block;">

                <h1 class="text-center">تسجيل جديد</h1>
                <div class="text-inverse text-opacity-50 text-center mb-4">
                    لحمايتك، يرجى التحقق من هويتك.
                </div>
                <div class="mb-3">
                    <label class="form-label">الاسم كاملاً<span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                           class="form-control form-control-lg bg-inverse bg-opacity-5 @error('name') is-invalid @enderror"
                           placeholder="الاسم كاملاً">

                    @error('name')
                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                    @enderror
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
                        <label for="role" class="form-label">نوع المستخدم<span
                                class="text-danger">*</span></label>
                        <select name="role"   autocomplete="role" autofocus    class="form-control form-control-lg bg-inverse bg-opacity-5 @error('role') is-invalid @enderror" required id="role">
                            <option value="">اختر</option>
                            <option value="owner">صيّاد</option>
                            <option value="counter">عداد</option>
                            <option value="dalal">دلال</option>


                        </select>


                        @error('role') <span class="text-danger error">{{ $message }}</span>@enderror

                    </div>



                <div class="mb-3">
                    <label class="form-label">رقم الجوال<span class="text-danger">*</span></label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required autocomplete="phone" autofocus
                           class="form-control form-control-lg bg-inverse bg-opacity-5 @error('phone') is-invalid @enderror"
                           placeholder="رقم الجوال">

                    @error('phone')
                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                    @enderror
                </div>

                <div class="mb-3">
                    <div class="d-flex">
                        <label class="form-label">كلمة المرور <span class="text-danger">*</span></label>
                        {{--                        <a href="#" class="ms-auto text-inverse text-decoration-none text-opacity-50">Forgot--}}
                        {{--                            password?</a>--}}
                    </div>
                    <input id="password" type="password" name="password"  required
                           autocomplete="new-password"
                           class="form-control form-control-lg bg-inverse bg-opacity-5 @error('password') is-invalid @enderror"
                           placeholder="كلمة المرور">
                    @error('password')
                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                    @enderror
                </div>    <div class="mb-3">
                    <div class="d-flex">
                        <label class="form-label">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                        {{--                        <a href="#" class="ms-auto text-inverse text-decoration-none text-opacity-50">Forgot--}}
                        {{--                            password?</a>--}}
                    </div>
                    <input id="password_confirmation" type="password" name="password_confirmation"  required
                           autocomplete="new-password"
                           class="form-control form-control-lg bg-inverse bg-opacity-5 @error('password_confirmation') is-invalid @enderror"
                           placeholder="تأكيد كلمة المرور">
                    @error('password_confirmation')
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
                <button type="submit" class="btn btn-outline-theme btn-lg d-block w-100 fw-500 mb-3">تسجيل جديد</button>
                                <div class="text-center text-inverse text-opacity-50">
                                    لديك حساب بالفعل? <a href="{{route('frontend.login')}}">تسجيل دخول</a>.
                                </div>
            </form>
        </div>
        <!-- END login-content -->
    </div>
    <!-- END login -->



@endsection
