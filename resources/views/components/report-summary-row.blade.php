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

<div class="{{ trim($rowClasses) }}">
    <span>{{ $label }}</span>
    <span class="summary-value {{ $valueClass }}">
        @if($showMinus)
            <span class="minus">-</span>
        @endif
        {{ $displayValue }}
        @if($showCurrency)
            <x-riyal-icon size="sm" />
        @endif
    </span>
</div>
