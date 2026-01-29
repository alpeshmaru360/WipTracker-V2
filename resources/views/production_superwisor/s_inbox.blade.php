@extends('layouts.main')
@section('content')
<link href="{{ asset('css/production_manager.css') }}" rel="stylesheet" />
<link href="{{ asset('css/product_superwisor.css') }}" rel="stylesheet" />
<style type="text/css">
    .switch-group label {
        line-height: 10px !important;
    }

    .dataTables_wrapper {
        width: 100%;
    }

    .send-email-btn:disabled {
        background-color: #169e88 !important;
        color: #fff !important;
        opacity: 1 !important;
        border-color: #169e88 !important;
    }
</style>
<link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap-switch-button@1.1.0/css/bootstrap-switch-button.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap-switch-button@1.1.0/dist/bootstrap-switch-button.min.js"></script>

<div class="main_section bg-white m-4">
    <div class="d-flex justify-content-between align-items-center">
        <h3 class="ml-4 pt-4 text-bold text-left text-uppercase">Pending MRF</h3>
        <a class="mr-4 mt-4 mb-2 btn btn-primary btn-sm py-2 px-3" onclick="location.reload();">
            <i class="fa fa-refresh text-light"></i>
        </a>
    </div>
    <hr class="mx-4 mt-2 mb-4" />
    <div class="container">
        <div class="row mt-2">
            @if(count($pending_project) > 0)
            <table class="table table-hover table-border w-100 text-center" id="pending_project_table">
                <thead>
                    <tr>
                        <th scope="col" class="project_table_heading">Request Date</th>
                        <th scope="col" class="project_table_heading">Deadline</th>
                        <th scope="col" class="project_table_heading">Project No.</th>
                        <th scope="col" class="project_table_heading">Project Name</th>
                        <th scope="col" class="project_table_heading">Product Article No.</th>
                        <th scope="col" class="project_table_heading">Product Description</th>
                        <th scope="col" class="project_table_heading">Product QTY</th>
                        <th scope="col" class="project_table_heading">Action</th>
                        <th scope="col" class="project_table_heading">Upload</th>
                        <th scope="col" class="project_table_heading">Email</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pending_project as $val)
                    <?php
                    $orderDate = $val->ini_inspection_date ? \Carbon\Carbon::parse($val->ini_inspection_date) : $val->created_at;
                    $currentDateTime = \Carbon\Carbon::now('Asia/Kolkata'); // Set to IST
                    $deadlineClass = $val->deadline && $currentDateTime->gte($val->deadline) ? 'text-danger' : 'text-success';
                    ?>
                    <tr>
                        <td>
                            {{ $orderDate->format('d-m-y H:i') }}
                        </td>
                        <td class="{{ $deadlineClass }}">
                            {{ $val->deadline ? $val->deadline->format('d-m-y H:i') : 'N/A' }}
                        </td>
                        <td>{{ $val->projects['project_no'] }}</td>
                        <td>{{ $val->projects['project_name'] }}</td>
                        <td>{{ $val->full_article_number }}</td>
                        <td>{{ $val->description }}</td>
                        <td>{{ $val->qty }}</td>
                        <td>
                            <span class="project_check_status">
                                <a class="btn btn-primary primary_bg_color text-white p-0" href="{{ route('MRFExcelDownload', ['article_number' => $val->full_article_number, 'description' => $val->description, 'qty' => $val->qty]) }}">MRF To Warehouse</a>
                            </span>
                        </td>
                        <td>
                            <span class="project_check_status">
                                <button type="button" class="btn btn-primary primary_bg_color text-white py-1 px-2 upload-mrf-btn" data-product-id="{{ $val->id }}" data-project-id="{{ $val->project_id }}">
                                    Upload MRF
                                </button>
                                <br>
                                <input type="file" name="mrf_file" class="d-none upload-mrf-input" data-product-id="{{ $val->id }}" data-project-id="{{ $val->project_id }}" accept=".xlsx">
                            </span>
                        </td>
                        <td>
                            <span class="project_check_status">
                                <button type="button" class="btn btn-primary primary_bg_color text-white py-1 px-2 send-email-btn" data-product-id="{{ $val->id }}" data-project-id="{{ $val->project_id }}">
                                    Send Email
                                </button>
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="alert alert-info w-100">
                No Pending MRF found.
            </div>
            @endif
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center">
        <h3 class="ml-4 pt-4 text-bold text-left text-uppercase">Ready MRF</h3>
        <a class="mr-4 mt-4 mb-2 btn btn-primary btn-sm py-2 px-3" onclick="location.reload();">
            <i class="fa fa-refresh text-light"></i>
        </a>
    </div>

    <hr class="mx-4 mt-2 mb-4" />

    <div class="container">
        <div class="row mt-2">
            @if(count($ready_mrf) > 0)
            <table class="table table-hover table-border w-100 text-center" id="ready_mrf_table">
                <thead>
                    <tr>
                        <th scope="col" class="project_table_heading">Request Date</th>
                        <th scope="col" class="project_table_heading">Deadline</th>
                        <th scope="col" class="project_table_heading">Project No.</th>
                        <th scope="col" class="project_table_heading">Project Name</th>
                        <th scope="col" class="project_table_heading">Product Article No.</th>
                        <th scope="col" class="project_table_heading">Product Description</th>
                        <th scope="col" class="project_table_heading">Product QTY</th>
                        <th scope="col" class="project_table_heading">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ready_mrf as $val)
                    <?php
                    $orderDate = $val->ini_inspection_date ? \Carbon\Carbon::parse($val->ini_inspection_date) : $val->created_at;
                    $currentDateTime = \Carbon\Carbon::now('Asia/Kolkata'); // Set to IST
                    $deadlineClass = $val->deadline && $currentDateTime->gte($val->deadline) ? 'text-danger' : 'text-success';
                    ?>
                    <tr>
                        <td>
                            {{ $orderDate->format('d-m-y H:i') }}
                        </td>
                        <td class="{{ $deadlineClass }}">
                            {{ $val->deadline ? $val->deadline->format('d-m-y H:i') : 'N/A' }}
                        </td>
                        <td>{{ $val->projects['project_no'] }}</td>
                        <td>{{ $val->projects['project_name'] }}</td>
                        <td>{{ $val->full_article_number }}</td>
                        <td>{{ $val->description }}</td>
                        <td>{{ $val->qty }}</td>
                        <td>
                            <span class="project_check_status">
                                <a class="btn btn-primary primary_bg_color text-white p-0" href="{{ route('MRFExcelDownload', ['article_number' => $val->full_article_number, 'description' => $val->description, 'qty' => $val->qty]) }}">Download MRF</a>
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="alert alert-info w-100">
                No Ready MRF found.
            </div>
            @endif
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center">
        <h3 class="ml-4 pt-4 text-bold text-left text-uppercase">Orders</h3>
        <a class="mr-4 mt-4 mb-2 btn btn-primary btn-sm py-2 px-3" onclick="location.reload();">
            <i class="fa fa-refresh text-light">&#xf021;</i>
        </a>
    </div>

    <hr class="mx-4 mt-2 mb-4" />

    <div class="container">
        <div class="row mt-2">
            {{--
            @foreach($all_projects_full as $val)
            <div class="col-xl-2 mb-4">
                <div class="pb-4 product_superwisor_dashboard">
                    @if($val->superwisor_status == "0")
                    <div class="dropdown text-right">
                        <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton{{ $val->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">&#x2022;&#x2022;&#x2022;</button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $val->id }}">
                @foreach($operators as $operator)
                <a class="dropdown-item" href="#" onclick="assignTask( '{{ $val->id }}','{{$val->project_id}}','{{ $val->projects['project_name'] }}', '{{ $operator->name }}', '{{$operator->id}}' )">
                    Assign to {{ $operator->name }}
                </a>
                @endforeach
            </div>
        </div>
        @endif
        <div class="role_lable">
            <span class=" fs-15 fw-600 text-uppercase">Date : {{ $val->created_at->format('d-m-y') }}</span><br>
            <span class=" fs-15">{{ $val->projects['project_no'] }} - {{ $val->projects['project_name'] }} </span><br>
            <span class=" fs-12 text-green bg-gray">{{ $val->description }} </span>
        </div>

        @if($val->superwisor_status == "1")
        <div class="text-center mt-2">
            <span class="project_check_status">
                <a class="btn btn-primary primary_bg_color text-white p-1" href="{{route('MRFExcelDownload')}}" title="{{$val->operator->name}}">Assigned</a>
            </span>
        </div>
        @endif
    </div>
</div>

@endforeach

--}}

@if(count($all_projects_full) > 0)
<table class="table table-hover table-border w-100 text-center" id="project_full_orders_table">
    <thead>
        <tr>
            <!-- <th scope="col" class="project_table_heading">Estimated Date</th> -->
            <th scope="col" class="project_table_heading">Project No.</th>
            <th scope="col" class="project_table_heading">Project Name</th>
            <th scope="col" class="project_table_heading">Article No.</th>
            <th scope="col" class="project_table_heading">Product Description</th>
            <th scope="col" class="project_table_heading">QTY</th>
            <th scope="col" class="project_table_heading">Assigned QTY</th>
            <th scope="col" class="project_table_heading">Assign QTY</th>
            <th scope="col" class="project_table_heading">View operator</th>
        </tr>
    </thead>
    <tbody>
        @foreach($all_projects_full as $val)
        <tr>
            {{--<td>
                {{ $val->projects['estimated_readiness'] ? \Carbon\Carbon::parse($val->projects['estimated_readiness'])->format('d-m-y') : 'N/A' }}
            </td>--}}
            <td>{{$val->projects['project_no']}}</td>
            <td>{{$val->projects['project_name']}}</td>
            <td>{{$val->full_article_number}}</td>
            <td>{{ $val->description}}</td>
            <td>{{$val->qty}}</td>
            <td>{{ $val->assigned_qty}}</td>
            <td>
                @if($val->superwisor_status == "0")
                <div class="dropdown">
                    <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton{{ $val->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Select <!--  &#x2022;&#x2022;&#x2022; Three dots -->
                    </button>

                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $val->id }}">
                        @foreach($operators as $operator)
                        <a class="dropdown-item" href="#" onclick="
                                                        assignTask( '{{ $val->id }}','{{$val->project_id}}','{{ $val->projects['project_name'] }}', '{{ $operator->name }}', '{{$operator->id}}','{{ $val->qty }}','{{ $val->assigned_qty }}' )">
                            Assign to {{ $operator->name }}
                        </a>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="text-center mt-2">
                    <span class="project_check_status">
                        <a class="btn btn-primary primary_bg_color text-white p-1" title="{{$val->operator->name}}">Assigned</a>
                    </span>
                </div>
                @endif
            </td>
            <td>
                <button type="button" class="project_check_status primary_bg_color text-white p-2 show_operator_list" data-toggle="modal" data-target="#operatorShowModal" data-project_id="{{$val->project_id}}" data-product_id="{{$val->id}}">
                    <i class="fa fa-eye primary_bg_color"></i>
                </button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<div class="alert alert-info w-100">
    No Orders found.
</div>
@endif
</div>
</div>

<div class="d-flex justify-content-between align-items-center">
    <h3 class="ml-4 pt-4 text-bold text-left text-uppercase">Partial Orders</h3>
    <a class="mr-4 mt-4 mb-2 btn btn-primary btn-sm py-2 px-3" onclick="location.reload();">
        <i class="fa fa-refresh text-light">&#xf021;</i>
    </a>
</div>

<hr class="mx-4 mt-2 mb-4" />

<div class="container">
    <div class="row mt-2">
        {{--
            @foreach($all_projects_partials as $val)
            <div class="col-xl-2 mb-4">
                <div class="pb-4 product_superwisor_dashboard">
                    @if($val->superwisor_status == "0")
                    <div class="dropdown text-right">
                        <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton{{ $val->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> &#x2022;&#x2022;&#x2022; <!-- Three dots -->
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $val->id }}">
            @foreach($operators as $operator)
            <a class="dropdown-item" href="#" onclick="assignTask( '{{ $val->id }}','{{$val->project_id}}','{{ $val->projects['project_name'] }}', '{{ $operator->name }}', '{{$operator->id}}' )">
                Assign to {{ $operator->name }}
            </a>
            @endforeach
        </div>
    </div>
    @endif
    <div class="role_lable">
        <span class=" fs-15 fw-600 text-uppercase">Date : {{ $val->created_at->format('d-m-y') }}</span><br>
        <span class=" fs-15">{{ $val->projects['project_no'] }} - {{ $val->projects['project_name'] }} </span><br>
        <span class=" fs-12 text-green bg-gray">{{ $val->description }} </span>
    </div>

    @if($val->superwisor_status == "1")
    <div class="text-center mt-2">
        <span class="project_check_status">
            <a class="btn btn-primary primary_bg_color text-white p-1" href="{{route('MRFExcelDownload')}}">Assigned</a>
        </span>
    </div>
    @endif
</div>
</div>
@endforeach
--}}
@if(count($all_projects_partials) > 0)

<table class="table table-hover table-border w-100 text-center" id="project_partial_orders_table">
    <thead>
        <tr>
            <th scope="col" class="project_table_heading">Project No.</th>
            <th scope="col" class="project_table_heading">Project Name</th>
            <th scope="col" class="project_table_heading">Article No.</th>
            <th scope="col" class="project_table_heading">Product Description</th>
            <th scope="col" class="project_table_heading">QTY</th>
            <th scope="col" class="project_table_heading">Assigned QTY</th>
            <th scope="col" class="project_table_heading">Assign QTY</th>
            <th scope="col" class="project_table_heading">View operator</th>
        </tr>
    </thead>
    <tbody>
        @foreach($all_projects_partials as $val)
        <tr>
            {{-- <td>
                {{ $val->projects['estimated_readiness'] ? \Carbon\Carbon::parse($val->projects['estimated_readiness'])->format('d-m-y') : 'N/A' }}
            </td> --}}
            <td>{{$val->projects['project_no']}}</td>
            <td>{{$val->projects['project_name']}}</td>
            <td>{{$val->full_article_number}}</td>
            <td>{{ $val->description}}</td>
            <td>{{$val->qty}}</td>
            <td>{{ $val->assigned_qty}}</td>
            <td>
                @if($val->superwisor_status == "0")
                <div class="dropdown text-right">
                    <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton{{ $val->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Select <!-- &#x2022;&#x2022;&#x2022; Three dots -->
                    </button>

                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $val->id }}">
                        @foreach($operators as $operator)
                        <a class="dropdown-item" href="#" onclick="
                                                        assignTask( '{{ $val->id }}','{{$val->project_id}}','{{ $val->projects['project_name'] }}', '{{ $operator->name }}', '{{$operator->id}}','{{ $val->qty }}','{{ $val->assigned_qty }}' )">
                            Assign to {{ $operator->name }}
                        </a>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="text-center mt-2">
                    <span class="project_check_status">
                        <a class="btn btn-primary primary_bg_color text-white p-1" title="{{$val->operator->name}}">Assigned</a>
                    </span>
                </div>
                @endif
            </td>
            <td>
                <button type="button" class="show_operator_list project_check_status primary_bg_color text-white p-2" data-toggle="modal" data-target="#operatorShowModal" data-project_id="{{$val->project_id}}" data-product_id="{{$val->id}}">
                    <i class="fa fa-eye primary_bg_color"></i>
                </button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<div class="alert alert-info w-100">
    No Partial Orders found.
</div>
@endif
</div>
</div>

<div class="d-flex justify-content-between align-items-center">
    <h3 class="ml-4 pt-4 text-bold text-left text-uppercase">Pending AS Built PDF Approvals</h3>
    <a class="mr-4 mt-4 mb-2 btn btn-primary btn-sm py-2 px-3" onclick="location.reload();">
        <i class="fa fa-refresh text-light">&#xf021;</i>
    </a>
</div>

<hr class="mx-4 mt-2 mb-4" />

<div class="container">
    <div class="row mt-2">
        @if($pdf_req->isNotEmpty())
        <table class="table table-hover table-border w-100 text-center" id="as_built_pdf">
            <thead>
                <tr>
                    <th scope="col" class="project_table_heading">Project No.</th>
                    <th scope="col" class="project_table_heading">Project Name</th>
                    <th scope="col" class="project_table_heading">Article No.</th>
                    <th scope="col" class="project_table_heading">Product Description</th>
                    <th scope="col" class="project_table_heading">Assigned Qty</th>
                    <th scope="col" class="project_table_heading">Edited PDF</th>
                    <th scope="col" class="project_table_heading" style="width: 15%;">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pdf_req as $val)
                <tr>
                    <td>{{ $val->projects->project_no ?? 'N/A' }}</td>
                    <td>{{ $val->projects->project_name ?? 'N/A' }}</td>
                    <td>{{ $val->article_number ?? 'N/A' }}</td>
                    <td>{{ $val->description?? 'N/A' }}</td>
                    <td>{{ $val->qty ?? 0 }}</td>
                    <td>
                        @if(!empty($val->editable_drawing_path))
                        <a href="{{ asset($val->editable_drawing_path) }}"
                            class="btn btn-sm"
                            style="background-color: #169e88; color: #fff; padding: 5px 10px; border-radius: 5px;"
                            download
                            title="Download Edited PDF">
                            <i class="fa fa-download"></i>
                        </a>
                        @else
                        <span class="text-muted">No PDF</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-success btn-sm m-0 px-3 py-1" data-bs-toggle="modal" data-bs-target="#approveModal" data-id="{{ $val->id }}">
                            <i class="fa fa-check"></i>
                        </button>
                        <button class="btn btn-danger btn-sm m-0 px-3 py-1" data-bs-toggle="modal" data-bs-target="#rejectModal" data-id="{{ $val->id }}">
                            <i class="fa fa-times"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="alert alert-info w-100">
            No Pending AS Built PDF Approvals Found.
        </div>
        @endif
    </div>
</div>

<div class="d-flex justify-content-between align-items-center">
    <h3 class="ml-4 pt-4 text-bold text-left text-uppercase">Completed process of assembly</h3>
    <a class="mr-4 mt-4 mb-2 btn btn-primary btn-sm py-2 px-3" onclick="location.reload();">
        <i class="fa fa-refresh text-light">&#xf021;</i>
    </a>
</div>

<hr class="mx-4 mt-2 mb-4" />

<div class="container">
    <div class="row mt-2">
        {{--
            @if(count($completed_process_of_assembly) > 0)
            
            <table class="table table-hover table-border w-100 text-center" id="completed_process">
                <thead>
                    <tr>
                        <th scope="col" class="project_table_heading">Project No.</th>
                        <th scope="col" class="project_table_heading">Project Name</th>
                        <th scope="col" class="project_table_heading">Product Article No.</th>
                        <th scope="col" class="project_table_heading">Product Description</th>
                        <th scope="col" class="project_table_heading">QTY</th>
                        <th scope="col" class="project_table_heading" style="width:15%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($completed_process_of_assembly as $val)
                    <tr>
                        <td>{{$val->projects['project_no']}}</td>
        <td>{{$val->projects['project_name']}}</td>
        <td>{{$val->full_article_number}}</td>
        <td>{{ $val->description}}</td>
        <td>{{$val->qty}}</td>
        <td>--}}
            {{--
                            <input type="checkbox" class="process-confirm" data-id="{{ $val->id }}" data-toggle="switchbutton" data-onstyle="primary" data-width="100" data-height="20" data-onlabel="Checked" data-offlabel="Pending" data-onstyle="success" data-offstyle="danger">{{route('assembly.confirm',$val->id)}}
            --}}
            {{--<button type="button" class="btn btn-primary process-confirm" data-id="{{ $val->id }}">Confirm</button>

        </td>
        </tr>
        @endforeach
        </tbody>
        </table>
        @else
        <div class="alert alert-info w-100">
            No Pending Task found.
        </div>
        @endif
        --}}

        @if(count($completed_process_of_assembly_products_qty_wise) > 0)

        <table class="table table-hover table-border w-100 text-center" id="completed_process">
            <thead>
                <tr>
                    <th scope="col" class="project_table_heading">Project No.</th>
                    <th scope="col" class="project_table_heading">Project Name</th>
                    <th scope="col" class="project_table_heading">Article No.</th>
                    <th scope="col" class="project_table_heading">Product Description</th>
                    <th scope="col" class="project_table_heading">Total QTY</th>
                    <th scope="col" class="project_table_heading">Unit No</th>
                    <th scope="col" class="project_table_heading" style="width:15%;">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($completed_process_of_assembly_products_qty_wise as $val)
                <tr>
                    <td>{{$val->projects['project_no']}}</td>
                    <td>{{$val->projects['project_name']}}</td>
                    <td>{{$val['products']->full_article_number}}</td>
                    <td>{{ $val['products']->description}}</td>
                    <td>{{$val['products']->qty}} </td>
                    <td>{{$val->qty_number}} of {{$val['products']->qty}}</td>
                    <td>
                        {{--
                            <input type="checkbox" class="process-confirm" data-id="{{ $val->id }}" data-toggle="switchbutton" data-onstyle="primary" data-width="100" data-height="20" data-onlabel="Checked" data-offlabel="Pending" data-onstyle="success" data-offstyle="danger">{{route('assembly.confirm',$val->id)}}
                        --}}
                        <button type="button" class="btn btn-primary process-confirm" data-id="{{ $val->id }}" title="Click here to start NamePlate flow">Confirm</button>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="alert alert-info w-100">
            No Pending Task found.
        </div>
        @endif
    </div>
</div>

<div class="d-flex justify-content-between align-items-center">
    <h3 class="ml-4 pt-4 text-bold text-left text-uppercase">Upload PL For Full Order</h3>
    <a class="mr-4 mt-4 mb-2 btn btn-primary btn-sm py-2 px-3" onclick="location.reload();">
        <i class="fa fa-refresh text-light">&#xf021;</i>
    </a>
</div>

<hr class="mx-4 mt-2 mb-4" />

<div class="container">
    <div class="row mt-2">
        @if(count($upload_pl_req) > 0)

        <table class="table table-hover table-border w-100 text-center" id="upload_pl_table">
            <thead>
                <tr>
                    <th scope="col" class="project_table_heading">Project No.</th>
                    <th scope="col" class="project_table_heading">Project Name</th>
                    <th scope="col" class="project_table_heading">View Products [Full Order]</th>
                    <th scope="col" class="project_table_heading">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($upload_pl_req as $val)
                @php
                $qty_records = DB::table('qty_of_products')
                ->select('qty_of_products.id as qty_id','qty_of_products.*','products_of_projects.*')
                ->where('qty_of_products.project_id', $val->project_id)
                ->join('products_of_projects', function ($join) {

                $join->on('products_of_projects.project_id', '=', 'qty_of_products.project_id')
                ->on('products_of_projects.id', '=', 'qty_of_products.product_id');

                })->where('products_of_projects.delivery', 1)->get();

                $upload_btn_enable = false;

                foreach($qty_records as $qty) {
                if($qty->is_final_inspection_started != 2) {
                $upload_btn_enable = true;
                break;
                }
                }
                @endphp
                <tr>
                    <td>{{$val->projects['project_no']}}</td>
                    <td>{{$val->projects['project_name']}}</td>
                    <td>
                        <button type="button" class="project_check_status primary_bg_color text-white p-2 view_products" data-toggle="modal" data-target="#viewProductsModal" data-project_id="{{$val->project_id}}">
                            <i class="fa fa-eye primary_bg_color"></i>
                        </button>
                    </td>
                    <td>
                        {{--
                            <span class="project_check_status">
                                @if($upload_btn_enable == true)
                                <button type="button" class="btn btn-primary primary_bg_color text-white py-1 px-2 upload-pl-btn">
                                    Upload PL
                                </button>
                                @else
                                <button type="button" class="btn btn-primary primary_bg_color text-white py-1 px-2 upload-pl-btn" disabled>
                                    Upload PL
                                </button>
                                @endif
                                <br>
                                <input type="file" name="reports_docs" 
                                class="d-none upload-pl-input" data-id="{{$val->id}}" data-lable="pl" accept=".pdf, .xls, .xlsx">
                            </span>
                        --}}
                        <span class="project_check_status bg-light">
                            @if($upload_btn_enable != true)
                            <button type="button" class="btn btn-primary primary_bg_color text-white py-1 px-2 upload-pl-btn">
                                Upload PL
                            </button>
                            @else
                            <button type="button" class="btn btn-primary primary_bg_color text-white py-1 px-2 upload-pl-btn" disabled>
                                Upload PL
                            </button>
                            @endif
                            <br>
                            <input type="file" name="reports_docs"
                                class="d-none upload-pl-input" data-id="{{$val->projects['id']}}"
                                data-product_id="{{$val->id}}"
                                data-lable="pl" accept=".pdf, .xls, .xlsx">
                        </span>

                        <div id="file-upload-success"></div>

                    </td>
                </tr>
                @endforeach

            </tbody>
        </table>
        @else
        <div class="alert alert-info w-100">
            No Pending Task found.
        </div>
        @endif
    </div>
</div>
<div class="d-flex justify-content-between align-items-center">
    <h3 class="ml-4 pt-4 text-bold text-left text-uppercase">Upload PL For Partial Order</h3>
    <a class="mr-4 mt-4 mb-2 btn btn-primary btn-sm py-2 px-3" onclick="location.reload();">
        <i class="fa fa-refresh text-light"></i>
    </a>
</div>
<hr class="mx-4 mt-2 mb-4" />
<div class="container">
    <div class="row mt-2">
        @if(count($upload_pl_partial_dilivery_req) > 0)
        <table class="table table-hover table-border w-100 text-center" id="upload_pl_partial_dilivery_table">
            <thead>
                <tr>
                    <th scope="col" class="project_table_heading">Project No.</th>
                    <th scope="col" class="project_table_heading">Project Name</th>
                    <th scope="col" class="project_table_heading">Product Article No</th>
                    <th scope="col" class="project_table_heading">Product Description</th>
                    <th scope="col" class="project_table_heading">Unit (Qty)</th>
                    <th scope="col" class="project_table_heading">Final Inspection Status</th>
                    <th scope="col" class="project_table_heading">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($upload_pl_partial_dilivery_req as $val)
                <tr data-id="{{ $val->id }}">
                    <td>{{ $val->project_no }}</td>
                    <td>{{ $val->project_name }}</td>
                    <td>{{ $val->full_article_number ?? 'N/A' }}</td>
                    <td>{{ $val->description ?? '' }}</td>
                    <td>
                        {{ $val->qty > 0 ? $val->qty_number . '/' . $val->qty : 'N/A' }}
                    </td>
                    <td>
                        @php
                        $statusValue = $val->is_final_inspection_started ?? 0;
                        $statusLabel = '';
                        $badgeClass = '';
                        switch ($statusValue) {
                        case 1:
                        $statusLabel = 'In Process';
                        $badgeClass = 'badge bg-warning';
                        break;
                        case 2:
                        $statusLabel = 'Completed';
                        $badgeClass = 'badge bg-success';
                        break;
                        default:
                        $statusLabel = 'Not Started';
                        $badgeClass = 'badge bg-danger';
                        }
                        @endphp
                        <span class="{{ $badgeClass }} p-2">{{ $statusLabel }}</span>
                    </td>
                    <td>
                        <span class="project_check_status">
                            <button type="button" class="btn btn-primary primary_bg_color text-white py-1 px-2 upload-partial-pl-btn">
                                Upload PL
                            </button>
                            <br>
                            <input type="file" name="reports_docs"
                                class="d-none upload-pl-input" data-id="{{ $val->id }}"
                                data-lable="pl" accept=".pdf, .xls, .xlsx">
                        </span>
                        <div id="file-upload-success-{{ $val->id }}" class="file-upload-success"></div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="alert alert-info w-100">
            No Pending Task found.
        </div>
        @endif
    </div>
</div>

<!-- <div class="d-flex justify-content-between align-items-center">
    <h3 class="ml-4 pt-4 text-bold text-left text-uppercase">Completed Projects</h3>
    <a class="mr-4 mt-4 mb-2 btn btn-primary btn-sm py-2 px-3" onclick="location.reload();">
        <i class="fa fa-refresh text-light">&#xf021;</i>
    </a>
</div>
<hr class="mx-4 mt-2 mb-4" />
<div class="container">
    <div class="row mt-2">
        @if(count($completed_projects) > 0)
        <table class="table table-hover table-border w-100 text-center" id="completed_projects">
            <thead>
                <tr>
                    <th scope="col" class="project_table_heading">Project No.</th>
                    <th scope="col" class="project_table_heading">Project Name</th>
                </tr>
            </thead>
            <tbody>
                @foreach($completed_projects as $val)
                <tr>
                    <td>{{$val->project_no}}</td>
                    <td>{{$val->project_name}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="alert alert-info w-100">
            No Pending Task found.
        </div>
        @endif
    </div>
</div> -->

<!-- Operator Show Modal -->
<!-- <div class="modal fade" id="operatorShowModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Quantity Assigned List</h5>           
            </div>
            <div class="modal-body">
                <ul id="projectDetailsList"></ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div> -->
<!-- Operator Show Modal -->
<div class="modal fade" id="operatorShowModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Quantity Assigned List</h5>
            </div>

            <div class="modal-body p-0">
                <table class="table table-hover table-border text-center mb-0" id="projectDetailsTable">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="project_table_heading">Qty</th>
                            <th scope="col" class="project_table_heading">Operator</th>
                            <th scope="col" class="project_table_heading">E-mail</th>
                        </tr>
                    </thead>
                    <tbody><!-- rows inserted here --></tbody>
                </table>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modals remain unchanged -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white" id="rejectModalLabel">Reject Request</h5>
            </div>
            <div class="modal-body">
                <textarea class="form-control" rows="3" id="rejectReason" placeholder="Enter reason for rejection..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="cancel-black" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmReject">Reject</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white" id="approveModalLabel">Approve Request</h5>
            </div>
            <div class="modal-body">
                <textarea class="form-control" rows="3" id="approveRemarks" placeholder="Enter remarks (optional)..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="cancel-black" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmApprove">Approve</button>
            </div>
        </div>
    </div>
</div>

<!-- View Products Modal -->
<div class="modal fade stockModal" id="viewProductsModal" tabindex="-1" aria-labelledby="view_productsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white edit-model-header">
                <h5 class="modal-title" id="view_productsModalLabel">Project Detail</h5>
            </div>
            <div class="modal-body">

                <table class="table table-bordered" id="viewProductsTable" border="1">
                    <thead>
                        <tr>
                            <th style="width:15%;">Product Article No</th>
                            <th>Product Description</th>
                            <th style="width:15%;">Unit (Qty)</th>
                            <th style="width:15%;">Final Inspection Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade stockModal" id="viewProductsModalpartial" tabindex="-1" aria-labelledby="view_productsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white edit-model-header">
                <h5 class="modal-title" id="view_productsModalLabel">Project Detail</h5>
            </div>
            <div class="modal-body">

                <table class="table table-bordered" id="viewProductsTable" border="1">
                    <thead>
                        <tr>
                            <th style="width:15%;">Product Article No</th>
                            <th>Product Description</th>
                            <th style="width:15%;">Unit (Qty)</th>
                            <th style="width:15%;">Final Inspection Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {

        $('#pending_project_table').DataTable({
            paging: true,
            pageLength: 2,
            lengthMenu: [2, 5, 10, 25, 50, 100],
            searching: true,
            ordering: false,
            info: false
        });

        $('#project_full_orders_table').DataTable({
            paging: true,
            pageLength: 2,
            lengthMenu: [2, 5, 10, 25, 50, 100],
            searching: true,
            ordering: false,
            info: false
        });

        $('#project_partial_orders_table').DataTable({
            paging: true,
            pageLength: 2,
            lengthMenu: [2, 5, 10, 25, 50, 100],
            searching: true,
            ordering: false,
            info: false
        });

        $('#as_built_pdf').DataTable({
            paging: true,
            pageLength: 2,
            lengthMenu: [2, 5, 10, 25, 50, 100],
            searching: true,
            ordering: false,
            info: false
        });

        $('#completed_process').DataTable({
            paging: true,
            pageLength: 2,
            lengthMenu: [2, 5, 10, 25, 50, 100],
            searching: true,
            ordering: false,
            info: false
        });

        $('#upload_pl_table').DataTable({
            paging: true,
            pageLength: 2,
            lengthMenu: [2, 5, 10, 25, 50, 100],
            searching: true,
            ordering: false,
            info: false
        });

        $('#upload_pl_partial_dilivery_table').DataTable({
            paging: true,
            pageLength: 2,
            lengthMenu: [2, 5, 10, 25, 50, 100],
            searching: true,
            ordering: false,
            info: false
        });

        $('#completed_projects').DataTable({
            paging: true,
            pageLength: 2,
            lengthMenu: [2, 5, 10, 25, 50, 100],
            searching: true,
            ordering: false,
            info: false
        });
        $('#ready_mrf_table').DataTable({
            paging: true,
            pageLength: 2,
            lengthMenu: [2, 5, 10, 25, 50, 100],
            searching: true,
            ordering: false,
            info: false
        });

        console.log('Looking for .upload-mrf-btn elements:', $('.upload-mrf-btn').length);
        $(document).on('click', '.upload-mrf-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Upload MRF button clicked', $(this).data());
            const $input = $(this).closest('.project_check_status').find('.upload-mrf-input');
            if ($input.length) {
                console.log('Triggering click on file input');
                $input.trigger('click');
            } else {
                console.log('File input not found for this button');
            }
        });

        $(document).on('click', '.send-email-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const productId = $(this).data('product-id');
            const projectId = $(this).data('project-id');
            const csrfToken = "{{ csrf_token() }}";
            const $button = $(this);
            const originalText = $button.text();
            if (confirm('Are you sure you want to send the MRF email to Warehouse Coordinators?')) {
                $button.text('Sending...').prop('disabled', true).addClass('py-1 px-2');
                $.ajax({
                    url: "{{ route('send.mrf.email') }}",
                    type: 'POST',
                    data: {
                        _token: csrfToken,
                        product_id: productId,
                        project_id: projectId
                    },
                    success: function(response) {
                        console.log('AJAX success', response);
                        if (response.success) {
                            alert(response.message);
                            setTimeout(() => location.reload(), 2000);
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        console.log('AJAX error', xhr);
                        let errorMessage = 'Error sending email.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.statusText) {
                            errorMessage += ' (Status: ' + xhr.statusText + ')';
                        }
                        alert(errorMessage);
                    },
                    complete: function() {
                        $button.text(originalText).prop('disabled', false).removeClass('py-1 px-2');
                    }
                });
            }
        });

        $(document).on('change', '.upload-mrf-input', function(e) {
            console.log('File input changed', e.target.files);
            const file = e.target.files[0];
            const productId = $(this).data('product-id');
            const projectId = $(this).data('project-id');
            const csrfToken = "{{ csrf_token() }}";
            const $button = $(this).closest('.project_check_status').find('.upload-mrf-btn');
            const originalText = $button.text(); // Store the original button text

            if (file) {
                let formData = new FormData();
                formData.append('_token', csrfToken);
                formData.append('mrf_file', file);
                formData.append('product_id', productId);
                formData.append('project_id', projectId);

                // Change button text to "Uploading..."
                $button.text('Uploading...');

                console.log('Sending AJAX request for productId:', productId);

                $.ajax({
                    url: "{{ route('upload.mrf.file') }}",
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        console.log('AJAX success', response);
                        if (response.success) {
                            alert('File uploaded and database updated successfully!');
                            setTimeout(() => location.reload(), 2000);
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        console.log('AJAX error', xhr);
                        let errorMessage = 'Error uploading file.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.statusText) {
                            errorMessage += ' (Status: ' + xhr.statusText + ')';
                        }
                        alert(errorMessage);
                    },
                    complete: function() {
                        // Restore the original button text after the request completes
                        $button.text(originalText);
                    }
                });
            } else {
                console.log('No file selected');
            }
        });
    });
</script>

<script>
    function assignTask(productId, projectId, projectName, operator, operator_id, totalQty, assignedQty) {
        const remainingQty = totalQty - assignedQty;
        let quantity = prompt('Enter the quantity you want to assign to ' + operator + ':');
        if (quantity === null) {
            return;
        }
        if (isNaN(quantity) || quantity <= 0 || quantity > remainingQty) {
            alert('Please enter a valid quantity (maximum: ' + remainingQty + ').');
            return;
        }
        if (confirm('Do you want to assign ' + quantity + ' units of the project named ' + projectName + ' to ' + operator + '?')) {

            $.ajax({
                url: "{{ route('superwisorAssignTaskToOperator') }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    project_id: projectId,
                    operator_id: operator_id,
                    productId: productId,
                    quantity: quantity
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        setTimeout(function() {
                            window.location.href = response.redirect_url;
                        }, 2000);
                    }
                },
                error: function(err) {
                    console.error('Error:', err);
                    toastr.error('Error assigning task. Please try again.');
                }
            });
        }
    }
</script>

<script>
    // When the button is clicked
    /*$(document).on('click', '.show_operator_list', function() {
        // Get the project_id and product_id from the data attributes
        const csrfToken = "{{ csrf_token() }}";
        var projectId = $(this).data('project_id');
        var productId = $(this).data('product_id');

        $.ajax({
            url: "{{route('showOperatorList')}}", // URL of your endpoint that returns the list
            method: 'POST',
            data: {
                _token: csrfToken,
                project_id: projectId,
                product_id: productId
            },
            success: function(response) {
                console.log(response);
                // Clear the previous list
                $('#projectDetailsList').empty();

                // Assuming response is an array of rows to display
                if (response.length > 0) {
                    // Loop through each row and append to the modal list
                    response.forEach(function(row) {
                        console.log(row);
                        $('#projectDetailsList').append('<li>' + row.seq_qty + '  --  ' + row.operator.name + '  --  ' + row.operator.email + '</li>');
                    });
                } else {
                    $('#projectDetailsList').append('<li>No data available</li>');
                }
            },
            error: function() {
                alert('Error loading project details.');
            }
        });
    });*/

    $(document).on('click', '.show_operator_list', function() {
        const csrfToken = "{{ csrf_token() }}";
        const projectId = $(this).data('project_id');
        const productId = $(this).data('product_id');

        $.ajax({
            url: "{{ route('showOperatorList') }}",
            type: 'POST',
            data: {
                _token: csrfToken,
                project_id: projectId,
                product_id: productId
            },
            success: function(response) {
                const $tbody = $('#projectDetailsTable tbody').empty(); // clear previous rows

                if (Array.isArray(response) && response.length) {
                    response.forEach((row, idx) => {
                        $tbody.append(`
                            <tr>
                                <td>${row.seq_qty}</td>
                                <td>${row.operator.name}</td>
                                <td>${row.operator.email}</td>
                            </tr>
                        `);
                    });
                } else {
                    $tbody.append(`
                        <tr>
                            <td colspan="4" class="text-center">No data available</td>
                        </tr>
                    `);
                }
            },
            error: function() {
                alert('Error loading project details.');
            }
        });
    });


    $(document).on('click', '.upload-pl-btn, .upload-partial-pl-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const $input = $(this).closest('.project_check_status').find('.upload-pl-input');
        if ($input.length) {
            $input.trigger('click');
        }
    });

    $(document).on('change', '.upload-pl-input', function() {
        const csrfToken = "{{ csrf_token() }}";
        const file = this.files[0];
        const id = $(this).data('id');
        const label = $(this).data('lable');
        const $button = $(this).closest('.project_check_status').find('.upload-pl-btn, .upload-partial-pl-btn');
        const originalText = $button.text();
        const $successDiv = $(this).closest('.project_check_status').next('.file-upload-success');
        const $row = $(this).closest('tr');
        const product_id = $(this).data('product_id');
        

        if (file) {
            let formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('file', file);
            formData.append('id', id);
            formData.append('product_id', product_id);
            formData.append('lable', label);

            $button.text('Uploading...').prop('disabled', true);

            // Determine if this is a partial order upload
            const isPartialOrder = $(this).closest('table').attr('id') === 'upload_pl_partial_dilivery_table';
            const url = isPartialOrder ? "{{ route('ProductionSuperwisorUploadPartialPlDoc') }}" : "{{ route('ProductionSuperwisorUploadPlDoc') }}";

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    console.log(response);
                    if (response.success) {
                        $successDiv.html('<span class="text-success">File uploaded successfully!</span>');
                        // Remove the row from the table
                        $row.fadeOut(500, function() {
                            $(this).remove();
                            // Check if table is empty
                            if ($('#upload_pl_partial_dilivery_table tbody tr').length === 0) {
                                $('#upload_pl_partial_dilivery_table').replaceWith('<div class="alert alert-info w-100">No Pending Task found.</div>');
                            }
                        });
                        // Optional: Reload page after a delay as a fallback
                        setTimeout(() => location.reload(), 2000);
                    } else {
                        $successDiv.html('<span class="text-danger">Error: ' + (response.message || 'Unknown error') + '</span>');
                    }
                },
                error: function(error) {
                    let errorMessage = 'Error uploading file.';
                    if (error.status === 422) {
                        var errors = error.responseJSON.errors;
                        var errorMessages = '';
                        for (var field in errors) {
                            errorMessages += errors[field].join('<br>');
                        }
                        errorMessage = 'Validation errors:<br>' + errorMessages;
                    } else if (error.responseJSON && error.responseJSON.message) {
                        errorMessage = error.responseJSON.message;
                    }
                    $successDiv.html('<span class="text-danger">' + errorMessage + '</span>');
                    $button.text(originalText).prop('disabled', false);
                },
                complete: function() {
                    $button.text(originalText).prop('disabled', false);
                }
            });
        } else {
            $successDiv.html('<span class="text-danger">No file selected.</span>');
            $button.text(originalText).prop('disabled', false);
        }
    });

    // document.addEventListener('DOMContentLoaded', function() {
    //     const checkboxes = document.querySelectorAll('.process-confirm');
    //     checkboxes.forEach(checkbox => {
    //         checkbox.addEventListener('change', function() {
    //             const id = this.dataset.id;
    //             const checked = this.checked ? 1 : 0;
    //             const csrfToken = "{{ csrf_token() }}";

    //             fetch("{{route('ConfirmAssemblyProcess')}}", {
    //                     method: 'POST',
    //                     headers: {
    //                         'Content-Type': 'application/json',
    //                         'X-CSRF-TOKEN': csrfToken
    //                     },
    //                     body: JSON.stringify({
    //                         id: id,
    //                         checked: checked
    //                     })
    //                 })
    //                 .then(response => response.json())
    //                 .then(data => {
    //                     if (data.success) {
    //                         location.reload();
    //                     } else {
    //                         alert('An error occurred. Please try again.');
    //                     }
    //                 })
    //                 .catch(error => {
    //                     console.error('Error:', error);
    //                     alert('An error occurred. Please try again.');
    //                 });
    //         });
    //     });
    // });

    document.addEventListener('DOMContentLoaded', function() {
        const buttons = document.querySelectorAll('.process-confirm');

        buttons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const csrfToken = "{{ csrf_token() }}";

                fetch("{{ route('ConfirmAssemblyProcess') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            id: id
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Assembled Process confirmed successfully!');
                            location.reload();
                        } else {
                            alert('An error occurred: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                    });
            });
        });
    });

    // View Products
    $(document).on('click', '.view_products', function() {
        const csrfToken = "{{ csrf_token() }}";
        var projectId = $(this).data('project_id');

        $.ajax({
            url: "{{route('viewProjectDetails')}}",
            method: 'POST',
            data: {
                _token: csrfToken,
                project_id: projectId
            },
            success: function(response) {
                console.log(response);

                if (response.length > 0) {
                    $('#viewProductsTable tbody').empty();

                    response.forEach(row => {
                        console.log(row);
                        let full_article_number = row.full_article_number || 'N/A';
                        let description = row.description || '';
                        let total_qty = row.qty || 0;
                        let qty_number = row.qty_number || 0;
                        let statusValue = row.is_final_inspection_started || 0;

                        // Format Unit (Qty) as fraction (e.g., 1/3)
                        let unit_qty = total_qty > 0 ? `${qty_number}/${total_qty}` : 'N/A';

                        let statusLabel = '';
                        let badgeClass = '';

                        switch (statusValue) {
                            case 1:
                                statusLabel = 'In Process';
                                badgeClass = 'badge bg-warning';
                                break;
                            case 2:
                                statusLabel = 'Completed';
                                badgeClass = 'badge bg-success';
                                break;
                            default:
                                statusLabel = 'Not Started';
                                badgeClass = 'badge bg-danger';
                        }

                        let rowHtml = `
                        <tr>
                            <td>${full_article_number}</td>
                            <td>${description}</td>
                            <td class="text-center">${unit_qty}</td>
                            <td class="text-center"><span class="${badgeClass} p-2">${statusLabel}</span></td>
                        </tr>
                    `;

                        $('#viewProductsTable tbody').append(rowHtml);
                    });
                } else {
                    $('#viewProductsTable tbody').html(`
                    <tr>
                        <td colspan="4" style="text-align: center;">No data available</td>
                    </tr>
                `);
                }
            },
            error: function() {
                alert('Error loading project details.');
            }
        });
    });
</script>

<script>
    let selectedProductId = null;

    $('#approveModal').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget);
        selectedProductId = button.data('id');
    });

    $('#confirmApprove').on('click', function() {
        const remarks = $('#approveRemarks').val();

        $.ajax({
            url: "{{ route('approve.pdf') }}",
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                product_id: selectedProductId,
                remarks: remarks
            },
            success: function(response) {
                if (response.success) {
                    $(`button[data-id="${selectedProductId}"]`).closest('tr').remove();
                    $('#approveModal').modal('hide');
                    alert(response.message);
                    location.reload();
                }
            },
            error: function(err) {
                console.error(err);
                alert('Something went wrong!');
            }
        });
    });

    $('#rejectModal').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget);
        selectedProductId = button.data('id');
    });

    $('#confirmReject').on('click', function() {
        const reason = $('#rejectReason').val();

        $.ajax({
            url: "{{ route('reject.pdf') }}",
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                product_id: selectedProductId,
                reason: reason
            },
            success: function(response) {
                if (response.success) {
                    $(`button[data-id="${selectedProductId}"]`).closest('tr').remove();
                    $('#rejectModal').modal('hide');
                    alert(response.message);
                    location.reload();
                }
            },
            error: function(err) {
                console.error(err);
                alert('Something went wrong!');
            }
        });
    });
</script>
@endsection