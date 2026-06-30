@props([
    'title' => '',
    'titleEn' => '',
    'documentNumber' => '',
    'settings' => [],
    'qrCode' => '',
    'printable' => true,
])

@php $isRtl = app()->getLocale() == 'ar'; @endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    @if($printable)<meta name="viewport" content="width=device-width, initial-scale=1">@endif
    <title>{{ $title }}</title>
    @if($printable)
        {{-- Printable preview is rendered by the browser, so load the same web
             font the dashboard uses to match the on-screen UI. --}}
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    @endif
    <style>
        /* Rendered on screen and printed by the browser. Bordered tables keep
           the dense "grid" look consistent in both the screen preview and the
           printout; Arabic shaping / RTL is handled natively by the browser. */
        @page { size: A4; margin: 12mm 10mm; }

        a, a:link, a:visited, a:hover { text-decoration: none; color: inherit; }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        /* Force background graphics (black section bars, KPI cells, badges) to
           print instead of being stripped by the browser's print dialog. */
        html { -webkit-print-color-adjust: exact; print-color-adjust: exact; }

        body {
            direction: {{ $isRtl ? 'rtl' : 'ltr' }};
            font-family: 'Tajawal', 'Roboto', 'Chakra Petch', 'Segoe UI', 'Tahoma', 'Arial', sans-serif;
            color: #1a1a1a;
            background: #fff;
            font-size: 9pt;
            line-height: 1.5;
        }

        /* Faded full-page watermark (replaces the mPDF <watermarkimage> tag).
           Fixed so Chromium repeats it on every printed page. */
        .report-watermark {
            position: fixed;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            width: 60%;
            opacity: 0.06;
            z-index: 0;
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
        table.report-table thead th { background: #ededed; color: #1a1a1a; font-weight: 700; border-color: #cfcfcf; }
        table.report-table tbody th { font-weight: 700; color: #1a1a1a; }
        table.report-table tfoot td, table.report-table tfoot th {
            background: #f2f2f2; font-weight: 700; color: #1a1a1a; border-top: 1.5px solid #1a1a1a;
        }
        table.report-table tr.net-row th, table.report-table tr.net-row td {
            background: #1a1a1a; color: #fff; font-weight: 700; border-color: #fff;
        }
        /* Info box variant — swapped emphasis: black title bar, light total row */
        table.report-table.info-box thead th { background: #1a1a1a; color: #fff; border-color: #fff; }
        table.report-table.info-box tr.net-row th, table.report-table.info-box tr.net-row td {
            background: #ededed; color: #1a1a1a; border-color: #cfcfcf;
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
        thead th { background: #ededed; padding: 5px 6px; font-weight: 700; text-align: center; color: #1a1a1a; border: 1px solid #cfcfcf; }
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
           body whose final row is the card's emphasized total. Rendered by
           Chromium, so flexbox lays the heads and bodies out as two stretch
           rows — every head equalises to the tallest head and every body to the
           tallest body, while each total is pinned to the bottom of its card
           (margin-top:auto). Result: all five total bars line up on one row
           regardless of how many facts a card lists. */
        .sum-cards { margin: 0 0 12px; }
        .sum-heads, .sum-bodies { display: flex; align-items: stretch; gap: 5px; }
        .sum-head {
            flex: 1 1 0; min-width: 0;
            background: #1a1a1a; color: #fff; font-weight: 700; text-align: center;
            font-size: 8pt; line-height: 1.25; padding: 5px 4px;
            display: flex; align-items: center; justify-content: center;
        }
        .sum-body {
            flex: 1 1 0; min-width: 0; display: flex; flex-direction: column;
            border: 1px solid #cfcfcf; border-top: none;
        }
        .sum-row { display: flex; align-items: center; justify-content: space-between; gap: 6px; padding: 3px 6px; font-size: 7.8pt; }
        .sum-row .sum-k { color: #555; text-align: {{ $startAlign }}; }
        .sum-row .sum-v { font-weight: 700; color: #1a1a1a; white-space: nowrap; text-align: {{ $endAlign }}; }
        .sum-row.sum-total { margin-top: auto; background: #f2f2f2; border-top: 1px solid #1a1a1a; }
        .sum-row.sum-total .sum-k { color: #1a1a1a; font-weight: 700; }

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

        @if($printable)
        /* ───────────────────────────────────────────────────────────────
           Printable preview mode — the report is served as a normal HTML
           page the user reviews on screen as an A4 sheet, then prints from
           the browser ("Print" / "Save as PDF"). Screen styles mimic a PDF
           viewer; print styles strip the chrome so @page owns the margins.
           ─────────────────────────────────────────────────────────────── */
        @media screen {
            body { background: #525659; padding: 0 0 40px; }
            .report-toolbar {
                position: sticky; top: 0; z-index: 50;
                display: flex; justify-content: center; gap: 10px;
                padding: 11px; margin-bottom: 22px;
                background: #fff; border-bottom: 1px solid #d9d9d9;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
            }
            .report-toolbar button {
                font-family: inherit; font-size: 10pt; font-weight: 700;
                cursor: pointer; border: none; border-radius: 6px; padding: 9px 24px;
                display: inline-flex; align-items: center; gap: 8px; transition: background 0.15s ease;
            }
            .report-toolbar .rt-print { background: #1a1a1a; color: #fff; }
            .report-toolbar .rt-print:hover { background: #000; }
            .report-toolbar .rt-close { background: #efefef; color: #1a1a1a; border: 1px solid #d9d9d9; }
            .report-toolbar .rt-close:hover { background: #e3e3e3; }
            .report-page {
                width: 210mm; min-height: 297mm; box-sizing: border-box;
                margin: 0 auto; padding: 12mm 10mm;
                background: #fff; box-shadow: 0 3px 18px rgba(0, 0, 0, 0.45);
            }
        }
        @media print {
            .report-toolbar { display: none !important; }
            .report-page { width: auto; min-height: 0; margin: 0; padding: 0; box-shadow: none; background: transparent; }
            body { background: #fff; padding: 0; }
        }
        @endif

        {{ $extraStyles ?? '' }}
    </style>
</head>
<body>

@if($printable)
    <div class="report-toolbar">
        <button type="button" class="rt-print" onclick="window.print()">
            <svg viewBox="0 0 24 24" width="15" height="15" fill="currentColor" aria-hidden="true"><path d="M19 8H5a3 3 0 0 0-3 3v6h4v4h12v-4h4v-6a3 3 0 0 0-3-3zm-3 11H8v-5h8v5zm4-7a1 1 0 1 1 0-2 1 1 0 0 1 0 2zM18 3H6v4h12V3z"/></svg>
            {{ __('owner.reports.print') }}
        </button>
        <button type="button" class="rt-close" onclick="window.close()">{{ __('owner.reports.close') }}</button>
    </div>
    <div class="report-page">
@endif

@if(!empty($settings['watermark']))
    @php
        $wmPath = $settings['watermark'];
        $wmData = file_exists($wmPath) ? 'data:image/png;base64,'.base64_encode(file_get_contents($wmPath)) : '';
    @endphp
    @if(!empty($wmData))
        <img class="report-watermark" src="{{ $wmData }}" alt="">
    @endif
@endif

{{ $slot }}

@if($printable)
    </div>
@endif

</body>
</html>
