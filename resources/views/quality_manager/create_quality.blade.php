@extends('layouts.main')
@section('content')
<link rel="stylesheet" href="{{ asset('css/quality_manager.css') }}" />

<section class="create_quality_page bg-image mt-4">
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

                            <form action="{{route('QualityManagerInitialInspection')}}" enctype="multipart/form-data"
                                method="post">
                                @csrf
                                <input type="hidden" name="id" value="{{$id}}">
                                <input type="hidden" name="po_number" value="{{$po_number}}">
                                <input type="hidden" name="supplier" value="{{$supplier}}">
                                <input type="hidden" name="artical_no" value="{{$artical_no}}">
                                <input type="hidden" name="quantity" value="{{$quantity}}">
                                <input type="hidden" name="description" value="{{$description}}">                              

                                @foreach($purchaseOrderData as $data)
                                    @if($data->id == $id || (!empty($pot_ids) && in_array($data->id, (array) $pot_ids)))
                                        <div class="mb-2">
                                            <input type="hidden" name="artical_no_group[]" value="{{ $data->artical_no }}">
                                            <input type="hidden" name="quantity_group[]" value="{{ $data->quantity }}">
                                            <input type="hidden" name="description_group[]" value="{{ $data->description }}">
                                            {{--
                                            <input type="hidden" name="project_no_group[]" value="{{ $project_no }}">
                                            --}}
                                        </div>
                                    @endif
                                @endforeach

                                <div class="row mt-3">
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                        <label class="form-label" for="">Project No.<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                        <input type="text" id="project_number" class="w-100 pb-2 pt-2" name="project_number" value="{{$project_no}}" placeholder="Please Enter Project Number" onblur="fetchProjectName()" readonly />
                                    </div>
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                        <label class="form-label" for="">Project Name<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                        <input type="text" id="project_name" class="w-100 pb-2 pt-2" name="project_name" required value="{{$project_name}}" readonly
                                            placeholder="Please Enter Project Name" />
                                    </div>
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                    </div>
                                </div>
                                <div class="row mt-3 hidden">
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                        <label class="form-label" for="">Product Article no.<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                        <input type="text" id="product_article_no" class="w-100 pb-2 pt-2" name="product_article_no" required value="{{$product_article_no}}" readonly
                                            placeholder="Please Enter Product Article No." />
                                    </div>
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                    </div>
                                </div>
                                <div class="row mt-3 hidden">
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                        <label class="form-label" for="">Product Description<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                        <input type="text" id="product_desc" class="w-100 pb-2 pt-2" name="product_desc" required value="{{$product_desc}}" readonly
                                            placeholder="Please Enter Product Description" />
                                    </div>
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                        <label class="form-label" for="">Product Qty<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                        <input type="text" id="quantity" class="w-100 pb-2 pt-2" name="quantity" required value="{{$quantity}}" readonly
                                            placeholder="Please Enter Project Name" />
                                    </div>
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                    </div>
                                </div>

                                <div class="row mt-4 mx-4 project_table_section">
                                    <h2>INITIAL INSPECTION</h2>
                                    <table class="table table-hover table-border w-100 text-center" id="quotation-items">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="project_table_heading p-1" style="width: 40% !important;">Product Type</th>
                                                <th scope="col" class="project_table_heading p-1" style="width: 30% !important;">PO No.</th>
                                                <th scope="col" class="project_table_heading p-1" style="width: 30% !important;">Report Docs</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <select name="pump_type" id="pump_type" required>
                                                        <option value="">Select Type</option>
                                                        @foreach($inspectionNames as $inspection)
                                                        <option value="{{ $inspection->id }}">{{ $inspection->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>{{$po_number}}</td>
                                                <td>
                                                    <span class="project_check_status">
                                                        <button type="button" class="btn btn-primary primary_bg_color text-white py-1 px-2 upload-doc-btn">
                                                            Upload
                                                        </button>
                                                        <br>
                                                        <input type="file" name="reports_docs" class="d-none upload-doc-input"
                                                            data-id="{{$id}}" data-lable="bom" accept=".doc,.docx">
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="row mt-4 mx-4 project_table_section">
                                    <h2>ITEM DETAILS</h2>
                                    <table class="table table-hover table-border w-100 text-center" id="quotation-items">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="project_table_heading p-1" style="width: 10% !important;">Sr no</th>
                                                <th scope="col" class="project_table_heading p-1" style="width: 20% !important;">Article Number</th>
                                                <th scope="col" class="project_table_heading p-1" style="width: 30% !important;">Description</th>
                                                <th scope="col" class="project_table_heading p-1" style="width: 15% !important;">Quantity</th>
                                                <th scope="col" class="project_table_heading p-1" style="width: 15% !important;">Currency</th>
                                                <th scope="col" class="project_table_heading p-1" style="width: 15% !important;">Price in EUR</th>
                                                <th scope="col" class="project_table_heading p-1" style="width: 15% !important;">Shipment Qty Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                            $i = 1;
                                            @endphp
                                            @foreach($purchaseOrderData as $data)

                                            @php
                                                $isHighlighted = ($data->id == $id) || (in_array($data->id, (array) $pot_ids));
                                                $hightlight_color = $isHighlighted ? 'bg-gradient-green text-white' : '';
                                            @endphp 
                                            <tr class="{{ $hightlight_color }}">
                                                <td>{{$i}}</td>
                                                <td>{{$data->artical_no}}</td>
                                                <td>{{$data->description}}</td>
                                                <td>{{$data->quantity}}</td>
                                                <td>{{$data->currency}}</td>
                                                <td>{{$data->amount_eur}}</td>
                                                <td>
                                                    @if($data->pending_slot == 1)
                                                    Pending Slot
                                                    @elseif($data->is_parent == 0 && $data->pending_slot == 0)
                                                    Partial Shipment
                                                    @else
                                                    Full Shipment
                                                    @endif
                                                </td>
                                            </tr>
                                            @php $i++; @endphp
                                            @endforeach
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