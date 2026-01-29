@extends('layouts.main')
@section('content')
<!-- ... other head elements ... -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/4.5.0/fabric.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js"></script>
<script>pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.worker.min.js';</script>
<style type="text/css">
    .dataTables_wrapper .dataTables_paginate{
        margin-top: 0px !important;
    }
    .dashboard_heading{
        padding-bottom: 30px;
    }
    #cameraButton{
        margin-top: 20px !important;
    }
    @media (max-width: 768px) {
      table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
        table-layout:fixed;
      }
    }

    /* public/css/pdf-editor.css */
    #pdfContainer {
        overflow: auto;
        max-height: 70vh;
        border: 1px solid #ddd;
        margin-bottom: 20px;
    }

    .tools-panel {
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .tools-panel button {
        width: 100%;
        margin-bottom: 10px;
    }

    #pdfCanvas {
        margin: 0 auto;
        display: block;
    }

    .modal-xl {
        max-width: 95% !important;
    }
    .formatting-toolbar {
    padding: 10px;
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    display: none;
    }

    .formatting-toolbar button {
        margin-right: 5px;
    }

    .formatting-toolbar select {
        margin-right: 10px;
    }

    .tool-button {
        padding: 5px 10px;
        margin: 2px;
        border: 1px solid #ddd;
        background: white;
        cursor: pointer;
    }

    .tool-button.active {
        background: #007bff;
        color: white;
    }

    .color-picker {
        width: 40px;
        height: 30px;
        padding: 0;
        border: none;
        margin-right: 10px;
    }
</style>
<style>
    #pdfContainer {
        overflow: auto;
        height: 70vh;
        background-color: #525659;
        padding: 20px;
        display: flex;
        justify-content: center;
        align-items: flex-start;
    }

    #pdfCanvas {
        background-color: white;
        box-shadow: 0 0 10px rgba(0,0,0,0.3);
    }

    .modal-xl {
        max-width: 95% !important;
    }

    .formatting-toolbar {
        padding: 10px;
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        display: none;
    }

    .tool-button {
        padding: 5px 10px;
        margin: 2px;
        border: 1px solid #ddd;
        background: white;
        cursor: pointer;
    }

    .tool-button.active {
        background: #007bff;
        color: white;
    }

    .color-picker {
        width: 40px;
        height: 30px;
        padding: 0;
        border: none;
        margin-right: 10px;
    }

    @media (max-width: 768px) {
        #pdfContainer {
            height: 60vh;
        }
    }
</style>
<div class="main_section bg-white m-4 dashboard_heading">
    <div class="row">
        <div class="ml-5 mt-4 col-xl-7">
            <a href="{{route('OperatorProductType',['product_id'=>$product_id,'redirect' => '1'])}}" class="project_icon p-2 m-1 text-decoration-none">
                <i class="fa fa-arrow-left project_icon"></i>
                <span class="text-white">&nbsp; BACK</span>
            </a>
        </div>

        <div class="mt-4 col-xl-4 text-right">
            @if($projects->bom_path != null)
                <a href="{{asset($projects->bom_path)}}" class="project_icon p-2 m-1 text-decoration-none" download>
                <i class="fa fa-download project_icon"></i>
                <span class="text-white">&nbsp; BOM </span>
            @else
                <a href="#" class="project_icon p-2 m-1 text-decoration-none">
                <i class="fa fa-tags project_icon"></i>
                <span class="text-white">&nbsp;Requested BOM </span>
            @endif
            </a>

            @if($projects->drawing_req_estimation_manager == "0")
                <a href="#" class="project_icon p-2 m-1 text-decoration-none" title="Didn't get any Drawing Request while creating a project.">
                <i class="fa fa-tags project_icon"></i>
                <span class="text-white">&nbsp;Drawing Request Not Sent</span>
            @elseif($projects->drawing_req_estimation_manager == "1")
                <a href="#" class="project_icon p-2 m-1 text-decoration-none" title="Drawing Request get but yes Estimation Manager didn't upload.">
                <i class="fa fa-tags project_icon"></i>
                <span class="text-white">&nbsp;Drawing Request Sent</span>
            @elseif($projects->drawing_req_estimation_manager == "2" && $projects->drawing_check_procurement_manager == "1")
                <a href="#" class="project_icon p-2 m-1 text-decoration-none" title="Drawing Uploaded but yet Procurement Manager didn't checked.">
                <i class="fa fa-tags project_icon"></i>
                <span class="text-white">&nbsp;Drawing Request Not Checked</span>
            @else
                <a href="{{asset($projects->drawing_path)}}" class="project_icon p-2 m-1 text-decoration-none" download>
                <i class="fa fa-download project_icon"></i>
                <span class="text-white">&nbsp; Drawing  </span>
            @endif
            </a>

            @if($projects->drawing_req_estimation_manager == "2" && $projects->drawing_check_procurement_manager == "2")
                <button type="button" 
                        class="project_icon p-2 m-1 text-decoration-none" 
                        data-toggle="modal" 
                        data-target="#pdfEditorModal" 
                        data-pdf-url="{{asset($projects->drawing_path)}}"
                        data-project-id="{{$projects->id}}">
                    <i class="fa fa-tags project_icon"></i> Open PDF Editor
                </button>
            @endif
        </div>
    </div>
    <h3 class="ml-4 pt-4 text-bold text-left text-uppercase">{{$page_title}}</h3>

    <div class="m-3 mt-2  project_approval_section border">
        <h5 class="text-uppercase text-left mb-2 mt-3 ml-3 font-weight-bolder">
            Project Details</h5>
        <hr class="mx-3 mt-2 mb-2" />
        
        <div class="row mt-0 ml-3">
            <div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xl-2">
                <label class="form-label" for="">Project No. :  </label>
            </div>
            <div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3">
                {{$projects->projects['project_no']}}
            </div>
            <!-- <div class="col-3 col-md-6 col-lg-6 col-xl-2"></div> -->
        </div>
        <div class="row ml-3">
            <div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xl-2">
                <label class="form-label" for="">Project Name : </label>
            </div>
            <div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3">
                {{$projects->projects['project_name']}}
            </div>
            <!-- <div class="col-3 col-md-6 col-lg-6 col-xl-2"></div> -->
        </div>

        <div class="row mt-0 ml-3">
            <div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xl-2">
                <label class="form-label" for="">Estimated Date : </label>
            </div>
            <div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3">
                {{$projects->projects['estimated_readiness'] ? \Carbon\Carbon::parse($projects->projects['estimated_readiness'])->format('d-M-Y') : 'N/A'}}
            </div>
            <!-- <div class="col-3 col-md-6 col-lg-6 col-xl-2"></div> -->
        </div>
    </div>
    
    <div class="m-3 mt-4 project_approval_section border">
        <h5 class="text-uppercase text-left mb-2 mt-3 ml-3 font-weight-bolder">Product Type : {{$project_type_name}}</h5>
        <hr class="mx-3 mt-0" />
        <div class="row mt-3 mx-3"> 
        <table class="table table-hover table-border w-100 text-center" id = "project_table">
            <thead>
              <tr>
                <th  class="col" class="project_table_heading">Process</th>
                <th  scope="col" class="project_table_heading">Standard Time <br>(In Hours)</th>
                <th  scope="col" class="project_table_heading">Capture</th>
                <th  scope="col" class="project_table_heading">Timer</th>
                <th  scope="col" class="project_table_heading">Show Current Timer</th>
                <th  scope="col" class="project_table_heading">Actual Time</th>
                <th  scope="col" class="project_table_heading">Status</th>
                <th  scope="col" class="project_table_heading">Action</th>
              </tr>
            </thead>
            <tbody>
                @foreach($process_name as $key => $val)
                <tr>
                    <td class="w-30">{{$val->project_process_name}}</td>
                    <td class="w-15">
                         <span id="" class="text-bold">{{getTimeFormat($val->process_std_time)}} </span> 
                         <span class="text-bold">
                    </td>
                    <td  class="w-10"><i class="fa fa-camera project_icon p-2 m-1"></i></td>
                    <td class="w-20 text-center">
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
                        @endphp

                        @php
                            $uniqueId = $val->projects_id . '-' . $val->product_id . '-' . $val->order_qty . '-' . $key;
                        @endphp

                        <a class="pt-1 pb-1 ml-3 cursor_pointer start-timer {{$disabled_btn_class}}" href="javascript:void(0)" data-unique-id="{{ $uniqueId }}" data-key="{{ $key }}" data-project-id="{{ $val->projects_id }}" data-product-id="{{ $val->product_id }}" data-project-type-name="{{ $val->project_type_name }}" data-project-process-name="{{ $val->project_process_name }}" data-seq-qty="{{ $val->order_qty }}" data-std-time="{{ $val->process_std_time }}" title="Click here to Start Timer">
                            <i class="fa fa-clock project_icon p-2 m-1"></i>
                        </a>
                        
                        <a class="pt-1 pb-1 d-none cursor_pointer pause-timer" href="javascript:void(0)" data-unique-id="{{ $uniqueId }}" data-key="{{ $key }}" data-project-id="{{ $val->projects_id }}" data-product-id="{{ $val->product_id }}" data-project-type-name="{{ $val->project_type_name }}" data-project-process-name="{{ $val->project_process_name }}" data-std-time="{{ $val->process_std_time }}" data-seq-qty="{{ $val->order_qty }}"  title="Click here to Pause Timer">
                            <i class="fa fa-pause project_icon p-2 m-1 ml-3"></i>
                        </a>

                        <a class="pt-1 pb-1 d-none cursor_pointer stop-timer" href="javascript:void(0)" data-unique-id="{{ $uniqueId }}" data-key="{{ $key }}" data-project-id="{{ $val->projects_id }}" data-product-id="{{ $val->product_id }}" data-project-type-name="{{ $val->project_type_name }}" data-project-process-name="{{ $val->project_process_name }}" data-std-time="{{ $val->process_std_time }}" data-seq-qty="{{ $val->order_qty }}"  title="Click here to Stop Timer">
                            <i class="fa fa-stop project_icon p-2 m-1 ml-3"></i>
                        </a>
                    </td>
                    <td class="w-15">
                        <span id="time-remaining-{{$uniqueId}}" class="text-white p-2 text-center br-12 primary_bg_color w-20 d-none fs-22">{{$val->process_std_time}}</span>
                        {{--
                            <span id="time-remaining-{{ $val->projects_id }}-{{ $val->product_id }}-{{$val->order_qty}}" class="text-white p-2 text-center br-12 primary_bg_color w-20 d-none fs-22">{{$val->process_std_time}}</span>
                        --}}
                    </td>
                    <td class="w-15 actual-time" data-unique-id="{{ $uniqueId }}" data-project-id="{{ $val->projects_id }}" data-product-id="{{ $val->product_id }}" data-seq-qty="{{ $val->order_qty }}">{{$val->project_actual_time == "00:00:00" ? "" : $val->project_actual_time }}</td>
                    <td class="w-15">
                        <span class="badge p-2 status" data-unique-id="{{ $uniqueId }}" data-project-id="{{ $val->projects_id }}" data-product-id="{{ $val->product_id }}" data-seq-qty="{{ $val->order_qty }}">
                            @if($val->project_status == "0") 
                                <span class="badge badge-danger p-2">Pending</span>
                            @else
                                <span class="badge badge-success p-2">Completed</span>
                            @endif
                        </span>
                    </td>
                    <td>
                        <a class="pt-1 pb-1 ml-3 cursor_pointer reset-timer" href="javascript:void(0)" data-key="{{ $key }}" data-project-id="{{ $val->projects_id }}" data-product-id="{{ $val->product_id }}" data-project-type-name="{{ $val->project_type_name }}" data-project-process-name="{{ $val->project_process_name }}" data-seq-qty="{{ $val->order_qty }}" title="Click here to Start Timer">
                            <i class="fa fa-window-close project_icon p-2 m-1"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    </div>

<!-- resources/views/components/pdf-editor-modal.blade.php -->
<!-- <div class="modal fade" id="pdfEditorModal" tabindex="-1" role="dialog" aria-labelledby="pdfEditorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pdfEditorModalLabel">PDF Editor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="tools-container">
                <div class="main-toolbar p-2 bg-light border-bottom">
                    <button id="textTool" class="tool-button">
                        <i class="fas fa-font"></i> Add Text
                    </button>
                    <button id="drawTool" class="tool-button">
                        <i class="fas fa-pencil-alt"></i> Draw
                    </button>
                    <button id="clearTool" class="tool-button">
                        <i class="fas fa-trash"></i> Clear
                    </button>
                    <input type="color" id="strokeColor" class="color-picker" title="Drawing Color">
                    <input type="range" id="strokeWidth" min="1" max="20" value="2" title="Line Width">
                </div>
                <div id="textFormatting" class="formatting-toolbar">
                    <button id="boldBtn" class="tool-button" title="Bold">
                        <i class="fas fa-bold"></i>
                    </button>
                    <button id="italicBtn" class="tool-button" title="Italic">
                        <i class="fas fa-italic"></i>
                    </button>
                    <button id="underlineBtn" class="tool-button" title="Underline">
                        <i class="fas fa-underline"></i>
                    </button>
                    <select id="fontFamilySelect" class="custom-select" style="width: auto;">
                        <option value="Arial">Arial</option>
                        <option value="Times New Roman">Times New Roman</option>
                        <option value="Courier New">Courier New</option>
                        <option value="Georgia">Georgia</option>
                        <option value="Verdana">Verdana</option>
                    </select>
                    <select id="fontSizeSelect" class="custom-select" style="width: auto;">
                        <option value="12">12</option>
                        <option value="14">14</option>
                        <option value="16">16</option>
                        <option value="18">18</option>
                        <option value="20">20</option>
                        <option value="24">24</option>
                        <option value="28">28</option>
                        <option value="32">32</option>
                    </select>
                    <input type="color" id="textColorPicker" class="color-picker" title="Text Color">
                </div>
            </div>
            <div class="modal-body">
                <div id="pdfContainer">
                    <canvas id="pdfCanvas"></canvas>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="savePdfBtn">Save Changes</button>
            </div>
        </div>
    </div>
</div> -->
</div>
@endsection

@section('scripts') 
    <script type="text/javascript">
        const updateStatusUrl = "{{ route('OperatorUpdateProcessStatus') }}"; // Correctly define the route
        const csrfToken = "{{ csrf_token() }}"; // Get CSRF token
    </script>

    <script>
        const projectId = @json($projects['projects']->id); 
        const productId = @json($projects->id); 
        const orderQty = @json($seq_qty); 
        
        window.projectId = @json($projects['projects']->id); 
        window.productId =  @json($projects->id); 
        window.orderQty = @json($seq_qty);
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const pusherKey = "{{ env('VITE_PUSHER_APP_KEY') }}";
            const pusherCluster = "{{ env('VITE_PUSHER_APP_CLUSTER') }}";
            window.Pusher = Pusher;
            
            if (window.Echo) {
                console.log('Echo is properly initialized');
            } else {
                console.log("Echo not working");
                return;
            }

            let timerIntervals = {};
            let actualTimes = {};
            let remainingTimes = {};
            let startTimes = {};

            function startTimer(uniqueId, projectId, productId, orderQty, remainingTime) {
                const timerElement = document.getElementById(`time-remaining-${uniqueId}`);
                const startButton = document.querySelector(`.start-timer[data-unique-id="${uniqueId}"]`);
                const pauseButton = document.querySelector(`.pause-timer[data-unique-id="${uniqueId}"]`);
                const stopButton = document.querySelector(`.stop-timer[data-unique-id="${uniqueId}"]`);
                
                if (!timerElement) {
                    console.error('Timer element not found:', uniqueId);
                    return;
                }

                timerElement.classList.remove('d-none');
                startButton.classList.add('disabled-btn');
                pauseButton.classList.remove('d-none');
                stopButton.classList.remove('d-none');

                clearInterval(timerIntervals[uniqueId]);

                startTimes[uniqueId] = Date.now();
                if (!actualTimes[uniqueId]) {
                    actualTimes[uniqueId] = 0;
                }

                timerIntervals[uniqueId] = setInterval(() => {
                    const elapsedTime = Math.floor((Date.now() - startTimes[uniqueId]) / 1000);
                    actualTimes[uniqueId] += 1;
                    remainingTimes[uniqueId] = Math.max(0, remainingTime - elapsedTime);
                    
                    updateTimerDisplay(uniqueId);
                    
                    if (remainingTimes[uniqueId] <= 0) {
                        clearInterval(timerIntervals[uniqueId]);
                        const actualTime = remainingTime;
                        autoStopTimer(uniqueId, projectId, productId, orderQty, actualTime);
                    }
                }, 1000);
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

            function updateTimerDisplay(uniqueId) {
                const timerElement = document.getElementById(`time-remaining-${uniqueId}`);
                const actualTimeElement = document.querySelector(`.actual-time[data-unique-id="${uniqueId}"]`);
                
                if (timerElement) {
                    const remainingTime = remainingTimes[uniqueId];
                    timerElement.textContent = formatTime(remainingTime);
                }
                
                if (actualTimeElement) {
                    const actualTime = actualTimes[uniqueId];
                    actualTimeElement.textContent = formatTime(actualTime);
                }
            }

            function formatTime(seconds) {
                const hours = Math.floor(seconds / 3600);
                const minutes = Math.floor((seconds % 3600) / 60);
                const remainingSeconds = Math.floor(seconds % 60);
                return sprintf('%02d:%02d:%02d', hours, minutes, remainingSeconds);
            }

            function sprintf(format, ...args) {
                return format.replace(/%(\d+)?d/g, function(match, width) {
                    let num = args.shift();
                    return width ? num.toString().padStart(width, '0') : num;
                });
            }

            function pauseTimer(uniqueId) {  // Changed parameter to uniqueId
                clearInterval(timerIntervals[uniqueId]);  // Use uniqueId
                const startButton = document.querySelector(`.start-timer[data-unique-id="${uniqueId}"]`);
                const pauseButton = document.querySelector(`.pause-timer[data-unique-id="${uniqueId}"]`);
                
                startButton.classList.remove('disabled-btn');
                pauseButton.classList.add('d-none');
            }

            function stopTimer(uniqueId, projectId, productId, orderQty, actualTime, status) {
                clearInterval(timerIntervals[uniqueId]);
                
                delete actualTimes[uniqueId];
                delete remainingTimes[uniqueId];
                delete startTimes[uniqueId];

                const timerElement = document.getElementById(`time-remaining-${uniqueId}`);
                const startButton = document.querySelector(`.start-timer[data-unique-id="${uniqueId}"]`);
                const pauseButton = document.querySelector(`.pause-timer[data-unique-id="${uniqueId}"]`);
                const stopButton = document.querySelector(`.stop-timer[data-unique-id="${uniqueId}"]`);
                // Updated selectors to use uniqueId
                const actualTimeElement = document.querySelector(`.actual-time[data-unique-id="${uniqueId}"]`);
                const statusElement = document.querySelector(`.status[data-unique-id="${uniqueId}"]`);
                
                if (timerElement) {
                    timerElement.classList.add('d-none');
                }
                
                if (startButton) startButton.classList.add('disabled-btn');
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
                            const nextTimerElement = document.getElementById(`time-remaining-${nextUniqueId}`);
                            if (nextTimerElement) {
                                nextTimerElement.classList.remove('d-none');
                            }
                        }
                    }
                }
            }

            function convertHoursToSeconds(hours) {
                return Math.floor(hours * 3600); // Convert hours to seconds
            }

            // Event listeners for buttons
            document.querySelectorAll('.start-timer').forEach(button => {
                button.addEventListener('click', function() {
                    if (this.classList.contains('disabled-btn')) return;

                    const uniqueId = this.dataset.uniqueId;
                    const projectId = this.dataset.projectId;
                    const productId = this.dataset.productId;
                    const orderQty = this.dataset.seqQty;
                    const processName = this.dataset.projectProcessName;
                    const stdTime = parseFloat(this.dataset.stdTime); // Get the standard time in hours
                    const remainingTime = remainingTimes[uniqueId] || convertHoursToSeconds(stdTime); // Convert to seconds

                    fetch("{{ route('OperatorstartTimer') }}", {
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
                            process_name: processName, 
                            remainingTime 
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        startTimer(uniqueId, projectId, productId, orderQty, data.remainingTime);
                    });
                });
            });

            document.querySelectorAll('.pause-timer').forEach(button => {
                button.addEventListener('click', function() {
                    const uniqueId = this.dataset.uniqueId;
                    const projectId = this.dataset.projectId;
                    const productId = this.dataset.productId;
                    const orderQty = this.dataset.seqQty;
                    const remainingTime = remainingTimes[uniqueId] || 0;  // Use uniqueId

                    fetch("{{ route('OperatorpauseTimer') }}", {
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
                            remainingTime 
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        pauseTimer(uniqueId);  // Pass uniqueId
                    });
                });
            });

            // Update the stop button event listener
            document.querySelectorAll('.stop-timer').forEach(button => {
                button.addEventListener('click', function() {
                    const uniqueId = this.dataset.uniqueId;
                    const projectId = this.dataset.projectId;
                    const productId = this.dataset.productId;
                    const orderQty = this.dataset.seqQty;
                    const projectTypeName = this.dataset.projectTypeName;  // Add this
                    const projectProcessName = this.dataset.projectProcessName;  // Add this
                    const actualTime = actualTimes[uniqueId] || 0;

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
                            project_type_name: projectTypeName,     // Add this
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

    <!-- PDF Conversation -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
        // Global variables
        let pdfDoc = null;
        let canvas = null;
        let fabricCanvas = null;
        let currentPage = 1;
        let currentPdfUrl = '';
        let activeObject = null;
        let pdfjsLib = window['pdfjs-dist/build/pdf'];

        // Load PDF function
        async function loadPDF(url) {
            try {
                currentPdfUrl = url;
                const loadingTask = pdfjsLib.getDocument(url);
                pdfDoc = await loadingTask.promise;
                renderPage(currentPage);
            } catch (error) {
                console.error('Error loading PDF:', error);
                alert('Error loading PDF file');
            }
        }

        // Render PDF page
        async function renderPage(pageNumber) {
            try {
                const page = await pdfDoc.getPage(pageNumber);
                const viewport = page.getViewport({ scale: 1.5 });
                
                // Make canvas size responsive
                const container = document.getElementById('pdfContainer');
                const containerWidth = container.clientWidth;
                const scale = containerWidth / viewport.width;
                const scaledViewport = page.getViewport({ scale: scale * 0.95 });
                
                canvas.height = scaledViewport.height;
                canvas.width = scaledViewport.width;
                
                const renderContext = {
                    canvasContext: canvas.getContext('2d'),
                    viewport: scaledViewport
                };
                
                await page.render(renderContext).promise;

                // Set the rendered PDF as background in fabric canvas
                fabricCanvas.setWidth(canvas.width);
                fabricCanvas.setHeight(canvas.height);
                fabricCanvas.setBackgroundImage(canvas.toDataURL(), fabricCanvas.renderAll.bind(fabricCanvas));
            } catch (error) {
                console.error('Error rendering page:', error);
            }
        }

        // Initialize fabric.js canvas
        function initializeFabricCanvas() {
            canvas = document.getElementById('pdfCanvas');
            fabricCanvas = new fabric.Canvas('pdfCanvas', {
                isDrawingMode: false
            });
            
            // Handle selection events
            fabricCanvas.on('selection:created', function(e) {
                activeObject = e.target;
                if (activeObject.type === 'i-text') {
                    showTextFormatting();
                }
            });

            fabricCanvas.on('selection:cleared', function() {
                activeObject = null;
                hideTextFormatting();
            });
        }

        // Text formatting functions
        function showTextFormatting() {
            document.getElementById('textFormatting').style.display = 'block';
            updateFormatButtons();
        }

        function hideTextFormatting() {
            document.getElementById('textFormatting').style.display = 'none';
        }

        function updateFormatButtons() {
            if (!activeObject || activeObject.type !== 'i-text') return;
            
            document.getElementById('boldBtn').classList.toggle('active', activeObject.fontWeight === 'bold');
            document.getElementById('italicBtn').classList.toggle('active', activeObject.fontStyle === 'italic');
            document.getElementById('underlineBtn').classList.toggle('active', activeObject.underline);
            document.getElementById('fontSizeSelect').value = activeObject.fontSize;
            document.getElementById('fontFamilySelect').value = activeObject.fontFamily;
            document.getElementById('textColorPicker').value = activeObject.fill;
        }

        // Initialize tools
        function initializeTools() {
            // Text tool
            document.getElementById('textTool').addEventListener('click', function() {
                const text = new fabric.IText('Click to edit text', {
                    left: 100,
                    top: 100,
                    fontSize: 20,
                    fontFamily: 'Arial',
                    fill: '#000000'
                });
                fabricCanvas.add(text);
                fabricCanvas.setActiveObject(text);
                fabricCanvas.isDrawingMode = false;
            });

            // Drawing tool
            document.getElementById('drawTool').addEventListener('click', function() {
                fabricCanvas.isDrawingMode = !fabricCanvas.isDrawingMode;
                this.classList.toggle('active');
                if (fabricCanvas.isDrawingMode) {
                    fabricCanvas.freeDrawingBrush.width = parseInt(document.getElementById('strokeWidth').value, 10);
                    fabricCanvas.freeDrawingBrush.color = document.getElementById('strokeColor').value;
                }
            });

            // Clear tool
            document.getElementById('clearTool').addEventListener('click', function() {
                if (confirm('Are you sure you want to clear all annotations?')) {
                    fabricCanvas.clear();
                    renderPage(currentPage);
                }
            });

            // Format buttons
            document.getElementById('boldBtn').addEventListener('click', function() {
                if (!activeObject) return;
                activeObject.set('fontWeight', activeObject.fontWeight === 'bold' ? 'normal' : 'bold');
                fabricCanvas.renderAll();
                updateFormatButtons();
            });

            document.getElementById('italicBtn').addEventListener('click', function() {
                if (!activeObject) return;
                activeObject.set('fontStyle', activeObject.fontStyle === 'italic' ? 'normal' : 'italic');
                fabricCanvas.renderAll();
                updateFormatButtons();
            });

            document.getElementById('underlineBtn').addEventListener('click', function() {
                if (!activeObject) return;
                activeObject.set('underline', !activeObject.underline);
                fabricCanvas.renderAll();
                updateFormatButtons();
            });

            // Color and stroke controls
            document.getElementById('strokeColor').addEventListener('change', function() {
                fabricCanvas.freeDrawingBrush.color = this.value;
            });

            document.getElementById('strokeWidth').addEventListener('change', function() {
                fabricCanvas.freeDrawingBrush.width = parseInt(this.value, 10);
            });

            document.getElementById('textColorPicker').addEventListener('input', function() {
                if (!activeObject) return;
                activeObject.set('fill', this.value);
                fabricCanvas.renderAll();
            });

            // Font controls
            document.getElementById('fontSizeSelect').addEventListener('change', function() {
                if (!activeObject) return;
                activeObject.set('fontSize', parseInt(this.value, 10));
                fabricCanvas.renderAll();
            });

            document.getElementById('fontFamilySelect').addEventListener('change', function() {
                if (!activeObject) return;
                activeObject.set('fontFamily', this.value);
                fabricCanvas.renderAll();
            });
        }

        // Initialize modal
        $('#pdfEditorModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const pdfUrl = button.data('pdf-url');
            
            initializeFabricCanvas();
            initializeTools();
            loadPDF(pdfUrl);
        });

        // Handle window resize
        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function() {
                if (pdfDoc) {
                    renderPage(currentPage);
                }
            }, 250);
        });

        // Clean up when modal closes
        $('#pdfEditorModal').on('hidden.bs.modal', function () {
            if (fabricCanvas) {
                fabricCanvas.dispose();
            }
            pdfDoc = null;
            currentPage = 1;
            currentPdfUrl = '';
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        });
        });
    </script>

    <script>
        // Add this to your existing JavaScript code, right after the tools initialization
        document.getElementById('savePdfBtn').addEventListener('click', async function() {
            try {
                if (!fabricCanvas) return;
                
                const dataUrl = fabricCanvas.toDataURL({
                    format: 'png',
                    quality: 1
                });

                // Get the project ID from the button that opened the modal
                const projectId = document.querySelector('[data-target="#pdfEditorModal"]').dataset.projectId;

                const response = await fetch('/save-edited-pdf', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        pdfData: dataUrl,
                        projectId: projectId
                    })
                });

                const result = await response.json();
                if (result.success) {
                    alert('PDF saved successfully');
                    $('#pdfEditorModal').modal('hide');
                    // Optionally reload the page or update the UI
                    window.location.reload();
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                console.error('Error saving PDF:', error);
                alert('Error saving PDF: ' + error.message);
            }
        });
    </script>
@endsection
<!-- <script type="text/javascript" src="{{ asset('js/countdown.js') }}"></script> -->
<script type="module">
   // document.addEventListener('DOMContentLoaded', function() {
   //  const pusherKey = '2bac3c836ee8920ba598';
   //  const pusherCluster = 'mt1';

   //  if (!pusherKey || !pusherCluster) {
   //      console.error('Pusher environment variables are missing.');
   //      return;
   //  }

   //  const pusher = new Pusher(pusherKey, {
   //      cluster: pusherCluster,
   //      encrypted: true,
   //      authEndpoint: '/pusher/auth', // Laravel's default auth endpoint
   //      auth: {
   //          headers: {
   //              'X-CSRF-TOKEN': csrfToken,
   //          },
   //      },
   //  });

   //  const channelName = 'private-channel'; // This should match the channel you're trying to access
   //  const socketId = pusher.connection.socket_id;

   //  console.log('Pusher initialized successfully:', pusher);
   //  // Subscribe to a channel
   //  const channel = pusher.subscribe('private-channel');

   //  // Handle successful subscription
   //  channel.bind('pusher:subscription_succeeded', function() {
   //      console.log('Successfully subscribed to the private channel.');
   //  });

   //  // Listen for subscription error
   //  channel.bind('pusher:subscription_error', function(status) {
   //      console.error('Pusher subscription error1:', status);
   //  });


   //  // channel.bind('pusher:subscribe', function() {
   //  //     // Manually trigger the authentication request
   //  //     fetch('/pusher/auth', {
   //  //         method: 'POST',
   //  //         body: JSON.stringify({ channel_name: 'private-channel' }),
   //  //         headers: {
   //  //             'Content-Type': 'application/json',
   //  //             'X-CSRF-TOKEN': csrfToken
   //  //         }
   //  //     })
   //  //     .then(response => response.json())
   //  //     .then(data => {
   //  //         if (data && data.auth) {
   //  //             pusher.subscribe('private-channel', {
   //  //                 auth: data.auth
   //  //             });
   //  //         }
   //  //     })
   //  //     .catch(error => {
   //  //         console.error('Error with authentication:', error);
   //  //     });
   //  // });
   //      // Initialize Pusher
   //      // const pusher = new Pusher(import.meta.env.VITE_PUSHER_APP_KEY, {
   //      //     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
   //      //     encrypted: true,
   //      // });

   //      // Subscribe to the channel
   //      // const channel = pusher.subscribe('private-product.' + productId);

   //      // Listen for the event
   //      channel.bind('process.started', function(data) {
   //          console.log('Process Started: ', data);

   //          const productId = data.productId;
   //          const startTime = new Date(data.startTime).getTime();

   //          // Update UI or start the timer based on the event
   //          startFrontendTimer(productId, startTime);
   //      });

   //      function startFrontendTimer(productId, startTime) {
   //          const timerButton = document.querySelector(`.start-timer[data-product-id="${productId}"]`);
   //          if (timerButton) {
   //              timerButton.classList.add('d-none');
   //              const stopButton = document.querySelector(`.stop-timer[data-product-id="${productId}"]`);
   //              if (stopButton) stopButton.classList.remove('d-none');

   //              // Start the timer UI logic
   //              const timeRemainingDisplay = document.getElementById('time-remaining-' + productId);
   //              if (timeRemainingDisplay) {
   //                  const elapsedTime = (Date.now() - startTime) / 1000; // Calculate elapsed time in seconds
   //                  const timeRemaining = Math.max(timeRemaining - elapsedTime, 0); // Adjust the timer display
   //                  updateTimerDisplay(timeRemainingDisplay, timeRemaining);
   //              }
   //          }
   //      }

   //      function updateTimerDisplay(displayElement, timeRemaining) {
   //          let hours = Math.floor(timeRemaining / 3600);
   //          let minutes = Math.floor((timeRemaining % 3600) / 60);
   //          let seconds = timeRemaining % 60;

   //          displayElement.textContent =
   //              `${hours.toString().padStart(2, '0')}:
   //               ${minutes.toString().padStart(2, '0')}:
   //               ${seconds.toString().padStart(2, '0')}`;
   //      }
   //  });
</script>
