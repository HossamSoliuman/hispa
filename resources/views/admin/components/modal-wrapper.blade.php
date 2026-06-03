@props([
    'id' => 'ownerModal',
    'title' => '',
    'size' => 'md', // sm, md, lg, xl, fullscreen
    'centered' => true,
    'scrollable' => true,
    'backdrop' => 'true', // true, false, 'static'
    'keyboard' => 'true',
    'showHeader' => true,
    'showFooter' => true,
    'showCloseButton' => true,
    'headerClass' => '',
    'bodyClass' => '',
    'footerClass' => '',
    'formId' => null,
    'formAction' => null,
    'formMethod' => 'POST',
])

{{--
/**
 * Owner Dashboard Modal Wrapper Component
 *
 * Flexible modal with header/body/footer slots and form support
 *
 * @usage
 * <x-owner.modal-wrapper
 *     id="addBoatModal"
 *     title="إضافة قارب جديد"
 *     size="lg"
 *     :formAction="route('owner.boats.store')"
 *     formMethod="POST"
 * >
 *     <x-slot:body>
 *         <!-- Form fields here -->
 *     </x-slot:body>
 *
 *     <x-slot:footer>
 *         <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
 *         <button type="submit" class="btn btn-primary" form="addBoatForm">حفظ</button>
 *     </x-slot:footer>
 * </x-owner.modal-wrapper>
 */
--}}

@php
$sizeClass = match($size) {
    'sm' => 'modal-sm',
    'lg' => 'modal-lg',
    'xl' => 'modal-xl',
    'fullscreen' => 'modal-fullscreen',
    default => '',
};

$dialogClass = [
    $sizeClass,
    $centered ? 'modal-dialog-centered' : '',
    $scrollable ? 'modal-dialog-scrollable' : '',
];

$dialogClassStr = implode(' ', array_filter($dialogClass));

$formIdGenerated = $formId ?? $id . 'Form';
@endphp

<div class="modal fade"
     id="{{ $id }}"
     tabindex="-1"
     aria-labelledby="{{ $id }}Label"
     aria-hidden="true"
     data-bs-backdrop="{{ $backdrop }}"
     data-bs-keyboard="{{ $keyboard }}">
    <div class="modal-dialog {{ $dialogClassStr }}">
        <div class="modal-content">
            @if($showHeader)
            <div class="modal-header {{ $headerClass }}">
                <h5 class="modal-title" id="{{ $id }}Label">
                    {{ $title }}
                    @isset($titleExtra)
                        {{ $titleExtra }}
                    @endisset
                </h5>
                @if($showCloseButton)
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                @endif
            </div>
            @endif

            @if($formAction)
                <form id="{{ $formIdGenerated }}"
                      action="{{ $formAction }}"
                      method="{{ $formMethod === 'GET' ? 'GET' : 'POST' }}"
                      enctype="multipart/form-data">
                    @if($formMethod !== 'GET')
                        @csrf
                    @endif

                    @if(!in_array(strtoupper($formMethod), ['GET', 'POST']))
                        @method($formMethod)
                    @endif

                    <div class="modal-body {{ $bodyClass }}">
                        @isset($body)
                            {{ $body }}
                        @else
                            {{ $slot }}
                        @endisset
                    </div>

                    @if($showFooter)
                    <div class="modal-footer {{ $footerClass }}">
                        @isset($footer)
                            {{ $footer }}
                        @else
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="bi bi-x-lg me-1"></i>إلغاء
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>حفظ
                            </button>
                        @endisset
                    </div>
                    @endif
                </form>
            @else
                <div class="modal-body {{ $bodyClass }}">
                    @isset($body)
                        {{ $body }}
                    @else
                        {{ $slot }}
                    @endisset
                </div>

                @if($showFooter)
                <div class="modal-footer {{ $footerClass }}">
                    @isset($footer)
                        {{ $footer }}
                    @else
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-lg me-1"></i>إغلاق
                        </button>
                    @endisset
                </div>
                @endif
            @endif
        </div>
    </div>
</div>

<style>
    /* Modal Header Styling */
    .modal-header {
        background: linear-gradient(135deg, #0d6efd, #0b5ed7);
        color: white;
        border-bottom: none;
        padding: 1rem 1.5rem;
    }

    .modal-header .modal-title {
        font-weight: 600;
        font-size: 1.125rem;
    }

    .modal-header .btn-close {
        filter: brightness(0) invert(1);
        opacity: 0.8;
    }

    .modal-header .btn-close:hover {
        opacity: 1;
    }

    /* Modal Body */
    .modal-body {
        padding: 1.5rem;
    }

    /* Modal Footer */
    .modal-footer {
        background-color: #f8f9fa;
        border-top: 1px solid #dee2e6;
        padding: 1rem 1.5rem;
    }

    /* Smooth animations */
    .modal.fade .modal-dialog {
        transition: transform 0.3s ease-out;
    }

    /* Fullscreen modal adjustments */
    .modal-fullscreen .modal-body {
        overflow-y: auto;
    }

    /* Scrollable modal */
    .modal-dialog-scrollable .modal-body {
        overflow-y: auto;
        max-height: calc(100vh - 200px);
    }

    /* Form validation styles */
    .modal .is-invalid {
        border-color: #dc3545;
    }

    .modal .invalid-feedback {
        display: block;
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    /* Loading state */
    .modal.loading .modal-content {
        opacity: 0.6;
        pointer-events: none;
    }

    /* Responsive adjustments */
    @media (max-width: 576px) {
        .modal-body {
            padding: 1rem;
        }

        .modal-footer {
            padding: 0.75rem 1rem;
            flex-direction: column-reverse;
            gap: 0.5rem;
        }

        .modal-footer > * {
            width: 100%;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Reset form on modal hide
        document.querySelectorAll('.modal').forEach(function(modalEl) {
            modalEl.addEventListener('hidden.bs.modal', function() {
                const form = modalEl.querySelector('form');
                if (form) {
                    form.reset();
                    // Remove validation classes
                    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                    form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
                }
            });
        });

        // Handle form submission
        document.querySelectorAll('.modal form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                const modal = form.closest('.modal');
                if (modal) {
                    modal.classList.add('loading');
                }
            });
        });
    });
</script>
