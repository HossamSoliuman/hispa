@props(['amount' => 0, 'muted' => false, 'class' => ''])

<span class="d-inline-flex align-items-baseline">
    <span class="num">{{ number_format($amount, 2) }}</span>
    <span class="unit ms-1">
        @if($muted)
            <x-riyal-icon size="sm" class="text-muted {{ $class }}" />
        @else
            <x-riyal-icon size="sm" class="text-white {{ $class }}" />
        @endif
    </span>
</span>
