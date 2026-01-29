@extends('layouts.main')
@section('content')

<link rel="stylesheet" href="{{ asset('css/operator.css') }}" />
<div class="product_type_timer_page main_section bg-white mx-0 mx-md-4 my-4 dashboard_heading px-0 px-md-4 my-4">   

    <div class="px-4 pt-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center">

            <!-- BACK BUTTON -->
            <a href="{{ route('OperatorProductType',['product_id'=>$product_id,'redirect' => '1']) }}"
                class="project_icon back-btn p-2 m-1 text-decoration-none d-flex align-items-center">
                <i class="fa fa-arrow-left project_icon me-2"></i><span class="text-white">BACK</span>
            </a>

            <!-- RIGHT ACTION BUTTONS -->
            <div class="d-flex flex-wrap justify-content-end gap-2 right-action-btns">

                {{-- BOM SECTION --}}
                @if($projects->bom_path != null)

                    @if(Auth::user()->role == "Wilo Operator")
                        <a href="#" onclick="downloadFilteredCSV()" class="project_icon p-2 m-1 text-decoration-none">
                            <i class="fa fa-download project_icon me-2"></i><span class="text-white">BOM</span>
                        </a>
                    @else
                        <a href="{{ asset($projects->bom_path) }}" class="project_icon p-2 m-1 text-decoration-none" download>
                            <i class="fa fa-download project_icon me-2"></i><span class="text-white"> BOM </span>
                        </a>
                    @endif

                @else
                    <a href="#" class="project_icon p-2 m-1 text-decoration-none">
                        <i class="fa fa-tags me-2"></i><span class="text-white">Requested BOM </span>
                    </a>
                @endif

                {{-- DRAWING REQUEST STATUS --}}
                @if($projects->drawing_req_estimation_manager == 0)
                    <a href="#" class="project_icon p-2 m-1 text-decoration-none"
                        title="No Drawing request from the Production Engineer while creating a project.">
                        <i class="fa fa-tags project_icon me-2"></i>
                        <span class="text-white">No Drawing request from the Production Engineer</span>
                    </a>

                @elseif($projects->drawing_req_estimation_manager == 1)
                    <a href="#" class="project_icon p-2 m-1 text-decoration-none"
                        title="Didn't get any Drawing Request while creating a project.">
                        <i class="fa fa-tags project_icon me-2"></i>
                        <span class="text-white"> Basic Drawings are awaited from the Estimation Manager</span>
                    </a>

                @else
                    <a href="{{ asset($projects->drawing_path) }}"
                        class="project_icon p-2 m-1 text-decoration-none" download>
                        <i class="fa fa-download project_icon me-2"></i>
                        <span class="text-white"> Basic Drawing </span>
                    </a>
                @endif


                {{-- AS-BUILT SECTION --}}
                @if($projects->drawing_req_estimation_manager == "2")

                    @php
                        $isRejectedByEstimation = $projects->is_asbuilt_drawing_pdf_approve_by_estimation_manager == 2;
                        $isRejectedByProduction = $projects->is_asbuilt_drawing_pdf_approve_by_production_superwisor == 2;

                        $isApprovedByEstimation = $projects->is_asbuilt_drawing_pdf_approve_by_estimation_manager == 1;
                        $isApprovedByProduction = $projects->is_asbuilt_drawing_pdf_approve_by_production_superwisor == 1;

                        $isRequestedByEstimation = $projects->is_asbuilt_drawing_pdf_approve_by_estimation_manager == 3;
                        $isRequestedByProduction = $projects->is_asbuilt_drawing_pdf_approve_by_production_superwisor == 3;
                    @endphp

                    @if($isRejectedByEstimation && $isRejectedByProduction)
                        <button type="button" class="btn btn-danger p-2 m-1" data-bs-toggle="modal"
                            data-bs-target="#rejectReasonModal"
                            data-role="Estimation Manager & Production Supervisor"
                            data-reason="{{ 'Estimation Manager: ' . $projects->asbuilt_drawing_approve_reject_remarks_by_estimation_manager . ' | Production Supervisor: ' . $projects->asbuilt_drawing_approve_reject_remarks_by_production_superwisor }}">
                            <i class="fa fa-exclamation-triangle me-2"></i>Rejected by Estimation Manager & Production Superwisor
                        </button>

                        <button type="button" class="project_icon p-2 m-1 text-decoration-none"
                            data-toggle="modal" data-target="#pdfEditorModal"
                            data-pdf-url="{{ asset($projects->editable_drawing_path) }}"
                            data-project-id="{{ $projects->project_id }}" data-product-id="{{ $projects->id }}">
                            <i class="fa fa-tags project_icon me-2"></i>Open PDF Editor
                        </button>

                    @elseif($isRejectedByEstimation)
                        <button type="button" class="btn btn-danger p-2 m-1" data-bs-toggle="modal"
                            data-bs-target="#rejectReasonModal" data-role="Estimation Manager"
                            data-reason="{{ $projects->asbuilt_drawing_approve_reject_remarks_by_estimation_manager }}">
                            <i class="fa fa-exclamation-triangle me-2"></i>Rejected by Estimation Manager
                        </button>

                        <button type="button" class="project_icon p-2 m-1 text-decoration-none"
                            data-toggle="modal" data-target="#pdfEditorModal"
                            data-pdf-url="{{ asset($projects->editable_drawing_path) }}"
                            data-project-id="{{ $projects->project_id }}" data-product-id="{{ $projects->id }}">
                            <i class="fa fa-tags project_icon me-2"></i>Open PDF Editor
                        </button>

                    @elseif($isRejectedByProduction)
                        <button type="button" class="btn btn-danger p-2 m-1" data-bs-toggle="modal"
                            data-bs-target="#rejectReasonModal" data-role="Production Supervisor"
                            data-reason="{{ $projects->asbuilt_drawing_approve_reject_remarks_by_production_superwisor }}">
                            <i class="fa fa-exclamation-triangle me-2"></i>Rejected by Production Superwisor
                        </button>

                        <button type="button" class="project_icon p-2 m-1 text-decoration-none"
                            data-toggle="modal" data-target="#pdfEditorModal"
                            data-pdf-url="{{ asset($projects->editable_drawing_path) }}"
                            data-project-id="{{ $projects->project_id }}" data-product-id="{{ $projects->id }}">
                            <i class="fa fa-tags project_icon me-2"></i>Open PDF Editor
                        </button>

                    @elseif($isApprovedByEstimation && $isApprovedByProduction)
                        <a href="{{ asset($projects->editable_drawing_path) }}"
                            class="project_icon p-2 m-1 text-decoration-none" download>
                            <i class="fa fa-download project_icon me-2"></i><span class="text-white">Operators As-Built Drawing</span>
                        </a>

                    @elseif($isApprovedByEstimation && !$isApprovedByProduction)
                        <!-- <button type="button" class="btn btn-info" disabled>
                            <span class="text-white"> As-Built PDF approval is awaited from Production Superwisor</span>
                        </button> -->
                        
                        <!-- A Code: 15-12-2025 Start -->
                        <button type="button" class="btn btn-secondary p-2 m-1" disabled>
                            <i class="fa fa-exclamation-circle project_icon me-2"></i>
                            <span class="text-white">As-Built PDF approval is awaited from Production Superwisor</span>
                        </button>
                        <!-- A Code: 15-12-2025 End -->

                    @elseif(!$isApprovedByEstimation && $isApprovedByProduction)
                        <!-- <button type="button" class="btn btn-info" disabled>
                            <span class="text-white"> As-Built PDF approval is awaited from Estimation Manager</span>
                        </button> -->

                        <!-- A Code: 15-12-2025 Start -->
                        <button type="button" class="btn btn-secondary p-2 m-1" disabled>
                            <i class="fa fa-exclamation-circle project_icon me-2"></i>
                            <span class="text-white">As-Built PDF approval is awaited from Estimation Manager</span>
                        </button>
                        <!-- A Code: 15-12-2025 End -->

                    @elseif($isRequestedByEstimation && $isRequestedByProduction)
                        <button type="button" class="btn btn-secondary p-2 m-1" disabled>
                            <i class="fa fa-exclamation-circle project_icon me-2"></i>
                            <span class="text-white"> As-built Drawing Request Sent to Estimation Manager & Production Superwisor</span>
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
                        <button type="button" class="project_icon p-2 m-1 text-decoration-none"
                            data-toggle="modal" data-target="#pdfEditorModal"
                            data-pdf-url="{{ asset($projects->drawing_path) }}"
                            data-project-id="{{ $projects->project_id }}" data-product-id="{{ $projects->id }}">
                            <i class="fa fa-tags project_icon me-2"></i> Open PDF Editor
                        </button>

                    @endif
                @endif


                {{-- FINAL DRAWING SECTION --}}
                @if($projects->editable_drawing_path)

                    @if($projects->drawing_upload_by_estimation_manager)
                        <a href="{{ asset($projects->drawing_upload_by_estimation_manager) }}"
                            class="project_icon p-2 m-1 text-decoration-none" download>
                            <i class="fa fa-download project_icon me-2"></i>
                            <span class="text-white">Final Drawing </span>
                        </a>
                    @else
                        @if($isRequestedByEstimation && $isRequestedByProduction)
                            <button type="button" class="btn btn-secondary p-2 m-1" disabled
                                title="Estimation Manager has not uploaded the final drawing.">
                                <i class="fa fa-exclamation-circle project_icon me-2"></i>
                                <span class="text-white">Estimation Manager will upload Final Drawing after approval</span>
                            </button>
                        @else
                            <button type="button" class="btn btn-secondary p-2 m-1" disabled
                                title="Estimation Manager has not uploaded the final drawing.">
                                <i class="fa fa-exclamation-circle project_icon me-2"></i>
                                <span class="text-white">Final Drawing is awaited from Estimation Manager.</span>
                            </button>
                        @endif
                    @endif

                @endif

            </div>
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
                <div class="col-md-3 col-lg-2 text-bold">Project No. :</div>
                <div class="col-md-9 col-lg-10 text-dark">
                    {{ $projects->projects['project_no'] }}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-3 col-lg-2 text-bold">Project Name :</div>
                <div class="col-md-9 col-lg-10 text-dark">
                    {{ $projects->projects['project_name'] }}
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 col-lg-2 text-bold">Estimated Date :</div>
                <div class="col-md-9 col-lg-10 text-dark">
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
                        <th scope="col" class="project_table_heading">Operator Wise Time</th>
                        <th scope="col" class="project_table_heading">Operator Wise Status</th>
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

                            <i class="fa fa-eye project_icon p-2 m-1 photo-info-btn"
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
                                    data-unique-id="{{ $val->projects_id }}-{{ $val->product_id }}-{{ $val->order_qty }}-{{ $operatorId }}-{{ $key }}"
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
                                    data-unique-id="{{ $val->projects_id }}-{{ $val->product_id }}-{{ $val->order_qty }}-{{ $operatorId }}-{{ $key }}"
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

                        <td class="w-15 actual-time"
                            data-unique-id="{{ $uniqueId }}"
                            data-project-id="{{ $val->projects_id }}"
                            data-product-id="{{ $val->product_id }}"
                            data-seq-qty="{{ $val->order_qty }}">
                            <span class="project_icon" id = "timer-{{ $uniqueId }}">
                                {{$val->project_actual_time == "00:00:00" ? "" : $val->project_actual_time }}
                            </span>
                        </td>

                        @php
                        $opId = (int) ($operatorId ?? 0);
                        $opStatus = $val->latestStatusForOperator($opId) ?? 'Pending';
                        @endphp

                        <td class="w-15">
                            <span class="badge p-2 status"
                                data-unique-id="{{ $rowUnique }}"
                                data-project-id="{{ $val->projects_id }}"
                                data-product-id="{{ $val->product_id }}"
                                data-seq-qty="{{ $val->order_qty }}">
                                <span class="badge badge-danger p-2" id = "opertor_wise_status-{{ $uniqueId }}">Pending</span>
                            </span>
                        </td>
                        
                        @if($opIndex === 0)
                       
                        @endif
                    </tr>
                    @endforeach
                    @php $previous_val = $val; @endphp
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- PDF Editor Modal -->
    <div class="modal fade" id="pdfEditorModal" tabindex="-1" role="dialog" aria-labelledby="pdfEditorModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-light" id="pdfEditorModalLabel">PDF Editor</h5>
                    <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    
                    <div class="mb-3 d-flex justify-content-between pdf-toolbar flex-wrap">
                        <div>
                            <button class="btn btn-success" onclick="addText()">🅰️ Add Text</button>
                            <button class="btn btn-warning" onclick="chooseShape()" title="Available Shapes: Rectangle, Circle, Line, Triangle">✏️ Add Shape</button>
                            <button class="btn btn-info" onclick="clearCanvas()">🗑 Clear</button>
                            <button onclick="deleteSelectedObject()" class="btn btn-danger">Delete Selected Text or Shape</button>
                            <button onclick="editSelectedText()" class="btn btn-primary">Edit Text</button>
                            <br><br>
                            <button class="btn btn-danger" onclick="saveEditedPDF()">📄 Save PDF</button>
                        </div>
                        <div class="page-nav d-flex align-items-center mt-2">
                            <button class="btn btn-secondary" onclick="changePage(-1)">⬅ Previous</button>
                            <span id="pageNumberDisplay" class="mx-2"></span>
                            <button class="btn btn-secondary" onclick="changePage(1)">Next ➡</button>
                        </div>
                    </div>

                    <div class="pdfImageContainer" id="pdfImageContainer"></div>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
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
    const projectId = @json($projects['projects'] -> id);
    const productId = @json($projects -> id);
    const orderQty = @json($seq_qty);
    const processName = @json($active_process_name);

    window.projectId = @json($projects['projects'] -> id);
    window.productId = @json($projects -> id);
    window.orderQty = @json($seq_qty);
    window.processName = @json($active_process_name);
    window.currentOperatorId = null; // Store active operatorId
    window.currentUniqueId = null; // Store active uniqueId
</script>

<script>
let timerIntervals = {};
let actualTimes = {};
let remainingTimes = {};
let startTimes = {};
let totalTimes = {};
let photoAvailability = {};
let periodicCheckInterval = null;
let serverTimeDrift = 0;

function startTimer(uniqueId, projectId, productId, orderQty, remainingTime, elapsedTime = 0) {    
    
    const actualTimeElement = document.querySelector(`.actual-time[data-unique-id="${uniqueId}"]`);
    if (!actualTimeElement) {       
        return;
    }
    
    // Clear existing interval
    if (timerIntervals[uniqueId]) {
        clearInterval(timerIntervals[uniqueId]);
    }
    
    // Initialize times with server values
    startTimes[uniqueId] = Date.now() - (elapsedTime * 1000);
    remainingTimes[uniqueId] = remainingTime;
    actualTimes[uniqueId] = elapsedTime;

    // Update immediately
    actualTimeElement.textContent = formatTime(elapsedTime);

    timerIntervals[uniqueId] = setInterval(() => {
        const currentElapsed = Math.floor((Date.now() - startTimes[uniqueId]) / 1000);
        const currentRemaining = Math.max(0, remainingTime - currentElapsed);
        
        // Update actual time display
        actualTimeElement.textContent = formatTime(currentElapsed);
        
        // Store current values
        actualTimes[uniqueId] = currentElapsed;
        remainingTimes[uniqueId] = currentRemaining;
        
        if (currentRemaining <= 0) {           
            clearInterval(timerIntervals[uniqueId]);
            delete timerIntervals[uniqueId];
            autoStopTimer(uniqueId, projectId, productId, orderQty, currentElapsed);
        }
    }, 1000);
}

function pauseTimer(uniqueId) {   
    
    // Clear any running interval for this timer
    if (timerIntervals[uniqueId]) {
        clearInterval(timerIntervals[uniqueId]);
        delete timerIntervals[uniqueId];
    }

    // Update UI elements
    const startButton = document.querySelector(`.start-timer[data-unique-id="${uniqueId}"]`);
    const pauseButton = document.querySelector(`.pause-timer[data-unique-id="${uniqueId}"]`);
    const stopButton = document.querySelector(`.stop-timer[data-unique-id="${uniqueId}"]`);

    if (startButton) startButton.classList.remove('disabled-btn');
    if (pauseButton) pauseButton.classList.add('d-none');
    if (stopButton) stopButton.classList.remove('d-none');    
}

function stopTimer(uniqueId, operatorId, processId, projectId, productId, orderQty) {
    if (activeTimers[uniqueId]) {
        clearInterval(activeTimers[uniqueId].interval);
        delete activeTimers[uniqueId];
    }

    const actualTimeElement = document.querySelector(`.actual-time[data-unique-id="${uniqueId}"]`);
    const actualTime = actualTimes[uniqueId] || 0;

    if (actualTimeElement) {
        // Show final formatted time
        actualTimeElement.textContent = formatTime(actualTime);
    }

    // Save to backend
    fetch(updateStatusUrl, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken
        },
        body: JSON.stringify({
            operator_id: operatorId,
            process_id: processId,
            project_id: projectId,
            product_id: productId,
            order_qty: orderQty,
            status: "stopped",
            elapsed_time: actualTime
        })
    })
    .then(res => res.json())
    .then(data => console.log("Stopped & saved:", data))
    .catch(err => console.error("Stop error:", err));
}

    function updateTimerDisplay(uniqueId) {
        const actualTimeElement = document.querySelector(`.actual-time[data-unique-id="${uniqueId}"]`)    

        if (actualTimeElement && actualTimes[uniqueId] !== undefined) {
            actualTimeElement.textContent = formatTime(actualTimes[uniqueId])
        }
    }

function autoStopTimer(uniqueId, projectId, productId, orderQty, actualTime) { 
    
    const stopButton = document.querySelector(`.stop-timer[data-unique-id="${uniqueId}"]`);
    if (!stopButton) return;
    
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
        fetchTimerState(projectId, productId, orderQty, processName);
    })
    .catch(error => {
        console.error("Error in auto-stop:", error);
    });
}

    function sprintf(format, ...args) {
        return format.replace(/%(\d+)?d/g, (match, width) => {
            const num = args.shift()
            return width ? num.toString().padStart(width, "0") : num
        })
    }

    function convertHoursToSeconds(hours) {
        return Math.floor(hours * 3600); // Convert hours to seconds
    }

    startPeriodicStateCheck();

function startPeriodicStateCheck() {
    // Clear any existing interval to prevent duplicates
    if (periodicCheckInterval) {
        clearInterval(periodicCheckInterval);
    }

    periodicCheckInterval = setInterval(() => {
        if (typeof projectId !== 'undefined' &&
            typeof productId !== 'undefined' &&
            typeof orderQty !== 'undefined') {
            fetchTimerState(projectId, productId, orderQty, processName);
        }
    }, 3000); // Check every 3 seconds
}

function fetchTimerState(projectId, productId, orderQty) {
    fetch('/get-timer-state', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            projectId: projectId,
            productId: productId,
            orderQty: orderQty
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.timer_states && data.timer_states.length > 0) {
            data.timer_states.forEach(timerState => {
                updateTimerUI(timerState);
                updateButtonStates(timerState); // <-- Add this line
            });
        }
    })
    .catch(error => {
        console.error('Error fetching timer state:', error);
    });
}

function updateButtonStates(timerState) {  
    const uniqueId = timerState.unique_id;
    const operatorId = timerState.operator_id;

    // Find buttons using the correct class names from your HTML
    const startBtn = document.querySelector(`.start-timer[data-unique-id="${uniqueId}"]`);
    const pauseBtn = document.querySelector(`.pause-timer[data-unique-id="${uniqueId}"]`);
    const stopBtn = document.querySelector(`.stop-timer[data-unique-id="${uniqueId}"]`);
    
    if (startBtn) {
        if (timerState.show_start_btn) {
            startBtn.classList.remove('d-none');
            startBtn.classList.remove('disabled-btn');
        } else {
            startBtn.classList.add('d-none');
        }
    }
    
    if (pauseBtn) {
        if (timerState.show_pause_btn) {
            pauseBtn.classList.remove('d-none');
        } else {
            pauseBtn.classList.add('d-none');
        }
    }
    
    if (stopBtn) {
        if (timerState.show_stop_btn) {
            stopBtn.classList.remove('d-none');
        } else {
            stopBtn.classList.add('d-none');
        }
    }

    if (stopBtn) {
        if (timerState.hide_all_btns) {
            startBtn.classList.add('d-none');
            pauseBtn.classList.add('d-none');
            stopBtn.classList.add('d-none');
        } 
    } 
}

const activeTimers = {};

function updateTimerUI(timerState) {
    const uniqueId = timerState.unique_id;
    const status = timerState.status;
    const elapsed = timerState.elapsed_time;
    const operatorId = timerState.operator_id;

    // Find the timer element using the exact unique_id
    const timerElement = document.querySelector(`#timer-${uniqueId}`);
    const OpertorWiseStatus = document.querySelector(`#opertor_wise_status-${uniqueId}`);
    
    if (!timerElement) {
        console.warn(`Timer element not found for uniqueId: ${uniqueId}`);
        return;
    }

    // Clear any existing interval for this specific timer
    if (activeTimers[uniqueId]) {
        clearInterval(activeTimers[uniqueId]);
        delete activeTimers[uniqueId];
    }

    // Set initial time from server
    let seconds = elapsed;
    timerElement.textContent = formatTime(seconds);
    
    // Only start interval if this operator's timer is running
    if (status === 'running') {
        OpertorWiseStatus.textContent = 'Running';
        OpertorWiseStatus.className = 'badge badge-warning p-2';      
        activeTimers[uniqueId] = setInterval(() => {
            seconds += 1;
            timerElement.textContent = formatTime(seconds);
        }, 1000);
    }else if (status === 'paused') {
        OpertorWiseStatus.textContent = 'Paused';
        OpertorWiseStatus.className = 'badge badge-info p-2';
    }else if (status === 'stopped') {
        OpertorWiseStatus.textContent = 'Completed';
        OpertorWiseStatus.className = 'badge badge-success p-2';
    }else {     

    }
}

function formatTime(totalSeconds) {
    const h = Math.floor(totalSeconds / 3600).toString().padStart(2, '0');
    const m = Math.floor((totalSeconds % 3600) / 60).toString().padStart(2, '0');
    const s = Math.floor(totalSeconds % 60).toString().padStart(2, '0');
    return `${h}:${m}:${s}`;
}

// Temporary debug function - call this in browser console
async function debugDatabaseState() {
    try {
        const response = await fetch("/get-timer-state", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                projectId: 3,
                productId: 3,
                orderQty: 1
            }),
        });
        const data = await response.json();
        
        // Check each timer state
        Object.entries(data.timer_states).forEach(([uniqueId, state]) => {
            console.log(`🔍 ${uniqueId}:`, {
                operatorStatus: state.status,
                processStatus: state.process_status,
                elapsedTime: state.elapsed_time,
                operatorId: state.operatorId
            });
        });
        
        return data;
    } catch (error) {
        console.error("DEBUG Error:", error);
    }
}

window.debugDatabaseState = debugDatabaseState;

    // Call this in browser console to check

    // Call this function when the page loads
    document.addEventListener('DOMContentLoaded', function() {
        const pusherKey = "{{ env('VITE_PUSHER_APP_KEY') }}";
        const pusherCluster = "{{ env('VITE_PUSHER_APP_CLUSTER') }}";
        window.Pusher = Pusher;

        if (window.Echo) {           
        } else {          
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

        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                const context = this;
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(context, args), wait);
            };
        }
       
        // Update the stop button event listener
    document.querySelectorAll('.start-timer').forEach(button => {
        button.addEventListener('click', function() {
            if (this.classList.contains('disabled-btn')) {                
                return;
            }
            
            const operatorId = this.dataset.operatorId;
            const uniqueId = this.dataset.uniqueId;
            const projectId = this.dataset.projectId;
            const productId = this.dataset.productId;
            const orderQty = this.dataset.seqQty;
            const processName = this.dataset.projectProcessName;           

            this.classList.add('disabled-btn');

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
                    process_name: processName
                })
            })
            .then(response => response.json())
            .then(data => { 
                // Wait for database to update and then fetch latest state
                setTimeout(() => {                 
                    fetchTimerState(projectId, productId, orderQty, processName);
                }, 1000);
            })
                .catch(error => {
                    console.error("Error starting timer:", error);
                    this.classList.remove('disabled-btn');
                });
            });
    });

    // Pause button event listeners
    document.querySelectorAll('.pause-timer').forEach(button => {
        button.addEventListener('click', function() {
            const operatorId = this.dataset.operatorId;
            const uniqueId = this.dataset.uniqueId;
            const projectId = this.dataset.projectId;
            const productId = this.dataset.productId;
            const orderQty = this.dataset.seqQty;
            const processName = this.dataset.projectProcessName;         

            // Immediately pause the timer in the UI
            pauseTimer(uniqueId);

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
                    process_name: processName
                })
            })
            .then(response => response.json())
            .then(data => {
                
                // Fetch latest state to ensure synchronization
                fetchTimerState(projectId, productId, orderQty, processName);
            })
            .catch(error => {
               
            });
        });
    });

    // Stop button event listeners
    document.querySelectorAll('.stop-timer').forEach(button => {
        button.addEventListener('click', function() {
            const operatorId = this.dataset.operatorId;
            const uniqueId = this.dataset.uniqueId;
            const projectId = this.dataset.projectId;
            const productId = this.dataset.productId;
            const orderQty = this.dataset.seqQty;
            const projectTypeName = this.dataset.projectTypeName;
            const projectProcessName = this.dataset.projectProcessName;
            const actualTime = actualTimes[uniqueId] || 0;
            const key = this.dataset.key;

           

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
                    project_type_name: projectTypeName,
                    project_process_name: projectProcessName
                })
            })
            .then(response => response.json())
            .then(data => {
                
                // Fetch latest state to update all UI elements
                fetchTimerState(projectId, productId, orderQty, processName);
                 if (data.allStoppedProcess === "true") {
                        
                        enableNextProcess(projectId, productId, orderQty, key);
                    }
            })
            .catch(error => {
                
            });
        });
    });

    function enableNextProcess(projectId, productId, orderQty, currentKey) {
       

        // The next process key will be +1
        const nextKey = parseInt(currentKey) + 1;

        // Create a partial ID pattern (prefix)
        const idPrefix = `start-btn-${projectId}-${productId}-${orderQty}-`;

        // Find all start buttons that match this project/product/qty
        const allButtons = document.querySelectorAll(`[id^="${idPrefix}"]`);

        let nextButtonFound = false;

        allButtons.forEach((btn) => {
            const parts = btn.id.split('-');
            const btnKey = parseInt(parts[parts.length - 1]); // last part is the key
            if (btnKey === nextKey) {
                btn.classList.remove('disabled-btn');
                btn.classList.remove('d-none');
                nextButtonFound = true;               
            }
        });

        if (!nextButtonFound) {           
        }
    }

    // Listening for broadcast events
        function listenToTimerEvents(uniqueId, projectId, productId, orderQty) {
            const channelName = `timer.project.${projectId}.product.${productId}.order.${orderQty}`;          

            Echo.channel(channelName)
            .listen('.timer.started', (data) => {                
                fetchTimerState(projectId, productId, orderQty, processName);
            })
            .listen('.timer.paused', (data) => {              
                fetchTimerState(projectId, productId, orderQty, processName);
            })
            .listen('.timer.stopped', (data) => {               
                fetchTimerState(projectId, productId, orderQty, processName);
            });
        }

        if (typeof projectId !== 'undefined' &&
            typeof productId !== 'undefined' &&
            typeof orderQty !== 'undefined') {           
            fetchTimerState(projectId, productId, orderQty, processName);
            startPeriodicStateCheck();
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
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
        });
    }

    function saveCurrentPageState() {
        const canvas = canvasInstances[currentPage];
        if (canvas) {
            // Save canvas state as JSON instead of just image
            editedPages[currentPage] = canvas.toJSON();           
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
            for (let pageNum = 1; pageNum <= totalPages; pageNum++) {
                if (editedPages[pageNum]) {                   

                    // Get original page size
                    const originalPage = await loadedPDF.getPage(pageNum);
                    const viewport = originalPage.getViewport({
                        scale: 1
                    });
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

                    const pdfPage = await loadedPDF.getPage(pageNum);
                    const viewport = pdfPage.getViewport({
                        scale: 1
                    });

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
                    alert("An unexpected error occurred while uploading.");
                });
           
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Attach click event to all complete-process links
        document.querySelectorAll('.complete-process').forEach(function(element) {
            element.addEventListener('click', function(e) {
                e.preventDefault();

                var processId = this.getAttribute('data-id');
                if (!processId) {
                    alert('Process ID not found');
                    return;
                }

                fetch("{{ route('operator.updateProcessStatus') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            id: processId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            // Optional: Update UI, e.g. disable this button or show Completed badge
                            element.classList.add('disabled-btn');
                            element.title = 'Process completed';
                        } else {
                            alert('Failed to update process status');
                        }
                    })
                    .catch((error) => {
                        console.error('Error:', error);
                        alert('An error occurred while updating the process status');
                    });
            });
        });
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
                const parsed = Papa.parse(csvText.trim(), {
                    skipEmptyLines: true
                });
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
                        items.push({
                            description,
                            articleNo,
                            projectNo,
                            productId
                        });
                    }
                });

                // Step 2: Lookup PO Numbers
                fetch("/operator/get-po-numbers", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            items
                        })
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