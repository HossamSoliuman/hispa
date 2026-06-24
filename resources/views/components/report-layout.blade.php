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
            color: #1a1a1a;
            background: #fff;
            font-size: 9pt;
            line-height: 1.5;
        }

        @php $startAlign = $isRtl ? 'right' : 'left'; $endAlign = $isRtl ? 'left' : 'right'; @endphp

        /* ───────────────────────────────────────────────────────────────
           Report design standard — dense, bordered "grid" layout for mPDF
           (no flex/grid: tables only). Matches the printed reference set.
           These are the shared defaults; individual reports just use the
           classes below and inherit the same look everywhere.
           ─────────────────────────────────────────────────────────────── */

        /* Section heading — solid black bar, white centered label */
        .section-bar, h3 {
            background: #1a1a1a; color: #fff; font-weight: 700; text-align: center;
            font-size: 10pt; padding: 5px 8px; margin: 14px 0 0;
        }
        .section-title { font-size: 10pt; font-weight: 700; color: #1a1a1a; margin: 0 0 5px; }

        /* Horizontal key-fact strip — one bordered cell per fact */
        table.info-bar { width: 100%; border-collapse: collapse; margin: 0 0 10px; }
        table.info-bar td { border: 1px solid #cfcfcf; padding: 4px 7px; vertical-align: middle; text-align: center; }
        .ib-label { font-size: 8pt; color: #888; display: block; margin-bottom: 2px; }
        .ib-value { font-size: 9.5pt; font-weight: 700; color: #1a1a1a; }

        /* Centered meta strip — inline "label: [boxed value]" facts, centered
           under the title (the printed reference "top part"). mPDF-safe: inline
           blocks, no flex. */
        .meta-row { text-align: center; margin: 0 0 16px; }
        .meta-row .meta-item { display: inline-block; margin: 0 9px; font-size: 9.5pt; vertical-align: middle; }
        .meta-row .meta-item .lbl { font-weight: 700; color: #1a1a1a; }
        .meta-row .meta-item .val-box { display: inline-block; border: 1px solid #cfcfcf; background: #fafafa; padding: 3px 14px; margin: 0 4px; min-width: 64px; text-align: center; font-weight: 700; color: #1a1a1a; }

        /* Two sections side-by-side — fixed geometry so dual rows line up */
        table.dual { width: 100%; table-layout: fixed; border-collapse: collapse; margin: 0 0 10px; }
        table.dual > tbody > tr > td.dual-col, table.dual > tbody > tr > td.dual-gap { vertical-align: top; padding: 0; border: none; }
        td.dual-gap { width: 16px; }

        /* Bordered grid data table: solid black header bar, hairline cell rules,
           a heavier rule above the totals row. */
        table.report-table { table-layout: fixed; width: 100%; border-collapse: collapse; margin: 0; }
        table.report-table th, table.report-table td {
            border: 1px solid #cfcfcf; padding: 4px 6px; font-size: 8.5pt;
            word-wrap: break-word; overflow-wrap: break-word; vertical-align: middle; text-align: center; color: #1a1a1a;
        }
        table.report-table thead th { background: #1a1a1a; color: #fff; font-weight: 700; border-color: #fff; }
        table.report-table tbody th { font-weight: 700; color: #1a1a1a; }
        table.report-table tfoot td, table.report-table tfoot th {
            background: #f2f2f2; font-weight: 700; color: #1a1a1a; border-top: 1.5px solid #1a1a1a;
        }
        table.report-table tr.net-row th, table.report-table tr.net-row td {
            background: #1a1a1a; color: #fff; font-weight: 700; border-color: #fff;
        }

        /* Column alignment: text labels start-aligned, numbers end-aligned */
        .col-text { text-align: {{ $startAlign }}; }
        .col-num  { text-align: {{ $endAlign }}; white-space: nowrap; }
        .block { margin: 0 0 10px; }
        .num { text-align: center; }
        td.rf-text { text-align: center; font-size: 8pt; color: #888; }

        /* Amount-in-words box — bordered with a black caption bar */
        table.amount-words { width: 100%; border-collapse: collapse; margin: 0 0 10px; }
        table.amount-words td { border: 1px solid #cfcfcf; padding: 0; vertical-align: middle; }
        .aw-cap { background: #1a1a1a; color: #fff; font-weight: 700; text-align: center; padding: 5px 8px; font-size: 9pt; }
        .aw-text { padding: 7px 10px; font-size: 9.5pt; color: #1a1a1a; }

        /* Signature row — dotted lines, evenly spaced */
        table.sig-table { width: 100%; border-collapse: collapse; margin: 26px 0 4px; }
        table.sig-table td { border: none; text-align: center; vertical-align: bottom; padding: 0 8px; }
        .sig-label { font-size: 9pt; font-weight: 700; color: #1a1a1a; margin-bottom: 18px; }
        .sig-line { font-size: 9pt; color: #888; letter-spacing: 1px; }

        /* Compact report footer: company + rights on a single row */
        table.report-footer { width: 100%; border-collapse: collapse; margin: 18px 0 0; border-top: 1px solid #cfcfcf; }
        table.report-footer td { border: none; padding: 8px 4px 0; vertical-align: middle; text-align: center; font-size: 8pt; color: #888; }

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

        /* Info section — compact bordered strip */
        .info-section { margin: 0 0 12px; padding: 7px 10px; border: 1px solid #cfcfcf; }
        .info-col { padding: 0; }
        .info-label { font-size: 9pt; font-weight: 700; margin-bottom: 4px; color: #1a1a1a; }
        .info-col p { font-size: 8.5pt; margin: 2px 0; color: #444; }
        .info-item { margin: 3px 0; }
        .info-item .label { color: #888; }
        .info-item .value { font-weight: 700; color: #1a1a1a; }

        .metadata { margin: 18px 0; padding: 12px 18px; background: #ecf0f1; border-radius: 4px; font-size: 9pt; }
        .meta-label { color: #7f8c8d; }
        .meta-value { font-weight: 600; color: #2c3e50; }

        /* Generic tables (legacy reports) — bordered grid with black header bar */
        table { width: 100%; border-collapse: collapse; margin: 8px 0; font-size: 8.5pt; }
        thead th { background: #1a1a1a; padding: 5px 6px; font-weight: 700; text-align: center; color: #fff; border: 1px solid #fff; }
        tbody td { padding: 4px 6px; text-align: center; color: #1a1a1a; border: 1px solid #cfcfcf; }
        tfoot td, tfoot th { padding: 4px 6px; text-align: center; font-weight: 700; color: #1a1a1a; background: #f2f2f2; border: 1px solid #cfcfcf; border-top: 1.5px solid #1a1a1a; }

        /* KPI cards — bordered box, caption above a bordered value cell */
        .report-stats { width: 100%; border-collapse: separate; border-spacing: 7px 0; margin: 0 0 12px; }
        .report-stats td.report-stat-card { padding: 0; text-align: center; vertical-align: top; border: none; }
        .report-stat-label { font-size: 8.5pt; font-weight: 700; color: #1a1a1a; margin-bottom: 4px; }
        .report-stat-value { font-size: 12pt; font-weight: 700; color: #1a1a1a; border: 1px solid #cfcfcf; padding: 6px 4px; }

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
        .qr-cell { width: 130px; }
        .qr-box { text-align: center; padding: 8px; border: 1px solid #cfcfcf; }
        .qr-box img { width: 104px; height: 104px; }
        .summary-box { width: 100%; }
        .summary-row { width: 100%; border-collapse: collapse; font-size: 9pt; margin-bottom: -1px; }
        .summary-row td { padding: 6px 12px; border: 1px solid #cfcfcf; }
        .summary-row-highlight { background: #1a1a1a; font-weight: 700; }
        .summary-row-highlight td { color: #fff; border-color: #1a1a1a; }
        .summary-row-strong { font-weight: 700; }
        .summary-row-strong td { background: #f2f2f2; }
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

        /* ───────────────────────────────────────────────────────────────
           Month-close one-page layout: multi-row summary cards + the
           three-column closing footer (summary grid / sign-off / notes).
           mPDF-safe — tables only, no flex/grid.
           ─────────────────────────────────────────────────────────────── */

        /* Five summary cards: black caption banner over a bordered key/value
           body whose final row is the card's emphasized total. Heads and bodies
           sit in two separate table rows so mPDF equalises each row's cell
           heights — every card ends up the same height even when a head wraps
           or a body has fewer rows (bodies are padded with blank rows). */
        table.sum-cards { width: 100%; table-layout: fixed; border-collapse: separate; border-spacing: 5px 0; margin: 0 0 12px; }
        table.sum-cards td.sum-head { background: #1a1a1a; color: #fff; font-weight: 700; text-align: center; font-size: 8pt; padding: 5px 3px; line-height: 1.25; vertical-align: middle; border: none; }
        table.sum-cards td.sum-card { padding: 0; vertical-align: top; border: none; background: #fff; }
        table.sum-body { width: 100%; border-collapse: collapse; border: 1px solid #cfcfcf; border-top: none; margin: 0; }
        table.sum-body td { padding: 3px 5px; font-size: 7.8pt; vertical-align: middle; border: none; }
        table.sum-body td.sum-k { text-align: {{ $startAlign }}; color: #555; }
        table.sum-body td.sum-v { text-align: {{ $endAlign }}; font-weight: 700; color: #1a1a1a; white-space: nowrap; }
        table.sum-body tr.sum-total td { background: #f2f2f2; border-top: 1px solid #1a1a1a; font-weight: 700; color: #1a1a1a; }

        /* Three-column closing footer: summary grid / sign-off / notes. */
        table.close-footer { width: 100%; table-layout: fixed; border-collapse: collapse; margin: 16px 0 0; }
        table.close-footer td.cf-col { width: 32%; vertical-align: top; padding: 0; border: none; }
        table.close-footer td.cf-gap { width: 2%; padding: 0; border: none; }
        .cf-card { border: none; }
        .cf-head { font-weight: 700; text-align: {{ $startAlign }}; font-size: 9pt; padding: 0 0 4px; color: #1a1a1a; }
        .cf-body { padding: 0; }
        table.cf-kv { width: 100%; border-collapse: collapse; }
        table.cf-kv td { border: none; padding: 3px 2px; font-size: 8.5pt; }
        table.cf-kv td.cf-k { text-align: {{ $startAlign }}; color: #555; }
        table.cf-kv td.cf-v { text-align: {{ $endAlign }}; font-weight: 700; color: #1a1a1a; white-space: nowrap; }
        table.cf-kv tr.cf-total td { border: none; font-weight: 700; color: #1a1a1a; padding-top: 4px; }
        .cf-signoff-label { font-size: 9pt; font-weight: 700; color: #1a1a1a; }
        .cf-signoff-line { font-size: 9pt; color: #888; white-space: nowrap; margin: 4px 0 10px; }
        .cf-signoff-date { font-size: 8.5pt; color: #555; }
        ul.cf-notes { margin: 0; padding-{{ $startAlign }}: 15px; list-style-type: disc; }
        ul.cf-notes li { font-size: 8pt; color: #444; margin-bottom: 5px; line-height: 1.5; }

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
