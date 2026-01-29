@extends('layouts.main')
@section('content')

<link href="{{ asset('css/operator.css') }}" rel="stylesheet" />

<style type="text/css">
    .dataTables_wrapper .dataTables_paginate {
        margin-top: 0px !important;
    }
    .dashboard_heading {
        padding-bottom: 30px;
    }
    #cameraButton {
        margin-top: 20px !important;
    }
    @media (max-width: 768px) {
        table {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
            table-layout: fixed;
        }
    }
    /* New styles for the photos modal */
    #photosModal .modal-body {
        max-height: 70vh;
        /* Limit the modal body height */
        overflow-y: auto;
        /* Add scroll if there are many photos */
    }
    #photosContainer .col-md-4 {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 5px;
        /* Reduced padding for compactness */
    }
    #photosContainer img {
        transition: transform 0.2s;
        /* Add a slight hover effect */
    }
    #photosContainer img:hover {
        transform: scale(1.05);
        /* Slight zoom on hover */
    }
    #photosContainer p {
        margin: 0;
        /* Remove default margin */
        color: #333;
        /* Darker text color for better readability */
    }
    .photosModal-dialog {
        margin-top: 7% !important;
    }
</style>

<div class="main_section bg-white m-4 dashboard_heading p-4">
    <div class="row">
        <div class="ml-4 mt-4 col-xl-3">
            <a href="{{route('OperatorProductType',['product_id'=>$product_id,'redirect' => '1'])}}"
                class="project_icon p-2 m-1 text-decoration-none">
                <i class="fa fa-arrow-left project_icon"></i>
                <span class="text-white">&nbsp; BACK</span>
            </a>
        </div>

        <div class="mt-4 col-xl-8 text-right">
                @if($projects->bom_path != null)

                    @if(Auth::user()->role == "Operator")
                    <a href="#" onclick="downloadFilteredCSV()" class="project_icon p-2 m-1 text-decoration-none">
                      <i class="fa fa-download project_icon"></i>
                      <span class="text-white">&nbsp; BOM </span>
                    </a>
                    @else
                    <a href="{{asset($projects->bom_path)}}" class="project_icon p-2 m-1 text-decoration-none" download>
                        <i class="fa fa-download project_icon"></i>
                        <span class="text-white">&nbsp; BOM </span>
                    </a>
                    @endif            

                @else
                    <a href="#" class="project_icon p-2 m-1 text-decoration-none">
                        <i class="fa fa-tags"></i>
                        <span class="text-white">&nbsp;Requested BOM </span>
                    </a>
                @endif

                @if($projects->drawing_req_estimation_manager == 0)
                    <a href="#" class="project_icon p-2 m-1 text-decoration-none"
                        title="No Drawing request from the Production Engineer while creating a project.">
                        <i class="fa fa-tags project_icon"></i>
                        <span class="text-white">&nbsp;No Drawing request from the Production Engineer</span>
                    </a>
                 
                @elseif($projects->drawing_req_estimation_manager == 1)
                    <a href="#" class="project_icon p-2 m-1 text-decoration-none"
                        title="Didn't get any Drawing Request while creating a project.">
                        <i class="fa fa-tags project_icon"></i>
                        <span class="text-white">&nbsp;Basic Drawings are awaited from the Estimation Manager</span>
                    </a>
                @else
                    <a href="{{ asset($projects->drawing_path) }}"
                        class="project_icon p-2 m-1 text-decoration-none" download>
                        <i class="fa fa-download project_icon"></i>
                        <span class="text-white">&nbsp; Basic Drawing </span>
                    </a>
                @endif
                          
                            @if($projects->drawing_req_estimation_manager == "2" )

                            @php
                            $isRejectedByEstimation = $projects->is_asbuilt_drawing_pdf_approve_by_estimation_manager == 2;
                            $isRejectedByProduction = $projects->is_asbuilt_drawing_pdf_approve_by_production_superwisor == 2;

                            $isApprovedByEstimation = $projects->is_asbuilt_drawing_pdf_approve_by_estimation_manager == 1;
                            $isApprovedByProduction = $projects->is_asbuilt_drawing_pdf_approve_by_production_superwisor == 1;

                            $isRequestedByEstimation = $projects->is_asbuilt_drawing_pdf_approve_by_estimation_manager == 3;
                            $isRequestedByProduction = $projects->is_asbuilt_drawing_pdf_approve_by_production_superwisor == 3;
                            @endphp

                            @if($isRejectedByEstimation && $isRejectedByProduction)
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectReasonModal"
                                data-role="Estimation Manager & Production Supervisor"
                                data-reason="{{ 'Estimation Manager: ' . $projects->asbuilt_drawing_approve_reject_remarks_by_estimation_manager . ' | Production Supervisor: ' . $projects->asbuilt_drawing_approve_reject_remarks_by_production_superwisor }}">
                                Rejected by Estimation Manager & Production Superwisor
                            </button>

                            <button type="button" class="project_icon p-2 m-1 text-decoration-none" data-toggle="modal"
                                data-target="#pdfEditorModal" data-pdf-url="{{ asset($projects->editable_drawing_path) }}"
                                data-project-id="{{ $projects->project_id }}" data-product-id="{{ $projects->id }}">
                                <i class="fa fa-tags project_icon"></i> Open PDF Editor
                            </button>

                            @elseif($isRejectedByEstimation)
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectReasonModal"
                                data-role="Estimation Manager"
                                data-reason="{{ $projects->asbuilt_drawing_approve_reject_remarks_by_estimation_manager }}">
                                Rejected by Estimation Manager
                            </button>

                            <button type="button" class="project_icon p-2 m-1 text-decoration-none" data-toggle="modal"
                                data-target="#pdfEditorModal" data-pdf-url="{{ asset($projects->editable_drawing_path) }}"
                                data-project-id="{{ $projects->project_id }}" data-product-id="{{ $projects->id }}">
                                <i class="fa fa-tags project_icon"></i> Open PDF Editor
                            </button>

                            @elseif($isRejectedByProduction)
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectReasonModal"
                                data-role="Production Supervisor"
                                data-reason="{{ $projects->asbuilt_drawing_approve_reject_remarks_by_production_superwisor }}">
                                Rejected by Production Superwisor
                            </button>

                            <button type="button" class="project_icon p-2 m-1 text-decoration-none" data-toggle="modal"
                                data-target="#pdfEditorModal" data-pdf-url="{{ asset($projects->editable_drawing_path) }}"
                                data-project-id="{{ $projects->project_id }}" data-product-id="{{ $projects->id }}">
                                <i class="fa fa-tags project_icon"></i> Open PDF Editor
                            </button>

                            @elseif($isApprovedByEstimation && $isApprovedByProduction)
                            <a href="{{ asset($projects->editable_drawing_path) }}" class="project_icon p-2 m-1 text-decoration-none" download>
                                <i class="fa fa-download project_icon"></i>
                                <span class="text-white">&nbsp; Operators As-Built Drawing</span>
                            </a>

                            @elseif($isApprovedByEstimation && !$isApprovedByProduction)
                            <button type="button" class="btn btn-info" disabled>
                                <span class="text-white">&nbsp;As-Built PDF approval is awaited from Production Superwisor</span>
                            </button>

                            @elseif(!$isApprovedByEstimation && $isApprovedByProduction)
                            <button type="button" class="btn btn-info" disabled>
                                <span class="text-white">&nbsp;As-Built PDF approval is awaited from Estimation Manager</span>
                            </button>

                            @elseif($isRequestedByEstimation && $isRequestedByProduction)
                            <button type="button" class="btn btn-warning" disabled>
                                <span class="text-white">&nbsp; As-built Drawing Request Sent to Estimation Manager & Production Superwisor</span>
                            </button>

                            @elseif($isRequestedByEstimation)
                            <button type="button" class="btn btn-warning" disabled>
                                Request Sent to Estimation Manager
                            </button>

                            @elseif($isRequestedByProduction)
                            <button type="button" class="btn btn-warning" disabled>
                                Request Sent to Production Superwisor
                            </button>

                            @else
                            <button type="button" class="project_icon p-2 m-1 text-decoration-none" data-toggle="modal"
                                data-target="#pdfEditorModal" data-pdf-url="{{ asset($projects->drawing_path) }}"
                                data-project-id="{{ $projects->project_id }}" data-product-id="{{ $projects->id }}">
                                <i class="fa fa-tags project_icon"></i> Open PDF Editor
                            </button>
                            @endif

                    @endif

                    @if($projects->editable_drawing_path)
                        @if($projects->drawing_upload_by_estimation_manager)
                            <a href="{{ asset($projects->drawing_upload_by_estimation_manager) }}"
                                class="project_icon p-2 m-1 text-decoration-none" download>
                                <i class="fa fa-download project_icon"></i>
                                <span class="text-white">&nbsp;Final Drawing </span>
                            </a>
                        @else
                        @if($isRequestedByEstimation && $isRequestedByProduction)
                            <button type="button" class="btn btn-secondary p-2 m-1" disabled
                                title="Estimation Manager has not uploaded the final drawing.">
                                <i class="fa fa-exclamation-circle project_icon"></i>
                                <span class="text-white">&nbsp; Estimation Manager will upload Final Drawing after getting approval</span>
                            </button>
                        @else
                            <button type="button" class="btn btn-secondary p-2 m-1" disabled
                                title="Estimation Manager has not uploaded the final drawing.">
                                <i class="fa fa-exclamation-circle project_icon"></i>
                                <span class="text-white">&nbsp; Final Drawing is awaited from Estimation Manager.</span>
                            </button>
                        @endif
                        @endif
                    @endif
        </div>
    </div>

    <h3 class="ml-4 pt-4 text-bold text-left text-uppercase">{{$page_title}}
        <a class="mr-4 mt-3 mb-2 btn btn-primary btn-sm py-1 px-2 mt-4 float-right" onclick="location.reload();">
            <i class="fa fa-refresh text-light">&#xf021;</i>
        </a>
    </h3>

    <div class="m-3 mt-2  project_approval_section border">
        <h5 class="text-uppercase text-left mb-2 mt-3 ml-3 font-weight-bolder">
            Project Details</h5>
        <hr class="mb-0 mt-0">
        <div class="card-body p-4">
            <div class="row mb-2">
                <div class="col-md-2 text-bold">Project No. :</div>
                <div class="col-md-10 text-dark">
                    {{ $projects->projects['project_no'] }}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-2 text-bold">Project Name :</div>
                <div class="col-md-10 text-dark">
                    {{ $projects->projects['project_name'] }}
                </div>
            </div>
            <div class="row">
                <div class="col-md-2 text-bold">Estimated Date :</div>
                <div class="col-md-10 text-dark">
                    {{ $projects->projects['estimated_readiness'] ? \Carbon\Carbon::parse($projects->projects['estimated_readiness'])->format('d-M-Y') : 'N/A' }}
                </div>
            </div>
        </div>
    </div>

    <div class="m-3 mt-4 project_approval_section border">
        <h5 class="text-uppercase text-left mb-2 mt-3 ml-3 font-weight-bolder">Product Type : {{$project_type_name}}
        </h5>
        <hr class="mx-0 mt-0" />
        <div class="row mt-3 mx-3">
            <table class="table table-hover table-border w-100 text-center" id="project_table">
                <thead>
                    <tr>
                        <th scope="col" class="project_table_heading">Process</th>
                        <th scope="col" class="project_table_heading">Standard Time <br>(In Hours)</th>
                        <th scope="col" class="project_table_heading">Capture</th>
                        <th scope="col" class="project_table_heading">Timer</th>
                        <!-- <th scope="col" class="project_table_heading">Show Current Timer</th> -->
                        <th scope="col" class="project_table_heading">Actual Time</th>
                        <th scope="col" class="project_table_heading">Status</th>
                        <th scope="col" class="project_table_heading">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($process_name as $key => $val)
                        @php
                            $previous_val = $previous_val ?? null; // Initialize previous value
                            $disabled_btn_class = '';

                            // Disable current button if the process is completed
                            if ($val->project_status == '1') {
                            $disabled_btn_class = 'disabled-btn';
                            }

                            // Enable the first button if it is not completed
                            if ($key == 0 && $val->project_status != '1') {
                            $disabled_btn_class = ''; // First process is pending, so enable it
                            }

                            // Disable all others initially
                            if ($key != 0 && $val->project_status != '1') {
                            $disabled_btn_class = 'disabled-btn';
                            }

                            // Enable only if the previous process is completed
                            if ($previous_val && $previous_val->project_status == '1' && $val->project_status != '1') {
                            $disabled_btn_class = ''; // Enable this process because the previous one is completed
                            }

                            // Set current item as the previous item for the next iteration
                            $previous_val = $val;

                            $opCount = count($assignedOperatorProductQtyWiseIDs ?: []);
                            // fallback: if no operators, still render one row
                            $opCount = $opCount > 0 ? $opCount : 1;
                        @endphp

                    @foreach(($assignedOperatorProductQtyWiseIDs ?: [null]) as $opIndex => $operatorId)
                        @php
                            $rowUnique = $val->projects_id . '-' . $val->product_id . '-' . $val->order_qty . '-' . $key;
                            $uniqueId = $val->projects_id.'-'.$val->product_id.'-'.$val->order_qty.'-'.$operatorId.'-'.$key;
                        @endphp
                    
                    @if($opIndex === 0)
                    <tr data-unique-id="{{$val->projects_id . '-' . $val->product_id . '-' . $val->order_qty . '-' . $key}}">
                        <td class="w-30" rowspan="{{ $opCount }}">{{$val->project_process_name}}</td>

                        <td class="w-15" rowspan="{{ $opCount }}">
                            <span id="" class="text-bold">{{getTimeFormat($val->process_std_time)}} </span>
                            <span class="text-bold">
                        </td>

                        <td class="w-10" rowspan="{{ $opCount }}">
                            <i class="fa fa-camera project_icon p-2 m-1"
                                onclick="openCamera('{{$val->projects_id . '-' . $val->product_id . '-' . $val->order_qty . '-' . $key}}', 
                                    '{{$projects->projects['project_no']}}',
                                    '{{$val->projects_id}}',
                                    '{{$val->product_id}}',
                                    '{{$val->order_qty}}',
                                    '{{$val->project_type_name}}',
                                    '{{$val->project_process_name}}')">
                            </i>

                            <i class="fa fa-info-circle project_icon p-2 m-1 photo-info-btn"
                                data-unique-id="{{$val->projects_id . '-' . $val->product_id . '-' . $val->order_qty . '-' . $key}}"
                                onclick="openPhotosModal('{{$val->projects_id . '-' . $val->product_id . '-' . $val->order_qty . '-' . $key}}', 
                                '{{$projects->projects['project_no']}}',
                                '{{$val->product_id}}',
                                '{{$val->order_qty}}',
                                '{{$val->project_type_name}}',
                                '{{$val->project_process_name}}')"
                                title="View Photos" style="display: none;">
                            </i>

                            <input type="file" id="cameraInput" accept="image/*" capture="environment" style="display: none;" />
                        </td>
                        
                        @else
                            <tr data-unique-id="{{ $rowUnique }}-op-{{ $operatorId ?? 'op0' }}">
                        @endif

                        <td class="w-20 text-center">
                            <div class="operator-block"> 
                                <strong class="mr-2">{{ $operators_name[$operatorId] ?? 'Unknown' }}</strong>

                                @if(($val->project_process_name == 'Packing' || $val->project_process_name == 'Export packing') && $projects->editable_drawing_path != null && $projects->drawing_upload_by_estimation_manager == null)
                                    <span class="text-danger">{{ 'Final PDF delivery is awaited from the Estimation Manager.' }}</span>

                                @elseif(($val->project_process_name == 'Packing' || $val->project_process_name == 'Export packing') && $projects->editable_drawing_path == null && $projects->drawing_req_estimation_manager == 1)

                                    <span class="text-danger">{{ 'Final PDF delivery is awaited from the Estimation Manager.' }}</span>
                                    @else
                                    
                                    <a class="pt-1 pb-1 ml-0 cursor_pointer start-timer {{$disabled_btn_class}}"
                                    href="javascript:void(0)" id="start-btn-{{ $uniqueId }}" 
                                        data-unique-id="{{ $val->projects_id }}-{{ $val->product_id }}-{{ $val->order_qty }}-{{ $operatorId }}-{{ $key }}"
                                        {{--data-unique-id="{{ $uniqueId }}"--}}    
                                        data-operator-id="{{ $operatorId }}" 
                                        data-key="{{ $key }}"   
                                        data-project-id="{{ $val->projects_id }}" 
                                        data-product-id="{{ $val->product_id }}"
                                        data-project-type-name="{{ $val->project_type_name }}"
                                        data-project-process-name="{{ $val->project_process_name }}"
                                        data-seq-qty="{{ $val->order_qty }}" 
                                        data-std-time="{{ $val->process_std_time }}"
                                        title="Click here to Start Timer">
                                        <i class="fa fa-clock project_icon p-2 m-1"></i>
                                    </a>

                                @endif                             

                                <a class="pt-1 pb-1 d-none cursor_pointer pause-timer" href="javascript:void(0)"
                                    data-operator-id="{{ $operatorId }}" 
                                    data-unique-id="{{ $uniqueId }}" 
                                    data-key="{{ $key }}"
                                    data-project-id="{{ $val->projects_id }}" 
                                    data-product-id="{{ $val->product_id }}"
                                    data-project-type-name="{{ $val->project_type_name }}"
                                    data-project-process-name="{{ $val->project_process_name }}"
                                    data-std-time="{{ $val->process_std_time }}" 
                                    data-seq-qty="{{ $val->order_qty }}"
                                    title="Click here to Pause Timer">
                                    <i class="fa fa-pause project_icon p-2 m-1 ml-3"></i>
                                </a>

                                <a class="pt-1 pb-1 d-none cursor_pointer stop-timer" href="javascript:void(0)"
                                    data-unique-id="{{ $uniqueId }}" 
                                    data-key="{{ $key }}"
                                    data-operator-id="{{ $operatorId }}" 
                                    data-project-id="{{ $val->projects_id }}" 
                                    data-product-id="{{ $val->product_id }}"
                                    data-project-type-name="{{ $val->project_type_name }}"
                                    data-project-process-name="{{ $val->project_process_name }}"
                                    data-std-time="{{ $val->process_std_time }}" 
                                    data-seq-qty="{{ $val->order_qty }}"
                                    title="Click here to Stop Timer">
                                    <i class="fa fa-stop project_icon p-2 m-1 ml-3"></i>
                                </a>
                            </div>
                        </td>

                        {{--
                        <td class="w-15">
                            <span id="time-remaining-{{$uniqueId}}"
                                class="text-white p-2 text-center br-12 primary_bg_color w-20 d-none fs-22">{{$val->process_std_time}}</span>
                            <span id="time-remaining-{{ $val->projects_id }}-{{ $val->product_id }}-{{$val->order_qty}}"
                            class="text-white p-2 text-center br-12 primary_bg_color w-20 d-none fs-22">{{$val->process_std_time}}</span>
                        </td>
                        --}}

                        <td class="w-15 actual-time" 
                            data-unique-id="{{ $uniqueId }}"
                            data-project-id="{{ $val->projects_id }}" 
                            data-product-id="{{ $val->product_id }}"
                            data-seq-qty="{{ $val->order_qty }}">
                            <span class="project_icon">
                                {{$val->project_actual_time == "00:00:00" ? "" : $val->project_actual_time }}
                            </span>
                        </td>
                        
                        @if($opIndex === 0)
                            <td class="w-15" rowspan="{{ $opCount }}">
                                <span class="badge p-2 status" 
                                    data-unique-id="{{ $rowUnique }}"
                                    data-project-id="{{ $val->projects_id }}" 
                                    data-product-id="{{ $val->product_id }}"
                                    data-seq-qty="{{ $val->order_qty }}">
                                    @if($val->project_status == "0")
                                    <span class="badge badge-danger p-2">Pending</span>
                                    @else
                                    <span class="badge badge-success p-2">Completed</span>
                                    @endif
                                </span>
                            </td>
                            
                            <td>
                                <a class="pt-1 pb-1 ml-3 cursor_pointer reset-timer" href="javascript:void(0)"
                                    data-key="{{ $key }}" data-project-id="{{ $val->projects_id }}"
                                    data-product-id="{{ $val->product_id }}"
                                    data-project-type-name="{{ $val->project_type_name }}"
                                    data-project-process-name="{{ $val->project_process_name }}"
                                    data-seq-qty="{{ $val->order_qty }}" title="Click here to Start Timer">
                                    <i class="fa fa-window-close project_icon p-2 m-1"></i>
                                </a>
                            </td>
                        @endif
                    </tr>
                    @endforeach
                    @php $previous_val = $val; @endphp
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- PDF Editor Modal -->
<div class="modal fade" id="pdfEditorModal" tabindex="-1" role="dialog" aria-labelledby="pdfEditorModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pdfEditorModalLabel">PDF Editor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <div class="mb-3 d-flex justify-content-between">
                    <div>
                        <button class="btn btn-success" onclick="addText()">🅰️ Add Text</button>
                        <button class="btn btn-warning" onclick="chooseShape()" title="Available Shapes: Rectangle, Circle, Line, Triangle">âž• Add Shape</button>
                        <button class="btn btn-info" onclick="clearCanvas()">🗑 Clear</button>
                        <button onclick="deleteSelectedObject()" class="btn btn-danger">Delete Selected Text or Shape</button>
                        <button onclick="editSelectedText()" class="btn btn-primary">Edit Text</button>
                        <br><br>
                        <button class="btn btn-danger" onclick="saveEditedPDF()">📄 Save PDF</button>
                    </div>

                    <!-- Page Navigation -->
                    <div>
                        <button class="btn btn-secondary" onclick="changePage(-1)">⬅ Previous</button>
                        <span id="pageNumberDisplay" class="mx-2"></span>
                        <button class="btn btn-secondary" onclick="changePage(1)">Next ➡</button>
                    </div>
                </div>

                <div id="pdfImageContainer" style="text-align: center; max-height: 75vh; overflow: auto;"></div>
            </div>
        </div>
    </div>
</div>

<!-- rejection modal -->
<div class="modal fade rejectReason" id="rejectReasonModal" tabindex="-1" aria-labelledby="rejectReasonModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectReasonModalLabel">Rejection Reason</h5>
            </div>
            <div class="modal-body">
                <p><strong>Rejected By:</strong> <span id="rejectedByRole"></span></p>
                <p><strong>Reason:</strong></p>
                <p id="rejectionReasonText" class="text-warning"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Photos Modal -->
<div class="modal fade" id="photosModal" tabindex="-1" role="dialog" aria-labelledby="photosModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg photosModal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white" id="photosModalLabel">Photos for Process</h5>
            </div>
            <div class="modal-body">
                <div id="photosContainer" class="row mx-0">
                    <!-- Photos will be dynamically loaded here -->
                </div>
                <div id="noPhotosMessage" class="text-center" style="display: none;">
                    <p>No photos found for this process.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')

<script type="text/javascript">
    const updateStatusUrl = "{{ route('OperatorUpdateProcessStatus') }}";
    const csrfToken = "{{ csrf_token() }}";
</script>

<script>
    const projectId = @json($projects['projects']->id);
    const productId = @json($projects->id);
    const orderQty = @json($seq_qty);
    const processName = @json($active_process_name);

    window.projectId = @json($projects['projects']->id);
    window.productId = @json($projects->id);
    window.orderQty = @json($seq_qty);
    window.processName = @json($active_process_name);
</script>

<script>
    let timerIntervals = {};
    let actualTimes = {};
    let remainingTimes = {};
    let startTimes = {};
    let totalTimes = {}
    let photoAvailability = {};

    function startTimer(uniqueId, projectId, productId, orderQty, remainingTime) {
        //const timerElement = document.getElementById(`time-remaining-${uniqueId}`)
        const actualTimeElement = document.querySelector(`.actual-time[data-unique-id="${uniqueId}"]`)
        const startButton = document.querySelector(`.start-timer[data-unique-id="${uniqueId}"]`)
        const pauseButton = document.querySelector(`.pause-timer[data-unique-id="${uniqueId}"]`)
        const stopButton = document.querySelector(`.stop-timer[data-unique-id="${uniqueId}"]`)

        startButton.classList.add("disabled-btn")
        pauseButton.classList.remove("d-none")
        stopButton.classList.remove("d-none")
        clearInterval(timerIntervals[uniqueId])
        startTimes[uniqueId] = Date.now()
        if (totalTimes[uniqueId] === undefined) {
            const stdTimeButton = document.querySelector(`.start-timer[data-unique-id="${uniqueId}"]`)
            const stdTimeHours = parseFloat(stdTimeButton.dataset.stdTime)
            totalTimes[uniqueId] = stdTimeHours * 3600 // Convert hours to seconds
        }
        remainingTimes[uniqueId] = remainingTime
        actualTimes[uniqueId] = totalTimes[uniqueId] - remainingTime
        
        if (actualTimeElement) {
            actualTimeElement.textContent = formatTime(actualTimes[uniqueId])
        }

        timerIntervals[uniqueId] = setInterval(() => {
            const elapsedSinceStart = Math.floor((Date.now() - startTimes[uniqueId]) / 1000)

            // Calculate current remaining time by subtracting elapsed time since this session started
            const remaining = Math.max(0, remainingTimes[uniqueId] - elapsedSinceStart)

            // Calculate total elapsed time
            const totalElapsed = totalTimes[uniqueId] - remaining

            if (actualTimeElement) {
                actualTimeElement.textContent = formatTime(totalElapsed)
            }

            if (remaining <= 0) {
                clearInterval(timerIntervals[uniqueId])
                autoStopTimer(uniqueId, projectId, productId, orderQty, totalElapsed)
            }
        }, 1000)
    }

    function pauseTimer(uniqueId) {
        // Clear any running interval for this timer
        if (timerIntervals[uniqueId]) {
            clearInterval(timerIntervals[uniqueId]);
            delete timerIntervals[uniqueId];
        }

        // Calculate elapsed time since this session started
        const elapsedSinceStart = startTimes[uniqueId] ?
            Math.floor((Date.now() - startTimes[uniqueId]) / 1000) : 0;

        // Update remaining time by subtracting elapsed time since this session started
        if (remainingTimes[uniqueId] !== undefined) {
            remainingTimes[uniqueId] = Math.max(0, remainingTimes[uniqueId] - elapsedSinceStart);
        }

        // Update actual time
        if (totalTimes[uniqueId] !== undefined && remainingTimes[uniqueId] !== undefined) {
            actualTimes[uniqueId] = totalTimes[uniqueId] - remainingTimes[uniqueId];
        }

        // Update UI elements
        const startButton = document.querySelector(`.start-timer[data-unique-id="${uniqueId}"]`);
        const pauseButton = document.querySelector(`.pause-timer[data-unique-id="${uniqueId}"]`);
        const stopButton = document.querySelector(`.stop-timer[data-unique-id="${uniqueId}"]`);

        if (startButton) startButton.classList.remove('disabled-btn');
        if (pauseButton) pauseButton.classList.add('d-none');
        if (stopButton) stopButton.classList.remove('d-none');

        // Update the timer display
        updateTimerDisplay(uniqueId);

        // Store the pause time
        window.lastPauseTime = Date.now();
    }

    function updateTimerDisplay(uniqueId) {
        //const timerElement = document.getElementById(`time-remaining-${uniqueId}`)
        const actualTimeElement = document.querySelector(`.actual-time[data-unique-id="${uniqueId}"]`)

        if (actualTimeElement && actualTimes[uniqueId] !== undefined) {
            actualTimeElement.textContent = formatTime(actualTimes[uniqueId])
        }
    }

    function autoStopTimer(uniqueId, projectId, productId, orderQty, actualTime) {
        const stopButton = document.querySelector(`.stop-timer[data-unique-id="${uniqueId}"]`);
        const projectTypeName = stopButton.dataset.projectTypeName;
        const projectProcessName = stopButton.dataset.projectProcessName;

        fetch("{{ route('OperatorstopTimer') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    uniqueId,
                    projectId,
                    productId,
                    orderQty,
                    actualTime,
                    project_type_name: projectTypeName,
                    project_process_name: projectProcessName,
                    autoStopped: true
                })
            })
            .then(response => response.json())
            .then(data => {
                stopTimer(uniqueId, projectId, productId, orderQty, data.actualTime, data.status);
            });
    }

    function formatTime(seconds) {
        const hours = Math.floor(seconds / 3600)
        const minutes = Math.floor((seconds % 3600) / 60)
        const remainingSeconds = Math.floor(seconds % 60)
        return sprintf("%02d:%02d:%02d", hours, minutes, remainingSeconds)
    }

    function sprintf(format, ...args) {
        return format.replace(/%(\d+)?d/g, (match, width) => {
            const num = args.shift()
            return width ? num.toString().padStart(width, "0") : num
        })
    }

    function stopTimer(uniqueId, projectId, productId, orderQty, actualTime, status) {
        if (timerIntervals[uniqueId]) {
            clearInterval(timerIntervals[uniqueId]);
            delete timerIntervals[uniqueId];
            console.log("Timer stopped for:", uniqueId);
        }

        delete actualTimes[uniqueId];
        delete remainingTimes[uniqueId];
        delete startTimes[uniqueId];

        // const timerElement = document.getElementById(`time-remaining-${uniqueId}`);
        const startButton = document.querySelector(`.start-timer[data-unique-id="${uniqueId}"]`);
        const pauseButton = document.querySelector(`.pause-timer[data-unique-id="${uniqueId}"]`);
        const stopButton = document.querySelector(`.stop-timer[data-unique-id="${uniqueId}"]`);
        // Updated selectors to use uniqueId
        const actualTimeElement = document.querySelector(`.actual-time[data-unique-id="${uniqueId}"]`);
        const statusElement = document.querySelector(`.status[data-unique-id="${uniqueId}"]`);

        // if (timerElement) {
        //     timerElement.classList.add('d-none');
        // }

        if (startButton) {
            startButton.classList.add('disabled-btn');
            startButton.setAttribute('disabled', 'true'); // Ensure it's disabled
        }

        // if (startButton) startButton.classList.add('disabled-btn');
        if (pauseButton) pauseButton.classList.add('d-none');
        if (stopButton) stopButton.classList.add('d-none');

        if (actualTimeElement) {
            actualTimeElement.textContent = actualTime;
            console.log('Updating actual time to:', actualTime, 'for uniqueId:', uniqueId);
        }

        if (statusElement) {
            statusElement.innerHTML = status === "1" ?
                '<span class="badge badge-success p-2">Completed</span>' :
                '<span class="badge badge-danger p-2">Pending</span>';
            console.log('Updating status to:', status, 'for uniqueId:', uniqueId);
        }

        // Enable next process button if status is completed
        if (status === "1") {
            const currentButton = document.querySelector(`.start-timer[data-unique-id="${uniqueId}"]`);
            if (!currentButton) return;

            const currentKey = parseInt(currentButton.dataset.key);
            const nextButton = document.querySelector(`.start-timer[data-key="${currentKey + 1}"]`);

            if (nextButton) {
                nextButton.classList.remove('disabled-btn');
                const nextUniqueId = nextButton.dataset.uniqueId;
                if (nextUniqueId) {
                    setTimeout(() => {
                        const nextTimerElement = document.getElementById(`time-remaining-${nextUniqueId}`);
                        if (nextTimerElement) {
                            const rawText = nextTimerElement.textContent.trim();

                            const floatTime = parseFloat(rawText);

                            if (!isNaN(floatTime) && !rawText.includes(':')) {
                                const seconds = floatTime * 3600;
                                const formattedTime = formatTime(seconds);
                                nextTimerElement.textContent = formattedTime;
                            }

                            nextTimerElement.classList.remove('d-none');
                        }
                    }, 10); // Small delay to let DOM render if needed
                }
            }

        }
    }

    function convertHoursToSeconds(hours) {
        return Math.floor(hours * 3600); // Convert hours to seconds
    }

    startPeriodicStateCheck();

    function startPeriodicStateCheck() {
        setInterval(() => {
            if (typeof projectId !== 'undefined' &&
                typeof productId !== 'undefined' &&
                typeof orderQty !== 'undefined') {
                fetchTimerState(projectId, productId, orderQty);
            }
        }, 3000);
    }

    // Call this when the page loads

    async function fetchTimerState(projectId, productId, orderQty, processName,operatorId,uniqueId) {
        console.log(operatorId);
        try {
            const response = await fetch("/get-timer-state", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    uniqueId,
                    operatorId,
                    projectId,
                    productId,
                    orderQty,
                    process_name: processName
                }),
            })

            const data = await response.json()

            Object.entries(data.timer_states).forEach(([uniqueId, timerState]) => {
                // const timerElement = document.getElementById(`time-remaining-${uniqueId}`)
                const actualTimeElement = document.querySelector(`.actual-time[data-unique-id="${uniqueId}"]`)
                //
                const startButton = document.querySelector(`.start-timer[data-unique-id="${uniqueId}"]`)
                const pauseButton = document.querySelector(`.pause-timer[data-unique-id="${uniqueId}"]`)

                const stopButton = document.querySelector(`.stop-timer[data-unique-id="${uniqueId}"]`)
                //

                const stdTimeButton = document.querySelector(`.start-timer[data-unique-id="${uniqueId}"]`)
                
                if (!stdTimeButton) {
                    const stdTimeInSeconds = Number.parseFloat(stdTimeButton.dataset.stdTime) * 3600
                    totalTimes[uniqueId] = stdTimeInSeconds
                }

                if (timerState.status === "running") {
                    // Calculate time elapsed since the server started the timer
                    const serverStartTime = new Date(timerState.timer_started_at)
                    const currentTime = new Date()
                    const elapsedSinceServerStart = Math.floor((currentTime - serverStartTime) / 1000)

                    // Set the actual time to what the server reports plus time since server start
                    actualTimes[uniqueId] = timerState.elapsed_time

                    // Calculate the total time (standard time in seconds)
                    const stdTimeInSeconds =
                        Number.parseFloat(document.querySelector(`.start-timer[data-unique-id="${uniqueId}"]`).dataset.stdTime) * 3600
                    totalTimes[uniqueId] = stdTimeInSeconds

                    // Start the timer with the correct remaining time
                    startTimer(uniqueId, projectId, productId, orderQty, timerState.remaining_time)

                    // Show the timer element
                    // if (timerElement) {
                    //     timerElement.classList.remove("d-none")
                    // }
                } else if (timerState.status === "paused") {
                    // For paused timers, just update the displays
                    actualTimes[uniqueId] = timerState.elapsed_time
                    remainingTimes[uniqueId] = timerState.remaining_time

                    // if (timerElement) {
                    //     timerElement.classList.remove("d-none")
                    //     timerElement.textContent = formatTime(timerState.remaining_time)
                    // }

                    if (actualTimeElement) {
                        actualTimeElement.textContent = formatTime(timerState.elapsed_time)
                    }

                    // Make sure the UI shows paused state
                    const startButton = document.querySelector(`.start-timer[data-unique-id="${uniqueId}"]`)
                    const pauseButton = document.querySelector(`.pause-timer[data-unique-id="${uniqueId}"]`)
                    const stopButton = document.querySelector(`.stop-timer[data-unique-id="${uniqueId}"]`)

                    if (startButton) startButton.classList.remove("disabled-btn")
                    if (pauseButton) pauseButton.classList.add("d-none")
                    if (stopButton) stopButton.classList.remove("d-none")
                } else if (timerState.status === "completed") {
                    // For completed timers, update the UI accordingly
                    if (actualTimeElement) {
                        actualTimeElement.textContent = timerState.actual_time
                    }
                    // if (timerElement) {
                    //     timerElement.classList.add("d-none")
                    // }

                    const statusElement = document.querySelector(`.status[data-unique-id="${uniqueId}"]`)
                    if (statusElement) {
                        statusElement.innerHTML =
                            timerState.project_status == "1" ?
                            '<span class="badge badge-success p-3 test3">Completed</span>' :
                            '<span class="badge badge-danger p-2 test3">Pending</span>'
                    }
                }
            })
        } catch (error) {
            console.error("Error fetching timer state:", error)
        }
    }

    // Call this function when the page loads
    // fetchTimerState(projectId, productId, orderQty, processName);

    document.addEventListener('DOMContentLoaded', function() {
        const pusherKey = "{{ env('VITE_PUSHER_APP_KEY') }}";
        const pusherCluster = "{{ env('VITE_PUSHER_APP_CLUSTER') }}";
        window.Pusher = Pusher;

        if (window.Echo) {
            console.log('Echo is properly initialized');
        } else {
            console.log("Echo not working");
            return;
        }

        // Fetch photo availability for all processes
        function fetchPhotoAvailability() {
            const processes = @json($process_name);
            processes.forEach((process, key) => {
                const uniqueId = `${process.projects_id}-${process.product_id}-${process.order_qty}-${key}`;
                fetch("{{ route('get.process.photos') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            project_no: "{{$projects->projects['project_no']}}",
                            product_id: process.product_id,
                            seq_qty: process.order_qty,
                            product_type: process.project_type_name,
                            project_process_name: process.project_process_name
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        photoAvailability[uniqueId] = data.success && data.photos.length > 0;
                        const infoButton = document.querySelector(`.photo-info-btn[data-unique-id="${uniqueId}"]`);
                        if (infoButton) {
                            infoButton.style.display = photoAvailability[uniqueId] ? 'inline-block' : 'none';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching photo availability for', uniqueId, ':', error);
                        photoAvailability[uniqueId] = false;
                        const infoButton = document.querySelector(`.photo-info-btn[data-unique-id="${uniqueId}"]`);
                        if (infoButton) {
                            infoButton.style.display = 'none';
                        }
                    });
            });
        }

        fetchPhotoAvailability();
  
        document.querySelectorAll('.start-timer').forEach(button => {
            button.addEventListener('click', function() {

                if (this.classList.contains('disabled-btn')) return;
                const operatorId = this.dataset.operatorId;
                const uniqueId = this.dataset.uniqueId;
                const projectId = this.dataset.projectId;
                const productId = this.dataset.productId;
                const orderQty = this.dataset.seqQty;
                const processName = this.dataset.projectProcessName;
                const stdTime = parseFloat(this.dataset.stdTime);
                
                const timerKey = `${uniqueId}-${operatorId}`;

                //const remainingTime = remainingTimes[uniqueId] || convertHoursToSeconds(stdTime);
                const remainingTime = remainingTimes[uniqueId] || convertHoursToSeconds(stdTime);

                this.classList.add('disabled-btn');

                //const timerElement = document.getElementById(`time-remaining-${uniqueId}`);
                //const timerElement = document.getElementById(`time-remaining-${uniqueId}`);

                // if (timerElement) {
                //     // Store the current display value
                //     const currentDisplay = timerElement.textContent;
                //     // You could show a loading indicator here if desired
                // }

                fetch("{{ route('OperatorstartTimer') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            operatorId,
                            uniqueId,
                            projectId,
                            productId,
                            orderQty,
                            process_name: processName,
                            remainingTime
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        //here working 4
                        fetchTimerState(projectId, productId, orderQty, processName,operatorId,uniqueId);

                    })
                    .catch(error => {
                        console.error("Error starting timer:", error);
                        // Re-enable the button if there was an error
                        this.classList.remove('disabled-btn');
                    });
            });
        });

        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                const context = this;
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(context, args), wait);
            };
        }

        document.querySelectorAll('.pause-timer').forEach(button => {
            // button.addEventListener('click', function() {
            button.addEventListener('click', debounce(function() {
                const operatorId = this.dataset.operatorId;
                const uniqueId = this.dataset.uniqueId;
                const projectId = this.dataset.projectId;
                const productId = this.dataset.productId;
                const orderQty = this.dataset.seqQty;
                const processName = this.dataset.projectProcessName;

                // Record the exact client time when pause was clicked
                const clientPauseTime = Date.now();

                // Immediately pause the timer in the UI
                pauseTimer(uniqueId);

                // Then send the request to the server with the client pause time
                fetch("{{ route('OperatorpauseTimer') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            operatorId,
                            uniqueId,
                            projectId,
                            productId,
                            orderQty,
                            process_name: processName,
                            remainingTime: remainingTimes[uniqueId] || 0,
                            clientPauseTime: clientPauseTime // Send the exact time when pause was clicked
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        // After the server responds, update with the server's values
                        remainingTimes[uniqueId] = data.remainingTime;
                        actualTimes[uniqueId] = data.elapsedTime;

                        // Calculate time drift between client and server
                        const serverTime = data.serverTime;
                        const clientTime = Date.now();
                        const timeDrift = clientTime - serverTime;

                        // Store the time drift for future synchronization
                        window.serverTimeDrift = timeDrift;

                        // Update the display with the server's values
                        updateTimerDisplay(uniqueId);

                        // Fetch the latest state to ensure all tabs are in sync
                        fetchTimerState(projectId, productId, orderQty, processName);
                    });
            }, 300));
        });

        // Update the stop button event listener
        document.querySelectorAll('.stop-timer').forEach(button => {
            button.addEventListener('click', function() {
                const operatorId = this.dataset.operatorId;
                const uniqueId = this.dataset.uniqueId;
                const projectId = this.dataset.projectId;
                const productId = this.dataset.productId;
                const orderQty = this.dataset.seqQty;
                const projectTypeName = this.dataset.projectTypeName; // Add this
                const projectProcessName = this.dataset.projectProcessName; // Add this
                const actualTime = actualTimes[uniqueId] || 0;

                fetch("{{ route('OperatorstopTimer') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            operatorId,
                            uniqueId,
                            projectId,
                            productId,
                            orderQty,
                            actualTime,
                            project_type_name: projectTypeName, // Add this
                            project_process_name: projectProcessName // Add this
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Stop timer response:', data);
                        stopTimer(uniqueId, projectId, productId, orderQty, data.actualTime, data.status);
                    });
            });
        });

        // Listening for broadcast events
        function listenToTimerEvents(uniqueId, projectId, productId, orderQty) {
            const channelName = `timer.project.${projectId}.product.${productId}.order.${orderQty}`;
            console.log('Setting up listener for channel:', channelName);

            Echo.channel(channelName)
                .listen('.timer.started', (data) => {
                    console.log('Timer started event received:', data);
                    startTimer(data.uniqueId, data.projectId, data.productId, data.orderQty, data.remainingTime);
                })
                .listen('.timer.paused', (data) => {
                    console.log('Timer paused event received:', data);
                    pauseTimer(data.uniqueId);
                })
                .listen('.timer.stopped', (data) => {
                    console.log('Timer stopped event received:', data);
                    stopTimer(data.uniqueId, data.projectId, data.productId, data.orderQty, data.actualTime, data.status);
                });
        }

        // Call this function for each timer on the page
        document.querySelectorAll('.start-timer').forEach(button => {
            const uniqueId = button.dataset.uniqueId;
            const projectId = button.dataset.projectId;
            const productId = button.dataset.productId;
            const orderQty = button.dataset.seqQty;
            listenToTimerEvents(uniqueId, projectId, productId, orderQty);
        });

        if (typeof projectId !== 'undefined' &&
            typeof productId !== 'undefined' &&
            typeof orderQty !== 'undefined') {

            fetchTimerState(projectId, productId, orderQty);
        }
        // startPeriodicStateCheck();
    });
</script>

<script>
    $(document).ready(function() {
        $('.reset-timer').on('click', function() {
            var projectId = $(this).data('project-id');
            var productId = $(this).data('product-id');
            var seqQty = $(this).data('seq-qty');
            var projectProcessName = $(this).data('project-process-name');
            var projectTypeName = $(this).data('project-type-name');
            // Add other data attributes as needed

            $.ajax({
                url: '{{ route("OperatorresetTimer") }}', // Use the named route
                type: 'POST',
                data: {
                    project_id: projectId,
                    product_id: productId,
                    seqQty: seqQty,
                    projectProcessName: projectProcessName,
                    projectTypeName: projectTypeName,
                    _token: '{{ csrf_token() }}' // Include CSRF token for security
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        window.location.reload();
                        // Optionally, update the UI or table row here
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr) {
                    alert('An error occurred: ' + xhr.responseText);
                }
            });
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.16.0/dist/pdf-lib.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pdf-lib/dist/pdf-lib.min.js"></script>

<script>
    let canvasInstances = [];
    let currentPage = 1;
    let totalPages = 0;
    let loadedPDF = null;

    $('#pdfEditorModal').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget);
        const pdfUrl = button.data('pdf-url');
        renderPDFToEditableCanvases(pdfUrl);
    });

    function renderPDFToEditableCanvases(pdfUrl) {
        const container = document.getElementById('pdfImageContainer');
        container.innerHTML = '';
        canvasInstances = [];
        currentPage = 1;

        const loadingTask = pdfjsLib.getDocument(pdfUrl);
        loadingTask.promise.then(function(pdf) {
            loadedPDF = pdf;
            totalPages = pdf.numPages;
            renderPage(pdf, currentPage);
        }).catch(function(error) {
            console.error('Error loading PDF:', error);
            container.innerHTML = '<p class="text-danger">Failed to load PDF.</p>';
        });
    }

    function renderPage(pdf, pageNum) {
        const container = document.getElementById('pdfImageContainer');
        container.innerHTML = '';

        pdf.getPage(pageNum).then(function(page) {
            const containerWidth = container.clientWidth || 800;
            const scale = containerWidth / page.getViewport({
                scale: 1
            }).width;
            const viewport = page.getViewport({
                scale: scale
            });

            const canvasElement = document.createElement('canvas');
            canvasElement.width = viewport.width;
            canvasElement.height = viewport.height;
            canvasElement.id = 'pdf-canvas-' + pageNum;

            container.appendChild(canvasElement);

            const context = canvasElement.getContext('2d');
            const renderContext = {
                canvasContext: context,
                viewport: viewport
            };

            page.render(renderContext).promise.then(function() {
                const imgUrl = canvasElement.toDataURL('image/png');
                initFabricCanvas(canvasElement, imgUrl, viewport.width, viewport.height, pageNum);
                updatePageNumberDisplay();
            });
        }).catch(function(error) {
            console.error(`Error rendering page ${pageNum}:`, error);
        });
    }

    function saveCurrentPageState() {
        const canvas = canvasInstances[currentPage];
        if (canvas) {
            // Save canvas state as JSON instead of just image
            editedPages[currentPage] = canvas.toJSON();
            console.log(`Page ${currentPage} state saved.`);
        }
    }


    function initFabricCanvas(canvasElement, imgUrl, width, height, pageNum) {
        let fabricCanvas = new fabric.Canvas(canvasElement, {
            selection: true
        });

        fabric.Image.fromURL(imgUrl, function(img) {
            fabricCanvas.setBackgroundImage(img, fabricCanvas.renderAll.bind(fabricCanvas), {
                scaleX: 1,
                scaleY: 1,
                originX: 'left',
                originY: 'top'
            });

            // Load saved state (if exists)
            if (editedPages[pageNum]) {
                fabricCanvas.loadFromJSON(editedPages[pageNum], function() {
                    fabricCanvas.renderAll();
                    console.log(`Restored edits for page ${pageNum}`);
                });
            }
        });

        canvasInstances[pageNum] = fabricCanvas;
        fabric.Object.prototype.transparentCorners = false;
    }


    function changePage(delta) {
        saveCurrentPageState(); // Save current page state

        const newPage = currentPage + delta;
        if (newPage >= 1 && newPage <= totalPages) {
            currentPage = newPage;
            renderPage(loadedPDF, currentPage); // Render new page
        }
    }

    function addText() {
        const canvas = canvasInstances[currentPage];
        const inputText = prompt('Enter your text:');

        if (inputText !== null && inputText.trim() !== '') {
            const textBox = new fabric.Textbox(inputText, {
                left: 50,
                top: 50,
                fontSize: 20,
                fill: 'blue',
                width: 300, // Set a fixed width to allow wrapping
                borderColor: 'red',
                editingBorderColor: 'red'
            });

            canvas.add(textBox);
            canvas.renderAll();
        } else {
            alert('Please enter valid text!');
        }
    }


    function chooseShape() {
        const canvas = canvasInstances[currentPage];
        if (!canvas) return;

        const shape = prompt("Enter the shape you want to add (rectangle, circle, line, triangle):");

        switch (shape?.toLowerCase()) {
            case 'rectangle':
                addRectangle(canvas);
                break;
            case 'circle':
                addCircle(canvas);
                break;
            case 'line':
                addLine(canvas);
                break;
            case 'triangle':
                addTriangle(canvas);
                break;
            default:
                alert("Invalid shape! Please choose rectangle, circle, line, or triangle.");
        }
    }

    function addRectangle(canvas) {
        const rect = new fabric.Rect({
            left: 100,
            top: 100,
            fill: 'transparent',
            stroke: 'green',
            strokeWidth: 3,
            width: 100,
            height: 100
        });
        canvas.add(rect);
    }

    function addCircle(canvas) {
        const circle = new fabric.Circle({
            left: 150,
            top: 150,
            radius: 50,
            fill: 'transparent',
            stroke: 'blue',
            strokeWidth: 3
        });
        canvas.add(circle);
    }

    function addLine(canvas) {
        const line = new fabric.Line([50, 50, 200, 50], {
            stroke: 'red',
            strokeWidth: 3
        });
        canvas.add(line);
    }

    function addTriangle(canvas) {
        const triangle = new fabric.Triangle({
            left: 200,
            top: 200,
            width: 100,
            height: 100,
            fill: 'transparent',
            stroke: 'purple',
            strokeWidth: 3
        });
        canvas.add(triangle);
    }


    function clearCanvas() {
        const canvas = canvasInstances[currentPage];
        canvas.getObjects().forEach((obj) => {
            if (obj !== canvas.backgroundImage) canvas.remove(obj);
        });
        canvas.renderAll();
    }

    const editedPages = {};

    function saveCurrentPageState() {
        const canvas = canvasInstances[currentPage];
        if (canvas) {
            editedPages[currentPage] = canvas.toJSON();
            console.log(`Page ${currentPage} state saved.`);
        }
    }


    // Save the entire PDF with all edits
    async function saveEditedPDF() {
        saveCurrentPageState(); // Save current page edits

        if (!window.PDFLib) {
            alert("PDFLib is not loaded. Please check the script tag.");
            return;
        }

        const {
            PDFDocument
        } = PDFLib;
        const pdfDoc = await PDFDocument.create();

        try {
            // for (let pageNum = 1; pageNum <= totalPages; pageNum++) {
            //     if (editedPages[pageNum]) {
            //         console.log(`Rendering edited page ${pageNum}`);

            //         const editedImage = canvasInstances[pageNum].toDataURL('image/png');
            //         const img = await pdfDoc.embedPng(editedImage);

            //         const page = pdfDoc.addPage([canvasInstances[pageNum].width, canvasInstances[pageNum].height]);
            //         page.drawImage(img, {
            //             x: 0,
            //             y: 0,
            //             // width: canvasInstances[pageNum].width,
            //             // height: canvasInstances[pageNum].height,
            //         });
            //     } else {
            //         console.warn(`No edits found for page ${pageNum}, adding original page.`);

            //         const pdfPage = await loadedPDF.getPage(pageNum);
            //         const viewport = pdfPage.getViewport({
            //             scale: 1
            //         });

            //         const tempCanvas = document.createElement('canvas');
            //         tempCanvas.width = viewport.width;
            //         tempCanvas.height = viewport.height;

            //         const context = tempCanvas.getContext('2d');
            //         await pdfPage.render({
            //             canvasContext: context,
            //             viewport
            //         }).promise;

            //         const originalImage = tempCanvas.toDataURL('image/png');
            //         const img = await pdfDoc.embedPng(originalImage);
            //         const page = pdfDoc.addPage([viewport.width, viewport.height]);

            //         page.drawImage(img, {
            //             x: 0,
            //             y: 0,
            //             width: viewport.width,
            //             height: viewport.height,
            //         });
            //     }
            // }


            for (let pageNum = 1; pageNum <= totalPages; pageNum++) {
                if (editedPages[pageNum]) {
                    console.log(`Rendering edited page ${pageNum}`);

                    // Get original page size
                    const originalPage = await loadedPDF.getPage(pageNum);
                    const viewport = originalPage.getViewport({ scale: 1 });
                    const pageWidth = viewport.width;
                    const pageHeight = viewport.height;

                    // Get canvas image and scale to match original size
                    const editedCanvas = canvasInstances[pageNum];
                    const editedImage = editedCanvas.toDataURL('image/png');
                    const img = await pdfDoc.embedPng(editedImage);

                    const page = pdfDoc.addPage([pageWidth, pageHeight]);

                    const scaleX = pageWidth / editedCanvas.width;
                    const scaleY = pageHeight / editedCanvas.height;

                    page.drawImage(img, {
                        x: 0,
                        y: 0,
                        width: editedCanvas.width * scaleX,
                        height: editedCanvas.height * scaleY,
                    });
                } else {
                    console.warn(`No edits found for page ${pageNum}, adding original page.`);

                    const pdfPage = await loadedPDF.getPage(pageNum);
                    const viewport = pdfPage.getViewport({ scale: 1 });

                    const tempCanvas = document.createElement('canvas');
                    tempCanvas.width = viewport.width;
                    tempCanvas.height = viewport.height;

                    const context = tempCanvas.getContext('2d');
                    await pdfPage.render({
                        canvasContext: context,
                        viewport
                    }).promise;

                    const originalImage = tempCanvas.toDataURL('image/png');
                    const img = await pdfDoc.embedPng(originalImage);
                    const page = pdfDoc.addPage([viewport.width, viewport.height]);

                    page.drawImage(img, {
                        x: 0,
                        y: 0,
                        width: viewport.width,
                        height: viewport.height,
                    });
                }
            }

            const pdfBytes = await pdfDoc.save();
            const pdfBlob = new Blob([pdfBytes], {
                type: 'application/pdf'
            });

            const formData = new FormData();
            formData.append("pdfFile", pdfBlob, "edited_document.pdf");
            formData.append("product_id", productId);

            // fetch("/operator/save-edited-pdf", {
            //         method: "POST",
            //         headers: {
            //             'X-CSRF-TOKEN': '{{ csrf_token() }}' 
            //         },
            //         body: formData, 
            //     })
            //     .then(response => response.json())
            //     .then(data => {
            //         console.log("PDF uploaded successfully:", data);
            //     })
            //     .catch(error => {
            //         console.error("Error uploading PDF:", error);
            //     });

            fetch("/operator/save-edited-pdf", {
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show a success message if needed
                        alert(data.message);

                        // Redirect to same page to reload it (or to a specific page if needed)
                        window.location.reload(); // or use: window.location.href = window.location.href;
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error("Error uploading PDF:", error);
                    alert("An unexpected error occurred while uploading.");
                });


            console.log("PDF upload triggered!");
        } catch (error) {
            console.error("Error saving PDF:", error);
        }
    }


    function deleteSelectedObject() {
        const canvas = canvasInstances[currentPage];
        if (canvas) {
            const activeObject = canvas.getActiveObject();
            if (activeObject) {
                canvas.remove(activeObject);
                canvas.renderAll();
            } else {
                alert('Please select an object to delete!');
            }
        }
    }

    function editSelectedText() {
        const canvas = canvasInstances[currentPage];
        if (canvas) {
            const activeObject = canvas.getActiveObject();

            if (activeObject && (activeObject.type === 'textbox' || activeObject.type === 'i-text')) {
                const newText = prompt('Edit your text:', activeObject.text);

                if (newText !== null) {
                    activeObject.text = newText;
                    canvas.renderAll();
                    saveCurrentPageState();
                }
            } else {
                alert('Please select a text object to edit!');
            }
        }
    }

    function updatePageNumberDisplay() {
        document.getElementById('pageNumberDisplay').innerText = `Page ${currentPage} of ${totalPages}`;
    }
</script>

<!-- Cmera Input Script -->
<script>
    function openCamera(uniqueId, projectNo, projectId, productId, seqQty, productType, projectProcessName) {
        window.cameraData = {
            uniqueId: uniqueId,
            projectNo: projectNo,
            projectId: projectId,
            productId: productId,
            seqQty: seqQty,
            productType: productType,
            projectProcessName: projectProcessName // Add process name to cameraData
        };
        document.getElementById('cameraInput').click();
    }

    document.getElementById('cameraInput').addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            const file = e.target.files[0];
            const reader = new FileReader();

            reader.onload = function(event) {
                const img = new Image();
                img.src = event.target.result;

                const previewContainer = document.createElement('div');
                previewContainer.style.cssText = 'position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.3); z-index: 1000;';

                const previewImg = document.createElement('img');
                previewImg.src = event.target.result;
                previewImg.style.maxWidth = '300px';
                previewImg.style.maxHeight = '300px';

                const buttonContainer = document.createElement('div');
                buttonContainer.style.marginTop = '10px';
                buttonContainer.style.textAlign = 'center';

                const okButton = document.createElement('button');
                okButton.textContent = 'OK';
                okButton.style.marginRight = '10px';
                okButton.className = 'btn btn-success';

                const retryButton = document.createElement('button');
                retryButton.textContent = 'Retry';
                retryButton.className = 'btn btn-danger';

                buttonContainer.appendChild(okButton);
                buttonContainer.appendChild(retryButton);
                previewContainer.appendChild(previewImg);
                previewContainer.appendChild(buttonContainer);

                document.body.appendChild(previewContainer);

                okButton.onclick = function() {
                    const originalText = okButton.textContent;
                    okButton.disabled = true;
                    okButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';

                    const formData = new FormData();
                    formData.append('photo', file);
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('project_no', window.cameraData.projectNo);
                    formData.append('project_id', window.cameraData.projectId);
                    formData.append('product_id', window.cameraData.productId);
                    formData.append('seq_qty', window.cameraData.seqQty);
                    formData.append('product_type', window.cameraData.productType);
                    formData.append('project_process_name', window.cameraData.projectProcessName); // Add process name

                    fetch("{{ route('save.captured.photo') }}", {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            okButton.disabled = false;
                            okButton.textContent = originalText;

                            if (data.success) {
                                alert('Photo saved successfully!');
                                // Update photo availability for this process
                                const uniqueId = window.cameraData.uniqueId;
                                photoAvailability[uniqueId] = true;
                                const infoButton = document.querySelector(`.photo-info-btn[data-unique-id="${uniqueId}"]`);
                                if (infoButton) {
                                    infoButton.style.display = 'inline-block';
                                }
                                location.reload();
                            } else {
                                alert('Error: ' + data.message);
                            }
                            document.body.removeChild(previewContainer);
                        })
                        .catch(error => {
                            okButton.disabled = false;
                            okButton.textContent = originalText;

                            alert('Error saving photo: ' + error);
                            document.body.removeChild(previewContainer);
                        });
                };

                retryButton.onclick = function() {
                    document.body.removeChild(previewContainer);
                    document.getElementById('cameraInput').value = '';
                    openCamera(window.cameraData.uniqueId, window.cameraData.projectNo,
                        window.cameraData.projectId, window.cameraData.productId,
                        window.cameraData.seqQty, window.cameraData.productType,
                        window.cameraData.projectProcessName); // Pass process name
                };
            };

            reader.readAsDataURL(file);
        }
    });
</script>

<script>
    function openPhotosModal(uniqueId, projectNo, productId, seqQty, productType, projectProcessName) {
        // Update modal title
        document.getElementById('photosModalLabel').textContent = `Photos for Process: ${projectProcessName}`;

        // Clear previous photos
        const photosContainer = document.getElementById('photosContainer');
        photosContainer.innerHTML = '';
        const noPhotosMessage = document.getElementById('noPhotosMessage');
        noPhotosMessage.style.display = 'none';

        // Fetch photos from the server
        fetch("{{ route('get.process.photos') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    project_no: projectNo,
                    product_id: productId,
                    seq_qty: seqQty,
                    product_type: productType,
                    project_process_name: projectProcessName
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.photos.length > 0) {
                    data.photos.forEach(photo => {
                        const col = document.createElement('div');
                        col.className = 'col-md-3 col-sm-6 mb-3 text-center'; // Adjusted for better responsiveness

                        const img = document.createElement('img');
                        img.src = photo.url;
                        img.className = 'img-fluid';
                        img.style.maxHeight = '100px'; // Reduced from 200px to 100px
                        img.style.width = 'auto'; // Ensure width adjusts proportionally
                        img.style.objectFit = 'cover';
                        img.style.cursor = 'pointer';
                        img.style.borderRadius = '5px'; // Optional: Add a slight border radius for better aesthetics
                        img.onclick = () => {
                            window.open(photo.url, '_blank');
                        };

                        const filename = document.createElement('p');
                        filename.textContent = photo.filename;
                        filename.className = 'text-center mt-1'; // Reduced margin-top
                        filename.style.fontSize = '12px'; // Smaller font size for the filename
                        filename.style.wordBreak = 'break-all'; // Ensure long filenames wrap properly
                        filename.style.display = 'none'; // Hide the element

                        col.appendChild(img);
                        col.appendChild(filename);
                        photosContainer.appendChild(col);
                    });
                } else {
                    noPhotosMessage.style.display = 'block';
                }

                // Show the modal
                $('#photosModal').modal('show');
            })
            .catch(error => {
                console.error('Error fetching photos:', error);
                noPhotosMessage.style.display = 'block';
                $('#photosModal').modal('show');
            });
    }
</script>

<script>
    $('#rejectReasonModal').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget);
        const role = button.data('role');
        const reason = button.data('reason');

        $('#rejectedByRole').text(role);
        $('#rejectionReasonText').text(reason || 'No reason provided.');
    });
</script>

<!-- SheetJS library -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/papaparse@5.4.1/papaparse.min.js"></script>
<script>
function downloadFilteredCSV() {
    const csvUrl = "{{ asset($projects->bom_path) }}";

    fetch(csvUrl)
        .then(response => response.text())
        .then(csvText => {
            const parsed = Papa.parse(csvText.trim(), { skipEmptyLines: true });
            const rows = parsed.data;

            // Find header row index that starts with "Item Description"
            const startIndex = rows.findIndex(r => r.includes("Item Description"));
            if (startIndex === -1) {
                console.error("Could not find 'Item Description' header");
                return;
            }

            // Get indices of columns to remove
            const headerRow = rows[startIndex];
            const removeCols = [
                headerRow.indexOf("Unit Price"),
                headerRow.indexOf("Adder code"),
                headerRow.indexOf("Total Price")
            ].filter(idx => idx !== -1); // filter out not found

            const filteredRows = rows.map((row, idx) => {
                if (idx >= startIndex) {
                    // Remove all unwanted columns from the row
                    return row.filter((_, i) => !removeCols.includes(i));
                }
                return row;
            });

            // Step 1: Prepare items
            const items = [];
            filteredRows.forEach((row, idx) => {
                if (idx <= startIndex) return;
                const description = row[0]?.trim() || "";
                const articleNo = row[1]?.trim() || "";
                const projectNo = "{{$projects->projects['project_no']}}";
                const productId = "{{$product_id}}";

                if (description) {
                    items.push({ description, articleNo, projectNo, productId });
                }
            });

            // Step 2: Lookup PO Numbers
            fetch("/operator/get-po-numbers", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ items })
            })
            .then(res => res.json())
            .then(poData => {
                const maxCols = Math.max(...filteredRows.map(r => r.length));
                const normalizeRow = row => {
                    const padded = [...row];
                    while (padded.length < maxCols) padded.push("");
                    return padded;
                };

                const rowsWithPo = filteredRows.map((row, idx) => {
                    const normRow = normalizeRow(row);
                    if (idx === startIndex) {
                        return [...normRow, "PO Number"];
                    }
                    if (idx < startIndex) return normRow;

                    const description = normRow[0]?.trim();
                    const articleNo = normRow[1]?.trim();
                    const projectNo = "{{$projects->projects['project_no']}}";
                    const productId = "{{$product_id}}";

                    const po = poData.find(p =>
                        p.description === description &&
                        p.articleNo === articleNo &&
                        p.projectNo === projectNo &&
                        p.productId === productId
                    )?.po || "";

                    return [...normRow, po];
                });

                // Export as Excel
                const ws = XLSX.utils.aoa_to_sheet(rowsWithPo);
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, "BOM");

                const csvFileName = csvUrl.split('/').pop().split('?')[0];
                const baseName = csvFileName.replace(/\.[^.]+$/, '');
                XLSX.writeFile(wb, `${baseName}.xlsx`);
            });
        })
        .catch(err => console.error("Failed to load or process CSV", err));
}
</script>
@endsection