@props([
    'amount' => 0,
    'decimals' => 2,
])

{{-- Monetary amount followed by the Saudi Riyal glyph. All sizing/alignment
     of the icon lives in the shared `.money` CSS in report-layout, so the look
     is tuned in one place for every report. --}}
<span class="money">{{ number_format((float) $amount, $decimals) }}<x-riyal-icon class="money-riyal" /></span>
