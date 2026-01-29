@extends('layouts.main')
@section('content')

<link rel="stylesheet" href="{{ asset('css/final_inspection.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" />

<div class="main_container d-flex">
    @include('layouts.setting')

    <div class="main_section py-5 bg-white my-4">
        <div class="container-fluid final_container">
            <h1>Final Inspection Name</h1>

            @if(session('success'))
                <div class="alert alert-success mt-3">
                    {{ session('success') }}
                </div>
            @endif

            <table class="table table-bordered text-center final-table">
                <thead>
                    <tr>
                        <th>SR No.</th>
                        <th>Inspection Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php $sr_no = 0; @endphp
                    @foreach($finalInspections as $inspection)
                        @php $sr_no++; @endphp
                        <tr>
                            <td>{{ $sr_no }}</td>
                            <td>{{ $inspection->name }}</td>
                            <td>
                                <button class="btn btn-primary edit-btn"
                                    data-id="{{ $inspection->id }}"
                                    data-name="{{ $inspection->name }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Final Inspection</h5>
                </div>
                <div class="modal-body">
                    <form id="editForm" method="POST" action="{{ route('final-inspection.update') }}">
                        @csrf
                        @method('PUT')

                        <input type="hidden" id="editInspectionId" name="id">

                        <div class="mb-3">
                            <label for="editInspectionName" class="form-label">Inspection Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editInspectionName" name="name" required>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary savechange">Save Changes</button>
                            <button type="button" class="btn btn-secondary cancle-button" data-bs-dismiss="modal">Cancel</button>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.edit-btn');

        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');

                // Fill modal form fields
                document.getElementById('editInspectionId').value = id;
                document.getElementById('editInspectionName').value = name;
            });
        });
    });

</script>
@endsection