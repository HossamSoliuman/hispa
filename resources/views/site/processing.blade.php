@extends('site.layouts.app')
@section('title', __('site.processing.title', ['default' => 'معالجة الطلب']) . ' - ' . __('site.meta.title'))

@section('content')
<main class="min-h-screen flex flex-col items-center py-10 px-4 pt-6 bg-gray-50">
    <div class="w-full flex justify-center mb-10">
        <div class="flex items-center justify-center space-x-10 rtl:space-x-reverse">
            <div class="flex flex-col items-center">
                <div class="w-9 h-9 flex items-center justify-center rounded-full text-white text-base font-semibold shadow-sm" style="background: linear-gradient(98.7deg, #3778BC 19.22%, #4BAEE5 73.07%);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12" /></svg>
                </div>
                <span class="mt-2 text-sm text-center text-gray-500">اختيار الباقة</span>
            </div>
            <div class="h-[2px] w-5 sm:w-20 md:w-28 rounded-full" style="background: linear-gradient(98.7deg, #3778BC 19.22%, #4BAEE5 73.07%);"></div>
            <div class="flex flex-col items-center">
                <div class="w-9 h-9 flex items-center justify-center rounded-full text-white text-base font-semibold shadow-sm" style="background: linear-gradient(98.7deg, #3778BC 19.22%, #4BAEE5 73.07%);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12" /></svg>
                </div>
                <span class="mt-2 text-sm text-center text-gray-500">الدفع</span>
            </div>
            <div class="h-[2px] w-5 sm:w-20 md:w-28 rounded-full" style="background: linear-gradient(98.7deg, #3778BC 19.22%, #4BAEE5 73.07%);"></div>
            <div class="flex flex-col items-center">
                <div class="w-9 h-9 flex items-center justify-center rounded-full text-white text-base font-semibold shadow-sm scale-110" style="background: linear-gradient(98.7deg, #3778BC 19.22%, #4BAEE5 73.07%);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12" /></svg>
                </div>
                <span class="mt-2 text-sm text-center text-black font-medium">التوجه إلى لوحة التحكم</span>
            </div>
        </div>
    </div>

    <div class="w-full max-w-4xl">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-10">
            <div class="flex flex-col items-center text-center space-y-4">
                <div class="w-14 h-14 rounded-full flex items-center justify-center shadow-inner bg-gradient-to-r from-[#00BC7D] to-[#009F6A]">
                    <span class="iconify" data-icon="qlementine-icons:success-32" style="font-size: 28px; color: white;"></span>
                </div>
                <div class="max-w-sm">
                    <h2 class="text-xl font-semibold text-gray-800 mt-2">تم تفعيل اشتراكك بنجاح!</h2>
                    <p class="text-[#717171] text-xs mt-2 leading-relaxed">يمكنك الآن البدء بتجهيز بيانات القارب وإضافة أول رحلة صيد. سيتم إرسال تفاصيل الاشتراك إلى بريدك الإلكتروني خلال دقائق.</p>
                </div>
                <div class="w-full border border-[#AFAFAF]/50 rounded-xl p-4 bg-[#FCFDFD] shadow-sm text-right">
                    <div class="flex flex-row justify-center items-center text-center gap-6">
                        <div class="flex flex-col gap-1">
                            <span class="text-[#7C7C7C] text-xs">رقم الاشتراك</span>
                            <span id="orderNumber" class="font-bold text-base text-[#3C74BE]">#SUB-{{ request('orderId', '') ?: substr((string)time(), -6) }}</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-[#7C7C7C] text-xs">الباقة</span>
                            <span id="orderService" class="font-bold">باقة المجداف (البداية)</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-[#7C7C7C] text-xs">مدة الاشتراك</span>
                            <span id="orderServiceDuration" class="font-bold text-sm">سنة واحدة</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-[#7C7C7C] text-xs">عدد القوارب</span>
                            <span id="orderServiceCount" class="font-bold text-sm">قارب واحد</span>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col w-full border border-[#BEDBFF] bg-gradient-to-r from-[#EFF6FF] to-[#FEFFFF] rounded-xl p-3 text-sm text-[#597EED]">
                    <div class="flex flex-row gap-1 items-center">
                        <span class="iconify" data-icon="mdi:information-outline" style="font-size: 22px; color: #2F6FFC;"></span>
                        <span class="text-[#1C39AB] font-bold">ماذا بعد ؟</span>
                    </div>
                    <span class="text-xs text-[#597EED] leading-relaxed text-start ms-5">الخطوة التالية هي تسجيل بيانات القارب الأساسية، ثم يمكنك البدء بإضافة الرحلات وتوثيق المصاريف والإيرادات بكل سهولة.</span>
                </div>
                <div class="flex w-full md:flex-row gap-5 mt-5">
                    <a href="{{ route('landing-page') }}" class="flex-1 border p-3 text-black border-[#E6E6E6] bg-[#F8F9FA] hover:shadow-md transition-all duration-300 rounded-lg text-center">الرئيسية</a>
                    <a href="{{ route('frontend.show_login_form') }}" class="flex-1 bg-gradient-to-r from-[#3778BC] to-[#4BAEE5] p-3 text-white hover:shadow-xl transition-all duration-300 rounded-lg text-center">تسجيل الدخول</a>
                </div>
            </div>
        </div>
    </div>
</main>

@push('scripts')
<script src="https://code.iconify.design/3/3.1.1/iconify.min.js"></script>
<script>
(function() {
    const urlParams = new URLSearchParams(window.location.search);
    const orderId = urlParams.get('orderId');
    if (orderId) {
        const el = document.getElementById('orderNumber');
        if (el) el.textContent = '#SUB-' + orderId.toString().replace('ORDER', '').slice(-8);
    }
})();
</script>
@endpush
@endsection
