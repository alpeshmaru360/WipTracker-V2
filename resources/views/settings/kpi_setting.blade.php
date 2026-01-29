@extends('layouts.main')

@section('content')

<link href="{{ asset('css/kpisetting.css') }}" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
<div class="main_container d-flex">

    @include('layouts.setting')

    <div class="main_section bg-white flex-grow-1">
        <div class="container kpi_container">
            <h1>KPI Setting Table</h1>

            @if(session('success'))
                <div class="alert alert-success mt-3">
                    {{ session('success') }}
                </div>
            @endif

            <table class="table table-bordered text-center kpi-table">
                <thead>
                    <tr>
                        <th>SR No.</th>
                        <th>Label</th>
                        <th>Key Name</th>
                        <th>Value</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php $sr_no = 0; @endphp
                    @foreach($kpisetting as $kpi)
                        @php $sr_no++; @endphp
                        <tr>
                            <td>{{ $sr_no }}</td>
                            <td>{{ $kpi->label }}</td>
                            <td>{{ $kpi->key_name }}</td>
                            <td>{{ $kpi->value }}</td>
                            <td>
                                <span class="badge {{ $kpi->status ? 'bg-success' : 'bg-danger' }}">
                                    {{ $kpi->status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <button 
                                    class="btn btn-primary edit-btn"
                                    data-id="{{ $kpi->id }}"
                                    data-label="{{ $kpi->label }}"
                                    data-key="{{ $kpi->key }}"
                                    data-value="{{ $kpi->value }}"
                                    data-status="{{ $kpi->status }}">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>


<div class="modal" id="editModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit KPI</h5>
            </div>
            <form id="editForm" method="POST" action="{{ route('kpi.update') }}">
                @csrf
                <input type="hidden" name="id" id="editId">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="editLabel">Label</label>
                        <input type="text" name="label" id="editLabel" class="form-control" required readonly>
                    </div>
                    <div class="form-group">
                        <label for="editKey">Key</label>
                        <input type="text" name="key" id="editKey" class="form-control" required readonly>
                    </div>
                    <div class="form-group">
                        <label for="editValue">Value<span class="text-danger">*</span></label>
                        <input type="text" name="value" id="editValue" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="editStatus">Status<span class="text-danger">*</span></label>
                        <select name="status" id="editStatus" class="form-control">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success savechange">Save Changes</button>
                    <button type="button" class="btn btn-secondary cancle-button" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection

@section('scripts')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const editButtons = document.querySelectorAll('.edit-btn');
    const editModal = document.getElementById('editModal');
    const bootstrapModal = new bootstrap.Modal(editModal);
    const editForm = document.getElementById('editForm');
    const editValueInput = document.getElementById('editValue');

    editButtons.forEach(button => {
        button.addEventListener('click', function () {
            document.getElementById('editId').value = this.dataset.id;
            document.getElementById('editLabel').value = this.dataset.label;
            document.getElementById('editKey').value = this.dataset.key;
            document.getElementById('editValue').value = this.dataset.value;
            document.getElementById('editStatus').value = this.dataset.status;

            bootstrapModal.show();
        });
    });

    editForm.addEventListener('submit', function (event) {
        let isValid = true;

        if (editValueInput.value.trim() === '') {
            isValid = false;
            showError(editValueInput, "Value is required.");
        } else {
            removeError(editValueInput);
        }

        if (!isValid) {
            event.preventDefault();
        }
    });

    function showError(input, message) {
        let errorSpan = input.nextElementSibling;
        if (!errorSpan || !errorSpan.classList.contains('text-danger')) {
            errorSpan = document.createElement('span');
            errorSpan.classList.add('text-danger');
            input.parentNode.appendChild(errorSpan);
        }
        errorSpan.textContent = message;
    }

    function removeError(input) {
        let errorSpan = input.nextElementSibling;
        if (errorSpan && errorSpan.classList.contains('text-danger')) {
            errorSpan.remove();
        }
    }
});

</script>

@endsection
