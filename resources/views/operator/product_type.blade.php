@extends('layouts.main')
@section('content')
<link rel="stylesheet" href="{{ asset('css/operator.css') }}" />
<div class="main_section bg-white mx-0 mx-md-4 my-4 px-0 py-0 px-md-4 py-md-4 ">

    <div class="d-flex justify-content-between px-4 pt-4">
        <a href="{{ Auth::check() ? route('OperatorDashboard') : '#' }}" 
            class="project_icon p-2 m-1 text-decoration-none {{ Auth::check() ? '' : 'd-none' }}">
            <i class="fa fa-arrow-left project_icon"></i><span class="text-white">&nbsp; BACK</span>
        </a> 
    </div>   
    <h3 class="ml-4 pt-4 text-bold text-left text-uppercase">{{$page_title}}</h3>

    <div class="mx-4 my-2 project_approval_section border">
        <h5 class="text-uppercase text-left mb-2 mt-3 ml-3 font-weight-bolder">
            Project Details</h5>
        <hr class="mx-0 mt-2 mb-2" />
        <div class="row mt-0 ml-3">
            <div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xl-2">
                <label class="form-label" for="">Project No. : </label>
            </div>
            <div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3">
                {{$projects->projects['project_no']}}
            </div>
        </div>
        <div class="row ml-3">
            <div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xl-2">
                <label class="form-label" for="">Project Name : </label>
            </div>
            <div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3">
                {{$projects->projects['project_name']}}
            </div>
        </div>

        <div class="row mt-0 ml-3">
            <div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xl-2">
                <label class="form-label" for="">Estimated Date : </label>
            </div>
            <div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3">
                {{$projects->projects['estimated_readiness'] ?
                \Carbon\Carbon::parse($projects->projects['estimated_readiness'])->format('d-M-Y') : 'N/A'}}
            </div>
        </div>
    </div>


    <div class="mx-4 my-2 pb-4 project_approval_section">
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
 
        @foreach($product as $key => $val)
            @php
                $processes = DB::table('project_process_std_time')
                    ->where('projects_id', $projects->project_id) 
                    ->where('product_id', $projects->id) 
                    ->where('order_qty', $val->seq_qty) 
                    ->where('project_status', '1')
                    ->orderBy('id', 'desc')
                    ->first();
            @endphp
        <div class="row mx-auto">
            <div class="col-xl-6">             

                @if($isClickable) 
                @endif
                <a href="{{ route('OperatorProductTypeProcess', ['product_id' => $projects->id, 'project_type_name' => $projects->product_type,'seq_qty' => $val->seq_qty]) }}" class="text-decoration-none">

                    <div class="pb-2 pt-1">
                        <div class="">
                            <div class="primary_bg_color br-12 p-2">
                                <span class="fs-13 text-uppercase px-2 text-white">
                                    {{$projects->full_article_number}} -- {{ str_pad($val->order_qty, 3, '0', STR_PAD_LEFT) }} -- {{$projects->description}} 
                                </span>

                                <span class="fs-1 px-2 text-white">                                        
                                    <br>
                                    @if($processes)
                                        Last action completed : {{$processes->project_process_name}} : 
                                        {{getAgoTime($processes->timer_ends_at)}}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xl-6 ml-3 mb-2">
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

