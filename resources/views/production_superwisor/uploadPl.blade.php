@extends('layouts.main')
@section('content')
<link href="{{ asset('css/production_manager.css') }}" rel="stylesheet" />
<style>
    .is-invalid {border-color: #dc3545 !important;background-color: #fff0f0;}
    .add-row-container {margin-top: 1rem;}
    .select2-container {width: 100% !important;}
    .select2-container--default .select2-selection--single {height: 38px;border: 1px solid #ced4da;border-radius: 4px;}
    .select2-container--default .select2-selection--single .select2-selection__rendered {line-height: 38px;padding-left: 10px;color: #495057;}
    .select2-container--default .select2-selection--single .select2-selection__arrow {height: 38px;right: 5px;}
    .select2-container--default .select2-selection--single.is-invalid {border-color: #dc3545 !important;}
    .remove-row {padding: 2px 8px;margin-left: 5px;border: none;}
    .input-group {display: flex;align-items: center;}
    .select2-results__option {padding: 6px 10px;}
    .select2-results__option--highlighted {background-color: #007bff !important;color: white !important;}
    body.menu_side_present .body_wrap{overflow : unset !important;}
</style>
<section class="bg-image mt-4">
    <div class="mask d-flex align-items-center h-100 gradient-custom-3">
        <div class="container h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-12 col-md-9 col-lg-7 col-xl-12">
                    <div class="card" style="border-radius: 15px;">
                        <div class="card-body p-4">
                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif
                            <form action="{{route('ProductionManagerProjectCreateDo')}}" enctype="multipart/form-data"
                                method="post">
                                @csrf

                                <div class="row mt-3">
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                        <label class="form-label" for="">Customer Documents<span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                     
                                    </div>
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                    </div>
                                </div>  
                                <div class="d-flex justify-content-center mt-2">
                                    <button type="submit" class="btn btn-lg">Create</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


@endsection

@section('scripts')
@endsection
