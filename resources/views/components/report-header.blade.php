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

    <div class="logo-box">
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('default-logo.png'))) }}" alt="Logo">
    </div>
</div>
