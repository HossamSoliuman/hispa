@extends('owner.layouts.master')

@section('title', __('owner.profit_loss.title'))
@section('css')
    <style>
        :root{
            --bg:#f7f9fc; --card:#ffffff; --text:#0f172a; --muted:#64748b;
            --border:#e2e8f0; --accent:#16a34a; --danger:#dc2626; --warn:#d97706;
        }
        [data-bs-theme=dark]{
            --bg:#1d2835; --card:#2b3947; --text:#e9edf1; --muted:#9aa7b2; --border:rgba(255,255,255,.14);
        }
        [data-bs-theme=dark] .pl-form input, [data-bs-theme=dark] .pl-form select{ background:var(--card); }
        [data-bs-theme=dark] .pl-btn.secondary{ background:var(--card); color:var(--text); }
        [data-bs-theme=dark] table.pl-table th{ background:rgba(255,255,255,.06); color:var(--text); }
        [data-bs-theme=dark] .badge{ background:var(--card); color:var(--text); }
        [dir="rtl"] .pl-wrap, .pl-wrap{ direction: rtl; }
        .pl-wrap{ background: var(--bg); padding: 20px; border-radius: 16px; }
        .pl-header{ display:flex; align-items:flex-start; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:10px; }
        .pl-title{ font-size:22px; font-weight:800; color:var(--text); }
        .pl-sub{ color:var(--muted); }
        .pl-form{ background:var(--card); border:1px solid var(--border); border-radius:14px; padding:12px; display:flex; gap:10px; flex-wrap:wrap; }
        .pl-form label{ font-size:12px; color:var(--muted); display:block; margin-bottom:6px; }
        .pl-form input, .pl-form select{
            background:#fff; color:var(--text); border:1px solid var(--border); border-radius:10px; padding:8px 10px; min-width:170px;
        }
        .pl-btn{ background:#2563eb; color:#fff; border:none; border-radius:10px; padding:9px 14px; font-weight:600; cursor:pointer; }
        .pl-btn.secondary{ background:#fff; color:#111827; border:1px solid var(--border); }
        .pl-grid{ display:grid; grid-template-columns: repeat(4,1fr); gap:12px; margin-top:14px; }
        .pl-card{ background:var(--card); border:1px solid var(--border); border-radius:14px; padding:14px; }
        .pl-card .label{ color:var(--muted); font-size:12px; }
        .pl-card .value{ color:var(--text); font-size:22px; font-weight:800; margin-top:4px; }
        .accent{ color:var(--accent); } .danger{ color:var(--danger);} .warn{ color:var(--warn);}
        .pl-sep{ height:1px; background:var(--border); margin:16px 0; }
        .pl-section-title{ color:var(--text); font-size:16px; font-weight:700; margin:10px 0; }
        table.pl-table{ width:100%; border-collapse: collapse; }
        table.pl-table th, table.pl-table td{ border-bottom:1px solid var(--border); padding:10px 12px; text-align:right; color:var(--text); }
        table.pl-table th{ background:#f1f5f9; color:#0f172a; font-weight:700; font-size:13px; }
        .badges{ display:flex; gap:8px; flex-wrap:wrap; }
        .badge{ background:#fff; border:1px solid var(--border); color:#334155; padding:4px 10px; border-radius:999px; font-size:11px; }
        @media (max-width: 920px){ .pl-grid{ grid-template-columns:repeat(2,1fr);} }
        @media (max-width: 560px){ .pl-grid{ grid-template-columns:1fr;} .pl-form input,.pl-form select{ min-width:unset; width:100%; } }

        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            body * { visibility: hidden !important; }

            #kpi-print-area, #kpi-print-area * {
                visibility: visible !important;
            }

            /* {{ __('owner.generated.item_2203c1') }} */
            #kpi-print-area {
                position: absolute !important;
                inset: 0 !important; /* top/right/bottom/left = 0 */
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                background: #fff !important;
            }

            /* {{ __('owner.generated.item_3569c9') }} */
            .no-print { display: none !important; }
        }

        /* ({{ __('owner.generated.item_f36a82') }}) {{ __('owner.generated.item_f4f0f5') }} */
        @page { size: A4 portrait; margin: 12mm; }
    </style>

@endsection
@section('content')

    <div class="d-flex align-items-center mb-3">
        <div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">{{ __('owner.profit_loss.breadcrumb_reports') }}</a></li>
                <li class="breadcrumb-item active">{{ __('owner.profit_loss.breadcrumb_financial') }}</li>
            </ul>
            <h1 class="page-header mb-0">{{ __('owner.profit_loss.title') }}</h1>
        </div>
    </div>

    <div class="tab-content py-4" id="kpi-print-area">
    <div class="pl-wrap">
        {{-- هيدر HUD + شريحة وصف --}}
        <div class="pl-header">
            <div>
                <div class="pl-title">{{ __('owner.profit_loss.title') }}</div>
                <div class="pl-sub">{{ __('owner.profit_loss.description') }}</div>
            </div>
        </div>

        {{-- فلاتر --}}
        <form class="pl-form" method="GET" action="{{ route('owner.profit.loss') }}">
            <div>
                <label>{{ __('owner.profit_loss.from_date') }}</label>
                <input type="date" name="from" value="{{ $from }}">
            </div>
            <div>
                <label>{{ __('owner.profit_loss.to_date') }}</label>
                <input type="date" name="to" value="{{ $to }}">
            </div>

            <div>
                <label>{{ __('owner.profit_loss.boat') }}</label>
                <select name="boat_id">
                    <option value="">{{ __('owner.profit_loss.all_boats') }}</option>
                    @foreach($boats as $boat)
                        <option value="{{ $boat->id }}" {{ $boatId == $boat->id ? 'selected' : '' }}>
                            {{ $boat->name ?? $boat->name_ar }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="align-self:end; display:flex; gap:8px;">
                <button class="pl-btn" type="submit">{{ __('owner.profit_loss.update') }}</button>
                <button class="pl-btn secondary no-print" type="button" onclick="window.print()">{{ __('owner.profit_loss.print') }}</button>
            </div>
        </form>

        {{-- بطاقات المؤشرات --}}
        <div class="pl-grid">
            <div class="pl-card">
                <div class="label">{{ __('owner.profit_loss.total_sales') }}</div>
                <div class="value accent">{{ number_format($sales ?? 0, 2) }}</div>
            </div>
            <div class="pl-card">
                <div class="label">{{ __('owner.profit_loss.total_expenses') }}</div>
                <div class="value danger">{{ number_format($expenses ?? 0, 2) }}</div>
            </div>
            <div class="pl-card">
                <div class="label">{{ __('owner.profit_loss.total_payrolls') }}</div>
                <div class="value warn">{{ number_format($payrolls ?? 0, 2) }}</div>
            </div>
            <div class="pl-card">
                <div class="label">{{ __('owner.profit_loss.net_profit_loss') }}</div>
                @php $np = (float)($net ?? 0); @endphp
                <div class="value" style="color: {{ $np >= 0 ? 'var(--accent)' : 'var(--danger)' }}">
                    {{ number_format($np, 2) }}
                </div>
                <div class="pl-sub" style="font-size:11px">{{ __('owner.profit_loss.formula_note') }}</div>
            </div>
        </div>


    </div>
    </div>
@section('script')

    <script src="{{asset('dashboard/assets/plugins/@highlightjs/cdn-assets/highlight.min.js')}}"></script>
    <script src="{{asset('dashboard/assets/js/demo/highlightjs.demo.js')}}"></script>
    <script src="{{asset('dashboard/assets/plugins/datatables.net/js/dataTables.min.js')}}"></script>
    <script src="{{asset('dashboard/assets/plugins/datatables.net-bs5/js/dataTables.bootstrap5.min.js')}}"></script>
    <script src="{{asset('dashboard/assets/plugins/datatables.net-buttons/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('dashboard/assets/plugins/datatables.net-buttons/js/buttons.colVis.min.js')}}"></script>
    <script src="{{asset('dashboard/assets/plugins/datatables.net-buttons/js/buttons.html5.min.js')}}"></script>
    <script src="{{asset('dashboard/assets/plugins/datatables.net-buttons/js/buttons.print.min.js')}}"></script>
    <script
        src="{{asset('dashboard/assets/plugins/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js')}}"></script>
    <script
        src="{{asset('dashboard/assets/plugins/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
    <script
        src="{{asset('dashboard/assets/plugins/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js')}}"></script>
    <script src="{{asset('dashboard/assets/plugins/bootstrap-table/dist/bootstrap-table.min.js')}}"></script>
    <script src="{{asset('dashboard/assets/js/demo/table-plugins.demo.js')}}"></script>
    <script src="{{asset('dashboard/assets/js/demo/sidebar-scrollspy.demo.js')}}"></script>
    <script src="{{asset('dashboard/assets/js/jquery.validate.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <!-- Buttons Bootstrap ({{ __('owner.generated.item_df378a') }} Bootstrap style) -->



    <script type="text/javascript">
        $('#resetBtn').on('click', function () {
            $('#start_date').val('');
            $('#end_date').val('');
            $('#dalal_id_filter').val('');
            $('#datatableDefault').DataTable().ajax.reload();
        });

        $(function () {
            if ($.fn.DataTable.isDataTable('#datatableDefault')) {
                $('#datatableDefault').DataTable().destroy();
            }

            var table = $('#datatableDefault').DataTable({
                processing: true,
                serverSide: true,
                dom:
                    "<'row mb-3' " +
                    "<'col-md-4'l>" +                   // {{ __('owner.generated.page_length') }}"<'col-md-4'f>" +                   // {{ __('owner.dalal_invoices.filters.search') }}"<'col-md-4 text-md-end'B>" +      // {{ __('owner.generated.export_buttons') }}">" +
                    "<'row'<'col-sm-12'tr>>" +             // {{ __('owner.generated.table') }}"<'row mt-2'<'col-sm-5'i><'col-sm-7'p>>", // معلومات الجدول والترقيم

                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json"
                },
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, '{{ __('owner.generated.item_6d08f1') }}']
                ],
                pageLength: 10,
                ajax: {
                    url: "{{ route('owner.getDalalStockDataReport') }}",
                    data: function (d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.dalal_id_filter = $('#dalal_id_filter').val(); // أضف هذا السطر

                    },
                    dataSrc: function (json) {
                        $('#totalFishCount').text(json.total_fish_count); // تحديث العنصر في HTML
                        $('#totalWeight').text(json.totalWeight); // تحديث العنصر في HTML
                        $('#totalDalalCount').text(json.total_dalal_count); // تحديث العنصر في HTML
                        return json.data;
                    }

                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'dalal_name', name: 'dalal_name'},
                    {data: 'fish_name', name: 'fish_name'},
                    {data: 'total_weight', name: 'total_weight'},
                    {data: 'date', name: 'date'},
                ],
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: 'Excel',
                        className: 'btn btn-outline-success btn-sm me-1'
                    },
                    {
                        extend: 'print',
                        text: '{{ __('owner.generated.item_88c5d1') }}',
                        className: 'btn btn-outline-primary btn-sm'
                    }
                ],
                responsive: false, scrollX: true
            });

            $('#filterBtn').on('click', function () {
                table.ajax.reload();
            });
        });

    </script>

@endsection
@endsection
