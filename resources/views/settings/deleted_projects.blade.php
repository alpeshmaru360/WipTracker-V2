@extends('layouts.main')
@section('content')

<link rel="stylesheet" href="{{ asset('css/settings.css') }}" />
<div class="procurement_std_time_page main_container d-flex">

    @include('layouts.setting')

    <div class="main_section py-5 bg-white my-4">
        <div class="container-fluid settings_container">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="setting_page_title py-2 text-left">Deleted Projects</h1>
            </div>

            @if ($errors->any())
            <div class="alert alert-danger mt-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            @if (session('error'))
            <div class="alert alert-danger mt-3">
                {{ session('error') }}
            </div>
            @endif
            @if(session('success'))
            <div class="alert alert-success mt-3">
                {{ session('success') }}
            </div>
            @endif

            <table class="table table-bordered text-center settings-table deleted_projects_table" id="deleted_projects_table">
                <thead>
                    <tr>
                        <th>SR No.</th>
                        <th>Product No</th>
                        <th>Project Name</th>    
                        <th>Customer Ref.</th>
                        <th>Sales Number.</th>
                        <th>Country</th>
                        <th>Customer Name</th>
                        <th>Sales Name</th>
                        <th>Project Delete Reason</th>
                        <th>Deleted Date</th>
                    </tr>
                </thead>
                <tbody>
                    @php $sr_no = 0; @endphp
                    @foreach($projects as $project)
                    @php $sr_no++; 
                    @endphp
                    <tr>
                        <td>{{ $sr_no }}</td>
                        <td>{{ $project->project_no }}</td>
                        <td>{{ $project->project_name }}</td>                        
                        <td>{{ $project->customer_ref }}</td>
                        <td>{{ $project->sales_order_number }}</td>
                        <td>{{ $project->country }}</td>
                        <td>{{ $project->customer_name }}</td>
                        <td>{{ $project->sales_name }}</td>
                        <!-- A Code: 21-01-2026 Start -->
                        <td>
                            {{ Str::limit($project->project_delete_reason, 20) }}
                            @if (strlen($project->project_delete_reason) > 20)
                                <a href="#" class="read-more" data-description="{{ $project->project_delete_reason }}">
                                    Read more
                                </a>
                            @endif
                        </td>
                        <!-- A Code: 21-01-2026 End -->
                        <td>{{ \Carbon\Carbon::parse($project->deleted_at)->format('d-m-Y h:i A') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- A Code: 21-01-2026 Start -->
    <div class="modal fade delete-reason-modal" id="deleteReasonModal" tabindex="-1" aria-labelledby="deleteReasonModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-custom text-white">
                    <h5 class="modal-title" id="deleteReasonModalLabel">Full Description</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body" id="fullDescriptionContent">
                </div>
            </div>
        </div>
    </div>
    <!-- A Code: 21-01-2026 End -->

</div>
@endsection
@section('scripts')
<script src="https://cdn.datatables.net/responsive/3.0.3/js/dataTables.responsive.min.js"></script>
<script>
    $(document).ready(function() {  
        $('#deleted_projects_table').DataTable();
        $('#deleted_projects_table').removeClass('dataTable');
    });
    // A Code: 21-01-2026 Start
    $(document).on('click', '.read-more', function (event) {
        event.preventDefault();
        const additionalNotes = $(this).data('description');
        $('#fullDescriptionContent').text(additionalNotes);

        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const descriptionModal = new bootstrap.Modal(document.getElementById('deleteReasonModal'));
            descriptionModal.show();
        } else {
            $('#deleteReasonModal').modal('show');
        }
    });
    // A Code: 21-01-2026 End
</script>
@endsection