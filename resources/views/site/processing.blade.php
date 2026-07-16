@extends('site.layouts.app')

@section('title', __('site.processing.title') . ' - ' . __('site.meta.title'))

@section('content')
@php
    $pending = session('pending_subscription');
@endphp

<main class="bg-transparent py-14 md:py-20">
    <div class="site-shell">
        <div class="mx-auto max-w-4xl">
            <div class="grid grid-cols-[1fr_auto_1fr_auto_1fr] items-start gap-2" aria-label="{{ __('marketing.checkout.step_label', ['current' => 3]) }}">
                @foreach([
                    ['number' => 1, 'label' => __('marketing.checkout.steps.plan')],
                    ['number' => 2, 'label' => __('marketing.checkout.steps.account')],
                    ['number' => 3, 'label' => __('marketing.checkout.steps.activation')],
                ] as $step)
                    <div class="flex flex-col items-center text-center">
                        <span class="grid h-9 w-9 place-items-center rounded-full bg-tide text-white">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M5 12l4 4L19 6" /></svg>
                        </span>
                        <span @class(['mt-2 text-[0.62rem] font-bold sm:text-xs', 'text-ink' => $loop->last, 'text-ink/42' => ! $loop->last])>{{ $step['label'] }}</span>
                    </div>
                    @if(! $loop->last)
                        <span class="mt-[1.1rem] h-px w-full bg-tide"></span>
                    @endif
                @endforeach
            </div>
        </div>

        <div class="hud-panel mx-auto mt-12 max-w-4xl overflow-hidden bg-white/90">
            <div class="bg-ocean px-6 py-10 text-center text-white sm:px-10 sm:py-12">
                <span class="mx-auto grid h-14 w-14 place-items-center rounded-full bg-tide text-white shadow-[0_0_0_10px_rgba(79,196,159,.1)]">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M5 12l4 4L19 6" /></svg>
                </span>
                <p class="mt-7 text-xs font-bold text-[#9ee8d3]">{{ __('marketing.processing.eyebrow') }}</p>
                <h1 class="mx-auto mt-3 max-w-2xl text-2xl font-extrabold leading-tight tracking-[-0.03em] sm:text-4xl">{{ __('marketing.processing.title') }}</h1>
                <p class="mx-auto mt-4 max-w-2xl text-sm leading-7 text-white/58">{{ __('marketing.processing.description') }}</p>
            </div>

            <div class="p-6 sm:p-10">
                @if($pending)
                    <dl class="grid gap-px overflow-hidden rounded-2xl border border-ink/8 bg-ink/8 sm:grid-cols-2 lg:grid-cols-4">
                        @if(! empty($pending['invoice_number']))
                            <div class="bg-white p-5 text-start">
                                <dt class="text-[0.65rem] font-bold text-ink/42">{{ __('site.processing.invoice_number') }}</dt>
                                <dd class="mt-2 text-sm font-extrabold text-ocean" dir="ltr">{{ $pending['invoice_number'] }}</dd>
                            </div>
                        @endif
                        <div class="bg-white p-5 text-start">
                            <dt class="text-[0.65rem] font-bold text-ink/42">{{ __('site.processing.plan') }}</dt>
                            <dd class="mt-2 text-sm font-extrabold text-ink">{{ $pending['package'] ?? '—' }}</dd>
                        </div>
                        @if(! empty($pending['duration']))
                            <div class="bg-white p-5 text-start">
                                <dt class="text-[0.65rem] font-bold text-ink/42">{{ __('site.processing.duration') }}</dt>
                                <dd class="mt-2 text-sm font-extrabold text-ink">{{ __('site.order_review.durations.' . $pending['duration']) }}</dd>
                            </div>
                        @endif
                        <div class="bg-white p-5 text-start">
                            <dt class="text-[0.65rem] font-bold text-ink/42">{{ __('marketing.processing.status') }}</dt>
                            <dd class="mt-2 inline-flex items-center gap-2 text-sm font-extrabold text-tide"><span class="h-2 w-2 rounded-full bg-tide"></span>{{ __('marketing.processing.pending') }}</dd>
                        </div>
                    </dl>
                @endif

                <div class="mt-7 flex items-start gap-4 rounded-2xl bg-mist/70 p-5 text-start">
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-ocean text-white">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 19V5m4 14v-6m4 6V9m4 10v-8m4 8v-3" /></svg>
                    </span>
                    <div>
                        <h2 class="text-sm font-extrabold text-ink">{{ __('marketing.processing.next_title') }}</h2>
                        <p class="mt-2 text-xs leading-6 text-ink/58">{{ __('marketing.processing.next_description') }}</p>
                    </div>
                </div>

                <div class="mt-7 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('owner.dashboard') }}" class="inline-flex min-h-12 flex-1 items-center justify-center gap-2 bg-ocean px-5 py-3 text-sm font-bold text-white hover:-translate-y-0.5 hover:bg-ocean-deep">
                        {{ __('site.processing.go_dashboard') }}
                        <svg class="h-4 w-4 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m9 18 6-6-6-6" /></svg>
                    </a>
                    <a href="{{ route('landing-page') }}" class="inline-flex min-h-12 flex-1 items-center justify-center rounded-xl border border-ink/12 bg-white px-5 py-3 text-sm font-bold text-ink hover:border-ocean/35 hover:text-ocean">{{ __('site.processing.home') }}</a>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
