@extends('layouts.main')
@section('content')
<link rel="stylesheet" href="{{ asset('css/production_manager.css') }}" />
<link rel="stylesheet" href="{{ asset('css/product_superwisor.css') }}" />

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap-switch-button@1.1.0/css/bootstrap-switch-button.min.css">
<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap-switch-button@1.1.0/dist/bootstrap-switch-button.min.js"></script>

<div class="production_manager_inbox_page main_section bg-white m-4 m-4 pb-5">
    <div class="d-flex justify-content-between align-items-center px-5 pt-3">
        <h3 class="pt-4 text-bold text-left text-uppercase">Pending WITrack Order</h3>
        <a class="mt-4 mb-2 btn btn-primary btn-sm py-2 px-3" onclick="location.reload();">
            <i class="fa fa-refresh text-light">&#xf021;</i>
        </a>
    </div>
    <hr class="mx-5 mt-2 mb-2" />
    <div class="container-fluid px-5 mx-3">
        <div class="row mt-3 table-responsive">
            @if(count($pendingWITrackProjects) > 0)
            <table class="table table-hover table-border w-100 text-center" id="project_table1">
                <thead>
                    <tr>
                        <th scope="col" class="project_table_heading p-1" style="width: 8% !important;">Date</th>
                        <th scope="col" class="project_table_heading p-1" style="width: 8% !important;">Deadline</th>
                        <th scope="col" class="project_table_heading p-1">Customer Ref.</th>
                        <th scope="col" class="project_table_heading p-1">WiTrack No.</th>
                        <th scope="col" class="project_table_heading p-1">Project Name</th>
                        <th scope="col" class="project_table_heading p-1">Country</th>
                        <th scope="col" class="project_table_heading p-1">Customer Name</th>
                        <th scope="col" class="project_table_heading p-1">Sales Name</th>
                        <th scope="col" class="project_table_heading p-1">Docs</th>
                        <th scope="col" class="project_table_heading p-1" style="width: 25% !important;">Action</th>
                    </tr>
                </thead>
                <tbody id="project_table_body">
                    @foreach($pendingWITrackProjects as $val)
                    <tr>
                        <td>{{ $val->created_at->format('d-m-y') }}</td>
                        <td>
                            @php
                            $deadlineDate = \Carbon\Carbon::createFromFormat('d-m-y', $val->deadline);
                            $currentDate = \Carbon\Carbon::today();
                            $isFutureOrToday = $deadlineDate->greaterThanOrEqualTo($currentDate);
                            @endphp
                            <span style="color: {{ $isFutureOrToday ? 'green' : 'red' }};">
                                {{ $val->deadline }}
                            </span>
                        </td>
                        <td>{{ $val->customer_ref }}</td>
                        <td>{{ $val->witrack_no }}</td>
                        {{--<td>{{ $val->project_no }}</td>--}}
                        <td>{{ $val->project_name }}</td>
                        <td>{{ $val->country }}</td>
                        <td>{{ $val->customer_name }}</td>
                        <td>{{ $val->sales_name }}</td>
                        @php
                        $projectTypesJson = $val->product_type;
                        $projectTypesArray = json_decode($projectTypesJson);
                        if (is_array($projectTypesArray)) {
                        $commaSeparatedprojectTypes = implode(", ", $projectTypesArray);
                        } else {
                        $commaSeparatedprojectTypes = $projectTypesJson;
                        }
                        @endphp
                        <td>
                            <a href="javascript:void(0);" id="open-documents-modal" data-id="{{ $val->id }}"
                                title="Open Documents">
                                <i class="p-2 m-1 fa fa-file project_icon"></i>
                            </a>
                        </td>
                        <td class="d-flex justify-content-center">
                            <a href="{{ route('ProductionManagerProjectCreate',
                           [
                                'project_name' => 'wi_track',
                                'id' => $val->id,
                                'customer_ref_no' => $val->customer_ref,
                                'witrack_no' => $val->witrack_no,
                                'internal_project_name' => $val->project_name,
                                'country_name' => $val->country,
                                'customer_name' => $val->customer_name,
                                'sales_name' => $val->sales_name,
                                'docs' => $val->documents
                            ])
                            }}" id="" data-id="{{ $val->id }}" data-customer-ref-no="{{ $val->customer_ref }}"
                                data-witrack-no="{{ $val->witrack_no }}" data-project-no="{{ $val->project_no }}"
                                data-project-name="{{ $val->project_name }}" data-country-name="{{ $val->country }}"
                                data-customer-name="{{ $val->customer_name }}" data-sales-name="{{ $val->sales_name }}"
                                data-docs="{{ $val->documents }}" title="Click here to process this order">
                                <i class="p-2 m-1 fa fa-plus project_icon"> Process Order</i>
                            </a>
                            <a href="javascript:void(0);" class="reject-order-btn" data-bs-toggle="modal"
                                data-bs-target="#rejectOrderModal" data-id="{{ $val->id }}"
                                title="Click here to reject this order" class="reject-button">
                                <i class="p-2 m-1 fa fa-times project_icon reject_bg"> Reject Order</i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="alert alert-info w-100">
                No Pending WITrack Orders found.
            </div>
            @endif
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center px-5 pt-3">
        <h3 class="pt-4 text-bold text-left text-uppercase">Rejected WITrack Order by Production Engineer</h3>
        <a class="mt-4 mb-2 btn btn-primary btn-sm py-2 px-3" onclick="location.reload();">
            <i class="fa fa-refresh text-light">&#xf021;</i>
        </a>
    </div>
    <hr class="mx-5 mt-2 mb-2" />
    <div class="container-fluid px-5 mx-3">
        <div class="row mt-3 table-responsive">
            @if(count($rejectedWITrackProjects) > 0)
            <table class="table table-hover table-border w-100 text-center" id="rejected_project_table">
                <thead>
                    <tr>
                        <th scope="col" class="project_table_heading p-1" style="width: 8% !important;">Date</th>
                        <th scope="col" class="project_table_heading p-1">Customer Ref.</th>
                        <th scope="col" class="project_table_heading p-1">WiTrack No.</th>
                        <th scope="col" class="project_table_heading p-1">Project Name</th>
                        <th scope="col" class="project_table_heading p-1">Country</th>
                        <th scope="col" class="project_table_heading p-1">Customer Name</th>
                        <th scope="col" class="project_table_heading p-1">Sales Name</th>
                        <th scope="col" class="project_table_heading p-1">Docs</th>
                        <th scope="col" class="project_table_heading p-1">Rejection Info</th>
                    </tr>
                </thead>
                <tbody id="rejected_project_table_body">
                    @foreach($rejectedWITrackProjects as $val)
                    <tr>
                        <td>{{ $val->created_at->format('d-m-y') }}</td>
                        <td>{{ $val->customer_ref }}</td>
                        <td>{{ $val->witrack_no }}</td>
                        <td>{{ $val->project_name }}</td>
                        <td>{{ $val->country }}</td>
                        <td>{{ $val->customer_name }}</td>
                        <td>{{ $val->sales_name }}</td>
                        @php
                        $projectTypesJson = $val->product_type;
                        $projectTypesArray = json_decode($projectTypesJson);
                        if (is_array($projectTypesArray)) {
                            $commaSeparatedprojectTypes = implode(", ", $projectTypesArray);
                        } else {
                            $commaSeparatedprojectTypes = $projectTypesJson;
                        }
                        @endphp
                        <td>
                            <a href="javascript:void(0);" id="open-documents-modal" data-id="{{ $val->id }}"
                                title="Open Documents">
                                <i class="p-2 m-1 fa fa-file project_icon"></i>
                            </a>
                        </td>
                        <td>
                            <a href="javascript:void(0);" class="rejection-info-btn" data-bs-toggle="modal"
                                data-bs-target="#rejectionInfoModal" data-id="{{ $val->id }}"
                                title="View Rejection Info">
                                <i class="p-2 m-1 fa fa-info-circle project_icon"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="alert alert-info w-100">
                No Rejected WITrack Orders found.
            </div>
            @endif
        </div>
    </div>
    
    <div class="d-flex justify-content-between align-items-center px-5 pt-3">
        <h3 class="pt-4 text-bold text-left text-uppercase">Cancelled Orders From WITrack Tool</h3>
        <a class="mt-4 mb-2 btn btn-primary btn-sm py-2 px-3" onclick="location.reload();">
            <i class="fa fa-refresh text-light">&#xf021;</i>
        </a>
    </div>
    <hr class="mx-5 mt-2 mb-2" />
    <div class="container-fluid px-5 mx-3">
        <div class="row mt-3 table-responsive">
            @if(count($cancelledWITrackProjects) > 0)
            <table class="table table-hover table-border w-100 text-center" id="cancelled_project_table">
                <thead>
                    <tr>
                        <th scope="col" class="project_table_heading p-1">Date</th>
                        <th scope="col" class="project_table_heading p-1">Customer Ref.</th>
                        <th scope="col" class="project_table_heading p-1">WiTrack No.</th>
                        <th scope="col" class="project_table_heading p-1">Project Name</th>
                        <th scope="col" class="project_table_heading p-1">Country</th>
                        <th scope="col" class="project_table_heading p-1">Customer Name</th>
                        <th scope="col" class="project_table_heading p-1">Sales Name</th>
                        <th scope="col" class="project_table_heading p-1">Docs</th>
                        <th scope="col" class="project_table_heading p-1">Cancellation Reason</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cancelledWITrackProjects as $val)
                    <tr class="bg-light text-danger">
                        <td>{{ $val->created_at->format('d-m-y') }}</td>
                        <td>{{ $val->customer_ref }}</td>
                        <td>{{ $val->witrack_no }}</td>
                        <td>{{ $val->project_name }}</td>
                        <td>{{ $val->country }}</td>
                        <td>{{ $val->customer_name }}</td>
                        <td>{{ $val->sales_name }}</td>
                        <td>
                            <a href="javascript:void(0);" id="open-documents-modal1" data-id="{{ $val->id }}"
                                title="Open Documents">
                                <i class="p-2 m-1 fa fa-file project_icon text-danger"></i>
                            </a>
                        </td>
                        <td>
                            <a href="javascript:void(0);" class="cancellation-info-btn" data-bs-toggle="modal"
                                data-bs-target="#cancellationInfoModal" data-id="{{ $val->id }}"
                                title="View Cancellation Info">
                                <i class="p-2 m-1 fa fa-info-circle project_icon"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="alert alert-info w-100">
                No Cancelled WITrack Orders found.
            </div>
            @endif
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center px-5 pt-3">
        <h3 class="pt-4 text-bold text-left text-uppercase">Pending Purchase Order Approvals</h3>
        <a class="mt-4 mb-2 btn btn-primary btn-sm py-2 px-3" onclick="location.reload();">
            <i class="fa fa-refresh text-light">&#xf021;</i>
        </a>
    </div>
    <hr class="mx-5 mt-2 mb-2" />
    <div class="container-fluid px-5 mx-3">
        <div class="row mt-3 table-responsive">
            @if(count($pendingPurchaseOrders) > 0)
            <table class="table table-hover table-border w-100 text-center" id="pending_purchase_order">
                <thead>
                    <tr>
                        <th scope="col" class="project_table_heading p-1">PO No.</th>
                        <th scope="col" class="project_table_heading p-1">Project No.</th>
                        <th scope="col" class="project_table_heading p-1">Project Name</th>
                        <th scope="col" class="project_table_heading p-1">Supplier</th>
                        <th scope="col" class="project_table_heading p-1">Order Date</th>
                        <th scope="col" class="project_table_heading p-1">Payment Terms</th>
                        <th scope="col" class="project_table_heading p-1">Action</th>
                    </tr>
                </thead>
                <tbody id="project_table_body">
                    @foreach($pendingPurchaseOrders as $val)
                    <tr>
                        <td>{{ $val->po_number }}</td>
                        <td>{{ $val->project_no ?? 'N/A' }}</td>
                        <td>{{ $val->project_name ?? 'N/A' }}</td>
                        <td>{{ $val->supplier ?? 'N/A'}}</td>
                        <td>{{ $val->order_date ?? 'N/A' }}</td>
                        <td>{{ $val->payment_terms ?? 'N/A' }}</td>
                        <td class="action">
                            <a href="{{ route('purchase_order_engineer.view', $val->po_id) }}"
                                class="btn btn-info btn-sm px-3 py-1">View PO</a>
                            <button class="btn btn-danger btn-sm m-0 px-3 py-1" data-bs-toggle="modal"
                                data-bs-target="#rejectModal" data-id="{{ $val->po_id }}">
                                <i class="fa fa-times"></i>
                            </button>

                            <button class="btn btn-success btn-sm m-0 px-3 py-1" data-bs-toggle="modal"
                                data-bs-target="#approveModal" data-id="{{ $val->po_id }}">
                                <i class="fa fa-check"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="alert alert-info w-100">
                No pending purchase orders found.
            </div>
            @endif
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white" id="rejectModalLabel">Reject Order</h5>
                </div>
                <div class="modal-body">
                    <textarea class="form-control" rows="3" id="rejectReason"
                        placeholder="Enter reason for rejection..." required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmReject">Reject</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="approveModalLabel">Approve Order</h5>
                </div>
                <div class="modal-body">
                    <textarea class="form-control" rows="3" id="approveRemarks"
                        placeholder="Enter remarks (optional)..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirmApprove">Approve</button>
                </div>
            </div>
        </div>
    </div>  

    <!-- Reject Order Modal -->
    <div class="modal fade" id="rejectOrderModal" tabindex="-1" aria-labelledby="rejectOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white" id="rejectOrderModalLabel">Reject WITrack Order</h5>
                </div>
                <div class="modal-body">
                    <label for="rejectionReason" class="form-label">
                        Select Reason for Rejection
                        <span class="text-danger">*</span>
                    </label>
                    <select class="form-control" id="rejectionReason" required>
                        <option value="" disabled selected>Select a reason</option>
                        <option value="Incorrect Pump Datasheet">Incorrect Pump Datasheet</option>
                        <option value="Discrepancy in order documents">Discrepancy in order documents</option>
                        <option value="Incorrect Assembly quotation">Incorrect Assembly quotation</option>
                        <option value="Customer PO does not match with Assembly quotation">Customer PO does not match with Assembly quotation</option>
                        <option value="No approved drawings provided">No approved drawings provided</option>
                        <!-- give Other a value (recommended) -->
                        <option value="Other">Other</option>
                    </select>
                    <!-- Hidden textbox for 'Other' reason -->
                    <div id="otherReasonContainer" class="mt-3" style="display: none;">
                        <label for="otherReason" class="form-label">Please specify <span class="text-danger">*</span></label>
                        <input type="text" id="otherReason" class="form-control" placeholder="Enter reason here">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger confirm_reject_order" id="confirmRejectOrder">Reject</button>

                    <!-- Loader (hidden by default) -->
                    <div id="rejectLoader" class="spinner-border text-light ms-2 reject_loader" role="status"
                     style="display:none; width: 1.5rem; height: 1.5rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rejection Info Modal -->
    <div class="modal fade" id="rejectionInfoModal" tabindex="-1" aria-labelledby="rejectionInfoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white" id="rejectionInfoModalLabel">Rejection Information</h5>
                </div>
                <div class="modal-body">
                    <p><strong>Rejection Date:</strong> <span id="rejectionDate">N/A</span></p>
                    <p><strong>Rejection Reason:</strong> <span id="rejectionReasonText">N/A</span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancellation Info Modal -->
    <div class="modal fade" id="cancellationInfoModal" tabindex="-1" aria-labelledby="cancellationInfoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white" id="cancellationInfoModalLabel">Cancellation Reason</h5>
                </div>
                <div class="modal-body">
                    <span id="cancellationReasonText">N/A</span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-bold" id="documentsModal" tabindex="-1" role="dialog"
        aria-labelledby="documentsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <a href="#" class="modal_show_back project_icon p-1 text-decoration-none">
                        <i class="fa fa-arrow-left mx-2 project_icon"></i>
                    </a>
                    <h5 class="modal-title text-white" id="documentsModalLabel">Project Documents</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul id="documentsList" class="list-group">
                    </ul>
                </div>
                <div class="modal-footer">
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
            $('#project_table1').DataTable({
                paging: true,
                pageLength: 2,
                lengthMenu: [2, 5, 10, 25, 50, 100],
                searching: true,
                ordering: false
            });
            $('#project_table1').removeClass('dataTable');

            $('#rejected_project_table').DataTable({
                paging: true,
                pageLength: 2,
                lengthMenu: [2, 5, 10, 25, 50, 100],
                searching: true,
                ordering: false
            });
            $('#rejected_project_table').removeClass('dataTable');

            $('#cancelled_project_table').DataTable({
                paging: true,
                pageLength: 2,
                lengthMenu: [2, 5, 10, 25, 50, 100],
                searching: true,
                ordering: false
            });
            $('#cancelled_project_table').removeClass('dataTable');

            $('#pending_purchase_order').DataTable({
                paging: true,
                pageLength: 2,
                lengthMenu: [2, 5, 10, 25, 50, 100],
                searching: true,
                ordering: false
            });
            $('#pending_purchase_order').removeClass('dataTable');

            $('#nameplate_table').DataTable({
                paging: true,
                pageLength: 2,
                lengthMenu: [2, 5, 10, 25, 50, 100],
                searching: true,
                ordering: false
            });
            $('#nameplate_table').removeClass('dataTable');

            $('#project_table_pending_task').DataTable({
                paging: true,
                pageLength: 2,
                lengthMenu: [2, 5, 10, 25, 50, 100],
                searching: false,
                ordering: false
            });
            $('#project_table_pending_task').removeClass('dataTable');

            $('#project_table_all_task').DataTable({
                paging: true,
                pageLength: 10,
                lengthMenu: [2, 5, 10, 25, 50, 100],
                searching: false,
                ordering: false
            });
            $('#project_table_all_task').removeClass('dataTable');

            $('#completed_projects').DataTable({
                paging: true,
                pageLength: 10,
                lengthMenu: [2, 5, 10, 25, 50, 100],
                searching: false,
                ordering: false
            });
            $('#completed_projects').removeClass('dataTable');
        });

        $(document).on('click', '.upload-image-btn', function() {
            $(this).siblings('.upload-image-input').trigger('click');
        });
        $(document).on('click', '.edit-image-btn', function() {
            $(this).siblings('.edit-image-input').trigger('click');
        });

        $(document).on('change', '.upload-image-input, .edit-image-input', function() {
            const csrfToken = "{{ csrf_token() }}";
            let file_data = $(this)[0].files[0];
            var id = $(this).data('id');
            var lable = $(this).data('lable');
            var isEdit = $(this).hasClass('edit-image-input');
            var projectId = $(this).data('project-id');
            var articleNumber = $(this).data('article-number');
            var qtyNo = $(this).data('qty-no');

            // Get reference to the current row
            var currentRow = $(this).closest('tr');

            let formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('file', file_data);
            formData.append('id', id);
            formData.append('lable', lable);
            formData.append('is_edit', isEdit ? 1 : 0);
            formData.append('project_id', projectId);
            formData.append('article_number', articleNumber);
            formData.append('qty_no', qtyNo);

            $.ajax({
                url: "{{ route('ProductionManagerUploadNameplateImg') }}",
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    alert('File ' + (isEdit ? 'updated' : 'uploaded') + ' successfully!');

                    // Update the button state dynamically
                    var confirmBtn = currentRow.find('.confirm-btn');
                    confirmBtn.removeClass('btn-secondary').addClass('btn-success');
                    confirmBtn.prop('disabled', false);

                    // Update the image preview if it's an upload (not edit)
                    if (!isEdit) {
                        var imagePath = response.file.path;
                        var imageHtml = `
                    <div class="d-flex align-items-center">
                        <a href="${imagePath}" target="_blank" title="Click to view full image">
                            <img src="${imagePath}" width="50" height="50" class="me-2" style="cursor: pointer; border: 1px solid #ddd; border-radius: 4px;">
                        </a>
                        <button type="button" class="btn btn-warning btn-sm edit-image-btn py-1 px-2">
                            <i class="fa fa-edit"></i>
                        </button>
                        <input type="file" class="d-none edit-image-input" 
                               data-id="${id}" 
                               data-lable="nameplate_img" 
                               data-project-id="${projectId}" 
                               data-article-number="${articleNumber}" 
                               data-qty-no="${qtyNo}" 
                               accept="image/*">
                    </div>`;
                        currentRow.find('.project_check_status').html(imageHtml);
                    }

                    // Optional: Avoid full page reload for better UX
                    location.reload();
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
                        alert('Error ' + (isEdit ? 'updating' : 'uploading') + ' file.');
                    }
                }
            });
        });

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

        $(document).on('click', '#open-documents-modal', function() {
            $('#foldersList').show();
            $('#documentsModal').modal('show');
            const subfolderName = 'From Customers';
            if (subfolderName === 'From Customers') {
                loadDocuments(subfolderName);
            }
        });

        $(document).on('click', '#open-documents-modal1', function() {
            $('#foldersList').show();
            $('#documentsModal').modal('show');
            const subfolderName = 'From Customers';
            if (subfolderName === 'From Customers') {
                loadDocuments(subfolderName);
            }
        });

        function loadDocuments(folderName) {
            var projectId = $('#open-documents-modal').data('id');
            $.ajax({
                url: '{{ route("getProjectDocumentsForInbox") }}',
                type: 'GET',
                data: {
                    id: projectId,
                    folder: folderName
                },
                success: function(response) {
                    console.log(response.documents);
                    if (!response.documents || response.documents.length === 0) {
                        $('#documentsList').html(`
                            <li class="list-group-item">
                                ${response.message ? response.message : 'No documents found'}
                            </li>
                        `);
                        return;
                    }
                    if (response.documents) {
                        $('#foldersList').hide();
                        $('#documentsList').empty().show();

                        $.each(response.documents, function(index, document) {
                            var fileExtension = document.name.split('.').pop().toLowerCase();
                            var iconClass = getIconClass(fileExtension);

                            $('#documentsList').append(`
                                    <li class="list-group-item">
                                        <a href="{{ asset('') }}${document.path}" target="_blank">
                                            <i class="${iconClass}"></i> ${document.name}
                                        </a>
                                    </li>
                                `);
                        });
                    }
                },
                error: function(xhr) {
                    alert('Failed to fetch documents.');
                }
            });
        }

        function getIconClass(fileExtension) {
            if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
                return "fs-30 fa fa-image";
            } else if (fileExtension === 'pdf') {
                return "fs-30 fa fa-file-pdf";
            } else if (['doc', 'docx'].includes(fileExtension)) {
                return "fs-30 fa fa-file-word";
            } else if (['xlsx', 'csv'].includes(fileExtension)) {
                return "fs-30 fa fa-file-excel";
            } else {
                return "fs-30 fa fa-file";
            }
        }

        $(document).on('click', '.modal_show_back', function() {
            $('#foldersList .subfolder-item').closest('ul').hide();
            $('#documentsList').hide();
            $('#foldersList').show();
        });

        $(document).on('click', '.rejection-info-btn', function() {
            const projectId = $(this).data('id');
            $.ajax({
                url: '{{ route("getRejectionDetails") }}',
                type: 'GET',
                data: {
                    project_id: projectId
                },
                success: function(response) {
                    if (response.rejection_date && response.rejection_reason) {
                        $('#rejectionDate').text(response.rejection_date);
                        $('#rejectionReasonText').text(response.rejection_reason);
                    } else {
                        $('#rejectionDate').text('N/A');
                        $('#rejectionReasonText').text('No reason provided');
                    }
                    $('#rejectionInfoModal').modal('show');
                },
                error: function(xhr) {
                    alert('Failed to fetch rejection details.');
                }
            });
        });
        $(document).on('click', '.cancellation-info-btn', function() {
            const projectId = $(this).data('id');
            $.ajax({
                url: '{{ route("getCancellationDetails") }}',
                type: 'GET',
                data: {
                    project_id: projectId
                },
                success: function(response) {
                    if (response.cancellation_reason) {
                        $('#cancellationReasonText').text(response.cancellation_reason);
                    } else {
                        $('#cancellationReasonText').text('No reason provided');
                    }
                    $('#cancellationInfoModal').modal('show');
                },
                error: function(xhr) {
                    alert('Failed to fetch rejection details.');
                }
            });
        });

        $(document).ready(function() {
            // Set the data-id attribute for the modals
            $('#approveModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const poId = button.data('id');
                $(this).data('id', poId);
            });

            $('#rejectModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const poId = button.data('id');
                $(this).data('id', poId);
            });

            // Handle approve button click
            $('#confirmApprove').on('click', function() {
                const poId = $('#approveModal').data('id');
                const remarks = $('#approveRemarks').val();

                $.ajax({
                    url: '/purchase-order-engineer/approve/' + poId,
                    method: 'POST',
                    data: {
                        remarks: remarks,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        alert(response.success);
                        location.reload();
                    },
                    error: function(xhr) {
                        console.error('Error approving purchase order:', xhr);
                        alert('Failed to approve purchase order.');
                    }
                });
            });

            // Handle reject button click
            $('#confirmReject').on('click', function() {
                const poId = $('#rejectModal').data('id');
                const reason = $('#rejectReason').val();

                // Check if reason is empty
                if (!reason) {
                    alert('Please provide a reason for rejection.');
                    $('#rejectReason').focus();
                    return;
                }

                $.ajax({
                    url: '/purchase-order-engineer/reject/' + poId,
                    method: 'POST',
                    data: {
                        reason: reason,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        alert(response.success);
                        location.reload();
                    },
                    error: function(xhr) {
                        console.error('Error rejecting purchase order:', xhr);
                        alert('Failed to reject purchase order.');
                    }
                });
            });

            // Set the data-id attribute for the reject order modal
            $('#rejectOrderModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const projectId = button.data('id');
                $(this).data('id', projectId);
            });

            // When rejection reason changes
            $('#rejectionReason').on('change', function () {
                const selected = $(this).val();

                if (selected === 'Other') {
                    // Show textbox if "Other" is selected
                    $('#otherReasonContainer').slideDown();
                    $('#otherReason').attr('required', true);
                } else {
                    // Hide textbox if not "Other"
                    $('#otherReasonContainer').slideUp();
                    $('#otherReason').removeAttr('required').val('');
                }
            });

            // Handle reject order button click
            $('#confirmRejectOrder').on('click', function () {
                const projectId = $('#rejectOrderModal').data('id');
                const reason = $('#rejectionReason').val();
                const otherReason = $('#otherReason').val().trim();

                // Validation
                if (!reason) {
                    alert('Please select a reason for rejection.');
                    $('#rejectionReason').focus();
                    return;
                }

                // If "Other" selected, require textbox
                let finalReason = reason;
                if (reason === 'Other') {
                    if (!otherReason) {
                        alert('Please specify Other reason for rejection.');
                        $('#otherReason').focus();
                        return;
                    }
                    finalReason = otherReason;
                }

                // Show loader & disable button
                $('#rejectLoader').show();
                $('#confirmRejectOrder').prop('disabled', true);

                // AJAX call
                $.ajax({
                    url: '{{ route("ProductionManagerRejectOrder") }}',
                    method: 'POST',
                    data: {
                        project_id: projectId,
                        reason: finalReason,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        alert(response.success);
                        location.reload();
                    },
                    error: function (xhr) {
                        console.error('Error rejecting order:', xhr);
                        alert('Failed to reject order.');
                    },
                    complete: function () {
                        // Always hide loader and re-enable button
                        $('#rejectLoader').hide();
                        $('#confirmRejectOrder').prop('disabled', false);
                    }
                });
            });

        });
    </script>
@endsection