@extends('admin.layouts.master')
@section('title')
    {{ __('admin.coupons.show.title') }}
@endsection
@section('content')
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
            <h2 class="fw-bold text-dark mb-1">{{ __('admin.coupons.show.title') }}</h2>
        </div>
        <div class="col-md-6 col-sm-12 text-md-end text-sm-start">
            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-outline-theme btn-equal me-1">
                <i class="bi bi-pencil"></i> {{ __('admin.actions.edit') }}
            </a>
            <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary btn-equal">
                <i class="bi bi-arrow-left"></i> {{ __('admin.actions.back') ?? 'رجوع' }}
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <th class="text-muted" style="width: 180px;">{{ __('admin.coupons.name') }}</th>
                            <td>{{ $coupon->name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">{{ __('admin.coupons.code') }}</th>
                            <td><code class="bg-light px-2 py-1 rounded">{{ $coupon->code }}</code></td>
                        </tr>
                        <tr>
                            <th class="text-muted">{{ __('admin.coupons.type') }}</th>
                            <td>{{ $coupon->type === 'percentage' ? __('admin.coupons.percentage') : __('admin.coupons.fixed') }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">{{ __('admin.coupons.value') }}</th>
                            <td>{{ $coupon->formatted_value }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">{{ __('admin.coupons.usage_limit') }}</th>
                            <td>{{ $coupon->usage_limit ?? __('admin.coupons.unlimited') }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">{{ __('admin.coupons.times_used') }}</th>
                            <td>{{ $coupon->times_used }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">{{ __('admin.coupons.valid_from') }}</th>
                            <td>{{ $coupon->valid_from ? $coupon->valid_from->format('Y-m-d H:i') : '—' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">{{ __('admin.coupons.valid_until') }}</th>
                            <td>{{ $coupon->valid_until ? $coupon->valid_until->format('Y-m-d H:i') : '—' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">{{ __('admin.coupons.status') }}</th>
                            <td>
                                @if($coupon->is_active)
                                    <span class="badge bg-success">{{ __('admin.status.active') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('admin.status.inactive') }}</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    @php $preview = $coupon->getDiscountPreview(1000); @endphp
                    <div class="card border-primary bg-light mb-3">
                        <div class="card-body py-3">
                            <h6 class="card-title text-primary mb-2">{{ __('admin.coupons.discount_preview') }}</h6>
                            <p class="text-muted small mb-2">{{ __('admin.coupons.sample_amount') }}: 1,000 {{ __('admin.units.sar') }}</p>
                            <p class="mb-1">
                                <span class="text-muted">{{ __('admin.coupons.discount_amount') }}:</span>
                                <strong class="text-danger">{{ number_format($preview['discount'], 2) }}</strong> {{ __('admin.units.sar') }}
                            </p>
                            <p class="mb-0">
                                <span class="text-muted">{{ __('admin.coupons.amount_after_discount') }}:</span>
                                <strong class="text-success">{{ number_format($preview['final_amount'], 2) }}</strong> {{ __('admin.units.sar') }}
                            </p>
                        </div>
                    </div>
                    @if($coupon->description_ar || $coupon->description_en)
                        <p class="text-muted small mb-1">{{ __('admin.coupons.description_ar') }} / {{ __('admin.coupons.description_en') }}</p>
                        <p>{{ app()->getLocale() == 'ar' ? ($coupon->description_ar ?? $coupon->description_en) : ($coupon->description_en ?? $coupon->description_ar) }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ __('admin.invoices.title') }} ({{ $coupon->invoices->count() }})</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('admin.invoices.invoice_number') }}</th>
                            <th>{{ __('admin.invoices.user') }}</th>
                            <th>{{ __('admin.invoices.amount') }}</th>
                            <th>{{ __('admin.invoices.total_amount') }}</th>
                            <th>{{ __('admin.invoices.payment_status') }}</th>
                            <th>{{ __('admin.invoices.payment_date') ?? 'التاريخ' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($coupon->invoices as $inv)
                        <tr>
                            <td><a href="{{ route('admin.invoices.show', $inv) }}">{{ $inv->invoice_number }}</a></td>
                            <td>{{ $inv->user?->name ?? '—' }}</td>
                            <td>{{ number_format($inv->amount, 2) }}</td>
                            <td>{{ number_format($inv->total_amount, 2) }}</td>
                            <td><span class="badge bg-{{ $inv->payment_status === 'paid' ? 'success' : 'warning' }}">{{ $inv->payment_status }}</span></td>
                            <td>{{ $inv->paid_at ? $inv->paid_at->format('Y-m-d') : '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">{{ __('admin.invoices.no_invoices') ?? 'لا توجد فواتير' }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
