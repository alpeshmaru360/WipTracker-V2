@extends('layouts.main')
@section('content')
<link href="{{ asset('css/production_manager.css') }}" rel="stylesheet" />
<link href="{{ asset('css/product_superwisor.css') }}" rel="stylesheet" />

<style type="text/css">
.switch-group label {line-height: 10px !important;}
.plus-sm-btn {padding: 4px 10px !important;}
.dataTables_wrapper {width: 100%;}
.select_container.selected::after {color: #ffffff !important;content: "✓";}

/*.modal-open .modal{
    z-index: 9999;
}
.swal2-container.swal2-center.swal2-backdrop-show{
    z-index: 99999;   
}*/
</style>

<link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap-switch-button@1.1.0/css/bootstrap-switch-button.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap-switch-button@1.1.0/dist/bootstrap-switch-button.min.js"></script>
<!-- Bootstrap Bundle (includes Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="main_section bg-white m-4">
    <div class="d-flex justify-content-between align-items-center">
        <h3 class="ml-4 pt-4 text-bold text-left text-uppercase">Pending BOM Check</h3>
        <a class="mr-4 mt-4 mb-2 btn btn-primary btn-sm py-2 px-3" onclick="location.reload();">
            <i class="fa fa-refresh text-light">&#xf021;</i>
        </a>
    </div>

    <hr class="mx-4 mt-2 mb-4" />
    <div class="container">
        <div class="row mt-3">

            @if(count($bom_check) > 0)

            <table class="table table-hover table-border w-100 text-center" id="procument_role_pending_task">
                <thead>
                    <tr>
                        <th scope="col" class="project_table_heading">Request Date</th>
                        <th scope="col" class="project_table_heading">Deadline</th>
                        <th scope="col" class="project_table_heading">Project No.</th>
                        <th scope="col" class="project_table_heading">Project Name</th>
                        <th scope="col" class="project_table_heading">Required Action</th>
                        <th scope="col" class="project_table_heading">Article No.</th>
                        <th scope="col" class="project_table_heading">Product Description</th>
                        <th scope="col" class="project_table_heading">QTY</th>
                        <th scope="col" class="project_table_heading">Check BOM</th>
                        <!-- <th  scope="col" class="project_table_heading">Status</th>-->
                        <th scope="col" class="project_table_heading">Remarks</th>
                        <!-- <th scope="col" class="project_table_heading">Action</th> -->
                    </tr>
                </thead>
                <tbody>

                    @foreach($bom_check as $val)

                    <tr>

                        <td>{{ $val->created_at->format('d-m-y H:i') }}</td>
                        <td class="{{ $val->deadline && \Carbon\Carbon::now()->greaterThan($val->deadline) ? 'text-danger' : 'text-success' }}">
                            {{ $val->deadline ? $val->deadline->format('d-m-y H:i') : 'N/A' }}
                        </td>
                        <td>{{$val->projects['project_no']}}</td>
                        <td>{{$val->projects['project_name']}}</td>
                        <td></td>
                        <td>{{$val->full_article_number}}</td>
                        <td>{{ $val->description}}</td>
                        <td>{{$val->qty}}</td>
                        <td>
                            <!-- <span class="project_check_status">
                                <a href="{{ asset($val->bom_path) }}" class="project_check_status text-decoration-none primary_bg_color text-white p-2 fs-12" download>
                                <i class="fa fa-check-circle"></i> &nbsp; BOM</a>
                            </span>
                            <br><br> -->
                            <button class="btn btn-info check-bom-btn btn-primary py-1 px-2"
                                data-id="{{ $val->id }}"
                                data-toggle="modal"
                                data-target="#bomModal">
                                <i class="fa fa-eye"></i>
                            </button>
                        </td>
                        <!-- <td>

                            <input type="checkbox" class="procurement-check" data-id="{{ $val->id }}" data-type="bom" data-toggle="switchbutton" data-onstyle="primary" data-width="100" data-height="20" data-onlabel="Checked" data-offlabel="Pending" data-onstyle="success" data-offstyle="danger">

                        </td> -->

                        <td>
                            <button class="btn btn-primary add-remark-btn py-1 px-2"
                                data-id="{{ $val->id }}"
                                data-toggle="modal"
                                data-target="#remarkModal">
                                <i class="fa fa-plus"></i>
                            </button>
                        </td>

                        <!-- <td><button class="btn btn-primary py-2 px-3" style="white-space: nowrap;">Place Order</button></td> -->

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

    <div class="d-flex justify-content-between align-items-center">
        <h3 class="ml-4 pt-4 text-bold text-left text-uppercase">BOM Items - Place PO</h3>
        <a class="mr-4 mt-4 mb-2 btn btn-primary btn-sm py-2 px-3" onclick="location.reload();">
            <i class="fa fa-refresh text-light">&#xf021;</i>
        </a>
    </div>

    <hr class="mx-4 mt-2 mb-4" />
    <div class="container">
        <div class="row mt-3">
            @if(count($pending_po_orders) > 0)
            <table class="table table-hover table-border w-100 text-center" id="pending_po_stock_comparison">
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
                        <!-- <th scope="col" class="project_table_heading">Action</th> -->
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
                        <!-- <td>
                            <a href="{{ route('addPO', $bom->id) }}" class="btn btn-primary btn-sm ml-2 py-1">
                                Place Order
                            </a>
                        </td> -->
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

    <div class="d-flex justify-content-between align-items-center">
        <h3 class="ml-4 pt-4 text-bold text-left text-uppercase">Rejected Purchase Orders</h3>
        <a class="mr-4 mt-4 mb-2 btn btn-primary btn-sm py-2 px-3" onclick="location.reload();">
            <i class="fa fa-refresh text-light">&#xf021;</i>
        </a>
    </div>

    <hr class="mx-4 mt-2 mb-4" />
    <div class="container">
        <div class="row mt-3">
            @if($RejectedPurchaseOrders->isNotEmpty())
            <table class="table table-hover table-border w-100 text-center" id="rejected_purchase_order">
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



    <div class="d-flex justify-content-between align-items-center">

        <h3 class="ml-4 pt-4 text-bold text-left text-uppercase">Pending BOM Upload from Estimation Manager Side</h3>

        <a class="mr-4 mt-4 mb-2 btn btn-primary btn-sm py-2 px-3" onclick="location.reload();">

            <i class="fa fa-refresh text-light">&#xf021;</i>

        </a>

    </div>



    <hr class="mx-4 mt-2 mb-4" />



    <div class="container">

        <div class="row mt-3">

            @if(count($pending_bom) > 0)

            <table class="table table-hover table-border w-100 text-center" id="pending_bom_upload">
                <thead>
                    <tr>
                        <!-- <th scope="col" class="project_table_heading">Request Date</th>
                        <th scope="col" class="project_table_heading">Deadline</th> -->
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
                        <!-- <td>{{ $val->created_at->format('d-m-y') }}</td>
                        <td>{{ $val->projects['estimated_readiness'] ? \Carbon\Carbon::parse($val->projects['estimated_readiness'])->format('d-m-y') : 'N/A' }}</td> -->

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



    <!-- <div class="d-flex justify-content-between align-items-center">

        <h3 class="ml-4 pt-4 text-bold text-left text-uppercase">Pending Drawing Check</h3>

        <a class="mr-4 mt-4 mb-2 btn btn-primary btn-sm py-2 px-3" onclick="location.reload();">

            <i class="fa fa-refresh text-light">&#xf021;</i>

        </a>

    </div> -->

    <!-- <hr class="mx-4 mt-2 mb-4" /> -->
    <!-- <div class="container"> -->
    <!-- <div class="row mt-3"> -->
    <!-- @if(count($drawing_check) > 0) -->
    <!-- <table class="table table-hover table-border w-100 text-center" id="procument_role_pending_drawing_check"> -->
    <!-- <thead> -->
    <!-- <tr> -->
    <!-- <th scope="col" class="project_table_heading">Request Date</th>
                        <th scope="col" class="project_table_heading">Deadline</th> -->
    <!-- <th scope="col" class="project_table_heading">Project No.</th> -->
    <!-- <th scope="col" class="project_table_heading">Project Name</th> -->
    <!-- <th scope="col" class="project_table_heading">Required Action</th> -->
    <!-- <th scope="col" class="project_table_heading">Article No.</th> -->
    <!-- <th scope="col" class="project_table_heading">Product Description</th> -->
    <!-- <th scope="col" class="project_table_heading">QTY</th> -->
    <!-- <th scope="col" class="project_table_heading" style="width:17%">Check Drawing</th> -->
    <!-- <th scope="col" class="project_table_heading">Status</th> -->
    <!-- <th scope="col" class="project_table_heading">Remarks</th> -->
    <!-- </tr> -->
    <!-- </thead> -->
    <!-- <tbody> -->
    <!-- @foreach($drawing_check as $val) -->
    <!-- <tr> -->
    <!-- <td>{{ $val->created_at->format('d-m-y') }}</td>
                        <td>{{ $val->projects['estimated_readiness'] ? \Carbon\Carbon::parse($val->projects['estimated_readiness'])->format('d-m-y') : 'N/A' }}</td> -->
    <!-- <td>{{$val->projects['project_no']}}</td> -->
    <!-- <td>{{$val->projects['project_name']}}</td> -->
    <!-- <td></td> -->
    <!-- <td>{{$val->full_article_number}}</td> -->
    <!-- <td>{{ $val->description}}</td> -->
    <!-- <td>{{$val->qty}}</td> -->
    <!-- <td>
                            <span class="project_check_status">
                                <a href="{{ asset($val->drawing_path) }}" class="btn btn-primary text-decoration-none project_check_status primary_bg_color text-white p-2 fs-12" download>
                                    <i class="fa fa-check-circle"></i> &nbsp; Drawing</a>
                            </span>
                        </td> -->
    <!-- <td>
                            <input type="checkbox" class="procurement-check" data-id="{{ $val->id }}" data-type="drawing" data-toggle="switchbutton" data-onstyle="primary" data-width="100" data-height="20" data-onlabel="Checked" data-offlabel="Pending" data-onstyle="success" data-offstyle="danger">
                        </td> -->
    <!-- <td>
                            <button class="btn btn-primary add-remark-btn plus-sm-btn"
                                data-id="{{ $val->id }}"
                                data-toggle="modal"
                                data-target="#drawingRemarkModal">
                                <i class="fa fa-plus"></i>
                            </button>
                        </td> -->
    <!-- </tr> -->
    <!-- @endforeach -->
    <!-- </tbody> -->
    <!-- </table> -->
    <!-- @else -->
    <!-- <div class="alert alert-info w-100">
                No Pending Drawing Check found.
            </div> -->
    <!-- @endif -->
    <!-- </div> -->
    <!-- </div> -->

    <div class="d-flex justify-content-between align-items-center">

        <h3 class="ml-4 pt-4 text-bold text-left text-uppercase">Minimum Low Stock Alert</h3>

        <a class="mr-4 mt-4 mb-2 btn btn-primary btn-sm py-2 px-3" onclick="location.reload();">

            <i class="fa fa-refresh text-light">&#xf021;</i>

        </a>

    </div>



    <hr class="mx-4 mt-2 mb-4" />



    <div class="container">

        <div class="row mt-3">

            @if(count($minimumLowStock) > 0)



            <table class="table table-hover table-border w-100 text-center" id="stock_available_table">
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
                            <a href="{{ route('addPOFromStock', $val->id) }}" class="btn btn-primary btn-sm ml-2 place-order-btn py-1">
                                Place Order
                            </a>
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
                <div class="modal-body">
                    <table class="table table-bordered">
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
                    <button type="button" class="btn btn-primary" id="saveBOMSelection">Process Order</button>
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


    {{--

<h3 class="ml-4 pt-4 text-bold text-left text-uppercase">Submitted Latest Task</h3>

<hr class="mx-4" />

<div class="container">

    <div class="row mt-3"> 

        <table class="table table-hover table-border w-100 text-center" id = "procument_role_pending_task">

            <thead>

              <tr>

                <th  scope="col" class="project_table_heading">Request Date</th>

                <th  scope="col" class="project_table_heading">Deadline</th>

                <th  scope="col" class="project_table_heading">Project No.</th>

                <th  scope="col" class="project_table_heading">Project Name</th>

                <th  scope="col" class="project_table_heading">Required Action</th>

                <th  scope="col" class="project_table_heading">Article No.</th>

                <th  scope="col" class="project_table_heading">Product Description</th>

                <th  scope="col" class="project_table_heading">QTY</th>

                <th  scope="col" class="project_table_heading">BOM/Drawing</th>

                <th  scope="col" class="project_table_heading">Status</th>

              </tr>

            </thead>

            <tbody>

                @foreach($bom_checked as $val)

                    <tr>

                        <td>{{ $val->created_at->format('d-m-y') }}</td>

    <td>{{ $val->projects['estimated_readiness'] ? \Carbon\Carbon::parse($val->projects['estimated_readiness'])->format('d-m-y') : 'N/A' }}</td>

    <td>{{$val->projects['project_no']}}</td>

    <td>{{$val->projects['project_name']}}</td>

    <td></td>

    <td>{{$val->full_article_number}}</td>

    <td>{{ $val->description}}</td>

    <td>{{$val->qty}}</td>

    <td>

        <span class="project_check_status">

            <a href="{{ asset($val->bom_path) }}" class="project_check_status text-decoration-none primary_bg_color text-white p-2 fs-12" download>

                <i class="fa fa-check-circle"></i> &nbsp; BOM</a>

        </span>

    </td>

    <td>

        <input type="checkbox" class="" data-id="{{ $val->id }}" data-type="bom" data-toggle="switchbutton" data-onstyle="primary" data-width="100" data-height="20" data-onlabel="Checked" checked data-onstyle="success" disabled>

    </td>

    </tr>

    @endforeach

    @foreach($drawing_checked as $val)

    <tr>

        <td>{{ $val->created_at->format('d-m-y') }}</td>

        <td>{{ $val->projects['estimated_readiness'] ? \Carbon\Carbon::parse($val->projects['estimated_readiness'])->format('d-m-y') : 'N/A' }}</td>

        <td>{{$val->projects['project_no']}}</td>

        <td>{{$val->projects['project_name']}}</td>

        <td></td>

        <td>{{$val->full_article_number}}</td>

        <td>{{ $val->description}}</td>

        <td>{{$val->qty}}</td>

        <td>

            <span class="project_check_status">

                <a href="{{ asset($val->drawing_path) }}" class="text-decoration-none project_check_status primary_bg_color text-white p-2 fs-12" download>

                    <i class="fa fa-check-circle"></i> &nbsp; Drawing</a>

            </span>

        </td>

        <td>

            <input type="checkbox" class="" data-id="{{ $val->id }}" data-type="drawing" data-toggle="switchbutton" data-onstyle="primary" data-width="100" data-height="20" data-onlabel="Checked" checked data-onstyle="success" disabled>

        </td>

    </tr>

    @endforeach

    </tbody>

    </table>

</div>

</div>

--}}

@endsection



@section('scripts')

<script>
    $(document).ready(function() {

        $('#bomTableBody').empty();

        // Load data from AJAX, then:
        $('#procument_role_pending_task').DataTable().destroy();
        $('#procument_role_pending_task').DataTable({
            paging: true,
            pageLength: 2,
            lengthMenu: [2, 5, 10, 25, 50, 100],
            searching: true,
            ordering: false
        });
        $('#pending_po_stock_comparison').DataTable({
            paging: true,
            pageLength: 2,
            lengthMenu: [2, 5, 10, 25, 50, 100],
            searching: true,
            ordering: false
        });
        $('#rejected_purchase_order').DataTable({
            paging: true,
            pageLength: 2,
            lengthMenu: [2, 5, 10, 25, 50, 100],
            searching: true,
            ordering: false
        });
        $('#pending_bom_upload').DataTable({
            paging: true,
            pageLength: 2,
            lengthMenu: [2, 5, 10, 25, 50, 100],
            searching: true,
            ordering: false
        });
        $('#procument_role_pending_drawing_check').DataTable({
            paging: true,
            pageLength: 2,
            lengthMenu: [2, 5, 10, 25, 50, 100],
            searching: true,
            ordering: false
        });
        $('#stock_available_table').DataTable({
            paging: true,
            pageLength: 2,
            lengthMenu: [2, 5, 10, 25, 50, 100],
            searching: true,
            ordering: false
        });

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
</script>

<script>
    let globalSuppliers = [];

    $(document).on('click', '.check-bom-btn', function() {
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

                let renderedBOMSuppliers = new Set(); // Track already added suppliers

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
                                let projectId = response.projectId;
                                let productId = response.productId;
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

                                    // Mark supplier as handled
                                    renderedBOMSuppliers.add(formattedSupplier);
                                }
                            }
                        } else {
                            let partialQtyInput = `<input type="hidden" class="partial-qty-input" value="${selectedPartialQty}">`;
                            optionCell = `
                                <td>
                                    <select class="form-control select-option">
                                        <option value="">Select Option</option>
                                        <option value="stock" ${selectedOption === 'stock' ? 'selected' : ''} ${stockOption}>From Stock</option>
                                        <option value="new_order" ${(selectedOption === 'new_order' || (!selectedOption && !isStockAvailable)) ? 'selected' : ''} ${newOrderOption}>New Order</option>
                                        <option value="partial" ${selectedOption === 'partial' ? 'selected' : ''} ${partialOption}>Partial</option>
                                    </select>
                                    ${partialQtyInput}
                                </td>
                            `;
                            supplierCell = `
                                <td>
                                    <select class="form-control supplier-select" ${selectedOption === 'stock' ? 'disabled' : ''}>
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

                    $('#saveBOMSelection').toggle(!response.isSaved);
                    if (response.isSaved) {
                        $(".po_added_checkmark").removeClass('d-none');
                    } else {
                        $(".po_added_checkmark").addClass('d-none');
                    }
                }
            },
            error: function() {
                alert('Failed to fetch BOM data. Please try again.');
            }
        });
    });

    $(document).on('change', '.select-option', function() {
        let $row = $(this).closest('tr');
        let selected = $(this).val();
        let supplierSelect = $row.find('.supplier-select');
        let stockQty = parseInt($row.data('stock'));
        let totalQty = parseInt($row.data('total'));
        let partialQtyInput = $row.find('.partial-qty-input');

        if (selected === 'stock') {
            supplierSelect.html('<option value="N/A" selected>N/A</option>');
            supplierSelect.prop('disabled', true);
            partialQtyInput.val(0);
        } else {
            let options = '<option value="">Select Supplier</option>';
            $.each(globalSuppliers, function(i, supplier) {
                options += `<option value="${supplier}">${supplier}</option>`;
            });
            supplierSelect.html(options);
            supplierSelect.prop('disabled', false);
        }

        if (selected === 'partial') {
            let previousSelected = $row.data('last-selected') || '';
            $row.data('last-selected', 'partial');

            if (stockQty >= totalQty) {
                // Case 1: Stock >= Required → Show Modal
                $('#partialModalStockQty').text(stockQty);
                $('#partialModalTotalQty').text(totalQty);
                $('#partialQtyInputField').val('');
                $('#partialQtyModal').data('row', $row).modal('show');
            } else {
                // Case 2: Stock < Required → Show Popup
                let newOrderQty = totalQty - stockQty;
                partialQtyInput.val(stockQty);
                Swal.fire({
                    title: 'Partial Qty Breakdown',
                    html: `
                        <p><strong>From Stock Qty:</strong> ${stockQty}</p>
                        <p><strong>New Order Qty:</strong> ${newOrderQty}</p>
                    `,
                    confirmButtonText: 'OK'
                });
            }
        } else {
            $row.data('last-selected', selected);
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

        // CASE 1: If total needed is more than 1
        if (totalQty > 1) {
            if (enteredQty >= totalQty) {
                alert(`Entered quantity cannot be equal to or more than total required (${totalQty}).`);
                return;
            }
        }

        // CASE 2: If total needed is 1
        if (totalQty === 1) {
            if (enteredQty > totalQty) {
                alert(`Entered quantity cannot be more than total required (${totalQty}).`);
                return;
            }
        }

        // Common check for stock
        if (enteredQty > stockQty) {
            alert(`Entered quantity cannot be more than available stock (${stockQty}).`);
            return;
        }

        // All validations passed
        $row.find('.partial-qty-input').val(enteredQty);
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
            bomData.push({
                article_no: row.find('td:eq(2)').text().trim(),
                item_desc: row.find('td:eq(1)').text().trim(),
                item_qty: row.find('td:eq(3)').text().trim(),
                product_qty: row.find('td:eq(4)').text().trim(),
                total_required_qty: row.find('td:eq(5)').text().trim(),
                stock_qty: row.find('td:eq(6)').text().trim(),
                option: row.find('.select-option').val(),
                supplier: row.find('.supplier-select').val(),
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

        if (selectedSupplier && selectedSupplier !== 'N/A') {
            $(this).addClass('supplier-selected');
        } else {
            $(this).removeClass('supplier-selected');
        }

        var container = $(this).closest('.select_container');
        if ($(this).val() !== '') {
            container.addClass('selected');
        } else {
            container.removeClass('selected');
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
</script>

<script>
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
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        document.querySelectorAll(".view-reasons-btn").forEach(button => {

            button.addEventListener("click", function() {

                let managerReason = this.getAttribute("data-manager-reason") || 'N/A';

                let engineerReason = this.getAttribute("data-engineer-reason") || 'N/A';



                // Set the textarea values, ensuring null/empty is replaced with 'N/A'

                document.getElementById("managerReason").value = managerReason.trim() ? managerReason : 'N/A';

                document.getElementById("engineerReason").value = engineerReason.trim() ? engineerReason : 'N/A';



                // Manually show the modal

                //let rejectionReasonModal = new bootstrap.Modal(document.getElementById('rejectionReasonModal'));

                //rejectionReasonModal.show();

            });

        });

    });
</script>

<script>
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
</script>

@endsection