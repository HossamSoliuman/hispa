@props([
    'amount' => 0,
])

@php
    $formatted = number_format($amount, 0);
@endphp

{{ $formatted }} <x-riyal-icon class="opacity-75" style="width:0.85em;height:auto;" />