@extends('layouts.main')
@section('content')

<link rel="stylesheet" href="{{ asset('css/initial_inspection.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" />

<div class="main_container d-flex">
    @include('layouts.setting')

    <div class="main_section py-5 bg-white my-4">
        <div class="container-fluid initial_container">
            <h1>Initial Inspection Name</h1>

            @if(session('success'))
                <div class="alert alert-success mt-3">
                    {{ session('success') }}
                </div>
            @endif

            <table class="table table-bordered text-center initial-table">
                <thead>
                    <tr>
                        <th>SR No.</th>
                        <th>Inspection Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php $sr_no = 0; @endphp
                    @foreach($initialInspections as $inspection)
                        @php $sr_no++; @endphp
                        <tr>
                            <td>{{ $sr_no }}</td>
                            <td>{{ $inspection->name }}</td>
                            <td>
                                <button 
                                    class="btn btn-primary edit-btn"
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

    <!-- 🟠 Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Inspection Name</h5>
                </div>
                <div class="modal-body">
                    <form id="editForm" method="POST" action="{{ route('initial.inspection.update') }}">
                        @csrf
                        <input type="hidden" name="id" id="edit-id">
                        <div class="mb-3">
                            <label for="edit-name" class="form-label">Inspection Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit-name" name="name" required>
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

<!-- 🟢 JS to Fill Modal -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function() {
        $('.edit-btn').on('click', function() {
            let id = $(this).data('id');
            let name = $(this).data('name');

            $('#edit-id').val(id);
            $('#edit-name').val(name);
        });
    });
</script>
@endsection