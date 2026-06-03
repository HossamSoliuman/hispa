@extends('site.layouts.auth')
@section('title', __('site.login.title') . ' - ' . __('site.meta.title'))

@section('content')
<main class="min-h-screen">
    <div class="grid min-h-screen w-full lg:grid-cols-2">
        {{-- RIGHT: Promo Panel --}}
        <section class="relative hidden lg:block overflow-hidden bg-[#3B74B8] px-6 py-10 text-white">
            <div class="pointer-events-none absolute -right-24 -top-24 h-96 w-96 rounded-full bg-white/10"></div>
            <div class="mx-auto flex h-full max-w-md flex-col justify-center gap-10">
                <div class="relative min-h-[280px]">
                    <div class="rounded-lg bg-white p-6 text-right text-slate-900 shadow-md">
                        <h2 class="text-base font-extrabold text-[#2F6FDB] md:text-2xl">{{ __('site.hero.title') }}</h2>
                        <p class="mt-3 text-sm leading-7 text-[#718096]">{{ __('site.hero.subtitle') }}</p>
                        <div class="mt-5">
                            <a href="{{ route('landing-page') }}#home" type="button" class="rounded-full bg-[linear-gradient(98.7deg,#3778BC_19.22%,#4BAEE5_73.07%)] px-8 py-3 text-sm font-semibold text-white hover:opacity-90 focus:outline-none focus:ring-4 focus:ring-blue-500/30 transition-all inline-block">{{ __('site.login.explore') }}</a>
                        </div>
                    </div>
                    <div class="absolute -left-10 -bottom-8 w-44 rounded-md bg-white p-2 text-slate-900 shadow-xl border border-slate-100/50">
                        <div class="flex items-center justify-start gap-2">
                            <span class="grid h-9 w-9 flex-shrink-0 place-items-center rounded-full bg-[#3576BC]/5 text-[#3B74B8]">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5"><path d="M4 19V5" /><path d="M8 19v-6" /><path d="M12 19V9" /><path d="M16 19v-8" /><path d="M20 19v-3" /></svg>
                            </span>
                            <div class="text-start">
                                <p class="text-xs text-[#3C74BE]">{{ __('site.login.net_profit') }}</p>
                                <p class="text-lg font-extrabold whitespace-nowrap" style="background: linear-gradient(104.3deg, #0179B4 7.76%, #88D8FF 90.48%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">{{ __('site.login.net_profit_sample') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <h3 class="text-lg font-extrabold">{{ __('site.login.features_title') }}</h3>
                    <p id="featureText" class="mt-1 text-sm leading-7 text-white/80 transition-opacity duration-300 min-h-[100px] flex items-center">{{ __('site.login.feature_1') }}</p>
                    <div class="mt-6 flex items-center justify-center gap-4">
                        <button id="nextFeature" class="grid h-9 w-9 place-items-center rounded-full bg-white/10 hover:bg-white/15 focus:outline-none focus:ring-4 focus:ring-white/20 transition-all" aria-label="{{ __('site.aria.next') }}" type="button">
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6" /></svg>
                        </button>
                        <div id="featureDots" class="flex items-center gap-2"></div>
                        <button id="prevFeature" class="grid h-9 w-9 place-items-center rounded-full bg-white/10 hover:bg-white/15 focus:outline-none focus:ring-4 focus:ring-white/20 transition-all" aria-label="{{ __('site.aria.prev') }}" type="button">
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 6l-6 6 6 6" /></svg>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        {{-- LEFT: Login Form --}}
        <section class="flex items-center justify-center bg-white px-6 py-10">
            <div class="w-full max-w-md">
                <div class="flex justify-start">
                    <img src="{{ asset('site/assets/logo.png') }}" alt="{{ __('site.aria.logo') }}" class="h-fit w-28 object-contain">
                </div>
                <h1 class="mt-6 text-start text-3xl font-extrabold text-slate-900">{{ __('site.login.title') }}</h1>
                <p class="mt-3 text-start text-sm text-slate-500">
                    {{ __('site.login.no_account') }}
                    <a href="{{ route('frontend.show_register_form') }}" class="font-semibold underline text-blue-600 hover:text-blue-700">{{ __('site.login.create_account') }}</a>
                </p>

                <form id="loginForm" action="{{ route('frontend.login') }}" method="post" class="mt-8 space-y-4">
                    @csrf
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">{{ __('site.login.email') }}</label>
                        <input id="emailInput" name="email" type="email" dir="rtl" placeholder="{{ __('site.login.email_placeholder') }}" value="{{ old('email') }}"
                            class="h-11 w-full rounded-md border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none ring-blue-500/30 placeholder:text-slate-400 focus:border-blue-500 focus:ring-4" />
                        <p id="emailInputError" class="mt-1 text-xs text-rose-600 hidden"></p>
                        @error('email')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">{{ __('site.login.password') }}</label>
                        <div class="relative">
                            <input id="passwordInput" name="password" type="password" placeholder="{{ __('site.login.password_placeholder') }}"
                                class="h-11 w-full rounded-md border border-slate-200 bg-white px-4 pe-11 text-sm text-slate-900 outline-none ring-blue-500/30 placeholder:text-slate-400 focus:border-blue-500 focus:ring-4" />
                            <button id="togglePassword" type="button" class="absolute inset-y-0 left-2 inline-flex items-center justify-center rounded-md px-2 text-slate-500 hover:text-slate-700" aria-label="{{ __('site.aria.toggle_password') }}">
                                <svg id="eyeIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z" /><path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" /></svg>
                            </button>
                        </div>
                        <p id="passwordInputError" class="mt-1 text-xs text-rose-600 hidden"></p>
                        @error('password')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex items-center justify-between pt-1 text-sm">
                        <a href="#forgot" class="text-blue-600 hover:text-blue-700">{{ __('site.login.forgot_password') }}</a>
                        <label class="inline-flex items-center gap-2 text-slate-600">
                            <input id="rememberMe" name="remember" type="checkbox" value="1" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500" {{ old('remember') ? 'checked' : '' }} />
                            {{ __('site.login.remember') }}
                        </label>
                    </div>
                    <button type="submit" class="mt-2 h-11 w-full rounded-md bg-[#3B74B8] text-sm font-semibold text-white shadow-sm hover:bg-[#336aa9] focus:outline-none focus:ring-4 focus:ring-blue-500/30">{{ __('site.login.submit') }}</button>
                    <p id="formMsg" class="hidden text-center text-sm"></p>
                </form>
            </div>
        </section>
    </div>
</main>

@push('scripts')
<script>
(function() {
    const passwordInput = document.getElementById("passwordInput");
    const togglePassword = document.getElementById("togglePassword");
    if (togglePassword) togglePassword.addEventListener("click", () => { passwordInput.type = passwordInput.type === "password" ? "text" : "password"; });

    const emailInput = document.getElementById("emailInput");
    const rememberMe = document.getElementById("rememberMe");
    const savedEmail = localStorage.getItem("hesba_email");
    if (savedEmail && emailInput) { emailInput.value = savedEmail; if (rememberMe) rememberMe.checked = true; }
    if (rememberMe) rememberMe.addEventListener("change", () => { if (!rememberMe.checked) localStorage.removeItem("hesba_email"); else if (emailInput) localStorage.setItem("hesba_email", emailInput.value.trim()); });
    if (emailInput) emailInput.addEventListener("input", () => { if (rememberMe && rememberMe.checked) localStorage.setItem("hesba_email", emailInput.value.trim()); });

    function showError(inputId, message) {
        const input = document.getElementById(inputId);
        const error = document.getElementById(inputId + 'Error');
        if (input) { input.classList.remove('border-slate-200'); input.classList.add('border-rose-500'); }
        if (error) { error.textContent = message; error.classList.remove('hidden'); }
    }
    function clearError(inputId) {
        const input = document.getElementById(inputId);
        const error = document.getElementById(inputId + 'Error');
        if (input) { input.classList.remove('border-rose-500'); input.classList.add('border-slate-200'); }
        if (error) error.classList.add('hidden');
    }
    const loginForm = document.getElementById("loginForm");
    const formMsg = document.getElementById("formMsg");
    if (emailInput) emailInput.addEventListener('input', () => clearError('emailInput'));
    if (passwordInput) passwordInput.addEventListener('input', () => clearError('passwordInput'));

    const features = [
        @json(__('site.login.feature_1')),
        @json(__('site.login.feature_2')),
        @json(__('site.login.feature_3'))
    ];
    const featureText = document.getElementById("featureText");
    const featureDots = document.getElementById("featureDots");
    const prevFeature = document.getElementById("prevFeature");
    const nextFeature = document.getElementById("nextFeature");
    let idx = 0;
    const ariaFeatureNum = {{ json_encode(__('site.aria.feature_num', ['num' => '__NUM__'])) }};
    function renderDots() {
        if (!featureDots) return;
        featureDots.innerHTML = "";
        features.forEach((_, i) => {
            const dot = document.createElement("button");
            dot.type = "button";
            dot.className = "h-2.5 w-2.5 rounded-full transition " + (i === idx ? "bg-white" : "bg-white/40 hover:bg-white/70");
            dot.setAttribute("aria-label", ariaFeatureNum.replace('__NUM__', i + 1));
            dot.addEventListener("click", () => { idx = i; updateFeature(); });
            featureDots.appendChild(dot);
        });
    }
    function updateFeature() {
        if (featureText) featureText.style.opacity = '0';
        setTimeout(() => {
            if (featureText) featureText.textContent = features[idx];
            renderDots();
            if (featureText) featureText.style.opacity = '1';
        }, 150);
    }
    if (prevFeature) prevFeature.addEventListener("click", () => { idx = (idx - 1 + features.length) % features.length; updateFeature(); });
    if (nextFeature) nextFeature.addEventListener("click", () => { idx = (idx + 1) % features.length; updateFeature(); });
    updateFeature();
})();
</script>
@endpush
@endsection
