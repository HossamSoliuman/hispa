<div id="footer" class="py-5 bg-gray-900 bg-opacity-75 text-body text-opacity-75" data-bs-theme="dark">
    <div class="container-xl px-3 px-lg-5">
        <div class="row gx-lg-5 gx-3 gy-lg-4 gy-3">
            <div class="col-lg-4 col-md-6">
                <h4>{{__('landing-page.footer.platform_name')}}</h4>
                <p class="pe-5">
                    {{__('landing-page.footer.description')}}
                </p>

            </div>
            <div class="col-lg-4 col-md-6 ps-5">
                <h5>{{__('landing-page.footer.pages')}}</h5>
                <ul class="list-unstyled">
                    <li class="mb-3px"><a href="#home" class="text-decoration-none text-body text-opacity-75">{{__('landing-page.header.home')}}</a></li>
                    <li class="mb-3px"><a href="#about" class="text-decoration-none text-body text-opacity-75">{{__('landing-page.header.about')}}</a></li>
                    <li class="mb-3px"><a href="#features" class="text-decoration-none text-body text-opacity-75">{{__('landing-page.header.features')}}</a></li>
                    <li class="mb-3px"><a href="#contact" class="text-decoration-none text-body text-opacity-75">{{__('landing-page.header.contact')}}</a></li>
                    @foreach($pages as $page)
                    <li class="mb-3px"><a href="{{route('frontend.page',$page->slug)}}" class="text-decoration-none text-body text-opacity-75">{{$page->title}}</a></li>
                    @endforeach
                    <li class="mb-3px"><a href="#contact" class="text-decoration-none text-body text-opacity-75">{{__('landing-page.footer.support_center')}}</a></li>
                </ul>
                {{-- <hr class="text-body text-opacity-50">--}}
                {{-- <h5>خدماتنا</h5>--}}
                {{-- <ul class="list-unstyled">--}}
                {{-- <li class="mb-3px"><a href="#" class="text-decoration-none text-body text-opacity-75">تطوير الويب</a></li>--}}
                {{-- <li class="mb-3px"><a href="#" class="text-decoration-none text-body text-opacity-75">تطوير التطبيقات</a></li>--}}
                {{-- <li class="mb-3px"><a href="#" class="text-decoration-none text-body text-opacity-75">تحسين محركات البحث</a></li>--}}
                {{-- <li class="mb-3px"><a href="#" class="text-decoration-none text-body text-opacity-75">التسويق الرقمي</a></li>--}}
                {{-- </ul>--}}
            </div>
            <!-- <div class="col-lg-3 col-md-6">
                <h5>{{__('landing-page.footer.help_center')}}</h5>
                <ul class="list-unstyled">
                    <li class="mb-3px"><a href="#contact" class="text-decoration-none text-body text-opacity-75">{{__('landing-page.footer.support_center')}}</a></li>
                </ul>
            </div> -->
            <div class="col-lg-4 col-md-6">
                <h5>{{__('landing-page.footer.download_app')}}</h5>
                <p>{{__('landing-page.footer.available_on_mobile')}}</p>
                <div class="d-flex gap-2">
                    <a href="https://play.google.com/store/apps/details?id=com.os.hawat" target="_blank">
                        <img src="{{asset('dashboard/assets/img/landing/google-play.svg')}}"
                            alt="Google Play" style="width:100px; height:auto;">
                    </a>
                    <a href="https://apps.apple.com/gb/app/hawat-حسبة/id6751249892?uo=2" target="_blank">
                        <img src="{{asset('dashboard/assets/img/landing/app-store.svg')}}"
                            alt="App Store" style="width:100px; height:auto;">
                    </a>
                </div>
                <h5 class="mt-4">{{__('landing-page.footer.follow_us')}}</h5>
                <div class="d-flex">
                    <a href="https://www.facebook.com/profile.php?id=61578341710725" class="me-2 text-body text-opacity-50" target="_blank"><i class="fab fa-lg fa-facebook fa-fw"></i></a>
                    <a href="https://www.instagram.com/hawat.sa/?hl=en" class="me-2 text-body text-opacity-50" target="_blank"><i class="fab fa-lg fa-instagram fa-fw"></i></a>
                    <a href="https://x.com/alhuwat" class="me-2 text-body text-opacity-50" target="_blank"><i class="fab fa-lg fa-x-twitter fa-fw"></i></a>
                    <a href="#" class="me-2 text-body text-opacity-50"><i class="fab fa-lg fa-youtube fa-fw"></i></a>
                    <a href="#" class="me-2 text-body text-opacity-50"><i class="fab fa-lg fa-linkedin fa-fw"></i></a>
                </div>
            </div>
        </div>
        <hr class="text-body text-opacity-50">
        <div class="row">
            <div class="col-sm-12 mb-3 mb-lg-0">
                <div class="footer-copyright-text">
                    &copy; {{$settings['title'] ?? "حسبة"}}
                    {{ date('Y') }}. {{__('landing-page.footer.copyright')}}
                </div>
            </div>
            {{-- <div class="col-sm-6 text-sm-end">--}}
            {{-- <div class="dropdown me-4 d-inline">--}}
            {{-- <a href="#" class="text-decoration-none dropdown-toggle text-body text-opacity-50" data-bs-toggle="dropdown">فلسطين (العربية)</a>--}}
            {{-- <ul class="dropdown-menu">--}}
            {{-- <li><a href="#" class="dropdown-item">فلسطين (العربية)</a></li>--}}
            {{-- <li><a href="#" class="dropdown-item">الولايات المتحدة (الإنجليزية)</a></li>--}}
            {{-- <li><a href="#" class="dropdown-item">الصين (简体中文)</a></li>--}}
            {{-- <li><a href="#" class="dropdown-item">البرازيل (Português)</a></li>--}}
            {{-- <li><a href="#" class="dropdown-item">ألمانيا (Deutsch)</a></li>--}}
            {{-- <li><a href="#" class="dropdown-item">فرنسا (Français)</a></li>--}}
            {{-- <li><a href="#" class="dropdown-item">اليابان (日本語)</a></li>--}}
            {{-- <li><a href="#" class="dropdown-item">كوريا (한국어)</a></li>--}}
            {{-- <li><a href="#" class="dropdown-item">أمريكا اللاتينية (Español)</a></li>--}}
            {{-- <li><a href="#" class="dropdown-item">إسبانيا (Español)</a></li>--}}
            {{-- </ul>--}}
            {{-- </div>--}}
            {{-- <a href="#" class="text-decoration-none text-body text-opacity-50">خريطة الموقع</a>--}}
            {{-- </div>--}}
        </div>
    </div>
</div>
