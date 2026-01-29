@extends('layouts.main')
@section('content')

<!-- Bootstrap JS & Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script> -->
<link rel="stylesheet" href="{{ asset('css/admin.css') }}" />

<section class="hours_settings_page main_section p-5 bg-white m-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <h1 class="flex-grow-1">Manage Product's Process</h1>

        <div class="btn-group mt-2 mr-2">
            <select class="process_dd process_status_dd form-control" name="process_type">
                <option value="All">All process</option>
                <option value="StandardProcessTimes">Standard Process</option>
                <option value="AssemblyProcessTime">Assembled Process</option>
            </select>
        </div>

        <div class="btn-group mt-2 mr-2" id="product_type_wrapper">
            <select class="select-control process_dd product_status_dd form-control" name="product_type">
                <option value="">Product Type</option>
                @foreach($product_types as $type)
                    <option value="{{ $type }}" title="{{ $type }}">{{ \Illuminate\Support\Str::limit($type, 25) }}</option>
                @endforeach
            </select>
        </div>

        <div class="btn-group mt-2">
            <button class="btn btn-primary add-button" data-toggle="modal" data-target="#addProcessModal">
                + Add Process
            </button>
        </div>
    </div>


    @if ($errors->any())
    <div class="mx-4 alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    @if (session('error'))
    <div class="mx-4 alert alert-danger">
        {{ session('error') }}
    </div>
    @endif
    @if (session('success'))
    <div class="mx-4 alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <div class="table-responsive">
        <table class="table table-hover table-border table-bordered w-100" id="process_table">
            <thead class="manager_table">
                <tr>
                    <th>Sr No</th>
                    <th>Process Type</th>
                    <th>Process Code</th>
                    <th>Product Type</th>
                    <th>Process Name</th>
                    <th>Hrs</th>
                    <th style="width:23%;">Actions</th>
                </tr>
            </thead>
            <tbody class="text-center" id="process_table_body">
                @include('admin.process_rows', ['processes' => $processes])                
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="addProcessModal" tabindex="-1" aria-labelledby="addProcessModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"> 
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title role-modal" id="addProcessModalLabel">Add New Process</h5>
                </div>
                <form method="POST" action="{{ route('admin.hrs.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="hrProcessType" class="form-label">Process Type<span class="text-danger">*</span></label>
                            <select class="form-control" id="hrProcessType" name="lable" required 
                            onchange="hide_product_type(this, false)">
                                <option value="">Select</option>
                                <option value="StandardProcessTimes">StandardProcessTimes</option>
                                <option value="AssemblyProcessTime">AssemblyProcessTime</option>
                            </select>
                        </div>
                        <div class="mb-3 product_type_field">
                            <label for="hrProductType" class="form-label">Product Type<span class="text-danger">*</span></label>
                            <select class="form-control" id="hrProductType" name="product_type" required>
                                <option value="">Select</option>
                                @foreach($product_types as $type)
                                    <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="hrProcessName" class="form-label">Process Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="hrProcessName" name="process_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="adminHrs" class="form-label">Hrs<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="adminHrs" name="value" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary cancle-button" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Process</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editProcessModal" tabindex="-1" aria-labelledby="editProcessModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title edit-modal-title" id="editProcessModalLabel">Edit Product's Process</h5>
                </div>
                <form id="editProcessForm" method="POST">
                    @csrf
                    @method('PUT') 
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editProcessType" class="form-label">Process Type<span class="text-danger">*</span></label>
                            <select class="form-control" id="editProcessType" name="lable" 
                            onchange="hide_product_type(this, true)" disabled>
                                <option value="">Select</option>
                                <option value="StandardProcessTimes">StandardProcessTimes</option>
                                <option value="AssemblyProcessTime">AssemblyProcessTime</option>
                            </select>
                        </div>
                        <div class="mb-3" id="product_code_field">
                            <label for="editProcessCode" class="form-label">Process Code<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editProcessCode" name="process_code" readonly>
                        </div>
                        <div class="mb-3 product_type_field">
                            <label for="editProductType" class="form-label">Product Type<span class="text-danger">*</span></label>
                            <select class="form-control" id="editProductType" name="product_type" disabled>
                                <option value="">Select</option>
                                @foreach($product_types as $type)
                                    <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editProcessName" class="form-label">Process Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editProcessName" name="process_name" required>
                        </div>
                        {{--
                        <div class="mb-3">
                            <label for="editKey" class="form-label">Key<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editKey" name="key" readonly>
                        </div>
                        --}}
                        <input type="hidden" class="form-control" id="editKey" name="key" readonly>
                        <div class="mb-3">
                            <label for="editHrs" class="form-label">Hrs<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editHrs" name="value">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary cancle-button" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Process</button>
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
                    Are you sure you want to delete the process: <strong id="process_key"></strong>?
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
<script>
    const ADMIN_HOURS_SETTINGS_URL = "{{ route('AdminHoursSettings') }}";
    const EDIT_PROCESS_UPDATE_URL = "{{ route('admin.hrs.update', ':id') }}";
    const DELETE_PROCESS_URL = "{{ route('admin.hrs.destroy', ':id') }}";
</script>
<script src="{{ asset('js/hours_settings.js') }}"></script>

@endsection





