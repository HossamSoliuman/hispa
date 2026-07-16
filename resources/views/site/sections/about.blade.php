<section id="about" class="public-site-section scroll-mt-20 border-t border-ocean/15 py-12 md:py-14">
    <div class="site-shell">
        <div class="mx-auto max-w-3xl text-center">
            <span class="eyebrow text-ocean">{{ __('site.about.heading') }}</span>
            <h2 class="mt-4 text-3xl font-bold leading-tight tracking-[-0.035em] text-ink md:text-5xl">
                {{ __('site.about.block1_title') }}
                <span class="text-ocean">{{ __('site.about.block1_title_highlight') }}</span>
            </h2>
        </div>

        <div class="mt-8 grid gap-5 lg:grid-cols-2">
            <article class="hud-panel grid items-center gap-6 p-5 text-start sm:p-7 md:grid-cols-[0.8fr_1.2fr]">
                <div class="about-media overflow-hidden border border-ocean/15 bg-white/55 p-2">
                    <img src="{{ asset('site/assets/hesba1.png') }}" alt="{{ __('site.meta.title') }}" class="aspect-[4/3] h-full w-full object-cover" draggable="false" />
                </div>
                <div>
                    <h3 class="text-xl font-bold leading-8 text-ink">{{ __('site.about.block1_title') }}</h3>
                    <p class="mt-3 text-sm leading-7 text-ink/65">{{ __('site.about.block1_p1') }}</p>
                    <p class="mt-2 text-sm leading-7 text-ink/45">{{ __('site.about.block1_p2') }}</p>
                    <a href="#features" class="mt-5 inline-flex items-center gap-2 text-sm font-bold text-ocean hover:text-ocean-deep">
                        {{ __('site.about.learn_more') }}
                        <svg class="h-4 w-4 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m9 18 6-6-6-6" /></svg>
                    </a>
                </div>
            </article>

            <article class="hud-panel grid items-center gap-6 p-5 text-start sm:p-7 md:grid-cols-[0.8fr_1.2fr]">
                <div class="about-media overflow-hidden border border-ocean/15 bg-white/55 p-2">
                    <img src="{{ asset('site/assets/hesba2.png') }}" alt="{{ __('site.meta.title') }}" class="aspect-[4/3] h-full w-full object-cover" draggable="false" />
                </div>
                <div>
                    <h3 class="text-xl font-bold leading-8 text-ink">{{ __('site.about.block2_title') }}</h3>
                    <p class="mt-3 text-sm leading-7 text-ink/65">{{ __('site.about.block2_p1') }}</p>
                    <p class="mt-2 text-sm leading-7 text-ink/45">{{ __('site.about.block2_p2') }}</p>
                    <a href="#pricing" class="mt-5 inline-flex items-center gap-2 text-sm font-bold text-ocean hover:text-ocean-deep">
                        {{ __('site.about.learn_more') }}
                        <svg class="h-4 w-4 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m9 18 6-6-6-6" /></svg>
                    </a>
                </div>
            </article>
        </div>
    </div>
</section>
