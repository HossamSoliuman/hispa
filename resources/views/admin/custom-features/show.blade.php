@extends('admin.layouts.master')

@section('title')
    {{ __('admin.custom_features.features.'.$feature->value.'.name') }}
@endsection

@section('css')
    <style>
        .access-title-icon {
            width: 48px;
            height: 48px;
            display: grid;
            place-items: center;
            color: #3675c2;
            border: 1px solid rgba(54, 117, 194, .3);
            background: rgba(54, 117, 194, .08);
            font-size: 1.2rem;
        }

        .access-stat {
            height: 100%;
            padding: 1rem 1.1rem;
            border: 1px solid var(--hud-border);
            border-inline-start: 4px solid var(--stat-color);
            background: transparent;
        }

        .access-stat strong {
            font-size: 1.55rem;
            line-height: 1;
        }

        .owner-result {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: .75rem;
            color: inherit;
            text-align: start;
            border: 1px solid var(--hud-border);
            background: transparent;
        }

        .owner-result:hover,
        .owner-result:focus {
            border-color: #3675c2;
            background: rgba(54, 117, 194, .07);
        }

        .owner-avatar {
            width: 36px;
            height: 36px;
            display: grid;
            flex: 0 0 auto;
            place-items: center;
            color: #fff;
            background: #3675c2;
            font-weight: 800;
        }

        .feature-status-dot {
            width: 8px;
            height: 8px;
            display: inline-block;
            border-radius: 50%;
            background: currentColor;
        }
    </style>
@endsection

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.custom-features.index') }}" class="btn btn-outline-secondary btn-icon" aria-label="{{ __('admin.actions.back') }}">
                <i class="bi bi-arrow-{{ app()->isLocale('ar') ? 'right' : 'left' }}"></i>
            </a>
            <span class="access-title-icon"><i class="bi bi-diagram-3"></i></span>
            <div>
                <div class="text-primary small fw-bold text-uppercase">{{ __('admin.custom_features.title') }}</div>
                <h2 class="fw-bold mb-0">{{ __('admin.custom_features.features.'.$feature->value.'.name') }}</h2>
            </div>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#grantFeatureModal">
            <i class="bi bi-person-plus me-1"></i> {{ __('admin.custom_features.add_owner') }}
        </button>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="access-stat" style="--stat-color: #279765;">
                <div class="d-flex align-items-center justify-content-between gap-2">
                    <div>
                        <strong class="d-block text-success">{{ $activeCount }}</strong>
                        <small class="text-body-secondary">{{ __('admin.custom_features.active_owners') }}</small>
                    </div>
                    <i class="bi bi-unlock fs-3 text-success opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="access-stat" style="--stat-color: #ef8b32;">
                <div class="d-flex align-items-center justify-content-between gap-2">
                    <div>
                        <strong class="d-block text-warning">{{ $pausedCount }}</strong>
                        <small class="text-body-secondary">{{ __('admin.custom_features.paused_owners') }}</small>
                    </div>
                    <i class="bi bi-pause-circle fs-3 text-warning opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.custom-features.show', $feature) }}">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-6">
                        <label for="access-search" class="form-label">{{ __('admin.filters.search') }}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input id="access-search" name="search" value="{{ request('search') }}" class="form-control" placeholder="{{ __('admin.custom_features.search_access_placeholder') }}">
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <label for="access-status" class="form-label">{{ __('admin.custom_features.feature_status') }}</label>
                        <select id="access-status" name="status" class="form-select">
                            <option value="">{{ __('admin.filters.all') }}</option>
                            <option value="active" @selected(request('status') === 'active')>{{ __('admin.custom_features.status.active') }}</option>
                            <option value="paused" @selected(request('status') === 'paused')>{{ __('admin.custom_features.status.paused') }}</option>
                        </select>
                    </div>
                    <div class="col-lg-3 d-flex gap-2">
                        <button class="btn btn-outline-primary flex-grow-1" type="submit"><i class="bi bi-search me-1"></i>{{ __('admin.filters.search') }}</button>
                        <a class="btn btn-outline-secondary" href="{{ route('admin.custom-features.show', $feature) }}" aria-label="{{ __('admin.filters.reset') }}"><i class="bi bi-arrow-counterclockwise"></i></a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between gap-2 py-3">
            <h5 class="fw-bold mb-0">{{ __('admin.custom_features.allowed_owners') }}</h5>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">{{ $accesses->total() }}</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('admin.custom_features.owner') }}</th>
                        <th>{{ __('admin.custom_features.contact') }}</th>
                        <th>{{ __('admin.custom_features.feature_status') }}</th>
                        <th>{{ __('admin.custom_features.granted_at') }}</th>
                        <th>{{ __('admin.custom_features.quick_actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($accesses as $access)
                        <tr>
                            <td>{{ $accesses->firstItem() + $loop->index }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2 text-start">
                                    <span class="owner-avatar">{{ mb_strtoupper(mb_substr($access->user->name, 0, 1)) }}</span>
                                    <div>
                                        <div class="fw-bold">{{ $access->user->name }}</div>
                                        <small class="text-body-secondary">{{ $access->user->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span dir="ltr">{{ $access->user->phone ?: '—' }}</span>
                                @if ((int) $access->user->status !== 1)
                                    <div><small class="text-danger">{{ __('admin.custom_features.owner_account_inactive') }}</small></div>
                                @endif
                            </td>
                            <td>
                                @if ($access->isActive())
                                    <span class="badge bg-success-subtle text-success border border-success-subtle"><span class="feature-status-dot me-1"></span>{{ __('admin.custom_features.status.active') }}</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle"><span class="feature-status-dot me-1"></span>{{ __('admin.custom_features.status.paused') }}</span>
                                @endif
                            </td>
                            <td>
                                <div>{{ $access->created_at->format('Y-m-d') }}</div>
                                @if ($access->grantedByAdmin)
                                    <small class="text-body-secondary">{{ $access->grantedByAdmin->name }}</small>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    @if ($access->isActive())
                                        <form method="POST" action="{{ route('admin.custom-features.access.pause', [$feature, $access]) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-sm btn-outline-warning" title="{{ __('admin.custom_features.pause') }}" aria-label="{{ __('admin.custom_features.pause') }}"><i class="bi bi-pause-fill"></i></button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.custom-features.access.resume', [$feature, $access]) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-sm btn-outline-success" title="{{ __('admin.custom_features.resume') }}" aria-label="{{ __('admin.custom_features.resume') }}"><i class="bi bi-play-fill"></i></button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.custom-features.access.destroy', [$feature, $access]) }}" onsubmit="return confirm('{{ __('admin.custom_features.delete_confirmation') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" title="{{ __('admin.actions.delete') }}" aria-label="{{ __('admin.actions.delete') }}"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-5 text-center">
                                <i class="bi bi-person-lock d-block fs-1 text-body-tertiary mb-2"></i>
                                <strong>{{ __('admin.custom_features.no_allowed_owners') }}</strong>
                                <p class="text-body-secondary small mb-0">{{ __('admin.custom_features.no_allowed_owners_hint') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($accesses->hasPages())
            <div class="card-footer py-3">{{ $accesses->links() }}</div>
        @endif
    </div>

    <div class="modal fade" id="grantFeatureModal" tabindex="-1" aria-labelledby="grantFeatureModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.custom-features.access.store', $feature) }}">
                    @csrf
                    <div class="modal-header">
                        <div>
                            <div class="text-primary small fw-bold">{{ __('admin.custom_features.features.'.$feature->value.'.name') }}</div>
                            <h5 class="modal-title fw-bold" id="grantFeatureModalLabel">{{ __('admin.custom_features.add_owner') }}</h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('admin.actions.close') }}"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-body-secondary small">{{ __('admin.custom_features.add_owner_hint') }}</p>
                        <label for="owner-email" class="form-label fw-bold">{{ __('admin.custom_features.owner_email') }}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" id="owner-email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" autocomplete="off" placeholder="owner@example.com" required>
                        </div>
                        @error('email')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror

                        <div class="d-flex align-items-center gap-2 my-3">
                            <span class="border-top flex-grow-1"></span>
                            <small class="text-body-secondary">{{ __('admin.custom_features.or_fast_search') }}</small>
                            <span class="border-top flex-grow-1"></span>
                        </div>

                        <label for="owner-lookup" class="form-label">{{ __('admin.custom_features.search_owner') }}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input id="owner-lookup" class="form-control" placeholder="{{ __('admin.custom_features.search_owner_placeholder') }}" autocomplete="off">
                        </div>
                        <div id="owner-search-feedback" class="small text-body-secondary mt-2" aria-live="polite"></div>
                        <div id="owner-search-results" class="d-grid gap-2 mt-2"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><i class="bi bi-x-lg me-1"></i>{{ __('admin.actions.cancel') }}</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-unlock me-1"></i>{{ __('admin.custom_features.grant_access') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    @php
        $ownerSearchStrings = [
            'searching' => __('admin.custom_features.searching'),
            'noResults' => __('admin.custom_features.no_search_results'),
            'error' => __('admin.custom_features.search_error'),
            'selected' => __('admin.custom_features.owner_selected'),
        ];
    @endphp
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const lookup = document.getElementById('owner-lookup');
            const emailInput = document.getElementById('owner-email');
            const results = document.getElementById('owner-search-results');
            const feedback = document.getElementById('owner-search-feedback');
            const searchUrl = @json(route('admin.custom-features.owners.search', $feature));
            const strings = {{ Illuminate\Support\Js::from($ownerSearchStrings) }};
            let timer;

            const renderOwners = (owners) => {
                results.replaceChildren();

                if (owners.length === 0) {
                    feedback.textContent = strings.noResults;
                    return;
                }

                feedback.textContent = '';
                owners.forEach((owner) => {
                    const button = document.createElement('button');
                    const identity = document.createElement('span');
                    const avatar = document.createElement('span');
                    const details = document.createElement('span');
                    const name = document.createElement('strong');
                    const email = document.createElement('small');
                    const arrow = document.createElement('i');

                    button.type = 'button';
                    button.className = 'owner-result';
                    identity.className = 'd-flex align-items-center gap-2';
                    avatar.className = 'owner-avatar';
                    avatar.textContent = owner.name.charAt(0).toUpperCase();
                    details.className = 'd-grid';
                    name.textContent = owner.name;
                    email.className = 'text-body-secondary';
                    email.textContent = owner.email;
                    arrow.className = 'bi bi-arrow-{{ app()->isLocale('ar') ? 'left' : 'right' }} text-primary';

                    details.append(name, email);
                    identity.append(avatar, details);
                    button.append(identity, arrow);
                    button.addEventListener('click', () => {
                        emailInput.value = owner.email;
                        emailInput.focus();
                        results.replaceChildren();
                        feedback.textContent = strings.selected;
                    });
                    results.append(button);
                });
            };

            lookup.addEventListener('input', () => {
                window.clearTimeout(timer);
                const query = lookup.value.trim();
                results.replaceChildren();

                if (query.length < 2) {
                    feedback.textContent = '';
                    return;
                }

                feedback.textContent = strings.searching;
                timer = window.setTimeout(async () => {
                    try {
                        const response = await fetch(`${searchUrl}?query=${encodeURIComponent(query)}`, {
                            headers: { 'Accept': 'application/json' },
                        });

                        if (!response.ok) {
                            throw new Error('Owner search failed');
                        }

                        renderOwners((await response.json()).owners);
                    } catch (error) {
                        feedback.textContent = strings.error;
                    }
                }, 250);
            });

            @error('email')
                bootstrap.Modal.getOrCreateInstance(document.getElementById('grantFeatureModal')).show();
            @enderror
        });
    </script>
@endsection
