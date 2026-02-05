@extends('layouts.main')
@section('content')
<link rel="stylesheet" href="{{ asset('css/operator.css') }}" />

<div class="product_qr_page main_section bg-white mx-0 mx-md-4 my-4 py-2">
    <div class="m-3 mt-2 project_approval_section border">
        <h5 class="text-uppercase text-left mb-2 mt-3 ml-3 font-weight-bolder">
            Project Details
        </h5>
        <hr class="mx-3 mt-2 mb-2" />

        <div class="row mt-0 ml-3 proj_detail pr-2">
            <div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xl-2">
                <label class="form-label" for="">Project No. : </label>
            </div>
            <div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3">
                {{$project->project_no}}
            </div>
        </div>

        <div class="row ml-3 proj_detail pr-2">
            <div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xl-2">
                <label class="form-label" for="">Project Name : </label>
            </div>
            <div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3">
                {{$project->project_name}}
            </div>
        </div>

        <div class="row mt-0 ml-3 proj_detail pr-2">
            <div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xl-2">
                <label class="form-label" for="">Estimated Date : </label>
            </div>
            <div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3">
                {{$project->estimated_readiness ? \Carbon\Carbon::parse($project->estimated_readiness)->format('d-M-Y') : 'N/A'}}
            </div>
        </div>
    </div>

    <!-- Product Details Section -->
    <div class="m-3 mt-4 pb-2 project_approval_section border">
        <h5 class="text-uppercase text-left mb-2 mt-3 ml-3 font-weight-bolder">
            Product Details
        </h5>
        <hr class="mx-3 mt-2 mb-2" />

        <div class="row mt-0 ml-3 proj_detail pr-2">
            <div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xl-2">
                <label class="form-label" for="">Article No. : </label>
            </div>
            <div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3">
                {{$product->full_article_number}}
            </div>
        </div>

        <div class="row ml-3 proj_detail pr-2">
            <div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xl-2">
                <label class="form-label" for="">Description : </label>
            </div>
            <div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3">
                {{$product->description ?? 'N/A'}}
            </div>
        </div>

        <div class="row ml-3 proj_detail pr-2">
            <div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xl-2">
                <label class="form-label" for="">Quantity : </label>
            </div>
            <div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3">
                @if($qty_index)
                Qty {{ $qty_index }} of {{ $totalQty }}
                @else
                Total: {{ $totalQty }}
                @endif
            </div>
        </div>

        <div class="row ml-3 proj_detail">
            <div class="col-6 col-sm-5 col-md-3 col-lg-2 col-xl-2">
                <label class="form-label" for="">Last Action Completed : </label>
            </div>
            <div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3">
                @if($lastAction)
                {{ $lastAction->project_process_name }} : {{ getAgoTime($lastAction->timer_ends_at) }}
                @else
                N/A
                @endif
            </div>
        </div>

        @php
            $isClickable = false;
            $assigned_product_wise_operatorID = 0;
            $product_assigned = \App\Models\AssignedProductToOperator::with('product')
                ->whereRaw("FIND_IN_SET(?, operator_id)", [Auth()->user()->id])
                ->where('product_id', $product->id)
                ->where('order_qty', $qty_index)
                ->first();
                if($product_assigned){
                    $isClickable = true;
                    $assigned_product_wise_operatorID = $product_assigned->operator_id;
                    $operatorIDs = explode(',', $assigned_product_wise_operatorID);
                    if (in_array(Auth()->user()->id, $operatorIDs)) {
                        $isClickable = true;
                    }
            }
        @endphp
        <div class="row ml-3 proj_detail">
            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6 pr-5">
                    <a 
                        href="{{ $isClickable ? route('OperatorProductTypeProcessQRCode', ['product_id' => $product->id, 'project_type_name' => $product->product_type, 'seq_qty' => $qty_index]) : '#' }}" 
                        class="text-decoration-none {{ $isClickable ? '' : 'opacity-80' }}" 
                        style="{{ $isClickable ? '' : 'pointer-events: none; opacity: 0.8;' }}"
                    >
                    <div class="pb-2 pt-1">
                        <div class="">
                            <div class="primary_bg_color br-12 p-2">
                                <span class="fs-13 text-uppercase px-2 text-white">
                                    {{$product->full_article_number}} -- {{ str_pad($qty_index, 3, '0', STR_PAD_LEFT) }} -- {{$product->description}} 
                                </span>

                                <span class="fs-1 px-2 text-white">                                        
                                    <br>
                                    @if($lastAction)
                                        Last action completed : {{ $lastAction->project_process_name }} : {{ getAgoTime($lastAction->timer_ends_at) }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        @if(Auth()->check() && Auth()->user()->id)
            @php
                $isClickable = true;
            @endphp
        @endif        
    </div>

    <!-- New Section: Quantity Details Table -->
    <div class="m-3 mt-4 project_approval_section border">
        <h5 class="text-uppercase text-left mb-2 mt-3 ml-3 font-weight-bolder">
            Quantity Details
        </h5>
        <!-- <hr class="mx-3 mt-2 mb-2" /> -->

        @if($qty_index && isset($groupedProcesses[$qty_index]))
        <div class="overflow-auto mx-3">
            <table class="table table-hover table-border w-100 text-center" id="project_table">
                <thead>
                    <tr>
                        <th scope="col" class="project_table_heading">Qty No</th>
                        <th scope="col" class="project_table_heading">Project creation</th>
                        <th scope="col" class="project_table_heading">BOM, drawings</th>
                        <th scope="col" class="project_table_heading">Check the BOM and place PO</th>                        
                        <th scope="col" class="project_table_heading">Initial Inspection</th>
                        @foreach($processNames as $processName)
                        <th scope="col" class="project_table_heading">{{ $processName }}</th>
                        @endforeach
                        <th scope="col" class="project_table_heading">Final inspection</th>
                        <th scope="col" class="project_table_heading">Prepare PL</th>
                        <th scope="col" class="project_table_heading">Sent PL to OM</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $processes = $groupedProcesses[$qty_index];
                    @endphp
                    <tr>
                        <td>{{ $qty_index }}</td>
                        <td>
                            <span style="color: {{ $check_status_project_create['hours_difference'] > $check_status_project_create['deadline_time'] ? 'red' : 'green' }}">
                                {{ \Carbon\Carbon::parse($project->projectStatus->first()->project_creation)->format('d F Y h:i A') }}
                            </span>
                        </td>
                        <td>
                            @php
                            $bomDate = '--';
                            if ($project->is_pricing_tool_quotation_number == 1 && !empty($project->created_at)) {
                            $bomDate = \Carbon\Carbon::parse($project->created_at)->format('d F Y h:i A');
                            } elseif ($project->is_pricing_tool_quotation_number == 0 && !empty($product->bom_upload_date)) {
                            $bomDate = \Carbon\Carbon::parse($product->bom_upload_date)->format('d F Y h:i A');
                            }
                            @endphp
                            BOM: {{ $bomDate }}<br>
                            Drawing: {{ $product->drawing_upload_date ? \Carbon\Carbon::parse($product->drawing_upload_date)->format('d F Y h:i A') : '--' }}
                        </td>
                        <td>
                            {{ $latest_create_po_date ? \Carbon\Carbon::parse($latest_create_po_date->created_at)->format('d F Y h:i A') : '--' }}
                        </td>                        
                        <td>
                            @php
                            $intailinspectionDate = $initial_inspection
                            ? \Carbon\Carbon::parse($initial_inspection)->format('d F Y h:i A')
                            : '--';
                            @endphp
                            {{ $intailinspectionDate }}
                        </td>
                        @foreach($processNames as $processName)
                        @php
                        $process = $processes->firstWhere('project_process_name', $processName);
                        $status = $process ? $process->project_status : null;
                        @endphp
                        <td>
                            @if($status !== null)
                            @if($status == 1)
                            <i class="fas fa-check text-success"></i>
                            @else
                            <i class="fas fa-times text-danger"></i>
                            @endif
                            @else
                            -
                            @endif
                        </td>
                        @endforeach
                        <td>
                            @php
                            $finalinspectionDate = $final_inspection
                            ? \Carbon\Carbon::parse($final_inspection)->format('d F Y h:i A')
                            : '--';
                            @endphp
                            {{ $finalinspectionDate }}
                        </td>
                        <td>
                            @php
                            $plPdfPathTime = $project->PL_PDF_path
                            ? \Carbon\Carbon::parse($project->created_at)->format('d F Y h:i A')
                            : '--';
                            @endphp
                            {{ $plPdfPathTime }}
                        </td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
        @else
        <div class="alert alert-warning text-center">
            No details available for this quantity.
        </div>
        @endif
    </div>
</div>
@endsection