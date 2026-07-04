@php
    $companyName = $settings['title'] ?? $settings['company_name'] ?? '';

    $netWeight = $catch->details
        ->map(fn ($detail) => rtrim(rtrim(number_format($detail->weight, 2), '0'), '.') . ' ' . ($detail->unit?->name ?: __('owner.units.kg')))
        ->implode(' ، ');
@endphp

<x-report-layout
    :title="__('owner.reports.product_card')"
    :settings="$settings">

    <x-slot:extraStyles>
        .product-card {
            width: 100%; height: 262mm; margin: 0 auto;
            border-collapse: collapse; table-layout: fixed;
        }
        .product-card td {
            border: 3px solid #1a1a1a; padding: 10px 18px; font-size: 22pt;
            font-weight: 700; color: #1a1a1a; vertical-align: middle; text-align: center;
        }
        .product-card td.pc-label { width: 46%; }
        .product-card td.pc-value { width: 54%; }
        @media print { .product-card { height: 100%; } }
        @media screen { .report-page { display: flex; flex-direction: column; } .product-card { flex: 1; } }
    </x-slot:extraStyles>

    <table class="product-card">
        <tr>
            <td class="pc-label">{{ __('owner.reports.country_of_origin') }} :</td>
            <td class="pc-value">{{ __('owner.reports.saudi_arabia') }}</td>
        </tr>
        <tr>
            <td class="pc-label">{{ __('owner.reports.fish_source') }} :</td>
            <td class="pc-value">{{ $catch->fish_source ?: '—' }}</td>
        </tr>
        <tr>
            <td class="pc-label">{{ __('owner.reports.temperature') }} :</td>
            <td class="pc-value">{{ $catch->temperature ?: '—' }}</td>
        </tr>
        <tr>
            <td class="pc-label">{{ __('owner.reports.net_weight') }} :</td>
            <td class="pc-value">{{ $netWeight ?: '—' }}</td>
        </tr>
        <tr>
            <td class="pc-label">{{ __('owner.reports.season_start') }} :</td>
            <td class="pc-value">{{ $seasonStart->format('Y/m/d') }} م</td>
        </tr>
        <tr>
            <td class="pc-label">{{ __('owner.reports.season_end') }} :</td>
            <td class="pc-value">{{ $seasonEnd->format('Y/m/d') }} م</td>
        </tr>
        <tr>
            <td class="pc-label">{{ __('owner.reports.product_owner_name') }} :</td>
            <td class="pc-value">{{ $companyName ?: '—' }}</td>
        </tr>
    </table>

</x-report-layout>
