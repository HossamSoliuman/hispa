<section id="forwho" class="bg-white">
    <div class="mx-auto max-w-6xl px-6 py-12 md:py-16">
        <div class="flex items-center justify-center gap-2">
            <span class="h-[2px] w-7 bg-slate-200"></span>
            <span class="text-2xl font-semibold text-primary">{{ __('site.forwho.heading') }}</span>
            <span class="h-[2px] w-7 bg-slate-200"></span>
        </div>
        <div class="mt-0 grid items-center gap-0 md:grid-cols-2">
            <div class="md:justify-self-start">
                <div class="mx-auto w-full max-w-[480px]">
                    <img src="{{ asset('site/assets/for-who.png') }}" alt="{{ __('site.forwho.title') }}" class="h-auto w-full object-contain select-none" draggable="false" />
                </div>
            </div>
            <div class="text-start">
                <h2 class="text-2xl font-bold leading-snug text-[#424242] md:text-3xl">
                    {{ __('site.forwho.title') }}
                    <span class="relative inline-block font-bold text-[#3F78C9] pb-3 z-10">
                        <span class="relative z-20">{{ __('site.forwho.title_highlight') }}</span>
                        <span class="absolute left-0 right-0 bottom-2 h-5 bg-[#3576BC]/5 z-0"></span>
                    </span>
                </h2>
                <p class="mt-4 max-w-xl text-sm text-[#575757] md:text-lg !leading-loose tracking-wide">
                    {{ __('site.forwho.description') }}
                </p>
            </div>
        </div>
    </div>
</section>
