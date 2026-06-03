@props([
    'headers' => [],
    'data' => [],
    'metadata' => null,
])

@if($metadata)
    <div class="metadata">
        {{ $metadata }}
    </div>
@endif

<table class="report-table table table-sm table-bordered mb-0">
    <thead>
        <tr>
            @foreach($headers as $header)
                @php
                    // Some translation helpers or callers may accidentally pass arrays —
                    // normalize to a string to avoid htmlspecialchars() TypeError.
                    if (is_array($header)) {
                        $headerText = implode(' ', array_filter($header, function($v){ return $v !== null && $v !== ''; }));
                    } else {
                        $headerText = $header;
                    }
                @endphp
                <th>{{ $headerText }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @if(count($data) > 0)
            {{ $slot }}
        @else
            <tr>
                <td colspan="{{ count($headers) }}" style="text-align: center; padding: 30px; color: #95a5a6;">
                    {{ __('dalal.reports.no_data') }}
                </td>
            </tr>
        @endif
    </tbody>
</table>
