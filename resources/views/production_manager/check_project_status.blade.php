@extends('layouts.main')
@section('content')
<link rel="stylesheet" href="{{ asset('css/production_manager.css') }}" />
<div class="check_status_screen_page main_section bg-white m-4 pb-3">
	<div class="container-fluid mt-3 px-5">
		<div class="row d-flex justify-content-between">
			<h3 class="ml-3 pt-4 text-bold text-left text-uppercase">Project No :- {{$project->project_no}} &nbsp; Project Name :- {{$project->project_name}}</h3>
		</div>
		<hr class="mt-1" />
		@foreach($project['product'] as $val)
			<div class="row align-items-center">
				<!-- QR Codes based on Quantity -->
				<div class="col-xl-12">
					@if(!empty($val->qr_codes))
					@php
					$qrCodes = json_decode($val->qr_codes, true);
					@endphp
					@if(is_array($qrCodes) && count($qrCodes) > 0)
					<div class="mb-3 d-flex flex-wrap">
						@foreach($qrCodes as $index => $qrCode)
							<div class="qr-code mx-2 text-center">
								<img src="{{ asset($qrCode) }}" class="qr_code py-2" alt="QR Code for {{ $val->full_article_number }} (Qty {{ $index + 1 }} of {{ $val->qty }})" title="{{ $val->full_article_number }} (Qty {{ $index + 1 }} of {{ $val->qty }})" />
								<div class="qty-lable mt-1">{{ $index + 1 }}/{{ $val->qty }}</div>
							</div>
						@endforeach
					</div>
					@else
					<div class="qr-code text-center">
						<span>No QR Code Available</span>
					</div>
					@endif
					@elseif($val->qr) <!-- Fallback for old data -->
					<div class="mb-3 d-flex flex-wrap">
						@for($i = 1; $i <= $val->qty; $i++)
							<div class="qr-code mx-2 text-center">
								<img src="{{ asset($val->qr) }}" class="qr_code py-2" alt="QR Code for {{ $val->full_article_number }} (Qty {{ $i }} of {{ $val->qty }})" title="{{ $val->full_article_number }} (Qty {{ $i }} of {{ $val->qty }})" />
								<div class="qty-lable mt-1">{{ $i }}/{{ $val->qty }}</div>
							</div>
						@endfor
					</div>
					@else
					<div class="qr-code text-center">
						<span>No QR Code Available</span>
					</div>
					@endif
				</div>

				<!-- Product Details -->
				<div class="col-xl-1 px-1 mb-md-1">
					<div class="p-2 project_status_box text-center text-white">
						<span>Qty: {{$val->qty}}</span>
					</div>
				</div>
				<div class="col-xl-5 px-1 mb-md-1">
					<div class="p-2 project_status_box text-center text-white">
						<span>{{$val->full_article_number}} - {{$val->description}}</span>
					</div>
				</div>
				<div class="col-xl-3 px-1 mb-md-1">
					<div class="p-2 project_status_box text-center text-white">
						@php $commited_date = \Carbon\Carbon::parse($val->estimated_readiness_date)->format('d M Y');@endphp
						<span>Committed Date: {{ $commited_date }}</span>
					</div>
				</div>
				<div class="col-xl-3 px-1 mb-md-1" id="as_per_schedule_date_{{$val->id}}">
					<div class="p-2 project_status_box text-center text-white">
						<span>As per schedule: {{$val->actual_readiness_date ?? $commited_date }}</span>
					</div>
				</div>
			</div>

			<div class="row mt-3 align-items-center">
				<div class="col-xl-6 px-1">
					<div class="p-2 project_status_box text-center text-white">
						<span>Product Type: {{$val->product_type}}</span>
					</div>
				</div>
			</div>

			<div class="row mt-3 table-responsive">
				<div class="table-scroll-wrapper scrollable">
					<table class="table table-hover table-border w-100 text-center" id="project_table">
						<thead>
							<tr>
								<th scope="col" class="project_table_heading">Qty</th>
								<th scope="col" class="project_table_heading">Project creation</th>
								<th scope="col" class="project_table_heading">BOM, drawings</th>
								<th scope="col" class="project_table_heading">Check the BOM and place PO</th>								
								<th scope="col" class="project_table_heading">Initial Inspection</th>
								<th scope="col" class="project_table_heading">Request MRF to warehouse</th>
								@php
								// Get unique process names
								$processNames = $project['productsProcess']
								->where('product_id', $val->id)
								->where('projects_id', $val->project_id)
								->pluck('project_process_name')
								->unique();
								@endphp
								@foreach($processNames as $processName)
								<th scope="col" class="project_table_heading">{{ $processName }}</th>
								@endforeach
								<th scope="col" class="project_table_heading">Final inspection</th>
								<th scope="col" class="project_table_heading">Prepare PL</th>
							</tr>
						</thead>
						<tbody>
							@php							
							$allTaskTotalHours = 0;
							$checkAddedHours = [];

							$remainingAsmblyHours = 0;
							$all_tasks_process = [];

							// Group processes by order_qty
							$groupedProcesses = $project['productsProcess']
							->where('product_id', $val->id)
							->where('projects_id', $val->project_id)
							->groupBy('order_qty');
							$srNo = 1;
							$latest_create_po_date = $project->purchaseOrders()->orderBy('id', 'desc')->first();
							$matchedValue = false;							
							@endphp
							@foreach($groupedProcesses as $qty => $processes)
							<tr>
								<td>{{ $qty }}</td>

								<!-- Project creation - Start -->
								<td>
									@php
									$createdAt = \Carbon\Carbon::parse($project->created_at);

									$wipCreateDate = $project->wip_project_create_date ? \Carbon\Carbon::parse($project->wip_project_create_date) : null;
									$all_tasks_process['create_new_project']['finish_date'] = $project->wip_project_create_date;

									$color = 'black'; // Default color if wip_project_create_date is null

									if ($wipCreateDate && $create_project_hours) 
									{
										$color = getDeadlineStatusColor($createdAt, $create_project_hours, $wipCreateDate);
										$all_tasks_process['create_new_project']['color'] = $color;
									}
									@endphp
									
									<span style="color: {{ $color }}">
										{{ $wipCreateDate ? $wipCreateDate->format('d F Y h:i A') : '--' }}
									</span>
								</td>
								<!-- Project creation - End -->
								<!-- BOM, drawings - Start -->
								<td>
									@php
									$bomDate = '--';
									$bomColor = 'black';
									$drawingDate = ($val->drawing_req_estimation_manager == 0) ? 'Not Requested' : '--';
									$drawingColor = 'black';
									$wipCreateDate = $project->wip_project_create_date ? \Carbon\Carbon::parse($project->wip_project_create_date) : null;									

									if ($project->is_pricing_tool_quotation_number == 1 && !empty($project->wip_project_create_date)) 
									{
										$bomDate = \Carbon\Carbon::parse($project->wip_project_create_date)->format('d F Y h:i A');
										$all_tasks_process['bom']['finish_date'] = $project->wip_project_create_date;

										if ($wipCreateDate && $bom_drawings_hours) {
											$startDate = \Carbon\Carbon::parse($project->wip_project_create_date);

											$bomColor = getDeadlineStatusColor($startDate, $bom_drawings_hours, $bomDate);
											$all_tasks_process['bom']['color'] = $bomColor;
										}
									} elseif ($project->is_pricing_tool_quotation_number == 0) 
									{
										$bomDate = \Carbon\Carbon::parse($val->bom_upload_date)->format('d F Y h:i A');
										$all_tasks_process['bom']['finish_date'] = $val->bom_upload_date;										

										if ($wipCreateDate && $bom_drawings_hours) {
											//$startDate = \Carbon\Carbon::parse($val->bom_upload_date);

											$startDate = null;
				                            $drawingMissing = false;
				                            $bomMissing = false;
											// Pricing Tool = 0, check both drawing + BOM
			                                // Drawing
			                                if ($val->drawing_req_estimation_manager == 0) {
			                                    $drawingDate = $val->created_at;
			                                } elseif (!empty($val->drawing_upload_date)) {
			                                    $drawingDate = \Carbon\Carbon::parse($val->drawing_upload_date);
			                                } else {
			                                    $drawingMissing = true;
			                                }

			                                // BOM
			                                if ($val->bom_req_estimation_manager == 0) {
			                                    $bomDate = $val->created_at;
			                                } elseif (!empty($val->bom_upload_date)) {
			                                    $bomDate = \Carbon\Carbon::parse($val->bom_upload_date);
			                                } else {
			                                    $bomMissing = true;
			                                }

			                                // Take max date if both are uploaded
			                                if (!$drawingMissing && !$bomMissing) {
			                                    $startDate = $drawingDate > $bomDate ? $drawingDate : $bomDate;
			                                }


											$bomColor = getDeadlineStatusColor($startDate, $bom_drawings_hours, $bomDate);
											$all_tasks_process['bom']['color'] = $bomColor;
										}
									}									

									$drawingColor = '';
									$drawingHoursDifference = '--'; // default if data is missing
									if (!empty($val->drawing_upload_date)) 
									{
										$drawingDate = \Carbon\Carbon::parse($val->drawing_upload_date)->format('d F Y h:i A');
										$all_tasks_process['drawings']['finish_date'] = $val->drawing_upload_date;
									}
									if (!empty($val->drawing_upload_date) && !empty($wipCreateDate) && !empty($bom_drawings_hours)) 
									{
									    $drawingUploadDate = \Carbon\Carbon::parse($val->drawing_upload_date);
									    $wipCreate = \Carbon\Carbon::parse($wipCreateDate);

									    $drawingColor = getDeadlineStatusColor($wipCreate, $bom_drawings_hours, $drawingUploadDate);

									    $all_tasks_process['drawings']['color'] = $drawingColor;
									}		


									$addDrawingsHours = false;

									// drawing hours will add only for std project with requested for drawing
									if ($project->is_pricing_tool_quotation_number == 1 
									&& $val->drawing_req_estimation_manager == 1 
									&& empty($val->drawing_upload_date)) 
									{
										$addDrawingsHours = true;								    
									}	

									// Bom and Drawing both combine for non std project
									if ($project->is_pricing_tool_quotation_number == 0 
									&& empty($val->bom_upload_date)	
									&& empty($val->drawing_upload_date))
									{
										$addDrawingsHours = true;
									}			
									
									@endphp
								
									BOM: <span style="color: {{ $bomColor }}">{{ $bomDate }}</span><br>	
									Drawing: <span style="color: {{ $drawingColor }}">{{ $drawingDate }}</span>
								</td>
								<!-- BOM, drawings - End -->

								<!-- Check the BOM and place PO - Start -->
								<td>
									@php
									$poDate = $latest_create_po_date ? \Carbon\Carbon::parse($latest_create_po_date->created_at) : null;
									$poColor = 'black';
									$poDisplay = $poDate ? $poDate->format('d F Y h:i A') : '--';

									if ($poDate) {
										$bomDate = '--';
										if ($project->is_pricing_tool_quotation_number == 1 && !empty($project->wip_project_create_date)) 
										{
											$bomDate = \Carbon\Carbon::parse($project->wip_project_create_date);
										
										} elseif ($project->is_pricing_tool_quotation_number == 0 && !empty($val->bom_upload_date)) {
											$bomDate = \Carbon\Carbon::parse($val->bom_upload_date);
										}

										if ($bomDate !== '--' && $check_bom_place_po_hours) {	
											$poColor = getDeadlineStatusColor($bomDate, $check_bom_place_po_hours, $poDate);
										}
									}
									if(!$poDate){
										$allTaskTotalHours += $check_bom_place_po_hours;
										$checkAddedHours['check_the_bom_and_place_po'][$qty] = $check_bom_place_po_hours;
									}else{
										$all_tasks_process['check_the_bom_and_place_po']['color'] = $poColor;
										$all_tasks_process['check_the_bom_and_place_po']['finish_date'] = $poDate;
									}																
									@endphp									
									<span style="color: {{ $poColor }}">{{ $poDisplay }}</span>
								</td>
								<!-- Check the BOM and place PO - End -->							

								<!-- Initial Inspection - Start -->
								<td>
									@php
								        $initial_inspection_start_at = DB::table('initial_inspection_data')
									                    ->where('project_no', $project->project_no)
									                    ->min('ini_inspection_date');

								        $ini_ins_createdAt = $initial_inspection_start_at ? \Carbon\Carbon::parse($initial_inspection_start_at) : null;
								        $inspectionDate = $intial_inspection ? \Carbon\Carbon::parse($intial_inspection) : null;

								        $color = getDeadlineStatusColor($ini_ins_createdAt, $intial_inspection_hours, $inspectionDate);
								    
								    if(!$intial_inspection){
										$allTaskTotalHours += $intial_inspection_hours;
										$checkAddedHours['initial_inspection'][$qty] = $intial_inspection_hours;
									}else{
										$all_tasks_process['initial_inspection']['color'] = $color;
										$all_tasks_process['initial_inspection']['finish_date'] = $intial_inspection;
									}
								    @endphp								   
									<span style="color: {{ $color }}">
								        {{ $inspectionDate ? $inspectionDate->format('d F Y h:i:s A') : '--' }}
								    </span>									
								</td>
								<!-- Initial Inspection - End -->

								<!-- Request MRF to warehouse - Start -->
								<td>
									@php								
									$request_mrf = DB::table('stock_bom_po')
										            ->where('project_id', $project->id)
										            ->where('product_id', '=', $val->id)
										            ->where('is_email_sent', 2)
										            ->value('mrf_email_sent_date');

									// Parse MRF start date if available (assuming the intial_inspection represents the MRF start date date)	
									$mrf_start = $intial_inspection ? \Carbon\Carbon::parse($intial_inspection) : null;	
									$request_mrfDate = $request_mrf ? \Carbon\Carbon::parse($request_mrf) : null;

									$color = getDeadlineStatusColor($mrf_start, $request_mrf_hours, $request_mrfDate);

									$mrfRequestDate = $request_mrf
									? \Carbon\Carbon::parse($request_mrf)->format('d F Y h:i:s A')
									: '--';

									
									if(!$request_mrf){
										$allTaskTotalHours += $request_mrf_hours;
										$checkAddedHours['request_mrf_to_warehouse'][$qty] = $request_mrf_hours;
									}else{
										$all_tasks_process['request_mrf_to_warehouse']['color'] = $color;
										$all_tasks_process['request_mrf_to_warehouse']['finish_date'] = $request_mrf;
									}
									@endphp								
								
									<span style="color: {{ $color }}">
						                {{ $mrfRequestDate }}
						            </span>
								</td>
								<!-- Request MRF to warehouse - End -->

								<!-- ALL Process - Start -->
								@php 
									$lastProcessEndsAt = null; 
								    $completedHours = 0;
								    // Total process hours once
								    $total_process_hours = DB::table('admin_hours_management')
								        ->where('lable', 'AssemblyProcessTime')
								        ->where('product_type', $val->product_type)
										->where('is_deleted', 0)
								        ->sum('value');
								@endphp

								@foreach($processNames as $processName)
								    @php								    
								        $process = $processes->firstWhere('project_process_name', $processName);
								        $timerEndsAt = null;
								        $deadline = null;
								        $color = 'black'; // default

								        if ($process) {
								            $stdTime = $stdTimes->first(function ($item) use ($process) {
								                return $item->product_id == $process->product_id 
								                    && $item->projects_id == $process->projects_id 
								                    && $item->order_qty == $process->order_qty
								                    && $item->project_process_name == $process->project_process_name;
								            });

								            $timerEndsAt = $stdTime ? $stdTime->timer_ends_at : null;
								            $timerStartsAt = $stdTime ? $stdTime->timer_started_at : null;
								           
								        	$process_hours = DB::table('admin_hours_management')
							                    ->where('lable', 'AssemblyProcessTime')
							                    ->where('product_type', $val->product_type)
							                    ->where('process_name', $process->project_process_name)
												->where('is_deleted', 0)
							                    ->value('value');

								            if ($timerStartsAt) {
								                $startTime = \Carbon\Carbon::parse($timerStartsAt);
								                $endTime = $timerEndsAt ? \Carbon\Carbon::parse($timerEndsAt) : null;							              

								                // Get process time (convert to float for decimal support)	
								                $processHours = (float) $process_hours;	
								                $deadline = calculateAssemblyDeadline($startTime,$processHours);

								                // remove completed process hours
								                if($timerEndsAt){								                	
								                	$completedHours += $process_hours;
								                }             

								                // Deadline check: either actual end time or current time
								                $isDeadlineMissed = $endTime ? $endTime->gt($deadline) : now()->gt($deadline);
								                $color = $isDeadlineMissed ? 'red' : 'green';
								            }
								        }
								    @endphp
								    <td>	
								    	@if($timerStartsAt)
								            Start Date: <span style="color:black">
								            	{{ \Carbon\Carbon::parse($timerStartsAt)->format('d F Y h:i:s A') }}
								            </span><br>								            
								         @else
								         	<span style="color:black">
								            	Timer Not Started								            	
								            </span><br>
								        @endif

								       	@php
										    $formattedTime = '';

										    if ($process_hours) {
										        $hours = floor($process_hours);
										        $minutes = round(($process_hours - $hours) * 60);

										        if ($hours > 0) {
										            $formattedTime .= $hours . ' Hour' . ($hours > 1 ? 's' : '');
										        }

										        if ($minutes > 0) {
										            $formattedTime .= ($hours > 0 ? ' ' : '') . $minutes . ' Min' . ($minutes > 1 ? 's' : '');
										        }

										        // fallback if zero
										        if ($formattedTime == '') {
										            $formattedTime = '0 Min';
										        }
										    }
										@endphp

								        <!-- // Uncomment This For Testing -->
								        @if($process_hours)
								            Process Hours: <span style="color: black">
								                {{ $formattedTime }}
								            </span><br>
								        @endif
								        @if($deadline)
								            Deadline : <span style="color: {{ $color }};font-weight: bold;border:1px solid;">
								                {{ \Carbon\Carbon::parse($deadline)->format('d F Y h:i:s A') }}
								            </span><br>
								        @endif

								        @if($timerEndsAt)
								        	@php 
								        	$lastProcessEndsAt = $timerEndsAt; 

								        	$all_tasks_process['assembly_process']['color'] = $color;
											$all_tasks_process['assembly_process']['finish_date'] = $lastProcessEndsAt;
											@endphp

								            End Date: <span style="color: {{ $color }}">
								                {{ \Carbon\Carbon::parse($timerEndsAt)->format('d F Y h:i:s A') }}
								            </span>
								        @endif
								    </td>
								@endforeach
								
								<!-- ALL Process - End -->

								<!-- Final inspection - Start -->
								<td>
									@php		
									$final_inspection = DB::table('final_inspection_data')
											            ->where('project_no', $project->project_no)
											            ->where('product_desc', '=', $val->description)
											            ->where('product_article_no', '=', $val->full_article_number)
											            ->where('unit_qty', '=', $qty)
											            ->value('created_at');

									// Parse the final inspection start date (assuming the last assembly process date represents the final inspection start date )
									$final_ins_createdAt = $lastProcessEndsAt ? \Carbon\Carbon::parse($lastProcessEndsAt) : null;
									$final_inspectionDate = $final_inspection ? \Carbon\Carbon::parse($final_inspection) : null;

									//echo calculateDeadline($final_ins_createdAt, $final_inspection_hours);
								    $color = getDeadlineStatusColor($final_ins_createdAt, $final_inspection_hours, $final_inspectionDate);

									$finalinspectionDate = $final_inspection
									? \Carbon\Carbon::parse($final_inspection)->format('d F Y h:i:s A')
									: '--';

									if(!$final_inspection){
										$allTaskTotalHours += $final_inspection_hours;
										$checkAddedHours['final_inspection'][$qty] = $final_inspection_hours;
									}else{
										$all_tasks_process['final_inspection']['color'] = $color;
										$all_tasks_process['final_inspection']['finish_date'] = $final_inspection;
									}									
									@endphp	
							       
						            <span style="color: {{ $color }}">
						                {{ $finalinspectionDate }}
						            </span>							   
								</td>
								<!-- Final inspection - End -->

								<!-- Prepare PL - Start -->
								<td>
									@php
									$pl_uploaded_date = DB::table('qty_of_products')
							            ->where('project_id', $project->id)
							            ->where('product_id', '=', $val->id)
							            ->where('qty_number', '=', $qty)
							            ->value('pl_uploaded_date');

							        // Parse the pl_createdAt date if available (assuming the final inspection date represents the pl_createdAt date)

									$pl_createdAt = $final_inspection ? \Carbon\Carbon::parse($final_inspection) : null;
									$pl_uploadedDate = $pl_uploaded_date ? \Carbon\Carbon::parse($pl_uploaded_date) : null;

									//echo calculateDeadline($pl_createdAt, $prepare_pl_hours);
								    $color = getDeadlineStatusColor($pl_createdAt, $prepare_pl_hours, $pl_uploadedDate);

									$plUploadedDate = $pl_uploaded_date
									? \Carbon\Carbon::parse($pl_uploaded_date)->format('d F Y h:i:s A')
									: '--';

									
									if(!$pl_uploaded_date){
										$allTaskTotalHours += $prepare_pl_hours;
										$checkAddedHours['prepare_pl'][$qty] = $prepare_pl_hours;
									}else{
										$all_tasks_process['prepare_pl']['color'] = $color;
										$all_tasks_process['prepare_pl']['finish_date'] = $pl_uploaded_date;
									}
									@endphp								
									
									<span style="color: {{ $color }}">
						                {{ $plUploadedDate }}
						            </span>	

									{{-- $pl_uploaded_dates[$project->id] ?? '--' --}}
								</td>
								<!-- Prepare PL - End -->

								<!-- Calculation As per schedule (Actual Readiness) Date - Start -->
								@php
    								$remainingProcessHours = $total_process_hours - $completedHours;
    								$remainingAsmblyHours += $remainingProcessHours;
    							@endphp
    							<!-- Calculation As per schedule (Actual Readiness) Date - End -->

							</tr>							
							@endforeach						
						</tbody>
					</table>
				</div>
			</div>
			<hr class="mt-1" />

			<!-- Calculation As per schedule (Actual Readiness) Date - Start -->
			@php
						
				$qty = $qty ?? 1;

				$qty_count = ($qty > 0) ? $qty : 1;
			    $remainingHours = $allTaskTotalHours / $qty_count; 

			    if(@$addDrawingsHours == true){
					$remainingHours += $bom_drawings_hours;
					$checkAddedHours['bom_drawings_hours'][$qty] = $bom_drawings_hours;
				}

			    //dd($checkAddedHours,$remainingHours);

			    //echo "Remaining Hours (Assembly: {$remainingAsmblyHours}) ";
			    //echo "(General: {$remainingHours}) <br>";			

			    // Find latest finished task
			    $latestTask = null;
			    $latestTimestamp = 0;

			    //echo '<pre>';print_r($all_tasks_process);echo '</pre>';

			    foreach ($all_tasks_process as $task => $details) {
			        if (!empty($details['finish_date'])) {
			            $timestamp = strtotime($details['finish_date']); // normalize format
			            if ($timestamp > $latestTimestamp) {
			                $latestTimestamp = $timestamp;
			                $latestTask = [
			                    'task' => $task,
			                    'finish_date' => $details['finish_date'],
			                    'color' => $details['color'] ?? '',
			                ];
			            }
			        }
			    }

			    $prepare_pl_date = $latestTask['task'] === 'prepare_pl' ? ($latestTask['finish_date'] ?? '') : '';

			    // Output last finished task details
			    //if ($latestTask) {
			        //echo "Last Finished Task: {$latestTask['task']}<br>";
			        //echo "Finish Date: {$latestTask['finish_date']}<br>";
			        //echo "Color: {$latestTask['color']}<br>";
			        //echo "addDrawingsHours: {$addDrawingsHours}<br>";			        
			        //echo "prepare_pl date: {$prepare_pl_date}<br>";
			    //}

			    // Use latest task finish date as starting point
			    $today_current_date = $latestTask['finish_date'] ?? now();

			    //$remainingHours = 192; // add menaul hours here for testing

			    // Calculate deadlines			    
				
			    $general_tasks_deadline = calculateDeadline($today_current_date, $remainingHours);	
			    //echo "<br>general_tasks_deadline: ".$general_tasks_deadline;
			    //echo "<br>remainingAsmblyHours: ".$remainingAsmblyHours;			    

			    //per shift limitation code - start

			    $product_type_data = DB::table('product_types')
				    ->select('id', 'limitation_per_shift')
				    ->where('project_type_name', $val->product_type)
				    ->first();

				$limitation_per_shift = $product_type_data->limitation_per_shift ?? 0;

				$total_hours_needed = 0;

			    if ($limitation_per_shift > 0 && $qty_count > $limitation_per_shift) {
			        $total_extra_units = $qty_count - $limitation_per_shift;
			        $hours_per_shift = 8;
			        $total_hours_needed = ceil($total_extra_units / $limitation_per_shift) * $hours_per_shift;
			        $final_deadline = calculateAssemblyDeadline($general_tasks_deadline, $total_hours_needed);
			    }else{
			    	$final_deadline = calculateAssemblyDeadline($general_tasks_deadline, $remainingAsmblyHours);
			    }
			    
			    //echo "<br>final_deadline: ".$final_deadline;	

			    //per shift limitation code - end

			    // when final process of prepare pl is done
			    if($prepare_pl_date != ''){
					$final_deadline = $prepare_pl_date;
				}
			    
				// Keep the original final_deadline value for DB
				$final_deadline_date = !empty($final_deadline) ? \Carbon\Carbon::parse($final_deadline) : null;

				// Format for display
				$final_deadline_display = $final_deadline_date 
				    ? $final_deadline_date->format('d M Y') 
				    : 'N/A';
				

				$latestTask['color'] = $latestTask['color'] ?? '';

				// Determine label color based on latest task status
				//$actual_readiness_label_color = ($latestTask['color'] == 'red') ? 'ard_color_red' : '';	


			    // Convert to Carbon instances for accurate date comparison
			    $commited = \Carbon\Carbon::createFromFormat('d M Y', trim($commited_date));
				$asperschedule = \Carbon\Carbon::createFromFormat('d M Y', trim($final_deadline_display));

			    // if As per schedule Date is bigger
			    $actual_readiness_label_color = $asperschedule->gt($commited) ? 'ard_color_red' : '';
				

				//echo "<br> {$latestTask['color']} <br> {$actual_readiness_label_color}";

				

				// Insert into DB
				if ($final_deadline_date) {
				    DB::table('products_of_projects')
				        ->where('id', $val->id)
				        ->update([
				            'actual_readiness_date' => $final_deadline_date->format('Y-m-d H:i:s')
				        ]);
				} else {
				    DB::table('products_of_projects')
				        ->where('id', $val->id)
				        ->update(['actual_readiness_date' => null]);
				}


			@endphp
			<!-- Calculation As per schedule (Actual Readiness) Date - End -->

			<script>
			    @if(!empty($val->id))
			        var actual_html = '<div class="p-2 project_status_box text-center text-white {{ $actual_readiness_label_color }}"><span>As per schedule: {{ $final_deadline_display }}</span></div>';
			        document.getElementById("as_per_schedule_date_{{ $val->id }}").innerHTML = actual_html;
			    @endif
			</script>
			
		@endforeach

		<div id="PO_table">
			<div class="container-fluid mt-3 px-1">
				<div class="">
					<div class="d-flex justify-content-between align-items-center">
						<h3 class="pt-4 text-bold text-left text-uppercase">Purchase Orders for Project: {{$project->project_no}}</h3>
					</div>
					<hr class="mt-1" />
					@if($project->purchaseOrders->isNotEmpty())
					<div class="table-scroll-wrapper table-responsive">
						<table class="table table-hover table-bordered w-100 text-center" id="purchase_orders">
							<thead>
								<tr>
									<th scope="col">#</th>
									<th scope="col">PO No.</th>
									<th scope="col">Article No.</th>
									<th scope="col">Description</th>
									<th scope="col">Status</th>
									<th scope="col">Qty</th>
									<th scope="col">Received Assembly Date</th>
								</tr>
							</thead>
							<tbody>
								@php
								$counter = 1;
								@endphp
								@foreach($project->purchaseOrders as $purchaseOrder)
								@foreach($purchaseOrder->purchaseOrderTables as $poTable)
								<tr>
									<td>{{ $counter++ }}</td>
									<td>{{ $purchaseOrder->po_number }}</td>
									<td>{{ $poTable->artical_no }}</td>
									<td>{{ $poTable->description }}</td>
									<td>
										@php
										$responseTime = $poTable->response_time;
										$status = 'N/A';
										$class = '';
										if ($responseTime !== null) {
										$status = $responseTime >= 0 ? 'Received' : 'Delayed';
										$class = $status === 'Delayed' ? 'text-danger' : 'text-success';
										}
										@endphp
										<span class="{{ $class }}">
											{{ $status }}
										</span>
									</td>
									<td>{{ $poTable->quantity }}</td>
									<td>
										{{ $poTable->actual_readiness_date ? \Carbon\Carbon::parse($poTable->actual_readiness_date)->format('d M Y') : 'N/A' }}
									</td>
								</tr>
								@endforeach
								@endforeach
							</tbody>
						</table>
					</div>
					@else
					<div class="container-fluid">
						<div class="row mt-3">
							<div class="alert alert-info w-100">
								No purchase orders found for this project.
							</div>
						</div>
					</div>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
	$(document).on('click', '.download_excel_bom', function() {
		var csrf_token = "{{csrf_token()}}";
		var quotation_number = $(this).data('quotation_number');
		var full_article_number = $(this).data('article_number');
		var item_id = $(this).data('item_id');
		var item_name = $(this).data('item_name');
		var cart_model_name = $(this).data('cart_model_name');
		$.ajax({
			url: "{{route('getBOMForCheckStatus')}}",
			type: "POST",
			data: {
				cart_model_name: cart_model_name,
				quotation_number: quotation_number,
				full_article_number: full_article_number,
				item_id: item_id,
				item_name: item_name
			},
			headers: {
				'X-CSRF-TOKEN': csrf_token
			},
			success: function(response) {
				console.log(response);
				if (response.item_name == "Atmos") {
					let csvContent = "data:text/csv;charset=utf-8,";
					csvContent += "Quotation Number,Full Article Number,Item ID,Item Name\n";
					csvContent += `${quotation_number},${full_article_number},${item_id},${item_name}\n`;
					if (response.data.items && Array.isArray(response.data.items)) {
						let cart = response.data.atmosCart;
						if (cart.is_accesories_manual != "1") {
							csvContent += "\nItems:\n";
							csvContent += "Item Description,Article No., Adder code, Unit Price, Qty, Total Price \n";
							response.data.items.forEach(item => {
								csvContent += `${item.item_description},${item.wilo_artilce_no}, ,${item.unit_price},${item.qty},${item.total_price}\n`;
							});
						}
					}
					if (response.data.atmosBOMitems && Array.isArray(response.data.atmosBOMitems)) {
						let cart = response.data.atmosCart;
						if (cart.is_accesories_manual != "1") {
							csvContent += "\nAtmos BOM Items:\n";
							csvContent += "Item Description,Article No., Adder code, Unit Price, Qty, Total Price \n";
							response.data.atmosBOMitems.forEach(item => {
								if (item.item_description != "Assembly Cost" && item.item_description != "Testing Cost" && item.item_description != "Balancing Cost") {
									csvContent += `${item.item_description},${item.wilo_artilce_no}, ,${item.unit_price},${item.qty},${item.total_price}\n`;
								}
							});
						}
					}
					if (response.data.atmosBOMitemsSupervisor) {
						let cart = response.data.atmosCart;
						if (cart.is_accesories_manual != "1") {
							let supervisor = response.data.atmosBOMitemsSupervisor;
							csvContent += `${supervisor.item_description},${supervisor.wilo_artilce_no}, ,${supervisor.unit_price},${supervisor.qty},${supervisor.total_price}\n`;
						}
					}
					if (response.data && typeof response.data.adderData === "object") {
						let adderData = Object.values(response.data.adderData);
						if (adderData.length > 0) {
							csvContent += "\nAtmos Adder ids Details:\n";
							csvContent += "Item Description,Article No., Adder code, Unit Price, Qty, Total Price \n";
							adderData.forEach(item => {
								csvContent += `${item.name},'', ${item.id},${item.price},1,${item.price}\n`;
							});
						} else {
							console.warn("AdderData is empty after conversion.");
						}
					} else {
						console.warn("AdderData is not found or is not an object.");
					}
					if (response.data.atmosCart) {
						let cart = response.data.atmosCart;
						if (cart.is_bareshaft_selection != "1") {
							csvContent += "\nAtmos Cart Details:\n";
							csvContent += "Item Description,Article No., Adder code, Unit Price, Qty, Total Price \n";
							const itemDesc = `${cart.power}KW ${cart.no_of_pole}P ${cart.efficiency} ${cart.voltage}V ${cart.frequency}Hz ${cart.brand} ${cart.application==1?"constant":"Variable"} Speed`;
							csvContent += `${itemDesc}, -- , ,${cart.accesories_price},1,${cart.accesories_price}\n`;
						}
					}
					if (response.data.atmosCart) {
						let cart = response.data.atmosCart;
						if (cart.is_accesories_manual == "1") {
							csvContent += "\nAtmos Cart Details:\n";
							csvContent += "Item Description,Article No., Adder code, Unit Price, Qty, Total Price \n";
							csvContent += `Accessories-Manual, -- , ,${cart.accesories_price},1,${cart.accesories_price}\n`;
						}
					}
					if (response.data.atmosCart) {
						let cart = response.data.atmosCart;
						if (cart.is_accesories_manual == "1") {
							csvContent += "\nAtmos Cart Details:\n";
							csvContent += "Item Description,Article No., Adder code, Unit Price, Qty, Total Price \n";
							csvContent += `${cart.pump_name},${cart.full_article_number}, ,${cart.price},${cart.qty},${cart.total_price}\n`;
						}
					}
					let encodedUri = encodeURI(csvContent);
					let link = document.createElement("a");
					link.setAttribute("href", encodedUri);
					link.setAttribute("download", `BOM_${quotation_number}.csv`);
					document.body.appendChild(link);
					link.click();
					document.body.removeChild(link);
				}
				if (response.item_name == "SCP") {
					let csvContent = "data:text/csv;charset=utf-8,";
					csvContent += "Quotation Number,Full Article Number,Item ID,Item Name\n";
					csvContent += `${quotation_number},${full_article_number},${item_id},${item_name}\n`;
					if (response.data.items && Array.isArray(response.data.items)) {
						let cart = response.data.items;
						csvContent += "\nItems:\n";
						csvContent += "Item Description,Article No., Adder code, Unit Price, Qty, Total Price \n";
						response.data.items.forEach(item => {
							csvContent += `${item.item_description},${item.wilo_artilce_no}, ,${item.unit_price},${item.qty},${item.total_price}\n`;
						});
					}
					if (response.data && typeof response.data.adderData === "object") {
						let adderData = Object.values(response.data.adderData);
						if (adderData.length > 0) {
							csvContent += "\nSCP Adder ids Details:\n";
							csvContent += "Item Description,Article No., Adder code, Unit Price, Qty, Total Price \n";
							adderData.forEach(item => {
								csvContent += `${item.name},'', ${item.id},${item.price},1,${item.price}\n`;
							});
						} else {
							console.warn("AdderData is empty after conversion.");
						}
					} else {
						console.warn("AdderData is not found or is not an object.");
					}
					if (response.data) {
						let cart = response.data.scpCart;
						csvContent += "\nSCP Cart Details:\n";
						csvContent += "Item Description,Article No., Adder code, Unit Price, Qty, Total Price \n";
						const itemDesc = `${cart.power}KW ${cart.no_of_pole}P ${cart.efficiency} ${cart.voltage}V ${cart.frequency}Hz ${cart.brand} ${cart.application==1?"constant":"Variable"} Speed`;
						csvContent += `${itemDesc}, -- , ,${response.data.motor_price},1,${response.data.motor_price}\n`;
					}
					if (response.data) {
						let cart = response.data.scpCart;
						csvContent += "\nSCP Cart Details:\n";
						csvContent += "Item Description,Article No., Adder code, Unit Price, Qty, Total Price \n";
						csvContent += `${cart.pump_name}, ${response.data.article_number} , ,${cart.bare_pump_price},1,${cart.bare_pump_price}\n`;
					}
					let encodedUri = encodeURI(csvContent);
					let link = document.createElement("a");
					link.setAttribute("href", encodedUri);
					link.setAttribute("download", `BOM_${quotation_number}.csv`);
					document.body.appendChild(link);
					link.click();
					document.body.removeChild(link);
				}
			},
			error: function(xhr, status, error) {
				console.log(xhr.responseText);
			}
		});
	});
</script>

<script src="https://cdn.datatables.net/responsive/3.0.3/js/dataTables.responsive.min.js"></script>

<script>
    $(document).ready(function() {
        
        $('#purchase_orders').DataTable({
            "order": [
                [0, 'desc']
            ]
        });

        $('#purchase_orders').removeClass('dataTable');
     });

</script>
@endsection