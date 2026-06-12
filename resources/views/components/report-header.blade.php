@props([
    'documentNumber' => '',
    'title' => '',
    'titleEn' => '',
    'settings' => [],
])

<div class="header">        
    <div class="doc-num-box">
        @if($documentNumber !== '#00000000')
        <div class="doc-num-label">{{ __('dalal.reports.document_number') }}</div>
        <div class="doc-num">{{ $documentNumber }}</div>
        @endif    
    </div>    

    <div class="title-box">
        <div class="doc-title-ar">{{ $title }}</div>
        <div class="doc-title-en">{{ $titleEn }}</div>
    </div>

    @php
        $reportLogo = '';
        $logoSetting = $settings['logo'] ?? '';

        if (! empty($logoSetting) && \Illuminate\Support\Facades\Storage::disk('public')->exists($logoSetting)) {
            $reportLogo = 'data:'.\Illuminate\Support\Facades\Storage::disk('public')->mimeType($logoSetting)
                .';base64,'.base64_encode(\Illuminate\Support\Facades\Storage::disk('public')->get($logoSetting));
        } elseif (file_exists(public_path('default-logo.png'))) {
            $reportLogo = 'data:image/png;base64,'.base64_encode(file_get_contents(public_path('default-logo.png')));
        }
    @endphp

    <div class="logo-box">
        @if($reportLogo)
            <img src="{{ $reportLogo }}" alt="Logo">
        @endif
    </div>
</div>
