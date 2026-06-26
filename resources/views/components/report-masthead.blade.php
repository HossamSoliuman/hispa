@props([
    'title' => '',
    'subtitle' => '',
    'settings' => [],
])

@php
    $isRtl = app()->getLocale() === 'ar';

    $companyName = $settings['title'] ?? ($settings['company_name'] ?? '');
    $address = $settings['address'] ?? '';
    $phone = $settings['phone'] ?? '';
    $email = $settings['email'] ?? '';
    $vat = $settings['vat_number'] ?? '';

    $startAlign = $isRtl ? 'right' : 'left';
    $endAlign = $isRtl ? 'left' : 'right';
@endphp

<style>
    /* Company masthead — Chromium (Browsershot) renders this as a normal
       in-flow block at the top of the document: bold company name + address
       in the start corner, tax number on the opposite corner (printed
       reference masthead). Uses flexbox now that the engine is a real browser. */
    .rmast {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding-bottom: 12px;
    }
    .rmast-co { text-align: {{ $startAlign }}; }
    .rmast-meta { text-align: {{ $endAlign }}; min-width: 165px; }

    .rmast-name { font-size: 15pt; font-weight: 800; color: #1a1a1a; margin-bottom: 4px; }
    .rmast-line { font-size: 8.5pt; color: #555; line-height: 1.6; }
    .rmast-meta-label { font-size: 8pt; color: #888; margin-bottom: 1px; }
    .rmast-meta-value { font-size: 9.5pt; font-weight: 700; color: #1a1a1a; }

    /* Centered report title. */
    .rtitle-wrap { text-align: center; margin: 6px 0 18px; }
    .rtitle { font-size: 20pt; font-weight: 800; color: #1a1a1a; margin-bottom: 4px; }
    .rsubtitle { font-size: 10pt; color: #666; }
</style>

<div class="rmast">
    <div class="rmast-co">
        @if($companyName)<div class="rmast-name">{{ $companyName }}</div>@endif
        @if($address)<div class="rmast-line">{!! nl2br(e($address)) !!}</div>@endif
        @if($phone)<div class="rmast-line">{{ __('owner.reports.tel') }} {{ $phone }}</div>@endif
        @if($email)<div class="rmast-line">{{ $email }}</div>@endif
    </div>
    <div class="rmast-meta">
        @if($vat)
            <div class="rmast-meta-label">{{ __('owner.reports.vat_label') }}</div>
            <div class="rmast-meta-value">{{ $vat }}</div>
        @endif
    </div>
</div>

@if($title)
    <div class="rtitle-wrap">
        <div class="rtitle">{{ $title }}</div>
        @if($subtitle)<div class="rsubtitle">{{ $subtitle }}</div>@endif
    </div>
@endif
