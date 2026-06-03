@extends('site.layouts.app')
@section('title', __('site.order_review.title', ['default' => 'مراجعة الطلب']) . ' - ' . __('site.meta.title'))
@section('description', __('site.order_review.description', ['default' => 'مراجعة طلب الاشتراك']))

@php
    $packageId = request('package_id');
    $selectedPackage = $subscriptionPackages->firstWhere('id', $packageId) ?? $subscriptionPackages->first();
    $price = $selectedPackage ? (float) $selectedPackage->effective_price : 500;
    $title = $selectedPackage ? $selectedPackage->name : 'البداية';
    $description = $selectedPackage ? ($selectedPackage->description ?: 'مناسبة لقارب واحد') : 'مناسبة لقارب واحد';
    $featuresList = $selectedPackage && is_array($selectedPackage->features) ? $selectedPackage->features : [
        'إتمام عملية الدفع',
        'إدارة قارب واحد بحساب واحد.',
        'تسجيل المصاريف والإيرادات.',
        'محاصصة تلقائية للرحلة.',
        'تقرير ختامي لكل رحلة.',
        'تتبع أساسي للديون والسلفيات.',
    ];
@endphp

@section('content')
<main class="min-h-screen flex flex-col items-center py-10 px-4 pt-6 bg-gray-50">
    <div class="w-full flex justify-center mb-10">
        <div class="flex items-center justify-center space-x-10 rtl:space-x-reverse">
            <div class="flex flex-col items-center">
                <div class="w-9 h-9 flex items-center justify-center rounded-full text-white text-base font-semibold shadow-sm bg-[linear-gradient(98.7deg,#3778BC_19.22%,#4BAEE5_73.07%)] scale-110">1</div>
                <span class="mt-2 text-sm text-center text-black font-medium">اختيار الباقة</span>
            </div>
            <div class="h-[2px] w-5 sm:w-20 md:w-28 rounded-full bg-[linear-gradient(98.7deg,#3778BC_19.22%,#4BAEE5_73.07%)]"></div>
            <div class="flex flex-col items-center">
                <div class="w-9 h-9 flex items-center justify-center rounded-full text-[#99A1AF] border border-[#D3D7DE] text-base font-semibold shadow-sm bg-white">2</div>
                <span class="mt-2 text-sm text-center text-gray-500">الدفع</span>
            </div>
            <div class="h-[2px] w-5 sm:w-20 md:w-28 rounded-full bg-[#E5E7EB]"></div>
            <div class="flex flex-col items-center">
                <div class="w-9 h-9 flex items-center justify-center rounded-full text-[#99A1AF] border border-[#D3D7DE] text-base font-semibold shadow-sm bg-white">3</div>
                <span class="mt-2 text-sm text-center text-gray-500">التوجه إلى لوحة التحكم</span>
            </div>
        </div>
    </div>

    <div class="w-full max-w-7xl grid grid-cols-1 lg:grid-cols-3 gap-6">
        <aside class="lg:col-span-1 order-1 lg:order-2">
            <div class="flex flex-col gap-6">
                <div class="bg-white rounded-lg shadow-md border border-[#E5E7EB] p-6">
                    <h3 class="font-semibold text-xl mb-3">ملخص الطلب</h3>
                    <span class="text-sm text-[#939393]">{{ $title }}</span>
                    <div class="flex justify-between text-sm text-gray-600 mt-4">
                        <span class="text-[#939393] text-xs">سعر الباقة</span>
                        <span id="sidebarPrice" class="font-bold text-[#3C74BE]">{{ number_format($price) }} ريال</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600 mb-3 mt-2">
                        <span class="text-[#939393] text-xs">رسوم المعاملة</span>
                        <span class="text-[#00B503] font-bold">مجانا</span>
                    </div>
                    <div class="border-t my-5"></div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm">الإجمالي</span>
                        <span id="sidebarTotal" class="text-[#3C74BE] text-2xl font-bold">{{ number_format($price) }} ريال</span>
                    </div>
                </div>
                <div class="rounded-lg shadow-lg border border-[#E5E7EB] bg-white p-6">
                    <h3 class="font-semibold text-xl mb-3">مزايا الباقة</h3>
                    <span class="text-sm text-[#939393]">{{ $title }}</span>
                    @foreach($featuresList as $f)
                        <div class="flex gap-2 text-sm text-[#3C74BE] mt-4 items-start">
                            <span class="iconify" data-icon="material-symbols:check-rounded" style="font-size: 20px; color: #3C74BE;"></span>
                            <span>{{ is_string($f) ? $f : ($f['text'] ?? $f['name'] ?? '') }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="bg-white rounded-lg border border-[#E5E7EB] shadow-lg p-5">
                    <div class="flex flex-col items-center text-center">
                        <span class="iconify" data-icon="mdi:help-circle" style="font-size: 24px; color: #3C74BE;"></span>
                        <span class="font-bold mb-1 text-[#3C74BE]">تحتاج مساعدة ؟</span>
                        <span class="mb-3 text-sm text-[#939393]">فريق الدعم جاهز لمساعدتك</span>
                        <a href="{{ route('site.contact') }}" class="bg-white text-[#3C74BE] hover:bg-[#3C74BE]/5 border rounded-2xl border-[#3C74BE] w-full py-2.5 font-semibold transition-colors inline-block text-center">تواصل معنا</a>
                    </div>
                </div>
            </div>
        </aside>

        <div id="step1Content" class="lg:col-span-2 order-2 lg:order-1">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold mb-5">مراجعة الطلب</h2>
                <div class="flex flex-col gap-2 items-start">
                    <div class="flex flex-row gap-2 items-center">
                        <span class="bg-[#3C74BE]/5 border border-[#46A1DB] rounded-lg">
                            <span class="text-sm inline-block font-semibold p-2 bg-[#3C74BE]/10 whitespace-nowrap" style="background: linear-gradient(104.3deg, #0179B4 7.76%, #88D8FF 90.48%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">الباقة المختارة</span>
                        </span>
                        <div class="flex flex-col">
                            <span id="serviceTitle" class="font-bold text-xl">{{ $title }}</span>
                            <span id="serviceDescription" class="text-sm text-[#939393] line-clamp-2">{{ $description }}</span>
                        </div>
                    </div>
                </div>
                <hr class="my-3" />
                <div class="flex flex-col border p-4 rounded-xl border-[#BEDBFF] bg-gradient-to-r from-[#EFF6FF] to-[#FEFFFF]">
                    <div class="flex flex-row gap-1 items-center">
                        <span class="iconify" data-icon="mdi:exclamation-circle" style="font-size: 24px; color: #1C39AB;"></span>
                        <span class="text-[#1C39AB] font-semibold">المستندات المطلوبة</span>
                    </div>
                    <span class="text-[#597EED] rounded text-xs mr-7">بعد إتمام الدفع، ستحتاج إلى رفع المستندات المطلوبة لإكمال طلبك. سيتم توجيهك تلقائياً لصفحة رفع الملفات.</span>
                </div>
                <hr class="my-3" />
                <div class="flex flex-row justify-between items-center">
                    <div class="flex flex-col items-end">
                        <span class="text-sm mb-1 text-gray-600">المبلغ الإجمالي</span>
                        <span id="servicePrice" class="text-[#3C74BE] font-bold text-3xl">{{ number_format($price) }} ريال</span>
                    </div>
                </div>
                <hr class="my-3" />
                <a href="{{ route('site.payment') }}?orderId=ORDER{{ $selectedPackage ? $selectedPackage->id : '' }}{{ time() }}&package_id={{ $selectedPackage?->id ?? '' }}&amount={{ $price }}" id="submitBtn" class="mt-6 block w-full text-center text-white bg-[#3C74BE] py-4 hover:bg-[#3C74BE]/80 transition-colors rounded-lg shadow-sm">المتابعة إلى الدفع</a>
                <p id="errorMsg" class="hidden text-center text-sm text-red-600 mt-3"></p>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script src="https://code.iconify.design/3/3.1.1/iconify.min.js"></script>
@endpush
