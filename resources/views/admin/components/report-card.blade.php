@props([
    'title' => '',
    'description' => '',
    'icon' => 'bi-file-earmark-text',
    'iconColor' => 'primary',
    'onClick' => null,
    'url' => null,
    'colClass' => 'col-md-6 col-sm-12',
])

{{--
/**
 * Owner Dashboard Report Card Component
 *
 * Clickable card for generating reports
 *
 * @usage
 * <x-owner.report-card
 *     title="تقرير العملاء"
 *     description="تصدير قائمة بجميع العملاء"
 *     icon="bi-people"
 *     iconColor="primary"
 *     onClick="printCustomersReport()"
 * />
 */
--}}

<div class="{{ $colClass }}">
    <div class="report-card {{ $url ? '' : 'cursor-pointer' }}"
         @if($onClick) onclick="{{ $onClick }}" @endif
         @if($url) onclick="window.open('{{ $url }}', '_blank')" @endif>
        <div class="report-icon bg-{{ $iconColor }}">
            <i class="{{ $icon }} fs-1"></i>
        </div>
        <h6 class="mt-3 mb-1">{{ $title }}</h6>
        @if($description)
            <small class="text-muted">{{ $description }}</small>
        @endif
    </div>
</div>

<style>
    .report-card {
        padding: 2rem 1rem;
        text-align: center;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        height: 100%;
    }

    .report-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        border-color: var(--bs-primary);
    }

    .report-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: white;
        margin: 0 auto;
    }

    .cursor-pointer {
        cursor: pointer;
    }
</style>
