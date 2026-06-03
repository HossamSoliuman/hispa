@extends('admin.layouts.master')
@section('title')
    {{ __('admin.menu.notifications') }}
@endsection
@section('content')
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
            <h2 class="fw-bold text-dark mb-1">{{ __('admin.menu.notifications') }}</h2>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('admin.notifications.title') }}</th>
                            <th>{{ __('admin.notifications.body') }}</th>
                            <th>{{ __('admin.notifications.date') }}</th>
                            <th>{{ __('admin.notifications.status') }}</th>
                            <th>{{ __('admin.notifications.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notifications as $notification)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $notification->data['title'] ?? __('admin.notifications.title') }}</td>
                            <td>{{ Str::limit($notification->data['body'] ?? '', 50) }}</td>
                            <td>{{ $notification->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                @if($notification->read_at)
                                    <span class="badge bg-secondary">{{ __('admin.notifications.read') }}</span>
                                @else
                                    <span class="badge bg-primary">{{ __('admin.notifications.unread') }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.notifications.read', $notification->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">{{ __('admin.notifications.no_notifications') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
@endsection
