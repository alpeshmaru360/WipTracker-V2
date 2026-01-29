@extends('layouts.main')
@section('content')

<link rel="stylesheet" href="{{ asset('css/settings.css') }}" />
<div class="procurement_std_time_page main_container d-flex">

    @include('layouts.setting')

    <div class="main_section py-5 bg-white my-4">
        <div class="container-fluid settings_container">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="setting_page_title py-2 text-left">Procurement Standard Time</h1>
                <button class="btn btn-success custom_bg_color add_member" data-toggle="modal" data-target="#addModal">+ Add</button>
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

            <table class="table table-bordered text-center settings-table" id="procurement_std_time_table">
                <thead>
                    <tr>
                        <th>SR No.</th>
                        <th>Product Type</th>
                        <th>Keyword</th>
                        <th>Total Days</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php $sr_no = 0; @endphp
                    @foreach($settings as $setting)
                    @php $sr_no++; @endphp
                    <tr>
                        <td>{{ $sr_no }}</td>
                        <td>{{ $setting->product_type }}</td>
                        <td>{{ $setting->keyword }}</td>
                        <td>{{ $setting->total_days }}</td>
                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <button type="button" class="btn btn-primary edit-button"
                                    data-toggle="modal" data-target="#editModal"
                                    data-id="{{ $setting->id }}"
                                    data-product_type="{{ $setting->product_type }}"
                                    data-keyword="{{ $setting->keyword }}"
                                    data-total_days="{{ $setting->total_days }}">
                                    Edit
                                </button>

                                <form action="{{ route('procurement.destroy', $setting->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this record?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger del_btn">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title edit-modal-title" id="editModalLabel">Edit Procurement Standard Time</h5>
                </div>
                <form action="{{ route('procurement.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit_id">

                        <div class="form-group">
                            <label for="product_type">Product Type</label>
                            <input type="text" class="form-control" id="edit_product_type" name="product_type" readonly>
                        </div>

                        <div class="form-group">
                            <label for="keyword">Keyword</label>
                            <input type="text" class="form-control" id="edit_keyword" name="keyword" readonly>
                        </div>

                        <div class="form-group">
                            <label for="total_days">Total Days<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="edit_total_days" name="total_days" min="1" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <button type="button" class="btn btn-secondary canclebutton" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document"> <!-- smaller width -->
            <div class="modal-content">
                <div class="modal-header"> <!-- reduced padding -->
                    <h5 class="modal-title text-white" id="addModalLabel">Add Procurement Standard Time</h5>
                </div>
                <form action="{{ route('procurement.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-3"> <!-- reduced padding -->
                        <div class="form-group mb-2">
                            <label for="add_product_type" class="mb-1">Product Type<span class="text-danger">*</span></label>
                            <select class="form-control form-control" id="add_product_type" name="product_type" required>
                                <option value="">Select Product Type</option>
                                @foreach($productTypes as $id => $name)
                                <option value="{{ $id }}" {{ old('product_type') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>

                        </div>
                        <div class="form-group mb-2">
                            <label for="add_keyword" class="mb-1">Keyword<span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control" id="add_keyword" name="keyword" required>
                        </div>
                        <div class="form-group mb-2">
                            <label for="add_total_days" class="mb-1">Total Days<span class="text-danger">*</span></label>
                            <input type="number" class="form-control form-control" id="add_total_days" name="total_days" min="1" required>
                        </div>
                    </div>
                    <div class="modal-footer p-2">
                        <button type="submit" class="btn btn-success btn">Add</button>
                        <button type="button" class="btn btn-secondary cancle-button" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
@section('scripts')
<script>
    $(document).ready(function() {
        $('.edit-button').on('click', function() {
            var id = $(this).data('id');
            var product_type = $(this).data('product_type');
            var keyword = $(this).data('keyword');
            var total_days = $(this).data('total_days');

            $('#edit_id').val(id);
            $('#edit_product_type').val(product_type);
            $('#edit_keyword').val(keyword);
            $('#edit_total_days').val(total_days);
        });
    });
</script>

<script src="https://cdn.datatables.net/responsive/3.0.3/js/dataTables.responsive.min.js"></script>
<script>
    $(document).ready(function() {  
        $('#procurement_std_time_table').DataTable();
        $('#procurement_std_time_table').removeClass('dataTable');
    });
</script>
@endsection