@extends('layouts.main')
@section('content')
<link href="{{ asset('css/production_manager.css') }}" rel="stylesheet" />
<link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet" />
<link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css" rel="stylesheet" />

<style>
    table.dataTable thead tr th {
        border: 1px solid #d9e0e3 !important;
    }

    #remarksModal {
        margin-top: 50px;
    }

    #remarksModalLabel {
        color: #fff;
    }

    .fa-download {
        color: white;
    }

    .fa-download:hover {
        color: white;
    }
</style>

<div class="main_section bg-white m-4 pb-5">
    <div class="container mt-3">
        <div class="justify-content-end row">
            <div class="btn-group mt-4">
            </div>
            <div class="btn-group mt-4">
                <a class="btn-lg project_index_btn ml-3" href="{{route('addNCR')}}" title="click here to add new product"><i class="fa fa-fw fa-plus"></i> Add New</a>
            </div>
        </div>
        <div class="row mt-3">
            <div class="w-100">
                <table class="table table-hover table-border text-center" id="project_table">
                    <thead>
                        <tr>
                            <th>CIA No</th>
                            <th>NCR Type</th>
                            <th>Responsible</th>
                            <th>Department</th>
                            <th>Started Date</th>
                            <th>Planned Finish Date</th>
                            <th>Action Closed Date</th>
                            <th>Remarks</th>
                            <th>Action</th>
                            <th>Download</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ncrRecords as $ncr)
                        <tr>
                            <td>{{ $ncr->cia_no }}</td>
                            <td>{{ $ncr->ncr_type }}</td>
                            <td>{{ $ncr->name_surname }}</td>
                            <td>{{ $ncr->related_dep }}</td>
                            <td>{{ date('d-m-Y', strtotime($ncr->created_at)) }}</td>
                            <td>{{ date('d-m-Y', strtotime($ncr->planned_action_date)) }}</td>
                            <td>{{ date('d-m-Y', strtotime($ncr->action_closed_date)) }}</td>
                            <td>
                                <button class="btn btn-primary btn-sm remark-btn" data-id="{{ $ncr->id }}" data-remark="{{ $ncr->remark ?? '' }}">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </td>
                            <td>
                                <a href="{{ route('editNCR', $ncr->id) }}" title="Edit" class="p-2 m-1 fa fa-pen project_icon"></a>
                            </td>
                            <td>
                                <a href="{{ $ncr->pdf_url }}" title="Download PDF" class="p-2 m-1 fa fa-download project_icon" download></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Bootstrap Modal for Remarks -->
<div class="modal fade" id="remarksModal" tabindex="-1" aria-labelledby="remarksModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="remarksModalLabel">Add/Edit Remarks</h5>
            </div>
            <div class="modal-body">
                <form id="remarksForm">
                    @csrf
                    <input type="hidden" id="ncr_id" name="id">
                    <div class="form-group">
                        <label for="remark">Remarks</label>
                        <textarea class="form-control" id="remark" name="remark" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Update</button>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        Close
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script>
    $(document).ready(function() {
        $('#project_table').DataTable({
            responsive: true,
            "pageLength": 10,
            "lengthMenu": [
                [5, 10, 25, 50, -1],
                [5, 10, 25, 50, "All"]
            ],
            "order": [],
            "columnDefs": [{
                    "orderable": false,
                    "targets": [7, 8, 9]
                } // Disable ordering for columns 0 and 2 (adjust indices as needed)
            ],
            "language": {
                "search": "Search:",
                "lengthMenu": "Show _MENU_ entries",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "infoEmpty": "Showing 0 to 0 of 0 entries",
                "infoFiltered": "(filtered from _MAX_ total entries)",
                "paginate": {
                    "first": "First",
                    "last": "Last",
                    "next": "Next",
                    "previous": "Previous"
                }
            }
        });
    });
</script>
<script>
    $(document).ready(function() {
        // Open Modal and Populate Data
        $('.remark-btn').click(function() {
            var ncrId = $(this).data('id');
            var remark = $(this).data('remark');

            $('#ncr_id').val(ncrId);
            $('#remark').val(remark);
            $('#remarksModal').modal('show');
        });

        // Handle Form Submission
        $('#remarksForm').submit(function(e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: "{{ route('updateNCRRemarks') }}",
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert('Failed to update remark.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    alert('An error occurred while updating remark.');
                }
            });
        });
    });
</script>
@endsection