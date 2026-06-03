@props([
    'buttons' => [],
    'alignment' => 'end', // start, end, center, between, around
    'gap' => '2',
    'wrapperClass' => '',
    'size' => 'md', // sm, md, lg
    'variant' => 'default', // default, compact, icon-only
])

{{--
/**
 * Owner Dashboard Action Buttons Component
 *
 * Standardized button group with consistent styling and alignment
 *
 * @usage
 * <x-owner.action-buttons
 *     :buttons="[
 *         [
 *             'text' => 'إضافة جديد',
 *             'icon' => 'bi-plus-lg',
 *             'type' => 'button',
 *             'class' => 'btn-primary',
 *             'modal' => '#addModal',
 *             'url' => null,
 *             'onclick' => null,
 *             'permission' => 'boats.create',
 *             'badge' => '5',
 *             'tooltip' => 'إضافة قارب جديد'
 *         ],
 *         [
 *             'text' => 'طباعة',
 *             'icon' => 'bi-printer',
 *             'type' => 'submit',
 *             'form' => 'printForm',
 *             'class' => 'btn-outline-secondary'
 *         ]
 *     ]"
 *     alignment="end"
 *     gap="2"
 *     size="md"
 * />
 */
--}}

@php
$alignmentClass = match($alignment) {
    'start' => 'justify-content-start',
    'center' => 'justify-content-center',
    'between' => 'justify-content-between',
    'around' => 'justify-content-around',
    default => 'justify-content-end',
};

$sizeClass = match($size) {
    'sm' => 'btn-sm',
    'lg' => 'btn-lg',
    default => '',
};
@endphp

<div class="action-buttons-wrapper d-flex flex-wrap align-items-center {{ $alignmentClass }} gap-{{ $gap }} {{ $wrapperClass }}">
    @foreach($buttons as $button)
        @php
        $btnType = $button['type'] ?? 'button';
        $btnClass = $button['class'] ?? 'btn-primary';
        $btnText = $button['text'] ?? '';
        $btnIcon = $button['icon'] ?? null;
        $btnModal = $button['modal'] ?? null;
        $btnUrl = $button['url'] ?? null;
        $btnOnclick = $button['onclick'] ?? null;
        $btnForm = $button['form'] ?? null;
        $btnPermission = $button['permission'] ?? null;
        $btnBadge = $button['badge'] ?? null;
        $btnTooltip = $button['tooltip'] ?? null;
        $btnId = $button['id'] ?? null;
        $btnDisabled = $button['disabled'] ?? false;
        $btnAttributes = $button['attributes'] ?? '';

        // Check permission if specified
        if ($btnPermission && !auth()->user()->can($btnPermission)) {
            continue;
        }

        // Determine element type
        $element = $btnUrl ? 'a' : 'button';

        // Build attributes
        $attrs = [];
        if ($btnId) $attrs[] = "id=\"{$btnId}\"";
        if ($btnModal) $attrs[] = "data-bs-toggle=\"modal\" data-bs-target=\"{$btnModal}\"";
        if ($btnUrl) $attrs[] = "href=\"{$btnUrl}\"";
        if ($btnOnclick) $attrs[] = "onclick=\"{$btnOnclick}\"";
        if ($btnForm) $attrs[] = "form=\"{$btnForm}\"";
        if ($btnType && !$btnUrl) $attrs[] = "type=\"{$btnType}\"";
        if ($btnDisabled) $attrs[] = "disabled";
        if ($btnTooltip) $attrs[] = "data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" title=\"{$btnTooltip}\"";

        $attrsStr = implode(' ', $attrs);
        @endphp

        @if($variant === 'icon-only')
            {{-- Icon-only button --}}
            <{!! $element !!}
                class="btn {{ $btnClass }} {{ $sizeClass }} btn-icon btn-equal d-inline-flex align-items-center justify-content-center position-relative"
                {!! $attrsStr !!}
                {!! $btnAttributes !!}>
                @if($btnIcon)
                    <i class="{{ $btnIcon }}"></i>
                @endif
                @if($btnBadge)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        {{ $btnBadge }}
                    </span>
                @endif
            </{!! $element !!}>
        @elseif($variant === 'compact')
            {{-- Compact button (icon + text, smaller) --}}
            <{!! $element !!}
                class="btn {{ $btnClass }} btn-sm btn-equal d-inline-flex align-items-center gap-1 position-relative"
                {!! $attrsStr !!}
                {!! $btnAttributes !!}>
                @if($btnIcon)
                    <i class="{{ $btnIcon }} small"></i>
                @endif
                <span>{{ $btnText }}</span>
                @if($btnBadge)
                    <span class="badge bg-white bg-opacity-25 ms-1">{{ $btnBadge }}</span>
                @endif
            </{!! $element !!}>
        @else
            {{-- Default button (icon + text) --}}
            <{!! $element !!}
                class="btn {{ $btnClass }} {{ $sizeClass }} btn-equal d-inline-flex align-items-center gap-2 position-relative"
                {!! $attrsStr !!}
                {!! $btnAttributes !!}>
                @if($btnIcon)
                    <i class="{{ $btnIcon }}"></i>
                @endif
                <span>{{ $btnText }}</span>
                @if($btnBadge)
                    <span class="badge bg-white bg-opacity-25 ms-1">{{ $btnBadge }}</span>
                @endif
            </{!! $element !!}>
        @endif
    @endforeach

    {{-- Custom slot for additional buttons --}}
    {{ $slot }}
</div>

<style>
    /* Ensure all buttons in group have equal height */
    .action-buttons-wrapper .btn-equal {
        min-height: 38px;
        padding: 0.375rem 0.75rem;
        line-height: 1.5;
    }

    .action-buttons-wrapper .btn-sm.btn-equal {
        min-height: 32px;
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .action-buttons-wrapper .btn-lg.btn-equal {
        min-height: 48px;
        padding: 0.5rem 1rem;
        font-size: 1.125rem;
    }

    /* Icon-only buttons should be square */
    .action-buttons-wrapper .btn-icon {
        width: 38px;
        height: 38px;
        padding: 0;
    }

    .action-buttons-wrapper .btn-icon.btn-sm {
        width: 32px;
        height: 32px;
    }

    .action-buttons-wrapper .btn-icon.btn-lg {
        width: 48px;
        height: 48px;
    }

    /* Badge positioning */
    .action-buttons-wrapper .position-absolute.badge {
        font-size: 0.625rem;
        padding: 0.25rem 0.4rem;
        z-index: 1;
    }

    /* Hover effects */
    .action-buttons-wrapper .btn {
        transition: all 0.2s ease;
    }

    .action-buttons-wrapper .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    /* Disabled state */
    .action-buttons-wrapper .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none !important;
    }

    /* Responsive: Stack on mobile */
    @media (max-width: 576px) {
        .action-buttons-wrapper {
            width: 100%;
        }

        .action-buttons-wrapper .btn:not(.btn-icon) {
            flex: 1;
            min-width: auto;
        }
    }
</style>

<script>
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    });
</script>
