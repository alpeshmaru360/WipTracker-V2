@extends('layouts.main')
@section('content')
<link rel="stylesheet" href="{{ asset('css/production_manager.css') }}" />
<section class="bg-image mt-4">
    <div class="mask d-flex align-items-center h-100 gradient-custom-3">
        <div class="container h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-12 col-md-9 col-lg-7 col-xl-12">
                    <div class="card" style="border-radius: 15px;">
                        <div class="card-body p-4">
                            @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                            @endif
                            <form action="{{route('ExpectedOrdersCreate')}}" enctype="multipart/form-data"
                                method="post">
                                @csrf
                                <div class="row mt-3">
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                        <label class="form-label" for="">Assembly Quotation Ref.<span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                        <input type="text" id="" class="w-100 pb-2 pt-2 assembly_quotation_ref"
                                            name="assembly_quotation_ref" required
                                            placeholder="Please Enter Assembly Quotation Reference Number" />
                                    </div>
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                        <label class="form-label" for="expected_order_date">Expected Order Date<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                        <input type="text" id="expected_order_date" class="form-control" name="expected_order_date" required placeholder="Select Expected Order Date" />
                                    </div>
                                </div>
                                <!-- <div class="row mt-3">
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                        <label class="form-label" for="expected_delivery_date">Expected Delivery Date<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                        <input type="text" id="expected_delivery_date" class="form-control" name="expected_delivery_date" required placeholder="Select Expected Delivery Date" />
                                    </div>
                                </div> -->
                                <div class="d-flex justify-content-center mt-2" id="buttonContainer">
                                    <button type="submit" class="btn btn-lg">Create</button>
                                    <button type="button" class="btn btn-lg ml-2" id="printButton">Print</button>
                                    <button type="button" class="btn btn-lg ml-2" id="downloadPDFButton">PDF</button>
                                    <button type="button" class="btn btn-lg ml-2" id="reserveButton">Reserved</button>
                                </div>
                            </form>

                            <div class="row mt-3 mx-4 project_table_section">
                                <table class="table table-hover table-border w-100 text-center" id="quotation-items">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="project_table_heading p-1">SR NO.</th>
                                            <th scope="col" class="project_table_heading p-1">ARTICLE NUMBER</th>
                                            <th scope="col" class="project_table_heading p-1">DESCRIPTION</th>
                                            <th scope="col" class="project_table_heading p-1">QTY</th>
                                            <th scope="col" class="project_table_heading p-1">PRODUCT NAME</th>
                                            <th scope="col" class="project_table_heading p-1">PRODUCT TYPE</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#expected_order_date').datepicker({
            format: 'dd/mm/yyyy',
            startDate: new Date(), // This will prevent past dates
            autoclose: true
        });
    });
    $(document).ready(function() {
        $('#expected_delivery_date').datepicker({
            format: 'dd/mm/yyyy',
            startDate: new Date(), // Prevents past dates
            autoclose: true
        });
    });
    $(".assembly_quotation_ref").on('blur', function() {
        var quotation_number = $(this).val();
        var csrf_token = "{{csrf_token()}}";
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
                                <td>${item.sr_no}</td>
                                <td>${item.full_article_number}</td>
                                <td>${item.description}</td>
                                <td>
                                    <input type="number" name="quotation_items[${index}][qty]" value="${item.qty}" class="form-control editable-qty" data-index="${index}">
                                </td>
                                <td class = "cart_model_name" name = "cart_model_name">${item.product_type}</td>
                                <td>
                                    <select class="form-control select2 product-type-dropdown" name="quotation_items[${index}][product_type]" data-index="${index}" required>
                                        ${productTypeOptions}
                                    </select>
                                </td>
                                <td class = "d-none"> 
                                    <input type="hidden" class="quotation_from_pricing_tool" data-index="${index}" value = "1">
                                </td>
                            </tr>
                        `;

                        tableBody.append(row);
                        $("form").append(hiddenFields);
                        var hiddenFields = `
                            <input type="hidden" name="quotation_items[${index}][sr_no]" value="${item.sr_no}">
                            <input type="hidden" name="quotation_items[${index}][full_article_number]" value="${item.full_article_number}">
                            <input type="hidden" name="quotation_items[${index}][description]" value="${item.description}">
                            <input type="hidden" name="quotation_items[${index}][item_id]" value="${item.item_id}">
                            <input type="hidden" name="quotation_items[${index}][qty]" value="${item.qty}" class="hidden-qty-${index}">
                            <input type="hidden" name="quotation_items[${index}][cart_model_name]" value="${item.product_type}" class="hidden-cart_model_name-${index}">
                            <input type="hidden" name="quotation_items[${index}][product_type]" value="${item.product_type}" class="hidden-product-type-${index}">
                            <input type="hidden" name="quotation_items[${index}][quotation_from_pricing_tool]" value="1" class="quotation_from_pricing_tool-${index}">
                        `;

                        // $("#quotation-items").append(hiddenFields);
                        $("form").append(hiddenFields);

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
                } else {
                    $(".project_table_section").css('display', 'block');
                    let rowIndex = 0;
                    var createRowHtml = `
                    <tr>
                        <td><input type="text" class="form-control edit_sr_no" name="quotation_items[${rowIndex}][sr_no]" placeholder="Enter SR No" data-index="${rowIndex}"></td>
                        <td><input type="text" class="form-control edit_article_number" name="quotation_items[${rowIndex}][full_article_number]" placeholder="Enter Full Article Number" data-index="${rowIndex}"></td>
                        <td><input type="text" class="form-control edit_desc" name="quotation_items[${rowIndex}][description]" placeholder="Enter Description" data-index="${rowIndex}"></td>
                        <td><input type="number" class="form-control edit_qty" name="quotation_items[${rowIndex}][qty]" placeholder="Enter Quantity" data-index="${rowIndex}"></td>
                        <td><input type="text" class="form-control edit_product_type_manual" name="quotation_items[${rowIndex}][cart_model_name]" placeholder="Enter Product Type" data-index="${rowIndex}"></td>
                        <td>
                            <select class="form-control select2 product-type-dropdown edit_product_type" name="quotation_items[${rowIndex}][product_type]" required data-index="${rowIndex}">
                               <option>Select product type</option>
                                @foreach($product_type as $product_type_val)
                                    <option value="{{$product_type_val->project_type_name}}">
                                        {{$product_type_val->project_type_name}}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td class = "d-none"> 
                            <input type="hidden" class="quotation_from_pricing_tool" data-index="0"  name="quotation_items[${rowIndex}][quotation_from_pricing_tool]">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8">
                            <button type="button" class="btn btn-primary add-new-row">Add Row</button>
                        </td>
                    </tr>
                `;
                    tableBody.append(createRowHtml);
                    var hiddenFields = `
                    <input type="hidden" name="quotation_items[${rowIndex}][sr_no]" value="" class="hidden-sr_no-${rowIndex}">
                    <input type="hidden" name="quotation_items[${rowIndex}][full_article_number]" value="" class="hidden-full_article_number-${rowIndex}">
                    <input type="hidden" name="quotation_items[${rowIndex}][description]" value="" class="hidden-description-${rowIndex}">
                    <input type="hidden" name="quotation_items[${rowIndex}][qty]" value="0" class="hidden-qty-${rowIndex}">
                    <input type="hidden" name="quotation_items[${rowIndex}][product_type]" value="" class="hidden-product-type-${rowIndex}">
                    <input type="hidden" name="quotation_items[${rowIndex}][item_id]" value="1" class="hidden-item_id-${rowIndex}">
                    <input type="hidden" name="quotation_items[${rowIndex}][quotation_from_pricing_tool]" value="0" class="quotation_from_pricing_tool-${rowIndex}">
                `;
                    $("form").append(hiddenFields);

                    $(document).on('input', '.edit_sr_no', function() {
                        var index = $(this).data('index');
                        var newValue = $(this).val();
                        $(`.hidden-sr_no-${index}`).val(newValue);
                        rowIndex
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

                    var $dropdown = $(`select[name="quotation_items[${rowIndex}][product_type]"]`);
                    $(`.hidden-product-type-${rowIndex}`).val($dropdown.val());
                    $dropdown.on("change", function() {
                        var selectedIndex = $(this).data("index");
                        var selectedValue = $(this).val();
                        $(`.hidden-product-type-${selectedIndex}`).val(selectedValue);
                    });


                    // Handle adding new rows
                    $(".add-new-row").on('click', function() {
                        rowIndex++;
                        var newRow = `
                        <tr>
                            <td><input type="text" class="form-control edit_sr_no" name="quotation_items[${rowIndex}][sr_no]" placeholder="Enter SR No" data-index="${rowIndex}"></td>
                            <td><input type="text" class="form-control edit_article_number" name="quotation_items[${rowIndex}][full_article_number]" placeholder="Enter Full Article Number" data-index="${rowIndex}"></td>
                            <td><input type="text" class="form-control edit_desc" name="quotation_items[${rowIndex}][description]" placeholder="Enter Description" data-index="${rowIndex}"></td>
                            <td><input type="number" class="form-control edit_qty" name="quotation_items[${rowIndex}][qty]" placeholder="Enter Quantity" data-index="${rowIndex}"></td>
                            <td><input type="text" class="form-control" name="quotation_items[${rowIndex}][product_type]" placeholder="Enter Product Type" data-index="${rowIndex}"></td>
                            <td>
                                <select class="form-control select2 product-type-dropdown edit_product_type trx_addons_attrib_" name="quotation_items[${rowIndex}][product_type]" required data-index="${rowIndex}">
                                   <option>Select product type</option>
                                    @foreach($product_type as $product_type_val)
                                        <option value="{{$product_type_val->project_type_name}}">
                                            {{$product_type_val->project_type_name}}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                    `;
                        tableBody.append(newRow);

                        var hiddenFields = `
                        <input type="hidden" name="quotation_items[${rowIndex}][sr_no]" value="" class="hidden-sr_no-${rowIndex}">
                        <input type="hidden" name="quotation_items[${rowIndex}][full_article_number]" value="" class="hidden-full_article_number-${rowIndex}">
                        <input type="hidden" name="quotation_items[${rowIndex}][description]" value="" class="hidden-description-${rowIndex}">
                        <input type="hidden" name="quotation_items[${rowIndex}][qty]" value="0" class="hidden-qty-${rowIndex}">
                        <input type="hidden" name="quotation_items[${rowIndex}][product_type]" value="" class="hidden-product-type-${rowIndex}">
                        <input type="hidden" name="quotation_items[${rowIndex}][item_id]" value="1" class="hidden-item_id-${rowIndex}">
                    `;

                        $("form").append(hiddenFields);
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

                        var $dropdown = $(`select[name="quotation_items[${rowIndex}][product_type]"]`);
                        $(`.hidden-product-type-${rowIndex}`).val($dropdown.val());
                        $dropdown.on("change", function() {
                            var selectedIndex = $(this).data("index");
                            var selectedValue = $(this).val();
                            $(`.hidden-product-type-${selectedIndex}`).val(selectedValue);
                        });

                    });
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
            }
        });
    });
    $("#printButton").on('click', function() {
        // Hide the button container
        $("#buttonContainer").hide();

        // Trigger the print dialog
        window.print();
    });
    $("#reserveButton").on('click', function() {
        // Implement your reserved logic here
        alert("Reserved functionality not implemented yet.");
    });
    $("#downloadPDFButton").on('click', function(e) {
        e.preventDefault();
        $("form").attr("action", "{{ route('expectedOrders.pdf') }}").submit();
    });
    $("#downloadPDFButton").on('click', function(e) {
        e.preventDefault();
        $("form").attr("action", "{{ route('expectedOrders.pdf') }}").submit();
    });
</script>
@endsection