@props([
    'label' => '',
    'value' => '',
    'highlight' => false,
    'showCurrency' => false,
    'valueClass' => '',
    'showMinus' => false,
    'isBold' => false,
])

@php
    $isRtl = app()->getLocale() == 'ar';
    $rowClasses = 'summary-row';
    if ($highlight) {
        $rowClasses .= ' summary-row-highlight';
    }
    if ($isBold) {
        $rowClasses .= ' summary-row-strong';
    }

    $displayValue = $value;
    if ($showMinus && is_numeric($value)) {
        $displayValue = abs($value);
    }
@endphp

{{-- Two-cell table keeps label and value on opposite ends across mPDF --}}
<table class="{{ trim($rowClasses) }}">
    <tr>
        <td style="text-align: {{ $isRtl ? 'right' : 'left' }};">{{ $label }}</td>
        <td class="summary-value {{ $valueClass }}" style="text-align: {{ $isRtl ? 'left' : 'right' }}; font-weight: 600;">
            @if($showMinus)
                <span class="minus">-</span>
            @endif
            {{ $displayValue }}
            @if($showCurrency)
                <x-riyal-icon size="sm" />
            @endif
        </td>
    </tr>
</table>
