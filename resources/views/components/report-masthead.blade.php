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

    // Flat single-row layout (mPDF chokes on nested tables). A spacer cell fills
    // the middle so the company hugs the start corner and the VAT hugs the end
    // corner. Order the cells per direction to pin them to the physical corners.
    $cellOrder = $isRtl ? ['co', 'spacer', 'meta'] : ['meta', 'spacer', 'co'];
@endphp

<style>
    /* Company masthead — rendered as an mPDF running page header so it repeats
       on every page. Mirrors the printed reference: bold company name + address
       block in the start corner, tax number on the opposite corner. */
    @page {
        odd-header-name: html_reportMast;
        even-header-name: html_reportMast;
        margin-top: 26mm;
        margin-bottom: 12mm;
        margin-left: 10mm;
        margin-right: 10mm;
        margin-header: 8mm;
    }

    /* Company band — bold name + address grouped in the start corner, tax number
       on the opposite corner (printed reference masthead). */
    table.rmast { width: 100%; border-collapse: collapse; }
    table.rmast td { border: none; padding: 0 0 12px; }
    td.rmast-co { text-align: {{ $startAlign }}; vertical-align: top; white-space: nowrap; }
    td.rmast-spacer { padding: 0; }
    td.rmast-meta { width: 165px; text-align: {{ $endAlign }}; vertical-align: top; }

    .rmast-name { font-size: 15pt; font-weight: 800; color: #1a1a1a; margin-bottom: 4px; }
    .rmast-line { font-size: 8.5pt; color: #555; line-height: 1.6; }
    .rmast-meta-label { font-size: 8pt; color: #888; margin-bottom: 1px; }
    .rmast-meta-value { font-size: 9.5pt; font-weight: 700; color: #1a1a1a; }

    /* Centered report title — shown once in the content flow on the first page. */
    .rtitle-wrap { text-align: center; margin: 6px 0 18px; }
    .rtitle { font-size: 20pt; font-weight: 800; color: #1a1a1a; margin-bottom: 4px; }
    .rsubtitle { font-size: 10pt; color: #666; }
</style>

<htmlpageheader name="reportMast">
    <table class="rmast">
        <tr>
            @foreach($cellOrder as $cell)
                @if($cell === 'co')
                    <td class="rmast-co">
                        @if($companyName)<div class="rmast-name">{{ $companyName }}</div>@endif
                        @if($address)<div class="rmast-line">{!! nl2br(e($address)) !!}</div>@endif
                        @if($phone)<div class="rmast-line">{{ __('owner.reports.tel') }} {{ $phone }}</div>@endif
                        @if($email)<div class="rmast-line">{{ $email }}</div>@endif
                    </td>
                @elseif($cell === 'spacer')
                    <td class="rmast-spacer"></td>
                @else
                    <td class="rmast-meta">
                        @if($vat)
                            <div class="rmast-meta-label">{{ __('owner.reports.vat_label') }}</div>
                            <div class="rmast-meta-value">{{ $vat }}</div>
                        @endif
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
