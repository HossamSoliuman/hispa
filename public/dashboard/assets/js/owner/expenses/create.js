$(document).ready(function () {

    const generalFields = $('#generalFields');
    const governmentFields = $('#governmentFields');
    const operatingFields = $('#operatingFields');
    const maintenanceFields = $('#maintenanceFields');
    const equipmentWrapper = $('#equipmentWrapper');
    const maintenanceWrapper = $('#maintenanceWrapper');

    // تغيير نوع المصروف أو القارب
    $('#expenseType, select[name="boat_id"]').on('change', function () {
        const type = $('#expenseType').val();
        const boatId = $('select[name="boat_id"]').val();

        generalFields.toggle(type === 'general');
        governmentFields.toggle(type === 'government');
        maintenanceFields.toggle(type === 'maintenance');
        operatingFields.toggle(type === 'operating');

        if (type === 'maintenance') {
            loadMaintenanceData(boatId);
        }
    });

    // إضافة صف معدات
    $('#addEquipment').on('click', function () {
        const newRow = equipmentWrapper.find('.equipment-row:first').clone();
        newRow.find('select, input').each(function () {
            if ($(this).hasClass('quantity')) $(this).val(1);
            else $(this).val(0);
        });
        equipmentWrapper.append(newRow);
        calculateEquipmentTotal();
    });

    // حذف صف معدات
    equipmentWrapper.on('click', '.removeEquipment', function () {
        if (equipmentWrapper.find('.equipment-row').length > 1) {
            $(this).closest('.equipment-row').remove();
            calculateEquipmentTotal();
        }
    });

    // حساب الإجمالي للمعدات
    equipmentWrapper.on('input', '.quantity, .unit_price', function () {
        calculateEquipmentTotal();
    });

    function calculateEquipmentTotal() {
        let total = 0;
        equipmentWrapper.find('.equipment-row').each(function () {
            const qty = parseFloat($(this).find('.quantity').val()) || 0;
            const unit = parseFloat($(this).find('.unit_price').val()) || 0;
            const rowTotal = qty * unit;
            $(this).find('.total_price_equipment').val(rowTotal.toFixed(2));
            total += rowTotal;
        });
        $('#operatingGrandTotal').val(total.toFixed(2));
        calculateDiscount('operating');
    }

    // تغيير نوع الخصم
    $(document).on('change', '.discount_type_select, .discount_type_select_gov,.discount_type_select_operating, .discount_type_select_maintenance', function () {
        const type = $(this).val();
        if ($(this).hasClass('discount_type_select')) toggleDiscountFields(type, 'general');
        else if ($(this).hasClass('discount_type_select_gov')) toggleDiscountFields(type, 'government');
        else if ($(this).hasClass('discount_type_select_operating')) toggleDiscountFields(type, 'operating');
        else toggleDiscountFields(type, 'maintenance');
        calculateDiscountBasedOnSection(this);
    });

    $(document).on('input', '.discount_value_input, .discount_value_input_gov,.discount_value_input_operating, .discount_value_input_maintenance', function () {
        calculateDiscountBasedOnSection(this);
    });

    function toggleDiscountFields(type, section) {
        if (section === 'general') {
            $('.discount_value_div').toggle(type !== 'none');
            $('.final_price_div').toggle(type !== 'none');
        } else if (section === 'operating') {
            $('.discount_value_div_operating').toggle(type !== 'none');
            $('.final_price_div_operating').toggle(type !== 'none');
        } else if (section === 'government') {
            $('.discount_value_div_gov').toggle(type !== 'none');
            $('.final_price_div_gov').toggle(type !== 'none');
        } else {
            $('.discount_value_div_maintenance').toggle(type !== 'none');
            $('.final_price_div_maintenance').toggle(type !== 'none');
        }
    }

    function calculateDiscountBasedOnSection(element) {
        if ($(element).hasClass('discount_value_input') || $(element).hasClass('discount_type_select')) calculateDiscount('general');
        if ($(element).hasClass('discount_value_input_gov') || $(element).hasClass('discount_type_select_gov')) calculateDiscount('government');
        if ($(element).hasClass('discount_value_input_operating') || $(element).hasClass('discount_type_select_operating')) calculateDiscount('operating');
        if ($(element).hasClass('discount_value_input_maintenance') || $(element).hasClass('discount_type_select_maintenance')) calculateDiscount('maintenance');
    }

    function calculateDiscount(type) {
        let total = 0, discountType, discountValue, finalPriceInput;

        if (type === 'general') {
            total = parseFloat($('.total_price_input').val()) || 0;
            discountType = $('.discount_type_select').val();
            discountValue = parseFloat($('.discount_value_input').val()) || 0;
            finalPriceInput = $('.final_price_input');
        }
        else if (type === 'government') {
            total = parseFloat($('.total_price_input_gov').val()) || 0;
            discountType = $('.discount_type_select_gov').val();
            discountValue = parseFloat($('.discount_value_input_gov').val()) || 0;
            finalPriceInput = $('.final_price_input_gov');
        }
        else if (type === 'operating') {
            total = parseFloat($('#operatingGrandTotal').val()) || 0;
            discountType = $('.discount_type_select_operating').val();
            discountValue = parseFloat($('.discount_value_input_operating').val()) || 0;
            finalPriceInput = $('.final_price_input_operating');
        }
        else if (type === 'maintenance') {
            total = parseFloat($('#maintenanceGrandTotal').val()) || 0;
            discountType = $('.discount_type_select_maintenance').val();
            discountValue = parseFloat($('.discount_value_input_maintenance').val()) || 0;
            finalPriceInput = $('.final_price_input_maintenance');
        }

        let finalPrice = total;
        if (discountType === 'percentage') finalPrice -= (total * discountValue / 100);
        else if (discountType === 'fixed') finalPrice -= discountValue;

        finalPriceInput.val(finalPrice.toFixed(2));
    }

    function loadMaintenanceData(boatId) {
        if (!boatId || boatId === 'general') {
            $('#maintenanceWrapper').html('<div class="alert alert-warning">يرجى اختيار قارب لعرض الصيانات.</div>');
            $('#maintenanceTotalSection').hide();
            return;
        }

        $.ajax({
            url: window.routes.availableMaintenanceData,
            method: 'GET',
            data: { boat_id: boatId },
            dataType: 'json',
            success: function (response) {

                if (!response.length) {
                    $('#maintenanceWrapper').html('<div class="alert alert-warning">لا يوجد جدول صيانة لهذا القارب.</div>');
                    $('#maintenanceTotalSection').hide();
                    return;
                }

                let html = `
                    <table class="table table-bordered" id="maintenanceTable">
                        <thead>
                            <tr>
                                <th>اختيار</th>
                                <th>التاريخ</th>
                                <th>الفئة</th>
                                <th>الوصف</th>
                                <th>التكلفة المقدرة (ر.س)</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                response.forEach(item => {
                    const boatName = item.boat ? item.boat.name : '-';
                    const categoryName = item.category ? item.category.name : '-';

                    html += `
                        <tr>
                            <td>
                                <input type="radio" name="maintenance_id" class="maintenance-radio" data-id="${item.id}" data-price="${item.estimated_cost}" />
                            </td>
                            <td>${item.date}</td>
                            <td>${categoryName}</td>
                            <td>${item.description ? item.description : '-'}</td>
                            <td>${parseFloat(item.estimated_cost).toFixed(2)}</td>
                        </tr>
                    `;
                });


                html += `</tbody></table>`;
                $('#maintenanceWrapper').html(html);

                $('.maintenance-radio').on('change', function () {
                    updateMaintenanceTotal();
                });
            },
            error: function (error) {
                $('#maintenanceWrapper').html('<div class="alert alert-danger">خطأ في تحميل الصيانات</div>');
                console.log(error.responseJSON?.message || error);
            }
        });
    }

    function updateMaintenanceTotal() {
        const selected = $('.maintenance-radio:checked');
        let total = 0;
        if (selected.length > 0) total = parseFloat(selected.data('price')) || 0;

        $('#maintenanceGrandTotal').val(total.toFixed(2));
        calculateDiscount('maintenance');
        $('#maintenanceTotalSection').toggle(total > 0);
    }

    $('#expenseForm').on('submit', function (e) {
        e.preventDefault();

        $(this).find('.is-invalid').removeClass('is-invalid');
        $(this).find('.invalid-feedback').remove();

        let formData = new FormData();

        $('#expenseForm').find('input[name="_token"], input[name="date"], select[name="vendor_id"], select[name="boat_id"], select[name="payment_method_id"], select[name="status"], input[name="attachment"], textarea[name="notes"]').each(function () {
            if ($(this).attr('type') === 'file') {
                if (this.files[0]) formData.append($(this).attr('name'), this.files[0]);
            } else {
                formData.append($(this).attr('name'), $(this).val());
            }
        });

        const expenseType = $('#expenseType').val();
        formData.append('expense_type', expenseType);

        let sectionId = '';
        if (expenseType === 'general') sectionId = '#generalFields';
        else if (expenseType === 'operating') sectionId = '#operatingFields';
        else if (expenseType === 'maintenance') sectionId = '#maintenanceFields';
        else if (expenseType === 'government') sectionId = '#governmentFields';


        if (expenseType === 'operating') {
            const categorySelect = $('#operatingFields select[name="category_id"]');
            const selectedCategoryId = categorySelect.val();
            const selectedCategoryType = categorySelect.find(':selected').data('type');
            formData.append('category_id', selectedCategoryId);
            if (selectedCategoryType === 'operating-equipments') {
                $('#equipmentWrapper').find('input, select, textarea').each(function () {
                    if ($(this).attr('type') === 'file') {
                        if (this.files[0]) formData.append($(this).attr('name'), this.files[0]);
                    } else {
                        formData.append($(this).attr('name'), $(this).val());
                    }
                });
            } else {
                $('#simpleOperatingWrapper').find('input, select, textarea').each(function () {
                    formData.append($(this).attr('name'), $(this).val());
                });
            }

            $('#operatingFields').find(
                'select[name="discount_type_operating"], input[name="discount_value_operating"], input[name="final_price_operating"]'
            ).each(function () {
                formData.append($(this).attr('name'), $(this).val());
            });
        }
        else if (sectionId) {
            $(sectionId).find('input, select, textarea').each(function () {
                if ($(this).attr('type') === 'file') {
                    if (this.files[0]) formData.append($(this).attr('name'), this.files[0]);
                }
                else if ($(this).attr('type') === 'radio') {
                    if ($(this).is(':checked')) {
                        if (expenseType === 'maintenance') {
                            formData.append('selected_maintenances[]', $(this).data('id'));
                            formData.append('maintenance_price', $(this).data('price'));
                        } else {
                            formData.append($(this).attr('name'), $(this).val());
                        }
                    }
                }
                else {
                    formData.append($(this).attr('name'), $(this).val());
                }
            });
        }

        for (let [key, value] of formData.entries()) {
            console.log(key, value);
        }
        // return;
        $.ajax({
            url: window.routes.expensesStore,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                toastr.success('تم حفظ المصروف بنجاح!');
                window.location.href = window.routes.expensesIndex;
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function (key, messages) {
                        let fieldName = key.replace(/\.\d+/g, '[]');
                        let input = $('[name="' + fieldName + '"]');

                        if (input.length > 0) {
                            input.next('.invalid-feedback').remove();
                            input.addClass('is-invalid');
                            input.after('<div class="invalid-feedback">' + messages[0] + '</div>');
                        } else {
                            $('#expenseForm').prepend('<div class="alert alert-danger dynamic-alert">' + messages[0] + '</div>');
                        }
                    });
                } else {
                    console.log(xhr.responseJSON?.message || xhr);
                    toastr.error('حدث خطأ غير متوقع');
                }
            }
        });
    });

    $(document).on('input', 'input, select, textarea', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });

    $('select[name="category_id"]').on('change', function () {
        const selectedCategoryId = $(this).val();
        const selectedCategoryType = $(this).find(':selected').data('type');

        if (selectedCategoryType === 'operating-equipments') {
            $('#equipmentWrapper').show();
            $('#simpleOperatingWrapper').hide();
        } else {
            $('#equipmentWrapper').hide();
            $('#simpleOperatingWrapper').show();
        }

        $('#operatingGrandTotal').val('');
        $('.discount_type_select_operating').val('none').trigger('change');
        $('.discount_value_input_operating').val('');
        $('.final_price_input_operating').val('');

        if (selectedCategoryType === 'operating-equipments') {
            // المعدات
            $('#equipmentWrapper').find('select.equipment_name').val('');
            $('#equipmentWrapper').find('input.quantity').val('1');
            $('#equipmentWrapper').find('input.unit_price').val('0');
            $('#equipmentWrapper').find('input.total_price_equipment').val('');
        } else {
            // المصروف البسيط
            $('#simpleOperatingWrapper').find('input[name="description_operating"]').val('');
            $('#simpleOperatingWrapper').find('input[name="total_price_operating"]').val('');
        }
    });

    function calculateOperatingTotal() {
        let total = 0;

        if ($('#equipmentWrapper').is(':visible')) {
            $('#equipmentWrapper .equipment-row').each(function () {
                const qty = parseFloat($(this).find('.quantity').val()) || 0;
                const unit = parseFloat($(this).find('.unit_price').val()) || 0;
                const rowTotal = qty * unit;
                $(this).find('.total_price_equipment').val(rowTotal.toFixed(2));
                total += rowTotal;
            });
        }

        if ($('#simpleOperatingWrapper').is(':visible')) {
            total = parseFloat($('input[name="total_price_operating"]').val()) || 0;
        }

        $('#operatingGrandTotal').val(total.toFixed(2));
        calculateDiscount('operating');
    }

    equipmentWrapper.on('input', '.quantity, .unit_price', function () {
        calculateOperatingTotal();
    });

    $(document).on('input', 'input[name="total_price_operating"]', function () {
        calculateOperatingTotal();
    });


});
