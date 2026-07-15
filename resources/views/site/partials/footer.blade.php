<footer class="border-t border-white/10 bg-ocean-deep text-white">
    <div class="site-shell grid gap-10 py-12 md:grid-cols-[1.5fr_.7fr_.7fr] md:py-16">
        <div>
            <a href="{{ route('landing-page') }}" class="inline-flex">
                <img src="{{ asset('site/assets/footer-logo.png') }}" alt="{{ __('site.meta.title') }}" class="h-auto w-24 object-contain" />
            </a>
            <p class="mt-5 max-w-md text-sm leading-7 text-white/65">{{ __('marketing.footer.description') }}</p>
        </div>

        <div>
            <h2 class="text-sm font-bold text-sand">{{ __('marketing.footer.product') }}</h2>
            <div class="mt-4 grid gap-3 text-sm text-white/68">
                <a href="{{ route('landing-page') }}#features" class="hover:text-white">{{ __('marketing.nav.product') }}</a>
                <a href="{{ route('site.pricing') }}" class="hover:text-white">{{ __('site.nav.pricing') }}</a>
                <a href="{{ route('frontend.show_login_form') }}" class="hover:text-white">{{ __('site.nav.login') }}</a>
            </div>
        </div>

        <div>
            <h2 class="text-sm font-bold text-sand">{{ __('marketing.footer.support') }}</h2>
            <div class="mt-4 grid gap-3 text-sm text-white/68">
                <a href="{{ route('site.contact') }}" class="hover:text-white">{{ __('site.nav.contact') }}</a>
                <a href="{{ route('site.about') }}" class="hover:text-white">{{ __('site.nav.about') }}</a>
            </div>
        </div>
    </div>

    <div class="border-t border-white/10">
        <div class="site-shell flex flex-col gap-2 py-5 text-xs text-white/45 sm:flex-row sm:items-center sm:justify-between">
            <p>{{ __('marketing.footer.copyright', ['year' => date('Y')]) }}</p>
            <p>{{ __('marketing.footer.tagline') }}</p>
        </div>
    </div>
</footer>
