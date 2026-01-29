@extends('layouts.main')
@section('content')


<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pikaday/css/pikaday.css">
<script src="https://cdn.jsdelivr.net/npm/pikaday/pikaday.js"></script>
<link rel="stylesheet" href="{{ asset('css/purchase_order.css') }}" />
<meta name="csrf-token" content="{{ csrf_token() }}">

<section class="edit_po_page bg-image mt-4">
    <div class="mask d-flex align-items-center h-100 gradient-custom-3">
        <div class="container h-100">
            <div class="row d-flex align-items-center h-100">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body p-4">
                            @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                            @endif
                            <form action="{{ route('updatePO', $purchaseOrder->id) }}" method="POST" enctype="multipart/form-data" id="purchaseOrderForm">
                                @csrf
                                @method('PUT')

                                <!-- Download PO -->
                                <div class="row mt-3">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label" for="">Download PO</label>
                                    </div>
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                        @if($purchaseOrder->po_pdf)
                                        <a href="{{ asset('purchase_order_pdf/' . $purchaseOrder->po_pdf) }}" class="btn btn-sm btn-primary px-2 py-2" download="{{ $purchaseOrder->po_pdf }}">Download PO PDF</a>
                                        @else
                                        <span>No PDF available</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- PO Number (Read-Only) -->
                                <div class="row mt-3">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label" for="">PO Number<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                        <input type="text" id="PO_number" class="w-100 pb-2 pt-2" name="PO_number" required placeholder="PO Number" value="{{ $purchaseOrder->po_number }}" readonly />
                                    </div>
                                </div>

                                <!-- Currency -->
                                <div class="row mt-3">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label" for="">Currency<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                        <input type="text" id="currency" class="w-100 pb-2 pt-2" name="currency" required placeholder="Please Enter currency" value="{{ $purchaseOrderTables->first()->currency ?? '' }}" readonly />
                                    </div>
                                </div>

                                <!-- Supplier (hidden by default) -->
                                <div class="row mt-3 hidden">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label" for="">Supplier<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                        <input type="text" id="supplier" class="w-100 pb-2 pt-2" name="supplier" required placeholder="Please Enter Supplier" value="{{ $purchaseOrder->supplier }}" />
                                    </div>
                                </div>

                                <!-- Artical No. -->
                                <div class="row mt-3 hidden">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label" for="">Artical No.<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                        <input type="text" id="artical_no" class="w-100 pb-2 pt-2" name="artical_no" required placeholder="Please Enter Artical No." value="{{ $purchaseOrderTables->first()->artical_no ?? '' }}" />
                                    </div>
                                </div>

                                <!-- Project Order Checkbox -->
                                <div class="row mt-3">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label" for="is_Project_Order">Project Order</label>
                                    </div>
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7 d-flex">
                                        <input class="me-2 req_for_finance_approval" type="checkbox" style="width: 20px; height: 20px; accent-color: green; clip: unset !important; position: unset !important;" id="is_Project_Order" value="1" onchange="toggleProjectFields()" {{ $purchaseOrder->is_project_order ? 'checked' : '' }} disabled />
                                    </div>
                                </div>

                                <input type="hidden" name="is_Project_Order" value="{{ $purchaseOrder->is_project_order }}">

                                <!-- Project Number -->
                                <div class="row mt-3" id="projectFields" style="display: {{ $purchaseOrder->is_project_order ? 'flex' : 'none' }};">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label" for="project_number">Project No<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                        <input type="text" id="project_number" class="w-100 pb-2 pt-2" name="project_number" placeholder="Please Enter Project Number" onblur="fetchProjectName()" value="{{ $purchaseOrder->project_no }}" />
                                    </div>
                                </div>

                                <!-- Project Name -->
                                <div class="row mt-3" id="projectNameField" style="display: {{ $purchaseOrder->is_project_order ? 'flex' : 'none' }};">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label" for="project_name">Project Name<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                        <input type="text" id="project_name" class="w-100 pb-2 pt-2" name="project_name" placeholder="Please Enter Project Name" value="{{ $purchaseOrder->project_name }}" readonly />
                                    </div>
                                </div>

                                <!-- Payment Terms -->
                                <div class="row mt-3">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label" for="">Payment Terms<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                        <input type="text" id="payment_terms" class="w-100 pb-2 pt-2" name="payment_terms" required placeholder="Please Enter Payment Terms" value="{{ $purchaseOrder->payment_terms }}" />
                                    </div>
                                </div>

                                <!-- Shipment Method -->
                                <div class="row mt-3">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label" for="">Shipment Method<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                        <input type="text" id="shipment_method" class="w-100 pb-2 pt-2" name="shipment_method" required placeholder="Please Enter Shipment Method" value="{{ $purchaseOrder->shipment_method }}" />
                                    </div>
                                </div>

                                <!-- Order Date Input -->
                                <div class="row mt-3">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label" for="order_date">Order Date<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                        <input type="text" id="order_date" class="w-100 pb-2 pt-2 form-control" name="order_date" required
                                            placeholder="Please Select Order Date"
                                            value="{{ \Carbon\Carbon::parse($purchaseOrder->order_date)->format('F j, Y') }}" />
                                    </div>
                                </div>

                                <!-- Local Supplier -->
                                <div class="row mt-3" id="localSupplierContainer" style="display: {{ $purchaseOrder->is_local_supplier ? 'flex' : 'none' }};">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label" for="">Local Supplier<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                        <input class="me-2 req_for_finance_approval" type="checkbox" style="width: 20px; height: 20px; accent-color: green; clip: unset !important; position: unset !important;" name="is_local_supplier" id="is_local_supplier" value="1" onchange="this.value = this.checked ? 1 : 0" {{ $purchaseOrder->is_local_supplier ? 'checked' : '' }} />
                                    </div>
                                </div>    

                                <!-- OA Files -->
                                <div class="row mt-3">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label">OA Files</label>
                                    </div>

                                    <!-- Upload New OA Files -->                                    
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                        <input type="file" class="form-control w-100 mb-2" name="oa_files[]" multiple 
                                            accept=".pdf, .doc, .docx, .xlsx, .csv" @if(!$purchaseOrder->oa_file || count($purchaseOrder->oa_file) == 0) @endif />
                                    </div>

                                    <!-- OA File List -->
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3"></div>
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                        <ul class="list-group">
                                            @if($purchaseOrder->oa_file && count($purchaseOrder->oa_file))
                                                @foreach($purchaseOrder->oa_file as $file)
                                                    @php
                                                        $extension = pathinfo($file, PATHINFO_EXTENSION);
                                                        $iconClass = '';

                                                        if ($extension == "pdf") {
                                                            $iconClass = "fs-22 fa fa-file-pdf";
                                                        } elseif (in_array($extension, ['doc', 'docx'])) {
                                                            $iconClass = "fs-22 fa fa-file-word";
                                                        } elseif (in_array($extension, ['xlsx', 'csv'])) {
                                                            $iconClass = "fs-22 fa fa-file-excel";
                                                        } elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                                                            $iconClass = "fs-22 fa fa-image";
                                                        } else {
                                                            $iconClass = "fs-22 fa fa-file";
                                                        }
                                                    @endphp
                                                    <div id="deleteHiddenInputs">
                                                        <li class="list-group-item d-flex justify-content-between align-items-center px-4 py-2">
                                                            <span>
                                                                <i class="file-icon {{ $extension }} {{$iconClass}} icon_color"></i>
                                                                <a href="{{ asset($file) }}" target="_blank">{{ basename($file) }}</a>
                                                            </span>
                                                            <button type="button" class="btn btn-danger btn-sm delete-btn px-3 py-1 my-1" onclick="removeOADocument(this)">
                                                                <i class="fa fa-times"></i>
                                                            </button>
                                                            <input type="checkbox" name="delete_oa_files[]" value="{{ $file }}" class="d-none">
                                                        </li>
                                                    </div>
                                                @endforeach
                                            @else
                                                <small class="ms-3">No OA files found</small>
                                            @endif
                                        </ul>
                                    </div>                                    
                                </div>

                                <!-- Invoice Files -->
                                <div class="row mt-3">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label">Invoice Files</label>
                                    </div>

                                    <!-- Upload New Invoice Files -->
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                        <input type="file" class="form-control w-100 mb-2" name="invoice_files[]" multiple>
                                    </div>

                                    <!-- Invoice File List -->
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3"></div>
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                        <ul class="list-group">
                                            @if(!empty($purchaseOrder->invoice_file))
                                                @foreach($purchaseOrder->invoice_file as $file)
                                                    @php
                                                        $extension = pathinfo($file, PATHINFO_EXTENSION);
                                                        $iconClass = match(true) {
                                                            $extension === "pdf" => "fs-22 fa fa-file-pdf",
                                                            in_array($extension, ['doc', 'docx']) => "fs-22 fa fa-file-word",
                                                            in_array($extension, ['xlsx', 'csv']) => "fs-22 fa fa-file-excel",
                                                            in_array($extension, ['jpg','jpeg','png','gif']) => "fs-22 fa fa-image",
                                                            default => "fs-22 fa fa-file"
                                                        };
                                                    @endphp

                                                    <li class="list-group-item d-flex justify-content-between align-items-center px-4 py-2">
                                                        <span>
                                                            <i class="{{ $iconClass }} icon_color"></i>
                                                            <a href="{{ asset($file) }}" target="_blank">{{ basename($file) }}</a>
                                                        </span>
                                                        <button type="button" class="btn btn-danger btn-sm delete-btn px-3 py-1 my-1"
                                                                onclick="removeInvoiceDocument(this)">
                                                            <i class="fa fa-times"></i>
                                                        </button>
                                                        <input type="checkbox" name="delete_invoice_files[]" value="{{ $file }}" class="d-none">
                                                    </li>
                                                @endforeach
                                            @else
                                                <small class="ms-3">No Invoice files found</small>
                                            @endif
                                        </ul>
                                    </div>

                                    
                                </div>

                                <!-- BOE Files -->
                                <div class="row mt-3">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label">BOE Files</label>
                                    </div>

                                    <!-- Upload New BOE Files -->
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                        <input type="file" class="form-control w-100 mb-2" name="boe_files[]" multiple>
                                    </div>

                                    <!-- BOE File List -->
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3"></div>
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                        <ul class="list-group">
                                            @if(!empty($purchaseOrder->boe_file))
                                                @foreach($purchaseOrder->boe_file as $file)
                                                    @php
                                                        $extension = pathinfo($file, PATHINFO_EXTENSION);
                                                        $iconClass = match(true) {
                                                            $extension === "pdf" => "fs-22 fa fa-file-pdf",
                                                            in_array($extension, ['doc', 'docx']) => "fs-22 fa fa-file-word",
                                                            in_array($extension, ['xlsx', 'csv']) => "fs-22 fa fa-file-excel",
                                                            in_array($extension, ['jpg','jpeg','png','gif']) => "fs-22 fa fa-image",
                                                            default => "fs-22 fa fa-file"
                                                        };
                                                    @endphp

                                                    <li class="list-group-item d-flex justify-content-between align-items-center px-4 py-2">
                                                        <span>
                                                            <i class="{{ $iconClass }} icon_color"></i>
                                                            <a href="{{ asset($file) }}" target="_blank">{{ basename($file) }}</a>
                                                        </span>
                                                        <button type="button" class="btn btn-danger btn-sm delete-btn px-3 py-1 my-1"
                                                                onclick="removeBOEDocument(this)">
                                                            <i class="fa fa-times"></i>
                                                        </button>
                                                        <input type="checkbox" name="delete_boe_files[]" value="{{ $file }}" class="d-none">
                                                    </li>
                                                @endforeach
                                            @else
                                                <small class="ms-3">No BOE files found</small>
                                            @endif
                                        </ul>
                                    </div>                                    
                                </div>

                                <script>
                                    function removeOADocument(button) {
                                        const li = button.closest('li');
                                        const checkbox = li.querySelector('input[type="checkbox"]');
                                        const clone = checkbox.cloneNode(true);
                                        clone.checked = true;
                                        document.getElementById('deleteHiddenInputs').appendChild(clone);
                                        li.remove();
                                    }

                                    function removeInvoiceDocument(button) {
                                        const li = button.closest('li');
                                        const checkbox = li.querySelector('input[type="checkbox"]');
                                        const clone = checkbox.cloneNode(true);
                                        clone.checked = true;
                                        document.getElementById('deleteHiddenInputs').appendChild(clone);
                                        li.remove();
                                    }

                                    function removeBOEDocument(button) {
                                        const li = button.closest('li');
                                        const checkbox = li.querySelector('input[type="checkbox"]');
                                        const clone = checkbox.cloneNode(true);
                                        clone.checked = true;
                                        document.getElementById('deleteHiddenInputs').appendChild(clone);
                                        li.remove();
                                    }
                                </script>

                                <!-- PDF Table Data -->
                                <div id="pdfTableContainer" class="container mt-4 table-responsive">
                                    <h4>PDF Table Data <span id="currencyDisplay">( )</span></h4>
                                    <table id="pdfTable" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="po_table_heading p-1">Position NO.</th>
                                                <th scope="col" class="po_table_heading p-1">ARTICLE NUMBER</th>
                                                <th scope="col" class="po_table_heading p-1">Vendor Item No</th>
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
                                            @foreach ($purchaseOrderTables as $tableRow)
                                            <tr>
                                                <td>{{ $tableRow->position_no }}</td>
                                                <td>{{ $tableRow->artical_no }}</td>
                                                <td>{{ $tableRow->vendor_item_no }}</td>
                                                <td>{{ $tableRow->description }}</td>
                                                <td>{{ $tableRow->quantity }}</td>
                                                <td>{{ $tableRow->unit_of_measure }}</td>
                                                <td>{{ $tableRow->vat_per }}</td>
                                                <td>{{ $tableRow->direct_unit_cost }}</td>
                                                <td>{{ $tableRow->vat_amount }}</td>
                                                <td>{{ $tableRow->amount }}</td>
                                                <td>{{ $tableRow->amount_eur }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Submit Button -->
                                <div class="d-flex justify-content-center mt-4">
                                    <button type="submit" class="btn btn-lg">Update</button>
                                </div>
                            </form>
                            <!-- Hidden inputs for currency conversion -->
                            <input type="hidden" class="usdValue" value="{{ $usdValue }}">
                            <input type="hidden" class="aedValue" value="{{ $aedValue }}">
                            <input type="hidden" class="eurValue" value="{{ $eurValue }}">
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

        // Fetch project name based on project number
        $('#project_number').on('blur', fetchProjectName);

        // Show/hide local supplier container based on shipment method
        $(document).on('input', '#shipment_method', function() {
            $('#localSupplierContainer').css('display', $(this).val().toUpperCase().startsWith('EXW') ? 'flex' : 'none');
        });

        // Handle form submission for update
        $('#purchaseOrderForm').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            // Ensure is_project_order is properly set
            const isProjectOrder = $('#is_Project_Order').is(':checked') ? 1 : 0;
            formData.set('is_project_order', isProjectOrder);

            // If not a project order, set project fields to empty string for submission
            if (!isProjectOrder) {
                formData.set('project_number', '');
                formData.set('project_name', '');
            }

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        window.location.href = "{{ route('PurchaseOrder') }}";
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', xhr.responseText);
                    alert('An error occurred while updating the form: ' + (xhr.responseJSON?.message || error));
                }
            });
        });

        // Update file input label when a file is selected
        $('.custom-file-input').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).siblings('.file-input-label').text(fileName || 'Choose file...');
        });

        // Trigger initial state for local supplier visibility
        $('#shipment_method').trigger('input');

        // Update currency display on page load
        updateCurrencyDisplay();
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
                        $('#project_number, #project_name').val('');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while fetching project information. Please try again.');
                });
        }
    }

    function toggleProjectFields() {
        const isChecked = document.getElementById('is_Project_Order').checked;
        const projectNumber = document.getElementById('project_number');
        const projectName = document.getElementById('project_name');
        const projectFields = document.getElementById('projectFields');
        const projectNameField = document.getElementById('projectNameField');

        if (isChecked) {
            projectFields.style.display = 'flex';
            projectNameField.style.display = 'flex';
            projectNumber.setAttribute('required', 'required');
            projectName.setAttribute('required', 'required');
        } else {
            projectFields.style.display = 'none';
            projectNameField.style.display = 'none';
            projectNumber.removeAttribute('required');
            projectName.removeAttribute('required');
        }
    }

    function updateCurrencyDisplay() {
        var currencyValue = $('#currency').val().trim();
        $('#currencyDisplay').text(currencyValue ? `(${currencyValue})` : `( )`);
    }

    $(document).ready(function() {
        let currencyInput = document.getElementById('currency');
        if (currencyInput) {
            let lastValue = currencyInput.value;
            setInterval(function() {
                if (currencyInput.value !== lastValue) {
                    lastValue = currencyInput.value;
                    updateCurrencyDisplay();
                }
            }, 500);
        }
    });
</script>
<!-- Updated Script with Pikaday -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var picker = new Pikaday({
            field: document.getElementById('order_date'),
            format: 'MMMM D, YYYY',
            trigger: document.getElementById('order_date'),
            defaultDate: new Date("{{ \Carbon\Carbon::parse($purchaseOrder->order_date)->format('Y-m-d') }}"),
            setDefaultDate: true,
            toString(date, format) {
                const monthNames = [
                    "January", "February", "March", "April", "May", "June",
                    "July", "August", "September", "October", "November", "December"
                ];
                const day = date.getDate();
                const month = monthNames[date.getMonth()];
                const year = date.getFullYear();
                return `${month} ${day}, ${year}`;
            },
            parse(dateString, format) {
                return new Date(dateString);
            }
        });

        document.getElementById('order_date').addEventListener('click', function() {
            picker.show();
        });
    });
</script>
@endsection