{{-- ================== TAB: BOATS (Add Boat Wizard) ================== --}}

<div id="boatWizard"
    data-store-boat="{{ route('owner.boats.store') }}"
    data-store-captain="{{ route('owner.captain.store') }}"
    data-store-crew="{{ route('owner.crew.store') }}"
    data-store-maintenance="{{ route('owner.maintenance.store') }}"
    data-store-inspection="{{ route('owner.inspections.store') }}"
    data-governorates-url="{{ route('owner.getGovernorates', ['region_id' => 'REGION_ID']) }}"
    data-ports-url="{{ route('owner.getPorts', ['gov_id' => 'GOV_ID']) }}">

    <div class="d-flex align-items-center mb-1">
        <h4 class="mb-0">{{ __('owner.boat_wizard.title') }}</h4>
    </div>
    <p class="text-muted">{{ __('owner.boat_wizard.subtitle') }}</p>

    {{-- Step indicator --}}
    <ul class="nav nav-pills mb-4 boat-wizard-steps">
        <li class="nav-item"><span class="nav-link active" data-step-pill="1">1. {{ __('owner.boat_wizard.step_boat') }}</span></li>
        <li class="nav-item"><span class="nav-link" data-step-pill="2">2. {{ __('owner.boat_wizard.step_captain') }}</span></li>
        <li class="nav-item"><span class="nav-link" data-step-pill="3">3. {{ __('owner.boat_wizard.step_crew') }}</span></li>
        <li class="nav-item"><span class="nav-link" data-step-pill="4">4. {{ __('owner.boat_wizard.step_maintenance') }}</span></li>
        <li class="nav-item"><span class="nav-link" data-step-pill="5">5. {{ __('owner.boat_wizard.step_inspection') }}</span></li>
    </ul>

    {{-- ============ STEP 1: BOAT ============ --}}
    <div class="wizard-step" data-step="1">
        <div class="card">
            <div class="card-body pb-2">
                <form id="wizardBoatForm" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">{{ __('owner.boats.name_ara') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name_ar" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('owner.boats.name_eng') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name_en" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label d-flex justify-content-between align-items-center">
                                <span>{{ __('owner.boats.class') }} <span class="text-danger">*</span></span>
                                <a href="#" class="small text-decoration-none" id="boatTypeQuickAddToggle">
                                    <i class="fa fa-plus-circle me-1"></i>{{ __('owner.actions.add') }}
                                </a>
                            </label>
                            <select name="boat_type_id" id="boat_type_id" required class="form-control">
                                <option value="">{{ __('owner.actions.choose') }}</option>
                                @foreach ($boatTypes as $boat_type)
                                    <option value="{{ $boat_type->id }}">{{ $boat_type->name }}</option>
                                @endforeach
                            </select>

                            {{-- Inline quick-add for boat type --}}
                            <div id="boatTypeQuickAdd" class="border rounded p-2 mt-2 d-none"
                                data-store-url="{{ route('owner.boat-types.store') }}">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <input type="text" class="form-control form-control-sm" id="quickBoatTypeNameAr"
                                            placeholder="{{ __('owner.boats.name_ara') }}">
                                    </div>
                                    <div class="col-6">
                                        <input type="text" class="form-control form-control-sm" id="quickBoatTypeNameEn"
                                            placeholder="{{ __('owner.boats.name_eng') }}">
                                    </div>
                                </div>
                                <div class="text-danger small mt-1 d-none" id="quickBoatTypeError"></div>
                                <div class="d-flex gap-2 mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-theme" id="quickBoatTypeSave">
                                        {{ __('owner.actions.save') }}
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-default" id="quickBoatTypeCancel">
                                        {{ __('owner.actions.close') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('owner.boats.boat_number') }} <span class="text-danger">*</span></label>
                            <input type="text" name="number" class="form-control" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">{{ __('owner.boats.status') }} <span class="text-danger">*</span></label>
                            <select name="status" required class="form-control">
                                <option value="1" selected>{{ __('owner.status.active') }}</option>
                                <option value="0">{{ __('owner.status.inactive') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('owner.boats.length') }} <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="length" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('owner.boats.width') }} <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="width" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('owner.boats.color') }} <span class="text-danger">*</span></label>
                            <input type="text" name="color" class="form-control" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">{{ __('owner.boats.type') }} <span class="text-danger">*</span></label>
                            <input type="text" name="type" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('owner.boats.license_region') }} <span class="text-danger">*</span></label>
                            <select name="license_region_id" required class="form-control">
                                <option value="">-- {{ __('owner.boats.select_region') }} --</option>
                                @foreach ($regions as $region)
                                    <option value="{{ $region->id }}">{{ $region->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('owner.boats.license_date') }} <span class="text-danger">*</span></label>
                            <input type="date" name="license_date" required class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('owner.boats.license_date_expire') }} <span class="text-danger">*</span></label>
                            <input type="date" name="license_date_expire" required class="form-control">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">{{ __('owner.boats.structure_number') }} <span class="text-danger">*</span></label>
                            <input type="text" name="body_number" required class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('owner.boats.structure_type') }} <span class="text-danger">*</span></label>
                            <input type="text" name="body_type" required class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('owner.boats.callsign_number') }} <span class="text-danger">*</span></label>
                            <input type="text" name="callsign_number" required class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('owner.boats.serial_number') }} <span class="text-danger">*</span></label>
                            <input type="text" name="serial_number" required class="form-control">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">{{ __('owner.boats.engine_type') }} <span class="text-danger">*</span></label>
                            <input type="text" required name="engine_type" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('owner.boats.engine_power') }} <span class="text-danger">*</span></label>
                            <input type="text" required name="engine_power" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('owner.boats.crew_count') }} <span class="text-danger">*</span></label>
                            <input type="number" required name="crew_number" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('owner.boats.cargo_capacity') }} <span class="text-danger">*</span></label>
                            <input type="number" required step="0.01" name="payload" class="form-control">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.boats.region') }} <span class="text-danger">*</span></label>
                            <select name="region_id" id="boat_region_id" required class="form-control wizard-region"
                                data-gov-target="#boat_governorate_id" data-port-target="#boat_port_id">
                                <option value="">-- {{ __('owner.boats.select_region') }} --</option>
                                @foreach ($regions as $region)
                                    <option value="{{ $region->id }}">{{ $region->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.boats.governorate') }} <span class="text-danger">*</span></label>
                            <select name="governorate_id" id="boat_governorate_id" required class="form-control wizard-governorate"
                                data-port-target="#boat_port_id">
                                <option value="">-- {{ __('owner.boats.select_governorate') }} --</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.boats.port') }} <span class="text-danger">*</span></label>
                            <select name="port_id" id="boat_port_id" required class="form-control">
                                <option value="">-- {{ __('owner.boats.select_port') }} --</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4 mb-3 text-end">
                        <button type="submit" class="btn btn-outline-theme" data-action="next">
                            {{ __('owner.actions.save_continue') }} <i class="fa fa-arrow-right ms-1"></i>
                        </button>
                    </div>
                </form>
            </div>
            <div class="card-arrow">
                <div class="card-arrow-top-left"></div>
                <div class="card-arrow-top-right"></div>
                <div class="card-arrow-bottom-left"></div>
                <div class="card-arrow-bottom-right"></div>
            </div>
        </div>
    </div>

    {{-- ============ STEP 2: CAPTAIN ============ --}}
    <div class="wizard-step" data-step="2" style="display:none">
        <div class="card">
            <div class="card-body pb-2">
                <p class="text-muted">{{ __('owner.boat_wizard.optional_hint') }}</p>
                <form id="wizardCaptainForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="boat_id" class="wizard-boat-id">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.assets.name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.customers.modal.labels.email') }} <span class="text-danger">*</span></label>
                            <input type="text" name="email" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.crew.edit.image') }}</label>
                            <input type="file" name="logo" class="form-control">
                        </div>

                        <div class="col-md-4 mt-3">
                            <label class="form-label">{{ __('owner.employee.table.nationality') }} <span class="text-danger">*</span></label>
                            <select name="nationality" class="form-control wizard-nationality" data-prefix="cap">
                                <option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>
                                <option value="{{ __('owner.generated.saudi') }}">{{ __('owner.generated.saudi') }}</option>
                                <option value="{{ __('owner.generated.egyptian') }}">{{ __('owner.generated.egyptian') }}</option>
                                <option value="{{ __('owner.generated.sudanese') }}">{{ __('owner.generated.sudanese') }}</option>
                                <option value="{{ __('owner.generated.yemeni') }}">{{ __('owner.generated.yemeni') }}</option>
                                <option value="{{ __('owner.generated.bangladeshi') }}">{{ __('owner.generated.bangladeshi') }}</option>
                                <option value="{{ __('owner.generated.indian') }}">{{ __('owner.generated.indian') }}</option>
                                <option value="{{ __('owner.generated.pakistani') }}">{{ __('owner.generated.pakistani') }}</option>
                                <option value="{{ __('owner.generated.filipino') }}">{{ __('owner.generated.filipino') }}</option>
                                <option value="{{ __('owner.generated.ethiopian') }}">{{ __('owner.generated.ethiopian') }}</option>
                                <option value="{{ __('owner.generated.nepali') }}">{{ __('owner.generated.nepali') }}</option>
                                <option value="{{ __('owner.assets.other') }}">{{ __('owner.assets.other') }}</option>
                            </select>
                        </div>

                        {{-- Saudi only --}}
                        <div class="col-md-4 mt-3 cap-saudi-fields">
                            <label class="form-label">{{ __('owner.crew.edit.id_number') }} <span class="text-danger">*</span></label>
                            <input type="text" name="id_number" class="form-control">
                        </div>
                        <div class="col-md-4 mt-3 cap-saudi-fields">
                            <label class="form-label">{{ __('owner.crew.edit.id_attachment') }}</label>
                            <input type="file" name="id_attachment" class="form-control">
                        </div>

                        {{-- Non-Saudi --}}
                        <div class="col-md-4 mt-3 cap-non-saudi-fields">
                            <label class="form-label">{{ __('owner.crew.edit.residence_number') }} <span class="text-danger">*</span></label>
                            <input type="text" name="residence_number" class="form-control">
                        </div>
                        <div class="col-md-4 mt-3 cap-non-saudi-fields">
                            <label class="form-label">{{ __('owner.crew.edit.passport_number') }} <span class="text-danger">*</span></label>
                            <input type="text" name="passport_number" class="form-control">
                        </div>
                        <div class="col-md-4 mt-3 cap-non-saudi-fields">
                            <label class="form-label">{{ __('owner.generated.passport_attachment') }}/{{ __('owner.generated.residence_permit') }} <span class="text-danger">*</span></label>
                            <input type="file" name="attachment" class="form-control">
                        </div>
                        <div class="col-md-4 mt-3 cap-non-saudi-fields">
                            <label class="form-label">{{ __('owner.crew.edit.residence_start_date') }} <span class="text-danger">*</span></label>
                            <input type="date" name="residence_start_date" class="form-control">
                        </div>
                        <div class="col-md-4 mt-3 cap-non-saudi-fields">
                            <label class="form-label">{{ __('owner.crew.edit.residence_end_date') }} <span class="text-danger">*</span></label>
                            <input type="date" name="residence_end_date" class="form-control">
                        </div>

                        <div class="col-md-4 mt-3">
                            <label class="form-label">{{ __('owner.employee.table.job_title') }} <span class="text-danger">*</span></label>
                            <input type="text" name="job_title" class="form-control">
                        </div>
                        <div class="col-md-4 mt-3">
                            <label class="form-label">{{ __('owner.crew.edit.phone') }} <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="col-md-4 mt-3">
                            <label class="form-label">{{ __('owner.payrolls.show.salary_type') }} <span class="text-danger">*</span></label>
                            <select name="salary_type" class="form-control wizard-salary-type" data-prefix="cap">
                                <option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>
                                <option value="salary">{{ __('owner.crew.edit.salary_option_salary') }}</option>
                                <option value="percentage">{{ __('owner.payrolls.show.percentage') }}%</option>
                            </select>
                        </div>
                        <div class="col-md-4 mt-3 cap-salary-value">
                            <label class="form-label">{{ __('owner.crew.edit.salary_amount') }} <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="salary_amount" class="form-control">
                        </div>

                        <div class="col-md-4 mt-3">
                            <label class="form-label">{{ __('owner.generated.fishing_license') }} <span class="text-danger">*</span></label>
                            <input type="text" name="fishing_license_number" class="form-control">
                        </div>
                        <div class="col-md-4 mt-3">
                            <label class="form-label">{{ __('owner.generated.fishing_license_expiry_date') }} <span class="text-danger">*</span></label>
                            <input type="date" name="fishing_license_expiry" class="form-control">
                        </div>
                        <div class="col-md-4 mt-3">
                            <label class="form-label">{{ __('owner.generated.driving_license') }} <span class="text-danger">*</span></label>
                            <input type="text" name="driving_license_number" class="form-control">
                        </div>
                        <div class="col-md-4 mt-3">
                            <label class="form-label">{{ __('owner.generated.license_expiry') }} <span class="text-danger">*</span></label>
                            <input type="date" name="driving_license_expiry" class="form-control">
                        </div>

                        <div class="col-md-4 mt-3">
                            <label class="form-label">{{ __('owner.dalal.modal.form.bank_name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="bank_name" class="form-control">
                        </div>
                        <div class="col-md-4 mt-3">
                            <label class="form-label">{{ __('owner.dalal.modal.form.bank_account') }} <span class="text-danger">*</span></label>
                            <input type="text" name="account_number" class="form-control">
                        </div>
                        <div class="col-md-4 mt-3">
                            <label class="form-label">IBAN <span class="text-danger">*</span></label>
                            <input type="text" name="IBAN" class="form-control">
                        </div>

                        <div class="col-md-4 mt-3">
                            <label class="form-label">{{ __('owner.generated.region_name') }} <span class="text-danger">*</span></label>
                            <select name="region_id" id="cap_region_id" class="form-control wizard-region"
                                data-gov-target="#cap_governorate_id">
                                <option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>
                                @foreach ($regions as $region)
                                    <option value="{{ $region->id }}">{{ $region->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mt-3">
                            <label class="form-label">{{ __('owner.generated.governorate_name') }} <span class="text-danger">*</span></label>
                            <select name="governorate_id" id="cap_governorate_id" class="form-control">
                                <option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>
                            </select>
                        </div>

                        <div class="col-md-4 mt-3">
                            <label class="form-label">{{ __('owner.crew.edit.password') }} <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="col-md-4 mt-3">
                            <label class="form-label">{{ __('owner.crew.edit.password_confirmation') }} <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>

                        <div class="col-md-4 mt-3">
                            <div class="form-check form-switch" style="margin-top: 35px">
                                <input type="checkbox" name="status" class="form-check-input" value="1" checked>
                                <label class="form-check-label">{{ __('owner.customers.modal.labels.status') }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 mb-3 d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-default" data-action="skip">{{ __('owner.actions.skip') }}</button>
                        <button type="submit" class="btn btn-outline-theme" data-action="next">{{ __('owner.actions.save_continue') }}</button>
                    </div>
                </form>
            </div>
            <div class="card-arrow">
                <div class="card-arrow-top-left"></div>
                <div class="card-arrow-top-right"></div>
                <div class="card-arrow-bottom-left"></div>
                <div class="card-arrow-bottom-right"></div>
            </div>
        </div>
    </div>

    {{-- ============ STEP 3: CREW ============ --}}
    <div class="wizard-step" data-step="3" style="display:none">
        <div class="card">
            <div class="card-body pb-2">
                <p class="text-muted">{{ __('owner.boat_wizard.optional_hint') }}</p>

                {{-- Added crew list --}}
                <div class="mb-3">
                    <h6 class="mb-2">{{ __('owner.boat_wizard.added_crew') }}</h6>
                    <ul class="list-group" id="crewAddedList">
                        <li class="list-group-item text-muted" id="crewEmptyItem">{{ __('owner.boat_wizard.no_crew_added') }}</li>
                    </ul>
                </div>

                <form id="wizardCrewForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="boat_id" class="wizard-boat-id">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.assets.name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.customers.modal.labels.email') }} <span class="text-danger">*</span></label>
                            <input type="text" name="email" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('owner.crew.edit.image') }}</label>
                            <input type="file" name="logo" class="form-control">
                        </div>

                        <div class="col-md-4 mt-3">
                            <label class="form-label">{{ __('owner.employee.table.nationality') }} <span class="text-danger">*</span></label>
                            <select name="nationality" class="form-control wizard-nationality" data-prefix="crew">
                                <option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>
                                <option value="{{ __('owner.generated.saudi') }}">{{ __('owner.generated.saudi') }}</option>
                                <option value="{{ __('owner.generated.egyptian') }}">{{ __('owner.generated.egyptian') }}</option>
                                <option value="{{ __('owner.generated.sudanese') }}">{{ __('owner.generated.sudanese') }}</option>
                                <option value="{{ __('owner.generated.yemeni') }}">{{ __('owner.generated.yemeni') }}</option>
                                <option value="{{ __('owner.generated.bangladeshi') }}">{{ __('owner.generated.bangladeshi') }}</option>
                                <option value="{{ __('owner.generated.indian') }}">{{ __('owner.generated.indian') }}</option>
                                <option value="{{ __('owner.generated.pakistani') }}">{{ __('owner.generated.pakistani') }}</option>
                                <option value="{{ __('owner.generated.filipino') }}">{{ __('owner.generated.filipino') }}</option>
                                <option value="{{ __('owner.generated.ethiopian') }}">{{ __('owner.generated.ethiopian') }}</option>
                                <option value="{{ __('owner.generated.nepali') }}">{{ __('owner.generated.nepali') }}</option>
                                <option value="{{ __('owner.assets.other') }}">{{ __('owner.assets.other') }}</option>
                            </select>
                        </div>

                        {{-- Saudi only --}}
                        <div class="col-md-4 mt-3 crew-saudi-fields">
                            <label class="form-label">{{ __('owner.crew.edit.id_number') }} <span class="text-danger">*</span></label>
                            <input type="text" name="id_number" class="form-control">
                        </div>
                        <div class="col-md-4 mt-3 crew-saudi-fields">
                            <label class="form-label">{{ __('owner.crew.edit.id_attachment') }}</label>
                            <input type="file" name="id_attachment" class="form-control">
                        </div>

                        {{-- Non-Saudi --}}
                        <div class="col-md-4 mt-3 crew-non-saudi-fields">
                            <label class="form-label">{{ __('owner.crew.edit.residence_number') }} <span class="text-danger">*</span></label>
                            <input type="text" name="residence_number" class="form-control">
                        </div>
                        <div class="col-md-4 mt-3 crew-non-saudi-fields">
                            <label class="form-label">{{ __('owner.crew.edit.passport_number') }} <span class="text-danger">*</span></label>
                            <input type="text" name="passport_number" class="form-control">
                        </div>
                        <div class="col-md-4 mt-3 crew-non-saudi-fields">
                            <label class="form-label">{{ __('owner.generated.passport_attachment') }}/{{ __('owner.generated.residence_permit') }} <span class="text-danger">*</span></label>
                            <input type="file" name="attachment" class="form-control">
                        </div>
                        <div class="col-md-4 mt-3 crew-non-saudi-fields">
                            <label class="form-label">{{ __('owner.crew.edit.residence_start_date') }} <span class="text-danger">*</span></label>
                            <input type="date" name="residence_start_date" class="form-control">
                        </div>
                        <div class="col-md-4 mt-3 crew-non-saudi-fields">
                            <label class="form-label">{{ __('owner.crew.edit.residence_end_date') }} <span class="text-danger">*</span></label>
                            <input type="date" name="residence_end_date" class="form-control">
                        </div>

                        <div class="col-md-4 mt-3">
                            <label class="form-label">{{ __('owner.employee.table.job_title') }} <span class="text-danger">*</span></label>
                            <input type="text" name="job_title" class="form-control">
                        </div>
                        <div class="col-md-4 mt-3">
                            <label class="form-label">{{ __('owner.crew.edit.phone') }} <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="col-md-4 mt-3">
                            <label class="form-label">{{ __('owner.payrolls.show.salary_type') }} <span class="text-danger">*</span></label>
                            <select name="salary_type" class="form-control wizard-salary-type" data-prefix="crew">
                                <option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>
                                <option value="salary">{{ __('owner.crew.edit.salary_option_salary') }}</option>
                                <option value="percentage">{{ __('owner.payrolls.show.percentage') }}%</option>
                            </select>
                        </div>
                        <div class="col-md-4 mt-3 crew-salary-value">
                            <label class="form-label">{{ __('owner.crew.edit.salary_amount') }} <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="salary_amount" class="form-control">
                        </div>

                        <div class="col-md-4 mt-3">
                            <label class="form-label">{{ __('owner.generated.fishing_license') }} <span class="text-danger">*</span></label>
                            <input type="text" name="fishing_license_number" class="form-control">
                        </div>
                        <div class="col-md-4 mt-3">
                            <label class="form-label">{{ __('owner.generated.fishing_license_expiry_date') }} <span class="text-danger">*</span></label>
                            <input type="date" name="fishing_license_expiry" class="form-control">
                        </div>

                        <div class="col-md-4 mt-3">
                            <label class="form-label">{{ __('owner.dalal.modal.form.bank_name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="bank_name" class="form-control">
                        </div>
                        <div class="col-md-4 mt-3">
                            <label class="form-label">{{ __('owner.dalal.modal.form.bank_account') }} <span class="text-danger">*</span></label>
                            <input type="text" name="account_number" class="form-control">
                        </div>
                        <div class="col-md-4 mt-3">
                            <label class="form-label">IBAN <span class="text-danger">*</span></label>
                            <input type="text" name="IBAN" class="form-control">
                        </div>

                        <div class="col-md-4 mt-3">
                            <label class="form-label">{{ __('owner.generated.region_name') }} <span class="text-danger">*</span></label>
                            <select name="region_id" id="crew_region_id" class="form-control wizard-region"
                                data-gov-target="#crew_governorate_id" data-port-target="#crew_port_id">
                                <option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>
                                @foreach ($regions as $region)
                                    <option value="{{ $region->id }}">{{ $region->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mt-3">
                            <label class="form-label">{{ __('owner.generated.governorate_name') }} <span class="text-danger">*</span></label>
                            <select name="governorate_id" id="crew_governorate_id" class="form-control wizard-governorate"
                                data-port-target="#crew_port_id">
                                <option value="">{{ __('owner.crew.edit.select_placeholder') }}</option>
                            </select>
                        </div>
                        <div class="col-md-4 mt-3">
                            <label class="form-label">{{ __('owner.crew.table.port') }} <span class="text-danger">*</span></label>
                            <select name="port_id" id="crew_port_id" class="form-control">
                                <option value="">-- {{ __('owner.boats.select_port') }} --</option>
                            </select>
                        </div>

                        <div class="col-md-4 mt-3">
                            <div class="form-check form-switch" style="margin-top: 35px">
                                <input type="checkbox" name="status" class="form-check-input" value="1" checked>
                                <label class="form-check-label">{{ __('owner.customers.modal.labels.status') }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <button type="submit" class="btn btn-outline-theme" data-action="add-crew">
                            <i class="fa fa-plus-circle me-1"></i> {{ __('owner.boat_wizard.add_crew') }}
                        </button>
                    </div>
                </form>

                <div class="mt-3 mb-3 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-default" data-action="skip">{{ __('owner.actions.skip') }}</button>
                    <button type="button" class="btn btn-outline-theme" data-action="next">{{ __('owner.actions.continue') }}</button>
                </div>
            </div>
            <div class="card-arrow">
                <div class="card-arrow-top-left"></div>
                <div class="card-arrow-top-right"></div>
                <div class="card-arrow-bottom-left"></div>
                <div class="card-arrow-bottom-right"></div>
            </div>
        </div>
    </div>

    {{-- ============ STEP 4: MAINTENANCE ============ --}}
    <div class="wizard-step" data-step="4" style="display:none">
        <div class="card">
            <div class="card-body pb-2">
                <p class="text-muted">{{ __('owner.boat_wizard.optional_hint') }}</p>
                <form id="wizardMaintenanceForm">
                    @csrf
                    <input type="hidden" name="boat_id" class="wizard-boat-id">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.boats.maintenance_type') }} <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select">
                                <option value="">{{ __('owner.actions.choose') }}</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.boats.date_maintenance') }} <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.boats.next_maintenance_date') }}</label>
                            <input type="date" name="next_maintenance_date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.boats.cost_expected') }} <span class="text-danger">*</span></label>
                            <input type="number" name="estimated_cost" class="form-control" placeholder="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.boats.technician') }} <span class="text-danger">*</span></label>
                            <input type="text" name="technician" class="form-control"
                                placeholder="{{ __('owner.generated.placeholder_technician_name') }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">{{ __('owner.boats.description') }}</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="mt-4 mb-3 d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-default" data-action="skip">{{ __('owner.actions.skip') }}</button>
                        <button type="submit" class="btn btn-outline-theme" data-action="next">{{ __('owner.actions.save_continue') }}</button>
                    </div>
                </form>
            </div>
            <div class="card-arrow">
                <div class="card-arrow-top-left"></div>
                <div class="card-arrow-top-right"></div>
                <div class="card-arrow-bottom-left"></div>
                <div class="card-arrow-bottom-right"></div>
            </div>
        </div>
    </div>

    {{-- ============ STEP 5: INSPECTION ============ --}}
    <div class="wizard-step" data-step="5" style="display:none">
        <div class="card">
            <div class="card-body pb-2">
                <p class="text-muted">{{ __('owner.boat_wizard.optional_hint') }}</p>
                <form id="wizardInspectionForm">
                    @csrf
                    <input type="hidden" name="boat_id" class="wizard-boat-id">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">{{ __('owner.assets.status') }} <span class="text-danger">*</span></label>
                            <select name="status" class="form-select">
                                @foreach (App\Enums\InspectionStatus::cases() as $status)
                                    <option value="{{ $status->value }}">{{ $status->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.generated.inspection_start_date') }} <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="check_date" class="form-control"
                                value="{{ now()->format('Y-m-d\TH:i') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('owner.generated.inspection_end_date') }}</label>
                            <input type="datetime-local" name="next_check" class="form-control"
                                value="{{ now()->addYear()->subDays(10)->format('Y-m-d\TH:i') }}">
                        </div>
                    </div>

                    <div class="mt-4 mb-3 d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-default" data-action="skip">{{ __('owner.actions.skip') }}</button>
                        <button type="submit" class="btn btn-success" data-action="finish">{{ __('owner.actions.finish') }}</button>
                    </div>
                </form>
            </div>
            <div class="card-arrow">
                <div class="card-arrow-top-left"></div>
                <div class="card-arrow-top-right"></div>
                <div class="card-arrow-bottom-left"></div>
                <div class="card-arrow-bottom-right"></div>
            </div>
        </div>
    </div>

    {{-- ============ DONE ============ --}}
    <div class="wizard-step" data-step="6" style="display:none">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fa fa-check-circle text-success" style="font-size: 3rem"></i>
                <h4 class="mt-3">{{ __('owner.boat_wizard.success_done') }}</h4>
                <button type="button" class="btn btn-outline-theme mt-3" data-action="restart">
                    <i class="fa fa-plus-circle me-1"></i> {{ __('owner.boat_wizard.add_another') }}
                </button>
            </div>
            <div class="card-arrow">
                <div class="card-arrow-top-left"></div>
                <div class="card-arrow-top-right"></div>
                <div class="card-arrow-bottom-left"></div>
                <div class="card-arrow-bottom-right"></div>
            </div>
        </div>
    </div>
</div>
