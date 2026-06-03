@extends('owner.layouts.master')

@section('title')
    {{ __('owner.generated.inventory_management') }}
@endsection

@section('content')
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-md-6">
            <h1 class="h3 fw-bold text-dark mb-1">{{ __('owner.generated.inventory_management') }}</h1>
            <p class="text-muted mb-0">{{ __('owner.generated.track_manage_inventory') }}</p>
        </div>

        <div class="col-md-6 text-md-end text-sm-start">
            <button class="btn btn-black btn-sm" data-bs-toggle="modal" data-bs-target="#addItemModal">
                <i class="bi bi-plus"></i> {{ __('owner.generated.add_item') }}</button>
        </div>
    </div>

    <!-- {{ __('owner.generated.item_f70e6a') }} -->
    <div class="alert alert-warning d-flex align-items-center" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
        {{ __('owner.generated.low_stock_warning') }}
    </div>

    <!-- {{ __('owner.generated.item_041a30') }} -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-primary text-white shadow-sm border-0">
                <div class="card-body d-flex justify-content-between align-items-center p-3">
                    <div>
                        <h6 class="mb-1 text-white">{{ __('owner.generated.total_items') }}</h6>
                        <h4 class="mb-0 text-white">2,590</h4>
                    </div>
                    <i class="bi bi-box-seam fs-3 text-white"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-success text-white shadow-sm border-0">
                <div class="card-body d-flex justify-content-between align-items-center p-3">
                    <div>
                        <h6 class="mb-1 text-white">{{ __('owner.generated.inventory_value') }}</h6>
                        <h4 class="mb-0 text-white">{{ __('owner.generated.amount_19625_sar') }}</h4>
                    </div>
                    <i class="bi bi-currency-dollar fs-3 text-white"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-warning text-dark shadow-sm border-0">
                <div class="card-body d-flex justify-content-between align-items-center p-3">
                    <div>
                        <h6 class="mb-1 text-white">{{ __('owner.generated.low_stock') }}</h6>
                        <h4 class="mb-0 text-white">1</h4>
                    </div>
                    <i class="bi bi-exclamation-diamond fs-3 text-white"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-danger text-white shadow-sm border-0">
                <div class="card-body d-flex justify-content-between align-items-center p-3">
                    <div>
                        <h6 class="mb-1 text-white">{{ __('owner.generated.out_of_stock') }}</h6>
                        <h4 class="mb-0 text-white">1</h4>
                    </div>
                    <i class="bi bi-x-octagon fs-3 text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- {{ __('owner.generated.item_032380') }} -->
    <ul class="nav nav-tabs mb-3" id="inventoryTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="items-tab" data-bs-toggle="tab" data-bs-target="#items" type="button"
                role="tab">
                {{ __('owner.generated.items') }}</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="movements-tab" data-bs-toggle="tab" data-bs-target="#movements" type="button"
                role="tab">
                {{ __('owner.generated.stock_movements') }}</button>
        </li>
    </ul>

    <div class="tab-content" id="inventoryTabContent">
        <!-- {{ __('owner.generated.item_46f5e9') }} -->
        <div class="tab-pane fade show active" id="items" role="tabpanel">
            <div class="row g-2 mb-3">
                <div class="col-md-8 col-sm-12">
                    <input type="text" class="form-control" placeholder="{{ __('owner.generated.item_4dc473') }}">
                </div>
                <div class="col-md-4 col-sm-12">
                    <select class="form-select">
                        <option value="">{{ __('owner.expenses.filters.all_categories') }}🔽</option>
                        <option value="equipment">{{ __('owner.generated.equipments') }}</option>
                        <option value="fuel">{{ __('owner.generated.fuel_item') }}</option>
                        <option value="ice">{{ __('owner.generated.ice') }}</option>
                        <option value="safety">{{ __('owner.generated.safety') }}</option>
                        <option value="supplies">{{ __('owner.generated.supplies_item') }}</option>
                    </select>
                </div>
            </div>
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">{{ __('owner.generated.items_in_stock') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered mb-0 text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('owner.generated.item_name') }}</th>
                                    <th>{{ __('owner.expenses.sections.categories.table.category') }}</th>
                                    <th>{{ __('owner.generated.current_quantity') }}</th>
                                    <th>{{ __('owner.generated.min_limit') }}/ {{ __('owner.generated.max_limit') }}</th>
                                    <th>{{ __('owner.expenses.print.unit_price') }}</th>
                                    <th>{{ __('owner.reports.total_value') }}</th>
                                    <th>{{ __('owner.assets.status') }}</th>
                                    <th>{{ __('owner.generated.readiness_status') }}</th>
                                    <th>{{ __('owner.assets.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        Fishing Nets - Large<br>
                                        <small class="text-muted">{{ __('owner.generated.heavy_fishing_nets') }}</small>
                                    </td>
                                    <td>{{ __('owner.generated.equipments') }}</td>
                                    <td>{{ __('owner.generated.pieces_15') }}<br><small
                                            class="text-muted">{{ __('owner.generated.late_2024') }}-01-10</small></td>
                                    <td>5 / 25</td>
                                    <td>{{ __('owner.generated.amount_450_sar') }}</td>
                                    <td>{{ __('owner.generated.amount_6750_sar') }}</td>
                                    <td><span class="badge bg-success">{{ __('owner.boats.show.crew_available') }}</span>
                                    </td>
                                    <td><span class="badge bg-info text-dark">{{ __('owner.generated.good_cond') }}</span>
                                    </td>
                                    <td>
                                        <!-- {{ __('owner.generated.item_c25f1b') }} (+) -->
                                        <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal"
                                            data-bs-target="#stockMovementModal">
                                            <i class="bi bi-plus-lg"></i>
                                        </button>

                                        <!-- {{ __('owner.generated.item_53511a') }} -->
                                        <button class="btn btn-sm btn-outline-warning me-1" data-bs-toggle="modal"
                                            data-bs-target="#stockMovementModal">
                                            <i class="bi bi-dash-lg"></i>
                                        </button>

                                        <a href="#" class="btn btn-sm btn-outline-success me-1"><i
                                                class="bi bi-pencil"></i></a>
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- {{ __('owner.generated.stock_movements') }} -->
        <div class="tab-pane fade" id="movements" role="tabpanel">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">{{ __('owner.generated.stock_movements') }}</h5>
                    <p class="text-muted mb-0">{{ __('owner.generated.track_transactions') }}</p>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered mb-0 text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('owner.sales.date') }}</th>
                                    <th>{{ __('owner.generated.the_item') }}</th>
                                    <th>{{ __('owner.assets.type') }}</th>
                                    <th>{{ __('owner.expenses.print.quantity') }}</th>
                                    <th>{{ __('owner.generated.reason') }}</th>
                                    <th>{{ __('owner.generated.the_user') }}</th>
                                    <th>{{ __('owner.generated.reference') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>2024-01-10</td>
                                    <td>Fishing Nets - Large</td>
                                    <td><span class="badge bg-success">{{ __('owner.generated.entry') }}</span></td>
                                    <td>5</td>
                                    <td>{{ __('owner.generated.purchase_order') }}</td>
                                    <td>Ahmed Al-Rashid</td>
                                    <td>PO-2024-001</td>
                                </tr>
                                <tr>
                                    <td>2024-01-12</td>
                                    <td>Marine Diesel</td>
                                    <td><span class="badge bg-danger">{{ __('owner.generated.exit_stock') }}</span></td>
                                    <td>800</td>
                                    <td>{{ __('owner.generated.trip_consumption') }}</td>
                                    <td>Mohammed Al-Zahra</td>
                                    <td>TR-2024-001</td>
                                </tr>
                                <tr>
                                    <td>2024-01-14</td>
                                    <td>Ice Blocks</td>
                                    <td><span class="badge bg-danger">{{ __('owner.generated.exit_stock') }}</span></td>
                                    <td>150</td>
                                    <td>{{ __('owner.generated.trip_usage') }}</td>
                                    <td>Fatima Al-Zahra</td>
                                    <td>TR-2024-002</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Modal: Add New Inventory Item -->
    <div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-0" id="addItemLabel">{{ __('owner.generated.add_new_item') }}</h5>
                        <small class="text-muted">{{ __('owner.generated.add_new_item_desc') }}</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="{{ __('owner.generated.btn_close_modal') }}"></button>
                </div>

                <div class="modal-body">
                    <form>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('owner.generated.item_name') }}</label>
                                <input type="text" class="form-control"
                                    placeholder="{{ __('owner.generated.item_5a8bca') }}">
                            </div>

                            <div class="col-md-6">
                                <label
                                    class="form-label">{{ __('owner.expenses.sections.categories.table.category') }}</label>
                                <select class="form-select">
                                    <option selected disabled>{{ __('owner.generated.select_category') }}</option>
                                    <option>Equipment</option>
                                    <option>Fuel</option>
                                    <option>Safety</option>
                                    <option>Ice</option>
                                    <option>Supplies</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ __('owner.generated.current_stock') }}</label>
                                <input type="number" class="form-control" value="0">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ __('owner.generated.unit') }}</label>
                                <input type="text" class="form-control"
                                    placeholder="{{ __('owner.generated.item_bc1beb') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ __('owner.generated.min_stock_level') }}</label>
                                <input type="number" class="form-control" value="0">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ __('owner.generated.item_f50028') }}</label>
                                <input type="number" class="form-control" value="0">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ __('owner.generated.item_6bf64d') }}</label>
                                <input type="number" class="form-control" value="0">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ __('owner.assets.status') }}</label>
                                <select class="form-select">
                                    <option selected>{{ __('owner.generated.item_da694c') }}</option>
                                    <option>{{ __('owner.generated.good_cond') }}</option>
                                    <option>{{ __('owner.generated.item_e433c8') }}</option>
                                    <option>{{ __('owner.generated.item_e6eea2') }}</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ __('owner.expenses.table.vendor') }}</label>
                                <input type="text" class="form-control"
                                    placeholder="{{ __('owner.generated.item_76de30') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ __('owner.generated.location') }}</label>
                                <input type="text" class="form-control"
                                    placeholder="{{ __('owner.generated.item_007819') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ __('owner.generated.serial_number') }}</label>
                                <input type="text" class="form-control" placeholder="Serial Number">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ __('owner.generated.expiry_date') }}</label>
                                <input type="date" class="form-control">
                            </div>

                            <div class="col-12">
                                <label class="form-label">{{ __('owner.generated.item_10b756') }}</label>
                                <textarea class="form-control" rows="3" placeholder="{{ __('owner.generated.item_ecc912') }}"></textarea>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('owner.payrolls.create.confirm_save_cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('owner.generated.item_165b27') }}</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal: Record Stock Movement -->
    <div class="modal fade" id="stockMovementModal" tabindex="-1" aria-labelledby="stockMovementLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="stockMovementLabel">{{ __('owner.generated.item_f01bf7') }}- <span
                            id="itemName">{{ __('owner.generated.item_name') }}</span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="{{ __('owner.generated.btn_close_modal') }}"></button>
                </div>

                <div class="modal-body">
                    <form id="stockMovementForm">
                        <div class="mb-3">
                            <label class="form-label">{{ __('owner.expenses.print.quantity') }}</label>
                            <input type="number" class="form-control"
                                placeholder="{{ __('owner.generated.item_d01e70') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('owner.generated.reason') }}</label>
                            <select class="form-select" required>
                                <option selected disabled>{{ __('owner.generated.item_7f7cba') }}</option>
                                <option>{{ __('owner.generated.item_b4d2fa') }}</option>
                                <option>{{ __('owner.boats.trip') }}</option>
                                <option>{{ __('owner.generated.item_86c84b') }}</option>
                                <option>{{ __('owner.generated.item_e3682e') }}</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('owner.generated.reference') }}</label>
                            <input type="text" class="form-control"
                                placeholder="{{ __('owner.generated.item_979a6c') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('owner.expenses.show.notes') }}</label>
                            <textarea class="form-control" rows="3" placeholder="{{ __('owner.generated.item_a95309') }}"></textarea>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('owner.payrolls.create.confirm_save_cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('owner.generated.item_cf8b06') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection
