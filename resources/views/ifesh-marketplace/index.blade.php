@extends('ifesh-marketplace.layout')

@section('content')
    {{-- Hero Section / Stats --}}
    {{-- Hero Section / Stats --}}
    <div class="row mb-4">
        {{-- Available Items --}}
        <div class="col-xl-3 col-md-6">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex fw-bold small mb-3">
                        <span class="flex-grow-1">{{ __('admin.ifesh.available_items') }}</span>
                        <a href="#" data-toggle="card-expand" class="text-white text-opacity-50 text-decoration-none"><i class="bi bi-fullscreen"></i></a>
                    </div>
                    <div class="row align-items-center mb-2">
                        <div class="col-7">
                            <h3 class="mb-0">{{ $items->total() }}</h3>
                        </div>
                        <div class="col-5">
                            <div class="mt-n2" data-render="apexchart" data-type="bar" data-title="Items" data-height="30"></div>
                        </div>
                    </div>
                    <div class="small text-white text-opacity-50 text-truncate">
                        <i class="bi bi-check-circle-fill text-success me-1"></i> {{ __('admin.ifesh.live') }}
                    </div>
                </div>
                <div class="card-arrow">
                    <div class="card-arrow-top-left"></div>
                    <div class="card-arrow-top-right"></div>
                    <div class="card-arrow-bottom-left"></div>
                    <div class="card-arrow-bottom-right"></div>
                </div>
            </div>
        </div>
        
        {{-- Active Auctions --}}
        <div class="col-xl-3 col-md-6">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex fw-bold small mb-3">
                        <span class="flex-grow-1">{{ __('admin.ifesh.active_auctions') }}</span>
                        <a href="#" data-toggle="card-expand" class="text-white text-opacity-50 text-decoration-none"><i class="bi bi-fullscreen"></i></a>
                    </div>
                    <div class="row align-items-center mb-2">
                        <div class="col-7">
                            <h3 class="mb-0">{{ \App\Models\IfeshAuction::where('status', 'active')->count() }}</h3>
                        </div>
                        <div class="col-5">
                            <div class="mt-n2" data-render="apexchart" data-type="line" data-title="Auctions" data-height="30"></div>
                        </div>
                    </div>
                    <div class="small text-white text-opacity-50 text-truncate">
                        <i class="bi bi-clock-history text-warning me-1"></i> {{ __('admin.ifesh.ending_soon') }}
                    </div>
                </div>
                <div class="card-arrow">
                    <div class="card-arrow-top-left"></div>
                    <div class="card-arrow-top-right"></div>
                    <div class="card-arrow-bottom-left"></div>
                    <div class="card-arrow-bottom-right"></div>
                </div>
            </div>
        </div>

        {{-- Total Bids --}}
        <div class="col-xl-3 col-md-6">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex fw-bold small mb-3">
                        <span class="flex-grow-1">{{ __('admin.ifesh.bids') }}</span>
                        <a href="#" data-toggle="card-expand" class="text-white text-opacity-50 text-decoration-none"><i class="bi bi-fullscreen"></i></a>
                    </div>
                    <div class="row align-items-center mb-2">
                        <div class="col-7">
                            <h3 class="mb-0">{{ $totalBids }}</h3>
                        </div>
                        <div class="col-5">
                            <div class="mt-n2" data-render="apexchart" data-type="bar" data-title="Bids" data-height="30"></div>
                        </div>
                    </div>
                    <div class="small text-white text-opacity-50 text-truncate">
                        <i class="bi bi-graph-up text-theme me-1"></i> {{ __('admin.ifesh.has_bids') }}
                    </div>
                </div>
                <div class="card-arrow">
                    <div class="card-arrow-top-left"></div>
                    <div class="card-arrow-top-right"></div>
                    <div class="card-arrow-bottom-left"></div>
                    <div class="card-arrow-bottom-right"></div>
                </div>
            </div>
        </div>

        {{-- Highest Bid --}}
        <div class="col-xl-3 col-md-6">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex fw-bold small mb-3">
                        <span class="flex-grow-1">{{ __('admin.ifesh.max_price') }}</span>
                        <a href="#" data-toggle="card-expand" class="text-white text-opacity-50 text-decoration-none"><i class="bi bi-fullscreen"></i></a>
                    </div>
                    <div class="row align-items-center mb-2">
                        <div class="col-7">
                            <h3 class="mb-0">{{ number_format($highestBid, 0) }} <small class="fs-12px text-white text-opacity-50">SAR</small></h3>
                        </div>
                        <div class="col-5">
                            <div class="mt-n2" data-render="apexchart" data-type="line" data-title="Price" data-height="30"></div>
                        </div>
                    </div>
                    <div class="small text-white text-opacity-50 text-truncate">
                        <i class="bi bi-trophy text-warning me-1"></i> {{ __('admin.ifesh.live') }}
                    </div>
                </div>
                <div class="card-arrow">
                    <div class="card-arrow-top-left"></div>
                    <div class="card-arrow-top-right"></div>
                    <div class="card-arrow-bottom-left"></div>
                    <div class="card-arrow-bottom-right"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Filters Sidebar --}}
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-header fw-bold small d-flex align-items-center">
                    {{ __('admin.actions.filter') }}
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('ifesh.marketplace') }}">
                        <div class="mb-3">
                            <label class="form-label small text-white text-opacity-75">{{ __('admin.ifesh.fish_type') }}</label>
                            <select name="fish_id" class="form-select form-select-sm">
                                <option value="">{{ __('admin.filters.all') }}</option>
                                @foreach($fishTypes as $fish)
                                    <option value="{{ $fish->id }}" {{ request('fish_id') == $fish->id ? 'selected' : '' }}>
                                        {{ $fish->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label small text-white text-opacity-75">{{ __('admin.ifesh.min_price') }}</label>
                            <input type="number" name="min_price" class="form-control form-control-sm" value="{{ request('min_price') }}">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label small text-white text-opacity-75">{{ __('admin.ifesh.max_price') }}</label>
                            <input type="number" name="max_price" class="form-control form-control-sm" value="{{ request('max_price') }}">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label small text-white text-opacity-75">{{ __('admin.ifesh.sort_by') }}</label>
                            <select name="sort" class="form-select form-select-sm">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>{{ __('admin.ifesh.latest') }}</option>
                                <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>{{ __('admin.ifesh.price_low_high') }}</option>
                                <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>{{ __('admin.ifesh.price_high_low') }}</option>
                                <option value="ending_soon" {{ request('sort') == 'ending_soon' ? 'selected' : '' }}>{{ __('admin.ifesh.ending_soon') }}</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-theme w-100 btn-sm">
                            <i class="bi bi-funnel me-1"></i> {{ __('admin.actions.filter') }}
                        </button>
                    </form>
                </div>
                <div class="card-arrow">
                    <div class="card-arrow-top-left"></div>
                    <div class="card-arrow-top-right"></div>
                    <div class="card-arrow-bottom-left"></div>
                    <div class="card-arrow-bottom-right"></div>
                </div>
            </div>
        </div>

        {{-- Items Grid --}}
        <div class="col-lg-9">
            <div class="row g-3">
                @forelse($items as $item)
                    <div class="col-md-6 col-xl-4">
                        <div class="card h-100">
                            <div class="card-body d-flex flex-column">
                                {{-- Badge --}}
                                <div class="position-absolute top-0 end-0 m-2">
                                    <span class="badge bg-danger bg-opacity-20 text-danger border border-danger border-opacity-25">
                                        {{ __('admin.ifesh.live') }}
                                        <span class="spinner-grow spinner-grow-sm ms-1" role="status" style="width: 0.5rem; height: 0.5rem;"></span>
                                    </span>
                                </div>

                                {{-- Icon --}}
                                <div class="text-center py-4 position-relative overflow-hidden">
                                    <i class="bi bi-fish text-theme text-opacity-50" style="font-size: 4rem;"></i>
                                    <i class="bi bi-water fish-icon-bg"></i>
                                </div>

                                {{-- Title & Info --}}
                                <h5 class="card-title mb-3 text-center">{{ $item->fish->name }}</h5>
                                
                                <div class="row g-2 mb-3 small">
                                    <div class="col-6">
                                        <div class="p-2 border border-white border-opacity-10 rounded text-center">
                                            <div class="text-white text-opacity-50">{{ __('admin.ifesh.quantity') }}</div>
                                            <div class="fw-bold">{{ number_format($item->quantity, 2) }} {{ __('admin.units.kg') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-2 border border-white border-opacity-10 rounded text-center">
                                            <div class="text-white text-opacity-50">{{ __('admin.ifesh.bids') }}</div>
                                            <div class="fw-bold">{{ $item->bids_count }}</div>
                                        </div>
                                    </div>
                                    
                                    @if($item->arrival_time)
                                    <div class="col-12 mt-2">
                                        <div class="small text-white text-opacity-75 text-center">
                                            <i class="bi bi-clock me-1"></i> وصول: {{ \Carbon\Carbon::parse($item->arrival_time)->format('Y-m-d H:i') }}
                                        </div>
                                    </div>
                                    @endif
                                    
                                    @if($item->notes)
                                    <div class="col-12 mt-1">
                                        <div class="small text-white text-opacity-50 text-center text-truncate" title="{{ $item->notes }}">
                                            <i class="bi bi-info-circle me-1"></i> {{ $item->notes }}
                                        </div>
                                    </div>
                                    @endif
                                </div>

                                {{-- Price & Timer --}}
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-end mb-3">
                                        <div>
                                            <div class="small text-white text-opacity-50">{{ __('admin.ifesh.current_price') }}</div>
                                            <div class="h3 mb-0 text-theme">
                                                {{ number_format($item->current_bid ?? $item->starting_price, 2) }}
                                                <small class="fs-6 text-white text-opacity-50">{{ __('admin.units.sar') }}</small>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="small text-white text-opacity-50">{{ __('admin.ifesh.time_remaining') }}</div>
                                            <div class="fw-bold text-warning countdown-timer" data-end="{{ $item->auction->end_date->toIso8601String() }}">
                                                {{ $item->auction->end_date->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>

                                    <a href="{{ route('ifesh.marketplace.show', $item->id) }}" class="btn btn-outline-theme w-100 d-flex align-items-center justify-content-center">
                                        <i class="bi bi-hammer me-2"></i> {{ __('admin.ifesh.place_bid') }}
                                    </a>
                                </div>
                            </div>
                            <div class="card-arrow">
                                <div class="card-arrow-top-left"></div>
                                <div class="card-arrow-top-right"></div>
                                <div class="card-arrow-bottom-left"></div>
                                <div class="card-arrow-bottom-right"></div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="bi bi-inbox display-1 text-white text-opacity-25"></i>
                                <h3 class="mt-3 text-white text-opacity-75">{{ __('admin.ifesh.no_items_available') }}</h3>
                                <p class="text-white text-opacity-50">{{ __('admin.ifesh.check_back_later') }}</p>
                            </div>
                            <div class="card-arrow">
                                <div class="card-arrow-top-left"></div>
                                <div class="card-arrow-top-right"></div>
                                <div class="card-arrow-bottom-left"></div>
                                <div class="card-arrow-bottom-right"></div>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($items->hasPages())
                <div class="mt-4">
                    {{ $items->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.countdown-timer').forEach(function(element) {
        const endDate = new Date(element.dataset.end);
        
        function updateCountdown() {
            const now = new Date();
            const diff = endDate - now;
            
            if (diff <= 0) {
                element.textContent = '{{ __("admin.ifesh.auction_ended") }}';
                element.classList.add('text-danger');
                element.classList.remove('text-warning');
                return;
            }
            
            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);
            
            let countdown = '';
            if (days > 0) countdown += days + 'd ';
            countdown += hours.toString().padStart(2, '0') + ':' + 
                        minutes.toString().padStart(2, '0') + ':' + 
                        seconds.toString().padStart(2, '0');
            
            element.textContent = countdown;
        }
        
        updateCountdown();
        setInterval(updateCountdown, 1000);
    });
</script>
@endpush
