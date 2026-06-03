<section id="contact" class="py-14">
    <div class="max-w-6xl mx-auto px-4">
        <div class="bg-white">
            <div class="flex flex-col lg:flex-row-reverse gap-20 items-start">
                <div class="w-full lg:w-7/12">
                    <div class="text-start mb-8">
                        <h2 class="mt-1 text-xl md:text-4xl font-extrabold text-[#3C74BE]">{{ __('site.contact.title') }}</h2>
                        <p class="mt-2 text-sm text-slate-500 max-w-2xl mx-auto">{{ __('site.contact.description') }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-7">
                        <div class="text-start">
                            <div class="me-auto w-12 h-12 rounded-xl border border-[#F6F2FF] bg-[#F6F2FF] text-[#3C74BE] flex items-center justify-center">
                                <span class="iconify text-3xl" data-icon="solar:phone-outline"></span>
                            </div>
                            <h3 class="mt-3 font-bold text-slate-900">{{ __('site.contact.phone') }}</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ __('site.contact.phone_hours') }}</p>
                            <a href="tel:997555515" class="mt-2 inline-block text-sm font-medium text-[#1272B9] hover:text-[#1272B9]/80">997555515</a>
                        </div>
                        <div class="text-start">
                            <div class="me-auto w-12 h-12 rounded-xl border border-[#F6F2FF] bg-[#F6F2FF] text-[#3C74BE] flex items-center justify-center">
                                <span class="iconify text-3xl" data-icon="mdi:email-outline"></span>
                            </div>
                            <h3 class="mt-3 font-bold text-slate-900">{{ __('site.contact.email') }}</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ __('site.contact.email_desc') }}</p>
                            <a href="mailto:support@hesba.sa" class="mt-2 inline-block text-sm font-medium text-[#1272B9] hover:text-[#1272B9]/80">support@hesba.sa</a>
                        </div>
                        <div class="text-start">
                            <div class="me-auto w-12 h-12 rounded-xl border border-[#F6F2FF] bg-[#F6F2FF] text-[#3C74BE] flex items-center justify-center">
                                <span class="iconify text-3xl" data-icon="mdi:map-marker-outline"></span>
                            </div>
                            <h3 class="mt-3 font-bold text-slate-900">{{ __('site.contact.location') }}</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ __('site.contact.location_address') }}</p>
                        </div>
                    </div>
                </div>
                <div class="w-full lg:w-5/12">
                    @if(session('success'))
                        <div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-800">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-800">{{ session('error') }}</div>
                    @endif
                    <h3 class="text-3xl font-bold">{{ __('site.contact.inquiry_title') }}</h3>
                    <p class="mt-2 text-sm text-slate-500 mb-10">{{ __('site.contact.inquiry_desc') }}</p>
                    <form action="{{ route('frontend-contact.store') }}" method="post" id="contactForm" class="space-y-4">
                        @csrf
                        <input type="hidden" name="subject" value="{{ __('site.contact.title') }}" />
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-[#344054]" for="firstName">{{ __('site.contact.first_name') }}</label>
                                <input id="firstName" name="first_name" type="text" value="{{ old('first_name') }}" placeholder="{{ __('site.contact.first_name') }}"
                                    class="mt-2 text-sm w-full h-11 rounded-lg border border-slate-200 bg-white px-3 text-right placeholder:text-[#667085] outline-none focus:ring-2 focus:ring-blue-500/20" />
                                @error('first_name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="text-sm font-medium text-[#344054]" for="lastName">{{ __('site.contact.last_name') }}</label>
                                <input id="lastName" name="last_name" type="text" value="{{ old('last_name') }}" placeholder="{{ __('site.contact.last_name') }}"
                                    class="mt-2 text-sm w-full h-11 rounded-lg border border-slate-200 bg-white px-3 text-right placeholder:text-[#667085] outline-none focus:ring-2 focus:ring-blue-500/20" />
                                @error('last_name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-[#344054]" for="email">البريد الإلكتروني</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="ادخل البريد الإلكتروني"
                                class="mt-2 text-sm w-full h-11 rounded-lg border border-slate-200 bg-white px-3 text-right placeholder:text-[#667085] outline-none focus:ring-2 focus:ring-blue-500/20" />
                            @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium text-[#344054]" for="phone">رقم الهاتف</label>
                            <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" placeholder="+966xxxxxxxxx"
                                class="mt-2 text-sm w-full h-11 rounded-lg border border-slate-200 bg-white px-3 text-right placeholder:text-[#667085] outline-none focus:ring-2 focus:ring-blue-500/20" />
                            @error('phone')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium text-[#344054]" for="message">{{ __('site.contact.message') }}</label>
                            <textarea id="message" name="message" placeholder="{{ __('site.contact.message_placeholder') }}"
                                class="mt-2 text-sm w-full min-h-[140px] rounded-lg border border-slate-200 bg-white px-3 py-2 text-right placeholder:text-[#667085] outline-none focus:ring-2 focus:ring-blue-500/20">{{ old('message') }}</textarea>
                            @error('message')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <button type="submit" class="w-full h-11 rounded-lg bg-[#1272B9] text-white font-semibold hover:bg-[#1272B9]/80 transition disabled:opacity-60 disabled:cursor-not-allowed">
                            {{ __('site.contact.send') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
