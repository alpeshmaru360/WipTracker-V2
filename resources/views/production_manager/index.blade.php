@extends('layouts.main')
@section('content')
<link rel="stylesheet" href="{{ asset('css/production_manager.css') }}" />
<script src="https://cdn.datatables.net/responsive/3.0.3/js/dataTables.responsive.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

@php
    $is_superwisor_login = Auth::user()->role === 'Production Superwisor';
    $is_quality_login = Auth::user()->role === 'Quality Engineer';
@endphp 
<div class="production_manager_index_page main_section bg-white m-4 pb-5 py-3 project_bg">
    <div class="container-fluid px-5 mx-3">
        <div class="justify-content-end row mx-1 px-1">            
            <div class="btn-group mt-4 mx-3">
                <select class="project_status_dd ml-2" name="status">
                    <option class="All" value="3">Project status</option>
                    <option class="Open" value="0">Open</option>
                    <option class="work_in_process" value="1">Work In Progress</option>
                    <option class="completed" value="2">completed</option>
                </select>
            </div>  
            
            <div class="btn-group mt-4">
                <div class="mb-4 ml-2 d-flex justify-content-end align-items-center">

                    <button type="button"
                            class="btn btn-primary mr-2"
                            onclick="download_csv();">
                        <i class="fas fa-download"></i> Export
                    </button>

                    <a href="{{ route('ProductionManagerProjectIndex') }}"
                    class="btn btn-primary mr-2">
                        <i class="fas fa-sync-alt"></i> Reset
                    </a>

                    @if(Auth::user()->role == "Production Engineer")
                        <a href="{{ route('ProductionManagerProjectCreate', ['project_name'=>'wip_tracker']) }}"
                        class="btn btn-primary">
                            <i class="fa fa-plus"></i> Add New
                        </a>
                    @endif

                </div>
            </div>
            
        </div>

        <!-- A Code: 21-01-2026 Start -->
        @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mt-3 mb-4 w-100" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mt-3 mb-4 w-100" role="alert">
            {{ session('error') }}
        </div>
        @endif
        @if (session('success'))
        <div class="row alert alert-success alert-dismissible fade show mt-3 mb-4 w-100" role="alert">
            {{ session('success') }}
        </div>
        @endif
        <!-- A Code: 21-01-2026 End -->

        <div class="row mt-3  table-responsive">
            <form action="{{ route('ProductionManagerProjectIndexFilter') }}" method="POST" id="filter_form">
                @csrf                
                <table class="table table-bordered table-hover align-middle text-center" id="project_table">
                    <thead id="project_table_head">
                        @include('production_manager.project_head', ['project' => $project])
                    </thead>
                    <tbody id="project_table_body">
                        @include('production_manager.project_rows', ['project' => $project])
                    </tbody>
                </table>
                <input type="hidden" name="last_filter_column" id="last_filter_column">
            </form>  
        </div>
    </div>

    <div class="modal fade text-bold" id="documentsModal" tabindex="-1" role="dialog"
        aria-labelledby="documentsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">

            <div class="modal-content">
                <div class="modal-header">
                    <a href="#" class="modal_show_back project_icon d-inline-flex py-1 pr-1 my-1 mr-1 text-decoration-none">
                        <i class="fa fa-arrow-left mx-2 project_icon"></i>
                    </a>
                    <h5 class="modal-title text-white d-inline-flex" id="documentsModalLabel">Project Documents</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"></span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul id="foldersList" class="list-group">
                        <li class="list-group-item folder" data-folder="PO and Invoices"> 
                            <i class="fa fa-folder folder-icon mr-1"></i> PO and Invoices
                            <ul class="subfolder-po_and_invoices mt-2" style="display: none;">
                                <li class="list-group-item subfolder-item" data-folder="BOE">BOE</li>
                                <li class="list-group-item subfolder-item" data-folder="INVOICE">INVOICE</li>
                                <li class="list-group-item subfolder-item" data-folder="OA">OA</li>
                                <li class="list-group-item subfolder-item" data-folder="PO">PO</li>
                            </ul>
                        </li>
                        <li class="list-group-item folder" data-folder="Project Data"> 
                            <i class="fa fa-folder folder-icon mr-1"></i> Project Data
                            <ul class="subfolder-project_data mt-2" style="display: none;">
                                <li class="list-group-item subfolder-item" data-folder="BOM">BOM</li>
                                <li class="list-group-item subfolder-item" data-folder="Drawings">Drawings</li>
                                <li class="list-group-item subfolder-item" data-folder="From Customers">From Customers</li>
                            </ul>
                        </li>
                        <li class="list-group-item folder" data-folder="Project Execution"> 
                            <i class="fa fa-folder folder-icon mr-1"></i> Project Execution

                            <ul class="subfolder-project_execution mt-2" style="display: none;">
                                <li class="list-group-item subfolder-item" data-folder="Images">Images</li>
                                <li class="list-group-item subfolder-item" data-folder="Full_Order_PL">Full Order PL</li>
                                <li class="list-group-item subfolder-item" data-folder="Partial Order PL">Partial Order PL</li>
                                <li class="list-group-item subfolder-item" data-folder="Work Orders">Work Orders</li>
                            </ul>
                        </li>
                        <li class="list-group-item folder" data-folder="Quality"> 
                            <i class="fa fa-folder folder-icon mr-1"></i> Quality
                            <ul class="subfolder-quality mt-2" style="display: none;">
                                <li class="list-group-item subfolder-item" data-folder="Final Inspection">Final Inspection doc</li>
                                <li class="list-group-item subfolder-item" data-folder="Images">Final Inspection Images</li>
                                <li class="list-group-item subfolder-item" data-folder="Test Reports">Test Reports</li>
                            </ul>
                        </li>
                        <li class="list-group-item folder" data-folder="WIP Photos"> 
                            <i class="fa fa-folder folder-icon mr-1"></i> WIP Photos</li>
                        <li class="list-group-item folder" data-folder="QR Codes">
                            <i class="fa fa-folder folder-icon mr-1"></i> QR Codes</li>
                    </ul>
                    <!-- New Subsubfolders List Container for Drawings -->
                    <ul id="subsubfoldersList" class="list-group" style="display: none;">
                        <!-- A Code: 19-01-2026 Start -->
                        <li class="list-group-item d-flex align-items-center justify-content-between subsubfolder-item" data-subsubfolder="Estimation Manager Upload Drawing">
                            Design Engineer Upload Drawing
                            <button class="btn btn-sm btn-primary px-2 view-subsubfolder" data-subsubfolder="Estimation Manager Upload Drawing">
                                <i class="fa fa-eye"></i>
                            </button>
                        </li>
                        <!-- A Code: 19-01-2026 End -->
                        <li class="list-group-item d-flex align-items-center justify-content-between subsubfolder-item" data-subsubfolder="Operators As-Built Drawing">
                            Operators As-Built Drawing
                            <button class="btn btn-sm btn-primary px-2 view-subsubfolder" data-subsubfolder="Operators As-Built Drawing">
                                <i class="fa fa-eye"></i>
                            </button>
                        </li>
                        <!-- A Code: 19-01-2026 Start -->
                        <li class="list-group-item d-flex align-items-center justify-content-between subsubfolder-item" data-subsubfolder="Estimation Manager Final Drawing">
                            Design Engineer Final Drawing
                            <button class="btn btn-sm btn-primary px-2 view-subsubfolder" data-subsubfolder="Estimation Manager Final Drawing">
                                <i class="fa fa-eye"></i>
                            </button>
                        </li>
                        <!-- A Code: 19-01-2026 End -->
                    </ul>
                    
                    <ul id="documentsList" class="list-group" style="display: none;"></ul>
                    <ul id="BOMList" class="list-group" style="display: none;"></ul>
                    <ul id="DrawingsList" class="list-group" style="display: none;"></ul>
                    <ul id="WIPPhotosList" class="list-group" style="display: none;"></ul>
                    <div id="executionImagesListContainer" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="executionImagesListTable">
                                <thead>
                                    <tr>
                                        <th>Cart Model</th>
                                        <th>Item Description</th>
                                        <th>Article Number</th>
                                        <th>Qty</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div id="executionImagesQtyListContainer" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="executionImagesQtyListTable">
                                <thead>
                                    <tr>
                                        <th>Qty No</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div id="namePlateListContainer" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="namePlateListTable">
                                <thead>
                                    <tr>
                                        <th>Cart Model</th>
                                        <th>Item Description</th>
                                        <th>Article Number</th>
                                        <th>Qty</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div id="namePlateQtyListContainer" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="namePlateQtyListTable">
                                <thead>
                                    <tr>
                                        <th>Qty No</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div id="namePlateImagesContainer" style="display: none;">
                        <div class="table-responsive position-relative">
                            <div class="path-display mb-2" style="font-size: 14px;color: #555;display: flex;align-items: center;">
                                <span id="namePlatePathDisplay"></span>
                            </div>
                            <table class="table table-bordered" id="namePlateImagesTable">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            <div id="noNamePlateImagesMessage" class="text-center text-muted" style="display: none;">No images found</div>
                        </div>
                    </div>
                    <div id="executionImagesContainer" style="display: none;">
                        <div class="table-responsive position-relative">
                            <div class="path-display mb-2" style="font-size: 14px;color: #555;display: flex;align-items: center;">
                                <span id="imagePathDisplay"></span>
                                @if($is_quality_login)
                                <button class="btn btn-primary add-execution-image d-flex" style="margin-left:auto; margin-bottom: 10px;">Add Image</button>
                                @endif
                            </div>
                            <input type="file" id="hiddenExecutionImageInput" accept="image/*" style="display: none;" multiple>
                            <table class="table table-bordered" id="executionImagesTable">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        @if($is_quality_login)
                                        <th>Action</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div id="executionWorkOrdersListContainer" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="executionWorkOrdersListTable">
                                <thead>
                                    <tr>
                                        <th>Cart Model</th>
                                        <th>Item Description</th>
                                        <th>Article Number</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>                    
                    <div id="executionDocsContainer" style="display: none;"> 
                        <!-- A Code: 13-01-2026 Start -->

                        <div class="breadcrumb-wrap mt-0 pt-0 px-0 d-flex justify-content-between align-items-center">
                            <div class="d-flex gap-3 align-items-center">                              
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mb-0">
                                        <li class="breadcrumb-item">
                                            <a href="#" class="modal_show_back" title="Project Execution">
                                                <i class="fa fa-folder-open mr-1"></i> Project Execution
                                            </a>
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page">Full Order PL</li>
                                    </ol>
                                </nav>                               
                            </div>
                            <!-- Right: path display + Add button (only for supervisor) -->                                                
                            <div class="breadcrumb-controls d-flex align-items-center">                                
                                <div class="path-display d-flex align-items-center"><span id="docPathDisplay"></span></div>
                                @if($is_superwisor_login)
                                <button class="btn btn-primary add-execution-doc d-flex">
                                    Add Document
                                </button>
                                @endif
                            </div>
                        </div>

                        <!-- A Code: 13-01-2026 End -->
                        <div class="table-responsive position-relative">                             
                            <input type="file" id="hiddenExecutionDocInput" accept=".pdf,.doc,.docx,.xls,.xlsx" style="display: none;">
                            <table class="table table-bordered" id="executionDocsTable">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        @if($is_superwisor_login)
                                        <th>Action</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            <div id="noDocsMessage" class="text-center text-muted" style="display: none;">No documents found</div>
                        </div>
                    </div>

                    <div id="executionPartialOrderPLListContainer" style="display: none;">
                        <div class="breadcrumb-wrap mt-0 pt-0 px-0 d-flex justify-content-between align-items-center">
                            <!-- A Code: 13-01-2026 Start -->
                            <div class="d-flex gap-3 align-items-center">                              
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mb-0">
                                        <li class="breadcrumb-item">
                                            <a href="#" class="modal_show_back" title="Project Execution">
                                                <i class="fa fa-folder-open mr-1"></i> Project Execution
                                            </a>
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page">Partial Order PL</li>
                                    </ol>
                                </nav>                               
                            </div>
                            <!-- A Code: 13-01-2026 End -->
                            <div class="table-responsive position-relative">  
                                <table class="table table-bordered" id="executionPartialOrderPLListTable">
                                    <thead>
                                        <tr>
                                            <th>Cart Model</th>
                                            <th>Item Description</th>
                                            <th>Article Number</th>
                                            <th>Qty</th>                                         
                                            @if($is_superwisor_login)
                                            <th>Action</th>
                                            @endif                                           
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div id="executionPartialOrderPLQtyListContainer" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="executionPartialOrderPLQtyListTable">
                                <thead>
                                    <tr>
                                        <th>Qty No</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div id="executionPartialOrderPLDocsContainer" style="display: none;">                        
                        <div class="table-responsive position-relative">
                            <div class="path-display mb-2" style="font-size: 14px;color: #555;display: flex;align-items: center;">
                                <span id="partialOrderPLPathDisplay"></span>
                            </div>
                            <input type="file" id="hiddenPartialOrderPLDocInput" accept=".pdf,.doc,.docx,.xls,.xlsx" style="display: none;">
                            <table class="table table-bordered" id="executionPartialOrderPLDocsTable">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            <div id="noPartialOrderPLDocsMessage" class="text-center text-muted" style="display: none;">No documents found</div>
                        </div>
                    </div>
                    <div id="qualityFinalInspectionListContainer" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="qualityFinalInspectionListTable">
                                <thead>
                                    <tr>
                                        <th>Cart Model</th>
                                        <th>Item Description</th>
                                        <th>Article Number</th>
                                        <th>Qty</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div id="qualityFinalInspectionQtyListContainer" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="qualityFinalInspectionQtyListTable">
                                <thead>
                                    <tr>
                                        <th>Qty No</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div id="qualityTestReportsListContainer" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="qualityTestReportsListTable">
                                <thead>
                                    <tr>
                                        <th>Cart Model</th>
                                        <th>Item Description</th>
                                        <th>Article Number</th>
                                        <th>Qty</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div id="qualityTestReportsQtyListContainer" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="qualityTestReportsQtyListTable">
                                <thead>
                                    <tr>
                                        <th>Qty No</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div id="qualityDocsContainer" style="display: none;">
                        <div class="table-responsive position-relative">
                            <div class="path-display mb-2" style="font-size: 14px;color: #555;display: flex;align-items: center;">
                                <span id="qualityPathDisplay"></span>
                                @if($is_quality_login)
                                <button class="btn btn-primary add-quality-doc d-flex" style="margin-left:auto; margin-bottom: 10px;">Add Document</button>
                                @endif
                            </div>
                            <input type="file" id="hiddenQualityDocInput" accept=".pdf,.doc,.docx,.xls,.xlsx" style="display: none;">
                            <table class="table table-bordered" id="qualityDocsTable">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        @if($is_quality_login)
                                        <th>Action</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            <div id="noQualityDocsMessage" class="text-center text-muted" style="display: none;">No documents found</div>
                        </div>
                    </div>
                    <div id="qrCodesListContainer" style="display: none;">
                        <div class="path-display mb-2" style="font-size: 14px;color: #555;display: flex;align-items: center;">
                            <span id="qrCodeListPathDisplay"></span>
                            <button class="btn btn-primary download-all-project-qr-codes d-flex" style="margin-left:auto; margin-bottom: 10px;">
                                Download All QR Codes for project no: <span id="projectNoDisplay">Project</span>
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="qrCodesListTable">
                                <thead>
                                    <tr>
                                        <th>Cart Model</th>
                                        <th>Item Description</th>
                                        <th>Article Number</th>
                                        <th>Qty</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div id="qrCodeImagesContainer" style="display: none;">
                        <div class="table-responsive position-relative">
                            <div class="path-display mb-2" style="font-size: 14px;color: #555;display: flex;align-items: center;">
                                <span id="qrCodePathDisplay"></span>
                                <button class="btn btn-primary download-all-qr-codes d-flex" style="margin-left:auto; margin-bottom: 10px;">Download All</button>
                            </div>
                            <table class="table table-bordered" id="qrCodeImagesTable">
                                <thead>
                                    <tr>
                                        <th>QR</th>
                                        <th>Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody class="Qr_item"></tbody>
                            </table>
                            <div id="noQrCodesMessage" class="text-center text-muted" style="display: none;">No QR codes found</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary-integration" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- A Code: 10-01-2026 Start -->
    <div class="modal fade" id="deleteProjectReasonModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title text-white">Delete Project</h5>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="deleteProjectId">

                    <div class="mb-3">
                        <label class="form-label">
                            Reason for Delete <span class="text-danger">*</span>
                        </label>
                        <textarea
                            id="deletationReason"
                            class="form-control"
                            rows="3"
                            placeholder="Enter reason here"
                        ></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <span id="rejectLoader" class="me-auto d-none">
                        <i class="fa fa-spinner fa-spin"></i> Deleting...
                    </span>

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancel
                    </button>
                    <button type="button" id="confirmDeleteProject" class="btn btn-danger">
                        Delete
                    </button>
                </div>

            </div>
        </div>
    </div>
    <!-- A Code: 10-01-2026 End -->

</div>
@endsection

@section('scripts')
<script>
    var isSupervisor = @json($is_superwisor_login);
    var isQuality = @json($is_quality_login);

    $(document).ready(function() {
        // Custom sorting logic for Project No (e.g., 2024-001)
        $.fn.dataTable.ext.type.order['project-no-pre'] = function(data) {
            var parts = data.split('-');
            var main = parseInt(parts[0], 10) || 0;
            var sub = parseInt(parts[1], 10) || 0;
            return main * 10000 + sub;
        };

        // Initialize DataTable
        let table = $('#project_table').DataTable({
            "ordering": false,
            order: [
                [3, 'desc']
            ],
            columnDefs: [{
                type: 'project-no',
                targets: 3
            }]
        });
        $('#project_table').removeClass('dataTable');
    });

    $(document).ready(function() {
        // Prevent dropdown from closing when clicking inside
        $(document).on('click', '.dropdown-menu', function(e) {
            e.stopPropagation(); // stop click from bubbling and closing dropdown
        });
    });

    $(document).on('change', '.select-all-filter', function() {
        var targetClass = $(this).data('target');
        var isChecked = $(this).is(':checked');
        $(targetClass).attr('checked', isChecked ? 'checked' : false);

        if (isChecked) {
            $(this).closest('.dropdown').find('.dropdown-item').addClass('checked');
        } else {
            $(this).closest('.dropdown').find('.dropdown-item').removeClass('checked');
        }
    });

    $(document).ready(function() {
        const projectFilterRoute = "{{ route('ProductionManagerProjectIndexFilter') }}";
        $(document).on('click', '.apply-filter-btn', function() {
            var last_filter_column = $(this).data('column');
            $('#last_filter_column').val(last_filter_column);

            $('#filter_form').attr('action', projectFilterRoute);

            if ($('#last_filter_column').val() && $('#filter_form').attr('action') === projectFilterRoute) {
                $('#filter_form').submit();
            } else {
                alert("Form action does not match ProjectFilter route.");
            }
        });
    });

    function download_csv() {
        var exportRoute = "{{ route('project.export.csv') }}";
        var $form = $('#filter_form');

        // Set form action to the export route
        $form.attr('action', exportRoute);

        // Confirm the action is correctly set before submitting
        if ($form.attr('action') === exportRoute) {
            $form.submit();
        } else {
            alert("Form route did not match. Please try again.");
        }
    }
</script>

<script type="text/javascript">
    let currentProjectId = null;
    let currentArticleNumber = null;
    let currentQtyNo = null;
    let currentQualityType = null;
    let modalHistory = [];
    const userRole = "{{ Auth::user()->role }}";
    let isNavigating = false; // Debounce flag

    // Function to show a specific container and hide others
    function showContainer(containerId) {
        const containers = [
            '#foldersList',
            '#subsubfoldersList',
            '#documentsList',
            '#BOMList',
            '#DrawingsList',
            '#WIPPhotosList',
            '#executionImagesListContainer',
            '#executionImagesQtyListContainer',
            '#executionImagesContainer',
            '#executionWorkOrdersListContainer',
            '#executionDocsContainer',
            '#qualityFinalInspectionListContainer',
            '#qualityFinalInspectionQtyListContainer',
            '#qualityTestReportsListContainer',
            '#qualityTestReportsQtyListContainer',
            '#qualityDocsContainer',
            '#qrCodesListContainer',
            '#qrCodeImagesContainer',
            '#executionPartialOrderPLListContainer',
            '#executionPartialOrderPLQtyListContainer',
            '#executionPartialOrderPLDocsContainer',
            '#namePlateListContainer',
            '#namePlateQtyListContainer',
            '#namePlateImagesContainer',
        ];

        containers.forEach(container => $(container).hide());
        $(containerId).show();
    }

    // Function to push current state to history with duplicate prevention
    function pushToHistory(containerId, params = {}) {
        // Prevent pushing duplicate #foldersList or #subsubfoldersList
        if (
            (containerId === '#foldersList' || containerId === '#subsubfoldersList') &&
            modalHistory.length > 0 &&
            modalHistory[modalHistory.length - 1].containerId === containerId &&
            JSON.stringify(modalHistory[modalHistory.length - 1].params) === JSON.stringify(params)
        ) {
            console.log(`Skipped duplicate push to ${containerId}`);
            return;
        }

        modalHistory.push({
            containerId,
            params
        });
        console.log('Pushed to history:', {
            containerId,
            params,
            history: modalHistory
        });
    }

    // Function to restore subfolder visibility
    function restoreSubfolderVisibility(folderName) {
        $('#foldersList .subfolder-item').closest('ul').hide();
        if (folderName) {
            $(`.subfolder-${folderName.toLowerCase().replace(/\s/g, '_')}`).show();
        }
    }

    // Modal open handler
    $(document).off('click', '.open-documents-modal').on('click', '.open-documents-modal', function(e) {
        e.stopPropagation();
        if (isNavigating) return;
        isNavigating = true;

        modalHistory = []; // Clear history
        currentProjectId = $(this).data('id');
        currentArticleNumber = null;
        currentQtyNo = null;
        currentQualityType = null;
        console.log('Clicked Project ID:', currentProjectId);

        if (!currentProjectId) {
            alert('Project ID is not defined. Please check the table data.');
            isNavigating = false;
            return;
        }

        pushToHistory('#foldersList');
        showContainer('#foldersList');
        restoreSubfolderVisibility(null);
        $('#documentsModal').modal('show');

        setTimeout(() => {
            isNavigating = false;
        }, 300);
    });

    $(document).off('click', '.folder').on('click', '.folder', function(e) {
        e.stopPropagation();
        if (isNavigating) return;
        isNavigating = true;

        const folderName = $(this).data('folder');
        console.log('Clicked folder:', folderName);
        let $subfolderList;

        // Explicitly handle "PO and Invoices" due to case mismatch
        if (folderName === 'PO and Invoices') {
            $subfolderList = $(this).find('ul.subfolder-po_and_invoices');
        } else {
            const subfolderClass = `subfolder-${folderName.toLowerCase().replace(/\s/g, '_')}`;
            $subfolderList = $(this).find(`ul.${subfolderClass}`);
        }

        if (folderName === 'NamePlate') {
            pushToHistory('#namePlateListContainer');
            showContainer('#namePlateListContainer');
            loadNamePlateList();
        }
        console.log('Subfolder list found:', $subfolderList.length);

        // Toggle subfolder visibility
        if ($subfolderList.length) {
            // Hide all other subfolder lists
            $('#foldersList .subfolder-item').closest('ul').hide();

            // Reset all icons to closed
            $('#foldersList .folder .folder-icon')
                .removeClass('fa-folder-open')
                .addClass('fa-folder');           

            // Toggle visibility of selected subfolder
            const isCurrentlyVisible = $subfolderList.is(':visible');

            // Toggle the clicked folder's subfolder list
            $subfolderList.toggle();
            console.log('Toggled visibility for:', folderName);

            // Update icon
            if (!isCurrentlyVisible) {
                $(this).find('.folder-icon')
                    .removeClass('fa-folder')
                    .addClass('fa-folder-open');
            } else {
                $(this).find('.folder-icon')
                    .removeClass('fa-folder-open')
                    .addClass('fa-folder');
            }

            // Update navigation history
            pushToHistory('#foldersList', {
                folder: folderName
            });
        } else {
            console.log('No subfolder list found for:', folderName);
            // For folders without subfolders (e.g., WIP Photos, QR Codes), load their content directly
            if (folderName === 'WIP Photos') {
                pushToHistory('#WIPPhotosList');
                showContainer('#WIPPhotosList');
                loadWIPPhotos();
            } else if (folderName === 'QR Codes') {
                pushToHistory('#qrCodesListContainer');
                showContainer('#qrCodesListContainer');
                loadQrCodeList();
            }
        }

        setTimeout(() => {
            isNavigating = false;
        }, 300);
    });

    // Subfolder click handler
    $(document).off('click', '.subfolder-item').on('click', '.subfolder-item', function(e) {
        e.stopPropagation();
        if (isNavigating) return;
        isNavigating = true;

        const subfolderName = $(this).data('folder');
        const parentFolder = $(this).closest('.folder').data('folder');

        if (subfolderName === 'Drawings') {
            pushToHistory('#subsubfoldersList', {
                folder: subfolderName
            });
            showContainer('#subsubfoldersList');
        } else if (parentFolder === 'Project Execution') {
            if (subfolderName === 'Images') {
                pushToHistory('#executionImagesListContainer');
                showContainer('#executionImagesListContainer');
                loadProjectExecutionImageList();
            } else if (subfolderName === 'Work Orders') {
                pushToHistory('#executionWorkOrdersListContainer');
                showContainer('#executionWorkOrdersListContainer');
                loadProjectExecutionWorkOrdersList();
            } else if (subfolderName === 'Full_Order_PL') {
                pushToHistory('#executionDocsContainer', {
                    type: subfolderName
                });
                showContainer('#executionDocsContainer');
                loadProjectExecutionDocs(subfolderName);
            } else if (subfolderName === 'Partial Order PL') {
                pushToHistory('#executionPartialOrderPLListContainer');
                showContainer('#executionPartialOrderPLListContainer');
                loadProjectExecutionPartialOrderPLList();
            }
        } else if (parentFolder === 'Quality') {
            if (subfolderName === 'Final Inspection') {
                pushToHistory('#qualityFinalInspectionListContainer');
                showContainer('#qualityFinalInspectionListContainer');
                loadQualityFinalInspectionList();
            }else if (subfolderName === 'Images') {
                pushToHistory('#executionImagesListContainer');
                showContainer('#executionImagesListContainer');
                loadProjectExecutionImageList();
            } else if (subfolderName === 'Test Reports') {
                pushToHistory('#qualityTestReportsListContainer');
                showContainer('#qualityTestReportsListContainer');
                loadQualityTestReportsList();
            } 
            
        } else if (subfolderName === 'From Customers') {
            pushToHistory('#documentsList', {
                folder: 'From Customers'
            });
            showContainer('#documentsList');
            loadDocuments('From Customers');
        } else if (subfolderName === 'BOM') {
            pushToHistory('#BOMList', {
                folder: subfolderName
            });
            showContainer('#BOMList');
            loadAllBOM(subfolderName);
        } else {
            pushToHistory('#documentsList', {
                folder: 'PO and Invoices',
                subfolder: subfolderName
            });
            showContainer('#documentsList');
            loadDocuments('PO and Invoices', subfolderName);
        }

        setTimeout(() => {
            isNavigating = false;
        }, 300);
    });

    $(document).off('click', '.view-subsubfolder').on('click', '.view-subsubfolder', function(e) {
        e.stopPropagation();
        if (isNavigating) return;
        isNavigating = true;

        const subsubfolderName = $(this).data('subsubfolder');
        pushToHistory('#DrawingsList', {
            folder: 'Drawings',
            subsubfolder: subsubfolderName
        });
        showContainer('#DrawingsList');
        loadAllDrawings('Drawings', subsubfolderName);

        setTimeout(() => {
            isNavigating = false;
        }, 300);
    });

    // Back button handler
    $(document).off('click', '.modal_show_back').on('click', '.modal_show_back', function(e) {
        e.stopPropagation();
        if (isNavigating) return;
        isNavigating = true;

        console.log('Back button clicked, current history:', modalHistory);
        modalHistory.pop(); // Remove current state

        const previousState = modalHistory.length > 0 ? modalHistory[modalHistory.length - 1] : null;
        if (previousState) {
            const {
                containerId,
                params
            } = previousState;
            showContainer(containerId);

            if (containerId === '#foldersList') {
                restoreSubfolderVisibility(params.folder);
            } else if (containerId === '#subsubfoldersList') {
                showContainer('#subsubfoldersList');
            } else if (containerId === '#documentsList') {
                loadDocuments(params.folder, params.subfolder);
            } else if (containerId === '#BOMList') {
                loadAllBOM(params.folder);
            } else if (containerId === '#DrawingsList') {
                loadAllDrawings(params.folder, params.subsubfolder);
            } else if (containerId === '#WIPPhotosList') {
                loadWIPPhotos();
            } else if (containerId === '#executionImagesListContainer') {
                loadProjectExecutionImageList();
            } else if (containerId === '#executionImagesQtyListContainer') {
                currentArticleNumber = params.articleNumber;
                loadProjectExecutionQtyList(params.articleNumber, params.qty);
            } else if (containerId === '#executionImagesContainer') {
                currentArticleNumber = params.articleNumber;
                currentQtyNo = params.qtyNo;
                loadProjectExecutionImages();
            } else if (containerId === '#executionWorkOrdersListContainer') {
                loadProjectExecutionWorkOrdersList();
            } else if (containerId === '#executionDocsContainer') {
                currentArticleNumber = params.articleNumber;
                loadProjectExecutionDocs(params.type);
            } else if (containerId === '#qualityFinalInspectionListContainer') {
                loadQualityFinalInspectionList();
            } else if (containerId === '#qualityFinalInspectionQtyListContainer') {
                currentArticleNumber = params.articleNumber;
                currentQualityType = params.type;
                loadQualityQtyList(params.articleNumber, params.qty, params.type);
            } else if (containerId === '#qualityTestReportsListContainer') {
                //loadQualityTestReports();
                loadQualityTestReportsList();
            } else if (containerId === '#qualityTestReportsQtyListContainer') {
                currentArticleNumber = params.qarticleNumber;
                currentQualityType = params.type;
                loadQualityQtyList(params.articleNumber, params.qqty, params.type);
            }             
            else if (containerId === '#qualityDocsContainer') {
                currentArticleNumber = params.articleNumber;
                currentQtyNo = params.qtyNo;
                currentQualityType = params.type;
                loadQualityDocs(params.type);
            } else if (containerId === '#qrCodesListContainer') {
                loadQrCodeList();
            } else if (containerId === '#qrCodeImagesContainer') {
                currentArticleNumber = params.articleNumber;
                loadQrCodeImages(params.articleNumber, params.qty);
            } else if (containerId === '#executionPartialOrderPLListContainer') {
                loadProjectExecutionPartialOrderPLList();
            } else if (containerId === '#executionPartialOrderPLQtyListContainer') {
                currentArticleNumber = params.articleNumber;
                loadProjectExecutionPartialOrderPLQtyList(params.articleNumber, params.qty);
            } else if (containerId === '#executionPartialOrderPLDocsContainer') {
                currentArticleNumber = params.articleNumber;
                currentQtyNo = params.qtyNo;
                loadProjectExecutionPartialOrderPLDocs();
            } else if (containerId === '#namePlateListContainer') {
                loadNamePlateList();
            } else if (containerId === '#namePlateQtyListContainer') {
                currentArticleNumber = params.articleNumber;
                loadNamePlateQtyList(params.articleNumber, params.qty);
            } else if (containerId === '#namePlateImagesContainer') {
                currentArticleNumber = params.articleNumber;
                currentQtyNo = params.qtyNo;
                loadNamePlateImages();
            }
        } else {
            showContainer('#foldersList');
            restoreSubfolderVisibility(null);
        }

        setTimeout(() => {
            isNavigating = false;
        }, 300);
    });

    $(document).off('click', '.view-partial-order-pl').on('click', '.view-partial-order-pl', function(e) {
        e.stopPropagation();
        if (isNavigating) return;
        isNavigating = true;

        currentArticleNumber = $(this).data('article');
        const qty = parseInt($(this).data('qty'));
        pushToHistory('#executionPartialOrderPLQtyListContainer', {
            articleNumber: currentArticleNumber,
            qty
        });
        showContainer('#executionPartialOrderPLQtyListContainer');
        loadProjectExecutionPartialOrderPLQtyList(currentArticleNumber, qty);

        setTimeout(() => {
            isNavigating = false;
        }, 300);
    });

    $(document).off('click', '.view-partial-order-pl-qty').on('click', '.view-partial-order-pl-qty', function(e) {
        e.stopPropagation();
        if (isNavigating) return;
        isNavigating = true;

        currentArticleNumber = $(this).data('article');
        currentQtyNo = $(this).data('qty-no');
        pushToHistory('#executionPartialOrderPLDocsContainer', {
            articleNumber: currentArticleNumber,
            qtyNo: currentQtyNo
        });
        showContainer('#executionPartialOrderPLDocsContainer');
        loadProjectExecutionPartialOrderPLDocs();

        setTimeout(() => {
            isNavigating = false;
        }, 300);
    });

    // Add document for Partial Order PL
    $(document).off('click', '.add-partial-order-pl-doc').on('click', '.add-partial-order-pl-doc', function(e) {
        e.stopPropagation();
        if (!currentQtyNo) {
            alert('Please select a quantity number first.');
            return;
        }
        $('#hiddenPartialOrderPLDocInput').click();
    });

    $(document).off('change', '#hiddenPartialOrderPLDocInput').on('change', '#hiddenPartialOrderPLDocInput', function() {
        var fileInput = this;
        if (fileInput.files.length === 0) {
            return;
        }

        var formData = new FormData();
        formData.append('project_id', currentProjectId);
        formData.append('article_number', currentArticleNumber);
        formData.append('qty_no', currentQtyNo);
        formData.append('document', fileInput.files[0]);
        formData.append('_token', '{{ csrf_token() }}');

        $.ajax({
            url: '',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#executionPartialOrderPLDocsTable tbody').empty();
                    loadProjectExecutionPartialOrderPLDocs();
                    fileInput.value = '';
                    alert(response.message);
                }
            },
            error: function(xhr) {
                alert('Failed to upload document');
            }
        });
    });

    // Delete Partial Order PL document
    $(document).off('click', '.delete-partial-order-pl-doc').on('click', '.delete-partial-order-pl-doc', function(e) {
        e.stopPropagation();
        var $row = $(this).closest('tr');
        var filePath = $(this).data('path');

        if (confirm('Are you sure you want to delete this document?')) {
            $.ajax({
                url: '',
                type: 'POST',
                data: {
                    project_id: currentProjectId,
                    file_path: filePath,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('#executionPartialOrderPLDocsTable tbody').empty();
                        loadProjectExecutionPartialOrderPLDocs();
                        alert(response.message);
                    }
                },
                error: function(xhr) {
                    alert('Failed to delete document');
                }
            });
        }
    });

    // View handlers
    $(document).off('click', '.view-execution-images').on('click', '.view-execution-images', function(e) {
        e.stopPropagation();
        if (isNavigating) return;
        isNavigating = true;

        currentArticleNumber = $(this).data('article');
        const qty = parseInt($(this).data('qty'));
        pushToHistory('#executionImagesQtyListContainer', {
            articleNumber: currentArticleNumber,
            qty
        });
        showContainer('#executionImagesQtyListContainer');
        loadProjectExecutionQtyList(currentArticleNumber, qty);

        setTimeout(() => {
            isNavigating = false;
        }, 300);
    });

    $(document).off('click', '.view-execution-images-qty').on('click', '.view-execution-images-qty', function(e) {
        e.stopPropagation();
        if (isNavigating) return;
        isNavigating = true;

        currentArticleNumber = $(this).data('article');
        currentQtyNo = $(this).data('qty-no');
        pushToHistory('#executionImagesContainer', {
            articleNumber: currentArticleNumber,
            qtyNo: currentQtyNo
        });
        showContainer('#executionImagesContainer');
        loadProjectExecutionImages();

        setTimeout(() => {
            isNavigating = false;
        }, 300);
    });

    $(document).off('click', '.view-execution-work-orders').on('click', '.view-execution-work-orders', function(e) {
        e.stopPropagation();
        if (isNavigating) return;
        isNavigating = true;

        currentArticleNumber = $(this).data('article');
        pushToHistory('#executionDocsContainer', {
            type: 'Work Orders',
            articleNumber: currentArticleNumber
        });
        showContainer('#executionDocsContainer');
        loadProjectExecutionDocs('Work Orders');

        setTimeout(() => {
            isNavigating = false;
        }, 300);
    });

    $(document).off('click', '.view-quality-final-inspection').on('click', '.view-quality-final-inspection', function(e) {
        e.stopPropagation();
        if (isNavigating) return;
        isNavigating = true;

        currentArticleNumber = $(this).data('article');
        const qty = parseInt($(this).data('qty'));
        currentQualityType = 'Final Inspection';
        pushToHistory('#qualityFinalInspectionQtyListContainer', {
            articleNumber: currentArticleNumber,
            qty,
            type: currentQualityType
        });
        showContainer('#qualityFinalInspectionQtyListContainer');
        loadQualityQtyList(currentArticleNumber, qty, currentQualityType);

        setTimeout(() => {
            isNavigating = false;
        }, 300);
    });

    $(document).off('click', '.view-quality-test-reports').on('click', '.view-quality-test-reports', function(e) {
        e.stopPropagation();
        if (isNavigating) return;
        isNavigating = true;

        currentArticleNumber = $(this).data('article');
        const qty = parseInt($(this).data('qty'));
        currentQualityType = 'Test Reports';
        pushToHistory('#qualityTestReportsQtyListContainer', {
            articleNumber: currentArticleNumber,
            qty,
            type: currentQualityType
        });
        showContainer('#qualityTestReportsQtyListContainer');
        loadQualityQtyList(currentArticleNumber, qty, currentQualityType);

        setTimeout(() => {
            isNavigating = false;
        }, 300);
    });

    $(document).off('click', '.view-quality-docs-qty').on('click', '.view-quality-docs-qty', function(e) {
        e.stopPropagation();
        if (isNavigating) return;
        isNavigating = true;

        currentArticleNumber = $(this).data('article');
        currentQtyNo = $(this).data('qty-no');
        currentQualityType = $(this).data('type');
        pushToHistory('#qualityDocsContainer', {
            articleNumber: currentArticleNumber,
            qtyNo: currentQtyNo,
            type: currentQualityType
        });
        showContainer('#qualityDocsContainer');
        loadQualityDocs(currentQualityType);

        setTimeout(() => {
            isNavigating = false;
        }, 300);
    });

    $(document).off('click', '.view-qr-codes').on('click', '.view-qr-codes', function(e) {
        e.stopPropagation();
        if (isNavigating) return;
        isNavigating = true;

        currentArticleNumber = $(this).data('article');
        const qty = parseInt($(this).data('qty'));
        pushToHistory('#qrCodeImagesContainer', {
            articleNumber: currentArticleNumber,
            qty
        });
        showContainer('#qrCodeImagesContainer');
        loadQrCodeImages(currentArticleNumber, qty);

        setTimeout(() => {
            isNavigating = false;
        }, 300);
    });

    function loadNamePlateList() {
        if (!currentProjectId) {
            console.error('Project ID is undefined');
            return;
        }

        $.ajax({
            url: '{{ route("getNamePlateList", ["projectId" => "projectIdPlaceholder"]) }}'.replace('projectIdPlaceholder', currentProjectId),
            type: 'GET',
            success: function(response) {
                if (!response.namePlateList || response.namePlateList.length === 0) {
                    $('#namePlateListTable tbody').empty();
                    $('#namePlateListTable tbody').append('<tr><td colspan="5">No data found</td></tr>');
                    return;
                }
                $('#foldersList').hide();
                $('#namePlateListContainer').show();
                $('#namePlateListTable tbody').empty();

                $.each(response.namePlateList, function(index, item) {
                    $('#namePlateListTable tbody').append(`
                    <tr>
                        <td>${item.cart_model_name || 'N/A'}</td>
                        <td>${item.description || 'N/A'}</td>
                        <td>${item.full_article_number}</td>
                        <td>${item.qty}</td>
                        <td>
                            <button class="btn btn-sm btn-primary m-auto d-flex px-2 view-nameplate" data-article="${item.full_article_number}" data-qty="${item.qty}">
                                <i class="fa fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                `);
                });
            },
            error: function(xhr) {
                alert('Failed to fetch NamePlate list');
            }
        });
    }

    function loadNamePlateQtyList(articleNumber, qty) {
        if (!currentProjectId || !articleNumber || !qty) {
            console.error('Project ID, Article Number, or Qty is undefined');
            return;
        }

        $('#foldersList').hide();
        $('#namePlateListContainer').hide();
        $('#namePlateQtyListContainer').show();
        $('#namePlateQtyListTable tbody').empty();

        for (let i = 1; i <= qty; i++) {
            $('#namePlateQtyListTable tbody').append(`
            <tr>
                <td>${i}</td>
                <td>
                    <button class="btn btn-sm btn-primary m-auto d-flex px-2 view-nameplate-images" data-article="${articleNumber}" data-qty-no="${i}">
                        <i class="fa fa-eye"></i>
                    </button>
                </td>
            </tr>
        `);
        }
    }

    function loadNamePlateImages() {
        if (!currentProjectId || !currentArticleNumber || !currentQtyNo) {
            console.error('Project ID, Article Number, or Qty No is undefined');
            alert('Project ID, Article Number, or Qty No is not available.');
            return;
        }

        $.ajax({
            url: '{{ route("getNamePlateImages", ["projectId" => "projectIdPlaceholder", "articleNumber" => "articleNumberPlaceholder", "qtyNo" => "qtyNoPlaceholder"]) }}'
                .replace('projectIdPlaceholder', currentProjectId)
                .replace('articleNumberPlaceholder', currentArticleNumber)
                .replace('qtyNoPlaceholder', currentQtyNo),
            type: 'GET',
            success: function(response) {
                if (!response.success) {
                    $('#namePlateImagesTable tbody').empty();
                    $('#noNamePlateImagesMessage').show();
                    return;
                }

                $('#namePlateListContainer').hide();
                $('#namePlateQtyListContainer').hide();
                $('#namePlateImagesContainer').show();
                $('#namePlateImagesTable tbody').empty();
                $('#noNamePlateImagesMessage').hide();

                if (response.images.length === 0) {
                    $('#noNamePlateImagesMessage').show();
                } else {
                    $.each(response.images, function(index, image) {
                        var fileExtension = image.name.split('.').pop().toLowerCase();
                        var isImage = ['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension);
                        let rowHtml = `
                            <tr data-path="${image.path}">
                                <td>
                                    ${isImage ? `
                                    <a href="{{ asset('') }}${image.path}" target="_blank" class="d-flex align-items-center text-decoration-none">
                                        <img src="{{ asset('') }}${image.path}" alt="${image.name}" style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                                    </a>` : `
                                    <i class="${getIconClass(fileExtension)} mr-2"></i>
                                    `}
                                </td>
                                <td>
                                    <a href="{{ asset('') }}${image.path}" target="_blank" class="text-decoration-none">
                                        <span>${image.name}</span>
                                    </a>
                                </td>
                                <td>
                                    <!-- Add actions if needed -->
                                </td>
                            </tr>`;
                        $('#namePlateImagesTable tbody').append(rowHtml);
                    });
                }

                $.ajax({
                    url: '{{ route("getProjectById", ["id" => "projectIdPlaceholder"]) }}'.replace('projectIdPlaceholder', currentProjectId),
                    type: 'GET',
                    success: function(projectResponse) {
                        if (projectResponse.success) {
                            const projectNo = projectResponse.project.project_no;
                            const path = `${projectNo}/NamePlate/${currentArticleNumber}/${currentQtyNo}`;
                            $('#namePlatePathDisplay').text(path);
                        }
                    },
                    error: function(xhr) {
                        console.error('Failed to fetch project details:', xhr.responseText);
                    }
                });
            },
            error: function(xhr) {
                $('#namePlateImagesTable tbody').empty();
                $('#noNamePlateImagesMessage').show();
            }
        });
    }

    $(document).off('click', '.view-nameplate').on('click', '.view-nameplate', function(e) {
        e.stopPropagation();
        if (isNavigating) return;
        isNavigating = true;

        currentArticleNumber = $(this).data('article');
        const qty = parseInt($(this).data('qty'));
        pushToHistory('#namePlateQtyListContainer', {
            articleNumber: currentArticleNumber,
            qty
        });
        showContainer('#namePlateQtyListContainer');
        loadNamePlateQtyList(currentArticleNumber, qty);

        setTimeout(() => {
            isNavigating = false;
        }, 300);
    });

    $(document).off('click', '.view-nameplate-images').on('click', '.view-nameplate-images', function(e) {
        e.stopPropagation();
        if (isNavigating) return;
        isNavigating = true;

        currentArticleNumber = $(this).data('article');
        currentQtyNo = $(this).data('qty-no');
        pushToHistory('#namePlateImagesContainer', {
            articleNumber: currentArticleNumber,
            qtyNo: currentQtyNo
        });
        showContainer('#namePlateImagesContainer');
        loadNamePlateImages();

        setTimeout(() => {
            isNavigating = false;
        }, 300);
    });

    // Load Partial Order PL item list
    function loadProjectExecutionPartialOrderPLList() {
        if (!currentProjectId) {
            console.error('Project ID is undefined');
            return;
        }

        $.ajax({
            url: '{{ route("getProjectExecutionPartialOrderPLList", ["projectId" => "projectIdPlaceholder"]) }}'.replace('projectIdPlaceholder', currentProjectId),
            type: 'GET',
            success: function(response) {
                if (!response.partialOrderPLList || response.partialOrderPLList.length === 0) {
                    $('#executionPartialOrderPLListTable tbody').empty();
                    $('#executionPartialOrderPLListTable tbody').append('<tr><td colspan="5">No Partial Order PL data found</td></tr>');
                    return;
                }
                $('#foldersList').hide();
                $('#executionPartialOrderPLListContainer').show();
                $('#executionPartialOrderPLQtyListContainer').hide();
                $('#executionPartialOrderPLDocsContainer').hide();
                $('#executionPartialOrderPLListTable tbody').empty();

                $.each(response.partialOrderPLList, function(index, item) {
                    $('#executionPartialOrderPLListTable tbody').append(`
                    <tr>
                        <td>${item.cart_model_name || 'N/A'}</td>
                        <td>${item.description || 'N/A'}</td>
                        <td>${item.full_article_number}</td>
                        <td>${item.qty}</td>
                        <td>
                            <button class="btn btn-sm btn-primary m-auto d-flex px-2 view-partial-order-pl" data-article="${item.full_article_number}" data-qty="${item.qty}">
                                <i class="fa fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                `);
                });
            },
            error: function(xhr) {
                alert('Failed to fetch Partial Order PL list');
            }
        });
    }

    // Load Partial Order PL quantity list
    function loadProjectExecutionPartialOrderPLQtyList(articleNumber, qty) {
        if (!currentProjectId || !articleNumber || !qty) {
            console.error('Project ID, Article Number, or Qty is undefined');
            return;
        }

        $('#foldersList').hide();
        $('#executionPartialOrderPLListContainer').hide();
        $('#executionPartialOrderPLDocsContainer').hide();
        $('#executionPartialOrderPLQtyListContainer').show();
        $('#executionPartialOrderPLQtyListTable tbody').empty();

        for (let i = 1; i <= qty; i++) {
            $('#executionPartialOrderPLQtyListTable tbody').append(`
            <tr>
                <td>${i}</td>
                <td>
                    <button class="btn btn-sm btn-primary m-auto d-flex px-2 view-partial-order-pl-qty" data-article="${articleNumber}" data-qty-no="${i}">
                        <i class="fa fa-eye"></i>
                    </button>
                </td>
            </tr>
        `);
        }

        $.ajax({
            url: '{{ route("getProjectById", ["id" => "projectIdPlaceholder"]) }}'.replace('projectIdPlaceholder', currentProjectId),
            type: 'GET',
            success: function(projectResponse) {
                if (projectResponse.success) {
                    const projectNo = projectResponse.project.project_no;
                    const path = `${projectNo}/${articleNumber}`;
                    $('#partialOrderPLPathDisplay').text(path);
                }
            },
            error: function(xhr) {
                console.error('Failed to fetch project details:', xhr.responseText);
            }
        });
    }

    // Load Partial Order PL documents
    function loadProjectExecutionPartialOrderPLDocs() {
        if (!currentProjectId || !currentArticleNumber || !currentQtyNo) {
            console.error('Project ID, Article Number, or Qty No is undefined');
            alert('Project ID, Article Number, or Qty No is not available.');
            return;
        }

        $.ajax({
            url: '{{ route("getPartialOrderPLDocs", ["projectId" => "projectIdPlaceholder", "articleNumber" => "articleNumberPlaceholder", "qtyNo" => "qtyNoPlaceholder"]) }}'
                .replace('projectIdPlaceholder', currentProjectId)
                .replace('articleNumberPlaceholder', currentArticleNumber)
                .replace('qtyNoPlaceholder', currentQtyNo),
            type: 'GET',
            success: function(response) {
                if (!response.success) {
                    $('#executionPartialOrderPLDocsTable tbody').empty();
                    $('#noPartialOrderPLDocsMessage').show();
                    return;
                }

                $('#executionPartialOrderPLListContainer').hide();
                $('#executionPartialOrderPLQtyListContainer').hide();
                $('#executionPartialOrderPLDocsContainer').show();
                $('#executionPartialOrderPLDocsTable tbody').empty();
                $('#noPartialOrderPLDocsMessage').hide();

                if (response.documents.length === 0) {
                    $('#noPartialOrderPLDocsMessage').show();
                } else {
                    $.each(response.documents, function(index, doc) {
                        var fileExtension = doc.name.split('.').pop().toLowerCase();
                        var iconClass = getIconClass(fileExtension);
                        let rowHtml = `
                        <tr data-path="${doc.path}">
                            <td>
                                <a href="{{ asset('') }}${doc.path}" target="_blank" class="d-flex align-items-center text-decoration-none">
                                    <i class="${iconClass} mr-2"></i>
                                    <span>${doc.name}</span>
                                </a>
                            </td>
                        </tr>`;
                        $('#executionPartialOrderPLDocsTable tbody').append(rowHtml);
                    });
                }

                $.ajax({
                    url: '{{ route("getProjectById", ["id" => "projectIdPlaceholder"]) }}'.replace('projectIdPlaceholder', currentProjectId),
                    type: 'GET',
                    success: function(projectResponse) {
                        if (projectResponse.success) {
                            const projectNo = projectResponse.project.project_no;
                            const path = `${projectNo}/${currentArticleNumber}/${currentQtyNo}`;
                            $('#partialOrderPLPathDisplay').text(path);
                        }
                    },
                    error: function(xhr) {
                        console.error('Failed to load project details:', xhr.responseText);
                    }
                });
            },
            error: function(xhr) {
                $('#executionPartialOrderPLDocsTable tbody').empty();
                $('#noPartialOrderPLDocsMessage').show();
            }
        });
    }

    // File upload and delete handlers
    $(document).off('click', '.add-execution-image').on('click', '.add-execution-image', function(e) {
        e.stopPropagation();
        if (!currentQtyNo) {
            alert('Please select a quantity number first.');
            return;
        }
        $('#hiddenExecutionImageInput').click();
    });

    $(document).off('change', '#hiddenExecutionImageInput').on('change', '#hiddenExecutionImageInput', function() {
        var fileInput = this;
        if (fileInput.files.length === 0) {
            return;
        }

        var formData = new FormData();
        formData.append('project_id', currentProjectId);
        formData.append('article_number', currentArticleNumber);
        formData.append('qty_no', currentQtyNo);
        formData.append('_token', '{{ csrf_token() }}');

        for (var i = 0; i < fileInput.files.length; i++) {
            formData.append('images[]', fileInput.files[i]);
        }

        $.ajax({
            url: '{{ route("uploadProjectExecutionImage") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('Upload Response:', response);
                if (response.success) {
                    $('#executionImagesTable tbody').empty();
                    loadProjectExecutionImages();
                    fileInput.value = '';
                    alert(response.message || 'Images uploaded successfully');
                } else {
                    alert('Upload failed: ' + (response.message || 'Unknown error'));
                }
            },
            error: function(xhr) {
                console.error('Upload Error:', xhr.responseText);
                alert('Failed to upload images: ' + xhr.responseText);
            }
        });
    });

    $(document).off('click', '.delete-execution-image').on('click', '.delete-execution-image', function(e) {
        e.stopPropagation();
        var $row = $(this).closest('tr');
        var filePath = $(this).data('path');

        if (confirm('Are you sure you want to delete this image?')) {
            $.ajax({
                url: '{{ route("deleteProjectExecutionImage") }}',
                type: 'POST',
                data: {
                    project_id: currentProjectId,
                    file_path: filePath,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('#executionImagesTable tbody').empty();
                        loadProjectExecutionImages();
                        alert(response.message);
                    }
                },
                error: function(xhr) {
                    alert('Failed to delete image');
                }
            });
        }
    });

    $(document).off('click', '.add-execution-doc').on('click', '.add-execution-doc', function(e) {
        e.stopPropagation();
        $('#hiddenExecutionDocInput').click();
    });

    $(document).off('change', '#hiddenExecutionDocInput').on('change', '#hiddenExecutionDocInput', function() {
        var fileInput = this;
        if (fileInput.files.length === 0) {
            return;
        }

        var type = $('#executionDocsContainer').data('type') ;
        var formData = new FormData();
        formData.append('project_id', currentProjectId);
        formData.append('type', type);
        if ((type === 'Work Orders') && currentArticleNumber) {
            formData.append('article_number', currentArticleNumber);
        }
        formData.append('document', fileInput.files[0]);
        formData.append('_token', '{{ csrf_token() }}');

        $.ajax({
            url: '{{ route("uploadProjectExecutionDoc") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#executionDocsTable tbody').empty();
                    loadProjectExecutionDocs(type);
                    fileInput.value = '';
                    alert(response.message);
                }
            },
            error: function(xhr) {
                alert('Failed to upload document');
            }
        });
    });

    $(document).off('click', '.delete-execution-doc').on('click', '.delete-execution-doc', function(e) {
        e.stopPropagation();
        var $row = $(this).closest('tr');
        var filePath = $(this).data('path');
        var type = $('#executionDocsContainer').data('type') ;

        if (confirm('Are you sure you want to delete this document?')) {
            $.ajax({
                url: '{{ route("deleteProjectExecutionDoc") }}',
                type: 'POST',
                data: {
                    project_id: currentProjectId,
                    file_path: filePath,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('#executionDocsTable tbody').empty();
                        loadProjectExecutionDocs(type);
                        alert(response.message);
                    }
                },
                error: function(xhr) {
                    alert('Failed to delete document');
                }
            });
        }
    });

    $(document).off('click', '.add-quality-doc').on('click', '.add-quality-doc', function(e) {
        e.stopPropagation();
        if (!currentQtyNo) {
            alert('Please select a quantity number first.');
            return;
        }
        $('#hiddenQualityDocInput').click();
    });

    $(document).off('change', '#hiddenQualityDocInput').on('change', '#hiddenQualityDocInput', function() {
        var fileInput = this;
        if (fileInput.files.length === 0) {
            return;
        }

        var type = $('#qualityDocsContainer').data('type') || 'Final Inspection';
        var formData = new FormData();
        formData.append('project_id', currentProjectId);
        formData.append('type', type);
        formData.append('article_number', currentArticleNumber);
        formData.append('qty_no', currentQtyNo);
        formData.append('document', fileInput.files[0]);
        formData.append('_token', '{{ csrf_token() }}');

        $.ajax({
            url: '{{ route("uploadQualityDoc") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#qualityDocsTable tbody').empty();
                    loadQualityDocs(type);
                    fileInput.value = '';
                    alert(response.message);
                }
            },
            error: function(xhr) {
                alert('Failed to upload document');
            }
        });
    });

    $(document).off('click', '.delete-quality-doc').on('click', '.delete-quality-doc', function(e) {
        e.stopPropagation();
        var $row = $(this).closest('tr');
        var filePath = $(this).data('path');
        var type = $('#qualityDocsContainer').data('type') || 'Final Inspection';

        if (confirm('Are you sure you want to delete this document?')) {
            $.ajax({
                url: '{{ route("deleteQualityDoc") }}',
                type: 'POST',
                data: {
                    project_id: currentProjectId,
                    file_path: filePath,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('#qualityDocsTable tbody').empty();
                        loadQualityDocs(type);
                        alert(response.message);
                    }
                },
                error: function(xhr) {
                    alert('Failed to delete document');
                }
            });
        }
    });

    $(document).off('click', '.download-all-project-qr-codes').on('click', '.download-all-project-qr-codes', function(e) {
        e.stopPropagation();
        if (!currentProjectId) {
            console.error('Project ID is undefined');
            alert('Project ID is not available.');
            return;
        }

        const projectNo = $('#projectNoDisplay').text();
        if (!projectNo || projectNo === 'Project') {
            alert('Project number is not available.');
            return;
        }

        const filename = `${projectNo}_qr_codes.zip`;
        const downloadUrl = '{{ route("downloadAllProjectQrCodes", ["projectId" => "projectIdPlaceholder"]) }}'
            .replace('projectIdPlaceholder', currentProjectId);

        $.ajax({
            url: downloadUrl,
            type: 'GET',
            xhrFields: {
                responseType: 'blob'
            },
            success: function(data, status, xhr) {
                const link = document.createElement('a');
                const url = window.URL.createObjectURL(data);
                link.href = url;
                link.download = filename;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.URL.revokeObjectURL(url);
            },
            error: function(xhr) {
                let message = 'Failed to download QR codes';
                const contentType = xhr.getResponseHeader('Content-Type');
                if (contentType && contentType.includes('application/json') && xhr.responseText) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        message = response.message || message;
                    } catch (e) {
                        console.error('Error parsing JSON response:', xhr.responseText);
                    }
                } else {
                    console.error('Non-JSON response received:', contentType, xhr.responseText);
                }
                alert(message);
            }
        });
    });

    $(document).off('click', '.download-all-qr-codes').on('click', '.download-all-qr-codes', function(e) {
        e.stopPropagation();
        if (!currentProjectId || !currentArticleNumber) {
            console.error('Project ID or Article Number is undefined');
            alert('Project ID or Article Number is not available.');
            return;
        }

        const downloadUrl = '{{ route("downloadAllQrCodes", ["projectId" => "projectIdPlaceholder", "articleNumber" => "articleNumberPlaceholder"]) }}'
            .replace('projectIdPlaceholder', currentProjectId)
            .replace('articleNumberPlaceholder', currentArticleNumber);

        const link = document.createElement('a');
        link.href = downloadUrl;
        link.download = '';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });

    function loadDocuments(folderName, subfolderName = null) {
        console.log('Project ID in loadDocuments:', currentProjectId);
        if (!currentProjectId) {
            console.error('Project ID is undefined');
            return;
        }

        $.ajax({
            url: '{{ route("getProjectDocuments") }}',
            type: 'GET',
            data: {
                id: currentProjectId,
                folder: folderName,
                subfolder: subfolderName
            },
            success: function(response) {
                if (!response.success) {
                    $('#foldersList').hide();
                    $('#documentsList').empty().show();
                    $('#documentsList').append('<li class="list-group-item">No documents available</li>');
                    return;
                }

                if (!response.documents || response.documents.length === 0) {
                    $('#foldersList').hide();
                    $('#documentsList').empty().show();
                    $('#documentsList').append('<li class="list-group-item">No documents available</li>');
                    return;
                }

                $('#foldersList').hide();
                $('#documentsList').empty().show();

                $.each(response.documents, function(index, document) {
                    var fileExtension = document.name.split('.').pop().toLowerCase();
                    var isImage = ['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension);

                    if (isImage) {
                        $('#documentsList').append(`
                            <li class="list-group-item d-flex align-items-center">
                                <a href="{{ asset('') }}${document.path}" target="_blank" class="d-flex align-items-center text-decoration-none">
                                    <img src="{{ asset('') }}${document.path}" alt="${document.name}" style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                                    <span>${document.name}</span>
                                </a>
                            </li>
                        `);
                    } else {
                        var iconClass = getIconClass(fileExtension);
                        $('#documentsList').append(`
                            <li class="list-group-item">
                                <a href="{{ asset('') }}${document.path}" target="_blank">
                                    <i class="${iconClass}"></i> ${document.name}
                                </a>
                            </li>
                        `);
                    }
                });
            },
            error: function(xhr) {
                alert('Failed to fetch documents.');
            }
        });
    }

    function loadWIPPhotos() {
        if (!currentProjectId) {
            console.error('Project ID is undefined');
            return;
        }

        $.ajax({
            url: '{{ route("getWIPPhotos") }}',
            type: 'GET',
            data: {
                id: currentProjectId
            },
            success: function(response) {
                console.log('WIP Photos Response:', response);
                if (!response.success) {
                    alert(response.message || 'Failed to load WIP photos');
                    return;
                }

                if (!response.photos || response.photos.length === 0) {
                    $('#foldersList').hide();
                    $('#WIPPhotosList').empty().show();
                    $('#WIPPhotosList').append('<li class="list-group-item">No WIP photos found for this project</li>');
                    if (response.debug) {
                        console.log('Debug Info:', response.debug);
                    }
                    return;
                }

                $('#foldersList').hide();
                $('#WIPPhotosList').empty().show();

                $.each(response.photos, function(index, photo) {
                    var fileExtension = photo.name.split('.').pop().toLowerCase();
                    var isImage = ['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension);

                    if (isImage) {
                        $('#WIPPhotosList').append(`
                            <li class="list-group-item d-flex align-items-center">
                                <a href="{{ asset('') }}${photo.path}" target="_blank" class="d-flex align-items-center text-decoration-none">
                                    <img src="{{ asset('') }}${photo.path}" alt="${photo.name}" style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                                    <span>${photo.name}</span>
                                </a>
                            </li>
                        `);
                    } else {
                        var iconClass = getIconClass(fileExtension);
                        $('#WIPPhotosList').append(`
                            <li class="list-group-item">
                                <a href="{{ asset('') }}${photo.path}" target="_blank">
                                    <i class="${iconClass}"></i> ${photo.name}
                                </a>
                            </li>
                        `);
                    }
                });

                if (response.debug) {
                    console.log('Debug Info:', response.debug);
                }
            },
            error: function(xhr) {
                console.error('AJAX Error:', xhr.responseText);
                alert('Failed to fetch WIP photos.');
            }
        });
    }

    function loadAllBOM(folderName) {
        if (!currentProjectId) {
            console.error('Project ID is undefined');
            return;
        }

        $.ajax({
            url: '{{ route("getProjectsBOM") }}',
            type: 'GET',
            data: {
                projectId: currentProjectId,
                folder: folderName
            },
            success: function(response) {
                if (!response.allProductsBOM || response.allProductsBOM.length === 0) {
                    alert('No BOM data found');
                    return;
                }
                if (response.allProductsBOM) {
                    displayBOMData(response.allProductsBOM);
                }
            },
            error: function(xhr) {
                alert('Failed to fetch BOM.');
            }
        });
    }

    function loadAllDrawings(folderName, subsubfolderName = null) {
        if (!currentProjectId) {
            console.error('Project ID is undefined');
            alert('Project ID is not available.');
            return;
        }

        $.ajax({
            url: '{{ route("getProjectsDrawings") }}',
            type: 'GET',
            data: {
                projectId: currentProjectId,
                folder: folderName,
                subsubfolder: subsubfolderName
            },
            success: function(response) {
                if (!response.allProductsDrawings || response.allProductsDrawings.length === 0) {
                    $('#DrawingsList').empty().show();
                    $('#DrawingsList').append('<li class="list-group-item">No drawings data found for ' + (subsubfolderName || 'Drawings') + '</li>');
                    return;
                }
                if (response.allProductsDrawings) {
                    displayDrawingsData(response.allProductsDrawings, subsubfolderName);
                }
            },
            error: function(xhr) {
                alert('Failed to fetch drawings for ' + (subsubfolderName || 'Drawings') + '.');
            }
        });
    }

    function loadProjectExecutionImageList() {
        if (!currentProjectId) {
            console.error('Project ID is undefined');
            return;
        }

        $.ajax({
            url: '{{ route("getProjectExecutionImageList", ["projectId" => "projectIdPlaceholder"]) }}'.replace('projectIdPlaceholder', currentProjectId),
            type: 'GET',
            success: function(response) {
                if (!response.imageList || response.imageList.length === 0) {
                    $('#executionImagesListTable tbody').empty();
                    $('#executionImagesListTable tbody').append('<tr><td colspan="5">No image data found</td></tr>');
                    return;
                }
                $('#foldersList').hide();
                $('#executionImagesListContainer').show();
                $('#executionImagesQtyListContainer').hide();
                $('#executionImagesContainer').hide();
                $('#executionImagesListTable tbody').empty();

                $.each(response.imageList, function(index, item) {
                    $('#executionImagesListTable tbody').append(`
                        <tr>
                            <td>${item.cart_model_name || 'N/A'}</td>
                            <td>${item.description || 'N/A'}</td>
                            <td>${item.full_article_number}</td>
                            <td>${item.qty}</td>
                            <td>
                                <button class="btn btn-sm btn-primary m-auto d-flex px-2 view-execution-images" data-article="${item.full_article_number}" data-qty="${item.qty}">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    `);
                });
            },
            error: function(xhr) {
                alert('Failed to fetch image list');
            }
        });
    }

    function loadProjectExecutionQtyList(articleNumber, qty) {
        if (!currentProjectId || !articleNumber || !qty) {
            console.error('Project ID, Article Number, or Qty is undefined');
            return;
        }

        $('#foldersList').hide();
        $('#executionImagesListContainer').hide();
        $('#executionImagesContainer').hide();
        $('#executionImagesQtyListContainer').show();
        $('#executionImagesQtyListTable tbody').empty();

        for (let i = 1; i <= qty; i++) {
            $('#executionImagesQtyListTable tbody').append(`
                <tr>
                    <td>${i}</td>
                    <td>
                        <button class="btn btn-sm btn-primary m-auto d-flex px-2 view-execution-images-qty" data-article="${articleNumber}" data-qty-no="${i}">
                            <i class="fa fa-eye"></i>
                        </button>
                    </td>
                </tr>
            `);
        }

        $.ajax({
            url: '{{ route("getProjectById", ["id" => "projectIdPlaceholder"]) }}'.replace('projectIdPlaceholder', currentProjectId),
            type: 'GET',
            success: function(projectResponse) {
                if (projectResponse.success) {
                    const projectNo = projectResponse.project.project_no;
                    const path = `${projectNo}/${articleNumber}`;
                    $('#imagePathDisplay').text(path);
                }
            },
            error: function(xhr) {
                console.error('Failed to fetch project details:', xhr.responseText);
            }
        });
    }

    function loadProjectExecutionImages() {
        if (!currentProjectId || !currentArticleNumber || !currentQtyNo) {
            console.error('Project ID, Article Number, or Qty No is undefined');
            alert('Project ID, Article Number, or Qty No is not available.');
            return;
        }

        $.ajax({
            url: '{{ route("getProjectExecutionImages", ["projectId" => "projectIdPlaceholder", "articleNumber" => "articleNumberPlaceholder", "qtyNo" => "qtyNoPlaceholder"]) }}'
                .replace('projectIdPlaceholder', currentProjectId)
                .replace('articleNumberPlaceholder', currentArticleNumber)
                .replace('qtyNoPlaceholder', currentQtyNo),
            type: 'GET',
            success: function(response) {
                if (!response.success) {
                    $('#executionImagesTable tbody').empty();
                    $('#executionImagesTable tbody').append('<tr><td align="center" colspan="2">No images found</td></tr>');
                    return;
                }

                $('#executionImagesListContainer').hide();
                $('#executionImagesQtyListContainer').hide();
                $('#executionImagesContainer').show();
                $('#executionImagesTable tbody').empty();

                $.ajax({
                    url: '{{ route("getProjectById", ["id" => "projectIdPlaceholder"]) }}'.replace('projectIdPlaceholder', currentProjectId),
                    type: 'GET',
                    success: function(projectResponse) {
                        if (projectResponse.success) {
                            const projectNo = projectResponse.project.project_no;
                            const path = `${projectNo}/${currentArticleNumber}/${currentQtyNo}`;
                            $('#imagePathDisplay').text(path);
                        }
                    },
                    error: function(xhr) {
                        console.error('Failed to fetch project details:', xhr.responseText);
                    }
                });

                if (response.images.length === 0) {
                    $('#executionImagesTable tbody').append('<tr><td align="center" colspan="2">No images found</td></tr>');
                } else {                  
                    $.each(response.images, function(index, image) {
                        let rowHtml = `
                            <tr data-path="${image.path}">
                                <td>
                                    <a href="{{ asset('') }}${image.path}" target="_blank" class="d-flex align-items-center text-decoration-none">
                                        <img src="{{ asset('') }}${image.path}" alt="${image.name}" style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                                        <span>${image.name}</span>
                                    </a>
                                </td>`;

                        // Show delete button only if Quality role
                        if (isQuality) {
                            rowHtml += `
                                <td>
                                    <button class="btn btn-danger d-flex m-auto btn-sm delete-execution-image" data-path="${image.path}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>`;
                        }
                        rowHtml += `</tr>`;
                        $('#executionImagesTable tbody').append(rowHtml);
                    });
                }
            },
            error: function(xhr) {
                $('#executionImagesTable tbody').empty();
                $('#executionImagesTable tbody').append('<tr><td align="center" colspan="2">No images found</td></tr>');
            }
        });
    }

    function loadProjectExecutionWorkOrdersList() {
        if (!currentProjectId) {
            console.error('Project ID is undefined');
            return;
        }

        $.ajax({
            url: '{{ route("getProjectExecutionWorkOrdersList", ["projectId" => "projectIdPlaceholder"]) }}'.replace('projectIdPlaceholder', currentProjectId),
            type: 'GET',
            success: function(response) {
                if (!response.workOrdersList || response.workOrdersList.length === 0) {
                    $('#executionWorkOrdersListTable tbody').empty();
                    $('#executionWorkOrdersListTable tbody').append('<tr><td colspan="4">No Work Orders data found</td></tr>');
                    return;
                }
                $('#foldersList').hide();
                $('#executionWorkOrdersListContainer').show();
                $('#executionWorkOrdersListTable tbody').empty();

                $.each(response.workOrdersList, function(index, item) {
                    $('#executionWorkOrdersListTable tbody').append(`
                        <tr>
                            <td>${item.cart_model_name || 'N/A'}</td>
                            <td>${item.description || 'N/A'}</td>
                            <td>${item.full_article_number}</td>
                            <td>
                                <button class="btn btn-sm btn-primary m-auto d-flex px-2 view-execution-work-orders" data-article="${item.full_article_number}">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    `);
                });
            },
            error: function(xhr) {
                alert('Failed to fetch Work Orders list');
            }
        });
    }

    function loadQualityFinalInspectionList() {
        if (!currentProjectId) {
            console.error('Project ID is undefined');
            return;
        }

        $.ajax({
            url: '{{ route("getProjectExecutionImageList", ["projectId" => "projectIdPlaceholder"]) }}'.replace('projectIdPlaceholder', currentProjectId),
            type: 'GET',
            success: function(response) {
                if (!response.imageList || response.imageList.length === 0) {
                    $('#qualityFinalInspectionListTable tbody').empty();
                    $('#qualityFinalInspectionListTable tbody').append('<tr><td colspan="5">No Final Inspection data found</td></tr>');
                    return;
                }
                $('#foldersList').hide();
                $('#qualityFinalInspectionListContainer').show();
                $('#qualityFinalInspectionQtyListContainer').hide();
                $('#qualityDocsContainer').hide();
                $('#qualityFinalInspectionListTable tbody').empty();

                $.each(response.imageList, function(index, item) {
                    $('#qualityFinalInspectionListTable tbody').append(`
                        <tr>
                            <td>${item.cart_model_name || 'N/A'}</td>
                            <td>${item.description || 'N/A'}</td>
                            <td>${item.full_article_number}</td>
                            <td>${item.qty}</td>
                            <td>
                                <button class="btn btn-sm btn-primary m-auto d-flex px-2 view-quality-final-inspection" data-article="${item.full_article_number}" data-qty="${item.qty}">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    `);
                });
            },
            error: function(xhr) {
                alert('Failed to fetch Final Inspection list');
            }
        });
    }

    function loadQualityTestReportsList() {
        if (!currentProjectId) {
            console.error('Project ID is undefined');
            return;
        }

        $.ajax({
            url: '{{ route("getProjectExecutionImageList", ["projectId" => "projectIdPlaceholder"]) }}'.replace('projectIdPlaceholder', currentProjectId),
            type: 'GET',
            success: function(response) {
                if (!response.imageList || response.imageList.length === 0) {
                    $('#qualityTestReportsListTable tbody').empty();
                    $('#qualityTestReportsListTable tbody').append('<tr><td colspan="5">No Test Reports data found</td></tr>');
                    return;
                }
                $('#foldersList').hide();
                $('#qualityTestReportsListContainer').show();
                $('#qualityTestReportsQtyListContainer').hide();
                $('#qualityDocsContainer').hide();
                $('#qualityTestReportsListTable tbody').empty();

                $.each(response.imageList, function(index, item) {
                    $('#qualityTestReportsListTable tbody').append(`
                        <tr>
                            <td>${item.cart_model_name || 'N/A'}</td>
                            <td>${item.description || 'N/A'}</td>
                            <td>${item.full_article_number}</td>
                            <td>${item.qty}</td>
                            <td>
                                <button class="btn btn-sm btn-primary m-auto d-flex px-2 view-quality-test-reports" data-article="${item.full_article_number}" data-qty="${item.qty}">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    `);
                });
            },
            error: function(xhr) {
                alert('Failed to fetch Test Reports list');
            }
        });
    }

    function loadQualityQtyList(articleNumber, qty, type) {
        if (!currentProjectId || !articleNumber || !qty || !type) {
            console.error('Project ID, Article Number, Qty, or Type is undefined');
            return;
        }

        const containerId = type === 'Final Inspection' ? 'qualityFinalInspectionQtyListContainer' : 'qualityTestReportsQtyListContainer';
        const tableId = type === 'Final Inspection' ? 'qualityFinalInspectionQtyListTable' : 'qualityTestReportsQtyListTable';

        $('#foldersList').hide();
        $('#qualityFinalInspectionListContainer').hide();
        $('#qualityTestReportsListContainer').hide();
        $('#qualityDocsContainer').hide();
        $('#qualityFinalInspectionQtyListContainer').hide();
        $('#qualityTestReportsQtyListContainer').hide();
        $(`#${containerId}`).show();
        $(`#${tableId} tbody`).empty();

        for (let i = 1; i <= qty; i++) {
            $(`#${tableId} tbody`).append(`
                <tr>
                    <td>${i}</td>
                    <td>
                        <button class="btn btn-sm btn-primary m-auto d-flex px-2 view-quality-docs-qty" data-article="${articleNumber}" data-qty-no="${i}" data-type="${type}">
                            <i class="fa fa-eye"></i>
                        </button>
                    </td>
                </tr>
            `);
        }

        $.ajax({
            url: '{{ route("getProjectById", ["id" => "projectIdPlaceholder"]) }}'.replace('projectIdPlaceholder', currentProjectId),
            type: 'GET',
            success: function(projectResponse) {
                if (projectResponse.success) {
                    const projectNo = projectResponse.project.project_no;
                    const path = `${projectNo}/${articleNumber}`;
                    $('#qualityPathDisplay').text(path);
                }
            },
            error: function(xhr) {
                console.error('Failed to fetch project details:', xhr.responseText);
            }
        });
    }

    function loadQualityDocs(type) {
        if (!currentProjectId || !type || !currentArticleNumber || !currentQtyNo) {
            console.error('Project ID, Type, Article Number, or Qty No is undefined');
            alert('Required parameters are missing.');
            return;
        }

        let url = '{{ route("getQualityDocs", ["projectId" => "projectIdPlaceholder", "type" => "typePlaceholder", "articleNumber" => "articleNumberPlaceholder", "qtyNo" => "qtyNoPlaceholder"]) }}'
            .replace('projectIdPlaceholder', currentProjectId)
            .replace('typePlaceholder', type)
            .replace('articleNumberPlaceholder', currentArticleNumber)
            .replace('qtyNoPlaceholder', currentQtyNo);

        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                if (!response.success) {
                    alert(response.message || 'Failed to load documents');
                    return;
                }

                $('#foldersList').hide();
                $('#qualityFinalInspectionListContainer').hide();
                $('#qualityFinalInspectionQtyListContainer').hide();
                $('#qualityTestReportsListContainer').hide();
                $('#qualityTestReportsQtyListContainer').hide();
                $('#qualityDocsContainer').show().data('type', type);
                $('#qualityDocsTable tbody').empty();
                $('#noQualityDocsMessage').hide();

                if (response.documents.length === 0) {
                    $('#noQualityDocsMessage').show();
                } else {                   

                    $.each(response.documents, function(index, doc) {
                        var fileExtension = doc.name.split('.').pop().toLowerCase();
                        var iconClass = getIconClass(fileExtension);
                        let rowHtml = `
                            <tr data-path="${doc.path}">
                                <td>
                                    <a href="{{ asset('') }}${doc.path}" target="_blank" class="d-flex align-items-center text-decoration-none">
                                        <i class="${iconClass} mr-2"></i>
                                        <span>${doc.name}</span>
                                    </a>
                                </td>`;
                        // Show delete button only for Quality role
                        if (isQuality) {
                            rowHtml += `
                                <td>
                                    <button class="btn btn-danger btn-sm d-flex m-auto delete-quality-doc" data-path="${doc.path}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>`;
                        }
                        rowHtml += `</tr>`;
                        $('#qualityDocsTable tbody').append(rowHtml);
                    });

                }

                $.ajax({
                    url: '{{ route("getProjectById", ["id" => "projectIdPlaceholder"]) }}'.replace('projectIdPlaceholder', currentProjectId),
                    type: 'GET',
                    success: function(projectResponse) {
                        if (projectResponse.success) {
                            const projectNo = projectResponse.project.project_no;
                            const path = `${projectNo}/${currentArticleNumber}/${currentQtyNo}`;
                            $('#qualityPathDisplay').text(path);
                        }
                    },
                    error: function(xhr) {
                        console.error('Failed to fetch project details:', xhr.responseText);
                    }
                });
            },
            error: function(xhr) {
                alert('Failed to fetch documents');
            }
        });
    }

    function loadProjectExecutionDocs(type) {
        if (!currentProjectId) {
            console.error('Project ID is undefined');
            return;
        }

        if (type === 'Partial Order PL') {
            console.error('Invalid document type: Partial Order PL. Use loadProjectExecutionPartialOrderPLList instead.');
            alert('Invalid document type. Please select an item and quantity for Partial Order PL documents.');
            return;
        }

        let url = currentArticleNumber ?
            '{{ route("getProjectExecutionDocs", ["projectId" => "projectIdPlaceholder", "type" => "typePlaceholder", "articleNumber" => "articleNumberPlaceholder"]) }}'
            .replace('projectIdPlaceholder', currentProjectId)
            .replace('typePlaceholder', type)
            .replace('articleNumberPlaceholder', currentArticleNumber) :
            type === 'Work Orders' && currentArticleNumber ?
            '{{ route("getProjectExecutionDocs", ["projectId" => "projectIdPlaceholder", "type" => "typePlaceholder", "articleNumber" => "articleNumberPlaceholder"]) }}'
            .replace('projectIdPlaceholder', currentProjectId)
            .replace('typePlaceholder', type)
            .replace('articleNumberPlaceholder', currentArticleNumber) :
            '{{ route("getProjectExecutionDocs", ["projectId" => "projectIdPlaceholder", "type" => "typePlaceholder"]) }}'
            .replace('projectIdPlaceholder', currentProjectId)
            .replace('typePlaceholder', type);

        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                console.log('loadProjectExecutionDocs called with type:', type);
                if (!response.success) {
                    alert(response.message || 'Failed to load documents');
                    return;
                }

                $('#foldersList').hide();
                $('#executionWorkOrdersListContainer').hide();
                $('#executionDocsContainer').show().data('type', type);
                $('#executionDocsTable tbody').empty();
                $('#noDocsMessage').hide();

                if (response.documents.length === 0) {
                    $('#noDocsMessage').show();
                } else {
                    $.each(response.documents, function(index, doc) {
                        var fileExtension = doc.name.split('.').pop().toLowerCase();
                        var iconClass = getIconClass(fileExtension);
                        let rowHtml = `
                        <tr data-path="${doc.path}">
                            <td>
                                <a href="{{ asset('') }}${doc.path}" target="_blank" class="d-flex align-items-center text-decoration-none">
                                    <i class="${iconClass} mr-2"></i>
                                    <span>${doc.name}</span>
                                </a>
                            </td>`;


                        rowHtml += `</tr>`;
                        $('#executionDocsTable tbody').append(rowHtml);
                    });
                }

            },
            error: function(xhr) {
                alert('Failed to fetch documents');
                console.error('Failed to fetch documents:', xhr.responseText);
            }
        });
    }

    function loadQrCodeList() {
        if (!currentProjectId) {
            console.error('Project ID is undefined');
            alert('Project ID is not available.');
            return;
        }

        $.ajax({
            url: '{{ route("getProjectExecutionImageList", ["projectId" => "projectIdPlaceholder"]) }}'.replace('projectIdPlaceholder', currentProjectId),
            type: 'GET',
            success: function(response) {
                if (!response.imageList || response.imageList.length === 0) {
                    $('#qrCodesListTable tbody').empty();
                    $('#qrCodesListTable tbody').append('<tr><td colspan="5">No products found for QR codes</td></tr>');
                    $('#foldersList').hide();
                    $('#qrCodesListContainer').show();
                    return;
                }
                $('#foldersList').hide();
                $('#qrCodesListContainer').show();
                $('#qrCodeImagesContainer').hide();
                $('#qrCodesListTable tbody').empty();

                $.each(response.imageList, function(index, item) {
                    $('#qrCodesListTable tbody').append(`
                        <tr>
                            <td>${item.cart_model_name || 'N/A'}</td>
                            <td>${item.description || 'N/A'}</td>
                            <td>${item.full_article_number}</td>
                            <td>${item.qty}</td>
                            <td>
                                <button class="btn btn-sm btn-primary m-auto d-flex px-2 view-qr-codes" data-article="${item.full_article_number}" data-qty="${item.qty}">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    `);
                });

                $.ajax({
                    url: '{{ route("getProjectById", ["id" => "projectIdPlaceholder"]) }}'.replace('projectIdPlaceholder', currentProjectId),
                    type: 'GET',
                    success: function(projectResponse) {
                        if (projectResponse.success) {
                            const projectNo = projectResponse.project.project_no;
                            $('#qrCodeListPathDisplay').text(`${projectNo}/product QR`);
                            $('#projectNoDisplay').text(projectNo);
                        }
                    },
                    error: function(xhr) {
                        console.error('Failed to fetch project details:', xhr.responseText);
                        $('#projectNoDisplay').text('Project');
                    }
                });
            },
            error: function(xhr) {
                alert('Failed to fetch product list for QR codes');
                $('#qrCodesListContainer').hide();
            }
        });
    }

    function loadQrCodeImages(articleNumber, qty) {
        if (!currentProjectId || !articleNumber) {
            console.error('Project ID or Article Number is undefined');
            alert('Project ID or Article Number is not available.');
            return;
        }

        $.ajax({
            url: '{{ route("getProjectQrCodes", ["projectId" => "projectIdPlaceholder", "articleNumber" => "articleNumberPlaceholder"]) }}'
                .replace('projectIdPlaceholder', currentProjectId)
                .replace('articleNumberPlaceholder', articleNumber),
            type: 'GET',
            success: function(response) {
                if (!response.success) {
                    alert(response.message || 'No QR codes found');
                    $('#qrCodeImagesTable tbody').empty();
                    $('#noQrCodesMessage').show();
                    $('#qrCodesListContainer').hide();
                    $('#qrCodeImagesContainer').show();
                    return;
                }

                $('#qrCodesListContainer').hide();
                $('#qrCodeImagesContainer').show();
                $('#qrCodeImagesTable tbody').empty();
                $('#noQrCodesMessage').hide();

                if (response.qrCodes.length === 0) {
                    $('#noQrCodesMessage').show();
                } else {
                    $.each(response.qrCodes, function(index, qrCode) {
                        $('#qrCodeImagesTable tbody').append(`
                            <tr data-path="${qrCode.path}">
                                <td class="qr_image_center">
                                    <a href="{{ asset('') }}${qrCode.path}" target="_blank" class="d-flex align-items-center text-decoration-none">
                                        <img src="{{ asset('') }}${qrCode.path}" alt="${qrCode.name}" style="width: 50px; height: 50px; object-fit: cover;">
                                    </a>
                                </td>
                                <td>${qrCode.name}</td>
                                <td>
                                    <a href="{{ asset('') }}${qrCode.path}" download class="btn btn-sm btn-qr-downlaod btn-primary d-flex m-auto">
                                        <i class="fa fa-download"></i>
                                    </a>
                                </td>
                            </tr>
                        `);
                    });
                }

                $.ajax({
                    url: '{{ route("getProjectById", ["id" => "projectIdPlaceholder"]) }}'.replace('projectIdPlaceholder', currentProjectId),
                    type: 'GET',
                    success: function(projectResponse) {
                        if (projectResponse.success) {
                            const projectNo = projectResponse.project.project_no;
                            const path = `${projectNo}/product QR/${articleNumber}`;
                            $('#qrCodePathDisplay').text(path);
                            $('.download-all-qr-codes').text(`Download all QR codes for ${projectNo} - ${articleNumber}`);
                        }
                    },
                    error: function(xhr) {
                        console.error('Failed to fetch project details:', xhr.responseText);
                        $('.download-all-qr-codes').text('Download all QR codes');
                    }
                });
            },
            error: function(xhr) {
                alert('Failed to fetch QR codes');
                $('#qrCodeImagesContainer').hide();
            }
        });
    }

    function displayBOMData(bomData) {
        $('#foldersList').hide();
        $('#documentsList').hide();
        $('#BOMList').empty().show();

        let tableHtml = `
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Cart Model</th>
                            <th>Item Description</th>
                            <th>Article Number</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        const baseUrl = $('meta[name="app-url"]').attr('content') || window.location.origin;
        bomData.forEach(item => {
            let actionColumn;

            if (!item.bom_path) {
                if (item.bom_req_estimation_manager === "1") {
                    actionColumn = `<span class="badge badge-warning">Requested to Estimation Manager</span>`;
                } else {
                    actionColumn = `
                        <button class="btn btn-sm btn-danger text-white request-bom"
                                data-article="${item.full_article_number}"
                                data-project="${item.project_id}">
                            Requested
                        </button>`;
                }
            } else {
                const fullPath = `${baseUrl}/public/${item.bom_path}`;
                actionColumn = `
                    <a href="${fullPath}" class="btn btn-sm project_icon text-white" download>
                        <i class="fa fa-download"></i> BOM Download
                    </a>`;
            }

            tableHtml += `
                <tr>
                    <td>${item.cart_model_name || 'N/A'}</td>
                    <td>${item.description || 'N/A'}</td>
                    <td>${item.full_article_number}</td>
                    <td>${actionColumn}</td>
                </tr>
            `;
        });

        tableHtml += `
                    </tbody>
                </table>
            </div>
        `;

        $('#BOMList').html(tableHtml);

        $('.request-bom').click(function() {
            const articleNumber = $(this).data('article');
            const projectId = $(this).data('project');
            requestBOM(articleNumber, projectId);
        });
    }

    function displayDrawingsData(drawingsData, subsubfolderName) {
        $('#foldersList').hide();
        $('#documentsList').hide();
        $('#BOMList').hide();
        $('#DrawingsList').empty().show();

        let tableHtml = `
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Cart Model</th>
                            <th>Item Description</th>
                            <th>Article Number</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

        drawingsData.forEach(item => {
            let actionColumn;
            if (item.drawing_path) {
                const downloadUrl = '{{ route("downloadDrawing", ["projectId" => ":projectId", "articleNumber" => ":articleNumber", "type" => ":type"]) }}'
                    .replace(':projectId', item.project_id)
                    .replace(':articleNumber', encodeURIComponent(item.full_article_number))
                    .replace(':type', encodeURIComponent(subsubfolderName || 'Estimation Manager Upload Drawing'));
                actionColumn = `
                <a href="${downloadUrl}" class="btn btn-sm project_icon text-white" download>
                    <i class="fa fa-download"></i> Download
                </a>`;
            } else {
                actionColumn = `<span class="badge badge-warning">No ${subsubfolderName || 'Drawing'} Available</span>`;
            }

            tableHtml += `
            <tr>
                <td>${item.cart_model_name || 'N/A'}</td>
                <td>${item.description || 'N/A'}</td>
                <td>${item.full_article_number}</td>
                <td>${actionColumn}</td>
            </tr>
        `;
        });

            tableHtml += `
                    </tbody>
                </table>
            </div>
        `;

        $('#DrawingsList').html(tableHtml);
    }

    function getIconClass(fileExtension) {
        if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
            return "fa fa-image";
        } else if (fileExtension === 'pdf') {
            return "fa fa-file-pdf";
        } else if (['doc', 'docx'].includes(fileExtension)) {
            return "fa fa-file-word";
        } else if (['xlsx', 'csv'].includes(fileExtension)) {
            return "fa fa-file-excel";
        } else {
            return "fa fa-file";
        }
    }
</script>

<script type="text/javascript">
    $(".project_search_btn").on('click', function() {
        var status = $(".project_status_dd").val();
        $.ajax({
            url: "{{route('ProductionManagerProjectIndex')}}",
            type: 'GET',
            data: {
                status: status
            },
            success: function(response) {
                $("#project_table_body").html(response.html);
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });
    
    // update Total Price
    $(document).on('input', '.editable-qty, .edit_qty, .edit_unit_price', function() {
        var $row = $(this).closest('tr');
        var index = $(this).data('index');

        // Get QTY
        var qty = parseFloat($row.find('.editable-qty').val() || $row.find('.edit_qty').val()) || 0;

        // Get UNIT PRICE
        var unitPrice = parseFloat($row.find('.edit_unit_price').val() || $row.find('.editunitprice').val() || $row.find('td').eq(9).text()) || 0;

        // Calculate TOTAL
        var totalPrice = (qty * unitPrice).toFixed(2);

        // Show live total in visible input/cell
        if ($row.find('.edit_total_price').length) {
            $row.find('.edit_total_price').val(totalPrice);
        } else {
            $row.find('td').eq(10).text(totalPrice);
        }

        // Update hidden total and unit price fields
        $(`.hidden-total_price-${index}`).val(totalPrice);
        $(`.hidden-unit_price-${index}`).val(unitPrice);
    });
</script>

<script type="text/javascript">
    $(".project_priority_dd, .project_status_dd").on('change', function() {
        var priority = $(".project_priority_dd").val();
        var status = $(".project_status_dd").val();

        $.ajax({
            url: "{{ route('ProductionManagerProjectIndex') }}",
            type: 'GET',
            data: {
                priority: priority,
                status: status
            },
            success: function(response) {
                $("#project_table_body").html(response.html);
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });

    // A Code: 10-01-2026 Start
    function openDeleteProjectReasonModal(projectId) {
        $('#deleteProjectId').val(projectId);
        $('#deletationReason').val('');
        let deleteProjectModal = new bootstrap.Modal(document.getElementById('deleteProjectReasonModal'));
        deleteProjectModal.show();
    }

    $('#confirmDeleteProject').on('click', function () {

        const projectId = $('#deleteProjectId').val();
        const reason    = $('#deletationReason').val().trim();

        if (!reason) {
            alert('Please enter delete reason.');
            return;
        }

        $('#rejectLoader').removeClass('d-none');
        $('#confirmDeleteProject').prop('disabled', true);        

        $.ajax({
            url: "{{ route('ProductionManagerProjectDelete', '') }}/" + projectId,
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            data: {
                reason: reason
            },
            success: function (res) {
                alert(res.message);
                location.reload();
            },
            error: function (xhr) {

                let message = 'Delete failed. Please try again.';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                alert(message);
                location.reload();
            },
            complete: function () {
                $('#rejectLoader').addClass('d-none');
                $('#confirmDeleteProject').prop('disabled', false);
            }
        });
    });
    // A Code: 10-01-2026 End
    
</script>
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->

@endsection