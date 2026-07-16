<section id="home" class="public-site-section relative isolate scroll-mt-20 overflow-hidden border-b border-ocean/20 bg-white/45">
    <div class="pointer-events-none absolute inset-y-0 end-0 hidden w-1/3 bg-ocean/[0.045] lg:block"></div>
    <div class="pointer-events-none absolute end-[8%] top-24 h-56 w-56 rounded-full bg-ocean/10 blur-3xl"></div>

    <div class="site-shell relative z-10 grid items-center gap-10 py-14 lg:grid-cols-[0.78fr_1.22fr] lg:py-16 xl:gap-14">
        <div class="hero-rise text-start">
            <span class="eyebrow text-ocean">{{ __('marketing.hero.eyebrow') }}</span>
            <h1 class="mt-5 max-w-xl text-[clamp(2.5rem,5.5vw,4.7rem)] font-bold leading-[1.08] tracking-[-0.045em] text-ink">
                {{ __('marketing.hero.title') }}
            </h1>
            <p class="mt-5 max-w-lg text-base leading-8 text-ink/62 md:text-lg">
                {{ __('marketing.hero.description') }}
            </p>

            <div class="mt-7 flex flex-col gap-3 sm:flex-row sm:items-center">
                <a href="#pricing" class="inline-flex min-h-12 items-center justify-center gap-2 bg-ocean px-6 py-3 text-sm font-bold text-white shadow-[0_10px_24px_rgba(54,117,194,.18)] hover:-translate-y-0.5 hover:bg-ocean-deep">
                    {{ __('marketing.hero.primary_cta') }}
                    <svg class="h-4 w-4 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m9 18 6-6-6-6" /></svg>
                </a>
                <a href="#features" class="hero-secondary-action inline-flex min-h-12 items-center justify-center border border-ocean/30 bg-white/75 px-6 py-3 text-sm font-semibold text-ocean hover:border-ocean hover:bg-white">
                    {{ __('marketing.hero.secondary_cta') }}
                </a>
            </div>

            <p class="mt-5 flex items-start gap-2 text-xs leading-6 text-ink/48">
                <svg class="mt-1 h-4 w-4 shrink-0 text-tide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m5 12 4 4L19 6" /></svg>
                {{ __('marketing.hero.note') }}
            </p>
        </div>

        <figure class="hero-rise-delayed relative mx-auto w-full max-w-[54rem] lg:mx-0">
            <div class="owner-screen">
                <img
                    src="{{ asset('site/assets/owner-dashboard.png') }}"
                    alt="{{ __('marketing.preview.alt') }}"
                    class="theme-dashboard-preview-light block aspect-[16/9] w-full object-cover object-top"
                    width="1600"
                    height="900"
                    decoding="async"
                    fetchpriority="high"
                />
                <img
                    src="{{ asset('site/assets/owner-dashboard.png') }}"
                    alt=""
                    class="theme-dashboard-preview-dark aspect-[16/9] w-full object-cover object-top"
                    width="1600"
                    height="900"
                    decoding="async"
                    aria-hidden="true"
                />
            </div>
            <figcaption class="absolute -bottom-5 start-5 border border-ocean/25 bg-white px-4 py-3 text-xs font-bold text-ocean shadow-[0_12px_30px_rgba(45,77,115,.12)] sm:start-8">
                <span class="me-2 inline-block h-2 w-2 bg-tide"></span>{{ __('marketing.preview.label') }}
            </figcaption>
        </figure>
    </div>

    <div class="relative z-10 border-t border-ocean/15 bg-white/65">
        <div class="site-shell grid divide-y divide-ocean/12 py-1 sm:grid-cols-3 sm:divide-x sm:divide-y-0 sm:rtl:divide-x-reverse">
            @foreach([__('marketing.hero.proof_1'), __('marketing.hero.proof_2'), __('marketing.hero.proof_3')] as $proof)
                <p class="flex items-center justify-center gap-2 py-4 text-center text-xs font-medium text-ink/58">
                    <span class="h-1.5 w-1.5 bg-ocean"></span>{{ $proof }}
                </p>
            @endforeach
        </div>
    </div>
</section>
