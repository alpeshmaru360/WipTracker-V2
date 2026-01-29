@extends('layouts.main')
@section('content')
<link rel="stylesheet" href="{{ asset('css/quality_manager.css') }}" />

<section class="create_final_inspection_page bg-image mt-4">
    <div class="mask d-flex align-items-center h-100 gradient-custom-3">
        <div class="container-fluid h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-12 col-md-12 col-lg-12 col-xl-12">
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

                            <form action="{{route('QualityManagerFinalInspection')}}" enctype="multipart/form-data" method="post">
                                @csrf
                                <input type="hidden" name="id" value="{{$id}}">
                                <input type="hidden" name="artical_no" value="{{$artical_no}}">
                                <input type="hidden" name="description" value="{{$description}}">
                                <input type="hidden" name="qty" value="{{$quantity}}">
                                <input type="hidden" name="unit_qty" value="{{$unit_qty}}">

                                <input type="hidden" name="project_no" value="{{$project_no}}">
                                <input type="hidden" name="project_name" value="{{$project_name}}">

                                <div class="row mt-3">
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                        <label class="form-label" for="">Project No.<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                        <input type="text" id="project_no" class="w-100 pb-2 pt-2" value="{{$project_no}}" placeholder="Please Enter Project Number" onblur="fetchProjectName()" readonly />
                                    </div>
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                        <label class="form-label" for="">Project Name<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                        <input type="text" id="project_name" class="w-100 pb-2 pt-2" required value="{{$project_name}}"
                                            placeholder="Please Enter Project Name" readonly />
                                    </div>
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                    </div>
                                </div>

                                <div class="row mt-4 mx-4 project_table_section">
                                    <h2>FINAL INSPECTION</h2>
                                    <table class="table table-hover table-border w-100 text-center" id="quotation-items">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="project_table_heading p-1" style="width: 10%;">Article</th>
                                                <th scope="col" class="project_table_heading p-1" style="width: 4%;">Serial No.</th>
                                                <th scope="col" class="project_table_heading p-1" style="width: 18%;">Description</th>
                                                <th scope="col" class="project_table_heading p-1" style="width: 3%;">Qty</th>
                                                <th scope="col" class="project_table_heading p-1" style="width: 8%;">Unit Qty</th>
                                                <th scope="col" class="project_table_heading p-1" style="width: 16%;">Type</th>
                                                <th scope="col" class="project_table_heading p-1" style="width: 10%;">Images <span style="color:red">*</span></th>
                                                <th scope="col" class="project_table_heading p-1" style="width: 17%;">Final Inspection Report <span style="color:red">*</span></th>
                                                <th scope="col" class="project_table_heading p-1" style="width: 14%;">Upload Test Report</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{$artical_no}}</td>
                                                <td><input type="text" name="serial_no" value="" placeholder="Enter Serial No." required></td>
                                                <td>{{$description}}</td>
                                                <td>{{$quantity}}</td>
                                                <td>{{$unit_qty}} of {{$quantity}}</td>
                                                <td><select name="pump_type" id="pump_type" required>
                                                        <option value="">Select</option>
                                                        @foreach($inspectionNames as $inspection)
                                                        <option value="{{ $inspection->id }}">{{ $inspection->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <span class="project_check_status">
                                                        <button type="button" class="btn btn-primary primary_bg_color text-white py-1 px-2 upload-img-btn">
                                                            Upload
                                                        </button>
                                                        <input type="file" name="product_images[]" multiple class="d-none upload-img-input" data-lable="product_images" accept="image/*">
                                                        <br>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="project_check_status">
                                                        <button type="button" class="btn btn-primary primary_bg_color text-white py-1 px-2 upload-doc-btn">
                                                            Upload
                                                        </button>
                                                        <input type="file" name="reports_docs" class="d-none upload-doc-input" data-lable="reports_docs" accept=".doc,.docx">
                                                        <br>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="project_check_status">
                                                        <button type="button" class="btn btn-primary primary_bg_color text-white py-1 px-2 upload-test-doc-btn">
                                                            Upload
                                                        </button>
                                                        <input type="file" name="test_reports_docs" class="d-none upload-test-doc-input" data-lable="test_reports_docs" accept=".doc,.docx">
                                                        <br>
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-center mt-2">
                                    <button type="submit" class="btn btn-lg">Create</button>
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
    function fetchProjectName() {
        const projectNumber = $('#project_number').val();
        if (projectNumber) {
            fetch(`/api/get-project-name/${projectNumber}`)
                .then(response => response.json())
                .then(data => {
                    if (data.project_name) {
                        $('#project_name').val(data.project_name);
                    } else {
                        alert('No project number found in records. Please enter a valid project number.');
                        $('#project_number, #project_name').val(''); // Clear input fields
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while fetching project information. Please try again.');
                });
        }
    }

    $(document).on('click', '.upload-img-btn', function() {
        $(this).siblings('.upload-img-input').trigger('click');
    });
    $(document).on('change', '.upload-img-input', function() {
        let files = $(this)[0].files;
        let statusContainer = $(this).closest('.project_check_status');
        statusContainer.find('.file-upload-success').remove(); // Remove previous success messages
        let fileCount = files.length;
        if (fileCount > 0) {
            let message = `${fileCount} file${fileCount === 1 ? '' : 's'} selected`;
            statusContainer.append(`<span class="text-success bg-light ml-2 file-upload-success">${message}</span><br>`);
        }

        // let fileName = $(this).val().split("\\").pop(); // Get file name
        // let statusContainer = $(this).closest('.project_check_status');
        // statusContainer.find('.file-upload-success').remove();
        // if (fileName) {
        //     statusContainer.append(`<span class="text-success bg-gray-light ml-2 file-upload-success">File Uploaded: ${fileName}</span>`);
        // }
    });

    $(document).on('click', '.upload-doc-btn', function() {
        $(this).siblings('.upload-doc-input').trigger('click');
    });
    $(document).on('change', '.upload-doc-input', function() {
        let files = $(this)[0].files;
        let statusContainer = $(this).closest('.project_check_status');
        statusContainer.find('.file-upload-success').remove(); // Remove previous success messages
        let fileCount = files.length;
        if (fileCount > 0) {
            let message = `${fileCount} file${fileCount === 1 ? '' : 's'} selected`;
            statusContainer.append(`<span class="text-success bg-light ml-2 file-upload-success">${message}</span><br>`);
        }

        // let fileName = $(this).val().split("\\").pop(); // Get file name
        // let statusContainer = $(this).closest('.project_check_status');
        // statusContainer.find('.file-upload-success').remove();
        // if (fileName) {
        //     statusContainer.append(`<span class="text-success bg-gray-light ml-2 file-upload-success">File Uploaded: ${fileName}</span>`);
        // }
    });

    $(document).on('click', '.upload-test-doc-btn', function() {
        $(this).siblings('.upload-test-doc-input').trigger('click');
    });
    $(document).on('change', '.upload-test-doc-input', function() {
        let files = $(this)[0].files;
        let statusContainer = $(this).closest('.project_check_status');
        statusContainer.find('.file-upload-success').remove(); // Remove previous success messages
        let fileCount = files.length;
        if (fileCount > 0) {
            let message = `${fileCount} file${fileCount === 1 ? '' : 's'} selected`;
            statusContainer.append(`<span class="text-success bg-light ml-2 file-upload-success">${message}</span><br>`);
        }

        // let fileName = $(this).val().split("\\").pop(); // Get file name
        // let statusContainer = $(this).closest('.project_check_status');
        // statusContainer.find('.file-upload-success').remove();
        // if (fileName) {
        //     statusContainer.append(`<span class="text-success bg-gray-light ml-2 file-upload-success">File Uploaded: ${fileName}</span>`);
        // }
    });
</script>
@endsection