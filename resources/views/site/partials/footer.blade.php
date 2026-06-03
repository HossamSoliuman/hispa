<footer class="relative bg-primary text-white pt-8 pb-4 md:pt-12 md:pb-6 lg:pt-16 lg:pb-8" dir="rtl">
    <div class="container mx-auto px-4 md:px-6 max-w-6xl">
        <div class="grid gap-6 md:gap-8 lg:gap-12 md:grid-cols-3">
            <div class="space-y-4 md:space-y-5 text-right">
                <a href="{{ route('landing-page') }}#home" class="inline-block">
                    <img src="{{ asset('site/assets/footer-logo.png') }}" alt="{{ __('site.meta.title') }}" class="w-24 h-fit object-contain" onerror="this.style.display='none'; this.nextElementSibling?.style.display='flex';" />
                    <div style="display: none;" class="flex items-center gap-2">
                        <span class="text-lg font-semibold text-white">{{ __('site.meta.title') }}</span>
                    </div>
                </a>
                <p class="text-xs md:text-sm opacity-90 leading-relaxed max-w-xs text-white">
                    {{ __('site.footer.about') }}
                </p>
                <div class="flex items-center gap-0 justify-start pt-2">
                    <a href="#" class="hover:opacity-80 transition px-2 md:px-3" aria-label="YouTube">
                        <span class="iconify text-xl md:text-2xl" data-icon="mdi:youtube" style="color: white;"></span>
                    </a>
                    <div class="h-5 md:h-6 w-px bg-white/30"></div>
                    <a href="#" class="hover:opacity-80 transition px-2 md:px-3" aria-label="Twitter">
                        <span class="iconify text-xl md:text-2xl" data-icon="mdi:twitter" style="color: white;"></span>
                    </a>
                    <div class="h-5 md:h-6 w-px bg-white/30"></div>
                    <a href="#" class="hover:opacity-80 transition px-2 md:px-3" aria-label="Instagram">
                        <span class="iconify text-xl md:text-2xl" data-icon="mdi:instagram" style="color: white;"></span>
                    </a>
                    <div class="h-5 md:h-6 w-px bg-white/30"></div>
                    <a href="#" class="hover:opacity-80 transition px-2 md:px-3" aria-label="Facebook">
                        <span class="iconify text-xl md:text-2xl" data-icon="mdi:facebook" style="color: white;"></span>
                    </a>
                </div>
            </div>
            <div class="space-y-4 md:space-y-5 text-right">
                <h5 class="font-medium text-xl md:text-2xl">{{ __('site.footer.quick_links') }}</h5>
                <div class="flex flex-col gap-2 md:gap-3 text-xs md:text-sm">
                    <a href="{{ route('landing-page') }}#home" class="hover:text-[#C49D48] transition">{{ __('site.nav.home') }}</a>
                    <a href="{{ route('site.about') }}" class="hover:text-[#C49D48] transition">{{ __('site.nav.about') }}</a>
                    <a href="{{ route('landing-page') }}#features" class="hover:text-[#C49D48] transition">{{ __('site.nav.features') }}</a>
                    <a href="{{ route('landing-page') }}#forwho" class="hover:text-[#C49D48] transition">{{ __('site.footer.for_who') }}</a>
                    <a href="{{ route('site.pricing') }}" class="hover:text-[#C49D48] transition">{{ __('site.footer.pricing') }}</a>
                    <a href="{{ route('site.contact') }}" class="hover:text-[#C49D48] transition">{{ __('site.nav.contact') }}</a>
                </div>
            </div>
            <div class="space-y-4 md:space-y-5 text-right">
                <h5 class="font-medium text-xl md:text-2xl">{{ __('site.footer.newsletter') }}</h5>
                <p class="text-xs md:text-sm opacity-90 leading-relaxed text-white">
                    {{ __('site.footer.newsletter_desc') }}
                </p>
                <div class="relative mt-3 md:mt-4">
                    <form id="newsletterForm" class="flex gap-0" action="#" method="post">
                        @csrf
                        <input type="email" id="newsletterEmail" name="email" placeholder="{{ __('site.footer.newsletter_placeholder') }}"
                            class="flex-1 h-10 md:h-11 rounded-r-lg rounded-l-none border border-[#E6E6E6] bg-white text-slate-900 placeholder:text-slate-500 text-xs md:text-sm px-3 outline-none focus:ring-2 focus:ring-white/50" />
                        <button type="submit" class="h-10 w-10 md:h-11 md:w-11 rounded-l-lg rounded-r-none bg-primary hover:bg-primary-dark border border-[#E6E6E6] border-r-0 text-white p-0 flex items-center justify-center transition">
                            <span class="iconify text-lg md:text-xl scale-x-[-1]" data-icon="material-symbols:send-rounded" style="color: white;"></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="text-center mt-6 md:mt-8 pt-4 md:pt-5 lg:pt-6 border-t border-white/20">
            <div class="opacity-80 text-xs">
                {{ __('site.footer.copyright') }} <span id="currentYear">{{ date('Y') }}</span>
            </div>
        </div>
    </div>
</footer>
