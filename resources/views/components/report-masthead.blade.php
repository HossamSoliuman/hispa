@props([
    'title' => '',
    'subtitle' => '',
    'settings' => [],
])

@php
    $isRtl = app()->getLocale() === 'ar';

    $companyName = $settings['company_name'] ?? '';
    // Show both identities; fall back across languages so neither side is blank
    // when an owner has only filled one name.
    $nameAr = ($settings['name_ar'] ?? '') ?: (($settings['name_en'] ?? '') ?: $companyName);
    $nameEn = ($settings['name_en'] ?? '') ?: (($settings['name_ar'] ?? '') ?: $companyName);
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
    /* Company masthead — three columns: English identity (left), centered logo,
       Arabic identity (right). Rendered by Chromium, so flexbox is available.
       A single thin rule closes the band off from the report body. */
    .rmast {
        display: flex; align-items: center; justify-content: space-between;
        gap: 14px; padding-bottom: 9px; margin-bottom: 6px;
        border-bottom: 1px solid #1a1a1a;
        /* Pin English to the far left and Arabic to the far right regardless of
           the report locale; without this the RTL page flips the DOM order. */
        direction: ltr;
    }
    .rmast-side { flex: 1 1 0; min-width: 0; }
    .rmast-en { text-align: left; direction: ltr; }
    .rmast-ar { text-align: right; direction: rtl; }
    .rmast-logo-wrap { flex: 0 0 auto; text-align: center; padding: 0 6px; }
    .rmast-logo { max-height: 62px; max-width: 132px; }

    .rmast-name { font-size: 13pt; font-weight: 800; color: #1a1a1a; line-height: 1.25; margin-bottom: 4px; }

    /* Compact key/value facts (licence numbers, phone, email). The label sits
       muted next to a bold value; the colon hugs the label in either script. */
    .rmast-facts { font-size: 8pt; color: #1a1a1a; line-height: 1.55; overflow-wrap: anywhere; }
    .rmast-facts .rf { display: block; }
    .rmast-facts .rf-label { color: #888; font-weight: 500; }
    .rmast-facts .rf-value { font-weight: 700; color: #1a1a1a; }
    .rmast-facts .rf-label::after { content: ': '; }

    /* Centered report title. */
    .rtitle-wrap { text-align: center; margin: 6px 0 18px; }
    .rtitle { font-size: 20pt; font-weight: 800; color: #1a1a1a; margin-bottom: 4px; }
    .rsubtitle { font-size: 10pt; color: #666; }
</style>

<div class="rmast">
    {{-- English identity --}}
    <div class="rmast-side rmast-en">
        @if($nameEn)<div class="rmast-name">{{ $nameEn }}</div>@endif
        <div class="rmast-facts">
            @if($crNumber)<span class="rf"><span class="rf-label">Commercial Reg.</span><span class="rf-value">{{ $crNumber }}</span></span>@endif
            @if($recordNumber)<span class="rf"><span class="rf-label">Agri. Record</span><span class="rf-value">{{ $recordNumber }}</span></span>@endif
            @if($vat)<span class="rf"><span class="rf-label">VAT</span><span class="rf-value">{{ $vat }}</span></span>@endif
            @if($phone)<span class="rf"><span class="rf-label">Tel</span><span class="rf-value">{{ $phone }}</span></span>@endif
            @if($email)<span class="rf"><span class="rf-label">Email</span><span class="rf-value">{{ $email }}</span></span>@endif
        </div>
    </div>

    {{-- Centered logo --}}
    @if($logoData)
        <div class="rmast-logo-wrap"><img class="rmast-logo" src="{{ $logoData }}" alt=""></div>
    @endif

    {{-- Arabic identity --}}
    <div class="rmast-side rmast-ar">
        @if($nameAr)<div class="rmast-name">{{ $nameAr }}</div>@endif
        <div class="rmast-facts">
            @if($crNumber)<span class="rf"><span class="rf-label">السجل التجاري</span><span class="rf-value">{{ $crNumber }}</span></span>@endif
            @if($recordNumber)<span class="rf"><span class="rf-label">السجل الزراعي</span><span class="rf-value">{{ $recordNumber }}</span></span>@endif
            @if($vat)<span class="rf"><span class="rf-label">الرقم الضريبي</span><span class="rf-value">{{ $vat }}</span></span>@endif
            @if($phone)<span class="rf"><span class="rf-label">الهاتف</span><span class="rf-value">{{ $phone }}</span></span>@endif
            @if($email)<span class="rf"><span class="rf-label">البريد الإلكتروني</span><span class="rf-value">{{ $email }}</span></span>@endif
        </div>
    </div>
</div>

@if($title)
    <div class="rtitle-wrap">
        <div class="rtitle">{{ $title }}</div>
        @if($subtitle)<div class="rsubtitle">{{ $subtitle }}</div>@endif
    </div>
@endif
