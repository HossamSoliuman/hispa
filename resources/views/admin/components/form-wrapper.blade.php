@props([
    'id' => null,
    'action' => '',
    'method' => 'POST',
    'title' => null,
    'subtitle' => null,
    'showCard' => true,
    'showArrow' => true,
    'cardClass' => '',
    'bodyClass' => '',
    'showSubmitButton' => true,
    'submitText' => 'حفظ',
    'submitIcon' => 'bi-check-lg',
    'submitClass' => 'btn-primary',
    'showCancelButton' => true,
    'cancelText' => 'إلغاء',
    'cancelIcon' => 'bi-x-lg',
    'cancelUrl' => null,
    'footerClass' => '',
    'enctype' => null,
])

{{--
/**
 * Owner Dashboard Form Wrapper Component
 *
 * Standardized form with card styling and consistent submit/cancel buttons
 *
 * @usage
 * <x-owner.form-wrapper
 *     :action="route('owner.boats.store')"
 *     method="POST"
 *     title="إضافة قارب جديد"
 *     subtitle="أدخل تفاصيل القارب"
 *     submitText="إضافة"
 *     :cancelUrl="route('owner.boats.index')"
 * >
 *     <!-- Form fields here -->
 *     <div class="mb-3">
 *         <label class="form-label">اسم القارب</label>
 *         <input type="text" name="name" class="form-control" required>
 *     </div>
 * </x-owner.form-wrapper>
 */
--}}

@php
$formId = $id ?? 'ownerForm_' . uniqid();
$formMethod = strtoupper($method);
$actualMethod = in_array($formMethod, ['GET', 'POST']) ? $formMethod : 'POST';
$enctypeAttr = $enctype ? "enctype=\"{$enctype}\"" : '';
@endphp

@if($showCard)
<x-card-wrapper
    :showArrow="$showArrow"
    :cardClass="$cardClass"
    :bodyClass="$bodyClass">

    @if($title || $subtitle)
    <x-slot:header>
        <div class="d-flex align-items-center justify-content-between">
            <div>
                @if($title)
                <h5 class="card-title mb-0">{{ $title }}</h5>
                @endif
                @if($subtitle)
                <p class="text-muted mb-0 small">{{ $subtitle }}</p>
                @endif
            </div>
        </div>
    </x-slot:header>
    @endif

    <form id="{{ $formId }}"
          action="{{ $action }}"
          method="{{ $actualMethod }}"
          {!! $enctypeAttr !!}
          class="form-wrapper-form">
        @csrf

        @if($formMethod !== 'GET' && $formMethod !== 'POST')
            @method($formMethod)
        @endif

        <div class="form-body">
            {{ $slot }}
        </div>

        @if($showSubmitButton || $showCancelButton)
        <div class="form-footer {{ $footerClass }} d-flex align-items-center gap-2 mt-4 pt-3 border-top">
            @if($showCancelButton)
                @if($cancelUrl)
                <a href="{{ $cancelUrl }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                    @if($cancelIcon)
                    <i class="{{ $cancelIcon }}"></i>
                    @endif
                    <span>{{ $cancelText }}</span>
                </a>
                @else
                <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2" onclick="window.history.back()">
                    @if($cancelIcon)
                    <i class="{{ $cancelIcon }}"></i>
                    @endif
                    <span>{{ $cancelText }}</span>
                </button>
                @endif
            @endif

            @if($showSubmitButton)
            <button type="submit" class="btn {{ $submitClass }} d-inline-flex align-items-center gap-2">
                @if($submitIcon)
                <i class="{{ $submitIcon }}"></i>
                @endif
                <span>{{ $submitText }}</span>
            </button>
            @endif

            @isset($footerExtra)
                {{ $footerExtra }}
            @endisset
        </div>
        @endif
    </form>
</x-card-wrapper>
@else
<form id="{{ $formId }}"
      action="{{ $action }}"
      method="{{ $actualMethod }}"
      {!! $enctypeAttr !!}
      class="form-wrapper-form">
    @csrf

    @if($formMethod !== 'GET' && $formMethod !== 'POST')
        @method($formMethod)
    @endif

    @if($title || $subtitle)
    <div class="form-header mb-4">
        @if($title)
        <h5 class="form-title mb-1">{{ $title }}</h5>
        @endif
        @if($subtitle)
        <p class="text-muted mb-0 small">{{ $subtitle }}</p>
        @endif
    </div>
    @endif

    <div class="form-body">
        {{ $slot }}
    </div>

    @if($showSubmitButton || $showCancelButton)
    <div class="form-footer {{ $footerClass }} d-flex align-items-center gap-2 mt-4 pt-3 border-top">
        @if($showCancelButton)
            @if($cancelUrl)
            <a href="{{ $cancelUrl }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                @if($cancelIcon)
                <i class="{{ $cancelIcon }}"></i>
                @endif
                <span>{{ $cancelText }}</span>
            </a>
            @else
            <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2" onclick="window.history.back()">
                @if($cancelIcon)
                <i class="{{ $cancelIcon }}"></i>
                @endif
                <span>{{ $cancelText }}</span>
            </button>
            @endif
        @endif

        @if($showSubmitButton)
        <button type="submit" class="btn {{ $submitClass }} d-inline-flex align-items-center gap-2">
            @if($submitIcon)
            <i class="{{ $submitIcon }}"></i>
            @endif
            <span>{{ $submitText }}</span>
        </button>
        @endif

        @isset($footerExtra)
            {{ $footerExtra }}
        @endisset
    </div>
    @endif
</form>
@endif

<style>
    /* Form Wrapper Styling */
    .form-wrapper-form {
        position: relative;
    }

    /* Form Header */
    .form-header {
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #e9ecef;
    }

    .form-title {
        font-weight: 600;
        color: #212529;
        font-size: 1.125rem;
    }

    /* Form Body */
    .form-body {
        position: relative;
    }

    /* Form Groups */
    .form-wrapper-form .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.5rem;
        font-size: 0.9375rem;
    }

    .form-wrapper-form .form-label.required::after {
        content: " *";
        color: #dc3545;
    }

    .form-wrapper-form .form-control,
    .form-wrapper-form .form-select {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        padding: 0.5rem 0.75rem;
        font-size: 0.9375rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .form-wrapper-form .form-control:focus,
    .form-wrapper-form .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }

    /* Validation States */
    .form-wrapper-form .is-invalid {
        border-color: #dc3545;
    }

    .form-wrapper-form .is-invalid:focus {
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    .form-wrapper-form .invalid-feedback {
        display: block;
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .form-wrapper-form .is-valid {
        border-color: #198754;
    }

    .form-wrapper-form .is-valid:focus {
        box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
    }

    /* Form Footer */
    .form-footer {
        background-color: transparent;
    }

    /* Loading State */
    .form-wrapper-form.loading {
        opacity: 0.6;
        pointer-events: none;
    }

    .form-wrapper-form.loading::after {
        content: "";
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 40px;
        height: 40px;
        border: 3px solid #f3f3f3;
        border-top: 3px solid #0d6efd;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        z-index: 10;
    }

    @keyframes spin {
        0% { transform: translate(-50%, -50%) rotate(0deg); }
        100% { transform: translate(-50%, -50%) rotate(360deg); }
    }

    /* Help Text */
    .form-wrapper-form .form-text {
        font-size: 0.875rem;
        color: #6c757d;
        margin-top: 0.25rem;
    }

    /* Input Groups */
    .form-wrapper-form .input-group-text {
        background-color: #e9ecef;
        border: 1px solid #ced4da;
        color: #495057;
    }

    /* File Input */
    .form-wrapper-form .form-control[type="file"] {
        padding: 0.4rem 0.75rem;
    }

    /* Checkbox and Radio */
    .form-wrapper-form .form-check-input {
        width: 1.125rem;
        height: 1.125rem;
        border: 1px solid #ced4da;
        cursor: pointer;
    }

    .form-wrapper-form .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .form-wrapper-form .form-check-label {
        margin-right: 0.5rem;
        cursor: pointer;
    }

    /* Responsive */
    @media (max-width: 576px) {
        .form-footer {
            flex-direction: column-reverse !important;
        }

        .form-footer > * {
            width: 100%;
        }

        .form-footer .btn {
            justify-content: center;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add loading state on form submit
        document.querySelectorAll('.form-wrapper-form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                // Check if form is valid (HTML5 validation)
                if (form.checkValidity()) {
                    form.classList.add('loading');
                }
            });
        });

        // Auto-remove validation classes on input change
        document.querySelectorAll('.form-wrapper-form .is-invalid').forEach(function(input) {
            input.addEventListener('input', function() {
                this.classList.remove('is-invalid');
                const feedback = this.nextElementSibling;
                if (feedback && feedback.classList.contains('invalid-feedback')) {
                    feedback.remove();
                }
            });
        });
    });
</script>
