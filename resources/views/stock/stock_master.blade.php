@extends('layouts.main')
@section('content')
@php
$role = Auth::user()->role;
@endphp

<link rel="stylesheet" href="{{ asset('css/stock.css') }}" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css" />

<div class="stock_master_page main_section bg-white m-4 pb-5 pt-3 px-3 stock_bg">
    <div class="d-flex justify-content-between px-4 pt-4">
        <h1>{{ $page_title }}</h1>
        
        <div class="mb-4 d-flex justify-content-end">          
            <button type="button" class="btn btn-primary mr-2" onclick="download_csv();">
                <i class="fas fa-download"></i> Export
            </button> 
            <a href="{{route('Stock')}}" class="btn btn-primary stock_reset_btn mr-2">              
                <i class="fas fa-sync-alt"></i> Reset
            </a>    

            @if($role == "Admin" || Auth::user()->is_admin_login) <!-- A Code: 22-12-2025 -->
            {{--<button class="btn btn-success import-button mr-2" data-toggle="modal" data-target="#importStockModal">
                <i class="fas fa-file-import mr-1"></i> Import
            </button>--}}
            <button class="btn btn-primary add-button" data-toggle="modal" data-target="#addStockModal">
                <i class="fas fa-plus mr-1"></i> Add Stock
            </button>
            @endif
        </div>        
    </div>
    <div class="container-fluid px-5">
        <div class="row mt-3">
            <div class="w-100">
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif
                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                @endif

                <form action="{{ route('StockFilter') }}" method="POST" id="filter_form">
                @csrf   
                    <div class="table-responsive">                 
                        <table class="table table-hover table-bordered text-center" id="stock_table">
                            <thead>
                                @include('stock.stock_master_head', ['stocks' => $stocks])
                            </thead>
                            <tbody>
                                @php $sr_no = 1; @endphp
                                @foreach($stocks as $stock)
                                <tr>
                                    <td>{{ $sr_no }}</td>
                                    <td>{{ $stock->article_number }}</td>
                                    <td>{{ $stock->item_desc }}</td>
                                    <td>{{ $stock->qty }}</td>
                                    <td>{{ $stock->hold_qty }}
                                        @if($stock->hold_qty > 0)
                                        <button type="button" class="project_check_status primary_bg_color text-white p-2 view_hold_qty" data-toggle="modal" data-target="#hold_qtyShowModal" data-article_number="{{$stock->article_number}}" data-item_desc="{{$stock->item_desc}}">
                                            <i class="fa fa-eye primary_bg_color"></i>
                                        </button>
                                        @endif
                                    </td>
                                    <td>{{ $stock->available_qty }}</td>
                                    <td>{{ $stock->minimum_required_qty }}</td>
                                    <td>{{ $stock->std_time }}</td>
                                    
                                    <td>
                                        <span class="ml-2 total-po-qty text-black" data-article_number="{{$stock->article_number}}">    
                                            {{ $stock->total_po_qty ?? 0 }}
                                        </span>
                                        @if($stock->total_po_qty > 0)
                                        <button type="button" class="project_check_status primary_bg_color text-white p-2 view_qty_in_order" data-article_number="{{$stock->article_number}}" data-item_desc="{{$stock->item_desc}}">
                                            <i class="fa fa-eye primary_bg_color"></i>
                                        </button>
                                        @endif
                                        @if(isset($stock->po_no))
                                            @if($stock->po_no != 'N/A')
                                            @else
                                                N/A
                                            @endif
                                        @endif
                                    </td>
                                    
                                   
                                    @if($role =="Admin" || Auth::user()->is_admin_login) <!-- A Code: 17-12-2025 -->
                                    <td class="action">
                                        <button type="button" class="btn btn-primary btn-sm edit-button edit_stock"
                                            data-id="{{ $stock->id }}"
                                            data-article_number="{{ $stock->article_number }}"
                                            data-item_desc="{{ $stock->item_desc }}"
                                            data-qty="{{ $stock->qty }}"
                                            data-hold_qty="{{ $stock->hold_qty }}"
                                            data-available_qty="{{ $stock->available_qty }}"
                                            data-minimum_required_qty="{{ $stock->minimum_required_qty }}"
                                            data-std_time="{{ $stock->std_time }}">
                                            Edit
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm delete-button" onclick="openDeleteModal({{ $stock->id }})">Delete</button>
                                    </td>
                                    @endif
                                </tr>
                                @php $sr_no++; @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <input type="hidden" name="last_filter_column" id="last_filter_column">
                </form>
            </div>
        </div>

    </div>

    <!-- Add Stock Modal -->
    <div class="modal fade stockModal" id="addStockModal" tabindex="-1" aria-labelledby="addStockModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header text-white edit-model-header">
                    <h5 class="modal-title" id="addStockModalLabel">Add Stock</h5>
                </div>
                <form id="addStockForm" method="POST" action="{{ route('StockMaster.Stock.store') }}">
                    <div class="modal-body">
                        @csrf
                        <div class="mb-3">
                            <label for="add_item_desc" class="form-label">Item Description <span class="text-danger">*</span></label>
                            <textarea name="item_desc" class="form-control" id="add_item_desc" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="add_article_number" class="form-label">Article Number <span class="text-danger">*</span></label>
                            <input type="text" name="article_number" class="form-control" id="add_article_number" required>
                        </div>
                        <div class="mb-3">
                            <label for="add_qty" class="form-label">Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="qty" class="form-control" id="add_qty" required>
                        </div>
                        <div class="mb-3">
                            <label for="add_minimum_required_qty" class="form-label">Minimum Required Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="minimum_required_qty" class="form-control" id="add_minimum_required_qty" required>
                        </div>
                        <div class="mb-3">
                            <label for="add_etd_std_weeks" class="form-label">ETA STD Weeks <span class="text-danger">*</span></label>
                            <input type="number" name="std_time" class="form-control" id="add_etd_std_weeks" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary cancle-button" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Stock</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Stock Modal -->
    <div class="modal fade stockModal" id="editStockModal" tabindex="-1" aria-labelledby="editStockModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header text-white edit-model-header">
                    <h5 class="modal-title" id="editStockModalLabel">Edit Stock</h5>
                </div>
                <form id="editStockForm" method="POST">
                    <div class="modal-body">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" id="edit_stock_id">
                        <div class="mb-3">
                            <label for="edit_item_desc" class="form-label">Item Description <span class="text-danger">*</span></label>
                            <textarea name="item_desc" class="form-control" id="edit_item_desc" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_article_number" class="form-label">Article Number <span class="text-danger">*</span></label>
                            <input type="text" name="article_number" class="form-control" id="edit_article_number" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Qty</label>
                                <input type="text" name="qty" class="form-control" id="edit_qty" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Reserved [WIP] Qty</label>
                                <input type="text" name="hold_qty" class="form-control" id="edit_hold_qty" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Warehouse Qty</label>
                                <input type="text" name="available_qty" class="form-control" id="edit_available_qty" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Add Qty</label>
                                <input type="number" name="added_qty" class="form-control" id="added_qty">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_minimum_required_qty" class="form-label">Minimum Required Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="minimum_required_qty" class="form-control" id="edit_minimum_required_qty" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_etd_std_weeks" class="form-label">ETA STD Weeks <span class="text-danger">*</span></label>
                            <input type="number" name="std_time" class="form-control" id="edit_etd_std_weeks" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary cancle-button" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Stock</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Stock Modal -->
    <div class="modal fade stockModal" id="deleteStockModal" tabindex="-1" aria-labelledby="deleteStockModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title" id="deleteStockModalLabel">Confirm Deletion</h5>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this stock?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary cancle-button" data-dismiss="modal">Cancel</button>
                    <form id="deleteStockForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger bg-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Hold Qty Show Modal -->
    <div class="modal fade stockModal" id="hold_qtyShowModal" tabindex="-1" aria-labelledby="hold_qtyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header text-white edit-model-header">
                    <h5 class="modal-title" id="hold_qtyModalLabel">Reserved [Hold] Quantity</h5>
                </div>
                <div class="modal-body">

                    <table class="table table-bordered" id="hold_qtyTable" border="1">
                        <thead>
                            <tr>
                                <th>Project No</th>
                                <th>Hold Qty</th>
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

    <!-- Qty In Order Show Modal -->
    <div class="modal fade stockModal" id="qtrInOrderShowModal" tabindex="-1" aria-labelledby="qty_in_orderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header text-white edit-model-header">
                    <h5 class="modal-title" id="qty_in_orderModalLabel">Qty In Order</h5>
                </div>
                <div class="modal-body">
                    <div class="mt-3 text-right">
                        <strong>Total PO Qty: <span id="total_po_qty">0</span></strong>
                    </div>
                    <table class="table table-bordered" id="qty_in_orderTable" border="1">
                        <thead>
                            <tr>
                                <th>PO NO.</th>
                                <th>Project No</th>
                                <th>Project Name</th>
                                <th>Item Description</th>
                                <th>Item Article No</th>
                                <th>PO Qty</th>
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

    <!-- Import Stock Modal -->
    <div class="modal fade stockModal" id="importStockModal" tabindex="-1" aria-labelledby="importStockModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h5 class="modal-title" id="importStockModalLabel">Import Stock from Excel</h5>
                </div>
                <form id="importStockForm" method="POST" action="{{ route('StockMaster.Stock.import') }}" enctype="multipart/form-data">
                    <div class="modal-body">
                        @csrf
                        <div class="mb-3">
                            <label for="excel_file" class="form-label">Upload Excel File <span class="text-danger">*</span></label>
                            <div class="custom-file-upload">
                                <input type="file" name="excel_file" class="form-control" id="excel_file" accept=".xlsx, .xls" required>
                                <label for="excel_file" class="file-upload-label">
                                    <i class="fas fa-upload mr-2"></i> Choose Excel File
                                </label>
                                <span class="file-name" id="file-name">No file chosen</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary cancle-button" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script>
    // Display
    $(document).ready(function() {
        $('#stock_table').DataTable({
            paging: true,
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            searching: true,
            ordering: false,
            info: false,
            deferRender: true,           // only renders rows that are visible
            processing: true,            // show processing indicator
            stateSave: true,             // remember table state (page, search, etc.)
            scrollCollapse: true         // allows the table to collapse when data is small
        });
        $('#stock_table').removeClass('dataTable');
    });

    // Edit
    $(document).on('click', '.edit_stock', function() {
        let $this = $(this);
        $('#edit_stock_id').val($this.data('id'));
        $('#edit_article_number').val($this.data('article_number'));
        $('#edit_item_desc').val($this.data('item_desc'));
        $('#edit_qty').val($this.data('qty'));
        $('#edit_hold_qty').val($this.data('hold_qty'));
        $('#edit_available_qty').val($this.data('available_qty'));
        $('#edit_minimum_required_qty').val($this.data('minimum_required_qty'));
        $('#edit_etd_std_weeks').val($this.data('std_time'));
        let updateUrl = '/Stock/update/' + $this.data('id');
        $('#editStockForm').attr('action', updateUrl);
        $('#editStockModal').modal('show');
    });

    // Delete
    function openDeleteModal(stockId) {
        let formAction = "{{ route('StockMaster.Stock.destroy', ':id') }}";
        formAction = formAction.replace(':id', stockId);
        document.getElementById('deleteStockForm').action = formAction;

        let deleteModal = new bootstrap.Modal(document.getElementById('deleteStockModal'));
        deleteModal.show();
    }

    // View Hold Qty
    $(document).on('click', '.view_hold_qty', function() {
        const csrfToken = "{{ csrf_token() }}";
        var articleNumber = $(this).data('article_number');
        var itemDesc = $(this).data('item_desc');

        $.ajax({
            url: "{{route('showHideQty')}}",
            method: 'POST',
            data: {
                _token: csrfToken,
                article_number: articleNumber,
                item_desc: itemDesc
            },
            success: function(response) {
                if (response.length > 0) {
                    $('#hold_qtyTable tbody').empty();

                    response.forEach(row => {
                        console.log(row);
                        let hold_qty = row.hold_qty || 0;
                        let project_no = row.projects?.project_no || 'N/A';

                        let rowHtml = `
                        <tr>
                            <td>${project_no}</td>
                            <td>${hold_qty}</td>
                        </tr>
                    `;

                        $('#hold_qtyTable tbody').append(rowHtml);
                    });

                } else {
                    $('#hold_qtyTable tbody').html(`
                    <tr>
                        <td colspan="2" class="text-center">No data available</td>
                    </tr>
                `);
                }


            },
            error: function() {
                alert('Error loading project details.');
            }
        });
    });

    // View Qty In Order
    $(document).on('click', '.view_qty_in_order', function () {
        const csrfToken = "{{ csrf_token() }}";
        var articleNumber = $(this).data('article_number');
        var itemDesc = $(this).data('item_desc');
        
        const clickedButton = $(this); // capture clicked button for later use

        $.ajax({
            url: "{{ route('showQtyInOrder') }}",
            method: 'POST',
            data: {
                _token: csrfToken,
                article_number: articleNumber,
                item_desc: itemDesc
            },
            success: function (response) {
                let totalQty = 0;

                if (response.length === 0) {
                    $('#qty_in_orderTable tbody').html(`
                        <tr>
                            <td colspan="7" class="text-center">No data available</td>
                        </tr>
                    `);
                    $('#total_po_qty').text(0);
                } else {
                    $('#qty_in_orderTable tbody').empty();

                    response.forEach(row => {
                        if (row.po_qty === 'N/A') return;

                        let hold_qty = row.hold_qty || 0;
                        let po_qty = parseFloat(row.po_qty) || 0;

                        totalQty += po_qty;

                        let rowHtml = `
                            <tr>
                                <td>${row.po_no}</td>
                                <td>${row.project_no}</td>
                                <td>${row.project_name}</td>                              
                                <td>${row.description}</td>                            
                                <td>${row.article_no}</td>
                                <td>${po_qty}</td>
                            </tr>
                        `;

                        $('#qty_in_orderTable tbody').append(rowHtml);
                    });

                    if ($('#qty_in_orderTable tbody tr').length === 0) {
                        $('#qty_in_orderTable tbody').html(`
                            <tr>
                                <td colspan="7" class="text-center">No valid data available</td>
                            </tr>
                        `);
                    }

                    $('#total_po_qty').text(totalQty);
                }

                $('#qtrInOrderShowModal').modal('show');
            },
            error: function () {
                alert('Error loading project details.');
            }
        });
    });

    // Display selected file name
    $(document).on('change', '#excel_file', function() {
        const fileName = this.files.length > 0 ? this.files[0].name : 'No file chosen';
        $('#file-name').text(fileName);
    });

    $(document).ready(function () {
        // Prevent dropdown from closing when clicking inside
        $(document).on('click', '.dropdown-menu', function (e) {
            e.stopPropagation();
        });
    });

    $(document).on('change', '.select-all-filter', function () {
        var targetClass = $(this).data('target');
        var isChecked = $(this).is(':checked');
        $(targetClass).attr('checked', isChecked ? 'checked' : false);

        if(isChecked){
            $(this).closest('.dropdown').find('.dropdown-item').addClass('checked');
        }else{
            $(this).closest('.dropdown').find('.dropdown-item').removeClass('checked');
        } 
    });
    
    $(document).ready(function () {
        const stockFilterRoute = "{{ route('StockFilter') }}";

        $(document).on('click', '.apply-filter-btn', function () {
            var last_filter_column = $(this).data('column');
            $('#last_filter_column').val(last_filter_column);

            // Optional: Set route dynamically (if needed)
            $('#filter_form').attr('action', stockFilterRoute);

            // Check if form action matches expected route before submitting
            if ($('#last_filter_column').val() && $('#filter_form').attr('action') === stockFilterRoute) {
                $('#filter_form').submit();
            } else {
                alert("Form action does not match StockFilter route.");
            }
        });
    });

    function download_csv() {
        var exportRoute = "{{ route('stock.export.csv') }}";
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

    $('.stock_reset_btn').click(function(){
        var table = $('#stock_table').DataTable();
        table.search('').draw();
    });

</script>
@endsection