@extends('layouts.main')
@section('content')

<link rel="stylesheet" href="{{ asset('css/settings.css') }}" />
<link rel="stylesheet" href="{{ asset('css/product_types.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" />

<div class="main_container d-flex">
    @include('layouts.setting')

    <div class="main_section py-5 bg-white my-4">
        <div class="container-fluid product_container">         

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="setting_page_title py-2 text-left">Product Types Table</h1>
                <button class="btn btn-primary mb-4 float-right add-button" data-bs-toggle="modal" data-bs-target="#addModal">+ Add Product Type</button>
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
            
            <div class="table-responsive"><!-- A Code: 15-12-2025 -->
                <table class="table table-bordered text-center product-table" id="product_types_table">
                    <thead>
                        <tr>
                            <th>SR No.</th>
                            <th>Product Type Name</th>
                            <th>Product Family Number</th>
                            <th>Capacity</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $sr_no = 0; @endphp
                        @foreach($producttypes as $producttype)
                            @php $sr_no++; @endphp
                            <tr>
                                <td>{{ $sr_no }}</td>
                                <td>{{ $producttype->project_type_name }}</td>
                                <td>{{ $producttype->product_family_number }}</td>
                                <td>{{ $producttype->limitation_per_shift }}</td>
                                <td>
                                    @if($producttype->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <button 
                                        class="btn btn-primary edit-btn"
                                        data-id="{{ $producttype->id }}"
                                        data-name="{{ $producttype->project_type_name }}"
                                        data-family_number="{{ $producttype->product_family_number }}"
                                        data-operator="{{ $producttype->operator_id }}"
                                        data-capacity="{{ $producttype->limitation_per_shift }}"
                                        data-is_active="{{ $producttype->is_active }}">
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

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Add Product Type</h5>
                </div>
                <div class="modal-body">
                    <form id="addProductForm" method="POST" action="{{ route('product-types.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="add_product_type_name" class="form-label">Product Type Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="add_product_type_name" 
                            name="project_type_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="add_product_family_number" class="form-label">Product Family Number<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="add_product_family_number" 
                            name="product_family_number" required>
                        </div>
                        <div class="mb-3">
                            <label for="add_capacity" class="form-label">Capacity<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="add_capacity" name="capacity" required>
                        </div>
                        <div class="mb-3">
                            <label for="add_is_active" class="form-label">Status<span class="text-danger">*</span></label>
                            <select class="form-control" id="add_is_active" name="is_active" required>
                                <option value="1" selected>Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success savechange">Add</button>
                            <button type="button" class="btn btn-secondary cancle-button" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Product Type</h5>
                </div>
                <div class="modal-body">
                    <form id="editProductForm" method="POST" action="{{ route('product-types.update') }}">
                        @csrf
                        <input type="hidden" id="product_type_id" name="id">
                        <div class="mb-3">
                            <label for="project_type_name" class="form-label">Product Type Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="project_type_name" 
                            name="project_type_name" required readonly>
                        </div>
                        <div class="mb-3">
                            <label for="product_family_number" class="form-label">Product Family Number<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="product_family_number" 
                            name="product_family_number" required>
                        </div>                        
                        <div class="mb-3">
                            <label for="capacity" class="form-label">Capacity<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="capacity" name="capacity" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_is_active" class="form-label">Status<span class="text-danger">*</span></label>
                            <select class="form-control" id="edit_is_active" name="is_active" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success savechange">Save Changes</button>
                            <button type="button" class="btn btn-secondary canclebutton" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        $('#product_types_table').DataTable({
            paging: true,
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            searching: true,
            ordering: false,
            info: false
        });
        $('#product_types_table').removeClass('dataTable');
    });

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function () {
                document.getElementById('product_type_id').value = this.getAttribute('data-id');
                document.getElementById('project_type_name').value = this.getAttribute('data-name');
                document.getElementById('product_family_number').value = this.getAttribute('data-family_number');   
                document.getElementById('capacity').value = this.getAttribute('data-capacity');
                document.getElementById('edit_is_active').value = this.getAttribute('data-is_active');
                new bootstrap.Modal(document.getElementById('editModal')).show();
            });
        });
    });
</script>

@endsection