@extends('layouts.main')
@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="{{ asset('css/production_manager.css') }}" />

<section class="edit_project_page bg-image mt-4">
    <div class="mask d-flex align-items-center h-100 gradient-custom-3">
        <div class="container h-100">
            <div class="row d-flex align-items-center h-100">
                <div class="col-12 col-md-12 col-lg-12 col-xl-12">
                    <div class="card">
                        <div class="card-body p-4">
                            @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                            @endif
                            <form action="{{route('ProductionManagerProjectUpdate')}}" enctype="multipart/form-data" method="post">
                                @csrf
                                <input type="hidden" name="id" value="{{$project->id}}">
                                <div class="row mt-3">
                                    <div class="col-4 col-xl-3">
                                        <label class="form-label" for="project_name">Project Name <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-xl-7">
                                        <input type="text" id="project_name" name="project_name" class="form-control" value="{{$project->project_name}}" required />
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-4 col-xl-3">
                                        <label class="form-label" for="assembly_quotation_ref">Assembly Quotation Ref. <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-xl-7">
                                        <input type="text" id="assembly_quotation_ref" name="assembly_quotation_ref" class="form-control" 
                                        value="{{$project->assembly_quotation_ref}}" required />
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-4 col-xl-3">
                                        <label class="form-label" for="sales_person">Sales Person <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-xl-7">
                                        <input type="text" id="sales_person" name="sales_person" class="form-control" value="{{$project->sales_name}}" required />
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-4 col-xl-3">
                                        <label class="form-label" for="customer_name">Customer Name <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-xl-7">
                                        <input type="text" id="customer_name" name="customer_name" class="form-control" value="{{$project->customer_name}}" required />
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-4 col-xl-3">
                                        <label class="form-label">Customer Documents <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-xl-7">
                                        <input type="file" name="customer_documents[]" class="form-control mb-2" 
                                        multiple accept=".pdf, .doc, .docx, .jpg, .jpeg, .png, .xlsx, .csv" @if(!$documents) required @endif />
                                    </div>

                                    <!-- Document List -->
                                    <div class="col-4 col-xl-3"></div>
                                    <div class="col-8 col-xl-7">
                                        <ul class="list-group">
                                            @if($documents)
                                            @foreach($documents as $document)
                                            @php
                                            $extension = pathinfo($document, PATHINFO_EXTENSION);
                                            @endphp
                                            <li class="list-group-item d-flex justify-content-between align-items-center px-4 py-2">
                                                <span>
                                                    @php
                                                    $iconClass = '';

                                                    if ($extension == "pdf") {
                                                    $iconClass = "fs-22 fa fa-file-pdf";
                                                    } elseif (in_array($extension, ['doc', 'docx'])) {
                                                    $iconClass = "fs-22 fa fa-file-word";
                                                    } elseif (in_array($extension, ['xlsx', 'csv'])) {
                                                    $iconClass = "fs-22 fa fa-file-excel";
                                                    } elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                                                    $iconClass = "fs-22 fa fa-image";
                                                    } else {
                                                    $iconClass = "fs-22 fa fa-file";
                                                    }
                                                    @endphp
                                                    <i class="file-icon {{ $extension }} {{$iconClass}} icon_color"></i>
                                                    <a href="{{ asset($document) }}" target="_blank">{{ basename($document) }}</a>
                                                </span>
                                                <button type="button" class="btn btn-danger btn-sm delete-btn px-3 py-1 my-1" onclick="removeDocument(this)">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                                <input type="checkbox" name="delete_documents[]" value="{{ $document }}" class="d-none">
                                            </li>
                                            @endforeach
                                            @endif
                                        </ul>
                                    </div>                                    
                                </div>

                                <div class="row mt-3">
                                    <div class="col-4 col-xl-3">
                                        <label class="form-label" for="country_name">Country <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-xl-7">
                                        <input type="text" id="country_name" name="country_name" class="form-control" value="{{$project->country}}" required />
                                    </div>
                                </div>
                                
                                <!-- A Code: 26-12-2025 Start -->
                                @if($project->isWITrack_project != 1)
                                <div class="row mt-3">
                                    <div class="col-4 col-xl-3">
                                        <label class="form-label">Sales Order Number <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-xl-7">
                                        <input type="text" name="sales_order_number" class="form-control" value="{{$project->sales_order_number}}" readonly />
                                    </div>
                                </div>    
                                <!-- A Code: 19-01-2026 Start -->
                                <input type="hidden" id="currency" name="currency" value="USD"> 
                                <!-- <div class="row mt-3">
                                    <div class="col-4 col-xl-3">
                                        <label class="form-label">Currency <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-8 col-xl-7">
                                        <input type="text" name="currency" class="form-control" value="{{ $project->currency }}" readonly>
                                    </div>                                    
                                </div> -->                                
                                <!-- A Code: 19-01-2026 End -->     
                                @endif
                                <!-- A Code: 26-12-2025 End -->

                                <div class="d-flex justify-content-center mt-2">
                                    <button type="submit" class="btn btn-lg">Update</button>
                                </div>

                                <div class="row mt-3 mx-4 project_table_section">
                                    <table class="table table-hover table-border w-100 text-center" id="quotation-items">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="project_table_heading p-1">SR NO.</th>
                                                <th scope="col" class="project_table_heading p-1">ARTICLE NUMBER</th>
                                                <th scope="col" class="project_table_heading p-1">DESCRIPTION</th>
                                                <th scope="col" class="project_table_heading p-1">QTY</th>
                                                <th scope="col" class="project_table_heading p-1">PRODUCT NAME</th>
                                                <th scope="col" class="project_table_heading p-1">PRODUCT TYPE</th>
                                                <th scope="col" class="project_table_heading p-1">BOM</th>
                                                <th scope="col" class="project_table_heading p-1">DRAWING</th>
                                                <th scope="col" class="project_table_heading p-1" style="width:17%;">PARTIAL DELIVERY</th>
                                                <th scope="col" class="project_table_heading p-1">UNIT PRICE</th>
                                                <th scope="col" class="project_table_heading p-1">TOTAL PRICE</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($project->productsOfProjects as $index => $product)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $product->article_number }}</td>
                                                <td>{{ $product->description }}</td>
                                                <td>{{ $product->qty }}</td>
                                                <td>{{ $product->cart_model_name }}</td>
                                                <td>{{ $product->product_type }}</td>
                                                <td>
                                                    @if($product->bom_req_estimation_manager == "2" || $product->bom_req_estimation_manager == "3")
                                                    <a href="{{ asset($product->bom_path) }}" class="btn btn-sm btn-primary" target="_blank">Download</a>
                                                    @elseif($product->bom_req_estimation_manager == "1")
                                                    BOM Upload Requested
                                                    @else
                                                    Not Requested
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($product->drawing_req_estimation_manager == "2")
                                                    <a href="{{ asset($product->drawing_path) }}" target="_blank">View</a>
                                                    @elseif($product->drawing_req_estimation_manager == "1")
                                                    Drawing Upload Requested
                                                    @else
                                                    No Drawing
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="hidden" name="products[{{ $product->project_id }}][{{ $product->article_number }}][article_number]" value="{{ $product->article_number }}">
                                                    <select name="products[{{ $product->project_id }}][{{ $product->article_number }}][delivery]" class="form-control">
                                                        <option value="1" {{ $product->delivery == 1 ? 'selected' : '' }}>Full Delivery</option>
                                                        <option value="2" {{ $product->delivery == 2 ? 'selected' : '' }}>Partial Delivery</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input readonly type="number" step="0.01" name="products[{{ $product->project_id }}][{{ $product->article_number }}][unit_price]" class="form-control" value="{{ $product->unit_price ?? '' }}" placeholder="Enter Unit Price">
                                                </td>
                                                <td>
                                                    <input readonly type="number" step="0.01" name="products[{{ $product->project_id }}][{{ $product->article_number }}][total_price]" class="form-control" value="{{ $product->total_price ?? '' }}" placeholder="Enter Total Price">
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>

                                    </table>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        $('.select2').select2();
        $(".select2-search__field").css('display', 'none');
        $(".select2-selection__choice__remove select2-selection__rendered").css('display', 'none');
        $(".select2 button").css('display', 'none');

    })

    $('.select2').on('click', function() {
        $(".select2-search__field").css('display', 'block');

    });
</script>

<script>
    function removeDocument(button) {
        const listItem = button.closest('li');
        const checkbox = listItem.querySelector('input[type="checkbox"]');
        checkbox.checked = true;
        listItem.style.setProperty('display', 'none', 'important');
    }
</script>
@endsection