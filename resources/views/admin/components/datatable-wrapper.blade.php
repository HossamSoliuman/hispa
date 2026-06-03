@props([
    'tableId' => 'ownerDataTable',
    'columns' => [],
    'actions' => [],
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'showArrow' => true,
    'cardClass' => 'card border-0 shadow-sm',
    'tableClass' => 'table table-sm table-bordered table-hover text-center small-text',
    'responsive' => true,
    'showExport' => false,
    'exportFormats' => ['excel', 'pdf', 'print'],
])

{{--
/**
 * Owner Dashboard DataTable Wrapper Component
 *
 * A complete datatable wrapper with header, actions, and export options
 *
 * @usage
 * <x-owner.datatable-wrapper
 *     tableId="boatsTable"
 *     title="جدول القوارب"
 *     subtitle="عرض جميع القوارب المسجلة"
 *     icon="bi-ship"
 *     :columns="[
 *         ['title' => '#', 'data' => 'DT_RowIndex', 'orderable' => false],
 *         ['title' => 'الاسم', 'data' => 'name'],
 *         ['title' => 'النوع', 'data' => 'type'],
 *         ['title' => 'الحالة', 'data' => 'status'],
 *         ['title' => 'الإجراءات', 'data' => 'actions', 'orderable' => false],
 *     ]"
 *     :actions="[
 *         ['label' => 'إضافة', 'url' => route('owner.boats.create'), 'icon' => 'bi-plus', 'class' => 'btn-primary btn-sm']
 *     ]"
 *     :showExport="true"
 * />
 */
--}}

<div class="{{ $cardClass }}">
    @if($showArrow)
        @include('owner.partials._card_arrow')
    @endif

    {{-- Card Header --}}
    @if($title || count($actions) > 0)
    <div class="card-header bg-light border-0">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            {{-- Title Section --}}
            @if($title)
            <div class="d-flex align-items-center gap-2">
                @if($icon)
                    <div class="bg-white bg-opacity-10 p-2 rounded">
                        <i class="{{ $icon }} text-primary"></i>
                    </div>
                @endif
                <div>
                    <h5 class="mb-0 fw-bold">{{ $title }}</h5>
                    @if($subtitle)
                        <small class="text-muted">{{ $subtitle }}</small>
                    @endif
                </div>
            </div>
            @endif

            {{-- Action Buttons --}}
            @if(count($actions) > 0)
            <div class="d-flex flex-wrap gap-2">
                @foreach($actions as $action)
                    @php
                        $actionLabel = $action['label'] ?? '';
                        $actionUrl = $action['url'] ?? '#';
                        $actionIcon = $action['icon'] ?? null;
                        $actionClass = $action['class'] ?? 'btn-outline-primary';
                        $actionTarget = $action['target'] ?? null;
                        $actionModal = $action['modal'] ?? null;
                        $actionClick = $action['onclick'] ?? null;
                    @endphp

                    @if($actionModal)
                        <button
                            type="button"
                            class="btn {{ $actionClass }}"
                            data-bs-toggle="modal"
                            data-bs-target="{{ $actionModal }}"
                        >
                            @if($actionIcon)<i class="{{ $actionIcon }} me-1"></i>@endif
                            {{ $actionLabel }}
                        </button>
                    @else
                        <a
                            href="{{ $actionUrl }}"
                            class="btn {{ $actionClass }}"
                            @if($actionTarget) target="{{ $actionTarget }}" @endif
                            @if($actionClick) onclick="{{ $actionClick }}" @endif
                        >
                            @if($actionIcon)<i class="{{ $actionIcon }} me-1"></i>@endif
                            {{ $actionLabel }}
                        </a>
                    @endif
                @endforeach

                {{-- Export Buttons --}}
                @if($showExport)
                    <div class="btn-group" role="group">
                        @if(in_array('excel', $exportFormats))
                            <button type="button" class="btn btn-outline-success btn-sm" id="{{ $tableId }}_export_excel">
                                <i class="bi bi-file-earmark-excel"></i>
                            </button>
                        @endif
                        @if(in_array('pdf', $exportFormats))
                            <button type="button" class="btn btn-outline-danger btn-sm" id="{{ $tableId }}_export_pdf">
                                <i class="bi bi-file-earmark-pdf"></i>
                            </button>
                        @endif
                        @if(in_array('print', $exportFormats))
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="{{ $tableId }}_export_print">
                                <i class="bi bi-printer"></i>
                            </button>
                        @endif
                    </div>
                @endif
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Card Body with Table --}}
    <div class="card-body">
        <div class="{{ $responsive ? 'table-responsive' : '' }}">
            <table id="{{ $tableId }}" class="{{ $tableClass }}" style="width:100%">
                <thead>
                    <tr>
                        @foreach($columns as $column)
                            <th>{{ $column['title'] ?? '' }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    {{-- DataTables will populate this --}}
                </tbody>
                @if(isset($showFooter) && $showFooter)
                <tfoot>
                    <tr>
                        @foreach($columns as $column)
                            <th>{{ $column['title'] ?? '' }}</th>
                        @endforeach
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        {{-- Custom Slot Content --}}
        @isset($slot)
            {{ $slot }}
        @endisset
    </div>
</div>

<style>
    #{{ $tableId }} th,
    #{{ $tableId }} td {
        text-align: center !important;
        vertical-align: middle;
    }

    #{{ $tableId }}.small-text th,
    #{{ $tableId }}.small-text td {
        font-size: 0.875rem;
        font-weight: 500;
    }

    #{{ $tableId }} thead {
        background-color: var(--bs-light);
    }

    #{{ $tableId }} tbody tr:hover {
        background-color: rgba(var(--bs-primary-rgb), 0.05);
    }

    /* DataTables custom styling */
    #{{ $tableId }}_wrapper .dataTables_length,
    #{{ $tableId }}_wrapper .dataTables_filter,
    #{{ $tableId }}_wrapper .dataTables_info,
    #{{ $tableId }}_wrapper .dataTables_paginate {
        font-size: 0.875rem;
    }

    #{{ $tableId }}_wrapper .dataTables_paginate .paginate_button {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
</style>

@if($showExport)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Export button handlers
        const exportExcel = document.getElementById('{{ $tableId }}_export_excel');
        const exportPdf = document.getElementById('{{ $tableId }}_export_pdf');
        const exportPrint = document.getElementById('{{ $tableId }}_export_print');

        if (exportExcel) {
            exportExcel.addEventListener('click', function() {
                const table = $('#{{ $tableId }}').DataTable();
                table.button('.buttons-excel').trigger();
            });
        }

        if (exportPdf) {
            exportPdf.addEventListener('click', function() {
                const table = $('#{{ $tableId }}').DataTable();
                table.button('.buttons-pdf').trigger();
            });
        }

        if (exportPrint) {
            exportPrint.addEventListener('click', function() {
                const table = $('#{{ $tableId }}').DataTable();
                table.button('.buttons-print').trigger();
            });
        }
    });
</script>
@endif
