@extends('layouts.main')
@section('content')
<link href="{{ asset('css/operator.css') }}" rel="stylesheet" />
<div class="main_section bg-white m-4">
    <h3 class="ml-4 pt-4 text-bold text-left text-uppercase"></h3>
    <div class="m-3 mt-2  project_approval_section border">
        <h5 class="text-uppercase text-left mb-2 mt-3 ml-3 font-weight-bolder">
            Project Details</h5>
        <hr class="mx-3 mt-2 mb-2" />
        <div class="row ml-3">
            <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                <label class="form-label" for="">Project Name : </label>
            </div>
            <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                
            </div>
            <div class="col-3 col-md-6 col-lg-6 col-xl-2">
            </div>
        </div>

        <div class="row mt-0 ml-3">
            <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                <label class="form-label" for="">Customer Ref : </label>
            </div>
            <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                
            </div>
            <div class="col-3 col-md-6 col-lg-6 col-xl-2">
            </div>
        </div>

        <div class="row mt-0 ml-3">
            <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                <label class="form-label" for="">Project No. :  </label>
            </div>
            <div class="col-3 col-md-6 col-lg-6 col-xl-3">
               
            </div>
            <div class="col-3 col-md-6 col-lg-6 col-xl-2">
            </div>
        </div>
    </div>

    <div class="m-3 mt-4 pb-4 project_approval_section">
        <h5 class="text-uppercase text-left mb-2 mt-3 ml-3 font-weight-bolder">
            Project - Product Types</h5>
        <hr class="mx-3 mt-0" />


        <div class="row mx-auto">
       
        </div>
    </div>
</div>
@endsection