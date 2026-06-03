@props([
    'amount' => 0,
])

@php
    $formatted = number_format($amount, 0);
@endphp

{{ $formatted }} <span style="font-size: 0.85em; opacity: 0.8;">ر.س</span>