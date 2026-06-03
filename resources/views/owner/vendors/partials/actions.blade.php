<a href="{{ route('owner.vendors.edit', $vendor->id) }}" class="btn btn-outline-primary"><i class="bi bi-pencil"></i></a>
<a href="#" onclick="window.open('{{ route('owner.reports.print.vendor', $vendor->id) }}', '_blank')" class="btn btn-outline-secondary" title="{{ __('owner.vendors.report') }}">
	<i class="bi bi-printer"></i>
	{{-- <span class="d-none d-md-inline"> {{ __('owner.vendors.report') }}</span> --}}
</a>
<button data-id="{{ $vendor->id }}" class="btn btn-outline-danger deleteVendor"><i class="bi bi-trash"></i></button>
