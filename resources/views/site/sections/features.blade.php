<section id="features" class="bg-white">
    <div class="mx-auto max-w-5xl px-6 py-12 md:py-16">
        <div class="flex items-center justify-center gap-2">
            <span class="h-[2px] w-7 bg-slate-200"></span>
            <span class="text-2xl font-semibold text-primary">{{ __('site.features.heading') }}</span>
            <span class="h-[2px] w-7 bg-slate-200"></span>
        </div>
        <h2 class="mt-3 text-center text-2xl font-bold text-[#424242] md:text-3xl">{{ __('site.features.title') }}</h2>
        <div class="relative mt-10">
            <div class="pointer-events-none absolute left-0 right-0 top-[18px] mx-auto hidden w-[92%] border-t border-dashed border-slate-300 md:block z-0"></div>
            <div class="grid gap-10 md:grid-cols-4 md:gap-6">
                @foreach([1, 2, 3, 4] as $i)
                <div class="text-center">
                    <div class="flex items-center justify-center relative z-10">
                        <span class="grid h-9 w-9 place-items-center rounded-full bg-blue-50 text-sm font-bold text-blue-600 ring-2 ring-white relative z-20">{{ $i }}</span>
                    </div>
                    <h3 class="mt-3 text-sm font-bold text-slate-900">{{ __('site.features.' . $i . '_title') }}</h3>
                    <p class="mt-2 text-xs leading-6 text-slate-500">{{ __('site.features.' . $i . '_desc') }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
