<div class="d-flex align-items-center mb-3">
    <h4 class="mb-2">{{ __('admin.settings.tabs.general') }}</h4>
</div>

<div class="card border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover text-center small-text">
                <thead>
                    <tr>
                        <th>{{ __('admin.table.id') }}</th>
                        <th>{{ __('admin.settings.key') }}</th>
                        <th>{{ __('admin.settings.value') }}</th>
                        <th>{{ __('admin.settings.type') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->key }}</td>
                        <td class="text-start">
                            @if($item->type == 'image')
                                @if($item->getRawOriginal('value'))
                                    <img src="{{ Storage::url($item->getRawOriginal('value')) }}" alt="" class="img-thumbnail" style="max-height: 40px;">
                                @else
                                    —
                                @endif
                            @else
                                {{ Str::limit($item->value ?? '—', 50) }}
                            @endif
                        </td>
                        <td><span class="badge bg-secondary">{{ $item->type ?? 'text' }}</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">{{ __('admin.no_data.0') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
