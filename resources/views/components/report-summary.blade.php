@props([
    'qrCode' => '',
    'summaryItems' => [],
])

<div class="bottom-section">
    @if($qrCode)
        <div class="qr-box">
            {{-- Controller passes complete data URL (data:image/png;base64,...) --}}
            <img src="{{ $qrCode }}" alt="QR Code">
        </div>
    @endif

    <div class="summary-box">
        {{ $slot }}
    </div>
</div>
