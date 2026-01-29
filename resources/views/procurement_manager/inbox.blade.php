@extends('layouts.main')
@section('content')
<link rel="stylesheet" href="{{ asset('css/procurement_manager.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap-switch-button@1.1.0/css/bootstrap-switch-button.min.css">
<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap-switch-button@1.1.0/dist/bootstrap-switch-button.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="procurement_inbox_page main_section bg-white m-4 pb-5">
    <div class="d-flex justify-content-between align-items-center px-5 pt-3">
        <h3 class="pt-4 text-bold text-left text-uppercase">Pending BOM Check</h3>
        <a class="mt-4 mb-2 btn btn-primary btn-sm py-2 px-3" onclick="location.reload();">
            <i class="fa fa-refresh text-light">&#xf021;</i>
        </a>
    </div>

    <hr class="mx-5 mt-2 mb-2" />

    <div class="container-fluid px-5 mx-3">
        <div class="row mt-3 table-responsive">
            @if(count($bom_check) > 0)
            <table class="table table-hover table-bordered w-100 text-center" id="procument_role_pending_task">
                <thead>
                    <tr>
                        <th scope="col" class="project_table_heading">Request Date</th>
                        <th scope="col" class="project_table_heading">Deadline</th>
                        <th scope="col" class="project_table_heading">Project No.</th>
                        <th scope="col" class="project_table_heading">Project Name</th>
                        <th scope="col" class="project_table_heading">Article No.</th>
                        <th scope="col" class="project_table_heading">Product Description</th>
                        <th scope="col" class="project_table_heading">QTY</th>
                        <th scope="col" class="project_table_heading">Check BOM</th>
                        <th scope="col" class="project_table_heading">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bom_check as $val)
                    <tr>
                        @php
                            $requestDate = null;
                            $drawingMissing = false;
                            $bomMissing = false;

                            // Pricing Tool = 1, only drawing
                            if ($val->projects->is_pricing_tool_quotation_number == 1) {
                                if ($val->drawing_req_estimation_manager == 0) {
                                    $requestDate = $val->created_at;
                                } elseif (!empty($val->drawing_upload_date)) {
                                    $requestDate = \Carbon\Carbon::parse($val->drawing_upload_date);
                                } else {
                                    $drawingMissing = true;
                                }
                            } else {
                                // Pricing Tool = 0, check both drawing + BOM
                                // Drawing
                                if ($val->drawing_req_estimation_manager == 0) {
                                    $drawingDate = $val->created_at;
                                } elseif (!empty($val->drawing_upload_date)) {
                                    $drawingDate = \Carbon\Carbon::parse($val->drawing_upload_date);
                                } else {
                                    $drawingMissing = true;
                                }

                                // BOM
                                if ($val->bom_req_estimation_manager == 0) {
                                    $bomDate = $val->created_at;
                                } elseif (!empty($val->bom_upload_date)) {
                                    $bomDate = \Carbon\Carbon::parse($val->bom_upload_date);
                                } else {
                                    $bomMissing = true;
                                }

                                // Take max date if both are uploaded
                                if (!$drawingMissing && !$bomMissing) {
                                    $requestDate = $drawingDate > $bomDate ? $drawingDate : $bomDate;
                                }
                            }

                            // Determine the message
                            if ($drawingMissing && $bomMissing) {
                                $message = 'Waiting for Estimation Drawing & BOM PDF';
                            } elseif ($drawingMissing) {
                                $message = 'Waiting for Estimation Drawing PDF';
                            } elseif ($bomMissing) {
                                $message = 'Waiting for Estimation BOM PDF';
                            } else {
                                $message = $requestDate ? $requestDate->format('d-m-y H:i') : null;
                            }
                        @endphp

                        <td>{{ $message }}</td>

                        <td class="{{ $val->deadline && $requestDate && \Carbon\Carbon::now()->greaterThan($val->deadline) ? 'text-danger' : 'text-success' }}">
                            {{ $val->deadline && $requestDate ? $val->deadline->format('d-m-y H:i') : '--' }}
                        </td>
                        <td>{{$val->projects['project_no']}}</td>
                        <td>{{$val->projects['project_name']}}</td>
                        <td>{{$val->full_article_number}}</td>
                        <td>{{ $val->description}}</td>
                        <td>{{$val->qty}}</td>
                        <td>
                            <button class="btn btn-info check-bom-btn btn-primary py-1 px-2"
                                data-id="{{ $val->id }}"
                                data-toggle="modal"
                                data-target="#bomModal"
                                data-drawing_req_estimation_manager="{{$val->drawing_req_estimation_manager}}">
                                <i class="fa fa-eye"></i>
                            </button>
                        </td>
                        <td>
                            <button class="btn btn-primary add-remark-btn py-1 px-2"
                                data-id="{{ $val->id }}"
                                data-toggle="modal"
                                data-target="#remarkModal">
                                <i class="fa fa-plus"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="alert alert-info w-100">
                No Pending BOM Check found.
            </div>
            @endif
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center px-5 pt-3">
        <h3 class="pt-4 text-bold text-left text-uppercase">BOM Items - Place PO</h3>
        <a class="mt-4 mb-2 btn btn-primary btn-sm py-2 px-3" onclick="location.reload();">
            <i class="fa fa-refresh text-light">&#xf021;</i>
        </a>
    </div>

    <hr class="mx-5 mt-2 mb-2" />

    <div class="container-fluid px-5 mx-3">
        <div class="row mt-3 table-responsive">
            @if(count($pending_po_orders) > 0)
            <table class="table table-hover table-bordered w-100 text-center" id="pending_po_stock_comparison">
                <thead>
                    <tr>
                        <th scope="col" class="project_table_heading">Request Date</th>
                        <th scope="col" class="project_table_heading">Deadline</th>
                        <th scope="col" class="project_table_heading">Project No.</th>
                        <th scope="col" class="project_table_heading">Project Name</th>
                        <th scope="col" class="project_table_heading">Article No.</th>
                        <th scope="col" class="project_table_heading">Product Description</th>
                        <th scope="col" class="project_table_heading">QTY</th>
                        <th scope="col" class="project_table_heading">View BOM</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pending_po_orders as $bom)
                    <tr>
                        <td>
                            @php
                            $processedDate = \App\Models\StockBOMPo::where('project_id', $bom->project_id)
                            ->where('product_id', $bom->id)
                            ->value('processed_at');
                            @endphp
                            {{ $processedDate ? \Carbon\Carbon::parse($processedDate)->format('d-m-y H:i') : 'N/A' }}
                        </td>
                        <td class="{{ $bom->deadline && \Carbon\Carbon::now()->greaterThan($bom->deadline) ? 'text-danger' : 'text-success' }}">
                            {{ $bom->deadline ? $bom->deadline->format('d-m-y H:i') : 'N/A' }}
                        </td>
                        <td>{{ $bom->projects->project_no ?? '-' }}</td>
                        <td>{{ $bom->projects['project_name']}}</td>
                        <td>{{ $bom->full_article_number }}</td>
                        <td>{{ $bom->description }}</td>
                        <td>{{ $bom->qty }}</td>
                        <td>
                            <button class="btn btn-info check-bom-btn btn-primary py-1 px-2"
                                data-id="{{ $bom->id }}"
                                data-toggle="modal"
                                data-target="#bomModal">
                                <i class="fa fa-eye"></i>
                            </button>
                        </td>                      
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="alert alert-info w-100">
                No Pending PO Orders from Stock Comparison.
            </div>
            @endif
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center px-5 pt-3">
        <h3 class="pt-4 text-bold text-left text-uppercase">Rejected Purchase Orders</h3>
        <a class="mt-4 mb-2 btn btn-primary btn-sm py-2 px-3" onclick="location.reload();">
            <i class="fa fa-refresh text-light">&#xf021;</i>
        </a>
    </div>

    <hr class="mx-5 mt-2 mb-2" />

    <div class="container-fluid px-5 mx-3">
        <div class="row mt-3 table-responsive">
            @if($RejectedPurchaseOrders->isNotEmpty())
            <table class="table table-hover table-bordered w-100 text-center" id="rejected_purchase_order">
                <thead>
                    <tr>
                        <th scope="col" class="project_table_heading p-1">PO No.</th>
                        <th scope="col" class="project_table_heading p-1">Project No.</th>
                        <th scope="col" class="project_table_heading p-1">Project Name</th>
                        <th scope="col" class="project_table_heading p-1">Supplier</th>
                        <th scope="col" class="project_table_heading p-1">Order Date</th>
                        <th scope="col" class="project_table_heading p-1">Payment Terms</th>
                        <th scope="col" class="project_table_heading p-1">Rejected by</th>
                        <th scope="col" class="project_table_heading p-1" style="width:25%;">Action</th>
                    </tr>
                </thead>
                <tbody id="project_table_body">
                    @foreach($RejectedPurchaseOrders as $val)
                    <tr>
                        <td>{{ $val->po_number }}</td>
                        <td>{{ $val->project_no ?? 'N/A' }}</td>
                        <td>{{ $val->project_name ?? 'N/A' }}</td>
                        <td>{{ $val->supplier ?? 'N/A'}}</td>
                        <td>{{ $val->order_date ?? 'N/A' }}</td>
                        <td>{{ $val->payment_terms ?? 'N/A' }}</td>
                        <td>
                            @php
                            $rejectedBy = [];

                            if ($val->is_production_manager_approved == 2 && !empty($val->production_manager_reject_date)) {
                            $rejectedBy[] = 'Assembly Manager - ' . \Carbon\Carbon::parse($val->production_manager_reject_date)->format('d-m-Y');
                            }
                            if ($val->is_production_engineer_approved == 2 && !empty($val->production_engineer_reject_date)) {
                            $rejectedBy[] = 'Production Engineer - ' . \Carbon\Carbon::parse($val->production_engineer_reject_date)->format('d-m-Y');
                            }
                            @endphp
                            {{ implode(', ', $rejectedBy) ?: 'N/A' }}
                        </td>
                        <td>
                            <button class="btn btn-danger btn-sm view-reasons-btn px-3 py-1"
                                data-manager-reason="{{ $val->rejection_reason_production_manager ?? 'No reason provided' }}"
                                data-engineer-reason="{{ $val->rejection_reason_production_engineer ?? 'No reason provided' }}" data-bs-toggle="modal"
                                data-bs-target="#rejectionReasonModal">
                                View Reasons
                            </button>
                            <a href="{{ route('procurement_manager.reupload_po', $val->id) }}" class="btn btn-info btn-sm px-3 py-1">Re-upload PO</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="alert alert-info w-100">
                No pending purchase orders found. All purchase orders have been processed.
            </div>
            @endif
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center px-5 pt-3">
        <h3 class="pt-4 text-bold text-left text-uppercase">Pending BOM Upload from Estimation Manager Side</h3>
        <a class="mt-4 mb-2 btn btn-primary btn-sm py-2 px-3" onclick="location.reload();">
            <i class="fa fa-refresh text-light">&#xf021;</i>
        </a>
    </div>

    <hr class="mx-5 mt-2 mb-2" />

    <div class="container-fluid px-5 mx-3">
        <div class="row mt-3 table-responsive">
            @if(count($pending_bom) > 0)
            <table class="table table-hover table-bordered w-100 text-center" id="pending_bom_upload">
                <thead>
                    <tr>                      
                        <th scope="col" class="project_table_heading">Project No.</th>
                        <th scope="col" class="project_table_heading">Project Name</th>
                        <th scope="col" class="project_table_heading">Article No.</th>
                        <th scope="col" class="project_table_heading">Product Description</th>
                        <th scope="col" class="project_table_heading">QTY</th>
                        <th scope="col" class="project_table_heading">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pending_bom as $val)
                    <tr>
                        <td>{{ $val->projects['project_no'] }}</td>
                        <td>{{ $val->projects['project_name'] }}</td>
                        <td>{{ $val->full_article_number }}</td>
                        <td>{{ $val->description }}</td>
                        <td>{{ $val->qty }}</td>
                        <td>Pending Upload</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="alert alert-info w-100">
                No Pending BOM Upload from Estimation Manager Side found.
            </div>
            @endif
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center px-5 pt-3">
        <h3 class="pt-4 text-bold text-left text-uppercase">Minimum Low Stock Alert</h3>
        <a class="mt-4 mb-2 btn btn-primary btn-sm py-2 px-3" onclick="location.reload();">
            <i class="fa fa-refresh text-light">&#xf021;</i>
        </a>
    </div>

    <hr class="mx-5 mt-2 mb-2" />

    <div class="container-fluid px-5 mx-3">
        <div class="row mt-3 table-responsive">
            @if(count($minimumLowStock) > 0)
            <table class="table table-hover table-bordered w-100 text-center" id="stock_available_table">
                <thead>
                    <tr>
                        <th scope="col" class="project_table_heading">Article Number</th>
                        <th scope="col" class="project_table_heading">Item Description</th>
                        <th scope="col" class="project_table_heading">Qty</th>
                        <th scope="col" class="project_table_heading">Reserved [Hold] Qty</th>
                        <th scope="col" class="project_table_heading">Available Qty</th>
                        <th scope="col" class="project_table_heading">Minimum Required Qty</th>
                        <th scope="col" class="project_table_heading">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($minimumLowStock as $val)
                    <tr>
                        <td>{{ $val->article_number }}</td>
                        <td>{{ $val->item_desc }}</td>
                        <td>{{ $val->qty }}</td>
                        <td>{{ $val->hold_qty }}</td>
                        <td>{{ $val->available_qty }}</td>
                        <td>{{ $val->minimum_required_qty }}</td>
                        <td>
                            @php
                                // Fetch matching PO record
                                $poRecord = DB::table('purchase_order_table')
                                    ->where('artical_no', $val->article_number)
                                    ->where('description', $val->item_desc)
                                    ->select('is_initial_inspection_started')
                                    ->first();
                            @endphp

                            @if($poRecord && $poRecord->is_initial_inspection_started == 1)
                                {{-- Show "PO Created" only if record exists and inspection started --}}                                
                                <a class="btn btn-primary btn-sm ml-2 place-order-btn-disabled py-1" 
                                    tabindex="-1" 
                                    aria-disabled="true"> 
                                        PO Created 
                                </a>
                            @else
                                <a href="{{ route('addPOFromStock', $val->id) }}"
                                   class="btn btn-primary btn-sm ml-2 place-order-btn py-1">
                                    Place Order
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="alert alert-info w-100">
                No data available
            </div>
            @endif
        </div>
    </div>

    <!-- Rejection Reasons Modal -->
    <div class="modal fade" id="rejectionReasonModal" tabindex="-1" aria-labelledby="rejectionReasonModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectionReasonModalLabel">Rejection Reasons</h5>
                </div>
                <div class="modal-body">
                    @if(isset($val->is_production_manager_approved) && $val->is_production_manager_approved == 2)
                    <div class="mb-3" id="managerReasonSection">
                        <label class="form-label"><strong>Assembly Manager's Reason:</strong></label>
                        <textarea class="form-control" id="managerReason" readonly>{{ $val->rejection_reason_production_manager ?? 'No reason provided' }}</textarea>
                    </div>
                    @endif
                    @if(isset($val->is_production_engineer_approved) && $val->is_production_engineer_approved == 2)
                    <div class="mb-3" id="engineerReasonSection">
                        <label class="form-label"><strong>Production Engineer's Reason:</strong></label>
                        <textarea class="form-control" id="engineerReason" readonly>{{ $val->rejection_reason_production_engineer ?? 'No reason provided' }}</textarea>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- New Remark Modal -->
    <div class="modal fade" id="drawingRemarkModal" tabindex="-1" role="dialog" aria-labelledby="drawingRemarkModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="drawingRemarkModalLabel">Add Remark</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="drawingRemarkForm" action="{{ route('SaveProcurementDrawingRemark') }}" method="POST">
                        @csrf
                        <input type="hidden" name="project_id">
                        <div class="form-group">
                            <label for="remark">Remark:</label>
                            <textarea class="form-control" name="remark" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" id="saveRemark">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- BOM Modal -->
    <div class="modal fade" id="bomModal" tabindex="-1" role="dialog" aria-labelledby="bomModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bomModalLabel">BOM Details</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="">
                    <button type="button" class="btn btn-info mt-3 mr-3" id="selectBulkSupplierBtn" style="float: right;">
                        Select Bulk Supplier
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-responsive">
                        <thead>
                            <tr>
                                <th style="width: 5%;">Sr No</th>
                                <th style="width: 28%;">Description</th>
                                <th style="width: 10%;">Article No</th>
                                <th style="width: 6%;">Item Qty</th>
                                <th style="width: 6%;">Product Qty</th>
                                <th style="width: 6%;">Total Needed Qty</th>
                                <th style="width: 6%;">In Stock Qty</th>
                                <th style="width: 14%;">Select Option</th>
                                <th style="width: 15%;">Supplier</th>
                                <th style="width: 15%;" class="d-none po_added_checkmark">PO Status</th>
                            </tr>
                        </thead>
                        <tbody id="bomTableBody">
                            <!-- BOM rows will load here -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-primary"
                        id="saveBOMSelection">
                        Process Order
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Supplier Selection Modal -->
    <div class="modal fade" id="bulkSupplierModal" tabindex="-1" role="dialog" aria-labelledby="bulkSupplierModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkSupplierModalLabel">Select Bulk Supplier</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="text"
                        id="supplierSearchInput"
                        class="form-control"
                        placeholder="Search suppliers...">
                    <div id="supplierListContainer">
                        <!-- Supplier list will be populated here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Item Selection Modal -->
    <div class="modal fade" id="itemSelectionModal" tabindex="-1" role="dialog" aria-labelledby="itemSelectionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content itemslectioncontent">
                <div class="modal-header">
                    <h5 class="modal-title" id="itemSelectionModalLabel">Select Items for <span id="selectedSupplierName"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="bulk-items-list" id="bulkItemsList">
                        <!-- Items will be populated here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveBulkSelectionBtn">Save Selection</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Remark Modal -->
    <div class="modal fade" id="remarkModal" tabindex="-1" role="dialog" aria-labelledby="remarkModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="remarkModalLabel">Add Remark</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="remarkForm" action="{{ route('SaveProcurementRemark') }}" method="POST">
                        @csrf
                        <input type="hidden" id="project_id" name="project_id">
                        <div class="form-group">
                            <label for="remark">Remark:</label>
                            <textarea class="form-control" id="remark" name="remark" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" id="saveRemark">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!--Partial Modal -->
    <div class="modal fade" id="partialQtyModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Enter Partial Quantity</h5>
                </div>
                <div class="modal-body">
                    <p><strong>In Stock Qty:</strong> <span id="partialModalStockQty"></span></p>
                    <p><strong>Total Needed Qty:</strong> <span id="partialModalTotalQty"></span></p>
                    <input type="number" id="partialQtyInputField" class="form-control" min="1" placeholder="Enter Qty" />
                </div>
                <div class="modal-footer">
                    <button type="button" id="savePartialQtyBtn" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
@section('scripts')

    <script>
        $(document).ready(function() {
            $('#bomTableBody').empty();
            $('#procument_role_pending_task').DataTable().destroy();
            $('#procument_role_pending_task').DataTable({
                paging: true,
                pageLength: 2,
                lengthMenu: [2, 5, 10, 25, 50, 100],
                searching: true,
                ordering: false
            });
            $('#procument_role_pending_task').removeClass('dataTable');
            $('#pending_po_stock_comparison').DataTable({
                paging: true,
                pageLength: 2,
                lengthMenu: [2, 5, 10, 25, 50, 100],
                searching: true,
                ordering: false
            });
            $('#pending_po_stock_comparison').removeClass('dataTable');
            $('#rejected_purchase_order').DataTable({
                paging: true,
                pageLength: 2,
                lengthMenu: [2, 5, 10, 25, 50, 100],
                searching: true,
                ordering: false
            });
            $('#rejected_purchase_order').removeClass('dataTable');
            $('#pending_bom_upload').DataTable({
                paging: true,
                pageLength: 2,
                lengthMenu: [2, 5, 10, 25, 50, 100],
                searching: true,
                ordering: false
            });
            $('#pending_bom_upload').removeClass('dataTable');
            // $('#procument_role_pending_drawing_check').DataTable({
            //     paging: true,
            //     pageLength: 2,
            //     lengthMenu: [2, 5, 10, 25, 50, 100],
            //     searching: true,
            //     ordering: false
            // });
            // $('#procument_role_pending_drawing_check').removeClass('dataTable');
            $('#stock_available_table').DataTable({
                paging: true,
                pageLength: 2,
                lengthMenu: [2, 5, 10, 25, 50, 100],
                searching: true,
                ordering: false
            });
            $('#stock_available_table').removeClass('dataTable');
        });
    </script>

    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.procurement-check');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const id = this.dataset.id;
                    const type = this.dataset.type;
                    const checked = this.checked ? 2 : 1;
                    const csrfToken = "{{ csrf_token() }}";
                    fetch("{{route('UpdateProcurementCheckStatus')}}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                id: id,
                                type: type,
                                checked: checked
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert('An error occurred. Please try again.');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred. Please try again.');
                        });
                });
            });
        });
   
        let globalSuppliers = [];
        $(document).on('click', '.check-bom-btn', function() {
                // Get value from the clicked button
                let drawingReqEstimationManager = $(this).data('drawing_req_estimation_manager');
                // Store this value temporarily if needed
                $('#bomModal').data('drawing_req_estimation_manager', drawingReqEstimationManager);
                // Check and toggle button
                if (drawingReqEstimationManager == 1) {
                    // Disable the Process Order button
                    $('#saveBOMSelection').prop('disabled', true).addClass('btn-secondary').removeClass('btn-primary');
                } else {
                    // Enable the Process Order button
                    $('#saveBOMSelection').prop('disabled', false).addClass('btn-primary').removeClass('btn-secondary');
                }

            var productId = $(this).data('id');
            $.ajax({
                url: "{{ route('GetProductBOM') }}",
                method: "POST",
                data: {
                    id: productId,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    $('#bomTableBody').empty();
                    $('#bomTableBody').data('product-id', response.productId);
                    $('#bomTableBody').data('project-id', response.projectId);
                    globalSuppliers = response.suppliers.sort((a, b) => a.localeCompare(b));

                    // Custom matcher to prioritize prefix matches
                    function customMatcher(params, data) {
                        if (!params.term || params.term.trim() === '') {
                            return data;
                        }
                        var term = params.term.toLowerCase();
                        var text = data.text.toLowerCase();
                        if (text.indexOf(term) > -1) {
                            var startsWithTerm = text.indexOf(term) === 0;
                            return {
                                id: data.id,
                                text: data.text,
                                startsWith: startsWithTerm
                            };
                        }
                        return null;
                    }

                    let renderedBOMSuppliers = new Set();

                    if (response.bomItems.length > 0) {
                        $.each(response.bomItems, function(index, item) {
                            let stockQty = parseInt(item.qty) || 0;
                            let totalRequiredQty = parseInt(item.total_required_qty) || 0;
                            let isStockEnough = stockQty >= totalRequiredQty;
                            let isStockAvailable = stockQty > 0;
                            let stockOption = isStockEnough ? '' : 'disabled';
                            let newOrderOption = !isStockAvailable ? '' : (isStockEnough ? 'disabled' : '');
                            let partialOption = isStockAvailable ? '' : 'disabled';
                            let projectId = response.projectId;
                            let productId = response.productId;
                            let selectedOption = item.saved_option || '';

                            // Auto-select the appropriate option if not already saved
                            if (!selectedOption) {
                                if (stockOption === '') {
                                    // If "From Stock" is available (not disabled), select it
                                    selectedOption = 'stock';
                                } else if (newOrderOption === '') {
                                    // If "New Order" is available (not disabled), select it
                                    selectedOption = 'new_order';
                                } else if (partialOption === '') {
                                    // Only if both stock and new_order are disabled, select partial
                                    selectedOption = 'partial';
                                }
                            }
                            let selectedSupplier = item.saved_supplier || '';
                            let selectedPartialQty = item.partial_qty || 0;

                            let supplierOptions = '<option value="">Select Supplier</option>';
                            $.each(globalSuppliers, function(i, supplier) {
                                supplierOptions += `<option value="${supplier}" ${selectedSupplier === supplier ? 'selected' : ''}>${supplier}</option>`;
                            });

                            let optionCell, supplierCell;
                            let checkmarkCell = '';
                            let renderedSuppliers = {};
                            if (response.isSaved) {
                                let formattedOption = '-';
                                if (selectedOption === 'stock') {
                                    formattedOption = 'FROM STOCK';
                                } else if (selectedOption === 'new_order') {
                                    formattedOption = 'NEW ORDER';
                                } else if (selectedOption === 'partial') {
                                    let hold_qty = item.hold_qty;
                                    let viewBOMnewOrder = totalRequiredQty - hold_qty;
                                    let fromStock = stockQty;
                                    formattedOption = `PARTIAL <i class="fa fa-eye text-primary view-partial" style="cursor:pointer;" data-stock="${fromStock}" data-total="${totalRequiredQty}" data-hold_qty="${item.hold_qty}" data-viewBOMnewOrder="${viewBOMnewOrder}"></i>`;
                                }

                                let formattedSupplier = selectedSupplier || (selectedOption === 'stock' ? 'N/A' : '-');
                                optionCell = `<td>${formattedOption}</td>`;
                                supplierCell = `<td>${formattedSupplier}</td>`;

                                if (item.po_added == 1) {
                                    checkmarkCell = `<td class="po_added_checkmark">
                                    <span class="badge-success fw-bold m-2 mt-5 fs-15 p-1 text-white">Added</span></td>`;
                                } else {
                                    if (selectedOption === 'stock') {
                                        checkmarkCell = `<td class="text-center">N/A</td>`;
                                    } else {
                                        let showAddBtn = !renderedBOMSuppliers.has(formattedSupplier);
                                        checkmarkCell = `<td class="po_added_checkmark d-none">`;
                                        if (showAddBtn) {
                                            checkmarkCell += `
                                            <button type="button" class="btn btn-danger place-po-btn"
                                                data-stock_bom_supplier="${selectedSupplier}"
                                                data-stock_bom_id="${item.stock_bom_id}"
                                                data-projectId="${projectId}"
                                                data-productId="${productId}">
                                                Add
                                            </button>`;
                                        }
                                        checkmarkCell += `</td>`;
                                        renderedBOMSuppliers.add(formattedSupplier);
                                    }
                                }
                            } else {
                                let partialQtyInput = `<input type="hidden" class="partial-qty-input" value="${selectedPartialQty}">`;
                                let partialQtyBadge = '';
                                if (selectedOption === 'partial' && selectedPartialQty > 0) {
                                    partialQtyBadge = `
                                        <i class="fa fa-eye text-primary view-partial ml-2" 
                                        style="cursor:pointer; font-size: 16px;" 
                                        data-stock="${stockQty}" 
                                        data-total="${totalRequiredQty}" 
                                        data-hold_qty="${selectedPartialQty}" 
                                        title="View Partial Qty Breakdown"></i>
                                    `;
                                }

                                optionCell = `
                                    <td>
                                        <select class="form-control select-option" style="display: inline-block; width: auto;">
                                            <option value="">Select Option</option>
                                            <option value="stock" ${selectedOption === 'stock' ? 'selected' : ''} ${stockOption}>From Stock</option>
                                            <option value="new_order" ${(selectedOption === 'new_order' || (!selectedOption && !isStockAvailable)) ? 'selected' : ''} ${newOrderOption}>New Order</option>
                                            <option value="partial" ${selectedOption === 'partial' ? 'selected' : ''} ${partialOption}>Partial</option>
                                        </select>
                                        ${partialQtyBadge}
                                        ${partialQtyInput}
                                    </td>
                                `;
                                supplierCell = `
                                <td>
                                    <select class="form-control supplier-select" style="width:100%;" ${selectedOption === 'stock' ? 'disabled' : ''}>
                                        ${selectedOption === 'stock' ? '<option value="N/A" selected>N/A</option>' : supplierOptions}
                                    </select>
                                </td>
                            `;
                                checkmarkCell = `<td class="po_added_checkmark d-none">
                                <button type="button" class="btn btn-danger place-po-btn">Add</button></td>`;
                            }

                            $('#bomTableBody').append(`
                            <tr data-index="${index}" data-item-id="${item.id}" data-stock="${stockQty}" data-total="${totalRequiredQty}">
                                <td>${index + 1}</td>
                                <td>${item.item_desc || '-'}</td>
                                <td>${item.wilo_article_no || '-'}</td>
                                <td>${item.item_qty || '-'}</td>
                                <td>${item.product_qty || '-'}</td>
                                <td>${totalRequiredQty}</td>
                                <td>${stockQty}</td>
                                ${optionCell}
                                ${supplierCell}
                                ${checkmarkCell}
                            </tr>
                        `);
                        });
                        // Sort rows to show new_order items at the end
                        // Sort rows: Show "From Stock" items first, then "New Order" items
                        if (!response.isSaved) {
                            let tbody = $('#bomTableBody');
                            let rows = tbody.find('tr').toArray();

                            let fromStockRows = [];
                            let newOrderRows = [];

                            rows.forEach(function(row) {
                                let $row = $(row);
                                let stockQty = parseInt($row.data('stock')) || 0;
                                let totalQty = parseInt($row.data('total')) || 0;
                                let selectedOption = $row.find('.select-option').val();

                                // Check if this item CAN be fulfilled from stock
                                let canBeFromStock = (stockQty >= totalQty && stockQty > 0);

                                // Categorize rows: From Stock vs New Order
                                if (selectedOption === 'stock' || (!selectedOption && canBeFromStock)) {
                                    // Items that are "From Stock" or CAN be from stock
                                    fromStockRows.push(row);
                                } else {
                                    // Everything else is New Order (including partial, new_order, insufficient stock)
                                    newOrderRows.push(row);
                                }
                            });

                            console.log('Sorting BOM rows:', {
                                'From Stock': fromStockRows.length,
                                'New Order': newOrderRows.length
                            });

                            // Reorder: from-stock first, then new-order
                            tbody.empty();
                            fromStockRows.concat(newOrderRows).forEach(function(row, index) {
                                $(row).find('td:first').text(index + 1); // Update Sr No
                                tbody.append(row);
                            });
                        }

                        // Initialize Select2 with fixed maximum results and custom positioning
                        $('#bomTableBody .supplier-select').each(function() {
                            if (!$(this).hasClass('select2-hidden-accessible')) {
                                var $select = $(this);

                                $select.select2({
                                    placeholder: "Search Supplier",
                                    allowClear: false,
                                    width: '100%',
                                    dropdownParent: $('#bomModal'),
                                    dropdownAutoWidth: true,
                                    matcher: customMatcher,
                                    sorter: function(data) {
                                        return data.sort(function(a, b) {
                                            let term = $('.select2-search__field').val()?.toLowerCase() || '';
                                            let aText = (a.text || '').toLowerCase();
                                            let bText = (b.text || '').toLowerCase();

                                            // prioritize startsWith(term)
                                            if (aText.startsWith(term) && !bText.startsWith(term)) return -1;
                                            if (!aText.startsWith(term) && bText.startsWith(term)) return 1;

                                            // fallback: alphabetical
                                            return aText.localeCompare(bText);
                                        });
                                    },
                                    maximumResults: 5
                                });
                            }
                        });
                        // Apply N/A styling on initial load
                        $('#bomTableBody tr').each(function() {
                            let $row = $(this);
                            let supplierValue = $row.find('.supplier-select').val();
                            let $container = $row.find('.supplier-select').closest('td');

                            if (supplierValue === 'N/A') {
                                $container.addClass('na-supplier');
                            }
                        });

                        $('#saveBOMSelection').toggle(!response.isSaved);
                        if (response.isSaved) {
                            $(".po_added_checkmark").removeClass('d-none');
                            $("#selectBulkSupplierBtn").addClass('d-none');                                                        
                        } else {
                            $(".po_added_checkmark").addClass('d-none');
                            $("#selectBulkSupplierBtn").removeClass('d-none');
                        }
                    }
                },
                error: function() {
                    alert('Failed to fetch BOM data. Please try again.');
                }
            });
        });

        $(document).on('change', '.select-option', function() {
            let selected = $(this).val();
            let $row = $(this).closest('tr');
            let supplierSelect = $row.find('.supplier-select');
            let partialQtyInput = $row.find('.partial-qty-input');

            if (selected === 'stock') {
                let $container = supplierSelect.closest('td');
                supplierSelect.html('<option value="N/A" selected>N/A</option>');
                supplierSelect.prop('disabled', true);
                partialQtyInput.val(0);

                // Remove green styling and add yellow N/A styling
                $container.removeClass('selected').addClass('na-supplier');

                // Also remove any eye icons if switching from partial
                $(this).closest('tr').find('.view-partial').remove();
            } else if (selected === 'partial') {
                // Open the partial quantity modal
                let stockQty = parseInt($row.data('stock')) || 0;
                let totalQty = parseInt($row.data('total')) || 0;

                $('#partialModalStockQty').text(stockQty);
                $('#partialModalTotalQty').text(totalQty);
                $('#partialQtyInputField').val('');
                $('#partialQtyModal').data('row', $row);
                $('#partialQtyModal').modal('show');

                // Enable supplier dropdown and remove N/A styling
                let options = '<option value="">Select Supplier</option>';
                $.each(globalSuppliers, function(i, supplier) {
                    options += `<option value="${supplier}">${supplier}</option>`;
                });
                supplierSelect.html(options);
                supplierSelect.prop('disabled', false);

                // Remove N/A styling
                supplierSelect.closest('td').removeClass('na-supplier');
            } else {
                // For 'new_order' or any other option
                let options = '<option value="">Select Supplier</option>';
                $.each(globalSuppliers, function(i, supplier) {
                    options += `<option value="${supplier}">${supplier}</option>`;
                });
                supplierSelect.html(options);
                supplierSelect.prop('disabled', false);

                // Remove N/A styling
                supplierSelect.closest('td').removeClass('na-supplier');
            }
        });

        $('#savePartialQtyBtn').on('click', function() {
            let enteredQty = parseInt($('#partialQtyInputField').val());
            let $row = $('#partialQtyModal').data('row');
            let totalQty = parseInt($row.data('total')) || 0;
            let stockQty = parseInt($row.data('stock')) || 0;

            if (!enteredQty || enteredQty <= 0) {
                alert('Please enter a valid quantity.');
                return;
            }
            if (totalQty > 1) {
                if (enteredQty >= totalQty) {
                    alert(`Entered quantity cannot be equal to or more than total required (${totalQty}).`);
                    return;
                }
            }
            if (totalQty === 1) {
                if (enteredQty > totalQty) {
                    alert(`Entered quantity cannot be more than total required (${totalQty}).`);
                    return;
                }
            }
            if (enteredQty > stockQty) {
                alert(`Entered quantity cannot be more than available stock (${stockQty}).`);
                return;
            }

            // Save the partial quantity
            $row.find('.partial-qty-input').val(enteredQty);

            // Remove existing eye icon if any
            $row.find('.view-partial').remove();

            // Add eye icon next to the dropdown
            let $selectCell = $row.find('.select-option').closest('td');
            let newOrderQty = totalQty - enteredQty;

                $selectCell.find('.select-option').after(`
                <i class="fa fa-eye text-primary view-partial ml-2" 
                style="cursor:pointer; font-size: 16px;" 
                data-stock="${stockQty}" 
                data-total="${totalQty}" 
                data-hold_qty="${enteredQty}" 
                data-new-order="${newOrderQty}"
                title="View Partial Qty Breakdown"></i>
            `);

            $('#partialQtyModal').modal('hide');
        });

        $(document).on('hidden.bs.modal', '.modal', function() {
            // Check if there are any visible modals left
            if ($('.modal:visible').length > 0) {
                $('body').addClass('modal-open');
            }
        });

        $(document).on('click', '.view-partial', function() {
            let stockQty = parseInt($(this).data('stock')) || 0;
            let totalQty = parseInt($(this).data('total')) || 0;
            let hold_qty = parseInt($(this).data('hold_qty')) || 0;
            let viewBOMnewOrder = totalQty - hold_qty;
            Swal.fire({
                title: 'Partial Qty Breakdown',
                html: `
                <p><strong>From Stock Qty:</strong> ${hold_qty}</p>
                <p><strong>New Order Qty:</strong> ${viewBOMnewOrder}</p>
            `,
                confirmButtonText: 'OK'
            });
        });

        $('#saveBOMSelection').on('click', function() {
            let bomData = [];
            let productId = $('#bomTableBody').data('product-id');
            let projectId = $('#bomTableBody').data('project-id');

            $('#bomTableBody tr').each(function() {
                let row = $(this);
                let itemId = row.data('item-id');

                // Check if supplier was set via bulk selection
                let supplierValue = row.find('.supplier-select').val();
                if (bulkSupplierSelections[itemId]) {
                    supplierValue = bulkSupplierSelections[itemId];
                }

                bomData.push({
                    article_no: row.find('td:eq(2)').text().trim(),
                    item_desc: row.find('td:eq(1)').text().trim(),
                    item_qty: row.find('td:eq(3)').text().trim(),
                    product_qty: row.find('td:eq(4)').text().trim(),
                    total_required_qty: row.find('td:eq(5)').text().trim(),
                    stock_qty: row.find('td:eq(6)').text().trim(),
                    option: row.find('.select-option').val(),
                    supplier: supplierValue,
                    partial_qty: row.find('.partial-qty-input').val(),
                    product_id: productId,
                    project_id: projectId
                });
            });

            $.ajax({
                url: "{{ route('SaveStockBOMPo') }}",
                method: "POST",
                data: {
                    bom_data: bomData,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    alert(response.message);
                    location.reload();
                },
                error: function(xhr) {
                    alert(xhr.responseJSON.error);
                }
            });
        });

        $(document).on('change', '.supplier-select', function() {
            let selectedSupplier = $(this).val();
            let container = $(this).closest('td');

            // Remove all previous styling classes
            container.removeClass('selected na-supplier');

            if (selectedSupplier && selectedSupplier !== '') {
                container.addClass('selected');

                // Add special class for N/A
                if (selectedSupplier === 'N/A') {
                    container.addClass('na-supplier');
                }
            }
        });

        $(document).off('click', '.place-po-btn').on('click', '.place-po-btn', function() {
            debugger;
            let button = $(this);
            let stockBomId = $(this).data('stock_bom_id');
            let stockBomSupplier = $(this).data('stock_bom_supplier');
            let projectId = $(this).data('projectid');
            let productId = $(this).data('productid');
            if (!stockBomId) {
                alert("Stock BOM ID not found!");
                return;
            }
            if (!stockBomSupplier) {
                alert("Stock BOM Supplier not found!");
                return;
            }

            let remainingButtons = $('.place-po-btn').length;
            let confirmationMessage = (remainingButtons === 1) ?
                "This is the last PO to add. After this, the row will be removed and page will refresh. Continue?" :
                "Are you sure you want to mark this PO as added?";
            if (!confirm(confirmationMessage)) {
                return;
            }

            $.ajax({
                url: '/place-po-status',
                method: 'POST',
                data: {
                    stock_bom_id: stockBomId,
                    stock_bom_supplier: stockBomSupplier,
                    projectId: projectId,
                    productId: productId,
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    alert("PO added successfully!");
                    let parentTd = button.closest('td');
                    parentTd.removeClass('d-none');
                    parentTd.html(`<span class="badge-success fw-bold m-2 mt-5 fs-15 p-1 text-white">Added</span>`);
                    if (remainingButtons === 1) {
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    }
                },
                error: function(err) {
                    console.error(err);
                    alert("Something went wrong!");
                }
            });
        });

        $(document).on('click', '.add-remark-btn', function() {
            let projectId = $(this).data('id');
            $('#project_id').val(projectId);

            $.ajax({
                url: "{{ route('GetProcurementRemark', '') }}/" + projectId,
                method: 'GET',
                success: function(response) {
                    $('#remark').val(response.remark);
                }
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".view-reasons-btn").forEach(button => {
                button.addEventListener("click", function() {
                    let managerReason = this.getAttribute("data-manager-reason") || 'N/A';
                    let engineerReason = this.getAttribute("data-engineer-reason") || 'N/A';

                    // Set the textarea values, ensuring null/empty is replaced with 'N/A'
                    document.getElementById("managerReason").value = managerReason.trim() ? managerReason : 'N/A';
                    document.getElementById("engineerReason").value = engineerReason.trim() ? engineerReason : 'N/A';
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const viewReasonButtons = document.querySelectorAll('.view-reasons-btn');
            viewReasonButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const managerReason = this.getAttribute('data-manager-reason');
                    const engineerReason = this.getAttribute('data-engineer-reason');
                    // Update modal content
                    const managerTextarea = document.getElementById('managerReason');
                    const engineerTextarea = document.getElementById('engineerReason');
                    const managerSection = document.getElementById('managerReasonSection');
                    const engineerSection = document.getElementById('engineerReasonSection');
                    if (managerSection && managerTextarea) {
                        managerTextarea.value = managerReason;
                    }
                    if (engineerSection && engineerTextarea) {
                        engineerTextarea.value = engineerReason;
                    }
                });
            });
        });

        $(document).ready(function() {
            $('.check-bom-btn').on('click', function() {
                let drawingReqEstimationManager = $(this).data('drawing_req_estimation_manager');
                $('#bomModal').data('drawing_req_estimation_manager', drawingReqEstimationManager);
                if (drawingReqEstimationManager == 1) {
                    $('#saveBOMSelection').prop('disabled', true).addClass('btn-secondary').removeClass('btn-primary');
                } else {
                    $('#saveBOMSelection').prop('disabled', false).addClass('btn-primary').removeClass('btn-secondary');
                }
            });
            $('#bomModal').on('hidden.bs.modal', function() {
                $('#saveBOMSelection').prop('disabled', true).addClass('btn-secondary').removeClass('btn-primary');              
            });
        });

        let bulkSupplierSelections = {};
        $(document).on('show.bs.modal', '.modal', function() {
            $(document).off('focusin.bs.modal');
        });

        // Helper function to generate consistent item ID
        function generateItemId(row, index) {
            let itemId = $(row).data('item-id') || $(row).attr('data-item-id');
            let articleNo = $(row).find('td:eq(2)').text().trim();
            let itemDesc = $(row).find('td:eq(1)').text().trim();

            // If we have a proper data-item-id, use it
            if (itemId && itemId !== 'undefined' && itemId !== '') {
                return itemId;
            }

            // Check if article number is valid (not empty, not just "-", not just whitespace)
            if (articleNo && articleNo !== '-' && articleNo.replace(/\s/g, '') !== '') {
                // Clean the article number for use as ID (remove special chars except alphanumeric and +)
                let cleanArticleNo = articleNo.replace(/[^a-zA-Z0-9+]/g, '_');
                return 'article_' + cleanArticleNo;
            }

            // Fallback: use row index + description
            let cleanDesc = itemDesc.substring(0, 15).replace(/[^a-zA-Z0-9]/g, '_');
            return 'row_' + index + '_' + cleanDesc;
        }

        // Sync existing supplier dropdown selections into bulk logic
        function updateBulkSelectionsFromBOM() {

            $('#bomTableBody tr').each(function(index) {
                let $row = $(this);
                let itemId = generateItemId(this, index);

                let selectOption = $row.find('.select-option').val();
                let supplierValue = $row.find('.supplier-select').val();

                console.log(`Row ${index}:`, {
                    itemId: itemId,
                    selectOption: selectOption,
                    supplierValue: supplierValue
                });

                // Track items that are NOT "From Stock" and have a selected supplier
                if (selectOption !== 'stock' && supplierValue && supplierValue !== '' && supplierValue !== 'N/A') {
                    bulkSupplierSelections[itemId] = supplierValue;
                } else if (bulkSupplierSelections[itemId] && selectOption === 'stock') {
                    // Remove if changed to "From Stock"
                    delete bulkSupplierSelections[itemId];
                }
            });

        }

        // Function to build and show supplier list
        function showBulkSupplierList() {
            let supplierCounts = {};
            // Count items per supplier from bulkSupplierSelections
            for (let itemId in bulkSupplierSelections) {
                let supplier = bulkSupplierSelections[itemId];
                if (supplier) {
                    supplierCounts[supplier] = (supplierCounts[supplier] || 0) + 1;
                }
            }

            // Build supplier list HTML
            let supplierListHTML = '';
            globalSuppliers.forEach(function(supplier) {
                let count = supplierCounts[supplier] || 0;
                supplierListHTML += `
            <div class="supplier-item" data-supplier="${supplier}">
                <strong>${supplier}</strong> <span class="badge badge-success">(${count})</span>
            </div>
            `;
            });

            if (supplierListHTML === '') {
                supplierListHTML = '<p class="text-center">No suppliers available</p>';
            }

            $('#supplierListContainer').html(supplierListHTML);
            $('#bulkSupplierModal').modal('show');
        }

        // Open Bulk Supplier Modal
        $(document).off('click', '#selectBulkSupplierBtn').on('click', '#selectBulkSupplierBtn', function() {
            updateBulkSelectionsFromBOM(); // Sync existing selections first
            showBulkSupplierList();
        });

        // When supplier is clicked in bulk modal
        $(document).off('click', '.supplier-item').on('click', '.supplier-item', function() {
            let selectedSupplier = $(this).data('supplier');
            $('#selectedSupplierName').text(selectedSupplier);

            // Get all "new_order" items
            let itemsHTML = '';
            let itemCount = 0;
            let skippedCount = 0;

            $('#bomTableBody tr').each(function(index) {
                let $row = $(this);
                let selectOption = $row.find('.select-option').val();

                if (selectOption !== 'stock') {
                    // Try multiple ways to get item ID
                    let itemId = $row.data('item-id') || $row.attr('data-item-id');
                    let articleNo = $row.find('td:eq(2)').text().trim();
                    let itemDesc = $row.find('td:eq(1)').text().trim();

                    console.log(`Row ${index}:`, {
                        'data-item-id (jQuery)': $row.data('item-id'),
                        'data-item-id (attr)': $row.attr('data-item-id'),
                        'articleNo': articleNo,
                        'itemDesc': itemDesc
                    });

                    // Use article number as fallback if item-id is not available
                    if (!itemId || itemId === 'undefined') {
                        if (articleNo && articleNo !== '-' && articleNo !== '') {
                            itemId = 'article_' + articleNo;
                        } else {
                            // Use row index for items with "-" or no article number
                            itemId = 'row_' + index + '_' + itemDesc.substring(0, 10).replace(/\s/g, '_');
                        }
                    }
                    // Check if already assigned to another supplier (from BOM or bulk)
                    let assignedSupplier = bulkSupplierSelections[itemId];
                    // Only show items NOT assigned to other suppliers OR assigned to current supplier
                    if (!assignedSupplier || assignedSupplier === selectedSupplier) {
                        itemCount++;
                        let isChecked = (assignedSupplier === selectedSupplier) ? 'checked' : '';
                        let rowClass = isChecked ? 'selected' : '';

                        itemsHTML += `
                    <div class="item-row ${rowClass}" data-item-id="${itemId}">
                        <label style="display: flex; align-items: center; cursor: pointer; user-select: none;">
                            <input type="checkbox" 
                                   class="item-checkbox" 
                                   data-item-id="${itemId}" 
                                   data-article-no="${articleNo}"
                                   ${isChecked}>
                            <span style="margin-left: 12px;"><strong>${articleNo}</strong> - ${itemDesc}</span>
                        </label>
                    </div>
                `;
                    }
                }
            });
            if (itemsHTML === '') {
                itemsHTML = '<p class="text-center text-muted">No available items for this supplier</p>';
            }

            $('#bulkItemsList').html(itemsHTML);
            $('#itemSelectionModal').data('current-supplier', selectedSupplier);
            // Hide bulk supplier modal and show item selection
            $('#bulkSupplierModal').modal('hide');
            setTimeout(function() {
                $('#itemSelectionModal').modal('show');
            }, 400);
        });

        // Handle checkbox change to update visual selection
        $(document).off('change', '.item-checkbox').on('change', '.item-checkbox', function() {
            let $row = $(this).closest('.item-row');
            if ($(this).prop('checked')) {
                $row.addClass('selected');
            } else {
                $row.removeClass('selected');
            }
        });

        // Save bulk selection
        $(document).off('click', '#saveBulkSelectionBtn').on('click', '#saveBulkSelectionBtn', function() {
            let selectedSupplier = $('#itemSelectionModal').data('current-supplier');
            let selectedItems = [];

            // Get all checked items
            $('#bulkItemsList .item-checkbox:checked').each(function() {
                let itemId = $(this).data('item-id');

                if (itemId && itemId !== 'undefined') {
                    selectedItems.push(itemId);
                    // Store in global tracking object
                    bulkSupplierSelections[itemId] = selectedSupplier;
                }
            });
            // Remove items that were unchecked for this supplier
            $('#bulkItemsList .item-checkbox:not(:checked)').each(function() {
                let itemId = $(this).data('item-id');
                if (itemId && bulkSupplierSelections[itemId] === selectedSupplier) {
                    delete bulkSupplierSelections[itemId];
                }
            });
            // Update the BOM table with selections
            let updatedCount = 0;
            $('#bomTableBody tr').each(function(index) {
                let $row = $(this);
                let itemId = $row.data('item-id') || $row.attr('data-item-id');
                let articleNo = $row.find('td:eq(2)').text().trim();
                let itemDesc = $row.find('td:eq(1)').text().trim();

                // Use same fallback logic
                if (!itemId || itemId === 'undefined') {
                    if (articleNo && articleNo !== '-' && articleNo !== '') {
                        itemId = 'article_' + articleNo;
                    } else {
                        // Use row index for items with "-"
                        itemId = 'row_' + index + '_' + itemDesc.substring(0, 10).replace(/\s/g, '_');
                    }
                }

                if (!itemId || itemId === 'undefined') return true;
                if (bulkSupplierSelections[itemId]) {
                    let supplier = bulkSupplierSelections[itemId];
                    let $supplierSelect = $row.find('.supplier-select');
                    // Update the value
                    if ($supplierSelect.hasClass('select2-hidden-accessible')) {
                        $supplierSelect.val(supplier).trigger('change');
                    } else {
                        $supplierSelect.val(supplier);
                    }

                    // Add visual indicator
                    $supplierSelect.closest('td').addClass('selected');
                    updatedCount++;
                }
            });

            // Close item selection modal
            $('#itemSelectionModal').modal('hide');

            // Show success message and return to supplier list
            setTimeout(function() {
                alert(`${selectedItems.length} item(s) assigned to ${selectedSupplier}`);
                showBulkSupplierList();
            }, 400);
        });

        // When clicking close/cancel on item selection modal
        $('#itemSelectionModal').on('click', '.close, .btn-secondary', function() {
            $('#itemSelectionModal').modal('hide');
            setTimeout(function() {
                showBulkSupplierList();
            }, 400);
        });

        // Reset bulk selections when BOM modal is closed
        $('#bomModal').on('hidden.bs.modal', function() {
            bulkSupplierSelections = {};
        });

        // Search functionality for suppliers - Simple contains search
        $(document).off('input', '#supplierSearchInput').on('input', '#supplierSearchInput', function() {
            let searchTerm = $(this).val().toLowerCase().trim();

            if (searchTerm === '') {
                $('.supplier-item').removeClass('hidden').show();
                $('#noResultsMessage').remove();
                return;
            }
            let visibleCount = 0;

            $('.supplier-item').each(function() {
                let supplierName = $(this).data('supplier').toLowerCase();

                // Check if supplier name contains the search term anywhere
                if (supplierName.includes(searchTerm)) {
                    $(this).removeClass('hidden').show();
                    visibleCount++;
                } else {
                    $(this).addClass('hidden').hide();
                }
            });

            $('#noResultsMessage').remove();

            if (visibleCount === 0) {
                $('#supplierListContainer').append(
                    '<p id="noResultsMessage" class="text-center text-muted mt-3">No suppliers found containing "' + searchTerm + '"</p>'
                );
            }
        });

        // Clear search when modal is opened
        $('#bulkSupplierModal').on('shown.bs.modal', function() {
            $('#supplierSearchInput').val('').focus();
            $('.supplier-item').removeClass('hidden').show();
            $('#noResultsMessage').remove();
        });

        // Clear search when modal is closed
        $('#bulkSupplierModal').on('hidden.bs.modal', function() {
            $('#supplierSearchInput').val('');
            $('.supplier-item').removeClass('hidden').show();
            $('#noResultsMessage').remove();
        });
    </script>

@endsection