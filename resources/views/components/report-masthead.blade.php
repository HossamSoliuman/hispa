@props([
    'title' => '',
    'subtitle' => '',
    'settings' => [],
])

@php
    $isRtl = app()->getLocale() === 'ar';

    $logo = '';
    $logoSetting = $settings['logo'] ?? '';
    if (! empty($logoSetting) && \Illuminate\Support\Facades\Storage::disk('public')->exists($logoSetting)) {
        $logo = 'data:'.\Illuminate\Support\Facades\Storage::disk('public')->mimeType($logoSetting)
            .';base64,'.base64_encode(\Illuminate\Support\Facades\Storage::disk('public')->get($logoSetting));
    } elseif (file_exists(public_path('default-logo.png'))) {
        $logo = 'data:image/png;base64,'.base64_encode(file_get_contents(public_path('default-logo.png')));
    }

    $companyName = $settings['title'] ?? ($settings['company_name'] ?? '');
    $address = $settings['address'] ?? '';
    $phone = $settings['phone'] ?? '';
    $email = $settings['email'] ?? '';
    $vat = $settings['vat_number'] ?? '';

    $startAlign = $isRtl ? 'right' : 'left';
    $endAlign = $isRtl ? 'left' : 'right';

    // Keep the logo pinned to the physical top-corner regardless of locale.
    // mPDF reverses table-column order for RTL, so order the cells per direction.
    $cellOrder = $isRtl ? ['logo', 'co', 'meta'] : ['meta', 'co', 'logo'];
@endphp

<style>
    /* Company masthead — rendered as an mPDF running page header so it repeats
       on every page. Mirrors the printed reference: bold company name + address
       block hugging the logo, tax number + page count on the opposite side, and
       a hairline rule under the whole band. */
    @page {
        odd-header-name: html_reportMast;
        even-header-name: html_reportMast;
        margin-top: 34mm;
        margin-bottom: 12mm;
        margin-left: 10mm;
        margin-right: 10mm;
        margin-header: 8mm;
    }

    table.rmast { width: 100%; border-collapse: collapse; border-bottom: 1.2px solid #1a1a1a; }
    table.rmast td { border: none; padding: 0 0 8px; vertical-align: top; }
    td.rmast-logo { width: 96px; text-align: {{ $endAlign === 'left' ? 'left' : 'right' }}; }
    td.rmast-logo img { height: 48px; width: auto; }
    td.rmast-co { text-align: {{ $startAlign }}; padding: 0 14px 8px; }
    td.rmast-meta { width: 165px; text-align: {{ $endAlign }}; }
    .rmast-name { font-size: 13pt; font-weight: 700; color: #1a1a1a; margin-bottom: 3px; }
    .rmast-line { font-size: 8.5pt; color: #444; line-height: 1.5; }
    .rmast-meta-label { font-size: 8pt; color: #888; margin-bottom: 1px; }
    .rmast-meta-value { font-size: 9.5pt; font-weight: 700; color: #1a1a1a; margin-bottom: 6px; }
    .rmast-page { font-size: 8.5pt; color: #444; }

    /* Centered report title — shown once in the content flow on the first page. */
    .rtitle-wrap { text-align: center; margin: 4px 0 16px; }
    .rtitle { font-size: 18pt; font-weight: 700; color: #1a1a1a; margin-bottom: 3px; }
    .rsubtitle { font-size: 10pt; color: #666; }
</style>

<htmlpageheader name="reportMast">
    <table class="rmast">
        <tr>
            @foreach($cellOrder as $cell)
                @if($cell === 'logo')
                    <td class="rmast-logo">
                        @if($logo)
                            {{-- mPDF ignores CSS height on images in running headers; size the tag itself. --}}
                            <img src="{{ $logo }}" alt="" height="48" style="height: 48px; width: auto;">
                        @endif
                    </td>
                @elseif($cell === 'co')
                    <td class="rmast-co">
                        @if($companyName)<div class="rmast-name">{{ $companyName }}</div>@endif
                        @if($address)<div class="rmast-line">{!! nl2br(e($address)) !!}</div>@endif
                        @if($phone)<div class="rmast-line">{{ __('owner.reports.tel') }} {{ $phone }}</div>@endif
                        @if($email)<div class="rmast-line">{{ $email }}</div>@endif
                    </td>
                @else
                    <td class="rmast-meta">
                        @if($vat)
                            <div class="rmast-meta-label">{{ __('owner.reports.vat_label') }}</div>
                            <div class="rmast-meta-value">{{ $vat }}</div>
                        @endif
                        <div class="rmast-page">{{ __('owner.reports.page_label') }} {PAGENO} {{ __('owner.reports.page_of') }} {nbpg}</div>
                    </td>
                @endif
            @endforeach
        </tr>
    </table>
</htmlpageheader>

@if($title)
    <div class="rtitle-wrap">
        <div class="rtitle">{{ $title }}</div>
        @if($subtitle)<div class="rsubtitle">{{ $subtitle }}</div>@endif
    </div>
@endif
