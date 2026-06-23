@props([
    'words' => '',
    'label' => null,
    'width' => '100%',
])

@php
    $caption = $label ?? __('owner.reports.amount_in_words');
@endphp

{{-- Amount-in-words box — black caption bar over the written amount. --}}
<table class="amount-words" style="width: {{ $width }};">
    <tr>
        <td class="aw-cap">{{ $caption }}</td>
    </tr>
    <tr>
        <td class="aw-text">{{ $words }}</td>
    </tr>
</table>
