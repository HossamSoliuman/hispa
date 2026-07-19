@extends('site.layouts.auth')

@section('html-class', 'checkout-viewport')
@section('body-class', 'checkout-page')
@section('title', __('marketing.checkout.page_title') . ' - ' . __('site.meta.title'))

@php
    $currency = __('site.pricing.currency');
    $selectedPrice = $selectedPackage ? number_format((float) $selectedPackage->effective_price, 0) : '—';
    $selectedDuration = $selectedPackage ? __('site.pricing.durations.' . $selectedPackage->duration_type) : '';
    $selectedBoats = $selectedPackage?->boatsLabel() ?? '';
    $hasBankDetails = $bank['bank_name'] !== '' || $bank['account_name'] !== '' || $bank['account_number'] !== '';
    $switchLocale = app()->getLocale() === 'ar' ? 'en' : 'ar';
    $checkoutSteps = [
        1 => [
            'label' => __('marketing.checkout.steps.account'),
            'description' => __('marketing.checkout.step_descriptions.account'),
        ],
        2 => [
            'label' => __('marketing.checkout.steps.payment'),
            'description' => __('marketing.checkout.step_descriptions.payment'),
        ],
        3 => [
            'label' => __('marketing.checkout.steps.dashboard'),
            'description' => __('marketing.checkout.step_descriptions.dashboard'),
        ],
    ];
@endphp

@section('content')
<main
    id="checkoutApp"
    class="checkout-app"
    data-start-step="{{ $startStep }}"
    aria-label="{{ __('marketing.checkout.page_title') }}"
>
    <div class="checkout-frame">
        <aside class="checkout-rail">
            <div class="checkout-brand-row">
                <a href="{{ route('landing-page') }}" class="checkout-brand" aria-label="{{ __('site.meta.title') }}">
                    <img src="{{ asset('site/assets/hisbah-huwat-logo-white.png') }}" alt="{{ __('site.meta.title') }}" />
                </a>
                <span class="checkout-quiet-label">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                        <path d="M7 10V8a5 5 0 0 1 10 0v2" />
                        <rect x="5" y="10" width="14" height="10" rx="2.5" />
                    </svg>
                    {{ __('marketing.checkout.quiet_label') }}
                </span>
            </div>

            <ol class="checkout-progress" aria-label="{{ __('marketing.checkout.step_label', ['current' => $startStep]) }}">
                @foreach($checkoutSteps as $number => $step)
                    <li
                        class="checkout-progress-item"
                        data-progress-step="{{ $number }}"
                        @if($number === $startStep) aria-current="step" @endif
                    >
                        <span class="checkout-progress-number" aria-hidden="true">{{ str_pad((string) $number, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="checkout-progress-copy">
                            <strong>{{ $step['label'] }}</strong>
                            <small>{{ $step['description'] }}</small>
                        </span>
                    </li>
                @endforeach
            </ol>

            <div class="checkout-rail-note">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M12 3 5 6v5c0 4.7 2.8 8.1 7 10 4.2-1.9 7-5.3 7-10V6l-7-3Z" />
                    <path d="m9 12 2 2 4-4" />
                </svg>
                <span>{{ __('marketing.checkout.no_card_note') }}</span>
            </div>
        </aside>

        <section class="checkout-workspace">
            <header class="checkout-topbar">
                <a href="{{ route('landing-page') }}#pricing" class="checkout-back-link">
                    <svg class="rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="m15 18-6-6 6-6" />
                    </svg>
                    <span>{{ __('marketing.checkout.back_to_plans') }}</span>
                </a>

                <span class="checkout-topbar-plan">
                    <span>{{ __('marketing.checkout.selected_plan') }}</span>
                    <strong data-selected-plan-name>{{ $selectedPackage?->name ?? '—' }}</strong>
                </span>

                <a
                    href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($switchLocale, null, [], true) }}"
                    class="checkout-locale"
                    lang="{{ $switchLocale }}"
                    hreflang="{{ $switchLocale }}"
                    aria-label="{{ __('marketing.nav.change_language') }}"
                >
                    {{ strtoupper($switchLocale) }}
                </a>
            </header>

            <div class="checkout-panel-stack" aria-live="polite">
                @if(! $selectedPackage)
                    <section class="checkout-step is-active" data-checkout-step="1">
                        <div class="checkout-empty-state">
                            <span class="checkout-empty-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path d="M12 8v4m0 4h.01" />
                                    <circle cx="12" cy="12" r="9" />
                                </svg>
                            </span>
                            <h1>{{ __('marketing.checkout.no_plans_title') }}</h1>
                            <p>{{ __('marketing.checkout.no_plans_description') }}</p>
                            <a href="{{ route('landing-page') }}#contact" class="checkout-primary-button">
                                {{ __('marketing.checkout.contact_team') }}
                            </a>
                        </div>
                    </section>
                @else
                    <section class="checkout-step" data-checkout-step="1" aria-labelledby="checkoutAccountTitle">
                        <div class="checkout-step-content checkout-account-content">
                            <div class="checkout-heading">
                                <span class="checkout-kicker">{{ __('marketing.checkout.account_eyebrow') }}</span>
                                <div class="checkout-heading-row">
                                    <div>
                                        <h1 id="checkoutAccountTitle" tabindex="-1">
                                            @if($owner)
                                                {{ __('marketing.checkout.signed_in_title', ['name' => $owner->name]) }}
                                            @else
                                                {{ __('marketing.checkout.account_title') }}
                                            @endif
                                        </h1>
                                        <p class="checkout-heading-description">
                                            {{ $owner ? __('marketing.checkout.signed_in_description') : __('marketing.checkout.account_description') }}
                                        </p>
                                    </div>

                                    @if(! $owner)
                                        <p class="checkout-login-note">
                                            {{ __('marketing.checkout.already_account') }}
                                            <a href="{{ route('frontend.show_login_form') }}">{{ __('marketing.checkout.sign_in') }}</a>
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div class="checkout-plan-band">
                                <span class="checkout-plan-mark" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                        <path d="M5 7.5A2.5 2.5 0 0 1 7.5 5h9A2.5 2.5 0 0 1 19 7.5v9a2.5 2.5 0 0 1-2.5 2.5h-9A2.5 2.5 0 0 1 5 16.5v-9Z" />
                                        <path d="m8.5 12 2.2 2.2 4.8-5" />
                                    </svg>
                                </span>
                                <span class="checkout-plan-main">
                                    <small>{{ __('marketing.checkout.selected_plan') }}</small>
                                    <strong data-selected-plan-name>{{ $selectedPackage->name }}</strong>
                                    <span>
                                        <span data-selected-plan-boats>{{ $selectedBoats }}</span>
                                        <b aria-hidden="true">·</b>
                                        <span data-selected-plan-duration>{{ $selectedDuration }}</span>
                                    </span>
                                    <span class="checkout-plan-mobile-price" dir="ltr"><span data-selected-plan-price>{{ $selectedPrice }}</span> {{ $currency }}</span>
                                </span>
                                <span class="checkout-plan-price">
                                    <strong dir="ltr"><span data-selected-plan-price>{{ $selectedPrice }}</span> {{ $currency }}</strong>
                                    <small>{{ __('site.pricing.per.' . $selectedPackage->duration_type) }}</small>
                                </span>

                                @if($packages->count() > 1)
                                    <label class="checkout-plan-select-wrap">
                                        <span class="sr-only">{{ __('marketing.checkout.change_plan') }}</span>
                                        <select
                                            id="checkoutPackage"
                                            name="package_id"
                                            form="checkoutAccountForm"
                                            class="checkout-plan-select"
                                            aria-label="{{ __('marketing.checkout.change_plan') }}"
                                            @disabled($startStep > 1)
                                        >
                                            @foreach($packages as $package)
                                                <option
                                                    value="{{ $package->id }}"
                                                    data-plan-name="{{ $package->name }}"
                                                    data-plan-price="{{ number_format((float) $package->effective_price, 0) }}"
                                                    data-plan-duration="{{ __('site.pricing.durations.' . $package->duration_type) }}"
                                                    data-plan-period="{{ __('site.pricing.per.' . $package->duration_type) }}"
                                                    data-plan-boats="{{ $package->boatsLabel() }}"
                                                    @selected((int) old('package_id', $selectedPackage->id) === $package->id)
                                                >
                                                    {{ $package->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                            <path d="m8 10 4 4 4-4" />
                                        </svg>
                                    </label>
                                @else
                                    <input type="hidden" name="package_id" value="{{ $selectedPackage->id }}" form="checkoutAccountForm" />
                                @endif
                            </div>

                            <form
                                id="checkoutAccountForm"
                                action="{{ route('site.checkout.register') }}"
                                method="post"
                                class="checkout-account-form"
                                novalidate
                            >
                                @csrf

                                <div class="checkout-form-error @if(! $errors->any()) hidden @endif" data-form-error role="alert">
                                    {{ $errors->first() }}
                                </div>

                                @if($owner)
                                    <div class="checkout-owner-card">
                                        <span class="checkout-owner-avatar">{{ mb_strtoupper(mb_substr($owner->name, 0, 1)) }}</span>
                                        <span>
                                            <strong>{{ $owner->name }}</strong>
                                            <small dir="ltr">{{ $owner->email }}</small>
                                        </span>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                            <path d="m5 12 4 4L19 6" />
                                        </svg>
                                    </div>
                                @else
                                    <div class="checkout-form-grid">
                                        <label class="checkout-field-group checkout-field-group-name">
                                            <span>{{ __('site.signup.name') }}</span>
                                            <input
                                                name="name"
                                                type="text"
                                                value="{{ old('name') }}"
                                                class="checkout-input @error('name') is-invalid @enderror"
                                                autocomplete="name"
                                                placeholder="{{ __('site.signup.name_placeholder') }}"
                                                required
                                            />
                                            <small data-error-for="name">@error('name'){{ $message }}@enderror</small>
                                        </label>

                                        <label class="checkout-field-group checkout-field-group-email">
                                            <span>{{ __('site.signup.email') }}</span>
                                            <input
                                                name="email"
                                                type="email"
                                                value="{{ old('email') }}"
                                                class="checkout-input @error('email') is-invalid @enderror"
                                                dir="ltr"
                                                autocomplete="email"
                                                placeholder="{{ __('site.signup.email_placeholder') }}"
                                                required
                                            />
                                            <small data-error-for="email">@error('email'){{ $message }}@enderror</small>
                                        </label>

                                        <label class="checkout-field-group checkout-field-group-phone">
                                            <span>{{ __('site.signup.phone') }}</span>
                                            <input
                                                name="phone"
                                                type="tel"
                                                value="{{ old('phone') }}"
                                                class="checkout-input @error('phone') is-invalid @enderror"
                                                dir="ltr"
                                                inputmode="tel"
                                                autocomplete="tel"
                                                placeholder="{{ __('site.signup.phone_placeholder') }}"
                                                required
                                            />
                                            <small data-error-for="phone">@error('phone'){{ $message }}@enderror</small>
                                        </label>

                                        <label class="checkout-field-group checkout-field-group-password">
                                            <span>{{ __('site.signup.password') }}</span>
                                            <input
                                                name="password"
                                                type="password"
                                                class="checkout-input @error('password') is-invalid @enderror"
                                                autocomplete="new-password"
                                                placeholder="{{ __('site.signup.password_placeholder') }}"
                                                minlength="8"
                                                required
                                            />
                                            <small data-error-for="password">@error('password'){{ $message }}@enderror</small>
                                        </label>

                                        <label class="checkout-field-group checkout-field-group-confirmation">
                                            <span>{{ __('site.signup.password_confirm') }}</span>
                                            <input
                                                name="password_confirmation"
                                                type="password"
                                                class="checkout-input"
                                                autocomplete="new-password"
                                                placeholder="{{ __('site.signup.password_confirm_placeholder') }}"
                                                minlength="8"
                                                required
                                            />
                                            <small data-error-for="password_confirmation"></small>
                                        </label>
                                    </div>
                                @endif

                                <div class="checkout-form-actions">
                                    <span class="checkout-action-note">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path d="M7 10V8a5 5 0 0 1 10 0v2" />
                                            <rect x="5" y="10" width="14" height="10" rx="2.5" />
                                        </svg>
                                        {{ __('marketing.checkout.no_card_note') }}
                                    </span>
                                    <button
                                        type="submit"
                                        class="checkout-primary-button"
                                        data-account-submit
                                        data-loading-label="{{ __('marketing.payment.processing') }}"
                                    >
                                        <span>{{ $owner ? __('marketing.checkout.continue_existing') : __('marketing.checkout.continue_payment') }}</span>
                                        <svg class="rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                            <path d="m9 18 6-6-6-6" />
                                        </svg>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </section>

                    <section class="checkout-step" data-checkout-step="2" aria-labelledby="checkoutPaymentTitle">
                        <div class="checkout-step-content checkout-payment-content">
                            <div class="checkout-heading checkout-payment-heading">
                                <span class="checkout-kicker">{{ __('marketing.checkout.payment_eyebrow') }}</span>
                                <div class="checkout-heading-row">
                                    <div>
                                        <h1 id="checkoutPaymentTitle" tabindex="-1">{{ __('marketing.payment.title') }}</h1>
                                        <p class="checkout-heading-description">{{ __('marketing.checkout.payment_description') }}</p>
                                    </div>

                                    <div class="checkout-total-chip">
                                        <small>{{ __('marketing.checkout.transfer_amount') }}</small>
                                        <strong dir="ltr"><span data-payment-total>{{ $invoicePayload['total_display'] ?? $selectedPrice }}</span> <span data-payment-currency>{{ $invoicePayload['currency'] ?? $currency }}</span></strong>
                                    </div>
                                </div>
                            </div>

                            <form
                                id="checkoutPaymentForm"
                                action="{{ route('site.checkout.payment') }}"
                                method="post"
                                enctype="multipart/form-data"
                                class="checkout-payment-form"
                                novalidate
                            >
                                @csrf

                                <div class="checkout-form-error hidden" data-form-error role="alert"></div>

                                <div class="checkout-payment-grid">
                                    <section class="checkout-bank-card">
                                        <div class="checkout-card-heading">
                                            <span>
                                                <small>{{ __('marketing.payment.bank_info_title') }}</small>
                                                <strong>{{ $bank['bank_name'] !== '' ? $bank['bank_name'] : __('marketing.payment.not_configured') }}</strong>
                                            </span>
                                            <span class="checkout-bank-icon">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                    <path d="m3 9 9-5 9 5" />
                                                    <path d="M5 10v7m4-7v7m6-7v7m4-7v7M3 20h18" />
                                                </svg>
                                            </span>
                                        </div>

                                        @if($hasBankDetails)
                                            <dl class="checkout-bank-details">
                                                @if($bank['account_name'] !== '')
                                                    <div>
                                                        <dt>{{ __('marketing.payment.account_holder') }}</dt>
                                                        <dd>{{ $bank['account_name'] }}</dd>
                                                    </div>
                                                @endif

                                                @if($bank['account_number'] !== '')
                                                    <div class="checkout-account-number-row">
                                                        <dt>{{ __('marketing.payment.account_number') }}</dt>
                                                        <dd dir="ltr">{{ $bank['account_number'] }}</dd>
                                                        <button
                                                            type="button"
                                                            data-copy-account="{{ $bank['account_number'] }}"
                                                            aria-label="{{ __('marketing.payment.copy') }}"
                                                        >
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                                <rect x="9" y="9" width="11" height="11" rx="2" />
                                                                <path d="M5 15V5a2 2 0 0 1 2-2h10" />
                                                            </svg>
                                                            <span>{{ __('marketing.payment.copy') }}</span>
                                                        </button>
                                                    </div>
                                                @endif
                                            </dl>

                                            @if($bank['instructions'] !== '')
                                                <p class="checkout-bank-instructions" title="{{ $bank['instructions'] }}">{{ $bank['instructions'] }}</p>
                                            @endif

                                            @if($qrCode)
                                                <img src="{{ $qrCode }}" alt="{{ __('marketing.payment.account_number') }}" class="checkout-bank-qr" />
                                            @endif
                                        @else
                                            <p class="checkout-bank-warning">{{ __('marketing.payment.not_configured') }}</p>
                                        @endif
                                    </section>

                                    <section class="checkout-upload-card">
                                        <div class="checkout-card-heading">
                                            <span>
                                                <small>{{ __('marketing.payment.upload_title') }}</small>
                                                <strong>{{ __('marketing.payment.upload_hint') }}</strong>
                                            </span>
                                            <span class="checkout-upload-state" data-upload-state>{{ __('marketing.payment.upload_formats') }}</span>
                                        </div>

                                        <input
                                            id="checkoutReceipt"
                                            name="bank_transfer_receipt"
                                            type="file"
                                            accept="image/png,image/jpeg,image/gif"
                                            class="sr-only"
                                            required
                                        />

                                        <label for="checkoutReceipt" class="checkout-drop-zone" data-drop-zone>
                                            <span class="checkout-drop-icon">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                    <path d="M12 16V4m0 0 4 4m-4-4-4 4" />
                                                    <path d="M4 16v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2" />
                                                </svg>
                                            </span>
                                            <span>
                                                <strong data-upload-title>{{ __('marketing.payment.upload_hint') }}</strong>
                                                <small data-upload-file>{{ __('marketing.payment.upload_formats') }}</small>
                                            </span>
                                            <span class="checkout-drop-action">{{ __('marketing.payment.upload_title') }}</span>
                                        </label>

                                        <small class="checkout-field-error" data-error-for="bank_transfer_receipt">@error('bank_transfer_receipt'){{ $message }}@enderror</small>
                                    </section>
                                </div>

                                <div class="checkout-order-line">
                                    <span>
                                        <small>{{ __('marketing.checkout.selected_plan') }}</small>
                                        <strong data-selected-plan-name>{{ $invoicePayload['package'] ?? $selectedPackage->name }}</strong>
                                    </span>
                                    <span>
                                        <small>{{ __('marketing.checkout.invoice') }}</small>
                                        <strong dir="ltr" data-invoice-number>{{ $invoicePayload['invoice_number'] ?? '—' }}</strong>
                                    </span>
                                    <span>
                                        <small>{{ __('marketing.checkout.total') }}</small>
                                        <strong dir="ltr"><span data-payment-total>{{ $invoicePayload['total_display'] ?? $selectedPrice }}</span> <span data-payment-currency>{{ $invoicePayload['currency'] ?? $currency }}</span></strong>
                                    </span>
                                </div>

                                <div class="checkout-form-actions checkout-payment-actions">
                                    <span class="checkout-action-note">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path d="M12 8v4m0 4h.01" />
                                            <circle cx="12" cy="12" r="9" />
                                        </svg>
                                        {{ __('marketing.processing.pending') }}
                                    </span>
                                    <button
                                        type="submit"
                                        class="checkout-primary-button"
                                        data-payment-submit
                                        data-loading-label="{{ __('marketing.payment.processing') }}"
                                        disabled
                                    >
                                        <span>{{ __('marketing.checkout.complete_payment') }}</span>
                                        <svg class="rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                            <path d="m9 18 6-6-6-6" />
                                        </svg>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </section>

                    <section class="checkout-step" data-checkout-step="3" aria-labelledby="checkoutCompleteTitle">
                        <div class="checkout-complete">
                            <div class="checkout-complete-mark" aria-hidden="true">
                                <span></span>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                                    <path d="m6 12 4 4 8-9" />
                                </svg>
                            </div>
                            <span class="checkout-kicker">{{ __('marketing.checkout.complete_eyebrow') }}</span>
                            <h1 id="checkoutCompleteTitle" tabindex="-1">{{ __('marketing.checkout.complete_title') }}</h1>
                            <p>{{ __('marketing.checkout.complete_description') }}</p>

                            <div class="checkout-complete-summary">
                                <span>
                                    <small>{{ __('marketing.checkout.selected_plan') }}</small>
                                    <strong data-selected-plan-name>{{ $invoicePayload['package'] ?? $selectedPackage->name }}</strong>
                                </span>
                                <span>
                                    <small>{{ __('marketing.checkout.invoice') }}</small>
                                    <strong dir="ltr" data-invoice-number>{{ $invoicePayload['invoice_number'] ?? '—' }}</strong>
                                </span>
                                <span>
                                    <small>{{ __('marketing.processing.status') }}</small>
                                    <strong class="checkout-pending-status">
                                        <i></i>
                                        {{ __('marketing.checkout.pending_review') }}
                                    </strong>
                                </span>
                            </div>

                            <div class="checkout-complete-actions">
                                <a
                                    href="{{ $invoicePayload['dashboard_url'] ?? route('owner.dashboard') }}"
                                    class="checkout-primary-button"
                                    data-dashboard-link
                                >
                                    {{ __('marketing.checkout.open_dashboard') }}
                                    <svg class="rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path d="m9 18 6-6-6-6" />
                                    </svg>
                                </a>
                                <a href="{{ route('landing-page') }}" class="checkout-secondary-button">{{ __('marketing.checkout.return_home') }}</a>
                            </div>
                        </div>
                    </section>
                @endif
            </div>
        </section>
    </div>
</main>
@endsection

@if($selectedPackage)
    @push('scripts')
    <script>
        (function () {
            var root = document.getElementById('checkoutApp');

            if (!root) {
                return;
            }

            var config = {
                startStep: Number(root.dataset.startStep || 1),
                invoice: @json($invoicePayload),
                strings: {
                    copied: @json(__('marketing.payment.copied')),
                    copy: @json(__('marketing.payment.copy')),
                    receiptSelected: @json(__('marketing.checkout.receipt_selected')),
                    processing: @json(__('marketing.payment.processing')),
                    unknownError: @json(__('marketing.checkout.unknown_error')),
                    fileTooLarge: @json(__('marketing.checkout.file_too_large')),
                    fileType: @json(__('marketing.checkout.file_type'))
                }
            };
            var panels = Array.from(root.querySelectorAll('[data-checkout-step]'));
            var progressItems = Array.from(root.querySelectorAll('[data-progress-step]'));
            var packageSelect = document.getElementById('checkoutPackage');
            var accountForm = document.getElementById('checkoutAccountForm');
            var paymentForm = document.getElementById('checkoutPaymentForm');
            var receiptInput = document.getElementById('checkoutReceipt');
            var paymentButton = root.querySelector('[data-payment-submit]');
            var currentStep = config.startStep;

            function setText(selector, value) {
                root.querySelectorAll(selector).forEach(function (element) {
                    element.textContent = value || '—';
                });
            }

            function setStep(step, shouldFocus) {
                currentStep = Math.max(1, Math.min(3, Number(step)));
                root.dataset.currentStep = String(currentStep);

                panels.forEach(function (panel) {
                    var isActive = Number(panel.dataset.checkoutStep) === currentStep;
                    panel.classList.toggle('is-active', isActive);
                    panel.setAttribute('aria-hidden', isActive ? 'false' : 'true');
                    panel.inert = !isActive;

                    if (isActive && shouldFocus) {
                        var heading = panel.querySelector('h1');

                        if (heading) {
                            window.setTimeout(function () {
                                heading.focus({ preventScroll: true });
                            }, 240);
                        }
                    }
                });

                progressItems.forEach(function (item) {
                    var itemStep = Number(item.dataset.progressStep);
                    item.classList.toggle('is-active', itemStep === currentStep);
                    item.classList.toggle('is-complete', itemStep < currentStep);

                    if (itemStep === currentStep) {
                        item.setAttribute('aria-current', 'step');
                    } else {
                        item.removeAttribute('aria-current');
                    }
                });

                if (packageSelect) {
                    packageSelect.disabled = currentStep > 1;
                }
            }

            function selectedPlan() {
                if (!packageSelect) {
                    return null;
                }

                return packageSelect.selectedOptions[0] || null;
            }

            function updateSelectedPlan() {
                var option = selectedPlan();

                if (!option) {
                    return;
                }

                setText('[data-selected-plan-name]', option.dataset.planName);
                setText('[data-selected-plan-price]', option.dataset.planPrice);
                setText('[data-selected-plan-duration]', option.dataset.planDuration);
                setText('[data-selected-plan-boats]', option.dataset.planBoats);
                setText('[data-payment-total]', option.dataset.planPrice);
            }

            function updateInvoice(payload) {
                if (!payload) {
                    return;
                }

                setText('[data-selected-plan-name]', payload.package);
                setText('[data-selected-plan-duration]', payload.duration_label);
                setText('[data-selected-plan-boats]', payload.boats_label);
                setText('[data-payment-total]', payload.total_display);
                setText('[data-payment-currency]', payload.currency);
                setText('[data-invoice-number]', payload.invoice_number);

                root.querySelectorAll('[data-dashboard-link]').forEach(function (link) {
                    link.href = payload.dashboard_url;
                });
            }

            function clearErrors(form) {
                form.querySelectorAll('.is-invalid').forEach(function (field) {
                    field.classList.remove('is-invalid');
                    field.removeAttribute('aria-invalid');
                });
                root.querySelectorAll('[data-error-for]').forEach(function (message) {
                    message.textContent = '';
                });

                var summary = form.querySelector('[data-form-error]');

                if (summary) {
                    summary.textContent = '';
                    summary.classList.add('hidden');
                }
            }

            function showErrors(form, payload) {
                var errors = payload.errors || {};
                var firstMessage = payload.message || config.strings.unknownError;

                Object.keys(errors).forEach(function (name) {
                    var messages = errors[name];
                    var field = form.elements.namedItem(name) || root.querySelector('[name="' + name + '"]');
                    var message = root.querySelector('[data-error-for="' + name + '"]');

                    if (field && field.classList) {
                        field.classList.add('is-invalid');
                        field.setAttribute('aria-invalid', 'true');
                    }

                    if (message) {
                        message.textContent = messages[0] || '';
                    }

                });

                var summary = form.querySelector('[data-form-error]');

                if (summary) {
                    summary.textContent = firstMessage;
                    summary.classList.remove('hidden');
                }
            }

            function setButtonLoading(button, loading) {
                if (!button) {
                    return;
                }

                var label = button.querySelector('span');

                if (!button.dataset.originalLabel && label) {
                    button.dataset.originalLabel = label.textContent;
                }

                button.classList.toggle('is-loading', loading);
                button.disabled = loading;

                if (label) {
                    label.textContent = loading
                        ? button.dataset.loadingLabel || config.strings.processing
                        : button.dataset.originalLabel;
                }
            }

            function csrfToken() {
                return document.querySelector('meta[name="csrf-token"]')?.content
                    || root.querySelector('input[name="_token"]')?.value
                    || '';
            }

            function updateCsrfToken(token) {
                if (!token) {
                    return;
                }

                document.querySelectorAll('meta[name="csrf-token"]').forEach(function (meta) {
                    meta.content = token;
                });

                root.querySelectorAll('input[name="_token"]').forEach(function (input) {
                    input.value = token;
                });
            }

            async function refreshCsrfToken() {
                var response = await fetch(window.location.href, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                    cache: 'no-store'
                });

                if (!response.ok) {
                    return false;
                }

                var documentFragment = new DOMParser().parseFromString(await response.text(), 'text/html');
                var token = documentFragment.querySelector('input[name="_token"]')?.value;

                if (!token) {
                    return false;
                }

                updateCsrfToken(token);

                return true;
            }

            async function sendForm(form) {
                return fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken(),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });
            }

            async function submitForm(form, button, onSuccess) {
                clearErrors(form);
                setButtonLoading(button, true);

                try {
                    var response = await sendForm(form);

                    if (response.status === 419 && await refreshCsrfToken()) {
                        response = await sendForm(form);
                    }

                    var payload = await response.json().catch(function () {
                        return {};
                    });

                    if (!response.ok) {
                        showErrors(form, payload);
                        return;
                    }

                    onSuccess(payload);
                } catch (error) {
                    showErrors(form, { message: config.strings.unknownError });
                } finally {
                    setButtonLoading(button, false);
                }
            }

            if (packageSelect) {
                packageSelect.addEventListener('change', updateSelectedPlan);
                updateSelectedPlan();
            }

            if (accountForm) {
                accountForm.addEventListener('submit', function (event) {
                    event.preventDefault();
                    var button = accountForm.querySelector('[data-account-submit]');

                    submitForm(accountForm, button, function (payload) {
                        config.invoice = payload;
                        updateInvoice(payload);
                        window.history.replaceState({}, '', @json(route('site.checkout')));
                        setStep(2, true);
                    });
                });
            }

            function showReceiptError(message) {
                var error = root.querySelector('[data-error-for="bank_transfer_receipt"]');

                if (error) {
                    error.textContent = message;
                }

                if (paymentButton) {
                    paymentButton.disabled = true;
                }
            }

            function acceptReceipt(file) {
                var allowedTypes = ['image/png', 'image/jpeg', 'image/gif'];

                if (!allowedTypes.includes(file.type)) {
                    receiptInput.value = '';
                    showReceiptError(config.strings.fileType);
                    return;
                }

                if (file.size > 5 * 1024 * 1024) {
                    receiptInput.value = '';
                    showReceiptError(config.strings.fileTooLarge);
                    return;
                }

                var error = root.querySelector('[data-error-for="bank_transfer_receipt"]');
                var dropZone = root.querySelector('[data-drop-zone]');
                var uploadTitle = root.querySelector('[data-upload-title]');
                var uploadFile = root.querySelector('[data-upload-file]');
                var uploadState = root.querySelector('[data-upload-state]');

                if (error) {
                    error.textContent = '';
                }

                if (dropZone) {
                    dropZone.classList.add('has-file');
                }

                if (uploadTitle) {
                    uploadTitle.textContent = config.strings.receiptSelected;
                }

                if (uploadFile) {
                    uploadFile.textContent = file.name;
                }

                if (uploadState) {
                    uploadState.textContent = Math.max(1, Math.round(file.size / 1024)) + ' KB';
                }

                if (paymentButton) {
                    paymentButton.disabled = false;
                }
            }

            if (receiptInput) {
                receiptInput.addEventListener('change', function () {
                    if (receiptInput.files && receiptInput.files[0]) {
                        acceptReceipt(receiptInput.files[0]);
                    }
                });
            }

            var dropZone = root.querySelector('[data-drop-zone]');

            if (dropZone && receiptInput) {
                ['dragenter', 'dragover'].forEach(function (eventName) {
                    dropZone.addEventListener(eventName, function (event) {
                        event.preventDefault();
                        dropZone.classList.add('is-dragging');
                    });
                });

                ['dragleave', 'drop'].forEach(function (eventName) {
                    dropZone.addEventListener(eventName, function (event) {
                        event.preventDefault();
                        dropZone.classList.remove('is-dragging');
                    });
                });

                dropZone.addEventListener('drop', function (event) {
                    var file = event.dataTransfer.files && event.dataTransfer.files[0];

                    if (!file) {
                        return;
                    }

                    try {
                        var transfer = new DataTransfer();
                        transfer.items.add(file);
                        receiptInput.files = transfer.files;
                    } catch (error) {
                        return;
                    }

                    acceptReceipt(file);
                });
            }

            if (paymentForm) {
                paymentForm.addEventListener('submit', function (event) {
                    event.preventDefault();

                    if (!receiptInput.files || !receiptInput.files[0]) {
                        showReceiptError(@json(__('marketing.payment.receipt_required')));
                        return;
                    }

                    submitForm(paymentForm, paymentButton, function (payload) {
                        config.invoice = payload;
                        updateInvoice(payload);
                        setStep(3, true);
                    });
                });
            }

            root.querySelectorAll('[data-copy-account]').forEach(function (button) {
                button.addEventListener('click', async function () {
                    var label = button.querySelector('span');

                    try {
                        await navigator.clipboard.writeText(button.dataset.copyAccount);
                        label.textContent = config.strings.copied;
                        window.setTimeout(function () {
                            label.textContent = config.strings.copy;
                        }, 1400);
                    } catch (error) {
                        label.textContent = button.dataset.copyAccount;
                    }
                });
            });

            updateInvoice(config.invoice);
            setStep(config.startStep, false);
        })();
    </script>
    @endpush
@endif
