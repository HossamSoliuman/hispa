@props([
    'showArrow' => true,
    'cardClass' => 'card border-0 shadow-sm',
    'bodyClass' => '',
])

<div class="{{ $cardClass }}">
    @if($showArrow)
        @include('owner.partials._card_arrow')
    @endif

    @isset($header)
    <div class="card-header bg-light border-0">
        {{ $header }}
    </div>
    @endisset

    <div class="card-body {{ $bodyClass }}">
        {{ $slot }}
    </div>
</div>
