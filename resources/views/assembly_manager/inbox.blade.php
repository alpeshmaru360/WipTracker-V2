@extends('layouts.main')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/assembly_manager.css') }}" />

<div class="main_section bg-white m-4 pb-5">

    <div class="d-flex justify-content-between align-items-center px-5 pt-3">
        <h3 class="pt-4 text-bold text-left text-uppercase">Pending Purchase Order Approvals</h3>
        <a class="mt-4 mb-2 btn btn-primary btn-sm py-2 px-3" onclick="location.reload();">
            <i class="fa fa-refresh text-light"></i>
        </a>
    </div>
    <hr class="mx-5 mt-2 mb-2" />

    <div class="container-fluid px-5 mx-3">
        <div class="row mt-3 table-responsive">
            @if(count($pendingPurchaseOrders) > 0)
            
            <table class="table table-hover table-bordered w-100 text-center" id="pending_purchase_order">
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
                        <td>
                            <a href="{{ route('purchase_order.view', $val->po_id) }}" class="btn btn-info btn-sm px-3 py-1">View PO</a>
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
                No pending purchase orders found. All purchase orders have been processed.
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
                    <textarea class="form-control" rows="3" id="rejectReason" placeholder="Enter reason for rejection..." required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="cancel-black" data-bs-dismiss="modal">Cancel</button>
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
                    <h5 class="modal-title text-white" id="approveModalLabel">Approve Order</h5>
                </div>
                <div class="modal-body">
                    <textarea class="form-control" rows="3" id="approveRemarks" placeholder="Enter remarks (optional)..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="cancel-black" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirmApprove">Approve</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#pending_purchase_order').DataTable({
            paging: true,
            pageLength: 2,
            lengthMenu: [2, 5, 10, 25, 50, 100],
            searching: true,
            ordering: false
        });
        $('#pending_purchase_order').removeClass('dataTable');

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
                url: '/purchase-order/approve/' + poId,
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
            const reason = $('#rejectReason').val().trim();

            // Check if reason is empty
            if (!reason) {
                alert('Please provide a reason for rejection.');
                $('#rejectReason').focus();
                return; // Stop execution if validation fails
            }

            // Proceed with AJAX request if validation passes
            $.ajax({
                url: '/purchase-order/reject/' + poId,
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

    });
</script>
@endsection