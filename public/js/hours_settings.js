// Function to show/hide product type input based on selected process type
function hide_product_type(selectElement, isEdit = false) {
    const selectedValue = selectElement.value;

    const prefix = isEdit ? 'edit' : 'hr';
    const productTypeInput = document.getElementById(prefix + 'ProductType');
    if (!productTypeInput) return;

    const productTypeField = productTypeInput.closest('.product_type_field');
    if (!productTypeField) return;

    if (selectedValue === 'StandardProcessTimes') {
        productTypeField.style.display = 'none';
        productTypeInput.removeAttribute('required');
        productTypeInput.value = '';
    } else {
        productTypeField.style.display = 'block';
        productTypeInput.setAttribute('required', 'required');
    }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function () {
    // Initialize hide_product_type for add and edit selects if present
    const addSelect = document.getElementById('hrProcessType');
    const editSelect = document.getElementById('editProcessType');

    if (addSelect) hide_product_type(addSelect, false);
    if (editSelect) hide_product_type(editSelect, true);

    // Initialize DataTable on #process_table
    if (window.jQuery && $.fn.DataTable) {
        $('#process_table').DataTable();
        $('#process_table').removeClass('dataTable');
    }

    // Centralized change handler for process and product status dropdowns
    $(".process_status_dd, .product_status_dd").on('change', function () {
        var process_type = $(".process_status_dd").val();
        var product_type = $(".product_status_dd").val();

        // Show/hide product type wrapper based on process type
        if (process_type === 'StandardProcessTimes') {
            $('#product_type_wrapper').hide();
            $('.product_status_dd').val('');
            product_type = ''; // Clear product_type for AJAX
        } else {
            $('#product_type_wrapper').show();
        }

        // AJAX call to update process table
        $.ajax({
            url: ADMIN_HOURS_SETTINGS_URL, // Replace with actual URL or set globally
            type: 'GET',
            data: {
                process_type: process_type,
                product_type: product_type
            },
            success: function (response) {
                if ($.fn.DataTable.isDataTable('#process_table')) {
                    $('#process_table').DataTable().destroy();
                }

                $("#process_table_body").html(response.html);

                $('#process_table').DataTable({
                    // Add any DataTable config here if needed
                });

                $('#process_table').removeClass('dataTable');
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });

    // Trigger change on page load to initialize table
    $('.process_status_dd').trigger('change');
});

// Function to populate edit form modal with process data
function edit_process(button) {
    const $btn = $(button);

    const processId = $btn.data('id');
    const processType = $btn.data('process_type');
    const processCode = $btn.data('process_code');
    const productType = $btn.data('product_type');
    const processName = $btn.data('process_name');
    const processKey = $btn.data('key');
    const processHrs = $btn.data('hrs');

    $('#editProcessType').val(processType);

    if (processType === 'StandardProcessTimes') {
        $('.product_type_field').hide();
        $('#product_code_field').hide();
        $('#editProcessCode').val('');
    } else {
        $('.product_type_field').show();
        $('#product_code_field').show();
        $('#editProductType').val(productType);
        $('#editProcessCode').val(processCode);
    }

    $('#editProcessName').val(processName);
    $('#editKey').val(processKey);
    $('#editHrs').val(processHrs);

    // Replace ':id' with actual processId in the form action URL
    const actionUrl = EDIT_PROCESS_UPDATE_URL.replace(':id', processId); // Set EDIT_PROCESS_UPDATE_URL globally
    $('#editProcessForm').attr('action', actionUrl);
}

// Function to open delete modal and set form action
function openDeleteModal(processId, processKey) {
    document.getElementById('process_key').textContent = processKey;

    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = DELETE_PROCESS_URL.replace(':id', processId); // Set DELETE_PROCESS_URL globally

    $('#deleteModal').modal('show');
}
