@extends('layouts.main')
@section('content')
<link rel="stylesheet" href="{{ asset('css/quality_manager.css') }}" />

<section class="create_quality_page bg-image mt-4">
    <div class="mask d-flex align-items-center h-100 gradient-custom-3">
        <div class="container-fluid h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-12 col-xl-12">
                    <div class="card">
                        <div class="card-body p-4">

                            @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                            @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                            @endif
                            @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                            @endif

                            <form action="{{route('QualityManagerActionSave')}}" enctype="multipart/form-data"
                                method="post">
                                @csrf        

                                <div class="row mt-4 mx-4 project_table_section">
                                    <div class="col-6">
                                        <h2>Quality Action</h2>
                                        <table class="table table-hover table-border text-center">
                                            <thead>
                                                <tr>
                                                    <th scope="col" class="project_table_heading p-1" style="width: 30% !important;">PO No.</th>
                                                    <th scope="col" class="project_table_heading p-1" style="width: 30% !important;">Report Docs</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><input type="text" name="po_number" class="form-control" value="" placeholder="Enter PO Number"></td>
                                                    <td>
                                                        <span class="project_check_status">
                                                            <button type="button" class="btn btn-primary primary_bg_color text-white py-1 px-2 upload-doc-btn">
                                                                Upload
                                                            </button>
                                                            <br>
                                                            <input type="file" name="reports_docs" class="d-none upload-doc-input"
                                                                data-id="" data-lable="bom" accept=".doc,.docx">
                                                        </span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        <div class="d-flex justify-content-center mt-2">
                                            <button type="submit" class="btn btn-lg">Create</button>
                                        </div>
                                        
                                    </div>
                                </div>                              

                                
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>

    $(document).on('click', '.upload-doc-btn', function() {
        $(this).closest('.project_check_status').find('.upload-doc-input').trigger('click');
    });

    $(document).on('change', '.upload-doc-input', function() {
        let fileName = $(this).val().split("\\").pop(); // Get file name
        let statusContainer = $(this).closest('.project_check_status');

        // Remove any existing success message before adding a new one
        statusContainer.find('.file-upload-success').remove();

        // Append success message if a file is selected
        if (fileName) {
            statusContainer.append(`<span class="text-success bg-gray-light ml-2 file-upload-success">File Uploaded: ${fileName}</span>`);
        }
    });
</script>
@endsection