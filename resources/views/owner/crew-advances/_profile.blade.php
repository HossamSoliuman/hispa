@php($advances = $user->advances ?? collect())
@php($totalAdvances = $advances->sum('amount'))

<div class="card mb-3">
    <div class="card-header bg-warning text-dark p-2 d-flex align-items-center">
        <h4 class="mb-0">{{ __('owner.crew_advances.section_title') }}</h4>
        <button type="button" class="btn btn-sm btn-dark ms-auto" data-bs-toggle="modal"
            data-bs-target="#addAdvanceModal">
            <i class="bi bi-plus-circle me-1"></i>{{ __('owner.crew_advances.add') }}
        </button>
        <span class="badge bg-dark fs-6 ms-2">
            {{ __('owner.crew_advances.total') }}: {{ number_format($totalAdvances, 2) }} <x-riyal-icon size="sm" />
        </span>
    </div>
    <div class="card-body p-0">
        <table class="table table-sm table-bordered table-hover text-center small-text mb-0">
            <thead>
                <tr>
                    <th>{{ __('owner.crew_advances.col_date') }}</th>
                    <th>{{ __('owner.crew_advances.col_amount') }}</th>
                    <th>{{ __('owner.crew_advances.col_notes') }}</th>
                    <th>{{ __('owner.crew_advances.col_actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($advances as $advance)
                    <tr>
                        <td>{{ optional($advance->date)->format('Y-m-d') }}</td>
                        <td>{{ number_format($advance->amount, 2) }} <x-riyal-icon size="sm" /></td>
                        <td>{{ $advance->notes ?: '-' }}</td>
                        <td>
                            <form method="POST" action="{{ route('owner.crew-advances.destroy', $advance->id) }}"
                                onsubmit="return confirm('{{ __('owner.crew_advances.delete_confirm') }}');"
                                class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-muted">{{ __('owner.crew_advances.empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-arrow">
        <div class="card-arrow-top-left"></div>
        <div class="card-arrow-top-right"></div>
        <div class="card-arrow-bottom-left"></div>
        <div class="card-arrow-bottom-right"></div>
    </div>
</div>
