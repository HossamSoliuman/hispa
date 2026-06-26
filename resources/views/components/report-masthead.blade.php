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
    /* Company masthead — table layout (mPDF: no flex/grid). Bold company name +
       address in the start corner, CR number on the opposite corner (printed
       reference masthead). */
    table.rmast { width: 100%; border-collapse: collapse; border-bottom: 2px solid #1a1a1a; margin-bottom: 4px; }
    table.rmast > tbody > tr > td { border: none; padding: 0 0 10px; vertical-align: top; }
    td.rmast-co { text-align: {{ $startAlign }}; }
    td.rmast-meta { text-align: {{ $endAlign }}; width: 165px; }
    table.rmast-inner { border-collapse: collapse; }
    table.rmast-inner td { border: none; padding: 0; vertical-align: middle; }
    td.rmast-logo-cell { padding-{{ $endAlign }}: 12px; }
    .rmast-logo { max-height: 60px; max-width: 130px; }
    .rmast-co-text { text-align: {{ $startAlign }}; }

    .rmast-name { font-size: 15pt; font-weight: 800; color: #1a1a1a; margin-bottom: 4px; }
    .rmast-line { font-size: 8.5pt; color: #555; line-height: 1.6; }
    .rmast-meta-label { font-size: 8pt; color: #888; margin-bottom: 1px; }
    .rmast-meta-value { font-size: 9.5pt; font-weight: 700; color: #1a1a1a; }

    /* Centered report title. */
    .rtitle-wrap { text-align: center; margin: 6px 0 18px; }
    .rtitle { font-size: 20pt; font-weight: 800; color: #1a1a1a; margin-bottom: 4px; }
    .rsubtitle { font-size: 10pt; color: #666; }
</style>

<table class="rmast">
    <tr>
        <td class="rmast-co">
            <table class="rmast-inner">
                <tr>
                    @if($logoData)<td class="rmast-logo-cell"><img class="rmast-logo" src="{{ $logoData }}" alt=""></td>@endif
                    <td class="rmast-co-text">
                        @if($companyName)<div class="rmast-name">{{ $companyName }}</div>@endif
                        @if($address)<div class="rmast-line">{!! nl2br(e($address)) !!}</div>@endif
                        @if($phone)<div class="rmast-line">{{ __('owner.reports.tel') }} {{ $phone }}</div>@endif
                    </td>
                </tr>
            </table>
        </td>
        <td class="rmast-meta">
            @if($crNumber)
                <div class="rmast-meta-label">{{ __('owner.reports.cr_label') }}</div>
                <div class="rmast-meta-value">{{ $crNumber }}</div>
            @endif
        </td>
    </tr>
</table>

@if($title)
    <div class="rtitle-wrap">
        <div class="rtitle">{{ $title }}</div>
        @if($subtitle)<div class="rsubtitle">{{ $subtitle }}</div>@endif
    </div>
@endif
