@extends('layouts.main')
@section('content')
<link rel="stylesheet" href="{{ asset('css/product_superwisor.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap-switch-button@1.1.0/css/bootstrap-switch-button.min.css">
<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap-switch-button@1.1.0/dist/bootstrap-switch-button.min.js"></script>

<div class="superwisor_inbox_page main_section bg-white m-4 pb-5">    

    <div class="d-flex justify-content-between align-items-center px-5 pt-3">
        <h3 class="pt-4 text-bold text-left text-uppercase">Orders</h3>
        <a class="mt-4 mb-2 btn btn-primary btn-sm py-2 px-3" onclick="location.reload();">
            <i class="fa fa-refresh text-light">&#xf021;</i>
        </a>
    </div>
    <hr class="mx-5 mt-2 mb-2" /> 

    <div class="container-fluid px-5 mx-3">
        <div class="row mt-3 table-responsive">
            @if(count($all_projects_full) > 0)
            <table class="table table-hover table-border w-100 text-center" id="project_full_orders_table">
                <thead>
                    <tr>
                        <th scope="col" class="project_table_heading">Project No.</th>
                        <th scope="col" class="project_table_heading">Project Name</th>
                        <th scope="col" class="project_table_heading">Article No.</th>
                        <th scope="col" class="project_table_heading">Product Description</th>
                        <th scope="col" class="project_table_heading">QTY</th>
                        <th scope="col" class="project_table_heading">Assigned QTY</th>
                        <th scope="col" class="project_table_heading">Assign QTY</th>
                        <th scope="col" class="project_table_heading">View operator</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($all_projects_full as $val)
                    @php
                    $limitation_per_shift = DB::table('product_types')
                    ->where('project_type_name', $val->product_type)
                    ->value('limitation_per_shift');

                    $totalAssignedQty = DB::table('assigned_products_operators')
                    ->where('project_id', $val->project_id)
                    ->where('product_id', $val->id)
                    ->where('created_at', '>=', \Carbon\Carbon::now()->subDay())
                    ->sum('assigned_qty');

                    $assign_limit = $limitation_per_shift - $totalAssignedQty;
                    @endphp
                    <tr>
                        <td>{{$val->projects['project_no']}}</td>
                        <td>{{$val->projects['project_name']}}</td>
                        <td>{{$val->full_article_number}}</td>
                        <td>{{$val->description}}</td>
                        <td>{{$val->qty}}</td>
                        <td>{{$val->assigned_qty}}</td>

                        <td>
                            @if($val->superwisor_status == "0")
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                    id="dropdownMenuButton{{ $val->id }}"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Select
                                </button>

                                <div class="dropdown-menu p-3 multi_oparators_dd" aria-labelledby="dropdownMenuButton{{ $val->id }}">

                                    <form id="assignForm{{ $val->id }}">
                                        @foreach($operators as $operator)
                                        <div class="form-check">
                                            <input class="form-check-input operator-checkbox"
                                                type="checkbox"
                                                name="operators[]"
                                                value="{{ $operator->id }}"
                                                id="operatorCheck{{ $val->id }}_{{ $operator->id }}">

                                            <label class="form-check-label pointer_cursor"
                                                for="operatorCheck{{ $val->id }}_{{ $operator->id }}">
                                                {{ $operator->name }}
                                            </label>
                                        </div>
                                        @endforeach

                                        <button type="button"
                                            class="btn btn-sm btn-success mt-3 w-100"
                                            onclick="assignMultipleOperators(
                                                            '{{ $val->id }}',
                                                            '{{ $val->project_id }}',
                                                            '{{ addslashes($val->projects['project_name']) }}',
                                                            '{{ $val->qty }}',
                                                            '{{ $val->assigned_qty }}',
                                                            '{{ $val->product_type }}',
                                                            '{{ $limitation_per_shift }}',
                                                            '{{ $assign_limit }}'
                                                        )">
                                            Assign Selected
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @else
                            <div class="text-center mt-2">
                                <span class="project_check_status">
                                    <a class="btn btn-primary primary_bg_color text-white p-1">Assigned</a>
                                </span>
                            </div>
                            @endif
                        </td>
                        <td>
                            <button type="button" class="project_check_status primary_bg_color text-white p-2 show_operator_list" data-toggle="modal" data-target="#operatorShowModal" data-project_id="{{$val->project_id}}" data-product_id="{{$val->id}}">
                                <i class="fa fa-eye primary_bg_color"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="alert alert-info w-100">
                No Orders found.
            </div>
            @endif
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center px-5 pt-3">
        <h3 class="pt-4 text-bold text-left text-uppercase">Partial Orders</h3>
        <a class="mt-4 mb-2 btn btn-primary btn-sm py-2 px-3" onclick="location.reload();">
            <i class="fa fa-refresh text-light">&#xf021;</i>
        </a>
    </div>
    <hr class="mx-5 mt-2 mb-2" /> 

    <div class="container-fluid px-5 mx-3">
        <div class="row mt-3 table-responsive">
            @if(count($all_projects_partials) > 0)
            <table class="table table-hover table-bordered w-100 text-center" id="project_partial_orders_table">
                <thead>
                    <tr>
                        <th scope="col" class="project_table_heading">Project No.</th>
                        <th scope="col" class="project_table_heading">Project Name</th>
                        <th scope="col" class="project_table_heading">Article No.</th>
                        <th scope="col" class="project_table_heading">Product Description</th>
                        <th scope="col" class="project_table_heading">QTY</th>
                        <th scope="col" class="project_table_heading">Assigned QTY</th>
                        <th scope="col" class="project_table_heading">Assign QTY</th>
                        <th scope="col" class="project_table_heading">View operator</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($all_projects_partials as $val)

                    @php
                    $limitation_per_shift = DB::table('product_types')
                    ->where('project_type_name', $val->product_type)
                    ->value('limitation_per_shift');

                    $totalAssignedQty = DB::table('assigned_products_operators')
                    ->where('project_id', $val->project_id)
                    ->where('product_id', $val->id)
                    ->where('created_at', '>=', \Carbon\Carbon::now()->subDay())
                    ->sum('assigned_qty');

                    $assign_limit = $limitation_per_shift - $totalAssignedQty;
                    @endphp
                    <tr>
                        <td>{{$val->projects['project_no']}}</td>
                        <td>{{$val->projects['project_name']}}</td>
                        <td>{{$val->full_article_number}}</td>
                        <td>{{ $val->description}}</td>
                        <td>{{$val->qty}}</td>
                        <td>{{ $val->assigned_qty}}</td>
                        <td>
                            @if($val->superwisor_status == "0")
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                    id="dropdownMenuButton{{ $val->id }}"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Select
                                </button>
                                <div class="dropdown-menu p-3"
                                    aria-labelledby="dropdownMenuButton{{ $val->id }}">

                                    <form id="assignForm{{ $val->id }}">
                                        @foreach($operators as $operator)
                                        <div class="form-check">
                                            <input class="form-check-input operator-checkbox"
                                                type="checkbox"
                                                name="operators[]"
                                                value="{{ $operator->id }}"
                                                id="operatorCheck{{ $val->id }}_{{ $operator->id }}">

                                            <label class="form-check-label"
                                                for="operatorCheck{{ $val->id }}_{{ $operator->id }}">
                                                {{ $operator->name }}
                                            </label>
                                        </div>
                                        @endforeach

                                        <button type="button"
                                            class="btn btn-sm btn-success mt-3 w-100"
                                            onclick="assignMultipleOperators(
                                                            '{{ $val->id }}',
                                                            '{{ $val->project_id }}',
                                                            '{{ addslashes($val->projects['project_name']) }}',
                                                            '{{ $val->qty }}',
                                                            '{{ $val->assigned_qty }}',
                                                            '{{ $val->product_type }}',
                                                            '{{ $limitation_per_shift }}',
                                                            '{{ $assign_limit }}'
                                                        )">
                                            Assign Selected
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @else
                            <div class="text-center mt-2">
                                <span class="project_check_status">
                                    <a class="btn btn-primary primary_bg_color text-white p-1">Assigned</a>
                                </span>
                            </div>
                            @endif
                        </td>
                        <td>
                            <button type="button" class="show_operator_list project_check_status primary_bg_color text-white p-2" data-toggle="modal" data-target="#operatorShowModal" data-project_id="{{$val->project_id}}" data-product_id="{{$val->id}}">
                                <i class="fa fa-eye primary_bg_color"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="alert alert-info w-100">
                No Partial Orders found.
            </div>
            @endif
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center px-5 pt-3">
        <h3 class="pt-4 text-bold text-left text-uppercase">Pending As-Built Drawing PDF Approval</h3>
        <a class="mt-4 mb-2 btn btn-primary btn-sm py-2 px-3" onclick="location.reload();">
            <i class="fa fa-refresh text-light">&#xf021;</i>
        </a>
    </div>
    <hr class="mx-5 mt-2 mb-2" /> 

    <div class="container-fluid px-5 mx-3">
        <div class="row mt-3 table-responsive">
            @if($pdf_req->isNotEmpty())
            <table class="table table-hover table-bordered w-100 text-center" id="as_built_pdf">
                <thead>
                    <tr>
                        <th scope="col" class="project_table_heading">Project No.</th>
                        <th scope="col" class="project_table_heading">Project Name</th>
                        <th scope="col" class="project_table_heading">Article No.</th>
                        <th scope="col" class="project_table_heading">Product Description</th>
                        <th scope="col" class="project_table_heading">Assigned Qty</th>
                        <th scope="col" class="project_table_heading">Basic PDF</th>
                        <th scope="col" class="project_table_heading">As-Built PDF</th>
                        <th scope="col" class="project_table_heading" style="width: 15%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pdf_req as $val)
                    <tr>
                        <td>{{ $val->projects->project_no ?? 'N/A' }}</td>
                        <td>{{ $val->projects->project_name ?? 'N/A' }}</td>
                        <td>{{ $val->article_number ?? 'N/A' }}</td>
                        <td>{{ $val->description?? 'N/A' }}</td>
                        <td>{{ $val->qty ?? 0 }}</td>
                        <td>
                            @if(!empty($val->drawing_path))
                            <a href="{{ asset($val->drawing_path) }}"
                                class="btn btn-sm m-0 px-3 py-1 dwld_pdf_btn"
                                download
                                title="Download Basic PDF">
                                <i class="fa fa-download"></i>
                            </a>
                            @else
                            <span class="text-muted">No PDF</span>
                            @endif
                        </td>
                        <td>
                            @if(!empty($val->editable_drawing_path))
                            <a href="{{ asset($val->editable_drawing_path) }}"
                                class="btn btn-sm m-0 px-3 py-1 dwld_pdf_btn"
                                download
                                title="Download Edited PDF">
                                <i class="fa fa-download"></i>
                            </a>
                            @else
                            <span class="text-muted">No PDF</span>
                            @endif
                        </td>
                        <td class="action">
                            <button class="btn btn-success btn-sm m-0 px-3 py-1" data-bs-toggle="modal" data-bs-target="#approveModal" data-id="{{ $val->id }}">
                                <i class="fa fa-check"></i>
                            </button>
                            <button class="btn btn-danger btn-sm m-0 px-3 py-1" data-bs-toggle="modal" data-bs-target="#rejectModal" data-id="{{ $val->id }}">
                                <i class="fa fa-times"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="alert alert-info w-100">
                No Pending AS Built PDF Approvals Found.
            </div>
            @endif
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center px-5 pt-3">
        <h3 class="pt-4 text-bold text-left text-uppercase">Completed process of assembly</h3>
        <a class="mt-4 mb-2 btn btn-primary btn-sm py-2 px-3" onclick="location.reload();">
            <i class="fa fa-refresh text-light">&#xf021;</i>
        </a>
    </div>
    <hr class="mx-5 mt-2 mb-2" /> 

    <div class="container-fluid px-5 mx-3">
        <div class="row mt-3 table-responsive">
            @if(count($completed_process_of_assembly_products_qty_wise) > 0)
            <table class="table table-hover table-bordered w-100 text-center" id="completed_process">
                <thead>
                    <tr>
                        <th scope="col" class="project_table_heading">Project No.</th>
                        <th scope="col" class="project_table_heading">Project Name</th>
                        <th scope="col" class="project_table_heading">Article No.</th>
                        <th scope="col" class="project_table_heading">Product Description</th>
                        <th scope="col" class="project_table_heading">Total QTY</th>
                        <th scope="col" class="project_table_heading">Unit No</th>
                        <th scope="col" class="project_table_heading" style="width:15%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($completed_process_of_assembly_products_qty_wise as $val)
                    <tr>
                        <td>{{$val->projects['project_no']}}</td>
                        <td>{{$val->projects['project_name']}}</td>
                        <td>{{$val['products']->full_article_number}}</td>
                        <td>{{ $val['products']->description}}</td>
                        <td>{{$val['products']->qty}} </td>
                        <td>{{$val->qty_number}} of {{$val['products']->qty}}</td>
                        <td>                           
                            <button type="button" class="btn btn-primary process-confirm" data-id="{{ $val->id }}" title="Final Inspection">Confirm</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="alert alert-info w-100">
                No Pending Task found.
            </div>
            @endif
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center px-5 pt-3">
        <h3 class="pt-4 text-bold text-left text-uppercase">Upload PL For Full Order</h3>
        <a class="mt-4 mb-2 btn btn-primary btn-sm py-2 px-3" onclick="location.reload();">
            <i class="fa fa-refresh text-light">&#xf021;</i>
        </a>
    </div>
    <hr class="mx-5 mt-2 mb-2" /> 

    <div class="container-fluid px-5 mx-3">
        <div class="row mt-3 table-responsive">
            @if(count($upload_pl_req) > 0)
            <table class="table table-hover table-bordered w-100 text-center" id="upload_pl_table">
                <thead>
                    <tr>
                        <th scope="col" class="project_table_heading">Project No.</th>
                        <th scope="col" class="project_table_heading">Project Name</th>
                        <th scope="col" class="project_table_heading">View Products [Full Order]</th>
                        <th scope="col" class="project_table_heading">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($upload_pl_req as $val)
                    @php
                    $qty_records = DB::table('qty_of_products')
                    ->join('products_of_projects', 'qty_of_products.product_id', '=', 'products_of_projects.id')
                    ->select(
                    'products_of_projects.full_article_number',
                    'products_of_projects.description',
                    'products_of_projects.qty',
                    'qty_of_products.qty_number',
                    'qty_of_products.is_final_inspection_started',
                    'qty_of_products.product_id',
                    'products_of_projects.id',
                    'qty_of_products.project_id',
                    'products_of_projects.delivery'
                    )
                    ->where('qty_of_products.project_id', $val->project_id)
                    ->where('products_of_projects.delivery', '!=', '2')
                    ->get();

                    $upload_btn_enable = true;
                    if ($qty_records->isEmpty()) {
                    $upload_btn_enable = false;
                    } else {
                    // Check if ALL records have is_final_inspection_started == 2
                    foreach ($qty_records as $qty) {
                    if ($qty->is_final_inspection_started != 2) {
                    $upload_btn_enable = false;
                    break; // No need to check further if any record fails
                    }
                    }
                    }
                    @endphp
                    <tr>
                        <td>{{$val->projects['project_no']}}</td>
                        <td>{{$val->projects['project_name']}}</td>
                        <td>
                            <button type="button" class="project_check_status primary_bg_color text-white p-2 view_products" data-toggle="modal" data-target="#viewProductsModal" data-project_id="{{$val->project_id}}">
                                <i class="fa fa-eye primary_bg_color"></i>
                            </button>
                        </td>
                        <td>                           
                            <span class="project_check_status bg-light">
                                @if($upload_btn_enable)
                                <button type="button" class="btn btn-primary primary_bg_color text-white py-1 px-2 upload-pl-btn">
                                    Upload PL
                                </button>
                                @else
                                <button type="button" class="btn btn-primary primary_bg_color text-white py-1 px-2 upload-pl-btn" disabled>
                                    Upload PL
                                </button>
                                @endif
                                <br>
                                <input type="file" name="reports_docs"
                                    class="d-none upload-pl-input"
                                    data-id="{{$val->projects['id']}}"
                                    data-product_id="{{$val->id}}"
                                    data-lable="pl" accept=".pdf, .xls, .xlsx">
                            </span>
                            <div id="file-upload-success"></div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="alert alert-info w-100">
                No Pending Task found.
            </div>
            @endif
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center px-5 pt-3">
        <h3 class="pt-4 text-bold text-left text-uppercase">Upload PL For Partial Order</h3>
        <a class="mt-4 mb-2 btn btn-primary btn-sm py-2 px-3" onclick="location.reload();">
            <i class="fa fa-refresh text-light">&#xf021;</i>
        </a>
    </div>
    <hr class="mx-5 mt-2 mb-2" /> 

    <div class="container-fluid px-5 mx-3">
        <div class="row mt-3 table-responsive">
            @if(count($upload_pl_partial_dilivery_req) > 0)
            <table class="table table-hover table-bordered w-100 text-center" id="upload_pl_partial_dilivery_table">
                <thead>
                    <tr>
                        <th scope="col" class="project_table_heading">Project No.</th>
                        <th scope="col" class="project_table_heading">Project Name</th>
                        <th scope="col" class="project_table_heading">Product Article No</th>
                        <th scope="col" class="project_table_heading">Product Description</th>
                        <th scope="col" class="project_table_heading">Unit (Qty)</th>
                        <th scope="col" class="project_table_heading">Final Inspection Status</th>
                        <th scope="col" class="project_table_heading">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($upload_pl_partial_dilivery_req as $val)
                    <tr data-id="{{ $val->id }}">
                        <td>{{ $val->project_no }}</td>
                        <td>{{ $val->project_name }}</td>
                        <td>{{ $val->full_article_number ?? 'N/A' }}</td>
                        <td>{{ $val->description ?? '' }}</td>
                        <td>
                            {{ $val->qty > 0 ? $val->qty_number . '/' . $val->qty : 'N/A' }}
                        </td>
                        <td>
                            @php
                            $statusValue = $val->is_final_inspection_started ?? 0;
                            $statusLabel = '';
                            $badgeClass = '';
                            switch ($statusValue) {
                            case 1:
                            $statusLabel = 'In Process';
                            $badgeClass = 'badge bg-warning';
                            break;
                            case 2:
                            $statusLabel = 'Completed';
                            $badgeClass = 'badge bg-success';
                            break;
                            default:
                            $statusLabel = 'Not Started';
                            $badgeClass = 'badge bg-danger';
                            }
                            @endphp
                            <span class="{{ $badgeClass }} p-2">{{ $statusLabel }}</span>
                        </td>
                        <td>
                            <span class="project_check_status bg-light">

                                <button type="button" class="btn btn-primary primary_bg_color text-white py-1 px-2 upload-partial-pl-btn">
                                    Upload PL
                                </button>
                                <br>
                                <input type="file" name="reports_docs"
                                    class="d-none upload-pl-input" data-id="{{ $val->id }}"
                                    data-lable="pl" accept=".pdf, .xls, .xlsx">
                            </span>
                            <div id="file-upload-success-{{ $val->id }}" class="file-upload-success"></div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="alert alert-info w-100">
                No Pending Task found.
            </div>
            @endif
        </div>
    </div>

    <div class="modal fade" id="operatorShowModal" tabindex="-1" aria-labelledby="operatorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="operatorModalLabel">Quantity Assigned List</h5>
                </div>
                <div class="modal-body p-0">
                    <table class="table table-hover table-bordered text-center mb-0" id="projectDetailsTable">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="project_table_heading">Qty</th>
                                <th scope="col" class="project_table_heading">Operator</th>
                                <th scope="col" class="project_table_heading">E-mail</th>
                            </tr>
                        </thead>
                        <tbody><!-- rows inserted dynamically here --></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals remain unchanged -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white" id="rejectModalLabel">Reject Request</h5>
                </div>
                <div class="modal-body">
                    <textarea class="form-control" rows="3" id="rejectReason" placeholder="Enter reason for rejection..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary cancel-black" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmReject">Reject</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white" id="approveModalLabel">Approve Request</h5>
                </div>
                <div class="modal-body">
                    <textarea class="form-control" rows="3" id="approveRemarks" placeholder="Enter remarks (optional)..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary cancel-black" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirmApprove">Approve</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Products Modal -->
    <div class="modal fade stockModal" id="viewProductsModal" tabindex="-1" aria-labelledby="view_productsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header text-white edit-model-header">
                    <h5 class="modal-title" id="view_productsModalLabel">Project Detail</h5>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered" id="viewProductsTable" border="1">
                        <thead>
                            <tr>
                                <th style="width:15%;">Product Article No</th>
                                <th>Product Description</th>
                                <th style="width:15%;">Unit (Qty)</th>
                                <th style="width:15%;">Final Inspection Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade stockModal" id="viewProductsModalpartial" tabindex="-1" aria-labelledby="view_productsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header text-white edit-model-header">
                    <h5 class="modal-title" id="view_productsModalLabel">Project Detail</h5>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered" id="viewProductsTable" border="1">
                        <thead>
                            <tr>
                                <th style="width:15%;">Product Article No</th>
                                <th>Product Description</th>
                                <th style="width:15%;">Unit (Qty)</th>
                                <th style="width:15%;">Final Inspection Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        $('#project_full_orders_table').DataTable({
            paging: true,
            pageLength: 2,
            lengthMenu: [2, 5, 10, 25, 50, 100],
            searching: true,
            ordering: false,
            info: false
        });
        $('#project_full_orders_table').removeClass('dataTable');

        $('#project_partial_orders_table').DataTable({
            paging: true,
            pageLength: 2,
            lengthMenu: [2, 5, 10, 25, 50, 100],
            searching: true,
            ordering: false,
            info: false
        });
        $('#project_partial_orders_table').removeClass('dataTable');

        $('#as_built_pdf').DataTable({
            paging: true,
            pageLength: 2,
            lengthMenu: [2, 5, 10, 25, 50, 100],
            searching: true,
            ordering: false,
            info: false
        });
        $('#as_built_pdf').removeClass('dataTable');

        $('#completed_process').DataTable({
            paging: true,
            pageLength: 2,
            lengthMenu: [2, 5, 10, 25, 50, 100],
            searching: true,
            ordering: false,
            info: false
        });
        $('#completed_process').removeClass('dataTable');

        $('#upload_pl_table').DataTable({
            paging: true,
            pageLength: 2,
            lengthMenu: [2, 5, 10, 25, 50, 100],
            searching: true,
            ordering: false,
            info: false
        });
        $('#upload_pl_table').removeClass('dataTable');

        $('#upload_pl_partial_dilivery_table').DataTable({
            paging: true,
            pageLength: 2,
            lengthMenu: [2, 5, 10, 25, 50, 100],
            searching: true,
            ordering: false,
            info: false
        });
        $('#upload_pl_partial_dilivery_table').removeClass('dataTable');

        $('#completed_projects').DataTable({
            paging: true,
            pageLength: 2,
            lengthMenu: [2, 5, 10, 25, 50, 100],
            searching: true,
            ordering: false,
            info: false
        });
        $('#completed_projects').removeClass('dataTable');

    function assignTask(productId, projectId, projectName, operator, operator_id, totalQty, assignedQty) {
        const remainingQty = totalQty - assignedQty;
        let quantity = prompt('Enter the quantity you want to assign to ' + operator + ':');
        if (quantity === null) {
            return;
        }
        if (isNaN(quantity) || quantity <= 0 || quantity > remainingQty) {
            alert('Please enter a valid quantity (maximum: ' + remainingQty + ').');
            return;
        }
        if (confirm('Do you want to assign ' + quantity + ' units of the project named ' + projectName + ' to ' + operator + '?')) {
            $.ajax({
                url: "{{ route('superwisorAssignTaskToOperator') }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    project_id: projectId,
                    operator_id: operator_id,
                    productId: productId,
                    quantity: quantity
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        setTimeout(function() {
                            window.location.href = response.redirect_url;
                        }, 2000);
                    }
                },
                error: function(err) {
                    console.error('Error:', err);
                    toastr.error('Error assigning task. Please try again.');
                }
            });
        }
    }

    $(document).on('click', '.show_operator_list', function() {
        const csrfToken = "{{ csrf_token() }}";
        const projectId = $(this).data('project_id');
        const productId = $(this).data('product_id');

        $.ajax({
            url: "{{ route('showOperatorList') }}",
            type: 'POST',
            data: {
                _token: csrfToken,
                project_id: projectId,
                product_id: productId
            },
            success: function(response) {
                const $tbody = $('#projectDetailsTable tbody').empty(); // clear previous rows

                if (Array.isArray(response) && response.length) {
                    response.forEach((row) => {
                        let operatorNames = [];
                        let operatorEmails = [];

                        if (Array.isArray(row.operators) && row.operators.length) {
                            row.operators.forEach((operator) => {
                                operatorNames.push(operator.name);
                                operatorEmails.push(operator.email);
                            });

                            $tbody.append(`
                                <tr>
                                    <td>${row.seq_qty}</td>
                                    <td>${operatorNames.join(', ')}</td>
                                    <td>${operatorEmails.join(', ')}</td>
                                </tr>
                            `);
                        } else {
                            $tbody.append(`
                                <tr>
                                    <td>${row.seq_qty}</td>
                                    <td colspan="2" class="text-muted">No operators assigned</td>
                                </tr>
                            `);
                        }
                    });
                } else {
                    $tbody.append(`
                        <tr>
                            <td colspan="3" class="text-center">No data available</td>
                        </tr>
                    `);
                }
            },
            error: function() {
                alert('Error loading project details.');
            }
        });
    });

    $(document).on('click', '.upload-pl-btn, .upload-partial-pl-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const $input = $(this).closest('.project_check_status').find('.upload-pl-input');
        if ($input.length) {
            $input.trigger('click');
        }
    });

    $(document).on('change', '.upload-pl-input', function() {
        const csrfToken = "{{ csrf_token() }}";
        const file = this.files[0];
        const id = $(this).data('id');
        const label = $(this).data('lable');
        const $button = $(this).closest('.project_check_status').find('.upload-pl-btn, .upload-partial-pl-btn');
        const originalText = $button.text();
        const $successDiv = $(this).closest('.project_check_status').next('.file-upload-success');
        const $row = $(this).closest('tr');
        const product_id = $(this).data('product_id');
        if (file) {
            let formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('file', file);
            formData.append('id', id);
            formData.append('lable', label);
            formData.append('product_id', product_id);
            $button.text('Uploading...').prop('disabled', true);
            // Determine if this is a partial order upload
            const isPartialOrder = $(this).closest('table').attr('id') === 'upload_pl_partial_dilivery_table';
            const url = isPartialOrder ? "{{ route('ProductionSuperwisorUploadPartialPlDoc') }}" : "{{ route('ProductionSuperwisorUploadPlDoc') }}";
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    console.log(response);
                    if (response.success) {
                        $successDiv.html('<span class="text-success">File uploaded successfully!</span>');
                        // Remove the row from the table
                        $row.fadeOut(500, function() {
                            $(this).remove();
                            // Check if table is empty
                            if ($('#upload_pl_partial_dilivery_table tbody tr').length === 0) {
                                $('#upload_pl_partial_dilivery_table').replaceWith('<div class="alert alert-info w-100">No Pending Task found.</div>');
                            }
                        });
                        // Optional: Reload page after a delay as a fallback
                        setTimeout(() => location.reload(), 2000);
                    } else {
                        $successDiv.html('<span class="text-danger">Error: ' + (response.message || 'Unknown error') + '</span>');
                    }
                },

                error: function(error) {
                    let errorMessage = 'Error uploading file.';
                    if (error.status === 422) {
                        var errors = error.responseJSON.errors;
                        var errorMessages = '';
                        for (var field in errors) {
                            errorMessages += errors[field].join('<br>');
                        }
                        errorMessage = 'Validation errors:<br>' + errorMessages;
                    } else if (error.responseJSON && error.responseJSON.message) {
                        errorMessage = error.responseJSON.message;
                    }
                    $successDiv.html('<span class="text-danger">' + errorMessage + '</span>');
                    $button.text(originalText).prop('disabled', false);
                },
                complete: function() {
                    $button.text(originalText).prop('disabled', false);
                }
            });
        } else {
            $successDiv.html('<span class="text-danger">No file selected.</span>');
            $button.text(originalText).prop('disabled', false);
        }
    });
  
    document.addEventListener('DOMContentLoaded', function() {
        const buttons = document.querySelectorAll('.process-confirm');
        buttons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const csrfToken = "{{ csrf_token() }}";
                fetch("{{ route('ConfirmAssemblyProcess') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            id: id
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Assembled Process confirmed successfully!');
                            location.reload();
                        } else {
                            alert('An error occurred: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                    });
            });
        });
    });

    // View Products
    $(document).on('click', '.view_products', function() {
        const csrfToken = "{{ csrf_token() }}";
        var projectId = $(this).data('project_id');
        $.ajax({

            url: "{{route('viewProjectDetails')}}",
            method: 'POST',
            data: {
                _token: csrfToken,
                project_id: projectId
            },
            success: function(response) {
                console.log(response);
                if (response.length > 0) {
                    $('#viewProductsTable tbody').empty();
                    response.forEach(row => {
                        console.log(row);
                        let full_article_number = row.full_article_number || 'N/A';
                        let description = row.description || '';
                        let total_qty = row.qty || 0;
                        let qty_number = row.qty_number || 0;
                        let statusValue = row.is_final_inspection_started || 0;
                        // Format Unit (Qty) as fraction (e.g., 1/3)
                        let unit_qty = total_qty > 0 ? `${qty_number}/${total_qty}` : 'N/A';
                        let statusLabel = '';
                        let badgeClass = '';


                        switch (Number(statusValue)) {
                            case 1:
                                statusLabel = 'In Process';
                                badgeClass = 'badge bg-warning';
                                break;
                            case 2:
                                statusLabel = 'Completed';
                                badgeClass = 'badge bg-success';
                                break;
                            default:
                                statusLabel = 'Not Started';
                                badgeClass = 'badge bg-danger';
                        }
                        console.log(statusValue, statusLabel);
                        let rowHtml = `
                        <tr>
                            <td>${full_article_number}</td>
                            <td>${description}</td>
                            <td class="text-center">${unit_qty}</td>
                            <td class="text-center"><span class="${badgeClass} p-2">${statusLabel}</span></td>
                            
                        </tr>`;
                        $('#viewProductsTable tbody').append(rowHtml);
                    });
                } else {
                    $('#viewProductsTable tbody').html(`
                    <tr>
                        <td colspan="4" class="text-center">No data available</td>
                    </tr>
                `);
                }
            },
            error: function() {
                alert('Error loading project details.');
            }
        });
    });
</script>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        const confirmButtons = document.querySelectorAll('.confirm-btn');
        confirmButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Check if button is disabled
                if (this.disabled) {
                    alert('Please upload an image first before confirming.');
                    return;
                }

                const id = this.dataset.id;
                const type = this.dataset.type;
                const csrfToken = "{{ csrf_token() }}";

                fetch("{{ route('UpdateProductionCheckStatus') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            id: id,
                            type: type,
                            checked: 2 // Assuming 2 means "confirmed"
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Final inspection started successfully!');
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

    let selectedProductId = null;
    $('#approveModal').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget);
        selectedProductId = button.data('id');
    });
    $('#confirmApprove').on('click', function() {
        const remarks = $('#approveRemarks').val();
        $.ajax({
            url: "{{ route('approve.pdf') }}",
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                product_id: selectedProductId,
                remarks: remarks
            },
            success: function(response) {
                if (response.success) {
                    $(`button[data-id="${selectedProductId}"]`).closest('tr').remove();
                    $('#approveModal').modal('hide');
                    alert(response.message);
                    location.reload();
                }
            },
            error: function(err) {
                console.error(err);
                alert('Something went wrong!');
            }
        });
    });
    $('#rejectModal').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget);
        selectedProductId = button.data('id');
    });
    $('#confirmReject').on('click', function() {
        const reason = $('#rejectReason').val();
        $.ajax({
            url: "{{ route('reject.pdf') }}",
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                product_id: selectedProductId,
                reason: reason
            },
            success: function(response) {
                if (response.success) {
                    $(`button[data-id="${selectedProductId}"]`).closest('tr').remove();
                    $('#rejectModal').modal('hide');
                    alert(response.message);
                    location.reload();
                }
            },
            error: function(err) {
                console.error(err);
                alert('Something went wrong!');
            }
        });
    });
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })

    function assignMultipleOperators(stepId, projectId, projectName, totalQty, assignedQty, productType, limitPerShift, assignLimit) {
        var remainingQty = totalQty - assignedQty;
        const form = $('#assignForm' + stepId);
        const selectedOperators = [];

        // Collect selected operator IDs and names
        form.find('input[name="operators[]"]:checked').each(function() {
            const operatorId = $(this).val();
            const operatorName = $(this).next('label').text().trim();
            selectedOperators.push({
                id: operatorId,
                name: operatorName
            });
        });

        // Validate selection
        if (selectedOperators.length === 0) {
            alert("Please select at least one operator.");
            return;
        }

        // Prompt user for quantity to assign to EACH operator
        let quantity = prompt("Enter quantity to assign to EACH selected operator:");
        if (quantity === null) return; // Cancelled

        quantity = parseInt(quantity);
        if (isNaN(quantity) || quantity <= 0) {
            alert("Invalid quantity entered.");
            return;
        }

        const totalRequiredQty = quantity;
        const remainingQtyInt = parseInt(remainingQty);
        const limitPerShiftInt = parseInt(limitPerShift);

        // Prevent over-assignment limitPerShift
        if (remainingQtyInt == 0) {
            alert(`The total production capacity for the ${productType} product type is ${limitPerShiftInt} units per day, which has already been assigned.`)
            return;
        }

        // Prevent over-assignment
        if (totalRequiredQty > remainingQtyInt) {
            alert(`You can assign a max of ${remainingQtyInt} total units.`);
            return;
        }

        // Confirm before proceeding
        if (!confirm(`Are you sure you want to assign ${quantity} units to EACH of the ${selectedOperators.length} selected operators?`)) {
            return;
        }

        // Send AJAX request to server
        $.ajax({
            url: "{{ route('superwisorAssignTaskToOperator') }}",
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                project_id: projectId,
                productId: stepId,
                operator_ids: selectedOperators.map(op => op.id),
                quantity: quantity
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    setTimeout(() => {
                        window.location.href = response.redirect_url;
                    }, 2000);
                } else {
                    toastr.error(response.message || 'Something went wrong.');
                }
            },
            error: function(err) {
                console.error('Error:', err);
                if (err.responseJSON && err.responseJSON.message) {
                    toastr.error(err.responseJSON.message);
                } else {
                    toastr.error('Error assigning task. Please try again.');
                }
            }
        });
    }

    $(document).ready(function() {
        // Initialize DataTables for the TWO new tables with same settings as other tables
        $('#pending_project_inspected_table').DataTable({
            paging: true,
            pageLength: 2,
            lengthMenu: [2, 5, 10, 25, 50, 100],
            searching: true,
            ordering: false,
            info: false
        });
        $('#pending_project_inspected_table').removeClass('dataTable');

        $('#pending_project_all_items_are_from_stock').DataTable({
            paging: true,
            pageLength: 2,
            lengthMenu: [2, 5, 10, 25, 50, 100],
            searching: true,
            ordering: false,
            info: false
        });
        $('#pending_project_all_items_are_from_stock').removeClass('dataTable');
        
        $('#pending_project_not_inspected_table').DataTable({
            paging: true,
            pageLength: 2,
            lengthMenu: [2, 5, 10, 25, 50, 100],
            searching: true,
            ordering: false,
            info: false
        });
        $('#pending_project_not_inspected_table').removeClass('dataTable');


        
    });
</script>
@endsection