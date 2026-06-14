@extends('owner.layouts.master')
@section('title')
    {{ __('owner.generated.notifications') }}
@endsection
@section('css')
    <link href="{{ asset('dashboard/assets/plugins/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}"
        rel="stylesheet">
    <link href="{{ asset('dashboard/assets/plugins/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css') }}"
        rel="stylesheet">
    <link href="{{ asset('dashboard/assets/plugins/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}"
        rel="stylesheet">
    <link href="{{ asset('dashboard/assets/plugins/bootstrap-table/dist/bootstrap-table.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        #datatableDefault th,
        #datatableDefault td {
            text-align: center !important;
            vertical-align: middle;
        }

        /* {{ __('owner.generated.item_ed06b0') }} */
        .small-text th,
        .small-text td {
            font-size: 12px;
            /* {{ __('owner.generated.or') }} 13px {{ __('owner.generated.item_4cc9e8') }} */
            text-align: center !important;
            vertical-align: middle;
            font-weight: bold;

        }

        /* {{ __('owner.generated.item_562bfe') }} Select2 100% */
        .select2-container {
            width: 100% !important;
        }

        /* {{ __('owner.generated.item_57aa7e') }} */
        .select2-dropdown {
            background-color: white;
            z-index: 99999;
            /* {{ __('owner.generated.item_3bfb27') }} Bootstrap modal */
        }

        /* {{ __('owner.generated.item_f66805') }} z-index {{ __('owner.generated.item_275229') }} */
        .modal .select2-container {
            z-index: 1050;
            /* {{ __('owner.generated.item_accaef') }} Bootstrap */
        }


        label.error {
            color: red;
            font-weight: bold;
            margin-top: 5px;
            display: block;
        }
    </style>
@endsection
@section('content')
    <div class="d-flex align-items-center mb-3">
        <div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">{{ __('owner.generated.notifications') }}</a></li>
                <li class="breadcrumb-item active">{{ __('owner.generated.notifications') }}</li>
            </ul>
            <h1 class="page-header mb-0">{{ __('owner.generated.notifications_settings') }}</h1>
        </div>
        <div class="ms-auto">
            <a href="#modalCreate" data-bs-toggle="modal" class="btn btn-outline-theme"><i
                    class="fa fa-plus-circle btn-success fa-fw me-1"></i>{{ __('owner.generated.add_btn') }}</a>
        </div>
    </div>
    <!-- BEGIN #modalCreate -->
    <div class="modal fade" id="modalCreate">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('owner.generated.add_new_notification') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('owner.notifications.send') }}" id="createForm" method="post">
                    @csrf
                    <div class="modal-body">

                        <div class="row">
                            <!-- {{ __('owner.generated.item_194e48') }} -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('owner.assets.type') }}<span
                                        class="text-danger">*</span></label>
                                <select id="userType" name="userType" class="form-control" required>
                                    <option value="">{{ __('owner.generated.select_type') }}</option>
                                    <option value="user">{{ __('owner.generated.user') }}</option>

                                </select>
                                <div class="invalid-feedback">{{ __('owner.generated.please_select_type') }}</div>
                            </div>

                            <!-- {{ __('owner.generated.item_932fbe') }} -->
                            <div class="col-md-6 mb-3 role-container d-none">
                                <label class="form-label">{{ __('owner.crew.table.job_title') }}(Role) <span
                                        class="text-danger">*</span></label>
                                <select id="role" name="role" class="form-control">
                                    <option value="">{{ __('owner.generated.select_role') }}</option>
                                    <option value="captain">{{ __('owner.payrolls.show.captain') }}</option>
                                    <option value="crew">{{ __('owner.generated.crew') }}</option>
                                    <option value="dalal">{{ __('owner.dalal.performance.broker_unit') }}</option>
                                </select>
                                <div class="invalid-feedback">{{ __('owner.generated.please_select_role') }}</div>
                            </div>

                            <!-- {{ __('owner.generated.item_75b772') }} "{{ __('owner.generated.item_6d08f1') }}" {{ __('owner.generated.or') }} "{{ __('owner.generated.select_specific_people') }}" -->
                            <div class="col-md-6 mb-3 specific-users-container d-none">
                                <label class="form-label">{{ __('owner.generated.select_recipients') }}<span
                                        class="text-danger">*</span></label>
                                <select id="recipientType" name="recipientType" class="form-control">
                                    <option value="">{{ __('owner.generated.select_delivery_method') }}</option>
                                    <option value="all">{{ __('owner.generated.send_to_all') }}</option>
                                    <option value="specific">{{ __('owner.generated.select_specific_people') }}</option>
                                </select>
                                <div class="invalid-feedback">{{ __('owner.generated.please_select_delivery') }}</div>
                            </div>

                            <!-- {{ __('owner.generated.item_526c0d') }} -->
                            <div class="col-md-12 mb-3 users-container d-none">
                                <div class="form-group" id="nameSelectContainer" style="display: none;">
                                    <label for="namesSelect">{{ __('owner.generated.choose_recipients') }}<span
                                            class="text-danger">*</span></label>
                                    <select id="namesSelect" name="recipient_ids[]" class="form-control" multiple="multiple"
                                        style="width: 100%;">
                                        <!-- {{ __('owner.generated.item_c24643') }} -->
                                    </select>
                                    <div class="invalid-feedback">{{ __('owner.generated.select_at_least_one') }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">




                            {{-- عنوان الإشعار --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('owner.generated.notification_title') }}<span
                                        class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control"
                                    placeholder="{{ __('owner.generated.placeholder_title') }}">
                                @error('title')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- موضوع الإشعار --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('owner.generated.notification_subject') }}<span
                                        class="text-danger">*</span></label>
                                <textarea name="subject" class="form-control" placeholder="{{ __('owner.generated.placeholder_body_example') }}"
                                    rows="4"></textarea>
                            </div>


                            {{-- القنوات --}}
                            <div class="col-md-12 mb-3">
                                <label class="form-label">{{ __('owner.generated.channels_used') }}</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" name="channels[]" type="checkbox" value="firebase"
                                        id="channelFirebase">
                                    <label class="form-check-label"
                                        for="channelFirebase">{{ __('owner.generated.mobile_notification') }}</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" name="channels[]" type="checkbox" value="database"
                                        id="channelDatabase">
                                    <label class="form-check-label"
                                        for="channelDatabase">{{ __('owner.generated.web_notification') }}</label>
                                </div>
                                <div class="text-danger d-none" id="channelsError">
                                    {{ __('owner.generated.select_one_channel') }}</div>
                            </div>

                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">{{ __('owner.boats.close') }}</button>
                        <button type="submit" class="btn btn-outline-primary">{{ __('owner.actions.send') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>




    <div class="tab-content py-4">
        <div class="tab-pane fade show active" id="allTab">
            <!-- BEGIN #datatable -->
            <!-- BEGIN #datatable -->
            <div id="datatable" class="mb-5">
                {{--                    <div class="card"> --}}
                {{--                        <div class="card-body"> --}}
                <table id="datatableDefault" class="table table-sm table-bordered table-hover text-center small-text"
                    style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('owner.generated.sender_name') }}</th>
                            <th>{{ __('owner.generated.recipient_type') }}</th>
                            <th>{{ __('owner.generated.recipient_name') }}</th>
                            <th>{{ __('owner.generated.notif_title') }}</th>
                            <th>{{ __('owner.generated.notif_subject') }}</th>
                            <th>{{ __('owner.generated.sent_date') }}</th>

                        </tr>
                    </thead>
                    <tbody>



                    </tbody>
                </table>
            </div>
            <div class="card-arrow">
                <div class="card-arrow-top-left"></div>
                <div class="card-arrow-top-right"></div>
                <div class="card-arrow-bottom-left"></div>
                <div class="card-arrow-bottom-right"></div>
            </div>

        </div>
    </div>
    <!-- END #datatable -->


    {{--            </div> --}}
    {{--        </div> --}}
    <!-- END #datatable -->

    <div class="card-arrow">
        <div class="card-arrow-top-left"></div>
        <div class="card-arrow-top-right"></div>
        <div class="card-arrow-bottom-left"></div>
        <div class="card-arrow-bottom-right"></div>
    </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('dashboard/assets/plugins/@highlightjs/cdn-assets/highlight.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/demo/highlightjs.demo.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net/js/dataTables.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-buttons/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-responsive/js/dataTables.responsive.min.js') }}">
    </script>
    <script src="{{ asset('dashboard/assets/plugins/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}">
    </script>
    <script src="{{ asset('dashboard/assets/plugins/bootstrap-table/dist/bootstrap-table.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/demo/table-plugins.demo.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/demo/sidebar-scrollspy.demo.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/jquery.validate.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/localization/messages_ar.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $("#createForm").validate();
    </script>
    <script type="text/javascript">
        $(function() {
            // Check if the DataTable is already initialized and destroy it
            if ($.fn.DataTable.isDataTable('#datatableDefault')) {
                $('#datatableDefault').DataTable().destroy();
            }


            // Initialize the DataTable
            var table = $('#datatableDefault').DataTable({
                processing: true,
                serverSide: true,

                language: {
                    url: "{{ asset('dashboard/assets/js/ar.json') }}?v={{ time() }}"

                },

                ajax: {
                    url: "{{ route('owner.getNotificationData') }}",
                    data: function(d) {
                        // d.from_date = $('#from_date').val();
                        // d.to_date = $('#to_date').val();
                    }

                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        title: '#'
                    },
                    {
                        data: 'sender_name',
                        name: 'sender_name',
                        title: '{{ __('owner.generated.sender_name') }}'
                    },
                    {
                        data: 'recipient_type',
                        name: 'recipient_type',
                        title: '{{ __('owner.generated.item_5bbe0a') }}'
                    },
                    {
                        data: 'recipient_name',
                        name: 'recipient_name',
                        title: '{{ __('owner.generated.recipient_name') }}'
                    },
                    {
                        data: 'title',
                        name: 'title',
                        title: '{{ __('owner.generated.notif_title') }}'
                    },
                    {
                        data: 'body',
                        name: 'body',
                        title: '{{ __('owner.generated.item_9bc7a8') }}'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        title: '{{ __('owner.generated.sent_date') }}'
                    },
                ],

                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                responsive: false, scrollX: true
            });

        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userTypeSelect = document.getElementById('userType');
            const roleContainer = document.querySelector('.role-container');
            const roleSelect = document.getElementById('role');
            const recipientTypeSelect = document.getElementById('recipientType');
            const recipientTypeContainer = document.querySelector('.specific-users-container');
            const usersContainer = document.querySelector('.users-container');
            const nameSelectContainer = document.getElementById('nameSelectContainer');
            const namesSelect = $('#namesSelect'); // jQuery object for select2

            // دالة لإظهار أو إخفاء حقل اختيار المستلمين وجعله مطلوب إذا ظهر
            function toggleUsersContainer(show) {
                if (show) {
                    usersContainer.classList.remove('d-none');
                    nameSelectContainer.style.display = 'block';

                    // اجعل select مطلوب
                    namesSelect.prop('required', true);

                    // تهيئة select2 إذا لم يكن مفعلًا
                    if (!namesSelect.hasClass('select2-hidden-accessible')) {
                        namesSelect.select2({
                            placeholder: "{{ __('owner.generated.choose_recipients') }}",
                            allowClear: true,
                            width: '100%'
                        });
                    }
                } else {
                    usersContainer.classList.add('d-none');
                    nameSelectContainer.style.display = 'none';

                    // إزالة خاصية required
                    namesSelect.prop('required', false);

                    // تدمير select2 إذا كان مفعّل
                    if (namesSelect.hasClass('select2-hidden-accessible')) {
                        namesSelect.select2('destroy');
                    }

                    // إفراغ التحديدات
                    namesSelect.val(null).trigger('change');

                    // إزالة أي رسائل خطأ
                    $('.select2-selection').removeClass('is-invalid');
                    $('#namesSelect').closest('.form-group').find('.invalid-feedback').hide();
                }
            }

            // تبديل الحقول حسب نوع المستخدم
            function toggleVisibility() {
                const userType = userTypeSelect.value;

                if (userType === 'user') {
                    roleContainer.classList.remove('d-none');
                    roleSelect.setAttribute('required', 'required');
                    recipientTypeContainer.classList.remove('d-none');
                    recipientTypeSelect.setAttribute('required', 'required');
                } else if (userType === 'admin') {
                    roleContainer.classList.add('d-none');
                    roleSelect.removeAttribute('required');
                    recipientTypeContainer.classList.remove('d-none');
                    recipientTypeSelect.setAttribute('required', 'required');
                    loadNames();
                } else {
                    roleContainer.classList.add('d-none');
                    roleSelect.removeAttribute('required');
                    recipientTypeContainer.classList.add('d-none');
                    recipientTypeSelect.removeAttribute('required');
                    toggleUsersContainer(false);
                }
            }

            // تحميل الأسماء حسب النوع والدور وطريقة الإرسال
            function loadNames() {
                const userType = userTypeSelect.value;
                const role = roleSelect.value;
                const recipientType = recipientTypeSelect.value;

                if (recipientType === 'specific') {
                    let url = `/owner/notifications/fetch-users?type=${userType}`;
                    if (userType === 'user' && role) {
                        url += `&role=${role}`;
                    }

                    fetch(url)
                        .then(res => res.json())
                        .then(data => {
                            namesSelect.empty();
                            data.forEach(user => {
                                const option = new Option(user.name, user.id, false, false);
                                namesSelect.append(option);
                            });
                            toggleUsersContainer(true);
                        })
                        .catch(() => {
                            // في حالة الخطأ، إخفاء الحقل
                            toggleUsersContainer(false);
                        });
                } else {
                    toggleUsersContainer(false);
                }
            }

            // التحقق من صحة الحقول عند الإرسال
            document.querySelector('form').addEventListener('submit', function(e) {
                let valid = true;

                // تحقق من نوع المستخدم
                if (!userTypeSelect.value) {
                    valid = false;
                    userTypeSelect.classList.add('is-invalid');
                } else {
                    userTypeSelect.classList.remove('is-invalid');
                }

                // تحقق من الدور إذا كان ظاهر
                if (!roleContainer.classList.contains('d-none') && !roleSelect.value) {
                    valid = false;
                    roleSelect.classList.add('is-invalid');
                } else {
                    roleSelect.classList.remove('is-invalid');
                }

                // تحقق من طريقة الإرسال إذا كان ظاهر
                if (!recipientTypeContainer.classList.contains('d-none') && !recipientTypeSelect.value) {
                    valid = false;
                    recipientTypeSelect.classList.add('is-invalid');
                } else {
                    recipientTypeSelect.classList.remove('is-invalid');
                }

                // تحقق من اختيار المستلمين فقط إذا كان الحقل ظاهر
                if (!usersContainer.classList.contains('d-none') && nameSelectContainer.style.display !==
                    'none') {
                    const selected = namesSelect.val();
                    if (!selected || selected.length === 0) {
                        valid = false;
                        $('.select2-selection').addClass('is-invalid');
                        $('#namesSelect').closest('.form-group').find('.invalid-feedback').show();
                    } else {
                        $('.select2-selection').removeClass('is-invalid');
                        $('#namesSelect').closest('.form-group').find('.invalid-feedback').hide();
                    }
                }

                if (!valid) {
                    e.preventDefault();
                    alert('{{ __('owner.generated.item_9a7556') }}');
                }
            });

            // استدعاء التبديل والتحميل عند تغيير القيم
            userTypeSelect.addEventListener('change', () => {
                toggleVisibility();
                loadNames();
            });

            roleSelect.addEventListener('change', loadNames);
            recipientTypeSelect.addEventListener('change', loadNames);

            // تهيئة أولية عند تحميل الصفحة
            toggleVisibility();
            loadNames();
        });

        document.getElementById('createForm').addEventListener('submit', function(e) {
            const checkboxes = document.querySelectorAll('input[name="channels[]"]');
            const isChecked = Array.from(checkboxes).some(cb => cb.checked);

            if (!isChecked) {
                e.preventDefault();
                document.getElementById('channelsError').classList.remove('d-none');
            } else {
                document.getElementById('channelsError').classList.add('d-none');
            }
        });
    </script>
@endsection
