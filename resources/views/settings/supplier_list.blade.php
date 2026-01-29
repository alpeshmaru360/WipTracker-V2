@extends('layouts.main')
@section('content')

<link rel="stylesheet" href="{{ asset('css/supplier.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" />

<div class="main_container d-flex">
    @include('layouts.setting')

    <div class="main_section py-5 bg-white my-4">
        <div class="container-fluid supplier_container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="setting_page_title py-2 text-left">Supplier List</h1>
                <button class="btn btn-success custom_bg_color add_member" data-bs-toggle="modal" data-bs-target="#addModal">+ Add Supplier</button>
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

            <table class="table table-bordered text-center supplier-table" id="supplier_table">
                <thead>
                    <tr>
                        <th>SR No.</th>
                        <th>Supplier Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php $sr_no = 0; @endphp
                    @foreach($suppliers->reverse() as $supplier)
                        @php $sr_no++; @endphp
                        <tr>
                            <td>{{ $sr_no }}</td>
                            <td>{{ $supplier->supplier_name }}</td>
                            <td class="action">
                                <button 
                                    class="btn btn-primary edit-btn"
                                    data-id="{{ $supplier->id }}"
                                    data-name="{{ $supplier->supplier_name }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal">
                                    Edit
                                </button>
                                <button 
                                    class="btn btn-danger delete-btn"
                                    data-id="{{ $supplier->id }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteModal">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Supplier Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Add Supplier</h5>
                </div>
                <div class="modal-body">
                    <form id="addForm" method="POST" action="{{ route('supplier.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="addSupplierName" class="form-label">Supplier Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="addSupplierName" name="supplier_name" required>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success add_supplier">Add Supplier</button>
                            <button type="button" class="btn btn-secondary cancle-button" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Supplier Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Supplier</h5>
                </div>
                <div class="modal-body">
                    <form id="editForm" method="POST" action="{{ route('supplier.update') }}">
                        @csrf
                        @method('PUT')

                        <input type="hidden" id="editSupplierId" name="id">

                        <div class="mb-3">
                            <label for="editSupplierName" class="form-label">Supplier Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editSupplierName" name="supplier_name" required>
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

    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete Supplier</h5>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this supplier?</p>
                    <form id="deleteForm" method="POST" action="{{ route('supplier.destroy') }}">
                        @csrf
                        @method('DELETE')

                        <input type="hidden" id="deleteSupplierId" name="id">

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-danger">Delete</button>
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


<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.edit-btn');

        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');

                // Fill modal form fields
                document.getElementById('editSupplierId').value = id;
                document.getElementById('editSupplierName').value = name;
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.delete-btn');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                document.getElementById('deleteSupplierId').value = id;
            });
        });
    });



</script>


<script src="https://cdn.datatables.net/responsive/3.0.3/js/dataTables.responsive.min.js"></script>

<script>
    $(document).ready(function() {
        
        // $('#supplier_table').DataTable({
        //     "order": [
        //         [0, 'desc']
        //     ]
        // });

        $('#supplier_table').DataTable();

        $('#supplier_table').removeClass('dataTable');
     });

</script>

@endsection