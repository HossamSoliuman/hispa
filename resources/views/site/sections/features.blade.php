<section id="features" class="public-site-section scroll-mt-20 py-12 md:py-14">
    <div class="site-shell">
        <div class="max-w-3xl text-start">
            <span class="eyebrow text-ocean">{{ __('marketing.features.eyebrow') }}</span>
            <h2 class="mt-4 text-3xl font-bold leading-tight tracking-[-0.035em] text-ink md:text-5xl">{{ __('marketing.features.title') }}</h2>
            <p class="mt-4 max-w-2xl text-base leading-8 text-ink/55">{{ __('marketing.features.description') }}</p>
        </div>

        <div class="mt-8 grid gap-5 md:grid-cols-2 xl:grid-cols-4">
            @foreach([
                ['number' => '01', 'title' => __('marketing.features.trip_title'), 'description' => __('marketing.features.trip_desc'), 'icon' => 'M4 19V5m4 14v-6m4 6V9m4 10v-8m4 8v-3'],
                ['number' => '02', 'title' => __('marketing.features.money_title'), 'description' => __('marketing.features.money_desc'), 'icon' => 'M4 7h16v10H4zM8 12h.01M16 12h.01M12 10v4'],
                ['number' => '03', 'title' => __('marketing.features.fleet_title'), 'description' => __('marketing.features.fleet_desc'), 'icon' => 'M3 17h18l-2 4H5l-2-4Zm3 0V9l6-4 6 4v8M9 12h6'],
                ['number' => '04', 'title' => __('marketing.features.reports_title'), 'description' => __('marketing.features.reports_desc'), 'icon' => 'M5 19V9m7 10V5m7 14v-7'],
            ] as $feature)
                <article class="hud-panel flex min-h-56 flex-col p-6 text-start">
                    <div class="flex items-center justify-between gap-4">
                        <span class="grid h-11 w-11 place-items-center border border-ocean/25 bg-ocean/[0.07] text-ocean">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $feature['icon'] }}" /></svg>
                        </span>
                        <span class="text-xs font-bold text-ocean/55">{{ $feature['number'] }}</span>
                    </div>
                    <div class="mt-9">
                        <h3 class="text-xl font-bold leading-8 text-ink">{{ $feature['title'] }}</h3>
                        <p class="mt-2 text-sm leading-7 text-ink/52">{{ $feature['description'] }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
