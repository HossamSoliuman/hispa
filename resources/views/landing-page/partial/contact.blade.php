<!-- Contact Section -->
<section id="contact" class="py-5 text-dark">
    <div class="container">
        <div class="row g-5 align-items-start">

            <div class="col-lg-6">
                <h4 class="fw-bold mb-4">{{ __('landing-page.contact.form.title') }}</h4>
                <p class="text-muted mb-4">{{ __('landing-page.contact.form.description') }}</p>

                <form id="contactForm" method="post" name="form_contact_us">
                    @csrf
                    <div class="row gy-3 mb-3">
                        <div class="col-6">
                            <label class="form-label">{{__('landing-page.contact.form.first_name')}} <span class="text-theme">*</span></label>
                            <input type="text" name="first_name" class="form-control form-control-lg fs-15px"  placeholder="{{__('landing-page.contact.form.first_name')}}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">{{__('landing-page.contact.form.last_name')}} <span class="text-theme">*</span></label>
                            <input type="text" name="last_name" class="form-control form-control-lg fs-15px" placeholder="{{__('landing-page.contact.form.last_name')}}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">{{__('landing-page.contact.form.email')}} <span class="text-theme">*</span></label>
                            <input type="email" name="email" class="form-control form-control-lg fs-15px" placeholder="{{__('landing-page.contact.form.email')}}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">{{__('landing-page.contact.form.phone')}} <span class="text-theme">*</span></label>
                            <input type="tel" name="phone" class="form-control form-control-lg fs-15px" placeholder="{{__('landing-page.contact.form.phone_placeholder')}}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{__('landing-page.contact.form.message')}} <span class="text-theme">*</span></label>
                            <textarea name="message" class="form-control form-control-lg fs-15px" rows="8" placeholder="{{__('landing-page.contact.form.message_placeholder')}}" required></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary w-100 btn-lg btn-block px-4 fs-15px">{{__('landing-page.contact.form.send_button')}}</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-lg-6">
                <div class="text-center text-lg-start">
                    <h2 class="fw-bold mb-3">{{ __('landing-page.contact.title') }}</h2>
                    <p class="text-muted mb-4">
                        {{ __('landing-page.contact.description') }}
                    </p>
                </div>

                <div class="container text-start">
                    <div class="row g-4">
                        
                        <!-- Phone -->
                        <div class="col-md-6">
                            <div class="p-3">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle"
                                    style="width:70px; height:70px; background-color:#f5f5f5;">
                                    <img src="{{ asset('dashboard/assets/img/landing/contact/call.png') }}" alt="phone icon" width="28">
                                </div>
                                <h5 class="fw-semibold mb-2 mt-2">{{ __('landing-page.contact.phone') }}</h5>
                                <a href="tel:{{ $settings['phone'] ?? '997555515' }}"
                                    class="text-primary d-block mb-1">
                                    {{ $settings['phone'] ?? '997555515' }}
                                </a>
                                <p class="text-muted small mb-0">
                                    {{ __('landing-page.contact.working_days') }}
                                </p>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <div class="p-3">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle"
                                    style="width:70px; height:70px; background-color:#f5f5f5;">
                                    <img src="{{ asset('dashboard/assets/img/landing/contact/sms.png') }}" alt="email icon" width="28">
                                </div>
                                <h5 class="fw-semibold mb-2 mt-2">{{ __('landing-page.contact.email') }}</h5>
                                <a href="mailto:{{ $settings['email'] ?? 'support@hawat.sa' }}"
                                    class="text-primary d-block mb-1">
                                    {{ $settings['email'] ?? 'support@hawat.sa' }}
                                </a>
                                <p class="text-muted small mb-0">{{ __('landing-page.contact.email_note') }}</p>
                            </div>
                        </div>

                    </div>

                    <!-- Location -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="p-3">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle"
                                    style="width:70px; height:70px; background-color:#f5f5f5;">
                                    <img src="{{ asset('dashboard/assets/img/landing/contact/location.png') }}" alt="location icon" width="28">
                                </div>
                                <h5 class="fw-semibold mb-2 mt-2">{{ __('landing-page.contact.platform') }}</h5>
                                <a href="#" class="text-primary">
                                          {{  $settings['location'] ?? __('landing-page.contact.location') }}
                                </a>
                                <p class="text-muted small mb-1">{{ __('landing-page.contact.location_note') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</section>