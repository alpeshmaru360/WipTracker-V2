@extends('layouts.main')

@section('content')
<link rel="stylesheet" href="{{ asset('css/purchase_order.css') }}" />
<meta name="csrf-token" content="{{ csrf_token() }}">

<section class="assembly_page bg-image mt-4">
    <div class="mask d-flex align-items-center h-100 gradient-custom-3">
        <div class="container h-100">
            <div class="row d-flex align-items-center h-100">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body p-4">
                            <!-- Back Button at Top-Left with Custom Styling -->
                            <div class="mb-4">
                                <a href="javascript:history.back();" class="btn back_btn">
                                    <i class="fas fa-arrow-left"></i> Back
                                </a>
                            </div>

                            <h2 class="text-center mb-4">View Purchase Order</h2>

                            <div class="row mt-3">
                                <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                    <label class="form-label">PO PDF</label>
                                </div>
                                <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                    <a href="{{ asset('purchase_order_pdf/' . $purchaseOrder->po_pdf) }}" target="_blank" class="w-100 pb-2 pt-2">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                    <label class="form-label">PO Number</label>
                                </div>
                                <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                    <input type="text" class="w-100 pb-2 pt-2" value="{{ $purchaseOrder->po_number }}" readonly />
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                    <label class="form-label">Supplier</label>
                                </div>
                                <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                    <input type="text" class="w-100 pb-2 pt-2" value="{{ $purchaseOrder->supplier }}" readonly />
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                    <label class="form-label">Project Order</label>
                                </div>
                                <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                    <input type="checkbox" style="width: 20px; height: 20px; accent-color: green;" {{ $purchaseOrder->is_project_order ? 'checked' : '' }} disabled />
                                    <span>{{ $purchaseOrder->is_project_order ? 'Yes' : 'No' }}</span>
                                </div>
                            </div>

                            @if($purchaseOrder->is_project_order)
                            <div class="row mt-3" id="projectFields">
                                <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                    <label class="form-label">Project No</label>
                                </div>
                                <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                    <input type="text" class="w-100 pb-2 pt-2" value="{{ $purchaseOrder->project_no ?? 'N/A' }}" readonly />
                                </div>
                            </div>

                            <div class="row mt-3" id="projectNameField">
                                <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                    <label class="form-label">Project Name</label>
                                </div>
                                <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                    <input type="text" class="w-100 pb-2 pt-2" value="{{ $purchaseOrder->project_name ?? 'N/A' }}" readonly />
                                </div>
                            </div>
                            @endif

                            <div class="row mt-3">
                                <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                    <label class="form-label">Payment Terms</label>
                                </div>
                                <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                    <input type="text" class="w-100 pb-2 pt-2" value="{{ $purchaseOrder->payment_terms }}" readonly />
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                    <label class="form-label">Shipment Method</label>
                                </div>
                                <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                    <input type="text" class="w-100 pb-2 pt-2" value="{{ $purchaseOrder->shipment_method }}" readonly />
                                </div>
                            </div>

                            @if(str_starts_with($purchaseOrder->shipment_method, 'EXW') && $purchaseOrder->is_local_supplier)
                            <div class="row mt-3">
                                <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                    <label class="form-label">Local Supplier</label>
                                </div>
                                <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                    <input type="checkbox" style="width: 20px; height: 20px; accent-color: green;" checked disabled />
                                    <span>Yes</span>
                                </div>
                            </div>
                            @endif

                            <div class="row mt-3">
                                <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                    <label class="form-label">Order Date</label>
                                </div>
                                <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                    <input type="text" class="w-100 pb-2 pt-2" value="{{ $purchaseOrder->order_date }}" readonly />
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                                    <label class="form-label">Currency</label>
                                </div>
                                <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-7">
                                    <input type="text" class="w-100 pb-2 pt-2" value="{{ $purchaseOrder->purchaseOrderTables->first()->currency ?? 'N/A' }}" readonly />
                                </div>
                            </div>

                            <div id="pdfTableContainer" class="container mt-4 table-responsive">
                                <h4>Items</h4>
                                <table id="pdfTable" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="po_table_heading p-1">Position No.</th>
                                            <th scope="col" class="po_table_heading p-1">Article No.</th>
                                            <th scope="col" class="po_table_heading p-1">Vendor Item No.</th>
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
                                        @foreach($purchaseOrder->purchaseOrderTables as $item)
                                        <tr>
                                            <td>{{ $item->position_no }}</td>
                                            <td>{{ $item->artical_no }}</td>
                                            <td>{{ $item->vendor_item_no }}</td>
                                            <td>{{ $item->description }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ $item->unit_of_measure }}</td>
                                            <td>{{ $item->vat_per }}</td>
                                            <td>{{ $item->direct_unit_cost }}</td>
                                            <td>{{ $item->vat_amount }}</td>
                                            <td>{{ $item->amount }}</td>
                                            <td>{{ $item->amount_eur }}</td>
                                        </tr>
                                        @endforeach
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