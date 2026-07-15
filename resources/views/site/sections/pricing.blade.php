<section id="pricing" class="border-t border-ocean/15 bg-white/60 py-28 backdrop-blur-[2px] md:py-36">
    <div class="site-shell">
        <div class="mx-auto max-w-3xl text-center">
            <span class="eyebrow text-ocean">{{ __('marketing.pricing.eyebrow') }}</span>
            <h2 class="mt-6 text-3xl font-bold leading-tight tracking-[-0.04em] text-ink md:text-5xl">{{ __('marketing.pricing.title') }}</h2>
            <p class="mx-auto mt-6 max-w-xl text-base leading-8 text-ink/55">{{ __('marketing.pricing.description') }}</p>
        </div>

        <div class="mt-16 grid items-start gap-8 lg:grid-cols-[0.68fr_1.32fr]">
            <aside class="hud-panel bg-mist/80 p-7 text-start sm:p-9">
                <div class="flex items-center gap-3">
                    <span class="grid h-10 w-10 place-items-center border border-ocean/25 bg-ocean text-white">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M5 12l4 4L19 6" /></svg>
                    </span>
                    <h3 class="text-lg font-bold text-ink">{{ __('marketing.pricing.included_title') }}</h3>
                </div>
                <ul class="mt-9 grid gap-5 sm:grid-cols-2 lg:grid-cols-1">
                    @foreach(trans('marketing.pricing.included') as $item)
                        <li class="flex items-start gap-3 text-sm font-medium leading-6 text-ink/68">
                            <svg class="mt-1 h-4 w-4 shrink-0 text-ocean" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M5 12l4 4L19 6" /></svg>
                            <span>{{ $item }}</span>
                        </li>
                    @endforeach
                </ul>
            </aside>

            <div @class(['grid gap-5', 'md:grid-cols-2' => ($subscriptionPackages ?? collect())->count() > 1])>
                @forelse($subscriptionPackages ?? [] as $package)
                    <article @class([
                        'price-card relative flex h-full flex-col overflow-hidden p-7 text-start sm:p-9',
                        'ring-2 ring-ocean' => $package->is_featured,
                    ])>
                        @if($package->is_featured || $loop->first)
                            <span class="absolute end-5 top-5 border border-sand/40 bg-sand/15 px-3 py-1.5 text-[0.65rem] font-bold text-[#825013]">{{ __('marketing.pricing.recommended') }}</span>
                        @endif

                        <div class="pe-24">
                            <p class="text-xs font-bold text-ocean">{{ __('marketing.pricing.boats_prefix') }}</p>
                            <h3 class="mt-2 text-2xl font-bold text-ink">{{ $package->name }}</h3>
                            <p class="mt-2 text-sm text-ink/48">{{ $package->boatsLabel() }} · {{ __('site.pricing.durations.' . $package->duration_type) }}</p>
                        </div>

                        <div class="mt-8 border-y border-ink/8 py-6">
                            @if($package->original_price !== null || $package->price !== null)
                                @if($package->hasOfferPrice())
                                    <p class="text-xs text-ink/38">
                                        <span class="line-through">{{ number_format((float) $package->original_price, 0) }} {{ __('site.pricing.currency') }}</span>
                                    </p>
                                @endif
                                <div class="mt-1 flex items-baseline gap-2">
                                    <span class="text-4xl font-bold tracking-[-0.04em] text-ink" dir="ltr">{{ number_format((float) $package->effective_price, 0) }}</span>
                                    <span class="text-xs font-bold text-ink/50">{{ __('site.pricing.per.' . $package->duration_type) }}</span>
                                </div>
                            @else
                                <p class="text-xl font-bold text-ink">{{ __('site.pricing.custom_price') }}</p>
                            @endif
                        </div>

                        <div class="mt-5 grid gap-3 text-xs font-medium text-ink/58 sm:grid-cols-2">
                            <p class="flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-tide"></span>{{ __('marketing.pricing.no_transaction_fee') }}</p>
                            <p class="flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-tide"></span>{{ __('marketing.pricing.activation') }}</p>
                        </div>

                        <div class="mt-auto pt-7">
                            @if($package->original_price !== null || $package->price !== null)
                                <a href="{{ route('site.order-review', ['package_id' => $package->id]) }}" class="inline-flex min-h-12 w-full items-center justify-center gap-2 bg-ocean px-5 py-3 text-sm font-bold text-white shadow-[0_10px_25px_rgba(54,117,194,.14)] hover:-translate-y-0.5 hover:bg-ocean-deep" data-plan="{{ $package->id }}">
                                    {{ __('marketing.pricing.choose') }}
                                    <svg class="h-4 w-4 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m9 18 6-6-6-6" /></svg>
                                </a>
                            @else
                                <a href="{{ route('site.contact') }}" class="inline-flex min-h-12 w-full items-center justify-center border border-ocean/25 bg-white px-5 py-3 text-sm font-bold text-ocean hover:border-ocean">{{ __('site.pricing.contact_us') }}</a>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="price-card p-8 text-center">
                        <span class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-mist text-ocean">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 8v4m0 4h.01M4.9 19h14.2a2 2 0 0 0 1.73-3L13.73 4a2 2 0 0 0-3.46 0L3.17 16a2 2 0 0 0 1.73 3Z" /></svg>
                        </span>
                        <h3 class="mt-5 text-xl font-bold text-ink">{{ __('marketing.pricing.empty_title') }}</h3>
                        <p class="mt-2 text-sm leading-7 text-ink/55">{{ __('marketing.pricing.empty_description') }}</p>
                        <a href="{{ route('site.contact') }}" class="mt-6 inline-flex bg-ocean px-5 py-3 text-sm font-bold text-white">{{ __('site.pricing.contact_us') }}</a>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</section>
