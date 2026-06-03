<section id="about" class="bg-white">
    <div class="mx-auto max-w-6xl px-6 py-12 md:py-16">
        <div class="flex items-center justify-center gap-2">
            <span class="h-[2px] w-7 bg-slate-200"></span>
            <span class="text-2xl font-semibold text-primary">{{ __('site.about.heading') }}</span>
            <span class="h-[2px] w-7 bg-slate-200"></span>
        </div>
        <div class="mt-5 space-y-14 md:space-y-16">
            <div class="grid items-center gap-6 md:gap-10 grid-cols-1 md:grid-cols-10">
                <div class="md:col-span-4">
                    <div class="relative mx-auto w-full max-w-[420px] rounded-3xl bg-white p-3 shadow-sm">
                        <div class="overflow-hidden rounded-2xl">
                            <img src="{{ asset('site/assets/hesba1.png') }}" alt="Hesba" class="h-auto w-full object-cover" draggable="false" />
                        </div>
                    </div>
                </div>
                <div class="text-start md:col-span-6">
                    <h2 class="text-2xl font-bold leading-snug text-[#424242] md:text-3xl">
                        {{ __('site.about.block1_title') }}
                        <span class="relative inline-block font-bold text-[#3F78C9] pb-3 z-10">
                            <span class="relative z-20">{{ __('site.about.block1_title_highlight') }}</span>
                            <span class="absolute left-0 right-0 bottom-2 h-5 bg-[#3576BC]/5 z-0"></span>
                        </span>
                    </h2>
                    <p class="mt-2 text-sm leading-relaxed text-[#424242] md:text-[15px]">{{ __('site.about.block1_p1') }}</p>
                    <p class="mt-2 text-sm leading-relaxed text-[#9E9E9E] md:text-[15px]">{{ __('site.about.block1_p2') }}</p>
                    <div class="mt-6">
                        <a href="#features"
                            class="inline-flex items-center rounded-xl border border-primary bg-white px-4 py-2 text-sm font-semibold text-primary hover:bg-blue-200/10 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                            {{ __('site.about.learn_more') }}
                        </a>
                    </div>
                </div>
            </div>
            <div class="grid items-center gap-6 md:gap-10 grid-cols-1 md:grid-cols-10">
                <div class="md:col-span-4 md:col-start-7 md:order-2">
                    <div class="relative mx-auto w-full max-w-[460px] rounded-3xl bg-white p-3 shadow-sm">
                        <div class="overflow-hidden rounded-2xl">
                            <img src="{{ asset('site/assets/hesba2.png') }}" alt="Hesba" class="h-auto w-full object-cover" draggable="false" />
                        </div>
                    </div>
                </div>
                <div class="text-right md:col-span-6 md:col-start-1 md:order-1">
                    <h3 class="text-2xl font-bold leading-snug text-[#424242] md:text-3xl">{{ __('site.about.block2_title') }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-[#424242] md:text-[15px]">{{ __('site.about.block2_p1') }}</p>
                    <p class="mt-2 text-sm leading-relaxed text-[#9E9E9E] md:text-[15px]">{{ __('site.about.block2_p2') }}</p>
                    <div class="mt-6">
                        <a href="{{ route('site.pricing') }}"
                            class="inline-flex items-center rounded-xl border border-primary bg-white px-4 py-2 text-sm font-semibold text-primary hover:bg-blue-200/10 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                            {{ __('site.about.learn_more') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
