@extends('site.layouts.app')

@section('title', __('site.order_review.title') . ' - ' . __('site.meta.title'))
@section('description', __('site.order_review.description'))

@php
    $selectedPackage = $subscriptionPackages->firstWhere('id', (int) request('package_id')) ?? $subscriptionPackages->first();
    $price = $selectedPackage ? (float) $selectedPackage->effective_price : 0;
@endphp

@section('content')
<main class="bg-transparent py-14 md:py-20">
    <div class="site-shell">
        <div class="mx-auto max-w-4xl">
            <div class="grid grid-cols-[1fr_auto_1fr_auto_1fr] items-start gap-2" aria-label="{{ __('marketing.checkout.step_label', ['current' => 1]) }}">
                @foreach([
                    ['number' => 1, 'label' => __('marketing.checkout.steps.plan'), 'active' => true],
                    ['number' => 2, 'label' => __('marketing.checkout.steps.account'), 'active' => false],
                    ['number' => 3, 'label' => __('marketing.checkout.steps.activation'), 'active' => false],
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

        <div class="mx-auto mt-12 max-w-5xl">
            <div class="text-center">
                <span class="eyebrow text-ocean">{{ __('marketing.checkout.step_label', ['current' => 1]) }}</span>
                <h1 class="mt-4 text-3xl font-extrabold tracking-[-0.04em] text-ink md:text-5xl">{{ __('marketing.checkout.review_title') }}</h1>
                <p class="mx-auto mt-4 max-w-2xl text-sm leading-7 text-ink/55">{{ __('marketing.checkout.review_description') }}</p>
            </div>

            @if($selectedPackage)
                <div class="mt-10 grid items-start gap-6 lg:grid-cols-[1.15fr_.85fr]">
                    <section class="hud-panel p-6 sm:p-8">
                        <div class="flex flex-col gap-5 border-b border-ink/8 pb-7 sm:flex-row sm:items-start sm:justify-between">
                            <div class="text-start">
                                <p class="text-xs font-bold text-ocean">{{ __('marketing.checkout.selected_plan') }}</p>
                                <h2 class="mt-2 text-2xl font-extrabold text-ink">{{ $selectedPackage->name }}</h2>
                                <p class="mt-2 text-sm text-ink/48">{{ $selectedPackage->boatsLabel() }} · {{ __('site.pricing.durations.' . $selectedPackage->duration_type) }}</p>
                            </div>
                            <a href="{{ route('site.pricing') }}" class="inline-flex items-center gap-1 text-xs font-bold text-ocean underline decoration-ocean/25 underline-offset-4 hover:text-ocean-deep">{{ __('marketing.checkout.change_plan') }}</a>
                        </div>

                        <div class="py-7">
                            <h3 class="text-base font-extrabold text-ink">{{ __('marketing.checkout.included_title') }}</h3>
                            <ul class="mt-5 grid gap-4 sm:grid-cols-2">
                                @foreach(trans('marketing.pricing.included') as $item)
                                    <li class="flex items-start gap-3 text-sm leading-6 text-ink/62">
                                        <span class="mt-1 grid h-4 w-4 shrink-0 place-items-center rounded-full bg-tide/14 text-ocean">
                                            <svg class="h-2.5 w-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true"><path d="M5 12l4 4L19 6" /></svg>
                                        </span>
                                        <span>{{ $item }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="rounded-2xl border border-ocean/14 bg-mist/65 p-5 text-start">
                            <div class="flex items-start gap-3">
                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-ocean text-white">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 8v4m0 4h.01" /><circle cx="12" cy="12" r="9" /></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-extrabold text-ink">{{ __('marketing.checkout.activation_title') }}</h3>
                                    <p class="mt-2 text-xs leading-6 text-ink/58">{{ __('marketing.checkout.activation_description') }}</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <aside class="checkout-summary p-6 text-white sm:p-8 lg:sticky lg:top-28">
                        <p class="text-xs font-bold text-white/72">{{ __('marketing.checkout.selected_plan') }}</p>
                        <h2 class="mt-2 text-xl font-extrabold">{{ $selectedPackage->name }}</h2>

                        <dl class="mt-7 grid gap-4 text-sm">
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-white/52">{{ __('marketing.checkout.price') }}</dt>
                                <dd class="font-bold" dir="ltr">{{ number_format($price, 0) }} {{ __('site.pricing.currency') }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-white/52">{{ __('marketing.checkout.fees') }}</dt>
                                <dd class="font-bold text-[#6ed0bd]">{{ __('marketing.checkout.free') }}</dd>
                            </div>
                        </dl>

                        <div class="my-6 h-px bg-white/12"></div>

                        <div class="flex items-end justify-between gap-4">
                            <span class="text-sm font-bold text-white/72">{{ __('marketing.checkout.total') }}</span>
                            <div class="text-end">
                                <strong class="text-3xl font-extrabold" dir="ltr">{{ number_format($price, 0) }}</strong>
                                <span class="block text-[0.65rem] text-white/45">{{ __('site.pricing.per.' . $selectedPackage->duration_type) }}</span>
                            </div>
                        </div>

                        <a href="{{ route('frontend.show_register_form', ['package_id' => $selectedPackage->id, 'guard' => 'owner']) }}" id="submitBtn" class="checkout-summary-action mt-7 inline-flex min-h-13 w-full items-center justify-center gap-2 rounded-xl px-5 py-3.5 text-center text-sm font-extrabold hover:-translate-y-0.5">
                            {{ __('marketing.checkout.continue') }}
                            <svg class="h-4 w-4 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m9 18 6-6-6-6" /></svg>
                        </a>

                        <div class="mt-6 border-t border-white/10 pt-5 text-center">
                            <p class="text-xs text-white/45">{{ __('marketing.checkout.help') }}</p>
                            <a href="{{ route('site.contact') }}" class="mt-1 inline-flex text-xs font-bold text-white underline decoration-white/25 underline-offset-4 hover:text-white/70">{{ __('marketing.checkout.contact') }}</a>
                        </div>
                    </aside>
                </div>
            @else
                <div class="price-card mx-auto mt-10 max-w-xl p-8 text-center">
                    <h2 class="text-xl font-extrabold text-ink">{{ __('marketing.pricing.empty_title') }}</h2>
                    <p class="mt-2 text-sm leading-7 text-ink/55">{{ __('marketing.pricing.empty_description') }}</p>
                    <a href="{{ route('site.contact') }}" class="mt-6 inline-flex bg-ocean px-5 py-3 text-sm font-bold text-white">{{ __('site.pricing.contact_us') }}</a>
                </div>
            @endif
        </div>
    </div>
</main>
@endsection
