@php
    // Determine if the user has an uploaded raw logo (stored filename). The model accessor
    // `getLogoAttribute` returns a ui-avatar URL when there is no uploaded file, so we
    // must check the raw original attribute to know if an upload exists.
    $rawLogo = $user->getRawOriginal('logo');
    $profileUrl = route('owner.crew.show', $user->id);
    $initial = strtoupper(mb_substr($user->name ?? 'U', 0, 1));
    $colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
    $color = $colors[$user->id % count($colors)];
@endphp

<div class="d-flex align-items-center">
    <a href="{{ $profileUrl }}" class="d-flex align-items-center text-decoration-none">
        @if($rawLogo)
            <img src="{{ asset($user->logo) }}" alt="logo" width="30" height="30" class="rounded-circle me-2">
        @else
            <div class="avatar-circle bg-{{ $color }} bg-opacity-10 text-{{ $color }} me-2" style="width:30px;height:30px;font-size:0.9rem;display:inline-flex;align-items:center;justify-content:center;border-radius:50%;">
                {{ $initial }}
            </div>
        @endif

        <span>{{ $user->name }}</span>
    </a>
</div>
