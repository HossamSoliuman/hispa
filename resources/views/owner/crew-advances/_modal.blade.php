@php($people = $people ?? collect())
@php($selectedUserId = old('user_id', $selectedUserId ?? null))

<div class="modal fade" id="addAdvanceModal" tabindex="-1" aria-labelledby="addAdvanceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('owner.crew-advances.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addAdvanceModalLabel">{{ __('owner.crew_advances.modal_title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="{{ __('owner.crew_advances.cancel') }}"></button>
                </div>
                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">{{ __('owner.crew_advances.person') }} <span class="text-danger">*</span></label>
                        <select name="user_id" class="form-select" required>
                            <option value="">{{ __('owner.crew_advances.select_person') }}</option>
                            @foreach ($people as $person)
                                <option value="{{ $person->id }}" {{ (string) $selectedUserId === (string) $person->id ? 'selected' : '' }}>
                                    {{ $person->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('owner.crew_advances.amount') }} <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0.01" name="amount" class="form-control"
                            value="{{ old('amount') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('owner.crew_advances.date') }} <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control"
                            value="{{ old('date', now()->toDateString()) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('owner.crew_advances.notes') }}</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ __('owner.crew_advances.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('owner.crew_advances.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var el = document.getElementById('addAdvanceModal');
            if (el && window.bootstrap) {
                bootstrap.Modal.getOrCreateInstance(el).show();
            }
        });
    </script>
@endif
