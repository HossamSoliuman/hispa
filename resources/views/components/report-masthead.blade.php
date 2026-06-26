@props([
    'title' => '',
    'subtitle' => '',
    'settings' => [],
])

@php
    $isRtl = app()->getLocale() === 'ar';

    $companyName = $settings['company_name'] ?? '';
    $address = $settings['address'] ?? '';
    $phone = $settings['phone'] ?? '';
    $email = $settings['email'] ?? '';
    $website = $settings['website'] ?? '';
    $crNumber = $settings['cr_number'] ?? '';
    $recordNumber = $settings['record_number'] ?? '';
    $vat = $settings['vat_number'] ?? '';

    $startAlign = $isRtl ? 'right' : 'left';
    $endAlign = $isRtl ? 'left' : 'right';

    // Resolve the company logo to an inline data URI: Browsershot renders from
    // an HTML string with no base URL, so relative <img src> paths cannot load.
    // Stored logos live on the public disk; absolute URLs / data URIs pass through.
    $logo = $settings['logo'] ?? '';
    $logoData = '';
    if ($logo) {
        if (\Illuminate\Support\Str::startsWith($logo, ['data:', 'http://', 'https://'])) {
            $logoData = $logo;
        } else {
            $logoPath = \Illuminate\Support\Facades\Storage::disk('public')->path($logo);
            if (is_file($logoPath)) {
                $ext = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
                $mime = ['svg' => 'image/svg+xml', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp'][$ext] ?? 'image/jpeg';
                $logoData = 'data:'.$mime.';base64,'.base64_encode((string) file_get_contents($logoPath));
            }
        }
    }
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
        padding-bottom: 10px;
        margin-bottom: 4px;
        border-bottom: 2px solid #1a1a1a;
    }
    .rmast-co { display: flex; align-items: center; gap: 12px; text-align: {{ $startAlign }}; }
    .rmast-logo { max-height: 60px; max-width: 130px; object-fit: contain; }
    .rmast-co-text { text-align: {{ $startAlign }}; }
    .rmast-meta { text-align: {{ $endAlign }}; min-width: 165px; display: flex; flex-direction: column; gap: 6px; justify-content: flex-start; }

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
        @if($logoData)<img class="rmast-logo" src="{{ $logoData }}" alt="">@endif
        <div class="rmast-co-text">
            @if($companyName)<div class="rmast-name">{{ $companyName }}</div>@endif
            @if($address)<div class="rmast-line">{!! nl2br(e($address)) !!}</div>@endif
            @if($phone)<div class="rmast-line">{{ __('owner.reports.tel') }} {{ $phone }}</div>@endif
        </div>
    </div>
    <div class="rmast-meta">
        @if($crNumber)
            <div class="rmast-meta-label">{{ __('owner.reports.cr_label') }}</div>
            <div class="rmast-meta-value">{{ $crNumber }}</div>
        @endif
    </div>
</div>

@if($title)
    <div class="rtitle-wrap">
        <div class="rtitle">{{ $title }}</div>
        @if($subtitle)<div class="rsubtitle">{{ $subtitle }}</div>@endif
    </div>
@endif
