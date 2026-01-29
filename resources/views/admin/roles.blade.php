@extends('layouts.main')
@section('content')

<!-- Bootstrap JS & Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script> -->
<script>
    window.LaravelRoutes = {
        update: "{{ route('roles.update', ':id') }}",
        delete: "{{ url('roles/delete') }}"
    };
</script>
<script src="{{ asset('js/admin_roles.js') }}"></script>
<link rel="stylesheet" href="{{ asset('css/admin.css') }}" />

<section class="main_section p-5 bg-white m-4">
    <div class="d-flex justify-content-between">
        <h1>Manage Roles</h1>        
        <button class="btn btn-primary mb-4 float-right add-button" data-toggle="modal" data-target="#exampleModal">+ Add Role</button>
    </div>

    @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
    @endif
    <div class="table-responsive">
        <table class="table table-hover table-border table-bordered w-100 text-center" id="roles_table">
            <thead class="manager_table">
                <tr>
                    <th>Sr No</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @php $sr_no = 0; @endphp
                @foreach($roles as $role)  
                    @php $sr_no++; @endphp
                    <tr>
                        <td>{{ $sr_no }}</td>
                        <td>{{ $role->rolename }}</td>  
                        <td>{{ $role->status }}</td>
                        <td>
                            <button class="btn edit-button" data-toggle="modal" data-target="#editModal"
                                data-id="{{ $role->id }}"
                                data-rolename="{{ $role->rolename }}"
                                data-status="{{ $role->status }}">Edit</button>
                            <button class="btn btn-danger" data-role-id="{{ $role->id }}" onclick="openDeleteModal('{{ $role->id }}', '{{ $role->rolename }}')">Delete</button>


                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"> 
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title role-modal" id="exampleModalLabel">Add New Role</h5>
                </div>
                <form method="POST" action="{{ route('roles.store') }}">
                    @csrf
                    <div class="modal-body">    
                        <div class="mb-3">
                            <label for="rolename" class="form-label">Role Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="rolename" name="rolename" required>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status<span class="text-danger">*</span></label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="Active">Active</option>
                                <option value="Deactive">Deactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary cancle-button" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Role</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title edit-modal-title" id="editModalLabel">Edit Role</h5>
                </div>
                <form id="editRoleForm" method="POST">
                    @csrf
                    @method('PUT') 
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editRoleName" class="form-label">Role Name</label>
                            <span class="text-danger">*</span>
                            <input type="text" class="form-control" id="editRoleName" name="rolename" required>
                        </div>
                        <div class="mb-3">
                            <label for="editStatus" class="form-label">Status</label>
                            <span class="text-danger">*</span>
                            <select class="form-control" id="editStatus" name="status" required>
                                <option value="Active">Active</option>
                                <option value="Deactive">Deactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary cancle-button" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Role</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title delete-modal-title" id="deleteModalLabel">Confirm Delete</h5>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete the role: <strong id="role-name"></strong>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary cancle-button" data-dismiss="modal">Close</button>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger delete">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="https://cdn.datatables.net/responsive/3.0.3/js/dataTables.responsive.min.js"></script>
@endsection





