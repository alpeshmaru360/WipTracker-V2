@extends('layouts.main')
@section('content')

<link href="{{ asset('css/operator.css') }}" rel="stylesheet" />
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
                <i class="fa fa-tags"></i>
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
                        data-project-id="{{$projects->project_id}}"
                        data-product-id="{{$projects->id}}">
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
                <th  scope="col" class="project_table_heading">Process</th>
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

                        <a class="pt-1 pb-1 ml-3 cursor_pointer start-timer {{$disabled_btn_class}}" 
                        href="javascript:void(0)" data-unique-id="{{ $uniqueId }}" 
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
</div>
<!-- Modal for PDF Editing -->
<div class="modal fade" id="pdfEditorModal" tabindex="-1" role="dialog" aria-labelledby="pdfEditorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pdfEditorModalLabel">Edit PDF</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <canvas id="pdfCanvas"></canvas>
                <div id="toolbar">
                    <button id="drawLine">Line</button>
                    <button id="drawRect">Rectangle</button>
                    <button id="drawArrow">Arrow</button>
                    <button id="selectMode">Select</button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="savePdfChanges">Save changes</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts') 
<!-- <script type="text/javascript" src="{{ asset('js/countdown.js') }}"></script> -->

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
        const activeChannels = new Set();
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

        function startTimer(uniqueId, projectId, productId, orderQty, processName, remainingTime) {
            if (timerIntervals[uniqueId]) {
                clearInterval(timerIntervals[uniqueId]);
            }

            const timerElement = document.getElementById(`time-remaining-${uniqueId}`);
            const startButton = document.querySelector(`.start-timer[data-unique-id="${uniqueId}"]`);
            const pauseButton = document.querySelector(`.pause-timer[data-unique-id="${uniqueId}"]`);
            const stopButton = document.querySelector(`.stop-timer[data-unique-id="${uniqueId}"]`);
            
            if (!timerElement) return;

            timerElement.classList.remove('d-none');
            if (startButton) startButton.classList.add('disabled-btn');
            if (pauseButton) pauseButton.classList.remove('d-none');
            if (stopButton) stopButton.classList.remove('d-none');

            if (!actualTimes[uniqueId]) {
                actualTimes[uniqueId] = 0;
            }

            remainingTimes[uniqueId] = remainingTime;

            timerIntervals[uniqueId] = setInterval(() => {
                actualTimes[uniqueId] += 1;
                remainingTimes[uniqueId] = Math.max(0, remainingTimes[uniqueId] - 1);
                
                updateTimerDisplay(uniqueId);
                
                if (remainingTimes[uniqueId] <= 0) {
                    clearInterval(timerIntervals[uniqueId]);
                    autoStopTimer(uniqueId, projectId, productId, orderQty, processName, actualTimes[uniqueId]);
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
                timerElement.textContent = formatTime(remainingTimes[uniqueId]);
            }
            
            if (actualTimeElement) {
                actualTimeElement.textContent = formatTime(actualTimes[uniqueId]);
            }
        }

        function formatTime(seconds) {
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const remainingSeconds = seconds % 60;
            return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(remainingSeconds).padStart(2, '0')}`;
        }

        function enableNextProcess(uniqueId, status) {
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

        function sprintf(format, ...args) {
            return format.replace(/%(\d+)?d/g, function(match, width) {
                let num = args.shift();
                return width ? num.toString().padStart(width, '0') : num;
            });
        }

        function pauseTimer(uniqueId) {
            clearInterval(timerIntervals[uniqueId]);
            const startButton = document.querySelector(`.start-timer[data-unique-id="${uniqueId}"]`);
            const pauseButton = document.querySelector(`.pause-timer[data-unique-id="${uniqueId}"]`);
            
            startButton.classList.remove('disabled-btn');
            pauseButton.classList.add('d-none');
        }

        function stopTimer(uniqueId, projectId, productId, orderQty, actualTime, status) {
            clearInterval(timerIntervals[uniqueId]);
            
            delete actualTimes[uniqueId];
            delete remainingTimes[uniqueId];

            const timerElement = document.getElementById(`time-remaining-${uniqueId}`);
            const startButton = document.querySelector(`.start-timer[data-unique-id="${uniqueId}"]`);
            const pauseButton = document.querySelector(`.pause-timer[data-unique-id="${uniqueId}"]`);
            const stopButton = document.querySelector(`.stop-timer[data-unique-id="${uniqueId}"]`);
            const actualTimeElement = document.querySelector(`.actual-time[data-unique-id="${uniqueId}"]`);
            const statusElement = document.querySelector(`.status[data-unique-id="${uniqueId}"]`);
            
            if (timerElement) timerElement.classList.add('d-none');
            if (startButton) startButton.classList.add('disabled-btn');
            if (pauseButton) pauseButton.classList.add('d-none');
            if (stopButton) stopButton.classList.add('d-none');
            
            if (actualTimeElement) actualTimeElement.textContent = actualTime;
            
            if (statusElement) {
                statusElement.innerHTML = status === "1" ? 
                    '<span class="badge badge-success p-2">Completed</span>' : 
                    '<span class="badge badge-danger p-2">Pending</span>';
            }

            enableNextProcess(uniqueId, status);
        }

        // async function checkExistingTimers() {
        //     const promises = Array.from(document.querySelectorAll('.start-timer')).map(async button => {
        //         const uniqueId = button.dataset.uniqueId;
        //         const projectId = button.dataset.projectId;
        //         const productId = button.dataset.productId;
        //         const orderQty = button.dataset.seqQty;

        //         try {
        //             const response = await fetch(`/check-timer-status/${projectId}/${productId}/${orderQty}`);
        //             const data = await response.json();

        //             if (data.status === 'running') {
        //                 const elapsedTime = Math.floor((Date.now() - new Date(data.startedAt).getTime()) / 1000);
        //                 const remainingTime = Math.max(0, data.remainingTime - elapsedTime);
                        
        //                 // Only start if timer isn't already running
        //                 if (!timerIntervals[uniqueId]) {
        //                     startTimer(uniqueId, projectId, productId, orderQty, remainingTime);
        //                 }
        //             }
        //         } catch (error) {
        //             console.error('Error checking timer status:', error);
        //         }
        //     });

        //     await Promise.all(promises);
        // }

        function convertHoursToSeconds(hours) {
            return Math.floor(hours * 3600); // Convert hours to seconds
        }

        // Event listeners for buttons
        document.querySelectorAll('.start-timer').forEach(button => {
            const uniqueId = button.dataset.uniqueId;
            const projectId = button.dataset.projectId;
            const productId = button.dataset.productId;
            const orderQty = button.dataset.seqQty;
            const processName = button.dataset.projectProcessName;

            listenToTimerEvents(uniqueId, projectId, productId, orderQty);

            button.addEventListener('click', function() {
                if (this.classList.contains('disabled-btn')) return;
                
                const stdTime = parseFloat(this.dataset.stdTime);
                const remainingTime = remainingTimes[uniqueId] || convertHoursToSeconds(stdTime);

                fetch("/operator/start_timer", {
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
                    startTimer(uniqueId, projectId, productId, orderQty, processName, data.remainingTime);
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
            const channel = Echo.channel(`timer.${projectId}.${productId}.${orderQty}`);

            channel.listen('.TimerUpdated', (data) => {
                console.log('Timer updated event received:', data);
                handleTimerUpdate(uniqueId, data);
            });
        }

        function handleTimerUpdate(uniqueId, data) {
            if (data.status === 'running') {
                startTimer(uniqueId, data.projectId, data.productId, data.orderQty, data.processName, data.remainingTime);
            } else if (data.status === 'paused') {
                pauseTimer(uniqueId);
            } else if (data.status === 'stopped') {
                stopTimer(uniqueId, data.projectId, data.productId, data.orderQty, data.actualTime, data.projectStatus);
            }
        }

        // Call this function for each timer on the page
        document.querySelectorAll('.start-timer').forEach(button => {
            const uniqueId = button.dataset.uniqueId;
            const projectId = button.dataset.projectId;
            const productId = button.dataset.productId;
            const orderQty = button.dataset.seqQty;
            listenToTimerEvents(uniqueId, projectId, productId, orderQty);
        });

        function checkExistingTimers() {
            document.querySelectorAll('.start-timer').forEach(button => {
                const uniqueId = button.dataset.uniqueId;
                const projectId = button.dataset.projectId;
                const productId = button.dataset.productId;
                const orderQty = button.dataset.seqQty;
                const processName = button.dataset.projectProcessName;

                fetch(`/get-timer-status/${projectId}/${productId}/${orderQty}/${processName}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'running') {
                            startTimer(uniqueId, projectId, productId, orderQty, processName, data.remainingTime);
                        } else if (data.status === 'stopped' && data.projectStatus === '1') {
                            stopTimer(uniqueId, projectId, productId, orderQty, data.actualTime, data.projectStatus);
                        }
                    })
                    .catch(error => console.error('Error checking timer status:', error));
            });
        }
        // Clean up function
        checkExistingTimers();

        // Clean up function
        function cleanupTimers() {
            Object.keys(timerIntervals).forEach(key => {
                clearInterval(timerIntervals[key]);
            });
            timerIntervals = {};
            actualTimes = {};
            remainingTimes = {};
        }

        // Clean up before unload
        window.addEventListener('beforeunload', cleanupTimers);
    });

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
                        // alert(response.message);
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

<!-- Timer code ends -->
<!-- PDF Edit & Download -->
<!-- Include PDF.js -->
<!-- Include PDF-LIB for PDF manipulation -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.6.347/pdf.min.js"></script>
<script src="https://unpkg.com/pdf-lib/dist/pdf-lib.min.js"></script>
<!-- Include Fabric.js for drawing shapes and annotations -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.2.4/fabric.min.js"></script>
<!-- Include jQuery and Bootstrap for modal and AJAX -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    // Set the worker source for PDF.js
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.6.347/pdf.worker.min.js';
    $(document).ready(function() {
        let fabricCanvas;
        let pdfDoc = null;
        let currentPage = 1;
        let scale = 1.5;

        // Initialize Fabric.js canvas
        function initFabricCanvas() {
            fabricCanvas = new fabric.Canvas('pdfCanvas', {
                isDrawingMode: false, // Disable freehand drawing by default
                selection: true, // Enable object selection
            });
        }

        // Render the PDF page onto the canvas
        async function renderPage(pageNum) {
            const page = await pdfDoc.getPage(pageNum);
            const viewport = page.getViewport({ scale: scale });
            const canvas = document.getElementById('pdfCanvas');
            const context = canvas.getContext('2d');

            canvas.height = viewport.height;
            canvas.width = viewport.width;

            const renderContext = {
                canvasContext: context,
                viewport: viewport,
            };

            await page.render(renderContext).promise;
        }

        // Load the PDF
        async function loadPdf(url) {
            const loadingTask = pdfjsLib.getDocument(url);
            pdfDoc = await loadingTask.promise;
            await renderPage(currentPage);
        }

        // Handle modal show event
        $('#pdfEditorModal').on('show.bs.modal', async function(event) {
            const button = $(event.relatedTarget);
            const pdfUrl = button.data('pdf-url');
            initFabricCanvas();
            await loadPdf(pdfUrl);
        });

        // Add drawing tools
        $('#drawLine').on('click', function() {
            fabricCanvas.isDrawingMode = false;
            const line = new fabric.Line([50, 100, 200, 200], {
                stroke: 'red',
                strokeWidth: 2,
                selectable: true,
            });
            fabricCanvas.add(line);
        });

        $('#drawRect').on('click', function() {
            fabricCanvas.isDrawingMode = false;
            const rect = new fabric.Rect({
                left: 100,
                top: 100,
                width: 200,
                height: 100,
                fill: 'transparent',
                stroke: 'blue',
                strokeWidth: 2,
                selectable: true,
            });
            fabricCanvas.add(rect);
        });

        $('#drawArrow').on('click', function() {
            fabricCanvas.isDrawingMode = false;
            const arrow = new fabric.Line([50, 50, 200, 50], {
                stroke: 'green',
                strokeWidth: 2,
                selectable: true,
            });
            fabricCanvas.add(arrow);
        });

        $('#selectMode').on('click', function() {
            fabricCanvas.isDrawingMode = false;
            fabricCanvas.selection = true;
        });

        // Handle save changes
        $('#savePdfChanges').on('click', async function() {
            const projectId = $('#pdfEditorModal').data('project-id');
            const productId = $('#pdfEditorModal').data('product-id');

            // Export the canvas as an image
            const imageData = fabricCanvas.toDataURL({
                format: 'png',
                quality: 1,
            });

            // Send the image data to the server
            $.ajax({
                url: '/save-pdf-changes', // Your backend endpoint to save changes
                method: 'POST',
                data: {
                    project_id: projectId,
                    product_id: productId,
                    image_data: imageData,
                },
                success: function(response) {
                    alert('Changes saved successfully!');
                    $('#pdfEditorModal').modal('hide');
                },
                error: function(error) {
                    alert('Error saving changes.');
                }
            });
        });
    });
</script>
@endsection
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