@props([
    'documentNumber' => '',
    'title' => '',
    'titleEn' => '',
    'subtitle' => '',
    'settings' => [],
])

{{-- Unified report header. Delegates to the masthead so the company band,
     tax number, page count and centered title render identically across every
     report (legacy callers keep using <x-report-header>). The English title is
     used as the subtitle when no explicit Arabic subtitle is provided. --}}
<x-report-masthead
    :title="$title"
    :subtitle="$subtitle ?: $titleEn"
    :settings="$settings" />
