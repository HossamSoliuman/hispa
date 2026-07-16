<section id="contact" class="public-site-section scroll-mt-20 border-t border-ocean/15 py-12 md:py-14">
    <div class="site-shell">
        <div class="mx-auto max-w-3xl text-center">
            <span class="eyebrow text-ocean">{{ __('site.nav.contact') }}</span>
            <h2 class="mt-4 text-3xl font-bold leading-tight tracking-[-0.035em] text-ink md:text-5xl">{{ __('site.contact.title') }}</h2>
            <p class="mx-auto mt-4 max-w-2xl text-base leading-8 text-ink/55">{{ __('site.contact.description') }}</p>
        </div>

        <div class="mt-8 grid items-start gap-5 lg:grid-cols-[0.85fr_1.15fr]">
            <aside class="hud-panel p-6 text-start sm:p-7">
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-1">
                    <div class="flex items-start gap-4">
                        <span class="grid h-11 w-11 shrink-0 place-items-center border border-ocean/20 bg-ocean/[0.08] text-ocean">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.12 4.2 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.9.33 1.78.62 2.63a2 2 0 0 1-.45 2.11L8 9.73a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.85.3 1.73.5 2.63.62A2 2 0 0 1 22 16.92Z" /></svg>
                        </span>
                        <div>
                            <h3 class="font-bold text-ink">{{ __('site.contact.phone') }}</h3>
                            <p class="mt-1 text-sm leading-6 text-ink/48">{{ __('site.contact.phone_hours') }}</p>
                            <a href="tel:997555515" class="mt-1 inline-block text-sm font-bold text-ocean" dir="ltr">997555515</a>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <span class="grid h-11 w-11 shrink-0 place-items-center border border-ocean/20 bg-ocean/[0.08] text-ocean">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 4h16v16H4zM4 7l8 6 8-6" /></svg>
                        </span>
                        <div>
                            <h3 class="font-bold text-ink">{{ __('site.contact.email') }}</h3>
                            <p class="mt-1 text-sm leading-6 text-ink/48">{{ __('site.contact.email_desc') }}</p>
                            <a href="mailto:support@hesba.sa" class="mt-1 inline-block text-sm font-bold text-ocean" dir="ltr">support@hesba.sa</a>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 sm:col-span-2 lg:col-span-1">
                        <span class="grid h-11 w-11 shrink-0 place-items-center border border-ocean/20 bg-ocean/[0.08] text-ocean">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z" /><circle cx="12" cy="10" r="2.5" /></svg>
                        </span>
                        <div>
                            <h3 class="font-bold text-ink">{{ __('site.contact.location') }}</h3>
                            <p class="mt-1 text-sm leading-6 text-ink/48">{{ __('site.contact.location_address') }}</p>
                        </div>
                    </div>
                </div>
            </aside>

            <div class="hud-panel p-6 text-start sm:p-7">
                @if(session('success'))
                    <div class="mb-5 border border-tide/25 bg-tide/10 p-4 text-sm text-tide">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="mb-5 border border-red-500/25 bg-red-500/10 p-4 text-sm text-red-600">{{ session('error') }}</div>
                @endif

                <h3 class="text-2xl font-bold text-ink">{{ __('site.contact.inquiry_title') }}</h3>
                <p class="mt-2 text-sm leading-7 text-ink/50">{{ __('site.contact.inquiry_desc') }}</p>

                <form action="{{ route('frontend-contact.store') }}" method="post" id="contactForm" class="mt-6 grid gap-4 sm:grid-cols-2">
                    @csrf
                    <input type="hidden" name="subject" value="{{ __('site.contact.title') }}" />

                    <div>
                        <label class="mb-2 block text-xs font-bold text-ink/70" for="firstName">{{ __('site.contact.first_name') }}</label>
                        <input id="firstName" name="first_name" type="text" value="{{ old('first_name') }}" placeholder="{{ __('site.contact.first_name') }}" class="checkout-field" />
                        @error('first_name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-xs font-bold text-ink/70" for="lastName">{{ __('site.contact.last_name') }}</label>
                        <input id="lastName" name="last_name" type="text" value="{{ old('last_name') }}" placeholder="{{ __('site.contact.last_name') }}" class="checkout-field" />
                        @error('last_name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-xs font-bold text-ink/70" for="email">{{ __('site.contact.email') }}</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="{{ __('site.contact.email') }}" class="checkout-field text-left" dir="ltr" />
                        @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-xs font-bold text-ink/70" for="phone">{{ __('site.contact.phone') }}</label>
                        <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" placeholder="+966xxxxxxxxx" class="checkout-field text-left" dir="ltr" />
                        @error('phone')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="mb-2 block text-xs font-bold text-ink/70" for="message">{{ __('site.contact.message') }}</label>
                        <textarea id="message" name="message" placeholder="{{ __('site.contact.message_placeholder') }}" class="checkout-field min-h-28 py-3">{{ old('message') }}</textarea>
                        @error('message')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <button type="submit" class="inline-flex min-h-12 items-center justify-center bg-ocean px-5 py-3 text-sm font-bold text-white hover:-translate-y-0.5 hover:bg-ocean-deep sm:col-span-2">
                        {{ __('site.contact.send') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
