@props([
    'items' => [],
])

@php
    // Drop any null/empty entries so callers can build the list conditionally.
    $cards = array_values(array_filter($items, fn ($item) => ! empty($item)));
@endphp

@if (count($cards))
    {{-- Table layout: mPDF does not support flex/grid/inline-block columns, so a
         single-row table is the only reliable way to keep stat cards horizontal. --}}
    <table class="report-stats">
        <tr>
            @foreach ($cards as $card)
                <td class="report-stat-card" @if (! empty($card['accent'])) style="border-top: 3px solid {{ $card['accent'] }};" @endif>
                    <div class="report-stat-label">{{ $card['label'] }}</div>
                    <div class="report-stat-value" @if (! empty($card['color'])) style="color: {{ $card['color'] }};" @endif>{!! $card['value'] !!}</div>
                </td>
            @endforeach
        </tr>
    </table>
@endif
