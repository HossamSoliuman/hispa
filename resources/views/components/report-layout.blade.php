@props([
    'title' => '',
    'titleEn' => '',
    'documentNumber' => '',
    'settings' => [],
    'qrCode' => '',
])

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Icon fonts needed for report summary icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        /* Ensure links in printable reports are not underlined */
        a, a:link, a:visited, a:hover { text-decoration: none !important; color: inherit; }
        .print-btn { text-decoration: none !important; }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        @page { margin: 15mm; }
        body {
            font-family: 'Tajawal', 'Roboto', 'Chakra Petch', 'Segoe UI', Tahoma, 'Traditional Arabic', 'Arabic Typesetting', Arial, sans-serif;
            direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }};
            padding: 20mm;
            color: #2c3e50;
            background: #fff;
            font-size: 10pt;
            line-height: 1.6;
        }

        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #e0e0e0; }
        @if(app()->getLocale() == 'ar')
        /* Ensure visual order in RTL: force header to reverse row so logo stays at right */
        .header { flex-direction: row-reverse; }
        .title-box { text-align: center; }
        .info-right { text-align: left; }
        @endif
            .doc-num-box { min-width: 140px; @if(app()->getLocale() == 'ar') order: 1; @else order: 3; @endif }
            .doc-num-label { font-size: 9pt; color: #95a5a6; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.5px; }
            .doc-num { font-size: 14pt; font-weight: 700; color: #2c3e50; }
            .title-box { flex: 1; text-align: center; order: 2; }
            .doc-title-ar { font-size: 18pt; font-weight: 700; margin-bottom: 5px; color: #34495e; }
            .doc-title-en { font-size: 11pt; color: #7f8c8d; font-weight: 500; }
            /* Logo on right for Arabic (order: 3), left for English (order: 1) */
        .logo-box { min-width: 140px; @if(app()->getLocale() == 'ar') order: 3; @else order: 1; @endif }
        .logo-box img { max-height: 70px; max-width: 140px; }

        .info-section { display: flex; gap: 30px; margin: 30px 0; padding: 25px 0; background: #f8f9fa; }
        .info-col { flex: 1; padding: 0 20px; }
        .info-label { font-size: 10pt; font-weight: 600; margin-bottom: 12px; color: #34495e; padding-bottom: 8px; border-bottom: 1px solid #e0e0e0; }
        .info-col p { font-size: 9pt; margin: 8px 0; line-height: 1.7; color: #5a6c7d; }
        .info-right { text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}; }

        .metadata { display: flex; justify-content: space-between; align-items: center; margin: 25px 0; padding: 15px 20px; background: #ecf0f1; border-radius: 4px; font-size: 9pt; }
        .meta-item { display: flex; gap: 8px; align-items: center; }
        .meta-label { color: #7f8c8d; }
        .meta-value { font-weight: 600; color: #2c3e50; }

        table { width: 100%; border-collapse: separate; border-spacing: 0; margin: 25px 0; font-size: 9pt; }
        thead th { background: #f8f9fa; padding: 12px 8px; font-weight: 600; text-align: center; color: #2c3e50; border-bottom: 2px solid #e0e0e0; }
        tbody td { padding: 12px 8px; text-align: center; color: #5a6c7d; border-bottom: 1px solid #f0f0f0; }
        tbody tr:hover { background: #fafbfc; }
        tbody tr:last-child td { border-bottom: none; }

        .stats-section { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 30px 0; }
        .stat-card { background: #f8f9fa; padding: 20px; border-radius: 6px; text-align: center; }
        .stat-label { font-size: 9pt; color: #7f8c8d; margin-bottom: 10px; }
        .stat-value { font-size: 16pt; font-weight: 700; color: #2c3e50; }

        /* Summary grid used by fish history and other reports */
        .summary-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin: 18px 0 28px; }
        @media (max-width: 900px) { .summary-grid { grid-template-columns: repeat(2, 1fr); } }
        @media print { .summary-grid { grid-template-columns: repeat(4, 1fr); } }

        .summary-card { background: #f8f9fa; padding: 14px; border-radius: 8px; display: flex; gap: 12px; align-items: center; }
        .summary-icon { flex: 0 0 46px; height: 46px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; font-size: 18px; color: #fff; }
        .summary-icon.fish { background: #16a085; }
        .summary-icon.weight { background: #2980b9; }
        .summary-icon.list { background: #8e44ad; }
        .summary-icon.catch { background: #e67e22; }

        .summary-content { flex: 1; text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}; }
        .summary-label { font-size: 10pt; color: #7f8c8d; margin-bottom: 6px; }
        .summary-value { font-size: 15pt; font-weight: 700; color: #2c3e50; }

            .bottom-section { display: flex; justify-content: space-between; align-items: flex-start; margin-top: 40px; gap: 30px; }
            .qr-box { flex: 0 0 130px; text-align: center; padding: 15px; background: #f8f9fa; border-radius: 6px; }
            .qr-box img { width: 110px; height: 110px; padding: 5px; }
            .summary-box { flex: 1; max-width: 450px; margin-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: auto; }
        .summary-row { display: flex; justify-content: space-between; padding: 15px 20px; font-size: 10pt; background: #f8f9fa; margin-bottom: 2px; }
        .summary-row:last-child { background: #34495e; color: #fff; font-weight: 700; font-size: 11pt; margin-top: 10px; border-radius: 4px; }
        .summary-row-highlight { background: #34495e !important; color: #fff; font-weight: 700; }
        .summary-row-strong { font-weight: 700; }
        .summary-value { display: inline-flex; align-items: center; gap: 6px; }
        .currency-symbol, .summary-value .currency-symbol { display: inline-flex; align-items: center; gap: 5px; }
        .currency-symbol svg, .summary-value .currency-symbol svg { width: 14px; height: 14px; fill: currentColor; }

            .footer { text-align: center; margin-top: 50px; padding-top: 20px; border-top: 1px solid #e0e0e0; font-size: 9pt; color: #95a5a6; }

        /* Watermark styles */
        .report-watermark { position: fixed; top: 25%; left: 50%; transform: translateX(-50%); width: 60%; text-align: center; opacity: 0.06; pointer-events: none; z-index: 0; }
        .report-watermark img { width: 100%; height: auto; display: block; filter: grayscale(1); }

        .no-print { text-align: center; margin: 30px 0; }
        .print-btn { background: #3498db; color: #fff; border: none; padding: 12px 40px; font-size: 10pt; cursor: pointer; border-radius: 6px; font-weight: 600; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: all 0.3s; }
        .print-btn:hover { background: #2980b9; box-shadow: 0 4px 8px rgba(0,0,0,0.15); }

        /* App theme utility classes (Bootstrap 5 palette) so printable reports
           keep the same badges/colors as the rest of the app. */
        .badge { display: inline-block; padding: 4px 10px; font-size: 8.5pt; font-weight: 600; line-height: 1; color: #fff; text-align: center; white-space: nowrap; vertical-align: middle; border-radius: 4px; }
        .bg-success { background-color: #198754 !important; color: #fff !important; }
        .bg-danger { background-color: #dc3545 !important; color: #fff !important; }
        .bg-warning { background-color: #ffc107 !important; color: #212529 !important; }
        .bg-info { background-color: #0dcaf0 !important; color: #212529 !important; }
        .bg-primary { background-color: #0d6efd !important; color: #fff !important; }
        .bg-secondary { background-color: #6c757d !important; color: #fff !important; }
        .text-success { color: #198754 !important; }
        .text-danger { color: #dc3545 !important; }
        .text-warning { color: #d97706 !important; }
        .text-info { color: #0a99b5 !important; }
        .text-primary { color: #0d6efd !important; }
        .text-muted { color: #7f8c8d !important; }
        .text-center { text-align: center !important; }
        .fw-bold { font-weight: 700; }
        .alert { padding: 12px 16px; border: 1px solid transparent; border-radius: 6px; margin: 15px 0; }
        .alert-warning { background: #fff3cd; border-color: #ffecb5; color: #664d03; }
        .alert-danger { background: #f8d7da; border-color: #f5c2c7; color: #842029; }
        .alert-info { background: #cff4fc; border-color: #b6effb; color: #055160; }
        table.table-bordered th, table.table-bordered td { border: 1px solid #e0e0e0; }

        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
            /* Keep badge/summary colors when printing */
            .badge, .summary-icon, .summary-row-highlight, .summary-row:last-child { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            /* Avoid breaking inside header/info/stats/bottom sections, but allow tables to span pages */
            .header, .info-section, .stats-section, .bottom-section { page-break-inside: avoid; }
            table { page-break-inside: auto; }
            tbody tr { page-break-inside: avoid; page-break-after: auto; }
            /* Ensure QR and images are visible and centered in print */
            .bottom-section { display: block !important; text-align: center !important; }
            .qr-box { display: block !important; margin: 0 auto 12px !important; }
            .qr-box img { -webkit-print-color-adjust: exact; display: block; margin: 0 auto; }
            .summary-box { display: block !important; margin: 0 auto !important; float: none !important; text-align: center !important; }
        }

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
        <div class="report-watermark" aria-hidden="true">
            <img src="{{ $wmData }}" alt="watermark" />
        </div>
    @endif
@endif

{{ $slot }}

<div class="no-print">
    <button class="print-btn" onclick="window.print()">
        <i class="bi bi-printer"></i> {{ __('dalal.reports.print') }}
    </button>
</div>

<script>
    // Auto-print functionality if needed
    // window.onload = function() { window.print(); }
</script>

</body>
</html>
