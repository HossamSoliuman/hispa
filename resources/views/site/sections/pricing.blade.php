<section id="pricing" class="bg-white">
    <div class="mx-auto max-w-6xl px-6 py-12 md:py-16">
        <div class="text-center">
            <h2 class="text-2xl font-bold text-[#424242] md:text-3xl">{{ __('site.pricing.title') }}</h2>
            <p class="mt-2 text-sm text-[#8F8F8F] md:text-base !leading-loose tracking-wide md:max-w-5xl mx-auto">
                {{ __('site.pricing.subtitle') }}
            </p>
        </div>
        <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @forelse($subscriptionPackages ?? [] as $package)
                <article class="rounded-3xl bg-white p-5 shadow-sm border border-slate-50 {{ $package->is_featured ? 'relative border-blue-600 bg-blue-600 text-white shadow-md -translate-y-5 md:col-span-1' : '' }}">
                    <div class="text-start {{ $package->is_featured ? 'text-right' : '' }}">
                        <p class="text-2xl font-bold {{ $package->is_featured ? '!text-white' : 'text-primary' }}">{{ $package->name }}</p>
                        <p class="mt-1 text-xs {{ $package->is_featured ? 'text-white/90' : 'text-[#959595]' }}">{{ $package->description ?: ($package->boats_count ? ($package->boats_count == 1 ? __('site.pricing.boats_count', ['count' => 1]) : ($package->boats_count == 2 ? 'قاربان' : $package->boats_count . ' قوارب')) : '') }}</p>
                    </div>
                    <ul class="mt-4 space-y-2 text-start text-sm {{ $package->is_featured ? 'text-white/95' : 'text-[#3C74BE]' }}">
                        @if($package->boats_count)
                            <li class="flex items-start gap-2">
                                <span class="mt-1 {{ $package->is_featured ? 'text-white' : 'text-primary' }}"><iconify-icon icon="entypo:check" style="font-size: 16px;"></iconify-icon></span>
                                <span>{{ $package->boats_count == 1 ? 'قارب واحد' : ($package->boats_count == 2 ? 'قاربان' : $package->boats_count . ' قوارب') }}</span>
                            </li>
                        @endif
                        @foreach(array_slice($package->features ?? [], 0, 5) as $feature)
                            <li class="flex items-start gap-2">
                                <span class="mt-1 {{ $package->is_featured ? 'text-white' : 'text-primary' }}"><iconify-icon icon="entypo:check" style="font-size: 16px;"></iconify-icon></span>
                                <span>{{ is_string($feature) ? $feature : (is_array($feature) ? ($feature['text'] ?? $feature['name'] ?? '') : '') }}</span>
                            </li>
                        @endforeach
                        @if(empty($package->features) && $package->description)
                            <li class="flex items-start gap-2">
                                <span class="mt-1 {{ $package->is_featured ? 'text-white' : 'text-primary' }}"><iconify-icon icon="entypo:check" style="font-size: 16px;"></iconify-icon></span>
                                <span>{{ Str::limit($package->description, 80) }}</span>
                            </li>
                        @endif
                    </ul>
                    <div class="mt-5 text-right">
                        @if($package->original_price !== null || $package->price !== null)
                            @if($package->hasOfferPrice())
                                <p class="text-sm {{ $package->is_featured ? 'text-white/80' : 'text-slate-500' }}">
                                    <span class="line-through">{{ number_format((float) $package->original_price, 0) }}</span>
                                    <span class="mr-1">{{ __('site.pricing.currency', ['default' => 'ر.س']) }}</span>
                                </p>
                                <p class="text-3xl font-extrabold {{ $package->is_featured ? 'text-white' : 'text-primary' }}">{{ number_format((float) $package->effective_price, 0) }} <span class="text-sm font-semibold {{ $package->is_featured ? 'text-white/90' : 'text-[#3C74BE]' }}">{{ $package->duration_type === 'year' ? __('site.pricing.per_year') : ($package->duration_type === 'month' ? __('site.pricing.per_month', ['default' => '/شهر']) : __('site.pricing.per_year')) }}</span></p>
                            @else
                                <p class="text-3xl font-extrabold {{ $package->is_featured ? 'text-white' : 'text-primary' }}">{{ number_format((float) $package->effective_price, 0) }} <span class="text-sm font-semibold {{ $package->is_featured ? 'text-white/90' : 'text-[#3C74BE]' }}">{{ $package->duration_type === 'year' ? __('site.pricing.per_year') : ($package->duration_type === 'month' ? __('site.pricing.per_month', ['default' => '/شهر']) : __('site.pricing.per_year')) }}</span></p>
                            @endif
                        @else
                            <p class="text-sm {{ $package->is_featured ? 'text-white/90' : 'text-[#3C74BE]' }}">{{ __('site.pricing.custom_price') }}</p>
                        @endif
                    </div>
                    @if($package->original_price !== null || $package->price !== null)
                        <a href="{{ route('site.order-review', ['package_id' => $package->id]) }}" class="mt-4 block w-full rounded-md {{ $package->is_featured ? 'bg-white text-primary hover:bg-blue-50' : 'bg-[#3576BC]/10 text-primary hover:bg-[#3576BC]/20' }} px-4 py-3 text-sm font-semibold transition-all duration-300 text-center" data-plan="{{ $package->id }}">
                            {{ __('site.pricing.choose_plan') }}
                        </a>
                    @else
                        <a href="{{ route('site.contact') }}" class="mt-4 block w-full rounded-md {{ $package->is_featured ? 'bg-white text-primary hover:bg-blue-50' : 'bg-[#3576BC]/10 text-primary hover:bg-[#3576BC]/20' }} px-4 py-3 text-sm font-semibold transition-all duration-300 text-center">
                            {{ __('site.pricing.contact_us') }}
                        </a>
                    @endif
                </article>
            @empty
                {{-- Fallback: عرض ثابت إذا لم توجد باقات --}}
                <article class="rounded-3xl bg-white p-5 shadow-sm border border-slate-50">
                    <div class="text-start">
                        <p class="text-2xl font-bold text-primary">{{ __('site.pricing.starter.name') }}</p>
                        <p class="mt-1 text-xs text-[#959595]">{{ __('site.pricing.starter.desc') }}</p>
                    </div>
                    <ul class="mt-4 space-y-2 text-start text-sm text-[#3C74BE]">
                        <li class="flex items-start gap-2"><span class="mt-1 text-primary"><iconify-icon icon="entypo:check" style="font-size: 16px;"></iconify-icon></span><span>{{ __('site.pricing.starter.f1', ['default' => 'إدارة قارب واحد بحساب واحد.']) }}</span></li>
                        <li class="flex items-start gap-2"><span class="mt-1 text-primary"><iconify-icon icon="entypo:check" style="font-size: 16px;"></iconify-icon></span><span>{{ __('site.pricing.starter.f2', ['default' => 'تسجيل المصاريف والإيرادات.']) }}</span></li>
                    </ul>
                    <div class="mt-5 text-right">
                        <p class="text-3xl font-extrabold text-primary">999 <span class="text-sm font-semibold text-[#3C74BE]">{{ __('site.pricing.per_year') }}</span></p>
                    </div>
                    <a href="{{ route('frontend.show_register_form') }}" class="mt-4 block w-full rounded-md bg-[#3576BC]/10 px-4 py-3 text-sm font-semibold text-primary hover:bg-[#3576BC]/20 transition-all duration-300 text-center">{{ __('site.pricing.choose_plan') }}</a>
                </article>
            @endforelse
        </div>
        <div id="toast" class="pointer-events-none fixed bottom-6 left-6 hidden w-[320px] rounded-xl border border-slate-200 bg-white p-4 shadow-lg" role="status" aria-live="polite">
            <p class="text-sm font-bold text-slate-900">{{ __('site.pricing.choose_plan') }}</p>
            <p id="toastText" class="mt-1 text-sm text-slate-600"></p>
        </div>
    </div>
</section>
