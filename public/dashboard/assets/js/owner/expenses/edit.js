$(document).ready(function () {

    $(document).on("input change", "input, select, textarea", function () {
        $(this).removeClass("is-invalid");
        $(this).next(".invalid-feedback").remove();
        $("#expenseEditForm .alert.alert-danger").fadeOut(300, function () {
            $(this).remove();
        });
    });

    function updateFinalPrice() {
        let discountType = $(".discount-type-select").val();
        let discountValue = parseFloat($(".discount-value-input").val()) || 0;

        let total = 0;
        if ($("#equipmentContainer").length > 0) {
            total = calculateEquipmentTotal();
        } else if ($(".maintenance-cost-input").length > 0) {
            total = calculateMaintenanceTotal();
        } else {
            total = parseFloat($("input[name='total_price']").val()) || 0;
        }

        let final = total;
        if (discountType === "percentage") {
            final = total - (total * (discountValue / 100));
        } else if (discountType === "fixed") {
            final = total - discountValue;
        }
        if (final < 0) final = 0;

        $(".final-price-input").val(final.toFixed(2));
    }

    $(".discount-type-select").on("change", function () {
        let type = $(this).val();
        let $section = $(this).closest(".row").find(".discount-value-section, .final-price-section");

        if (type === "none") {
            $section.hide();
            $(".discount-value-input").val(0);
            $(".final-price-input").val("");
        } else {
            $section.show();
            $(".discount-label").text(type === "percentage" ? "نسبة الخصم (%)" : "مبلغ الخصم (ر.س)");
            updateFinalPrice();
        }
    });

    $(document).on("input", ".discount-value-input", updateFinalPrice);

    function calculateEquipmentTotal() {
        let grandTotal = 0;
        $("#equipmentContainer .equipment-row").each(function () {
            let qty = parseFloat($(this).find(".quantity-input").val()) || 0;
            let price = parseFloat($(this).find(".price-input").val()) || 0;
            let total = qty * price;
            $(this).find(".total-price-display").val(total.toFixed(2));
            grandTotal += total;
        });
        $("#equipmentGrandTotal").text(grandTotal.toFixed(2));
        return grandTotal;
    }

    $(document).on("input", ".quantity-input, .price-input", function () {
        calculateEquipmentTotal();
        updateFinalPrice();
    });

    $(document).on("click", ".remove-equipment-row", function () {
        if ($("#equipmentContainer .equipment-row").length > 1) {
            $(this).closest(".equipment-row").remove();
            calculateEquipmentTotal();
            updateFinalPrice();
        } else {
            alert("يجب أن يبقى صف واحد على الأقل.");
        }
    });

    function calculateMaintenanceTotal() {
        let total = 0;
        $(".maintenance-cost-input").each(function () {
            total += parseFloat($(this).val()) || 0;
        });
        $("#maintenanceGrandTotal").text(total.toFixed(2));
        return total;
    }

    $(document).on("input", ".maintenance-cost-input", function () {
        calculateMaintenanceTotal();
        updateFinalPrice();
    });

    window.resetForm = function () {
        document.getElementById("expenseEditForm").reset();
        calculateEquipmentTotal();
        calculateMaintenanceTotal();
        updateFinalPrice();
    };

    function showAlert(message) {
        $("#expenseEditForm .dynamic-alert").remove();
        $("#expenseEditForm").prepend(
            `<div class="alert alert-danger dynamic-alert">${message}</div>`
        );
        setTimeout(() => {
            $("#expenseEditForm .dynamic-alert").fadeOut("slow", function () {
                $(this).remove();
            });
        }, 3000);
    }

    // $(document).on("form:error", function (e, messages) {
    //     showAlert(messages[0]);
    // });
    calculateEquipmentTotal();
    calculateMaintenanceTotal();
    updateFinalPrice();

    $("#expenseEditForm").on("submit", function (e) {
        e.preventDefault();

        let formData = new FormData(this);
        let actionUrl = $(this).attr("action");
        for (let [key, value] of formData.entries()) {
            console.log(key, value);
        }
        $.ajax({
            url: actionUrl,
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                toastr.success('تم تحديث المصروف بنجاح!');
                window.location.href = window.routes.expensesIndex;
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    handleValidationErrors(errors);
                } else {
                    showAlert("حدث خطأ غير متوقع. حاول مرة أخرى.");
                }
            },
        });
    });

    function handleValidationErrors(errors) {
        $(".is-invalid").removeClass("is-invalid");
        $(".invalid-feedback").remove();
        $("#expenseEditForm .alert.alert-danger").remove();

        let firstError = null;

        $.each(errors, function (field, messages) {
            let fieldName = field.replace(/\.\d+/g, "[]");
            let input = $(`[name="${fieldName}"]`);

            if (input.length > 0) {
                input.addClass("is-invalid");
                input.after(`<div class="invalid-feedback">${messages[0]}</div>`);
                if (!firstError) firstError = input;
            } else {
                if (!$("#expenseEditForm .dynamic-alert").length) {
                    $("#expenseEditForm").prepend(
                        `<div class="alert alert-danger dynamic-alert">${messages[0]}</div>`
                    );
                }
            }
        });

        if (firstError) firstError.focus();
    }
});
