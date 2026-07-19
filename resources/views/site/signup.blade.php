@extends('site.layouts.auth')

@section('title', __('marketing.signup.title') . ' - ' . __('site.meta.title'))

@section('content')
<main class="min-h-screen bg-transparent">
    <div class="grid min-h-screen lg:grid-cols-[0.8fr_1.2fr]">
        <aside class="relative overflow-hidden bg-ocean px-5 py-7 text-white sm:px-8 lg:flex lg:min-h-screen lg:flex-col lg:justify-between lg:px-12 lg:py-10">
            <div class="marine-grid pointer-events-none absolute inset-0 opacity-45"></div>
            <div class="pointer-events-none absolute -start-28 top-28 h-80 w-80 rounded-full bg-tide/20 blur-3xl"></div>
            <div class="relative z-10 flex items-center justify-between">
                <a href="{{ route('landing-page') }}" class="inline-flex">
                    <img src="{{ $platformLogoOnDarkUrl }}" alt="{{ __('site.meta.title') }}" class="h-auto w-24 object-contain" />
                </a>
                <a href="{{ route('landing-page') }}#pricing" class="text-xs font-bold text-white/62 underline decoration-white/20 underline-offset-4 hover:text-white">{{ __('marketing.checkout.change_plan') }}</a>
            </div>

            <div class="relative z-10 mt-10 lg:my-auto lg:max-w-md">
                <p class="text-xs font-bold text-tide">{{ __('marketing.signup.plan_summary') }}</p>

                @if($selectedPackage)
                    <div class="mt-4 border border-white/20 bg-white/[0.07] p-5 backdrop-blur-sm sm:p-6">
                        <div class="flex items-start justify-between gap-5">
                            <div class="text-start">
                                <h2 class="text-2xl font-extrabold text-white">{{ $selectedPackage->name }}</h2>
                                <p class="mt-2 text-xs leading-6 text-white/52">{{ $selectedPackage->boatsLabel() }} · {{ __('site.pricing.durations.' . $selectedPackage->duration_type) }}</p>
                            </div>
                            <div class="shrink-0 text-end">
                                <p class="text-2xl font-extrabold text-[#9ee8d3]" dir="ltr">{{ number_format((float) $selectedPackage->effective_price, 0) }}</p>
                                <p class="mt-1 text-[0.6rem] text-white/45">{{ __('site.pricing.per.' . $selectedPackage->duration_type) }}</p>
                            </div>
                        </div>

                        <div class="mt-6 h-px bg-white/10"></div>

                        <h3 class="mt-5 text-sm font-extrabold text-white">{{ __('marketing.signup.creates_title') }}</h3>
                        <ul class="mt-4 grid gap-3">
                            @foreach([__('marketing.signup.creates_1'), __('marketing.signup.creates_2'), __('marketing.signup.creates_3')] as $item)
                                <li class="flex items-start gap-3 text-xs leading-6 text-white/62">
                                    <span class="mt-1 grid h-4 w-4 shrink-0 place-items-center rounded-full bg-tide/20 text-[#70d7c9]">
                                        <svg class="h-2.5 w-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true"><path d="M5 12l4 4L19 6" /></svg>
                                    </span>
                                    <span>{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="mt-4 border border-tide/30 bg-tide/10 p-5 sm:p-6">
                        <h2 class="text-lg font-extrabold text-white">{{ __('marketing.signup.no_plan_title') }}</h2>
                        <p class="mt-2 text-xs leading-6 text-white/58">{{ __('marketing.signup.no_plan_description') }}</p>
                        <a href="{{ route('landing-page') }}#pricing" class="mt-5 inline-flex rounded-xl bg-tide px-4 py-2.5 text-xs font-extrabold text-white">{{ __('marketing.signup.choose_plan') }}</a>
                    </div>
                @endif
            </div>

            <p class="relative z-10 mt-8 hidden text-xs leading-6 text-white/40 lg:block">{{ __('marketing.hero.note') }}</p>
        </aside>

        <section class="flex items-center justify-center px-5 py-10 sm:px-8 lg:px-12 lg:py-14">
            <div class="w-full max-w-2xl">
                <div class="grid grid-cols-[1fr_auto_1fr_auto_1fr] items-start gap-2" aria-label="{{ __('marketing.checkout.step_label', ['current' => 2]) }}">
                    @foreach([
                        ['number' => 1, 'label' => __('marketing.checkout.steps.plan'), 'state' => 'done'],
                        ['number' => 2, 'label' => __('marketing.checkout.steps.payment'), 'state' => 'active'],
                        ['number' => 3, 'label' => __('marketing.checkout.steps.dashboard'), 'state' => 'next'],
                    ] as $step)
                        <div class="flex flex-col items-center text-center">
                            <span @class([
                                'grid h-9 w-9 place-items-center rounded-full text-xs font-extrabold',
                                'bg-tide text-white' => $step['state'] === 'done',
                                'bg-ink text-white shadow-lg' => $step['state'] === 'active',
                                'border border-ink/15 bg-white text-ink/38' => $step['state'] === 'next',
                            ])>
                                @if($step['state'] === 'done')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M5 12l4 4L19 6" /></svg>
                                @else
                                    {{ $step['number'] }}
                                @endif
                            </span>
                            <span @class(['mt-2 text-[0.62rem] font-bold sm:text-xs', 'text-ink' => $step['state'] === 'active', 'text-ink/42' => $step['state'] !== 'active'])>{{ $step['label'] }}</span>
                        </div>
                        @if(! $loop->last)
                            <span @class(['mt-[1.1rem] h-px w-full', 'bg-tide' => $step['state'] === 'done', 'bg-ink/12' => $step['state'] !== 'done'])></span>
                        @endif
                    @endforeach
                </div>

                <div class="mt-10 text-start">
                    <span class="eyebrow text-ocean">{{ __('marketing.checkout.step_label', ['current' => 2]) }}</span>
                    <h1 class="mt-4 text-3xl font-extrabold tracking-[-0.04em] text-ink md:text-4xl">{{ __('marketing.signup.title') }}</h1>
                    <div class="mt-3 flex flex-col gap-2 text-sm text-ink/52 sm:flex-row sm:items-center sm:justify-between">
                        <p>{{ __('marketing.signup.description') }}</p>
                        <p class="shrink-0">{{ __('site.signup.has_account') }} <a href="{{ route('frontend.show_login_form') }}" class="font-bold text-ocean underline decoration-ocean/25 underline-offset-4">{{ __('site.signup.login_link') }}</a></p>
                    </div>
                </div>

                @if($errors->any())
                    <div class="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-start text-sm text-red-700" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form id="signupForm" action="{{ route('frontend.register') }}" method="post" class="mt-8 grid gap-5 sm:grid-cols-2">
                    @csrf
                    <input type="hidden" name="guard" value="{{ $guard ?? 'owner' }}" />
                    @if($selectedPackage)
                        <input type="hidden" name="package_id" value="{{ $selectedPackage->id }}" />
                    @endif

                    <div class="sm:col-span-2">
                        <label for="fullNameInput" class="mb-2 block text-xs font-bold text-ink/72">{{ __('site.signup.name') }}</label>
                        <input id="fullNameInput" name="name" type="text" value="{{ old('name') }}" placeholder="{{ __('site.signup.name_placeholder') }}" class="checkout-field" autocomplete="name" required autofocus />
                        @error('name')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="emailInput" class="mb-2 block text-xs font-bold text-ink/72">{{ __('site.signup.email') }}</label>
                        <input id="emailInput" name="email" type="email" value="{{ old('email') }}" placeholder="{{ __('site.signup.email_placeholder') }}" class="checkout-field text-left" dir="ltr" autocomplete="email" required />
                        @error('email')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="phoneInput" class="mb-2 block text-xs font-bold text-ink/72">{{ __('site.signup.phone') }}</label>
                        <input id="phoneInput" name="phone" type="tel" value="{{ old('phone') }}" placeholder="{{ __('site.signup.phone_placeholder') }}" class="checkout-field text-left" dir="ltr" inputmode="tel" autocomplete="tel" required />
                        @error('phone')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="passwordInput" class="mb-2 block text-xs font-bold text-ink/72">{{ __('site.signup.password') }}</label>
                        <div class="relative">
                            <input id="passwordInput" name="password" type="password" placeholder="{{ __('site.signup.password_placeholder') }}" class="checkout-field pe-11" autocomplete="new-password" minlength="8" required />
                            <button type="button" class="absolute inset-y-0 end-2 grid w-9 place-items-center text-ink/38 hover:text-ocean" data-password-toggle="passwordInput" aria-label="{{ __('site.aria.toggle_password') }}">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z" /><circle cx="12" cy="12" r="3" /></svg>
                            </button>
                        </div>
                        @error('password')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="confirmPasswordInput" class="mb-2 block text-xs font-bold text-ink/72">{{ __('site.signup.password_confirm') }}</label>
                        <div class="relative">
                            <input id="confirmPasswordInput" name="password_confirmation" type="password" placeholder="{{ __('site.signup.password_confirm_placeholder') }}" class="checkout-field pe-11" autocomplete="new-password" minlength="8" required />
                            <button type="button" class="absolute inset-y-0 end-2 grid w-9 place-items-center text-ink/38 hover:text-ocean" data-password-toggle="confirmPasswordInput" aria-label="{{ __('site.aria.toggle_password_confirm') }}">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z" /><circle cx="12" cy="12" r="3" /></svg>
                            </button>
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <button type="submit" class="inline-flex min-h-13 w-full items-center justify-center gap-2 bg-ocean px-6 py-3.5 text-sm font-bold text-white shadow-[0_12px_28px_rgba(54,117,194,.16)] hover:-translate-y-0.5 hover:bg-ocean-deep">
                            {{ $selectedPackage ? __('marketing.signup.submit_with_plan') : __('marketing.signup.submit_without_plan') }}
                            <svg class="h-4 w-4 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m9 18 6-6-6-6" /></svg>
                        </button>
                        <div class="mt-4 flex flex-col items-center gap-1 text-center text-[0.68rem] leading-5 text-ink/42 sm:flex-row sm:justify-between sm:text-start">
                            <p>{{ __('marketing.signup.terms_note') }}</p>
                            <p class="shrink-0 font-semibold text-ocean">{{ __('marketing.signup.privacy_note') }}</p>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
</main>

@push('scripts')
<script>
    document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
        button.addEventListener('click', function () {
            var input = document.getElementById(button.dataset.passwordToggle);

            if (input) {
                input.type = input.type === 'password' ? 'text' : 'password';
            }
        });
    });
</script>
@endpush
@endsection
