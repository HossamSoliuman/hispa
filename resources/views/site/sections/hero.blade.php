<section id="home">
    <div class="mx-auto max-w-6xl px-6 py-12 md:py-16">
        <div class="grid items-center gap-10 md:grid-cols-2">
            <div class="order-2">
                <img src="{{ asset('site/assets/landingImg.png') }}" alt="{{ __('site.hero.title_3') }}"
                    class="w-full max-w-[520px] md:max-w-[560px] mx-auto select-none object-contain img-shadow-primary"
                    draggable="false" />
            </div>
            <div class="order-1 text-start">
                <div class="flex justify-start">
                    <span class="inline-flex items-center gap-2 rounded-full border border-blue-200 bg-white px-3 py-1 text-sm font-medium text-blue-600 shadow-sm">
                        <span class="h-1.5 w-1.5 rounded-full bg-blue-600"></span>
                        {{ __('site.hero.badge') }}
                    </span>
                </div>
                <h1 class="mt-4 text-3xl font-extrabold !leading-snug text-slate-900 md:text-4xl">
                    {{ __('site.hero.title_1') }}
                    <br />
                    <span class="inline-flex items-baseline gap-2">{{ __('site.hero.title_2') }} <span
                            class="relative inline-block font-extrabold text-[#3F78C9] pb-3 z-10">
                            <span class="relative z-20">{{ __('site.hero.title_3') }}</span>
                            <span class="absolute left-0 right-0 bottom-2 h-5 bg-[#3576BC]/5 z-0"></span>
                        </span></span>
                </h1>
                <p class="mt-4 max-w-xl text-sm leading-7 text-slate-500 md:text-[15px]">
                    {{ __('site.hero.description') }}
                </p>
                <div class="mt-6 flex flex-wrap justify-start gap-3">
                    <a href="{{ route('frontend.show_register_form') }}"
                        class="inline-flex items-center rounded-md bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        {{ __('site.hero.cta_start') }}
                    </a>
                    <a href="#features"
                        class="inline-flex items-center rounded-md border border-blue-200 bg-white px-5 py-2.5 text-sm font-semibold text-blue-600 hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        {{ __('site.hero.cta_watch') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
