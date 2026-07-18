@extends('site.layouts.app')

@section('title', __('site.payment.title') . ' - ' . __('site.meta.title'))

@php
    $package = $invoice?->subscription?->package;
    $total = $invoice ? (float) $invoice->total_amount : 0;
    $currency = __('site.pricing.currency');
    $hasBank = $bank['account_number'] !== '' || $bank['bank_name'] !== '' || $bank['account_name'] !== '';
@endphp

@section('content')
<main class="bg-transparent py-10 md:py-14">
    <div class="site-shell">
        <div class="mx-auto max-w-4xl">
            <div class="grid grid-cols-[1fr_auto_1fr_auto_1fr] items-start gap-2" aria-label="{{ __('marketing.checkout.step_label', ['current' => 2]) }}">
                @foreach([
                    ['number' => 1, 'label' => __('marketing.checkout.steps.plan'), 'state' => 'done'],
                    ['number' => 2, 'label' => __('marketing.checkout.steps.payment'), 'state' => 'active'],
                    ['number' => 3, 'label' => __('marketing.checkout.steps.dashboard'), 'state' => 'next'],
                ] as $step)
                    <div class="flex flex-col items-center text-center">
                        <span @class([
                            'grid h-9 w-9 place-items-center rounded-full text-xs font-extrabold',
                            'bg-tide text-white' => $step['state'] === 'done',
                            'bg-ink text-white shadow-lg' => $step['state'] === 'active',
                            'border border-ink/15 bg-white text-ink/38' => $step['state'] === 'next',
                        ])>
                            @if($step['state'] === 'done')
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M5 12l4 4L19 6" /></svg>
                            @else
                                {{ $step['number'] }}
                            @endif
                        </span>
                        <span @class(['mt-2 text-[0.62rem] font-bold sm:text-xs', 'text-ink' => $step['state'] === 'active', 'text-ink/42' => $step['state'] !== 'active'])>{{ $step['label'] }}</span>
                    </div>
                    @if(! $loop->last)
                        <span @class(['mt-[1.1rem] h-px w-full', 'bg-tide' => $step['state'] === 'done', 'bg-ink/12' => $step['state'] !== 'done'])></span>
                    @endif
                @endforeach
            </div>
        </div>

        @if(! $invoice)
            <div class="hud-panel mx-auto mt-12 max-w-xl p-8 text-center">
                <span class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-mist text-ocean">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M9 12h6m-3-3v6m9-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                </span>
                <h1 class="mt-5 text-xl font-extrabold text-ink">{{ __('marketing.payment.no_invoice_title') }}</h1>
                <p class="mt-2 text-sm leading-7 text-ink/55">{{ __('marketing.payment.no_invoice_description') }}</p>
                <a href="{{ route('landing-page') }}#pricing" class="mt-6 inline-flex bg-ocean px-5 py-3 text-sm font-bold text-white hover:bg-ocean-deep">{{ __('marketing.payment.choose_plan') }}</a>
            </div>
        @else
            <div class="mx-auto mt-10 max-w-5xl">
                <div class="text-center">
                    <span class="eyebrow text-ocean">{{ __('marketing.payment.eyebrow') }}</span>
                    <h1 class="mt-4 text-3xl font-extrabold tracking-[-0.04em] text-ink md:text-4xl">{{ __('marketing.payment.title') }}</h1>
                    <p class="mx-auto mt-4 max-w-2xl text-sm leading-7 text-ink/55">{{ __('marketing.payment.description') }}</p>
                </div>

                @if($errors->any())
                    <div class="mx-auto mt-6 max-w-3xl rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-start text-sm text-red-700" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('site.payment.store') }}" method="post" enctype="multipart/form-data" id="paymentForm" class="mt-10 grid items-start gap-6 lg:grid-cols-[1.15fr_.85fr]">
                    @csrf

                    <section class="hud-panel p-6 sm:p-8">
                        <h2 class="text-base font-extrabold text-ink">{{ __('marketing.payment.bank_info_title') }}</h2>

                        @if(! $hasBank)
                            <div class="mt-5 rounded-xl border border-amber-300/60 bg-amber-50 px-4 py-3 text-start text-sm text-amber-800">
                                {{ __('marketing.payment.not_configured') }}
                            </div>
                        @else
                            <dl class="mt-5 grid gap-4">
                                @if($bank['bank_name'] !== '')
                                    <div class="flex items-center justify-between gap-4 border-b border-ink/8 pb-4">
                                        <dt class="text-xs font-bold text-ink/48">{{ __('marketing.payment.bank_name') }}</dt>
                                        <dd class="text-sm font-extrabold text-ink">{{ $bank['bank_name'] }}</dd>
                                    </div>
                                @endif
                                @if($bank['account_name'] !== '')
                                    <div class="flex items-center justify-between gap-4 border-b border-ink/8 pb-4">
                                        <dt class="text-xs font-bold text-ink/48">{{ __('marketing.payment.account_holder') }}</dt>
                                        <dd class="text-sm font-extrabold text-ink">{{ $bank['account_name'] }}</dd>
                                    </div>
                                @endif
                                @if($bank['account_number'] !== '')
                                    <div>
                                        <dt class="text-xs font-bold text-ink/48">{{ __('marketing.payment.account_number') }}</dt>
                                        <dd class="mt-2 flex items-center gap-3 rounded-xl border border-ocean/15 bg-mist/60 p-4">
                                            <span id="accountNumber" class="text-lg font-extrabold tracking-wide text-ocean" dir="ltr">{{ $bank['account_number'] }}</span>
                                            <button type="button" id="copyAccountBtn" class="ms-auto inline-flex items-center gap-1.5 text-xs font-bold text-ocean hover:text-ocean-deep" data-copy="{{ $bank['account_number'] }}">
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="9" y="9" width="11" height="11" rx="2" /><path d="M5 15V5a2 2 0 0 1 2-2h10" /></svg>
                                                <span data-copy-label>{{ __('marketing.payment.copy') }}</span>
                                            </button>
                                        </dd>
                                    </div>
                                @endif
                            </dl>

                            @if($qrCode)
                                <div class="mt-6 flex flex-col items-center rounded-xl border border-ocean/15 bg-white p-5">
                                    <img src="{{ $qrCode }}" alt="{{ __('marketing.payment.account_number') }}" class="h-40 w-40" />
                                    <p class="mt-3 text-center text-xs text-ink/55">{{ __('marketing.payment.scan_hint') }}</p>
                                </div>
                            @endif

                            <div class="mt-6 flex items-start gap-3 rounded-xl border border-ocean/14 bg-mist/65 p-4 text-start">
                                <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-ocean text-white">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 8v4m0 4h.01" /><circle cx="12" cy="12" r="9" /></svg>
                                </span>
                                <p class="text-xs leading-6 text-ink/62">{{ __('marketing.payment.instructions', ['amount' => number_format($total, 0) . ' ' . $currency]) }}</p>
                            </div>

                            @if($bank['instructions'] !== '')
                                <p class="mt-3 whitespace-pre-line text-xs leading-6 text-ink/55">{{ $bank['instructions'] }}</p>
                            @endif
                        @endif

                        <div class="mt-8">
                            <h3 class="text-base font-extrabold text-ink">{{ __('marketing.payment.upload_title') }}</h3>
                            <input type="file" name="bank_transfer_receipt" id="receiptInput" accept="image/png,image/jpeg,image/gif" class="sr-only" required />

                            <label for="receiptInput" id="uploadArea" class="mt-4 flex cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed border-ocean/30 bg-white/60 px-6 py-10 text-center transition hover:border-ocean hover:bg-mist/40">
                                <span class="grid h-11 w-11 place-items-center rounded-full bg-mist text-ocean">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 16V4m0 0 4 4m-4-4-4 4" /><path d="M4 16v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2" /></svg>
                                </span>
                                <span class="text-sm font-bold text-ink/70">{{ __('marketing.payment.upload_hint') }}</span>
                                <span class="text-xs text-ink/42">{{ __('marketing.payment.upload_formats') }}</span>
                            </label>

                            <div id="filePreview" class="mt-4 hidden items-center gap-4 rounded-xl border border-ocean/25 bg-white p-4">
                                <img id="previewImage" src="" alt="" class="h-16 w-16 rounded-lg object-cover" />
                                <div class="min-w-0">
                                    <p id="fileName" class="truncate text-sm font-bold text-ink"></p>
                                    <p id="fileSize" class="text-xs text-ink/45"></p>
                                </div>
                                <button type="button" id="removeFileBtn" class="ms-auto inline-flex items-center gap-1 text-xs font-bold text-red-500 hover:text-red-700">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18" /></svg>
                                    {{ __('marketing.payment.remove') }}
                                </button>
                            </div>
                        </div>
                    </section>

                    <aside class="checkout-summary p-6 text-white sm:p-8 lg:sticky lg:top-28">
                        <p class="text-xs font-bold text-white/72">{{ __('marketing.payment.order_summary') }}</p>
                        <h2 class="mt-2 text-xl font-extrabold">{{ $package?->name ?? '—' }}</h2>

                        <dl class="mt-7 grid gap-4 text-sm">
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-white/52">{{ __('marketing.payment.plan_price') }}</dt>
                                <dd class="font-bold" dir="ltr">{{ number_format($total, 0) }} {{ $currency }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-white/52">{{ __('marketing.payment.fees') }}</dt>
                                <dd class="font-bold text-[#6ed0bd]">{{ __('marketing.payment.free') }}</dd>
                            </div>
                        </dl>

                        <div class="my-6 h-px bg-white/12"></div>

                        <div class="flex items-end justify-between gap-4">
                            <span class="text-sm font-bold text-white/72">{{ __('marketing.payment.total') }}</span>
                            <strong class="text-3xl font-extrabold" dir="ltr">{{ number_format($total, 0) }} {{ $currency }}</strong>
                        </div>

                        <button type="submit" id="completePaymentBtn" class="checkout-summary-action mt-7 inline-flex min-h-13 w-full items-center justify-center gap-2 rounded-xl px-5 py-3.5 text-center text-sm font-extrabold hover:-translate-y-0.5">
                            {{ __('marketing.payment.complete') }}
                            <svg class="h-4 w-4 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m9 18 6-6-6-6" /></svg>
                        </button>

                        <p class="mt-4 text-center text-[0.65rem] leading-5 text-white/45" dir="ltr">{{ $invoice->invoice_number }}</p>
                    </aside>
                </form>
            </div>
        @endif
    </div>
</main>

@push('scripts')
<script>
(function () {
    var input = document.getElementById('receiptInput');
    if (!input) { return; }

    var uploadArea = document.getElementById('uploadArea');
    var preview = document.getElementById('filePreview');
    var previewImage = document.getElementById('previewImage');
    var fileName = document.getElementById('fileName');
    var fileSize = document.getElementById('fileSize');
    var removeBtn = document.getElementById('removeFileBtn');

    function showFile(file) {
        var reader = new FileReader();
        reader.onload = function () {
            previewImage.src = reader.result;
            fileName.textContent = file.name;
            fileSize.textContent = (file.size / 1024).toFixed(1) + ' KB';
            uploadArea.classList.add('hidden');
            preview.classList.remove('hidden');
            preview.classList.add('flex');
        };
        reader.readAsDataURL(file);
    }

    input.addEventListener('change', function () {
        if (input.files && input.files[0]) { showFile(input.files[0]); }
    });

    removeBtn && removeBtn.addEventListener('click', function () {
        input.value = '';
        preview.classList.add('hidden');
        preview.classList.remove('flex');
        uploadArea.classList.remove('hidden');
    });

    ['dragenter', 'dragover'].forEach(function (evt) {
        uploadArea.addEventListener(evt, function (e) { e.preventDefault(); uploadArea.classList.add('border-ocean'); });
    });
    ['dragleave', 'drop'].forEach(function (evt) {
        uploadArea.addEventListener(evt, function (e) { e.preventDefault(); uploadArea.classList.remove('border-ocean'); });
    });
    uploadArea.addEventListener('drop', function (e) {
        if (e.dataTransfer.files && e.dataTransfer.files[0]) {
            input.files = e.dataTransfer.files;
            showFile(e.dataTransfer.files[0]);
        }
    });

    var copyBtn = document.getElementById('copyAccountBtn');
    if (copyBtn && navigator.clipboard) {
        copyBtn.addEventListener('click', function () {
            navigator.clipboard.writeText(copyBtn.dataset.copy).then(function () {
                var label = copyBtn.querySelector('[data-copy-label]');
                var original = label.textContent;
                label.textContent = @json(__('marketing.payment.copied'));
                setTimeout(function () { label.textContent = original; }, 1500);
            });
        });
    }
})();
</script>
@endpush
@endsection
