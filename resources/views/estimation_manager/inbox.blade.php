@extends('layouts.main')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="{{ asset('css/estimation_manager.css') }}" />

<div class="estimation_manager_page main_section bg-white m-4 pb-5">
    @if ($errors->any())
        <div class="mx-4 alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (session('error'))
        <div class="mx-4 alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    @if (session('success'))
        <div class="mx-4 alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center px-5 pt-3">
        <h3 class="pt-4 text-bold text-left text-uppercase">Pending BOM Upload</h3>
        <a class="mt-4 mb-2 btn btn-primary btn-sm py-2 px-3" onclick="location.reload();">
            <i class="fa fa-refresh text-light">&#xf021;</i>
        </a>
    </div>
    <hr class="mx-5 mt-2 mb-4" />    
    <div class="container-fluid px-5 mx-3">
        <div class="row mt-3 table-responsive">
            @if(count($bom_req) > 0)
            <table class="table table-hover table-bordered w-100 text-center" id="pending_bom_check">
                <thead>
                    <tr>
                        <th scope="col" class="project_table_heading">Request Date</th>
                        <th scope="col" class="project_table_heading">Deadline</th>
                        <th scope="col" class="project_table_heading">Project No.</th>
                        <th scope="col" class="project_table_heading">Project Name</th>
                        <th scope="col" class="project_table_heading">Article No.</th>
                        <th scope="col" class="project_table_heading">Product Description</th>
                        <th scope="col" class="project_table_heading">QTY</th>
                        <th scope="col" class="project_table_heading">Action</th>
                        <th scope="col" class="project_table_heading">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bom_req as $val)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($val->projects['wip_project_create_date'])->format('d-m-Y H:i') }}</td>
                            <td>
                                @if($val->projects['wip_project_create_date'])
                                @php
                                $requestDate = \Carbon\Carbon::parse($val->projects['wip_project_create_date']); // 30-05-2025 09:12
                                $hoursToAdd = $standard_hours; // 48 hours
                                $currentDate = $requestDate->copy();

                                while ($hoursToAdd > 0) {
                                $currentDate->addHour(); // Add one hour
                                // Skip Saturday (6) or Sunday (0)
                                if ($currentDate->dayOfWeek === 6 || $currentDate->dayOfWeek === 0) {
                                continue; // Don't count weekend hours
                                }
                                $hoursToAdd--; // Count only weekday hours
                                }

                                $deadline = $currentDate;
                                $now = \Carbon\Carbon::now();
                                $color = $deadline->isFuture() ? 'green' : 'red';
                                @endphp
                                <span style="color: {{ $color }}">{{ $deadline->format('d-m-Y H:i') }}</span>
                                @else
                                N/A
                                @endif
                            </td>
                            <td>{{$val->projects['project_no']}}</td>
                            <td>{{$val->projects['project_name']}}</td>
                            <td>{{$val->full_article_number}}</td>
                            <td>{{ $val->description}}</td>
                            <td>{{$val->qty}}</td>
                            <td>
                                <span class="project_check_status">
                                    <button type="button" class="btn btn-primary primary_bg_color text-white p-0 upload-bom-btn">
                                        <i class="fa fa-upload p-1"></i>
                                    </button>
                                    <input type="file" class="d-none upload-bom-input" data-id="{{$val->id}}" accept=".xls,.xlsx,.csv">
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-success btn-sm add-remarks-btn px-2 py-1"
                                    data-bs-toggle="modal"
                                    data-bs-target="#remarksModal"
                                    data-id="{{$val->id}}"
                                    data-type="bom">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @else
                <div class="alert alert-info w-100">
                    No Pending BOM Upload found.
                </div>
            @endif
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center px-5 pt-3">
        <h3 class="pt-4 text-bold text-left text-uppercase">Pending Basic Drawing Upload</h3>
        <a class="mt-4 mb-2 btn btn-primary btn-sm py-2 px-3" onclick="location.reload();">
            <i class="fa fa-refresh text-light">&#xf021;</i>
        </a>
    </div>
    <hr class="mx-5 mt-2 mb-4" />
    <div class="container-fluid px-5 mx-3">
        <div class="row mt-3 table-responsive">
            @if(count($drawing_req) > 0)
            <table class="table table-hover table-border w-100 text-center" id="pending_drawing_check">
                <thead>
                    <tr>
                        <th scope="col" class="project_table_heading">Request Date</th>
                        <th scope="col" class="project_table_heading">Deadline</th>
                        <th scope="col" class="project_table_heading">Project No.</th>
                        <th scope="col" class="project_table_heading">Project Name</th>
                        <th scope="col" class="project_table_heading">Article No.</th>
                        <th scope="col" class="project_table_heading">Product Description</th>
                        <th scope="col" class="project_table_heading">QTY</th>
                        <th scope="col" class="project_table_heading">Action</th>
                        <th scope="col" class="project_table_heading">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($drawing_req as $val)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($val->projects['wip_project_create_date'])->format('d-m-Y H:i') }}</td>
                            <td>
                                @if($val->projects['wip_project_create_date'])
                                @php
                                $requestDate = \Carbon\Carbon::parse($val->projects['wip_project_create_date']);
                                $hoursToAdd = $standard_hours; // e.g., 48 hours
                                $deadline = $requestDate->copy();

                                // Loop to add hours while skipping weekends
                                $hoursAdded = 0;
                                while ($hoursAdded < $hoursToAdd) {
                                    $deadline->addHour();
                                    // Check if the current day is Saturday (6) or Sunday (0)
                                    if ($deadline->isWeekday()) {
                                    $hoursAdded++;
                                    }
                                    }

                                    $now = \Carbon\Carbon::now();
                                    $color = $deadline->isFuture() ? 'green' : 'red';
                                    @endphp
                                    <span style="color: {{ $color }}">{{ $deadline->format('d-m-Y H:i') }}</span>
                                    @else
                                    N/A
                                    @endif
                            </td>
                            <td>{{$val->projects['project_no']}}</td>
                            <td>{{$val->projects['project_name']}}</td>
                            <td>{{$val->full_article_number}}</td>
                            <td>{{ $val->description}}</td>
                            <td>{{$val->qty}}</td>
                            <td>
                                <span class="project_check_status">
                                    <button type="button" class="btn btn-primary primary_bg_color text-white p-0 upload-drawing-btn">
                                        <i class="fa fa-upload p-1"></i>
                                    </button>
                                    <input type="file" class="d-none upload-drawing-input" data-id="{{$val->id}}" accept=".pdf,.psw" data-lable="drawing">
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-success btn-sm add-remarks-btn px-2 py-1"
                                    data-bs-toggle="modal"
                                    data-bs-target="#remarksModal"
                                    data-id="{{$val->id}}"
                                    data-type="drawing">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @else
                <div class="alert alert-info w-100">
                    No Pending Drawing Upload found.
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
    <hr class="mx-5 mt-2 mb-4" />
    <div class="container-fluid px-5 mx-3">
        <div class="row mt-3 table-responsive">
            @if(count($pdf_req) > 0)
            <table class="table table-hover table-border w-100 text-center as_built_pdf_table" id="as_built_pdf">
                <thead>
                    <tr>
                        <th scope="col" class="project_table_heading">Project No.</th>
                        <th scope="col" class="project_table_heading">Project Name</th>
                        <th scope="col" class="project_table_heading">Article No.</th>
                        <th scope="col" class="project_table_heading">Product Description</th>
                        <th scope="col" class="project_table_heading">Assigned Qty</th>
                        <th scope="col" class="project_table_heading">Basic PDF</th>
                        <th scope="col" class="project_table_heading">As-Built PDF</th>
                        <th scope="col" class="project_table_heading" style="width: 20%;">Action</th>
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
                            <td class="action"> <!-- A Code: 17-12-2025 -->
                                <button class="btn btn-success btn-sm m-0 px-3 py-1" data-bs-toggle="modal"
                                    data-bs-target="#approveModal" data-id="{{ $val->id }}">
                                    <i class="fa fa-check"></i>
                                </button>
                                <button class="btn btn-danger btn-sm m-0 px-3 py-1" data-bs-toggle="modal"
                                    data-bs-target="#rejectModal" data-id="{{ $val->id }}">
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
        <h3 class="pt-4 text-bold text-left text-uppercase">Upload Final PDF</h3>
        <a class="mt-4 mb-2 btn btn-primary btn-sm py-2 px-3" onclick="location.reload();">
            <i class="fa fa-refresh text-light">&#xf021;</i>
        </a>
    </div>
    <hr class="mx-5 mt-2 mb-4" />
    <div class="container-fluid px-5 mx-3">
        <div class="row mt-3 table-responsive">
            @if(count($final_pdf_req) > 0)
            <table class="table table-hover table-border w-100 text-center" id="final_pdf_upload">
                <thead>
                    <tr>
                        <th scope="col" class="project_table_heading">Project No.</th>
                        <th scope="col" class="project_table_heading">Project Name</th>
                        <th scope="col" class="project_table_heading">Article No.</th>
                        <th scope="col" class="project_table_heading">Product Description</th>
                        <th scope="col" class="project_table_heading">Assigned Qty</th>
                        <th scope="col" class="project_table_heading">Basic PDF</th>
                        <th scope="col" class="project_table_heading">Edited PDF</th>
                        <th scope="col" class="project_table_heading">Upload Final PDF</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($final_pdf_req as $val)
                    <tr>
                        <td>{{ $val->projects->project_no ?? 'N/A' }}</td>
                        <td>{{ $val->projects->project_name ?? 'N/A' }}</td>
                        <td>{{ $val->article_number ?? 'N/A' }}</td>
                        <td>{{ $val->description?? 'N/A' }}</td>
                        <td>{{ $val->qty ?? 0 }}</td>
                        <td>
                            @if(!empty($val->drawing_path))
                            <a href="{{ asset($val->editable_drawing_path) }}"
                                class="btn btn-sm dwld_pdf_btn" 
                                download
                                title="Download Edited PDF">
                                <i class="fa fa-download"></i>
                            </a>
                            @else
                            <span class="text-muted">No PDF</span>
                            @endif
                        </td>
                        <td>
                            @if(!empty($val->editable_drawing_path))
                            <a href="{{ asset($val->editable_drawing_path) }}"
                                class="btn btn-sm dwld_pdf_btn" 
                                download
                                title="Download Edited PDF">
                                <i class="fa fa-download"></i>
                            </a>
                            @else
                            <span class="text-muted">No PDF</span>
                            @endif
                        </td>
                        <td>
                            @if ($val->drawing_upload_by_estimation_manager)
                            <a href="{{ asset($val->drawing_upload_by_estimation_manager) }}" target="_blank" 
                                class="btn btn-sm dwld_pdf_btn" 
                                title="View Drawing Estimation">
                                <i class="fa fa-eye"></i>
                            </a>
                            @else
                            <form action="{{ route('upload.drawing.estimation') }}" method="POST" enctype="multipart/form-data" id="upload-drawing-form-{{ $val->id }}">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $val->id }}">
                                <input type="file" name="drawing_estimation" id="drawing-file-input-{{ $val->id }}" 
                                accept=".pdf" style="display: none;" onchange="this.form.submit()">
                                <button type="button" 
                                    class="btn btn-sm dwld_pdf_btn" 
                                    onclick="document.getElementById('drawing-file-input-{{ $val->id }}').click()" 
                                    title="Upload Drawing Estimation">
                                    <i class="fa fa-upload"></i>
                                </button>
                            </form>
                            @endif
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
    
    <div class="modal fade" id="remarksModal" tabindex="-1" aria-labelledby="remarksModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white" id="remarksModalLabel">Add Remarks</h5>
                </div>
                <div class="modal-body">
                    <textarea class="form-control" rows="3" id="remarksText" placeholder="Enter remarks..."></textarea>
                    <input type="hidden" id="remarkId">
                    <input type="hidden" id="remarkType">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="updateRemarks">Update</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    $(document).ready(function() {
        $('#pending_bom_check').DataTable({
            paging: true,
            pageLength: 2,
            lengthMenu: [2, 5, 10, 25, 50, 100],
            searching: true,
            ordering: false
        });
        $('#pending_bom_check').removeClass('dataTable');
        $('#project_table_all_task').DataTable({
            paging: true,
            pageLength: 2,
            lengthMenu: [2, 5, 10, 25, 50, 100],
            searching: true,
            ordering: false
        });
        $('#project_table_all_task').removeClass('dataTable');
        $('#pending_drawing_check').DataTable({
            paging: true,
            pageLength: 2,
            lengthMenu: [2, 5, 10, 25, 50, 100],
            searching: true,
            ordering: false
        });
        $('#pending_drawing_check').removeClass('dataTable');
        $('#final_pdf_upload').DataTable({
            paging: true,
            pageLength: 2,
            lengthMenu: [2, 5, 10, 25, 50, 100],
            searching: true,
            ordering: false
        });
        $('#final_pdf_upload').removeClass('dataTable');
    });

    $(document).on('click', '.upload-bom-btn', function() {
        $(this).siblings('.upload-bom-input').trigger('click');
    });

    $(document).on('change', '.upload-bom-input', function() {
        const csrfToken = "{{ csrf_token() }}";
        let fileInput = $(this);
        let file_data = fileInput[0].files[0];

        // Check if a file is selected
        if (!file_data) {
            alert('Please select a file to upload.');
            return;
        }

        var id = fileInput.data('id');

        let formData = new FormData();
        formData.append('_token', csrfToken);
        formData.append('file', file_data);
        formData.append('id', id);

        $.ajax({
            url: "{{ route('EstimationManagerUploadBom') }}",
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.success) {
                    alert('BOM uploaded successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(error) {

                let msg = "Validation error";
                if (error.responseJSON && error.responseJSON.message) {
                    msg = error.responseJSON.message;
                }
                alert(msg);

                if (error.status === 422) {
                    var errors = error.responseJSON.errors;
                    var errorMessages = '';
                    for (var field in errors) {
                        errorMessages += errors[field].join('<br>');
                    }
                    if(errorMessages != ''){
                        alert('Validation errors:\n' + errorMessages);
                    }
                } else {
                    alert('Error uploading BOM file: ' + (error.responseJSON?.message || 'Unknown error'));
                }
            }
        });
    });

    $(document).on('click', '.upload-drawing-btn', function() {
        $(this).siblings('.upload-drawing-input').trigger('click');
    });

    $(document).on('change', '.upload-drawing-input', function() {
        const csrfToken = "{{ csrf_token() }}";
        let file_data = $(this)[0].files[0];
        var id = $(this).data('id');
        var lable = $(this).data('lable');

        let formData = new FormData();
        formData.append('_token', csrfToken);
        formData.append('file', file_data);
        formData.append('id', id);
        formData.append('lable', lable);

        $.ajax({
            url: "{{ route('EstimationManagerUploadBomDrawing') }}",
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.success) {
                    alert('File uploaded successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(error) {
                if (error.status === 422) {
                    var errors = error.responseJSON.errors;
                    var errorMessages = '';
                    for (var field in errors) {
                        errorMessages += errors[field].join('<br>');
                    }
                    alert('Validation errors:\n' + errorMessages);
                } else {
                    alert('Error uploading file.');
                }
            }
        });
    });
    $(document).on('click', '.add-remarks-btn', function() {
        const id = $(this).data('id');
        const type = $(this).data('type');

        $('#remarkId').val(id);
        $('#remarkType').val(type);
        $('#remarksText').val(''); // Clear previous text
    });

    $(document).on('click', '#updateRemarks', function() {
        const csrfToken = "{{ csrf_token() }}";
        const id = $('#remarkId').val();
        const type = $('#remarkType').val();
        const remarks = $('#remarksText').val();

        $.ajax({
            url: "{{ route('EstimationManagerUpdateRemarks') }}", // Updated URL to the new route
            type: 'POST',
            data: {
                _token: csrfToken,
                id: id,
                type: type,
                remarks: remarks
            },
            success: function(response) {
                if (response.success) {
                    alert('Remarks updated successfully!');
                    $('#remarksModal').modal('hide');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(error) {
                alert('Error updating remarks: ' + (error.responseJSON?.message || 'Unknown error'));
            }
        });
    });
    $(document).on('click', '.add-remarks-btn', function() {
        const id = $(this).data('id');
        const type = $(this).data('type');

        $('#remarkId').val(id);
        $('#remarkType').val(type);

        // Fetch existing remarks (you might need to add an endpoint for this)
        $.ajax({
            url: "{{ route('EstimationManagerGetRemarks') }}", // You'll need to create this route
            type: 'GET',
            data: {
                id: id,
                type: type
            },
            success: function(response) {
                if (response.success) {
                    $('#remarksText').val(response.remarks || '');
                }
            },
            error: function() {
                $('#remarksText').val(''); // Clear if there's an error
            }
        });
    });
</script>

<script>
    let selectedProductId = null;

    $('#approveModal').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget);
        selectedProductId = button.data('id');
    });

    $('#confirmApprove').on('click', function() {
        const remarks = $('#approveRemarks').val();

        $.ajax({
            url: "{{ route('approvepdf') }}",
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
            url: "{{ route('rejectpdf') }}",
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
</script>
@endsection