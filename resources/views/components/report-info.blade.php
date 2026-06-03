@props([
    'settings' => [],
    'fromDate' => null,
    'toDate' => null,
    'additionalInfo' => null,
])

<div class="info-section">
    {{-- <div class="info-col">
        <div class="info-label">{{ __('dalal.reports.company_info') }}</div>
        <p><strong>{{ $settings['company_name'] ?? __('dalal.settings.company_name') }}</strong></p>
        @if(!empty($settings['email']))
            <p>{{ __('dalal.settings.email') }}: {{ $settings['email'] }}</p>
        @endif
        @if(!empty($settings['phone']))
            <p>{{ __('dalal.settings.phone') }}: {{ $settings['phone'] }}</p>
        @endif
        @if(!empty($settings['address']))
            <p>{{ __('dalal.settings.address') }}: {{ $settings['address'] }}</p>
        @endif
    </div> --}}
    <div class="info-col">
        <div class="info-label">{{ __('dalal.reports.report_info') }}</div>
        @if($fromDate && $toDate)
            <p><strong>{{ __('dalal.reports.period') }}:</strong></p>
            <p>{{ __('dalal.reports.from') }}: @hijri($fromDate)</p>
            <p>{{ __('dalal.reports.to') }}: @hijri($toDate)</p>
        @endif
        <p><strong>{{ __('dalal.reports.generated_at') }}:</strong> @hijri(now()) {{ now()->format('H:i:s') }}</p>
        @if($additionalInfo)
            {{ $additionalInfo }}
        @endif
    </div>
</div>
