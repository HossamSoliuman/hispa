@props([
    'title' => '',
    'subtitle' => '',
    'settings' => [],
])

@php
    $isRtl = app()->getLocale() === 'ar';

    $logo = '';
    // $logoSetting = $settings['logo'] ?? '';
    // if (! empty($logoSetting) && \Illuminate\Support\Facades\Storage::disk('public')->exists($logoSetting)) {
    //     $logo = 'data:'.\Illuminate\Support\Facades\Storage::disk('public')->mimeType($logoSetting)
    //         .';base64,'.base64_encode(\Illuminate\Support\Facades\Storage::disk('public')->get($logoSetting));
    // } elseif (file_exists(public_path('default-logo.png'))) {
    //     $logo = 'data:image/png;base64,'.base64_encode(file_get_contents(public_path('default-logo.png')));
    // }

    $companyName = $settings['title'] ?? ($settings['company_name'] ?? '');
    $address = $settings['address'] ?? '';
    $phone = $settings['phone'] ?? '';
    $email = $settings['email'] ?? '';
    $vat = $settings['vat_number'] ?? '';

    $startAlign = $isRtl ? 'right' : 'left';

    // Keep the logo pinned to the physical top-right corner regardless of locale.
    // mPDF reverses table-column order for RTL, so order the cells per direction.
    $cellOrder = $isRtl ? ['logo', 'co', 'meta'] : ['meta', 'co', 'logo'];
@endphp

<style>
    /* Clean company masthead — rendered as an mPDF running page header so it
       repeats on every page. No background and no rules: the report reads clean
       from the first glance, matching the product-sales-summary reference. */
    @page {
        odd-header-name: html_reportMast;
        even-header-name: html_reportMast;
        margin-top: 36mm;
        margin-bottom: 12mm;
        margin-left: 10mm;
        margin-right: 10mm;
        margin-header: 7mm;
    }

    table.rmast { width: 100%; border-collapse: collapse; }
    table.rmast td { border: none; padding: 0; vertical-align: top; }
    td.rmast-logo { width: 78px; text-align: right; }
    td.rmast-logo img { height: 40px; width: auto; }
    td.rmast-co { text-align: {{ $startAlign }}; padding: 0 12px; }
    td.rmast-meta { width: 170px; text-align: left; }
    .rmast-name { font-size: 11pt; font-weight: 700; color: #1a1a1a; margin-bottom: 2px; }
    .rmast-line { font-size: 8pt; color: #7f8c8d; line-height: 1.55; }

    /* Centered report title — shown once in the content flow on the first page. */
    .rtitle-wrap { text-align: center; margin: 0 0 16px; }
    .rtitle { font-size: 17pt; font-weight: 700; color: #1a1a1a; margin-bottom: 3px; }
    .rsubtitle { font-size: 9.5pt; color: #7f8c8d; }
</style>

<htmlpageheader name="reportMast">
    <table class="rmast">
        <tr>
            @foreach($cellOrder as $cell)
                @if($cell === 'logo')
                    <td class="rmast-logo">
                        @if($logo)
                            {{-- mPDF ignores CSS height on images (esp. in running headers); size the tag itself. --}}
                            <img src="{{ $logo }}" alt="" height="40" style="height: 40px; width: auto;">
                        @endif
                    </td>
                @elseif($cell === 'co')
                    <td class="rmast-co">
                        @if($companyName)<div class="rmast-name">{{ $companyName }}</div>@endif
                        @if($address)<div class="rmast-line">{{ $address }}</div>@endif
                        @if($phone)<div class="rmast-line">{{ __('owner.reports.tel') }} {{ $phone }}</div>@endif
                        @if($email)<div class="rmast-line">{{ $email }}</div>@endif
                    </td>
                @else
                    <td class="rmast-meta">
                        @if($vat)
                            <div class="rmast-line">{{ __('owner.reports.vat_label') }}: {{ $vat }}</div>
                        @endif
                        <div class="rmast-line">{{ __('owner.reports.page_label') }} {PAGENO} {{ __('owner.reports.page_of') }} {nbpg}</div>
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
