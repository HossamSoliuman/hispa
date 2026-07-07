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

    @php $pending = session('pending_subscription'); @endphp
    <div class="w-full max-w-4xl">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-10">
            <div class="flex flex-col items-center text-center space-y-4">
                <div class="w-14 h-14 rounded-full flex items-center justify-center shadow-inner bg-gradient-to-r from-[#3778BC] to-[#4BAEE5]">
                    <span class="iconify" data-icon="mdi:clock-check-outline" style="font-size: 28px; color: white;"></span>
                </div>
                <div class="max-w-md">
                    <h2 class="text-xl font-semibold text-gray-800 mt-2">{{ __('site.processing.received_title') }}</h2>
                    <p class="text-[#717171] text-xs mt-2 leading-relaxed">{{ __('site.processing.received_desc') }}</p>
                </div>
                @if($pending)
                <div class="w-full border border-[#AFAFAF]/50 rounded-xl p-4 bg-[#FCFDFD] shadow-sm text-right">
                    <div class="flex flex-row flex-wrap justify-center items-center text-center gap-6">
                        @if(!empty($pending['invoice_number']))
                        <div class="flex flex-col gap-1">
                            <span class="text-[#7C7C7C] text-xs">{{ __('site.processing.invoice_number') }}</span>
                            <span class="font-bold text-base text-[#3C74BE]">{{ $pending['invoice_number'] }}</span>
                        </div>
                        @endif
                        <div class="flex flex-col gap-1">
                            <span class="text-[#7C7C7C] text-xs">{{ __('site.processing.plan') }}</span>
                            <span class="font-bold">{{ $pending['package'] ?? '--' }}</span>
                        </div>
                        @if(!empty($pending['duration']))
                        <div class="flex flex-col gap-1">
                            <span class="text-[#7C7C7C] text-xs">{{ __('site.processing.duration') }}</span>
                            <span class="font-bold text-sm">{{ __('site.order_review.durations.' . $pending['duration']) }}</span>
                        </div>
                        @endif
                        @if(!empty($pending['boats_count']))
                        <div class="flex flex-col gap-1">
                            <span class="text-[#7C7C7C] text-xs">{{ __('site.processing.boats_count') }}</span>
                            <span class="font-bold text-sm">{{ $pending['boats_count'] }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
                <div class="flex flex-col w-full border border-[#BEDBFF] bg-gradient-to-r from-[#EFF6FF] to-[#FEFFFF] rounded-xl p-3 text-sm text-[#597EED]">
                    <div class="flex flex-row gap-1 items-center">
                        <span class="iconify" data-icon="mdi:information-outline" style="font-size: 22px; color: #2F6FFC;"></span>
                        <span class="text-[#1C39AB] font-bold">{{ __('site.processing.next_title') }}</span>
                    </div>
                    <span class="text-xs text-[#597EED] leading-relaxed text-start ms-5">{{ __('site.processing.next_desc') }}</span>
                </div>
                <div class="flex w-full md:flex-row gap-5 mt-5">
                    <a href="{{ route('landing-page') }}" class="flex-1 border p-3 text-black border-[#E6E6E6] bg-[#F8F9FA] hover:shadow-md transition-all duration-300 rounded-lg text-center">{{ __('site.processing.home') }}</a>
                    <a href="{{ route('owner.dashboard') }}" class="flex-1 bg-gradient-to-r from-[#3778BC] to-[#4BAEE5] p-3 text-white hover:shadow-xl transition-all duration-300 rounded-lg text-center">{{ __('site.processing.go_dashboard') }}</a>
                </div>
            </div>
        </div>
    </div>
</main>

@push('scripts')
<script src="https://code.iconify.design/3/3.1.1/iconify.min.js"></script>
@endpush
@endsection
