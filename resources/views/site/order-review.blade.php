@extends('site.layouts.app')

@section('title', __('site.order_review.title') . ' - ' . __('site.meta.title'))
@section('description', __('site.order_review.description'))
@section('robots', 'noindex, nofollow')

@php
    $selectedPackage = $subscriptionPackages->firstWhere('id', (int) request('package_id')) ?? $subscriptionPackages->first();
    $price = $selectedPackage ? (float) $selectedPackage->effective_price : 0;
@endphp

@section('content')
<main class="bg-transparent py-8 md:py-10">
    <div class="site-shell">
        <div class="mx-auto max-w-3xl">
            <div class="grid grid-cols-[1fr_auto_1fr_auto_1fr] items-start gap-2" aria-label="{{ __('marketing.checkout.step_label', ['current' => 1]) }}">
                @foreach([
                    ['number' => 1, 'label' => __('marketing.checkout.steps.plan'), 'active' => true],
                    ['number' => 2, 'label' => __('marketing.checkout.steps.payment'), 'active' => false],
                    ['number' => 3, 'label' => __('marketing.checkout.steps.dashboard'), 'active' => false],
                ] as $step)
                    <div class="flex flex-col items-center text-center">
                        <span @class([
                            'grid h-9 w-9 place-items-center rounded-full text-xs font-extrabold',
                            'bg-ink text-white shadow-lg' => $step['active'],
                            'border border-ink/15 bg-white text-ink/38' => ! $step['active'],
                        ])>{{ $step['number'] }}</span>
                        <span @class(['mt-2 text-[0.64rem] font-bold sm:text-xs', 'text-ink' => $step['active'], 'text-ink/38' => ! $step['active']])>{{ $step['label'] }}</span>
                    </div>
                    @if(! $loop->last)
                        <span @class(['mt-[1.1rem] h-px w-full', 'bg-ocean' => $step['active'], 'bg-ink/12' => ! $step['active']])></span>
                    @endif
                @endforeach
            </div>
        </div>

        <div class="mx-auto mt-8 max-w-5xl">
            <div class="text-center">
                <span class="eyebrow text-ocean">{{ __('marketing.checkout.step_label', ['current' => 1]) }}</span>
                <h1 class="mt-3 text-2xl font-extrabold tracking-[-0.04em] text-ink md:text-4xl">{{ __('marketing.checkout.review_title') }}</h1>
            </div>

            @if($selectedPackage)
                <div class="mt-8 grid items-start gap-6 lg:grid-cols-[1.15fr_.85fr]">
                    <section class="hud-panel p-6 sm:p-7">
                        <div class="flex items-start justify-between gap-5 border-b border-ink/8 pb-5">
                            <div class="text-start">
                                <p class="text-xs font-bold text-ocean">{{ __('marketing.checkout.selected_plan') }}</p>
                                <h2 class="mt-2 text-2xl font-extrabold text-ink">{{ $selectedPackage->name }}</h2>
                                <p class="mt-2 text-sm text-ink/48">{{ $selectedPackage->boatsLabel() }} · {{ __('site.pricing.durations.' . $selectedPackage->duration_type) }}</p>
                            </div>
                            <a href="{{ route('landing-page') }}#pricing" class="shrink-0 text-xs font-bold text-ocean underline decoration-ocean/25 underline-offset-4 hover:text-ocean-deep">{{ __('marketing.checkout.change_plan') }}</a>
                        </div>

                        <div class="mt-5 flex items-start gap-3 rounded-xl border border-ocean/14 bg-mist/65 p-4 text-start">
                            <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-ocean text-white">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 8v4m0 4h.01" /><circle cx="12" cy="12" r="9" /></svg>
                            </span>
                            <div>
                                <h3 class="text-sm font-extrabold text-ink">{{ __('marketing.checkout.activation_title') }}</h3>
                                <p class="mt-1.5 text-xs leading-6 text-ink/58">{{ __('marketing.checkout.activation_description') }}</p>
                            </div>
                        </div>
                    </section>

                    <aside class="checkout-summary p-6 text-white sm:p-7 lg:sticky lg:top-28">
                        <p class="text-xs font-bold text-white/72">{{ __('marketing.checkout.selected_plan') }}</p>
                        <h2 class="mt-2 text-xl font-extrabold">{{ $selectedPackage->name }}</h2>

                        <dl class="mt-6 grid gap-4 text-sm">
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-white/52">{{ __('marketing.checkout.price') }}</dt>
                                <dd class="font-bold" dir="ltr">{{ number_format($price, 0) }} {{ __('site.pricing.currency') }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-white/52">{{ __('marketing.checkout.fees') }}</dt>
                                <dd class="font-bold text-[#6ed0bd]">{{ __('marketing.checkout.free') }}</dd>
                            </div>
                        </dl>

                        <div class="my-5 h-px bg-white/12"></div>

                        <div class="flex items-end justify-between gap-4">
                            <span class="text-sm font-bold text-white/72">{{ __('marketing.checkout.total') }}</span>
                            <div class="text-end">
                                <strong class="text-3xl font-extrabold" dir="ltr">{{ number_format($price, 0) }}</strong>
                                <span class="block text-[0.65rem] text-white/45">{{ __('site.pricing.per.' . $selectedPackage->duration_type) }}</span>
                            </div>
                        </div>

                        <a href="{{ route('frontend.show_register_form', ['package_id' => $selectedPackage->id, 'guard' => 'owner']) }}" id="submitBtn" class="checkout-summary-action mt-6 inline-flex min-h-13 w-full items-center justify-center gap-2 rounded-xl px-5 py-3.5 text-center text-sm font-extrabold hover:-translate-y-0.5">
                            {{ __('marketing.checkout.continue') }}
                            <svg class="h-4 w-4 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m9 18 6-6-6-6" /></svg>
                        </a>

                        <div class="mt-5 border-t border-white/10 pt-4 text-center">
                            <a href="{{ route('landing-page') }}#contact" class="text-xs font-bold text-white/70 underline decoration-white/25 underline-offset-4 hover:text-white">{{ __('marketing.checkout.contact') }}</a>
                        </div>
                    </aside>
                </div>
            @else
                <div class="price-card mx-auto mt-8 max-w-xl p-8 text-center">
                    <h2 class="text-xl font-extrabold text-ink">{{ __('marketing.pricing.empty_title') }}</h2>
                    <p class="mt-2 text-sm leading-7 text-ink/55">{{ __('marketing.pricing.empty_description') }}</p>
                    <a href="{{ route('landing-page') }}#contact" class="mt-6 inline-flex bg-ocean px-5 py-3 text-sm font-bold text-white">{{ __('site.pricing.contact_us') }}</a>
                </div>
            @endif
        </div>
    </div>
</main>
@endsection
