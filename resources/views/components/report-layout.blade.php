@props([
    'title' => '',
    'titleEn' => '',
    'documentNumber' => '',
    'settings' => [],
    'qrCode' => '',
])

@php $isRtl = app()->getLocale() == 'ar'; @endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        /* This template is rendered to PDF by mPDF, which does not support
           flexbox / CSS grid, remote web fonts or icon fonts. Layout therefore
           relies on tables, inline-block and float, and uses mPDF's bundled
           fonts (auto-selected per script for correct Arabic shaping). */
        a, a:link, a:visited, a:hover { text-decoration: none; color: inherit; }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            direction: {{ $isRtl ? 'rtl' : 'ltr' }};
            color: #2c3e50;
            background: #fff;
            font-size: 10pt;
            line-height: 1.6;
        }

        /* Header — table layout: logo / title / document number on one row */
        .header { width: 100%; border-bottom: 2px solid #e0e0e0; margin-bottom: 25px; }
        .header td { vertical-align: middle; border: none; padding-bottom: 18px; }
        .doc-num-box { width: 140px; text-align: {{ $isRtl ? 'left' : 'right' }}; }
        .doc-num-label { font-size: 9pt; color: #95a5a6; margin-bottom: 5px; }
        .doc-num { font-size: 14pt; font-weight: 700; color: #2c3e50; }
        .title-box { text-align: center; }
        .doc-title-ar { font-size: 18pt; font-weight: 700; margin-bottom: 5px; color: #34495e; }
        .doc-title-en { font-size: 11pt; color: #7f8c8d; font-weight: 500; }
        .logo-box { width: 110px; text-align: {{ $isRtl ? 'right' : 'left' }}; }
        .logo-box img { max-height: 46px; max-width: 100px; }

        /* Info section — single column block */
        .info-section { margin: 20px 0; padding: 18px 20px; background: #f8f9fa; }
        .info-col { padding: 0; }
        .info-label { font-size: 10pt; font-weight: 600; margin-bottom: 10px; color: #34495e; padding-bottom: 8px; border-bottom: 1px solid #e0e0e0; }
        .info-col p { font-size: 9pt; margin: 6px 0; color: #5a6c7d; }
        .info-item { margin: 5px 0; }
        .info-item .label { color: #7f8c8d; }
        .info-item .value { font-weight: 600; color: #2c3e50; }

        .metadata { margin: 18px 0; padding: 12px 18px; background: #ecf0f1; border-radius: 4px; font-size: 9pt; }
        .meta-label { color: #7f8c8d; }
        .meta-value { font-weight: 600; color: #2c3e50; }

        table { width: 100%; border-collapse: collapse; margin: 18px 0; font-size: 9pt; }
        thead th { background: #f8f9fa; padding: 9px 6px; font-weight: 600; text-align: center; color: #2c3e50; border-bottom: 2px solid #e0e0e0; }
        tbody td { padding: 9px 6px; text-align: center; color: #5a6c7d; border-bottom: 1px solid #f0f0f0; }

        /* Stat cards — single-row table (mPDF has no flex/grid/inline-block columns) */
        .report-stats { width: 100%; border-collapse: separate; border-spacing: 7px 0; margin: 18px 0; }
        .report-stats td.report-stat-card { background: #f8f9fa; border-radius: 6px; padding: 13px 8px; text-align: center; vertical-align: top; border: none; }
        .report-stat-label { font-size: 9pt; color: #7f8c8d; margin-bottom: 7px; }
        .report-stat-value { font-size: 14pt; font-weight: 700; color: #2c3e50; }

        /* Summary / stat cards — inline-block instead of grid */
        .stats-section, .summary-grid { margin: 20px 0; }
        .stat-card { background: #f8f9fa; padding: 16px; border-radius: 6px; text-align: center; display: inline-block; width: 23%; vertical-align: top; }
        .stat-label { font-size: 9pt; color: #7f8c8d; margin-bottom: 8px; }
        .stat-value { font-size: 15pt; font-weight: 700; color: #2c3e50; }

        .summary-card { background: #f8f9fa; padding: 12px; border-radius: 8px; display: inline-block; width: 23%; vertical-align: top; text-align: center; }
        .summary-icon { display: inline-block; width: 40px; height: 40px; border-radius: 8px; color: #fff; }
        .summary-icon.fish, .summary-icon.weight, .summary-icon.list, .summary-icon.catch,
        .summary-icon.sales, .summary-icon.revenue, .summary-icon.net { background: #16a085; }
        .summary-icon.weight { background: #2980b9; }
        .summary-icon.list { background: #8e44ad; }
        .summary-icon.catch, .summary-icon.revenue { background: #e67e22; }
        .summary-icon.net { background: #06b6d4; }
        .summary-content { margin-top: 8px; }
        .summary-label { font-size: 9pt; color: #7f8c8d; margin-bottom: 5px; }
        .summary-content .summary-value { font-size: 13pt; font-weight: 700; color: #2c3e50; }

        /* Bottom section — table: QR + totals box */
        .bottom-section { width: 100%; margin-top: 30px; border: none; }
        .bottom-section td { border: none; vertical-align: top; }
        .qr-cell { width: 150px; }
        .qr-box { text-align: center; padding: 12px; background: #f8f9fa; border-radius: 6px; }
        .qr-box img { width: 110px; height: 110px; }
        .summary-box { width: 100%; }
        .summary-row { font-size: 10pt; background: #f8f9fa; margin-bottom: 2px; }
        .summary-row td { padding: 12px 18px; border: none; }
        .summary-row-highlight { background: #34495e; color: #fff; font-weight: 700; }
        .summary-row-highlight td { color: #fff; }
        .summary-row-strong { font-weight: 700; }
        .currency-symbol svg, .summary-value svg { width: 12px; height: 12px; fill: currentColor; }

        .footer { text-align: center; margin-top: 40px; padding-top: 18px; border-top: 1px solid #e0e0e0; font-size: 9pt; color: #95a5a6; }

        /* App theme utility classes (Bootstrap palette) for report badges/colors */
        .badge { display: inline-block; padding: 3px 9px; font-size: 8.5pt; font-weight: 600; color: #fff; text-align: center; border-radius: 4px; }
        .bg-success { background-color: #198754; color: #fff; }
        .bg-danger { background-color: #dc3545; color: #fff; }
        .bg-warning { background-color: #ffc107; color: #212529; }
        .bg-info { background-color: #0dcaf0; color: #212529; }
        .bg-primary { background-color: #0d6efd; color: #fff; }
        .bg-secondary { background-color: #6c757d; color: #fff; }
        .text-success { color: #198754; }
        .text-danger { color: #dc3545; }
        .text-warning { color: #d97706; }
        .text-info { color: #0a99b5; }
        .text-primary { color: #0d6efd; }
        .text-muted { color: #7f8c8d; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: 700; }
        .alert { padding: 10px 14px; border: 1px solid transparent; border-radius: 6px; margin: 12px 0; }
        .alert-warning { background: #fff3cd; border-color: #ffecb5; color: #664d03; }
        .alert-danger { background: #f8d7da; border-color: #f5c2c7; color: #842029; }
        .alert-info { background: #cff4fc; border-color: #b6effb; color: #055160; }
        table.table-bordered th, table.table-bordered td { border: 1px solid #e0e0e0; }

        {{ $extraStyles ?? '' }}
    </style>
</head>
<body>

@if(!empty($settings['watermark']))
    @php
        $wmPath = $settings['watermark'];
        $wmData = file_exists($wmPath) ? 'data:image/png;base64,'.base64_encode(file_get_contents($wmPath)) : '';
    @endphp
    @if(!empty($wmData))
        <watermarkimage src="{{ $wmData }}" alpha="0.06" />
    @endif
@endif

{{ $slot }}

</body>
</html>
