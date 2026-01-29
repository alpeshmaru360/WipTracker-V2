@extends('layouts.main')
@section('content')
<link rel="stylesheet" href="{{ asset('css/operator.css') }}" />

<div class="main_section bg-white mx-0 mx-md-2 mx-lg-4 my-4 pt-2">
    <div class="mx-3 my-2 project_approval_section border">
        <h5 class="text-uppercase text-left mt-2 mt-3 ml-3 font-weight-bolder">
            Project Details</h5>
        <hr class="mx-3 mt-2 mb-2" />

        <div class="row mt-0 ml-3 proj_detail pr-2">
            <div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xl-2">
                <label class="form-label" for="">Project No. : </label>
            </div>
            <div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3">
            {{$projects->project_no}}
            </div>
        </div>
        <div class="row ml-3 proj_detail pr-2">
            <div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xl-2">
                <label class="form-label" for="">Project Name : </label>
            </div>
            <div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3">
                {{$projects->project_name}}
            </div>
        </div>
        <div class="row mt-0 ml-3 proj_detail pr-2">
            <div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xl-2">
                <label class="form-label" for="">Estimated Date : </label>
            </div>
            <div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3">
                {{$projects->estimated_readiness ? \Carbon\Carbon::parse($projects->estimated_readiness)->format('d-M-Y') : 'N/A'}}
            </div>
        </div>

    </div>
    <div class="m-3 mt-4 pb-4 project_approval_section">
        <h5 class="text-uppercase text-left mb-2 mt-3 ml-3 font-weight-bolder">
            Products</h5>
        <hr class="mx-3 mt-0" />
      
        @php
        $isClickable = false;
        @endphp

        @if(Auth()->check() && Auth()->user()->id == $product_operator_id)
        @php
        $isClickable = true;
        @endphp
        @endif
        
        @php
            $processes = DB::table('project_process_std_time')
            ->where('projects_id', $projects->id)
            ->orderBy('id', 'desc')
            ->groupBy('order_qty')
            ->get();
        @endphp

        <div class="row mx-auto">
            @foreach($products as $key => $val)
            <div class="col-xl-6 ml-3">
                <!-- Auth Condition Appied -->                
                <a href="" class="text-decoration-none text-muted" style="pointer-events: none; opacity:1;">
                    <div class="pb-2 pt-1">
                        <div class="">
                            <div class="primary_bg_color br-12 p-2 text-white">
                                <span class="fs-13 text-uppercase px-2 text-white">
                                </span>
                                <span class="fs-1 px-2 text-white">
                                    <a href="javascript:void(0);" class="toggle-btn" data-full_article_number="{{$val->full_article_number}}">
                                        @if($val->qty > 1)
                                            <i class="fa fa-plus text-white mr-2"></i>
                                        @endif
                                    </a>
                                    {{$val->full_article_number ?? 'N/A'}} -- TOTAL QTY :  {{ $val->qty }} -- {{$val->description ?? 'N/A'}}
                                </span>
                            </div>
                        </div>
                    </div>

                    @php
                        $groupedProducts = $val->projectProcessStdTimes->groupBy('order_qty')->map(function($group) {
                                                                                                return $group->sortByDesc('id');
                                                                                             });
                    @endphp
                    
                    <div id="details-{{$val->full_article_number}}-subQty" class="details ml-5">
                        @foreach($groupedProducts as $orderQty => $grp_val)
                        <div class="pb-2 sub_qty_section">
                            <div class="primary_bg_color br-12 p-2">
                                <span class="fs-1 px-2 text-white">
                                      {{$val->full_article_number ?? 'N/A'}} -- {{ str_pad($grp_val[0]->order_qty, 3, '0', STR_PAD_LEFT) }} -- {{$val->description ?? 'N/A'}}
                                    
                                    @php
                                        $process_count = 1;
                                    @endphp
                                    @foreach($grp_val as $key => $process)
                                        @if($process->project_status == "1" && $process_count == "1")
                                            @php
                                                $process_count = 2;
                                            @endphp
                                            <br>
                                            Last action completed : {{$process->project_process_name}} :
                                            {{getAgoTime($process->timer_ends_at)}}
                                        @endif
                                    @endforeach
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@section('scripts') 
<script type="text/javascript">
    $(document).ready(function(){
        $('.details').hide();
        $('.toggle-btn').on('click',function() {
            const full_article_number = $(this).data('full_article_number');
            const targetSection = $('#details-' + full_article_number + '-subQty');
            if(targetSection.is(':visible')) {
                targetSection.hide();
            } else {
                $('.details').hide(); // Hide all other sections
                targetSection.show(); // Show the clicked section
            }
        });
    });
</script>
@endsection