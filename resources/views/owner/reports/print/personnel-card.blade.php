@php
    /**
     * Clean, government-ready data card for a captain / crew member / employee.
     * Sections are rendered only when they hold data, so the single view adapts
     * to each role (captains carry licences; employees usually don't).
     */
    $isSaudi = ($user->nationality ?? '') === __('owner.generated.saudi');
    $docNumber = '#' . str_pad($user->id, 8, '0', STR_PAD_LEFT);

    $hasLicenses = filled($user->fishing_license_number) || filled($user->fishing_license_expiry)
        || filled($user->driving_license_number) || filled($user->driving_license_expiry)
        || filled($user->residence_start_date) || filled($user->residence_end_date);

    $hasFinancial = filled($user->salary_type) || filled($user->salary_amount)
        || filled($user->bank_name) || filled($user->account_number) || filled($user->IBAN);

    $fmtDate = fn ($v) => filled($v) ? \Illuminate\Support\Carbon::parse($v)->format('Y-m-d') : '—';
    $val = fn ($v) => filled($v) ? $v : '—';

    $salaryTypeText = match ($user->salary_type) {
        'salary' => __('owner.crew.edit.salary_option_salary'),
        'percentage' => __('owner.crew.edit.salary_option_percentage'),
        default => '—',
    };
@endphp

<x-report-layout
    :title="$title"
    :document-number="$docNumber"
    :settings="$settings"
    :qr-code="$settings['qr_code'] ?? null">

    <x-report-masthead
        :title="$title"
        :subtitle="__('owner.reports.personnel_subtitle')"
        :settings="$settings" />

    {{-- Registration number + status --}}
    <table class="info-bar" style="width:70%; margin-left:auto; margin-right:auto;">
        <tr>
            <td>
                <span class="ib-label">{{ __('owner.reports.personnel_registration_no') }}</span>
                <span class="ib-value">{{ $docNumber }}</span>
            </td>
            <td>
                <span class="ib-label">{{ __('owner.assets.name') }}</span>
                <span class="ib-value">{{ $val($user->name) }}</span>
            </td>
            <td>
                <span class="ib-label">{{ __('owner.customers.modal.labels.status') }}</span>
                <span class="ib-value">
                    @if ($user->status)
                        {{ __('owner.status.active') }}
                    @else
                        {{ __('owner.status.inactive') }}
                    @endif
                </span>
            </td>
        </tr>
    </table>

    {{-- Personal data --}}
    <table class="report-table">
        <thead><tr><th colspan="4">{{ __('owner.reports.personnel_section_personal') }}</th></tr></thead>
        <tbody>
            <tr>
                <th class="col-text" style="width:18%;">{{ __('owner.employee.table.nationality') }}</th>
                <td class="col-text" style="width:32%;">{{ $val($user->nationality) }}</td>
                <th class="col-text" style="width:18%;">{{ __('owner.crew.edit.phone') }}</th>
                <td class="col-text" style="width:32%;">{{ $val($user->phone) }}</td>
            </tr>
            <tr>
                <th class="col-text">{{ __('owner.customers.modal.labels.email') }}</th>
                <td class="col-text">{{ $val($user->email) }}</td>
                <th class="col-text">{{ __('owner.employee.table.job_title') }}</th>
                <td class="col-text">{{ $val($user->job_title) }}</td>
            </tr>
            <tr>
                @if ($isSaudi)
                    <th class="col-text">{{ __('owner.crew.edit.id_number') }}</th>
                    <td class="col-text">{{ $val($user->id_number) }}</td>
                @else
                    <th class="col-text">{{ __('owner.crew.edit.residence_number') }}</th>
                    <td class="col-text">{{ $val($user->residence_number) }}</td>
                @endif
                <th class="col-text">{{ __('owner.crew.edit.passport_number') }}</th>
                <td class="col-text">{{ $val($user->passport_number) }}</td>
            </tr>
            <tr>
                <th class="col-text">{{ __('owner.sales.boat') }}</th>
                <td class="col-text">{{ $val($user->boat?->name) }}</td>
                <th class="col-text">{{ __('owner.crew.edit.region') }}</th>
                <td class="col-text">{{ $val($user->region?->name) }}</td>
            </tr>
            <tr>
                <th class="col-text">{{ __('owner.crew.edit.governorate') }}</th>
                <td class="col-text">{{ $val($user->governorate?->name) }}</td>
                <th class="col-text">{{ __('owner.crew.table.port') }}</th>
                <td class="col-text">{{ $val($user->port?->name) }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Licenses & dates --}}
    @if ($hasLicenses)
        <table class="report-table" style="margin-top:10px;">
            <thead><tr><th colspan="4">{{ __('owner.reports.personnel_section_licenses') }}</th></tr></thead>
            <tbody>
                <tr>
                    <th class="col-text" style="width:18%;">{{ __('owner.generated.fishing_license') }}</th>
                    <td class="col-text" style="width:32%;">{{ $val($user->fishing_license_number) }}</td>
                    <th class="col-text" style="width:18%;">{{ __('owner.generated.fishing_license_expiry_date') }}</th>
                    <td class="col-text" style="width:32%;">{{ $fmtDate($user->fishing_license_expiry) }}</td>
                </tr>
                <tr>
                    <th class="col-text">{{ __('owner.generated.driving_license') }}</th>
                    <td class="col-text">{{ $val($user->driving_license_number) }}</td>
                    <th class="col-text">{{ __('owner.generated.license_expiry') }}</th>
                    <td class="col-text">{{ $fmtDate($user->driving_license_expiry) }}</td>
                </tr>
                @if (filled($user->residence_start_date) || filled($user->residence_end_date))
                    <tr>
                        <th class="col-text">{{ __('owner.crew.edit.residence_start_date') }}</th>
                        <td class="col-text">{{ $fmtDate($user->residence_start_date) }}</td>
                        <th class="col-text">{{ __('owner.crew.edit.residence_end_date') }}</th>
                        <td class="col-text">{{ $fmtDate($user->residence_end_date) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    @endif

    {{-- Financial & bank data --}}
    @if ($hasFinancial)
        <table class="report-table" style="margin-top:10px;">
            <thead><tr><th colspan="4">{{ __('owner.reports.personnel_section_financial') }}</th></tr></thead>
            <tbody>
                <tr>
                    <th class="col-text" style="width:18%;">{{ __('owner.payrolls.show.salary_type') }}</th>
                    <td class="col-text" style="width:32%;">{{ $salaryTypeText }}</td>
                    <th class="col-text" style="width:18%;">{{ __('owner.crew.edit.salary_amount') }}</th>
                    <td class="col-text" style="width:32%;">
                        @if (! filled($user->salary_amount))
                            {{ $val($user->salary_amount) }}
                        @elseif ($user->salary_type === 'percentage')
                            {{ number_format((float) $user->salary_amount, 2) }}%
                        @else
                            <x-report-money :amount="$user->salary_amount" />
                        @endif
                    </td>
                </tr>
                <tr>
                    <th class="col-text">{{ __('owner.dalal.modal.form.bank_name') }}</th>
                    <td class="col-text">{{ $val($user->bank_name) }}</td>
                    <th class="col-text">{{ __('owner.dalal.modal.form.bank_account') }}</th>
                    <td class="col-text">{{ $val($user->account_number) }}</td>
                </tr>
                <tr>
                    <th class="col-text">IBAN</th>
                    <td class="col-text" colspan="3">{{ $val($user->IBAN) }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    <x-report-signatures :items="[
        __('owner.reports.personnel_sig_person'),
        __('owner.reports.personnel_sig_manager'),
    ]" />

    <table class="report-footer">
        <tr>
            <td>{{ $settings['title'] ?? $settings['company_name'] ?? '' }} — {{ __('owner.reports.all_rights_reserved') }} © {{ date('Y') }}</td>
        </tr>
    </table>

</x-report-layout>
