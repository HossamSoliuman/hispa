@props([
    'formId' => 'datatableFilters',
    'filters' => [],
    'colClass' => '',
    'showResetButton' => true,
    'showSearchButton' => false,
    'formAction' => null,
    'formMethod' => 'get',
])

@once
<style>
    .filter-box-wrap { display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: flex-end; }
    .filter-form-field { min-width: 120px; }
    .filter-form-field .form-label { margin-bottom: 0.35rem; font-size: 0.9rem; color: #495057; font-weight: 500; }
    .filter-form-field .form-control,
    .filter-form-field .form-select {
        background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 6px;
        padding: 0.5rem 0.75rem; font-size: 0.9rem; transition: border-color 0.15s ease;
    }
    .filter-form-field .form-control:focus,
    .filter-form-field .form-select:focus {
        border-color: #adb5bd; outline: none; background-color: #fff;
    }
    .filter-form-btn {
        display: inline-flex; align-items: center; padding: 0.5rem 1rem; border-radius: 6px;
        font-size: 0.9rem; font-weight: 500; border: 1px solid #dee2e6; background-color: #f8f9fa;
        color: #6c757d; text-decoration: none; cursor: pointer; transition: all 0.15s ease;
    }
    .filter-form-btn:hover { background-color: #e9ecef; color: #495057; }
</style>
@endonce

<div class="mb-4">
    <form id="{{ $formId }}" class="filter-box-wrap"
        @if($formAction) method="{{ strtolower($formMethod) }}" action="{{ $formAction }}" @endif>
        @foreach($filters as $filter)
            @php
                $f = (object) array_merge([
                    'colClass' => $colClass,
                    'id' => '', 'name' => '', 'label' => '', 'type' => 'text',
                    'placeholder' => '', 'value' => '', 'selected' => '', 'options' => [],
                    'optionValue' => 'id', 'optionLabel' => 'name',
                    'nameFrom' => 'from_date', 'nameTo' => 'to_date', 'valueFrom' => '', 'valueTo' => '',
                    'required' => false, 'disabled' => false, 'readonly' => false,
                    'class' => '', 'attributes' => '', 'help' => '', 'rows' => 2, 'checkValue' => '1',
                ], $filter);
            @endphp
            <div class="filter-form-field {{ $f->colClass }}">
                @if($f->label && !in_array($f->type, ['checkbox', 'switch']))
                    <label class="form-label d-block" for="{{ $f->id }}">{{ $f->label }}</label>
                @endif
                @if($f->type === 'select')
                    <select id="{{ $f->id }}" name="{{ $f->name }}" class="form-select form-control {{ $f->class }}"
                        @if($f->required) required @endif @if($f->disabled) disabled @endif {!! $f->attributes !!}>
                        <option value="">{{ $f->placeholder ?: __('admin.filters.all') }}</option>
                        @foreach($f->options as $opt)
                            @php $v = is_object($opt) ? $opt->{$f->optionValue} : ($opt[$f->optionValue] ?? $opt['value'] ?? ''); $l = is_object($opt) ? $opt->{$f->optionLabel} : ($opt[$f->optionLabel] ?? $opt['label'] ?? ''); @endphp
                            <option value="{{ $v }}" {{ (string)$f->selected === (string)$v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                @elseif($f->type === 'select-static')
                    <select id="{{ $f->id }}" name="{{ $f->name }}" class="form-select form-control {{ $f->class }}"
                        @if($f->required) required @endif @if($f->disabled) disabled @endif {!! $f->attributes !!}>
                        @foreach($f->options as $opt)
                            <option value="{{ $opt['value'] ?? '' }}" {{ (string)($f->selected ?? '') === (string)($opt['value'] ?? '') ? 'selected' : '' }}>{{ $opt['label'] ?? '' }}</option>
                        @endforeach
                    </select>
                @elseif($f->type === 'daterange')
                    <div class="d-flex gap-1">
                        <input type="date" id="{{ $f->id }}_from" name="{{ $f->nameFrom }}" class="form-control {{ $f->class }}"
                            value="{{ $f->valueFrom ?? '' }}" @if($f->required) required @endif {!! $f->attributes !!}>
                        <input type="date" id="{{ $f->id }}_to" name="{{ $f->nameTo }}" class="form-control {{ $f->class }}"
                            value="{{ $f->valueTo ?? '' }}" @if($f->required) required @endif {!! $f->attributes !!}>
                    </div>
                @elseif(in_array($f->type, ['text', 'email', 'tel', 'url', 'number']))
                    <input type="{{ $f->type }}" id="{{ $f->id }}" name="{{ $f->name }}" class="form-control {{ $f->class }}"
                        placeholder="{{ $f->placeholder }}" value="{{ $f->value }}"
                        @if($f->required) required @endif @if($f->disabled) disabled @endif @if($f->readonly) readonly @endif {!! $f->attributes !!}>
                @elseif($f->type === 'date')
                    <input type="date" id="{{ $f->id }}" name="{{ $f->name }}" class="form-control {{ $f->class }}"
                        value="{{ $f->value }}" @if($f->required) required @endif {!! $f->attributes !!}>
                @elseif($f->type === 'datetime')
                    <input type="datetime-local" id="{{ $f->id }}" name="{{ $f->name }}" class="form-control {{ $f->class }}"
                        value="{{ $f->value }}" @if($f->required) required @endif {!! $f->attributes !!}>
                @elseif($f->type === 'select-multiple')
                    <select id="{{ $f->id }}" name="{{ $f->name }}[]" class="form-select form-control {{ $f->class }}" multiple {!! $f->attributes !!}>
                        @foreach($f->options as $opt)
                            @php $v = is_object($opt) ? $opt->{$f->optionValue} : ($opt[$f->optionValue] ?? $opt['value'] ?? ''); $l = is_object($opt) ? $opt->{$f->optionLabel} : ($opt[$f->optionLabel] ?? $opt['label'] ?? ''); $sel = is_array($f->selected ?? null) ? $f->selected : []; @endphp
                            <option value="{{ $v }}" {{ in_array($v, $sel) ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                @elseif($f->type === 'textarea')
                    <textarea id="{{ $f->id }}" name="{{ $f->name }}" class="form-control {{ $f->class }}" placeholder="{{ $f->placeholder }}" rows="{{ $f->rows }}" {!! $f->attributes !!}>{{ $f->value }}</textarea>
                @elseif($f->type === 'checkbox')
                    <div class="form-check"><input type="checkbox" id="{{ $f->id }}" name="{{ $f->name }}" class="form-check-input {{ $f->class }}" value="{{ $f->checkValue }}" {{ $f->value == $f->checkValue ? 'checked' : '' }} {!! $f->attributes !!}><label class="form-check-label" for="{{ $f->id }}">{{ $f->label }}</label></div>
                @elseif($f->type === 'switch')
                    <div class="form-check form-switch"><input type="checkbox" id="{{ $f->id }}" name="{{ $f->name }}" class="form-check-input {{ $f->class }}" value="{{ $f->checkValue }}" {{ $f->value == $f->checkValue ? 'checked' : '' }} {!! $f->attributes !!}><label class="form-check-label" for="{{ $f->id }}">{{ $f->label }}</label></div>
                @elseif($f->type === 'custom')
                    {!! $filter['html'] ?? '' !!}
                @endif
                @if($f->help ?? null)<small class="form-text text-muted">{{ $f->help }}</small>@endif
            </div>
        @endforeach
        @if($showResetButton || $showSearchButton)
            <div class="filter-form-field d-flex align-items-end gap-1">
                @if($showSearchButton)
                    <button type="submit" class="filter-form-btn" style="background:#2980b9;color:#fff;border-color:#2980b9;">
                        <i class="bi bi-search me-1"></i>{{ __('admin.filters.search') }}
                    </button>
                @endif
                @if($showResetButton)
                    @if($formAction)
                        <a href="{{ $formAction }}" class="filter-form-btn" id="{{ $formId }}_reset">
                            <i class="bi bi-x-circle me-1"></i>{{ __('admin.filters.reset') }}
                        </a>
                    @else
                        <button type="button" class="filter-form-btn" id="{{ $formId }}_reset">
                            <i class="bi bi-x-circle me-1"></i>{{ __('admin.filters.reset') }}
                        </button>
                    @endif
                @endif
            </div>
        @endif
    </form>
</div>

@if($showResetButton && !$formAction)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('{{ $formId }}_reset');
    const form = document.getElementById('{{ $formId }}');
    if (btn && btn.tagName === 'BUTTON' && form) {
        btn.addEventListener('click', function() { form.reset(); form.querySelectorAll('select, input').forEach(el => el.dispatchEvent(new Event('change', { bubbles: true }))); });
    }
});
</script>
@endif
