@props([
    'qrCode' => '',
    'summaryItems' => [],
])

{{-- Table-based bottom section so mPDF keeps the QR and totals side by side --}}
<table class="bottom-section">
    <tr>
        @if($qrCode)
            <td class="qr-cell">
                <div class="qr-box">
                    {{-- Controller passes complete data URL (data:image/png;base64,...) --}}
                    <img src="{{ $qrCode }}" alt="QR Code">
                </div>
            </td>
        @endif
        <td class="summary-cell">
            <div class="summary-box">
                {{ $slot }}
            </div>
        </td>
    </tr>
</table>
