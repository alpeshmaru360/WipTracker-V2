@extends('layouts.main')
@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css" />
<link rel="stylesheet" href="{{ asset('css/quality_manager.css') }}" />

@php
$is_quality_login = Auth::user()->role === 'Quality Engineer';
@endphp

<div class="quality_page main_section bg-white m-4 pb-5 pt-2">
   
    @if ($errors->any())
    <div class="mx-4 my-3 alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    @if (session('error'))
    <div class="mx-4 my-3 alert alert-danger">
        {{ session('error') }}
    </div>
    @endif
    @if (session('success'))
    <div class="mx-4 my-3 alert alert-success">
        {{ session('success') }}
    </div>
    @endif
    <div class="container-fluid px-5">

        <!-- Tabs Navigation -->
        <ul class="nav nav-tabs mt-5" id="qualityTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="initial-tab" data-toggle="tab" href="#initial" role="tab" aria-controls="initial" aria-selected="true">Initial Inspection</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="final-tab" data-toggle="tab" href="#final" role="tab" aria-controls="final" aria-selected="false">Final Inspection</a>
            </li>
        </ul>

        <div class="tab-content mt-3 px-5" id="qualityTabsContent">
            <div class="tab-pane fade show active" id="initial" role="tabpanel" aria-labelledby="initial-tab">
                <!-- <h3>Initial Inspection</h3> -->

                <div class="row mt-3">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered text-center" id="initial_inspection_table">
                            <thead>
                                <tr>                             
                                    <th scope="col" class="project_table_heading p-1">Request Date</th>                                
                                    <th scope="col" class="project_table_heading p-1">Project No.</th>
                                    <th scope="col" class="project_table_heading p-1">Project Name</th>
                                    <th scope="col" class="project_table_heading p-1">PO No.</th>
                                    <th scope="col" class="project_table_heading p-1">Supplier</th>
                                    <th scope="col" class="project_table_heading p-1">Order Status</th>                                
                                    <th scope="col" class="project_table_heading p-1">Pump Type</th>                                
                                    <th scope="col" class="project_table_heading p-1">View Items</th>
                                    <th scope="col" class="project_table_heading p-1">Reports Docs</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($initialInspectionData as $val)
                                <tr>                              
                                    <td>{{ \Carbon\Carbon::parse($val->actual_received_date)->format('d-m-y') ?? 'N/A' }}</td>
                                    <td>{{ $val->project_no }}</td>
                                    <td>{{ $val->project_name }}</td>
                                    <td>{{ $val->po_number }}</td>
                                    <td>{{ $val->supplier }}</td>
                                    <td>
                                        @php
                                            $project_no = ($val->project_no === 'N/A') ? null : $val->project_no;
                                            $is_parent = DB::table('purchase_order as po')
                                                            ->join('purchase_order_table as pot', 'po.id', '=', 'pot.po_id')
                                                            ->where('po.po_number', $val->po_number)
                                                            ->where('po.project_no', $project_no)
                                                            ->value('pot.is_parent');
                                        @endphp

                                        {{ $is_parent == 1 ? 'Full Order' : 'Partial Order' }}
                                    </td>
                                    <td>{{ $val->pump_type_name ?? 'N/A' }}</td>                                
                                    <td>
                                        <a href="#" 
                                        class="show_initial_item_list" 
                                        data-toggle="modal" 
                                        data-target="#initialModal" 
                                        data-actual-received-date="{{ $val->actual_received_date }}"
                                        data-po-number="{{ $val->po_number }}"
                                        data-supplier="{{ $val->supplier }}"
                                        data-project-no="{{ $val->project_no }}">
                                        <i class="fa fa-eye p-2 m-1 project_icon"></i>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex">
                                            @if($val->reports_docs)
                                                @php
                                                $document = $val->reports_docs;
                                                $fileExtension = pathinfo($val->reports_docs, PATHINFO_EXTENSION);
                                                @endphp
                                                @if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif']))
                                                <a href="{{ asset($document) }}" target="_blank">
                                                    <i class="p-2 m-1 fa fa-image image project_icon"></i>
                                                </a>
                                                @elseif ($fileExtension == 'pdf')
                                                <a href="{{ asset($document) }}" target="_blank">
                                                    <i class="p-2 m-1 fa fa-file-pdf pdf project_icon"></i>
                                                </a>
                                                @elseif (in_array($fileExtension, ['doc', 'docx']))
                                                <a href="{{ asset($document) }}" target="_blank">
                                                    <i class="p-2 m-1 fa fa-file-word doc project_icon"></i>
                                                </a>
                                                @elseif (in_array($fileExtension, ['xlsx', 'csv']))
                                                <a href="{{ asset($document) }}" target="_blank">
                                                    <i class="p-2 m-1 fa fa-file-excel excel project_icon"></i>
                                                </a>
                                                @else
                                                <a href="{{ asset($document) }}" download>
                                                    <i class="p-2 m-1 fa fa-download other project_icon"></i>
                                                </a>
                                                @endif
                                            @endif                                   
                                            @if ($is_quality_login)
                                            <form action="{{ route('upload.initial.report') }}" method="POST" enctype="multipart/form-data" id="upload-form-{{ $val->id }}">
                                                @csrf
                                                <input type="hidden" name="inspection_id" value="{{ $val->id }}">
                                                <input type="file" name="reports_docs" id="file-input-{{ $val->id }}" accept=".doc,.docx" style="display: none;" onchange="this.form.submit()">
                                                <a href="javascript:;" onclick="document.getElementById('file-input-{{ $val->id }}').click()">
                                                    <i class="p-2 m-1 fas fa-edit project_icon"></i>
                                                </a>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
        
            </div>
            <div class="tab-pane fade" id="final" role="tabpanel" aria-labelledby="final-tab">
                <!-- <h3>Final Inspection</h3> -->

                <div class="row mt-3">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered text-center" id="final_inspection_table">
                            <thead>
                                <tr>
                                    <th>Project No</th>
                                    <th>Project Name</th>
                                    <th>Article</th>
                                    <th>Serial No.</th>
                                    <th>Description</th>
                                    <th>Total Product Qty</th>
                                    <th>Unit No From Total Qty</th>
                                    <th>Images <span class="text-red">*</span></th>
                                    <th>Final Inspection Report <span class="text-red">*</span></th>
                                    <th>Test Report</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($finalInspectionData as $val)
                                <tr>
                                    <td>{{$val->project_no}}</td>
                                    <td>{{$val->project_name}}</td>
                                    <td>{{$val->product_article_no}}</td>
                                    <td>{{$val->serial_no}}</td>
                                    <td>{{$val->product_desc}}</td>
                                    <td>{{$val->qty}}</td>
                                    <td>{{$val->unit_qty}} of {{$val->qty}}</td>
                                    <td>
                                        @if(!empty($val->product_image))
                                        <a href="#" data-toggle="modal" data-target="#imageModal{{ $val->id }}">
                                            <i class="fa fa-eye eye-icon"></i>
                                        </a>
                                        @endif
                                    </td>
                                    <td class="action">
                                        @if($val->reports_docs)
                                            @php
                                            $document = $val->reports_docs;
                                            $fileExtension = pathinfo($val->reports_docs, PATHINFO_EXTENSION);
                                            @endphp
                                            @if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif']))
                                            <a href="{{ asset($document) }}" target="_blank">
                                                <i class="p-2 m-1 fa fa-image image project_icon"></i>
                                            </a>
                                            @elseif ($fileExtension == 'pdf')
                                            <a href="{{ asset($document) }}" target="_blank">
                                                <i class="p-2 m-1 fa fa-file-pdf pdf project_icon"></i>
                                            </a>
                                            @elseif (in_array($fileExtension, ['doc', 'docx']))
                                            <a href="{{ asset($document) }}" target="_blank">
                                                <i class="p-2 m-1 fa fa-file-word doc project_icon"></i>
                                            </a>
                                            @elseif (in_array($fileExtension, ['xlsx', 'csv']))
                                            <a href="{{ asset($document) }}" target="_blank">
                                                <i class="p-2 m-1 fa fa-file-excel excel project_icon"></i>
                                            </a>
                                            @else
                                            <a href="{{ asset($document) }}" download>
                                                <i class="p-2 m-1 fa fa-download other project_icon"></i>
                                            </a>
                                            @endif
                                        @endif
                                        @if ($is_quality_login)
                                        <form action="{{ route('upload.final.report') }}" method="POST" enctype="multipart/form-data" id="upload-final-report-form-{{ $val->id }}" class="upload-doc">
                                            @csrf
                                            <input type="hidden" name="inspection_id" value="{{ $val->id }}">
                                            <input type="file" name="reports_docs" id="final-report-file-input-{{ $val->id }}" accept=".doc,.docx" style="display: none;" onchange="this.form.submit()">
                                            <a href="javascript:;" onclick="document.getElementById('final-report-file-input-{{ $val->id }}').click()">
                                                <i class="p-2 m-1 fas fa-edit project_icon"></i>
                                            </a>
                                        </form>
                                        @endif
                                    </td>
                                    <td class="action">
                                        @if($val->test_reports_docs)
                                        @php
                                        $document = $val->test_reports_docs;
                                        $fileExtension = pathinfo($val->test_reports_docs, PATHINFO_EXTENSION);
                                        @endphp
                                        @if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif']))
                                        <a href="{{ asset($document) }}" target="_blank">
                                            <i class="p-2 m-1 fa fa-image image project_icon"></i>
                                        </a>
                                        @elseif ($fileExtension == 'pdf')
                                        <a href="{{ asset($document) }}" target="_blank">
                                            <i class="p-2 m-1 fa fa-file-pdf pdf project_icon"></i>
                                        </a>
                                        @elseif (in_array($fileExtension, ['doc', 'docx']))
                                        <a href="{{ asset($document) }}" target="_blank">
                                            <i class="p-2 m-1 fa fa-file-word doc project_icon"></i>
                                        </a>
                                        @elseif (in_array($fileExtension, ['xlsx', 'csv']))
                                        <a href="{{ asset($document) }}" target="_blank">
                                            <i class="p-2 m-1 fa fa-file-excel excel project_icon"></i>
                                        </a>
                                        @else
                                        <a href="{{ asset($document) }}" download>
                                            <i class="p-2 m-1 fa fa-download other project_icon"></i>
                                        </a>
                                        @endif
                                        @else
                                        {{ 'N/A' }}
                                        @endif
                                        @if ($is_quality_login)
                                        <form action="{{ route('upload.test.report') }}" method="POST" enctype="multipart/form-data" id="upload-test-report-form-{{ $val->id }} " class="upload-doc">
                                            @csrf
                                            <input type="hidden" name="inspection_id" value="{{ $val->id }}">
                                            <input type="file" name="test_reports_docs" id="test-report-file-input-{{ $val->id }}" accept=".doc,.docx" style="display: none;" onchange="this.form.submit()">
                                            <a href="javascript:;" onclick="document.getElementById('test-report-file-input-{{ $val->id }}').click()">
                                                <i class="p-2 m-1 fas fa-edit project_icon"></i>
                                            </a>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
        
            </div>
        </div>

        <!-- Modals for Images -->
        @foreach($finalInspectionData as $val)
        <div class="modal fade" id="imageModal{{ $val->id }}" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel{{ $val->id }}" aria-hidden="true">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-white" id="imageModalLabel{{ $val->id }}">Images for {{ $val->product_article_no }}</h5>
                    </div>
                    <div class="modal-body">
                        @if ($is_quality_login)
                        <button type="button" class="btn btn-add text-right float-right" 
                            onclick="document.getElementById('fileInput{{ $val->id }}').click()">+ Add</button>
                        @endif
                        @php
                        $images = json_decode($val->product_image, true);
                        @endphp
                        @if($images && is_array($images))
                        <table class="table table-bordered image-table" id="imageTable{{ $val->id }}">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    @if ($is_quality_login)
                                    <th>Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($images as $image)
                                <tr>
                                    <td align="center">
                                        <a href="{{ asset($image) }}" target="_blank">
                                            <img src="{{ asset($image) }}" alt="Inspection Image">
                                        </a>
                                    </td>
                                    <td>{{ basename($image) }}</td>
                                    @if ($is_quality_login)
                                    <td align="center">
                                        <i class="fas fa-trash-alt delete-icon" data-image="{{ $image }}" data-inspection-id="{{ $val->id }}"></i>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <p id="noImages{{ $val->id }}">No images available.</p>
                        @endif
                    </div>
                    <div class="modal-footer">

                        <input type="file" id="fileInput{{ $val->id }}" accept="image/jpeg,image/jpg,image/png,image/webp" multiple style="display: none;" onchange="uploadImages({{ $val->id }})">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

    </div>
</div>

<!-- A Code: 20-01-2026 Start -->
<div class="modal fade" id="initialModal" tabindex="-1" aria-labelledby="initialModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white" id="initialModalLabel">Item Details</h5>
            </div>
            <div class="modal-body p-0">
                <table class="table table-hover table-bordered text-center mb-0" id="itemDetailsTable">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="project_table_heading">Sr no</th>
                            <th scope="col" class="project_table_heading">Article Number</th>
                            <th scope="col" class="project_table_heading">Description</th>
                            <th scope="col" class="project_table_heading">Quantity</th>  
                            <th scope="col" class="project_table_heading">Order Status</th>                  
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
<!-- A Code: 20-01-2026 End -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<!-- Bootstrap JS for Modal -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->

<script>
    $(document).ready(function() {
        $('#initial_inspection_table').DataTable({
            paging: true,
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            searching: true,
            ordering: false,
            info: false
        });
        $('#initial_inspection_table').removeClass('dataTable');        
        $('#final_inspection_table').DataTable({
            paging: true,
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            searching: true,
            ordering: false,
            info: false
        });
        $('#final_inspection_table').removeClass('dataTable');

        // Ensure modals are initialized
        $('.modal').modal({
            show: false,
            backdrop: 'static',
            keyboard: false
        });

        // Handle delete icon click
        $('.image-table').on('click', '.delete-icon', function() {
            if (confirm('Are you sure you want to delete this image?')) {
                var image = $(this).data('image');
                var inspectionId = $(this).data('inspection-id');
                var row = $(this).closest('tr');

                $.ajax({
                    url: '{{ route("delete.inspection.image") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        image: image,
                        inspection_id: inspectionId
                    },
                    success: function(response) {
                        if (response.success) {
                            row.remove();
                            alert('Image deleted successfully.');
                            // Check if table is empty
                            if ($('#imageTable' + inspectionId + ' tbody tr').length === 0) {
                                $('#imageTable' + inspectionId).remove();
                                $('#noImages' + inspectionId).show();
                            }
                        } else {
                            alert('Failed to delete image: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        alert('An error occurred while deleting the image: ' + (xhr.responseJSON?.message || 'Unknown error'));
                    }
                });
            }
        });
    });

    // Function to handle image uploads
    function uploadImages(inspectionId) {
        var files = document.getElementById('fileInput' + inspectionId).files;
        if (files.length === 0) {
            alert('Please select at least one image to upload.');
            return;
        }

        var formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('inspection_id', inspectionId);
        for (var i = 0; i < files.length; i++) {
            formData.append('images[]', files[i]);
        }

        $.ajax({
            url: '{{ route("upload.inspection.images") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert('Images uploaded successfully.');
                    // Update the table with the new images
                    var table = $('#imageTable' + inspectionId);
                    var noImages = $('#noImages' + inspectionId);

                    if (noImages.length) {
                        // If no images existed, create the table
                        noImages.hide();
                        var tableHtml = `
                            <table class="table table-bordered image-table" id="imageTable${inspectionId}">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>`;
                        noImages.after(tableHtml);
                        table = $('#imageTable' + inspectionId);
                    }

                    // Append new images to the table
                    response.images.forEach(function(image) {
                        var rowHtml = `
                            <tr>
                                <td>
                                    <a href="${image.url}" target="_blank">
                                        <img src="${image.url}" alt="Inspection Image">
                                    </a>
                                </td>
                                <td>${image.name}</td>
                                <td align="center">
                                    <i class="fas fa-trash-alt delete-icon" data-image="${image.url}" data-inspection-id="${inspectionId}"></i>
                                </td>
                            </tr>`;
                        table.find('tbody').append(rowHtml);
                    });

                    // Reset the file input
                    $('#fileInput' + inspectionId).val('');
                } else {
                    alert('Failed to upload images: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('An error occurred while uploading the images: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    }

    // A Code: 20-01-2026 Start  
    $(document).on('click', '.show_initial_item_list', function (e) {
        e.preventDefault();

        const csrfToken = "{{ csrf_token() }}";
        const actualReceivedDate = $(this).data('actual-received-date');
        const poNumber = $(this).data('po-number'); 
        const supplier = $(this).data('supplier');
        const projectNo = $(this).data('project-no');

        $.ajax({
            url: "{{ route('showInitialItemList') }}",
            type: 'POST',
            data: {
                _token: csrfToken,
                actual_received_date: actualReceivedDate,
                po_number: poNumber,
                item_supplier: supplier,
                project_no: projectNo                
            },
            success: function(response) {
                const $tbody = $('#itemDetailsTable tbody').empty();

                if (Array.isArray(response) && response.length) {
                    response.forEach((row, index) => {
                        $tbody.append(`
                            <tr>
                                <td>${index + 1}</td>
                                <td>${row.artical_no ?? '-'}</td>
                                <td>${row.description ?? ' '}</td>
                                <td>${row.quantity ?? 0}</td>
                                <td>${row.is_parent == 1 ? 'Full Order' : 'Partial Order'}</td>
                            </tr>
                        `);
                    });
                } else {
                    $tbody.append(`
                        <tr>
                            <td colspan="4" class="text-center">No data available</td>
                        </tr>
                    `);
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                alert('Error loading item details.');
            }
        });
    });
    // A Code: 20-01-2026 End 

</script>
@endsection