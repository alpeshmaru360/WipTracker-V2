@extends('layouts.main')
@section('content')
<link href="{{ asset('css/production_manager.css') }}" rel="stylesheet" />
<div class="">
    <div class="row mt-3">
        <div class="col-xl-1 col-sm-1"></div>
        <div class="col-xl-6 col-sm-6">
            <div class="production_dashboard">
                <div class="">
                    <div class="text-center">
                        <span class="text-white fs-30"></span>
                    </div>
                    <div class="text-center">
                        <span class="text--small text-white fs-14">*Project No.*_*Project Name*</span>
                    </div>
                </div>
            </div>
        </div>
       <div class="col-xl-5 col-sm-5"></div>
    </div>

    <div class="row mt-3">
        <div class="col-xl-3 col-sm-3"></div>
        <div class="col-xl-2 col-sm-2">
            <div class="production_dashboard_first_column">
                <div class="">
                    <div class="text-center">
                        <span class="text-white fs-30"></span>
                    </div>
                    <div class="text-center">
                        <span class="text--small text-white fs-14">Project Name</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-sm-2">
            <div class="production_dashboard_second_column">
                <div class="">
                    <div class="text-center">
                        <span class="text-white fs-30"></span>
                    </div>
                    <div class="text-center">
                        <input type="text" name="project_name" placeholder="Project Name" class="project_input">
                    </div>
                </div>
            </div>
        </div>
       <div class="col-xl-4 col-sm-4"></div>
    </div>

    <div class="row mt-3">
        <div class="col-xl-3 col-sm-3"></div>
        <div class="col-xl-2 col-sm-2">
            <div class="production_dashboard_first_column">
                <div class="">
                    <div class="text-center">
                        <span class="text-white fs-30"></span>
                    </div>
                    <div class="text-center">
                        <span class="text--small text-white fs-14">Assembly Quotation Ref.</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-sm-2">
            <div class="production_dashboard_second_column">
                <div class="">
                    <div class="text-center">
                        <span class="text-white fs-30"></span>
                    </div>
                    <div class="text-center">
                        <input type="text" name="project_name" placeholder="Link to Pricing Tool" class="project_input">
                    </div>
                </div>
            </div>
        </div>
       <div class="col-xl-4 col-sm-4"></div>
    </div>

    <div class="row mt-3">
        <div class="col-xl-3 col-sm-3"></div>
        <div class="col-xl-2 col-sm-2">
            <div class="production_dashboard_first_column">
                <div class="">
                    <div class="text-center">
                        <span class="text-white fs-30"></span>
                    </div>
                    <div class="text-center">
                        <span class="text--small text-white fs-14">Sales Person</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-sm-2">
            <div class="production_dashboard_second_column">
                <div class="">
                    <div class="text-center">
                        <span class="text-white fs-30"></span>
                    </div>
                    <div class="text-center">
                    <input type="text" name="project_name" placeholder="Sales Person name xxxxx" class="project_input">
                    </div>
                </div>
            </div>
        </div>
       <div class="col-xl-4 col-sm-4"></div>
    </div>

    <div class="row mt-3">
        <div class="col-xl-3 col-sm-3"></div>
        <div class="col-xl-2 col-sm-2">
            <div class="production_dashboard_first_column">
                <div class="">
                    <div class="text-center">
                        <span class="text-white fs-30"></span>
                    </div>
                    <div class="text-center">
                        <span class="text--small text-white fs-14">Customer Name</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-sm-2">
            <div class="production_dashboard_second_column">
                <div class="">
                    <div class="text-center">
                        <span class="text-white fs-30"></span>
                    </div>
                    <div class="text-center">
                        <select name="project_name" placeholder="" class="project_input" style="background-color: #e3b6637a !important;">
                            <option>Drop Down</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
       <div class="col-xl-4 col-sm-4"></div>
    </div>

    <div class="row mt-3">
        <div class="col-xl-3 col-sm-3"></div>
        <div class="col-xl-2 col-sm-2">
            <div class="production_dashboard_first_column">
                <div class="">
                    <div class="text-center">
                        <span class="text-white fs-30"></span>
                    </div>
                    <div class="text-center">
                        <span class="text--small text-white fs-14">Customer Documents</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-sm-2">
            <div class="production_dashboard_second_column">
                <div class="">
                    <div class="text-center">
                        <span class="text-white fs-30"></span>
                    </div>
                    <div class="text-center">
                        <span class="text--small text-white fs-14">
                            <i class="fa fa-file text-dark
                            "></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
       <div class="col-xl-4 col-sm-4"></div>
    </div>

    <div class="row mt-3">
        <div class="col-xl-1 col-sm-1"></div>
        <div class="col-xl-3 col-sm-3">
            <div class="production_dashboard_first_column">
                <div class="">
                    <div class="text-center">
                        <span class="text-white fs-30"></span>
                    </div>
                    <div class="text-center">
                        <span class="text--small text-white fs-14">Request for Finance Approval</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-1 col-sm-1">
             <input type="checkbox" checked class="product_manager_checkbox" style="position: relative !important;">
        </div>
        <div class="col-xl-1 col-sm-1">
            <div class="production_dashboard_second_column">
                <div class="">
                    <div class="text-center">
                        <span class="text-white fs-30"></span>
                    </div>
                    <div class="text-center">
                        <span class="text--small text-white fs-14">Approved</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-1 col-sm-1">
            <div class="">
                <div class="text-center">
                    <input type="checkbox" checked class="product_manager_checkbox" style="position: relative !important;">
                </div>
            </div>
        </div>
       <div class="col-xl-4 col-sm-4"></div>
    </div>

<div class="container mt-5">
    <table class="table-striped table-bordered w-100">
        <thead>
            <th class="table_th_bg">SL. NO.</th>
            <th class="table_th_bg">ART</th>
            <th class="table_th_bg">DESCRIPTION</th>
            <th class="table_th_bg">QTY</th>
            <th class="table_th_bg">PRODUCT TYPE</th>
            <th class="table_th_bg">BOM</th>
            <th class="table_th_bg">DRAWING</th>
            <th class="table_th_bg">PARTIAL DELIVERY</th>
        </thead>
       <tbody>
            <tr>
                <td class="">xx</td>
                <td class="">xx</td>
                <td class="">xx</td>
                <td class="">xx</td>
                <td class="">(drop down)</td>
                <td class="">Attached</td>
                <td><span class="badge badge-primary custom-badge">Request</span></td>
                <td class=""><input type="checkbox" checked class="product_manager_checkbox" style="position: relative !important;"></td>
            </tr>
            <tr>
                <td class=""></td>
                <td class=""></td>
                <td class=""></td>
                <td class=""></td>
                <td class=""></td>
                <td class="">Attached</td>
                <td><span class="badge badge-primary custom-badge">Request</span></td>
                <td class=""></td>
            </tr>
            <tr>
                <td class=""></td>
                <td class=""></td>
                <td class=""></td>
                <td class=""></td>
                <td class=""></td>
                <td><span class="badge badge-primary custom-badge">Upload / Request</span></td>
                <td><span class="badge badge-primary custom-badge">Request</span></td>
                <td class=""></td>
            </tr>
            <tr>
                <td class=""></td>
                <td class=""></td>
                <td class=""></td>
                <td class=""></td>
                <td class=""></td>
                <td class=""></td>
                <td class=""></td>
                <td class=""></td>
            </tr>
            <tr>
                <td class=""></td>
                <td class=""></td>
                <td class=""></td>
                <td class=""></td>
                <td class=""></td>
                <td class=""></td>
                <td class=""></td>
                <td class=""></td>
            </tr>
        </tbody>
    </table>
</div>
</div>
@endsection

@section('scripts') 

@endsection