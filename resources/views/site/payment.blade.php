@extends('site.layouts.app')
@section('title', __('site.payment.title', ['default' => 'الدفع']) . ' - ' . __('site.meta.title'))

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
                <div class="w-9 h-9 flex items-center justify-center rounded-full text-white text-base bg-gradient-to-r from-[#3778BC] to-[#4BAEE5] font-semibold shadow-sm scale-110">2</div>
                <span class="mt-2 text-sm text-center text-black font-medium">الدفع</span>
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
            <div class="bg-white rounded-lg shadow-md border border-[#E5E7EB] p-6">
                <h3 class="font-semibold text-xl mb-3">ملخص الطلب</h3>
                <span class="text-sm text-[#939393]" id="sidebarPackageName">باقة الاشتراك</span>
                <div class="flex justify-between text-sm text-gray-600 mt-4">
                    <span class="text-[#939393] text-xs">سعر الباقة</span>
                    <span id="sidebarPrice" class="font-bold text-[#3C74BE]">500 ريال</span>
                </div>
                <div class="flex justify-between text-sm text-gray-600 mb-3 mt-2">
                    <span class="text-[#939393] text-xs">رسوم المعاملة</span>
                    <span class="text-[#00B503] font-bold">مجانا</span>
                </div>
                <div class="border-t my-5"></div>
                <div class="flex justify-between items-center">
                    <span class="text-sm">الإجمالي</span>
                    <span id="sidebarTotal" class="text-[#3C74BE] text-2xl font-bold">500 ريال</span>
                </div>
            </div>
        </aside>

        <div class="lg:col-span-2 order-2 lg:order-1">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold mb-4">الدفع</h2>
                <div id="paymentLoading" class="flex items-center justify-center py-12">
                    <div class="flex flex-col items-center gap-4">
                        <div class="relative">
                            <div class="w-12 h-12 border-4 border-[#3C74BE]/20 rounded-full"></div>
                            <div class="absolute top-0 left-0 w-12 h-12 border-4 border-transparent border-t-[#3C74BE] rounded-full animate-spin"></div>
                        </div>
                        <span class="text-[#3C74BE] font-medium">جاري تحميل معلومات البنك...</span>
                    </div>
                </div>
                <div id="paymentContent" class="w-full flex flex-col gap-6 hidden">
                    <div class="bg-[#3C74BE]/5 rounded-xl p-6">
                        <h3 class="text-base font-semibold mb-4 text-gray-800">معلومات الحساب البنكي</h3>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">رقم الحساب البنكي</label>
                            <div class="flex items-center gap-3 bg-white rounded-lg p-4 border border-gray-200">
                                <span id="bankAccountNumber" class="text-2xl font-bold text-[#3C74BE]">1234567890</span>
                                <button type="button" id="copyAccountBtn" class="mr-auto text-[#3C74BE] hover:opacity-80 transition-colors">
                                    <span class="iconify" data-icon="mdi:content-copy" style="font-size: 24px;"></span>
                                </button>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-3">رمز الباركود</label>
                            <div class="bg-white rounded-xl p-2 border-2 border-[#3C74BE]/30 flex flex-col items-center justify-center shadow-md">
                                <div class="bg-white p-2 rounded-lg"><canvas id="qrCodeCanvas" class="rounded-lg"></canvas></div>
                                <p class="text-xs text-gray-600 mt-4 text-center font-medium">يمكنك مسح هذا الباركود لتسهيل عملية الدفع</p>
                            </div>
                        </div>
                        <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-start gap-2">
                                <span class="iconify" data-icon="mdi:information" style="font-size: 20px; color: #2563eb;"></span>
                                <p class="text-sm text-blue-800">قم بتحويل المبلغ <span id="paymentAmount">500</span> ريال إلى الحساب البنكي أعلاه ثم قم برفع إيصال التحويل في الحقل أدناه</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-xl p-6">
                        <h3 class="text-base font-semibold mb-4 text-gray-800">رفع إيصال الدفع</h3>
                        <div class="mb-4">
                            <input type="file" accept="image/*" id="paymentReceiptInput" class="hidden" />
                            <div id="uploadArea" class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-[#3C74BE]/30 rounded-xl cursor-pointer hover:border-[#3C74BE] hover:bg-gray-50 transition-colors">
                                <span class="iconify" data-icon="mdi:cloud-upload-outline" style="font-size: 48px; color: #9ca3af;"></span>
                                <span class="text-sm text-gray-600 mt-2">انقر لاختيار ملف أو اسحب الملف هنا</span>
                                <span class="text-xs text-gray-400">PNG, JPG, GIF حتى 5 ميجابايت</span>
                            </div>
                            <div id="filePreview" class="hidden relative w-full border-2 border-[#3C74BE] rounded-xl p-4 bg-gray-50 mt-2">
                                <div id="previewImageContainer" class="relative w-full h-48 mb-3"></div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="iconify" data-icon="mdi:file-image" style="font-size: 24px; color: #3C74BE;"></span>
                                        <span id="fileName" class="text-sm text-gray-700"></span>
                                        <span id="fileSize" class="text-xs text-gray-500"></span>
                                    </div>
                                    <button type="button" id="removeFileBtn" class="text-red-500 hover:text-red-700"><span class="iconify" data-icon="mdi:delete" style="font-size: 24px;"></span></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="paymentError" class="hidden p-3 bg-red-50 border border-red-200 rounded-lg text-red-600 text-sm"></div>
                    <a href="#" id="completePaymentBtn" class="w-full mt-6 text-center text-white bg-gradient-to-r from-[#3778BC] to-[#4BAEE5] hover:opacity-90 transition-opacity py-3 rounded-lg disabled:opacity-50 inline-block">إتمام الدفع</a>
                </div>
                <div id="paymentErrorState" class="hidden p-6 bg-red-50 border border-red-200 rounded-lg text-red-600">
                    <p>فشل في تحميل معلومات البنك. يرجى المحاولة مرة أخرى.</p>
                    <button onclick="window.location.reload()" class="mt-3 text-sm underline">إعادة تحميل الصفحة</button>
                </div>
            </div>
        </div>
    </div>
</main>

@push('scripts')
<script src="https://code.iconify.design/3/3.1.1/iconify.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script>
(function() {
    const urlParams = new URLSearchParams(window.location.search);
    const orderId = urlParams.get('orderId') || 'ORDER' + Date.now();
    const amount = urlParams.get('amount') || 500;
    let bankInfo = { bankAccountNumber: 'SA0380000000608010167519' };
    let selectedFile = null;

    document.getElementById('sidebarPrice').textContent = amount + ' ريال';
    document.getElementById('sidebarTotal').textContent = amount + ' ريال';
    document.getElementById('paymentAmount').textContent = amount;

    const paymentLoading = document.getElementById('paymentLoading');
    const paymentContent = document.getElementById('paymentContent');
    const paymentErrorState = document.getElementById('paymentErrorState');

    function loadBankInfo() {
        paymentLoading.classList.remove('hidden');
        paymentContent.classList.add('hidden');
        paymentErrorState.classList.add('hidden');
        setTimeout(function() {
            document.getElementById('bankAccountNumber').textContent = bankInfo.bankAccountNumber;
            if (typeof QRCode !== 'undefined') {
                QRCode.toCanvas(document.getElementById('qrCodeCanvas'), bankInfo.bankAccountNumber, { width: 200, margin: 2 }, function(err) { if (err) console.error(err); });
            }
            paymentLoading.classList.add('hidden');
            paymentContent.classList.remove('hidden');
        }, 800);
    }

    document.getElementById('uploadArea').addEventListener('click', function() { document.getElementById('paymentReceiptInput').click(); });
    document.getElementById('paymentReceiptInput').addEventListener('change', function(e) {
        const file = e.target.files && e.target.files[0];
        if (!file || !file.type.startsWith('image/')) return;
        if (file.size > 5 * 1024 * 1024) { alert('حجم الملف كبير جداً. الحد الأقصى 5 ميجابايت'); return; }
        selectedFile = file;
        const reader = new FileReader();
        reader.onloadend = function() {
            document.getElementById('previewImageContainer').innerHTML = '<img src="' + reader.result + '" alt="Preview" class="w-full h-full object-contain rounded-lg" />';
            document.getElementById('uploadArea').classList.add('hidden');
            document.getElementById('filePreview').classList.remove('hidden');
            document.getElementById('fileName').textContent = file.name;
            document.getElementById('fileSize').textContent = '(' + (file.size / 1024).toFixed(2) + ' KB)';
            document.getElementById('completePaymentBtn').classList.remove('disabled');
        };
        reader.readAsDataURL(file);
    });
    document.getElementById('removeFileBtn').addEventListener('click', function() {
        selectedFile = null;
        document.getElementById('paymentReceiptInput').value = '';
        document.getElementById('uploadArea').classList.remove('hidden');
        document.getElementById('filePreview').classList.add('hidden');
        document.getElementById('completePaymentBtn').classList.add('disabled');
    });
    document.getElementById('copyAccountBtn').addEventListener('click', function() {
        navigator.clipboard.writeText(bankInfo.bankAccountNumber);
        alert('تم نسخ رقم الحساب');
    });
    document.getElementById('completePaymentBtn').addEventListener('click', function(e) {
        e.preventDefault();
        if (!selectedFile) { alert('يرجى رفع إيصال الدفع'); return; }
        const btn = this;
        btn.classList.add('opacity-50', 'pointer-events-none');
        btn.textContent = 'جاري المعالجة...';
        setTimeout(function() {
            window.location.href = '{{ route("site.processing") }}?orderId=' + encodeURIComponent(orderId);
        }, 1000);
    });

    loadBankInfo();
})();
</script>
@endpush
@endsection
