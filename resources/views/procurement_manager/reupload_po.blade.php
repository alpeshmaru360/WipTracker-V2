@extends('layouts.main')
@section('content')
<link rel="stylesheet" href="{{ asset('css/purchase_order.css') }}" />
<meta name="csrf-token" content="{{ csrf_token() }}">

<section class="reupload_po_page bg-image mt-4">
    <div class="mask d-flex align-items-center h-100 gradient-custom-3">
        <div class="container h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-12 col-md-9 col-lg-7 col-xl-12">
                    <div class="card">
                        <div class="card-body p-4">
                            @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                            @endif
                            <form action="{{ route('procurement_manager.reupload_po.store', $purchaseOrder->id) }}" method="POST" enctype="multipart/form-data" id="purchaseOrderForm">
                                @csrf
                                @method('POST')

                                <div class="row mt-3">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label" for="">Upload PO<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                        <input type="file" id="PO_pdf" class="w-100 pb-2 pt-2" name="PO_pdf" accept=".pdf" onchange="extractPONumber(this)" />
                                        <small>Current PO: <a href="{{ asset('purchase_order_pdf/' . $purchaseOrder->po_pdf) }}" target="_blank">{{ $purchaseOrder->po_pdf }}</a></small>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label" for="">PO Number<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                        <input type="text" id="PO_number" class="w-100 pb-2 pt-2" name="PO_number" required value="{{ $purchaseOrder->po_number }}" readonly />
                                    </div>
                                </div>

                                <div class="row mt-3 hidden">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label" for="">Supplier<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                        <input type="text" id="supplier" class="w-100 pb-2 pt-2" name="supplier" required value="{{ $purchaseOrder->supplier }}" />
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
                                        <input class="me-2 req_for_finance_approval" type="checkbox" name="is_Project_Order" id="is_Project_Order" value="1" {{ $purchaseOrder->is_project_order ? 'checked' : '' }} onchange="toggleProjectFields()" />
                                    </div>
                                </div>

                                <div class="row mt-3" id="projectFields" style="display: {{ $purchaseOrder->is_project_order ? 'flex' : 'none' }};">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label" for="project_number">Project No<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                        <input type="text" id="project_number" class="w-100 pb-2 pt-2" name="project_number" value="{{ $purchaseOrder->project_no }}" placeholder="Please Enter Project Number" onblur="fetchProjectName()" />
                                    </div>
                                </div>

                                <div class="row mt-3" id="projectNameField" style="display: {{ $purchaseOrder->is_project_order ? 'flex' : 'none' }};">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label" for="project_name">Project Name<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                        <input type="text" id="project_name" class="w-100 pb-2 pt-2" name="project_name" value="{{ $purchaseOrder->project_name }}" placeholder="Please Enter Project Name" />
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label" for="">Request Approvals<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7 d-flex">
                                        <div class="text-center mr-3">
                                            <input class="me-2 req_for_finance_approval" type="checkbox" name="is_production_manager_approved" id="is_production_manager_approved" value="1" {{ $purchaseOrder->is_production_manager_approved == 2 ? 'checked' : '' }} disabled />
                                            <span for="is_production_manager_approved" style="display: block;">Production Manager</span>
                                        </div>
                                        <div class="text-center mr-3">
                                            <input class="me-2 req_for_finance_approval" type="checkbox" name="is_production_engineer_approved" id="is_production_engineer_approved" value="1" {{ $purchaseOrder->is_production_engineer_approved == 2 ? 'checked' : '' }} disabled />
                                            <span for="is_production_engineer_approved" style="display: block;">Production Engineer</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label" for="">Payment Terms<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                        <input type="text" id="payment_terms" class="w-100 pb-2 pt-2" name="payment_terms" required value="{{ $purchaseOrder->payment_terms }}" placeholder="Please Enter Payment Terms" />
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label" for="">Shipment Method<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                        <input type="text" id="shipment_method" class="w-100 pb-2 pt-2" name="shipment_method" required value="{{ $purchaseOrder->shipment_method }}" placeholder="Please Enter Shipment Method" />
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label" for="">Order Date<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                        <input type="text" id="order_date" class="w-100 pb-2 pt-2" name="order_date" required value="{{ $purchaseOrder->order_date ? \Carbon\Carbon::parse($purchaseOrder->order_date)->format('F d, Y') : '' }}" placeholder="Please Enter Order Date" />
                                    </div>
                                </div>

                                <div class="row mt-3" id="localSupplierContainer" style="display: {{ strtoupper($purchaseOrder->shipment_method ?? '') == 'EXW' ? 'flex' : 'none' }};">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label" for="">Local Supplier<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                        <input class="me-2 req_for_finance_approval" type="checkbox" name="is_local_supplier" id="is_local_supplier" value="1" {{ $purchaseOrder->is_local_supplier ? 'checked' : '' }} onchange="this.value = this.checked ? 1 : 0" />
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                        <label class="form-label" for="">Currency<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                        <input type="text" id="currency" class="w-100 pb-2 pt-2" name="currency" required value="{{ $purchaseOrder->purchaseOrderTables->first()->currency ?? '' }}" placeholder="Please Enter currency" />
                                    </div>
                                </div>

                                <div id="pdfTableContainer" class="container mt-4">
                                    <h4>PDF Table Data <span id="currencyDisplay">({{ $purchaseOrder->purchaseOrderTables->first()->currency ?? '' }})</span></h4>
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
                                            @foreach($purchaseOrder->purchaseOrderTables as $row)
                                            <tr>
                                                <td>{{ $row->position_no }}</td>
                                                <td>{{ $row->artical_no }}</td>
                                                <td>{{ $row->vendor_item_no }}</td>
                                                <td>{{ $row->description }}</td>
                                                <td>{{ $row->quantity }}</td>
                                                <td>{{ $row->unit_of_measure }}</td>
                                                <td>{{ $row->vat_per }}</td>
                                                <td>{{ $row->direct_unit_cost }}</td>
                                                <td>{{ $row->vat_amount }}</td>
                                                <td>{{ $row->amount }}</td>
                                                <td>{{ $row->amount_eur }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-center mt-4">
                                    <button type="submit" class="btn btn-lg">Update</button>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.9.359/pdf.min.js"></script>
<script src="{{ asset('js/purchase_order.js') }}"></script>

<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var usd_euro = $(".usdValue").val();
        var aed_euro = $(".aedValue").val();
        var euro_euro = $(".eurValue").val();

        $('#project_number').on('blur', fetchProjectName);

        $(document).on('input', '#shipment_method', function() {
            $('#localSupplierContainer').css('display', $(this).val().toUpperCase().startsWith('EXW') ? 'flex' : 'none');
        });

        $('#purchaseOrderForm').on('submit', function(e) {
            e.preventDefault();

            const rows = $('#pdfTable tbody tr');
            const tableData = [];

            rows.each(function(index) {
                const cells = $(this).find('td');

                if (cells.length === 11) {
                    const realAmount = parseFloat(cells.eq(10).text().trim()) || 0;
                    const amountEUR = parseFloat(cells.eq(9).text().trim()) || 0;

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
                        amount: realAmount.toFixed(2),
                        amount_eur: amountEUR.toFixed(2),
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
                .then(response => {
                    if (!response.ok) {
                        // If response is not OK (e.g., 400 status), throw the response to catch it in the error block
                        return response.json().then(errorData => {
                            throw new Error(errorData.error || 'An error occurred');
                        });
                    }
                    return response.json(); // If response is OK, parse it as JSON
                })
                .then(data => {
                    // Success case
                    alert(data.success); // Show success message
                    window.location.href = "{{ route('ProcurementManagerInbox') }}";
                })
                .catch(error => {
                    // Error case
                    console.error('Error:', error.message);
                    alert(error.message); // Show the specific error message from the server
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

        document.getElementById('projectFields').style.display = isChecked ? 'flex' : 'none';
        document.getElementById('projectNameField').style.display = isChecked ? 'flex' : 'none';

        if (isChecked) {
            projectNumber.setAttribute('required', 'required');
            projectName.setAttribute('required', 'required');
        } else {
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
        let lastValue = currencyInput.value;

        setInterval(function() {
            if (currencyInput.value !== lastValue) {
                lastValue = currencyInput.value;
                updateCurrencyDisplay();
            }
        }, 500);
    });
</script>
@endsection