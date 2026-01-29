$(".assembly_quotation_ref").on('blur keypress', function() {
        var quotation_number = $(this).val();
        var csrf_token = "{{csrf_token()}}";
        var tableBody = $(".project_table_section tbody");
        tableBody.empty();
        $("form input[name^='quotation_items']").remove();

        $.ajax({
            url: "{{route('GetQuotationItems')}}",
            type: "POST",
            data: {
                quotation_number: quotation_number
            },
            headers: {
                'X-CSRF-TOKEN': csrf_token
            },
            success: function(response) {
                var tableBody = $(".project_table_section tbody");
                tableBody.empty();
                // When we are having data for table
                if (response.data && response.data.length > 0) {
                    $("#customer_name").val(response.data[0].customer_name || '');
                    $("#country_name").val(response.data[0].country || '');
                    response.data.forEach(function(item, index) {
                        var productTypeOptions = `
                        
                <option value="" disabled ${!item.product_type ? 'selected' : ''}>
                    Select product type
                </option>

                @foreach($product_type as $product_type_val)
                    <option value="{{$product_type_val->project_type_name}}" 
                        ${item.product_type === "{{$product_type_val->project_type_name}}" ? 'selected' : ''}>
                        {{$product_type_val->project_type_name}}
                    </option>
                @endforeach
            `;

            var row = `
                <tr>
                    <td class="p-1">${item.sr_no}</td>
                    <td class="p-1">${item.full_article_number}</td>
                    <td class="p-1">
                        <input type="text" name="quotation_items[${index}][description]" value="${item.description}" class="form-control editable-description" data-index="${index}" style="width: 300px;" required>
                    </td>
                    <td class="p-1">
                        <input type="number" name="quotation_items[${index}][qty]" value="${item.qty}" class="form-control editable-qty" data-index="${index}" min="1">
                    </td>
                    <td class="cart_model_name" name="cart_model_name">${item.product_type}</td>
                    <td class="p-1">
                        <select class="form-control select2 product-type-dropdown" name="quotation_items[${index}][product_type]" data-index="${index}" required>
                            ${productTypeOptions}
                        </select>
                    </td>
                    <td class="p-1">
                        <a href="javascript:void(0);" class="download_excel_bom" data-item_id="${item.item_id}" data-quotation_number="" data-article_number="${item.full_article_number}" title="BOM" data-item_name="${item.product_type}">
                            <i class="p-2 m-1 fa fa-file project_icon"></i>
                        </a>
                    </td>
                    <td class="p-1"> 
                        <input type="checkbox" class="download_excel_drawing-checkbox" data-index="${index}" style="width: 20px; height: 20px; accent-color: green; clip: unset !important; position: unset !important;">
                    </td>
                    <td class="p-1">
                        <input type="checkbox" class="partial-delivery-checkbox" data-index="${index}" style="width: 20px; height: 20px; accent-color: green; clip: unset !important; position: unset !important;">
                    </td>

                    <td class="p-1">
                        ${item.unit_price}
                    </td>

                    <td class="p-1">
                        ${item.total_price}
                    </td>

                    <td class="d-none"> 
                        <input type="hidden" class="quotation_from_pricing_tool" data-index="${index}" value="1">
                    </td>
                </tr>
            `;

                        tableBody.append(row);
                        var hiddenFields = `
                <input type="hidden" name="quotation_items[${index}][sr_no]" value="${item.sr_no}">
                <input type="hidden" name="quotation_items[${index}][full_article_number]" value="${item.full_article_number}">
                <input type="hidden" name="quotation_items[${index}][description]" value="${item.description}" class="hidden-description-${index}">
                <input type="hidden" name="quotation_items[${index}][item_id]" value="${item.item_id}">
                <input type="hidden" name="quotation_items[${index}][qty]" value="${item.qty}" class="hidden-qty-${index}">
                <input type="hidden" name="quotation_items[${index}][cart_model_name]" value="${item.product_type}" class="hidden-cart_model_name-${index}">
                <input type="hidden" name="quotation_items[${index}][product_type]" value="${item.product_type}" class="hidden-product-type-${index}">
                <input type="hidden" name="quotation_items[${index}][download_excel_bom]" value="3" class="hidden-download_excel_bom-checkbox-${index}">
                <input type="hidden" name="quotation_items[${index}][download_excel_drawing]" value="0" class="hidden-download_excel_drawing-checkbox-${index}">
                <input type="hidden" name="quotation_items[${index}][partial_delivery]" value="1" class="hidden-partial-delivery-${index}">

                <input type="hidden" name="quotation_items[${index}][unit_price]"  class=hidden-unit_price-${index}" value="${item.unit_price}">
                <input type="hidden" name="quotation_items[${index}][total_price]" class="hidden-total_price-${index}" value="${item.total_price}">

                <input type="hidden" name="quotation_items[${index}][quotation_from_pricing_tool]" value="1" class="quotation_from_pricing_tool-${index}">
            `;

                        $("form").append(hiddenFields);

                        // Update hidden description when editable description changes
                        $(document).on('input', '.editable-description', function() {
                            var index = $(this).data('index');
                            var newValue = $(this).val();
                            $(`.hidden-description-${index}`).val(newValue);
                        });

                        // Existing event handlers
                        $(document).on('input', '.editable-qty', function() {
                            var index = $(this).data('index');
                            var newValue = $(this).val();
                            $(`.hidden-qty-${index}`).val(newValue);
                        });

                        var $dropdown = $(`select[name="quotation_items[${index}][product_type]"]`);
                        $(`.hidden-product-type-${index}`).val($dropdown.val());

                        $dropdown.on("change", function() {
                            var selectedIndex = $(this).data("index");
                            var selectedValue = $(this).val();
                            $(`.hidden-product-type-${selectedIndex}`).val(selectedValue);
                        });

                        $(document).on('change', '.partial-delivery-checkbox', function() {
                            var index = $(this).data('index');
                            var isChecked = $(this).is(':checked');
                            $(`.hidden-partial-delivery-${index}`).val(isChecked ? 2 : 1);
                        });

                        $(document).on('change', '.download_excel_drawing-checkbox', function() {
                            var index = $(this).data('index');
                            var isChecked = $(this).is(':checked');
                            $(`.hidden-download_excel_drawing-checkbox-${index}`).val(isChecked ? 1 : 0);
                        });
                    });
                    $(".project_table_section").css('display', 'block');
                    $(".add-new-row").addClass('d-none');
                }
                // Rest of the success function remains unchanged
                else {
                    let isMandatoryRowAdded = false;
                    let rowIndex = 0;
                    $(".project_table_section").css('display', 'block');
                    if (!isMandatoryRowAdded) {
                        addRow(true, false); // Add mandatory row
                        isMandatoryRowAdded = true; // Set flag to prevent further additions
                    }
                    // Function to add a row (mandatory or optional)
                    function addRow(isMandatory = false, removeBtn = false) {
                        console.log(removeBtn);
                        var rowHtml = `
                <tr class="quotation-row" data-index="${rowIndex}">
                    <td class="p-1">
                        <div class="input-group">
                            <input type="text" class="form-control edit_sr_no" 
                            name="quotation_items[${rowIndex}][sr_no]" 
                            placeholder="Enter SR No" 
                            data-index="${rowIndex}" ${isMandatory ? 'required' : ''}>
                            
                            ${!removeBtn ? '' : '<button type="button" class="btn btn-danger remove-row" data-index="' + rowIndex + '"><i class="fa fa-trash"></i></button>'}
                        
                        </div>
                    </td>
                    <td class="p-1"><input type="text" class="form-control edit_article_number" name="quotation_items[${rowIndex}][full_article_number]" placeholder="Enter Full Article Number" data-index="${rowIndex}" ${isMandatory ? 'required' : ''}></td>

                    <td class="p-1"><input type="text" class="form-control edit_desc" name="quotation_items[${rowIndex}][description]" placeholder="Enter Description" data-index="${rowIndex}" style="width: 300px;" ${isMandatory ? 'required' : ''}></td>

                    <td class="p-1"><input type="number" class="form-control edit_qty" name="quotation_items[${rowIndex}][qty]" placeholder="Enter Quantity" data-index="${rowIndex}" ${isMandatory ? 'required' : ''}  step="1" min="1"></td>

                    <td class="p-1">
                        <select class="form-control select2 edit_product_type_manual trx_addons_attrib_" name="quotation_items[${rowIndex}][cart_model_name]" data-index="${rowIndex}" ${isMandatory ? 'required' : ''}>
                            <option value="">Select Item Type</option>
                            <option value="Atmos">Atmos</option>
                            <option value="Booster">Booster</option>
                            <option value="SCP">SCP</option>
                            <option value="Control Panel">Control Panel</option>
                            <option value="Fire-Fighting">Fire-Fighting</option>
                        </select>
                    </td>
                    <td class="p-1">
                        <select class="form-control select2 product-type-dropdown edit_product_type trx_addons_attrib_" name="quotation_items[${rowIndex}][product_type]" data-index="${rowIndex}" ${isMandatory ? 'required' : ''}>
                            <option value="">Select product type</option>
                            @foreach($product_type as $product_type_val)
                                <option value="{{$product_type_val->project_type_name}}">{{$product_type_val->project_type_name}}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="p-1">
                        <input type="checkbox" class="download_excel_bom-checkbox" data-index="${rowIndex}" style="width: 20px; height: 20px; accent-color: green; clip: unset !important; margin-top:5px !important; position: unset !important;" name="quotation_items[${rowIndex}][download_excel_bom]">
                    </td>
                    <td class="p-1">
                        <input type="checkbox" class="download_excel_drawing-checkbox" data-index="${rowIndex}" style="width: 20px; height: 20px; accent-color: green; clip: unset !important; position: unset !important; margin-top:5px !important" name="quotation_items[${rowIndex}][download_excel_drawing]">
                    </td>
                    <td class="p-1">
                        <input type="checkbox" class="partial-delivery-checkbox" data-index="${rowIndex}" style="width: 20px; height: 20px; accent-color: green; clip: unset !important; position: unset !important; margin-top:5px !important" name="quotation_items[${rowIndex}][partial_delivery]">
                    </td>
 
                    <td class="p-1">
                        <input type="number" class="form-control edit_unit_price" name="quotation_items[${rowIndex}][unit_price]" placeholder="Enter Unit Price" data-index="${rowIndex}" ${isMandatory ? 'required' : ''} min="1" step="0.01">
                    </td>

                    <td class="p-1">
                        <input type="number" class="form-control edit_total_price" name="quotation_items[${rowIndex}][total_price]" placeholder="Enter total Price" data-index="${rowIndex}" ${isMandatory ? 'required' : ''} min="1" step="0.01">
                    </td>

                    <td class="d-none"> 
                        <input type="hidden" class="quotation_from_pricing_tool" data-index="${rowIndex}" value="0" name="quotation_items[${rowIndex}][quotation_from_pricing_tool]">
                    </td>
                </tr>
            `;

                        tableBody.append(rowHtml);

                        var hiddenFields = `
                <input type="hidden" name="quotation_items[${rowIndex}][sr_no]" value="" class="hidden-sr_no-${rowIndex}" ${isMandatory ? 'required' : ''}>
                <input type="hidden" name="quotation_items[${rowIndex}][full_article_number]" value="" class="hidden-full_article_number-${rowIndex}" ${isMandatory ? 'required' : ''}>
                <input type="hidden" name="quotation_items[${rowIndex}][description]" value="" class="hidden-description-${rowIndex}" ${isMandatory ? 'required' : ''}>
                <input type="hidden" name="quotation_items[${rowIndex}][qty]" value="0" class="hidden-qty-${rowIndex}" ${isMandatory ? 'required' : ''}>
                <input type="hidden" name="quotation_items[${rowIndex}][cart_model_name]" value="" class="hidden-cart_model_name-${rowIndex}" ${isMandatory ? 'required' : ''}>
                <input type="hidden" name="quotation_items[${rowIndex}][product_type]" value="" class="hidden-product-type-${rowIndex}" ${isMandatory ? 'required' : ''}>
                <input type="hidden" name="quotation_items[${rowIndex}][download_excel_bom]" value="0" class="hidden-download_excel_bom-checkbox-${rowIndex}">
                <input type="hidden" name="quotation_items[${rowIndex}][download_excel_drawing]" value="0" class="hidden-download_excel_drawing-checkbox-${rowIndex}">
                <input type="hidden" name="quotation_items[${rowIndex}][partial_delivery]" value="1" class="hidden-partial-delivery-${rowIndex}">
                <input type="hidden" name="quotation_items[${rowIndex}][item_id]" value="1" class="hidden-item_id-${rowIndex}">
                <input type="hidden" name="quotation_items[${rowIndex}][quotation_from_pricing_tool]" value="0" class="quotation_from_pricing_tool-${rowIndex}">
            `;

                        $("form").append(hiddenFields);

                        // Event handlers for the row
                        $(document).on('input', '.edit_sr_no', function() {
                            var index = $(this).data('index');
                            var newValue = $(this).val();
                            $(`.hidden-sr_no-${index}`).val(newValue);
                        });

                        $(document).on('input', '.edit_article_number', function() {
                            var index = $(this).data('index');
                            var newValue = $(this).val();
                            $(`.hidden-full_article_number-${index}`).val(newValue);
                        });

                        $(document).on('input', '.edit_desc', function() {
                            var index = $(this).data('index');
                            var newValue = $(this).val();
                            $(`.hidden-description-${index}`).val(newValue);
                        });

                        $(document).on('input', '.edit_qty', function() {
                            var index = $(this).data('index');
                            var newValue = $(this).val();
                            $(`.hidden-qty-${index}`).val(newValue);
                        });

                        var $dropdownCart = $(`select[name="quotation_items[${rowIndex}][cart_model_name]"]`);
                        
                        $(`.hidden-cart_model_name-${rowIndex}`).val($dropdownCart.val() || '');
                            $dropdownCart.on("change", function() {
                            var selectedIndex = $(this).data("index");
                            var selectedValue = $(this).val();
                            $(`.hidden-cart_model_name-${selectedIndex}`).val(selectedValue || '');
                        });

                        var $dropdownProduct = $(`select[name="quotation_items[${rowIndex}][product_type]"]`);
                        
                        $(`.hidden-product-type-${rowIndex}`).val($dropdownProduct.val() || '');
                        $dropdownProduct.on("change", function() {
                            var selectedIndex = $(this).data("index");
                            var selectedValue = $(this).val();
                            $(`.hidden-product-type-${selectedIndex}`).val(selectedValue || '');
                        });

                        $(document).on('change', '.download_excel_bom-checkbox', function() {
                            var index = $(this).data('index');
                            var isChecked = $(this).is(':checked');
                            $(`.hidden-download_excel_bom-checkbox-${index}`).val(isChecked ? 1 : 0);
                        });

                        $(document).on('change', '.download_excel_drawing-checkbox', function() {
                            var index = $(this).data('index');
                            var isChecked = $(this).is(':checked');
                            $(`.hidden-download_excel_drawing-checkbox-${index}`).val(isChecked ? 1 : 0);
                        });

                        $(document).on('change', '.partial-delivery-checkbox', function() {
                            var index = $(this).data('index');
                            var isChecked = $(this).is(':checked');
                            $(`.hidden-partial-delivery-${index}`).val(isChecked ? 2 : 1);
                        });

                        rowIndex++;
                    }

                    if ($(".add-new-row").hasClass("d-none")) {
                        $(".add-new-row").removeClass("d-none");
                    }

                    // Add the "Add Row" button
                    if ($('.add-new-row').length === 0) {
                        tableBody.after('<div class="mt-3"><button type="button" class="btn btn-primary mb-3 add-new-row">Add Row</button></div>');
                    }

                    // Handle adding new optional rows
                    // $(".add-new-row").on('click', function() {
                    //     addRow(true, true);
                    // });
                    $(".add-new-row").off('click').on('click', function() {
                        addRow(true, true);
                    });

                    // Handle removing optional rows
                    $(document).on('click', '.remove-row', function() {
                        var index = $(this).data('index');
                        $(`tr[data-index="${index}"]`).remove();
                        $(`.hidden-sr_no-${index}, 
                            .hidden-full_article_number-${index}, 
                            .hidden-description-${index}, .hidden-qty-${index}, 
                            .hidden-cart_model_name-${index}, 
                            .hidden-product-type-${index}, 
                            .hidden-download_excel_bom-checkbox-${index}, 
                            .hidden-download_excel_drawing-checkbox-${index}, 
                            .hidden-partial-delivery-${index}, 
 
                            .hidden-total_price-${index}, 
                            .hidden-unit_price-${index}, 

                            .hidden-item_id-${index}, .quotation_from_pricing_tool-${index}`).remove();
                    });
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
            }
        });
    });

    $("form").on('submit', function(e) {
        var tableBody = $(".project_table_section tbody");
        var rowCount = tableBody.find('tr').length;

        // If no rows exist, add a mandatory row
        if (rowCount === 0) {
            e.preventDefault(); // Prevent form submission
            addRow(true, false); // Add a mandatory row
            $('.select2').select2(); // Initialize Select2 for new row
            alert("At least one row is required in the table. A mandatory row has been added. Please fill it out and submit again.");
            return false;
        }

        // Optional: Validate that all required fields in the rows are filled
        var isValid = true;
        tableBody.find('tr').each(function() {
            var row = $(this);
            var requiredInputs = row.find('input[required], select[required]');
            requiredInputs.each(function() {
                if (!$(this).val()) {
                    isValid = false;
                    $(this).addClass('is-invalid'); // Highlight empty required fields
                } else {
                    $(this).removeClass('is-invalid');
                }
            });
        });

        if (!isValid) {
            e.preventDefault();
            alert("Please fill all required fields in the table before submitting.");
            return false;
        }

        // If rows exist and are valid, allow form submission
        return true;
    });