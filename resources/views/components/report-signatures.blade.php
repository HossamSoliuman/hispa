@props([
    'items' => [],
])

@php
    $labels = array_values(array_filter($items, fn ($label) => filled($label)));
@endphp

@if (count($labels))
    {{-- Signature strip — evenly spaced labels above dotted sign-off lines. --}}
    <table class="sig-table">
        <tr>
            @foreach ($labels as $label)
                <td style="width: {{ round(100 / count($labels), 4) }}%;">
                    <div class="sig-label">{{ $label }}</div>
                    <div class="sig-line">.............................</div>
                </td>
            @endforeach
        </tr>
    </table>
@endif
