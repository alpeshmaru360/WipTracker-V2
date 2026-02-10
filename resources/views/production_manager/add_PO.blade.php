@extends('layouts.main')
@section('content')
<link rel="stylesheet" href="{{ asset('css/purchase_order.css') }}" />
<meta name="csrf-token" content="{{ csrf_token() }}">

<section class="add_po_page bg-image mt-4">
    <div class="mask d-flex align-items-center h-100 gradient-custom-3">
        <div class="container h-100">
            <div class="row d-flex align-items-center h-100">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body p-4">
                            @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                            @endif
                            <form action="{{ route('purchase_order.store') }}" method="POST" enctype="multipart/form-data" id="purchaseOrderForm">
                                @csrf

                                <div class="row mt-3">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label" for="">Upload PO<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                        <input type="file" id="PO_pdf" class="w-100 pb-2 pt-2" name="PO_pdf" accept=".pdf" required onchange="extractPONumber(this)" />
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label" for="">PO Number<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                        <input type="text" id="PO_number" class="w-100 pb-2 pt-2" name="PO_number" required placeholder="Please Enter PO number" />
                                    </div>
                                </div>

                                <div class="row mt-3 hidden">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label" for="">Supplier<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                        <input type="text" id="supplier" class="w-100 pb-2 pt-2" name="supplier" required placeholder="Please Enter Supplier" />
                                    </div>
                                </div>

                                <div class="row mt-3 hidden">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label" for="">Artical No.<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                        <input type="text" id="artical_no" class="w-100 pb-2 pt-2" name="artical_no" required placeholder="Please Enter Artical No." />
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label" for="is_Project_Order">Project Order</label>
                                    </div>
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7 d-flex">
                                        <input class="me-2 req_for_finance_approval" type="checkbox"
                                            name="is_Project_Order" id="is_Project_Order" value="1"
                                            onchange="toggleProjectFields()"
                                            @if(isset($isFromStock) && $isFromStock) disabled @elseif(isset($product)) checked disabled @endif />
                                    </div>
                                </div>

                                <div class="row mt-3" id="projectFields" style="display: @if(isset($product)) flex @else none @endif;">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label" for="project_number">Project No<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                        <input type="text" id="project_number" class="w-100 pb-2 pt-2" name="project_number"
                                            placeholder="Please Enter Project Number" onblur="fetchProjectName()"
                                            value="{{ isset($product) ? $product->projects['project_no'] : '' }}"
                                            @if(isset($product)) readonly @endif />
                                    </div>
                                </div>

                                <div class="row mt-3" id="projectNameField" style="display: @if(isset($product)) flex @else none @endif;">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label" for="project_name">Project Name<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                        <input type="text" id="project_name" class="w-100 pb-2 pt-2" name="project_name"
                                            placeholder="Please Enter Project Name"
                                            value="{{ isset($product) ? $product->projects['project_name'] : '' }}"
                                            @if(isset($product)) readonly @endif />
                                    </div>
                                </div>

                                @if(isset($product))
                                <div class="row mt-3">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label" for="product_article_no">Product Article No.<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                        <input type="text" id="product_article_no" class="w-100 pb-2 pt-2" name="product_article_no"
                                            value="{{ $product->full_article_number }}" readonly />
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label" for="product_desc">Product Description<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                        <input type="text" id="product_desc" class="w-100 pb-2 pt-2" name="product_desc"
                                            value="{{ $product->description }}" readonly />
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label" for="product_qty">Product Quantity<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                        <input type="text" id="product_qty" class="w-100 pb-2 pt-2" name="product_qty"
                                            value="{{ $product->qty }}" readonly />
                                    </div>
                                </div>
                                @endif

                                <div class="row mt-3">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label" for="">Approval</label>
                                    </div>
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7 d-flex">
                                        <div class="text-center mr-3">
                                            <input class="me-2 req_for_finance_approval" type="checkbox"
                                                name="is_production_engineer_approved" id="is_production_engineer_approved" value="1" disabled />
                                            <span for="is_production_engineer_approved" class="d-block">Production Engineer</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label" for="">Payment Terms<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                        <input type="text" id="payment_terms" class="w-100 pb-2 pt-2" name="payment_terms" required placeholder="Please Enter Payment Terms" />
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label" for="">Shipment Method<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-sm-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                        <input type="text" id="shipment_method" class="w-100 pb-2 pt-2" name="shipment_method" required placeholder="Please Enter Shipment Method" />
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label" for="">Order Date<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                        <input type="text" id="order_date" class="w-100 pb-2 pt-2" name="order_date" required placeholder="Please Enter Order Date" />
                                    </div>
                                </div>

                                <div class="row mt-3" id="localSupplierContainer" style="display: none;">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label" for="">Local Supplier<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                        <input class="me-2 req_for_finance_approval" type="checkbox"  
                                        name="is_local_supplier" id="is_local_supplier" value="1"
                                            onchange="this.value = this.checked ? 1 : 0" />
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label" for="">Currency<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                        <input type="text" id="currency" class="w-100 pb-2 pt-2" name="currency" required placeholder="Please Enter currency" />
                                    </div>
                                </div>

                                <div id="pdfTableContainer" class="container mt-4 table-responsive">
                                    <h4>PDF Table Data <span id="currencyDisplay">( )</span></h4>
                                    <table id="pdfTable" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="po_table_heading p-1">Position NO.</th>
                                                <th scope="col" class="po_table_heading p-1">ARTICLE NUMBER</th>
                                                <th scope="col" class="po_table_heading p-1 hidden-column">Vendor Item No</th>
                                                <th scope="col" class="po_table_heading p-1">Description</th>
                                                <th scope="col" class="po_table_heading p-1">QTY</th>
                                                <th scope="col" class="po_table_heading p-1">Unit of Measure</th>
                                                <th scope="col" class="po_table_heading p-1">VAT %</th>
                                                <th scope="col" class="po_table_heading p-1">Direct Unit Cost</th>
                                                <th scope="col" class="po_table_heading p-1">VAT Amount</th>
                                                <th scope="col" class="po_table_heading p-1">Amount</th>
                                                <th scope="col" class="po_table_heading p-1">Amount EUR</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Table rows will be dynamically inserted here -->
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-center mt-4">
                                    <button type="submit" class="btn btn-lg">Create</button>
                                </div>
                            </form>
                            <input type="hidden" class="usdValue" value="{{$usdValue}}">
                            <input type="hidden" class="aedValue" value="{{$aedValue}}">
                            <input type="hidden" class="eurValue" value="{{$eurValue}}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Include jQuery CDN -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Include pdf.js library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.9.359/pdf.min.js"></script>
<script src="{{ asset('js/purchase_order.js') }}"></script>

<script>
    $(document).ready(function() {
        // Set up CSRF token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Display currency conversion values
        var usd_euro = $(".usdValue").val();
        var aed_euro = $(".aedValue").val();
        var euro_euro = $(".eurValue").val();
        // alert("USD to Euro: " + usd_euro + "\nAED to Euro: " + aed_euro + "\nEuro to Euro: " + euro_euro);

        // Fetch project name based on project number
        $('#project_number').on('blur', fetchProjectName);

        // Show/hide local supplier container based on shipment method
        $(document).on('input', '#shipment_method', function() {
            $('#localSupplierContainer').css('display', $(this).val().toUpperCase().startsWith('EXW') ? 'flex' : 'none');
        });

        // Handle PDF upload
        $(document).on('change', '#pdfInput', function(event) {
            const file = event.target.files[0];
            if (file) {
                const formData = new FormData();
                formData.append('pdf', file);

                // Display status
                $('#status').text('Uploading...');

                // Perform the AJAX request
                fetch('/upload', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.message) {
                            $('#status').text(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Upload error:', error);
                        $('#status').text('Upload failed.');
                    });
            }
        });

        // Handle form submission
        $('#purchaseOrderForm').on('submit', function(e) {
            e.preventDefault();

            const rows = $('#pdfTable tbody tr');
            const tableData = [];

            rows.each(function(index) {
                const cells = $(this).find('td');

                if (cells.length === 11) {
                    const realAmount = parseFloat(cells.eq(10).text().trim()) || 0; // Real Amount (EUR)
                    const amountEUR = parseFloat(cells.eq(9).text().trim()) || 0; // Converted Amount

                    const rowData = {
                        position_no: cells.eq(0).text().trim() || "",
                        artical_no: cells.eq(1).text().trim() || "",
                        vendor_item_no: cells.eq(2).text().trim() || "",
                        description: cells.eq(3).text().trim() || "",
                        quantity: cells.eq(4).text().trim() || "",
                        unit_of_measure: cells.eq(5).text().trim() || "",
                        vat_per: cells.eq(6).text().trim() || "",
                        direct_unit_cost: cells.eq(7).text().trim() || "",
                        vat_amount: cells.eq(8).text().trim() || "",
                        amount: amountEUR.toFixed(2), // Real Amount (from column 9)
                        amount_eur: realAmount.toFixed(2), // Converted Amount (from column 10)
                    };
                    tableData.push(rowData);
                }
            });

            const formData = new FormData(this);
            formData.append('table_data', JSON.stringify(tableData));

            fetch($(this).attr('action'), {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        // If the backend returns an error (e.g., PO number exists), show an alert
                        alert(data.error);
                    } else if (data.success) {
                        // If successful, show success message and redirect
                        alert('Purchase order created successfully!');
                        window.location.href = "{{ route('PurchaseOrder') }}";
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while submitting the form. Please try again.');
                });
        });
    });

    function fetchProjectName() {
        const projectNumber = $('#project_number').val();
        if (projectNumber) {
            fetch(`/api/get-project-name/${projectNumber}`)
                .then(response => response.json())
                .then(data => {
                    if (data.project_name) {
                        $('#project_name').val(data.project_name);
                    } else {
                        alert('No project number found in records. Please enter a valid project number.');
                        $('#project_number, #project_name').val(''); // Clear input fields
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while fetching project information. Please try again.');
                });
        }
    }

    function toggleProjectFields() {
        const checkbox = document.getElementById('is_Project_Order');
        const isChecked = checkbox.checked;
        const isDisabled = checkbox.disabled;
        const projectFields = document.getElementById('projectFields');
        const projectNameField = document.getElementById('projectNameField');

        // If checkbox is disabled and checked (from BOM), show fields
        if (isDisabled && isChecked) {
            projectFields.style.display = 'flex';
            projectNameField.style.display = 'flex';
        } else if (!isDisabled) {
            projectFields.style.display = isChecked ? 'flex' : 'none';
            projectNameField.style.display = isChecked ? 'flex' : 'none';
        } else {
            projectFields.style.display = 'none';
            projectNameField.style.display = 'none';
        }
    }

    // Call toggleProjectFields on page load to set initial state
    document.addEventListener('DOMContentLoaded', function() {
        toggleProjectFields();
    });
</script>
<script>
    $(document).ready(function() {
        function updateCurrencyDisplay() {
            var currencyValue = $('#currency').val().trim();
            $('#currencyDisplay').text(currencyValue ? `(${currencyValue})` : `( )`);
        }

        // Function to observe changes in the input field
        function observeCurrencyField() {
            let currencyInput = document.getElementById('currency');

            if (currencyInput) {
                let lastValue = currencyInput.value;

                // Check for changes every 500ms (useful for auto-fill detection)
                setInterval(function() {
                    if (currencyInput.value !== lastValue) {
                        lastValue = currencyInput.value;
                        updateCurrencyDisplay(); // Update display immediately
                    }
                }, 500);
            }
        }

        // Start observing the field when the page is ready
        observeCurrencyField();
    });
</script>

@endsection
