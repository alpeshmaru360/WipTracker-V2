// $(document).ready(() => {
//     // Toggle partial shipment container visibility
//     $("#is_partial_shipment").on("change", function () {
//         if ($(this).is(":checked")) {
//             $("#partial_shipment_container").show();
//             // Add the first input field
//             if ($(".partial_quantity").length === 0) {
//                 addPartialQuantityField();
//             }
//         } else {
//             $("#partial_shipment_container").hide();
//             // Clear all partial quantity fields
//             $("#partial_shipment_container .input-group").not(":first").remove();
//             $(".partial_quantity").val("");
//             $("#error_message").hide();
//         }
//     });

//     // Add more partial quantity fields
//     $("#add_partial_quantity").on("click", () => {
//         addPartialQuantityField();
//     });

//     // Function to add a new partial quantity field
//     function addPartialQuantityField() {
//         const newField = `
//                 <div class="input-group mb-3">
//                     <input type="text" class="form-control partial_quantity" name="partial_quantity[]" placeholder="Enter positive quantity" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
//                     <button type="button" class="btn btn-outline-danger remove_partial_quantity">-</button>
//                 </div>
//             `;
//         $("#partial_shipment_container").append(newField);
//     }

//     // Remove partial quantity field
//     $(document).on("click", ".remove_partial_quantity", function () {
//         $(this).closest(".input-group").remove();
//     });

//     // Handle form submission
//     // $("#combinedForm").on("submit", function (e) {
//     //     e.preventDefault(); // Always prevent default form submission

//     //     // Check if partial shipment is checked
//     //     if ($("#is_partial_shipment").is(":checked")) {
//     //         // Get the original quantity from the table
//     //         const originalQuantity = Number.parseInt($("#original_quantity").val());

//     //         // Get all partial quantities
//     //         const partialQuantities = [];
//     //         $(".partial_quantity").each(function () {
//     //             const qty = $(this).val() ? Number.parseInt($(this).val()) : 0;
//     //             if (qty > 0) {
//     //                 partialQuantities.push(qty);
//     //             }
//     //         });

//     //         // Validate: sum of partial quantities must not exceed original quantity
//     //         const sum = partialQuantities.reduce((a, b) => a + b, 0);

//     //         if (sum > originalQuantity) {
//     //             $("#error_message")
//     //                 .text(`Total of partial quantities (${sum}) cannot exceed the original quantity (${originalQuantity}).`)
//     //                 .show();
//     //             return false;
//     //         }

//     //         if (partialQuantities.length === 0) {
//     //             $("#error_message").text("Please enter at least one partial quantity.").show();
//     //             return false;
//     //         }

//     //         // If validation passes, hide error message
//     //         $("#error_message").hide();
//     //     }

//     //     // Proceed with form submission regardless of partial shipment status
//     //     const formData = $(this).serialize();

//     //     $.ajax({
//     //         url: "/update-purchase-order",
//     //         method: "POST",
//     //         data: formData,
//     //         success: (response) => {
//     //             $("#combinedModal").modal("hide");
//     //             alert(response.success);
//     //             location.reload();
//     //         },
//     //         error: (error) => {
//     //             alert(
//     //                 "Error updating purchase order: " +
//     //                 (error.responseJSON ? error.responseJSON.message || error.responseJSON.error : "Unknown error")
//     //             );
//     //         },
//     //     });
//     // });

//     // When the modal is shown, check if partial shipment is checked and show/hide container accordingly
//     $("#combinedModal").on("shown.bs.modal", () => {
//         if ($("#is_partial_shipment").is(":checked")) {
//             $("#partial_shipment_container").show();
//         } else {
//             $("#partial_shipment_container").hide();
//         }
//     });
// });






$(document).ready(() => {
    // Toggle partial shipment container visibility
    $("#is_partial_shipment").on("change", function () {
        if ($(this).is(":checked")) {
            $("#partial_shipment_container").show();
            if ($(".partial_quantity").length === 0) {
                addPartialQuantityField();
            }
        } else {
            $("#partial_shipment_container").hide();
            $("#partial_shipment_container .input-group").not(":first").remove();
            $(".partial_quantity").val("");
            $("#error_message").hide();
        }
    });

    // Add more partial quantity fields
    $("#add_partial_quantity").on("click", () => {
        addPartialQuantityField();
    });

    // Function to add a new partial quantity field
    function addPartialQuantityField() {
        const newField = `
            <div class="input-group mb-3">
                <input type="text" class="form-control partial_quantity" name="partial_quantity[]" placeholder="Enter positive quantity" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                <button type="button" class="btn btn-outline-danger remove_partial_quantity">-</button>
            </div>
        `;
        $("#partial_shipment_container").append(newField);
        validatePartialQuantities();
    }

    // Remove partial quantity field
    $(document).on("click", ".remove_partial_quantity", function () {
        $(this).closest(".input-group").remove();
        validatePartialQuantities();
    });

    // Validate partial quantities on input change
    $(document).on("input", ".partial_quantity", function () {
        validatePartialQuantities();
    });

    // Function to validate partial quantities
    function validatePartialQuantities() {
        const originalQuantity = Number.parseInt($("#original_quantity").val());
        const existingQuantities = [];
        $("#existing_quantities_list .form-control").each(function () {
            const qty = $(this).text() ? Number.parseInt($(this).text()) : 0;
            if (qty > 0) {
                existingQuantities.push(qty);
            }
        });
        const existingSum = existingQuantities.reduce((a, b) => a + b, 0);
        const remainingQuantity = originalQuantity - existingSum;

        const newQuantities = [];
        $(".partial_quantity").each(function () {
            const qty = $(this).val() ? Number.parseInt($(this).val()) : 0;
            if (qty > 0) {
                newQuantities.push(qty);
            }
        });
        const newSum = newQuantities.reduce((a, b) => a + b, 0);

        if (newSum > remainingQuantity) {
            $("#error_message")
                .text(`Total of new partial quantities (${newSum}) cannot exceed the remaining quantity (${remainingQuantity}).`)
                .show();
            $("#combinedForm button[type='submit']").prop("disabled", true);
        } else if (newQuantities.length === 0 && $("#is_partial_shipment").is(":checked")) {
            $("#error_message").text("Please enter at least one partial quantity.").show();
            $("#combinedForm button[type='submit']").prop("disabled", true);
        } else {
            $("#error_message").hide();
            $("#combinedForm button[type='submit']").prop("disabled", false);
        }
    }

    // When the modal is shown, check if partial shipment is checked and show/hide container accordingly
    $("#combinedModal").on("shown.bs.modal", () => {
        if ($("#is_partial_shipment").is(":checked")) {
            $("#partial_shipment_container").show();
        } else {
            $("#partial_shipment_container").hide();
        }
        validatePartialQuantities();
    });
});