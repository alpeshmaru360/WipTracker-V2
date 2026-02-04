@extends('layouts.main')
@section('content')

<link rel="stylesheet" href="{{ asset('css/admin.css') }}" />
<link rel="stylesheet" href="{{ asset('css/admin_settings.css') }}" />

<div class="admin_settings_page main_container d-flex">
    @include('layouts.setting')
    <div class="main-content  w-75" >
    <section class="bg-image mt-4">
        <div class="mask d-flex align-items-center h-100 gradient-custom-3">
            <div class="container h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-12 col-md-9 col-lg-7 col-xl-12">
                <div class="card main_content_card">
                    <div class="card-body p-4">
                        <h2 class="text-uppercase text-left font-weight-bolder">{{$page_title}}</h2>
                        <hr class="mt-3" />
                        @if(session('success'))
                            <div class="alert alert-success mt-3">
                                {{ session('success') }}
                            </div>
                        @endif
                            <form method="POST" action="{{ route('AdminSettings') }}">
                            @csrf
                            <!-- <div class="project_approval_section border mt-3">
                                <h5 class="text-uppercase text-left mb-2 mt-3 ml-3 font-weight-bolder">Project Approval ( <span class="text-danger">*</span> Write in HOURS)</h5>
                                <hr class="mx-3 mt-0" />
                                <div class="row mt-3 m-4">
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                        <label class="form-label" for="">Email To Finance Manager<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                        <input required type="number" step="0.001" min = "0" id="" class="w-100 pb-2 pt-2"/>
                                    </div>
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                    </div>
                                </div>

                                <div class="row mt-3 m-4">
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                        <label class="form-label" for="">Email To Sales<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                        <input required type="number" step="0.001" min = "0" class="w-100 pb-2 pt-2"/>
                                    </div>
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                    </div>
                                </div>
                            </div>

                            <div class="project_approval_section border mt-3">
                                <h5 class="text-uppercase text-left mb-2 mt-3 ml-3 font-weight-bolder">PO Approval ( <span class="text-danger">*</span> Write in HOURS)</h5>
                                <hr class="mx-3 mt-0" />
                                <div class="row mt-3 m-4">
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                        <label class="form-label">Inbox To Assembly Manager<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                        <input required type="number" step="0.001" class="w-100 pb-2 pt-2"/>
                                    </div>
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                    </div>
                                </div>

                                <div class="row mt-3 m-4">
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                        <label class="form-label" for="">Email To Finance Manager<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                        <input required type="number" step="0.001" class="w-100 pb-2 pt-2"/>
                                    </div>
                                    <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                    </div>
                                </div>
                            </div> -->
                            <div id="standardProcessTimesAccordion" class="accordion mt-3">
                                <div class="card">
                                    <div class="card-header" id="headingThree">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link text-uppercase cutome_collapsed font-weight-bolder d-flex align-items-center" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree" type="button">
                                                STANDARD PROCESS TIMES (<span class="text-danger">* </span> Write in HOURS)
                                                <span class="ml-2"><i class="fa fa-chevron-down arrow-icon"></i></span>
                                            </button>
                                        </h5>
                                    </div>
                                    
                                    <div id="collapseThree" class="collapse " aria-labelledby="headingThree" data-parent="#standardProcessTimesAccordion">
                                        <div class="card-body">
                                                <div class="row mt-3 m-4">
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                        <label class="form-label">Create new project <span class="text-danger">*</span></label>
                                                    </div>
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                        <input required type="number" step="0.001" name="create_new_project" class="w-100 pb-2 pt-2" value="{{$create_new_project}}"/>
                                                    </div>
                                                </div>
                                                <div class="row mt-3 m-4">
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                        <label class="form-label" for="">BOM, Drawings <span class="text-danger">*</span></label>
                                                    </div>
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                        <input required type="number" step="0.001" class="w-100 pb-2 pt-2" name = "bom_drawings" value="{{$bom_drawings}}" />
                                                    </div>
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                                    </div>
                                                </div>

                                                <div class="row mt-3 m-4">
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                        <label class="form-label" for="">Check the BOM and place PO (after getting approvals from Manager etc.)<span class="text-danger">*</span></label>
                                                    </div>
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                        <input required type="number" step="0.001" name="check_the_bom_and_place_po" class="w-100 pb-2 pt-2" value="{{$check_the_bom_and_place_po}}" />
                                                    </div>
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                                    </div>
                                                </div>

                                                <div class="row mt-3 m-4">
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                        <label class="form-label" for="">Gather OA from supplier<span class="text-danger">*</span></label>
                                                    </div>
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                        <input required type="number" step="0.001" name="gather_qa_from_supplier" class="w-100 pb-2 pt-2" value="{{$gather_qa_from_supplier}}" />
                                                    </div>
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                                    </div>
                                                </div>

                                                <div class="row mt-3 m-4">
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                        <label class="form-label" for="">Record OA <span class="text-danger">*</span></label>
                                                    </div>
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                        <input required type="number" step="0.001" name="record_qa" value = "{{$record_qa}}" class="w-100 pb-2 pt-2"/>
                                                    </div>
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                                    </div>
                                                </div>

                                                <div class="row mt-3 m-4">
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                        <label class="form-label" for="">Inform customer about material readiness <span class="text-danger">*</span></label>
                                                    </div>
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                        <input required type="number" step="0.001" name="inform_customer_about_material_readiness" class="w-100 pb-2 pt-2" value="{{$inform_customer_about_material_readiness}}" />
                                                    </div>
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                                    </div>
                                                </div>

                                                <div class="row mt-3 m-4">
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                        <label class="form-label" for="">Initial Inspection <span class="text-danger">*</span></label>
                                                    </div>
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                        <input required type="number" step="0.001" name="initial_inspection" value="{{$initial_inspection}}" class="w-100 pb-2 pt-2"/>
                                                    </div>
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                                    </div>
                                                </div>

                                                <div class="row mt-3 m-4">
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                        <label class="form-label" for="">NCR creation  <span class="text-danger">*</span></label>
                                                    </div>
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                        <input required type="number" step="0.001" name="ncr_creation" class="w-100 pb-2 pt-2" value="{{$ncr_creation}}" />
                                                    </div>
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                                    </div>
                                                </div>

                                                <div class="row mt-3 m-4">
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                        <label class="form-label" for="">NCR closing time <span class="text-danger">*</span></label>
                                                    </div>
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                        <input required type="number" step="0.001" name="ncr_closing_time" value="{{$ncr_closing_time}}" class="w-100 pb-2 pt-2"/>
                                                    </div>
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                                    </div>
                                                </div>

                                                <div class="row mt-3 m-4">
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                        <label class="form-label" for="">Final inspection <span class="text-danger">*</span></label>
                                                    </div>
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                        <input required type="number" step="0.001" name="final_inspection" class="w-100 pb-2 pt-2" value="{{$final_inspection}}" />
                                                    </div>
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                                    </div>
                                                </div>

                                                <div class="row mt-3 m-4">
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                        <label class="form-label" for="">Prepare PL <span class="text-danger">*</span></label>
                                                    </div>
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                        <input required type="number" step="0.001" name="prepare_pl" value="{{$prepare_pl}}" class="w-100 pb-2 pt-2"/>
                                                    </div>
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                                    </div>
                                                </div>

                                                <div class="row mt-3 m-4">
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                        <label class="form-label" for="">Sent PL to OM  <span class="text-danger">*</span></label>
                                                    </div>
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                        <input required type="number" step="0.001" name="sent_pl_to_om" value ="{{$sent_pl_to_om}}" class="w-100 pb-2 pt-2"/>
                                                    </div>
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                                    </div>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            

                            <div id="normPumpAccordion" class="accordion mt-3">
                                <div class="card">
                                    <div class="card-header" id="headingFour">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link text-uppercase cutome_collapsed font-weight-bolder d-flex align-items-center" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour" type="button">
                                                Norm Pump - Motor Alignment ( <span class="text-danger">* </span> Write in HOURS)
                                                <span class="ml-2"><i class="fa fa-chevron-down arrow-icon"></i></span>
                                            </button>
                                        </h5>
                                    </div>
                                    
                                    <div id="collapseFour" class="collapse " aria-labelledby="collapseFour" data-parent="#normPumpAccordion">
                                        <div class="card-body">
                                                <div class="row mt-3 m-4">
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                        <label class="form-label">Gather all material for assembly <span class="text-danger">*</span></label>
                                                    </div>
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                        <input required type="number" step="0.001" name="A1.1" class="w-100 pb-2 pt-2" value="{{$a1_1}}"/>
                                                    </div>
                                                </div>

                                                <div class="row mt-3 m-4">
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                        <label class="form-label">Air test <span class="text-danger">*</span></label>
                                                    </div>
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                        <input required type="number" step="0.001" name="A1.2" class="w-100 pb-2 pt-2" value="{{$a1_2}}"/>
                                                    </div>
                                                </div>

                                                <div class="row mt-3 m-4">
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                        <label class="form-label">Painting <span class="text-danger">*</span></label>
                                                    </div>
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                        <input required type="number" step="0.001" name="A1.3" class="w-100 pb-2 pt-2" value="{{$a1_3}}"/>
                                                    </div>
                                                </div>

                                                <div class="row mt-3 m-4">
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                        <label class="form-label">Pump motor alignment <span class="text-danger">*</span></label>
                                                    </div>
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                        <input required type="number" step="0.001" name="A1.4" class="w-100 pb-2 pt-2" value="{{$a1_4}}"/>
                                                    </div>
                                                </div>

                                                <div class="row mt-3 m-4">
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                        <label class="form-label">Prepare main label <span class="text-danger">*</span></label>
                                                    </div>
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                        <input required type="number" step="0.001" name="A1.5" class="w-100 pb-2 pt-2" value="{{$a1_5}}"/>
                                                    </div>
                                                </div>

                                                <div class="row mt-3 m-4">
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                        <label class="form-label">Fix nameplate <span class="text-danger">*</span></label>
                                                    </div>
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                        <input required type="number" step="0.001" name="A1.6" class="w-100 pb-2 pt-2" value="{{$a1_6}}"/>
                                                    </div>
                                                </div>

                                                <div class="row mt-3 m-4">
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                        <label class="form-label">Pump Hydraulic Testing <span class="text-danger">*</span></label>
                                                    </div>
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                        <input required type="number" step="0.001" name="A1.7" class="w-100 pb-2 pt-2" value="{{$a1_7}}"/>
                                                    </div>
                                                </div>

                                                <div class="row mt-3 m-4">
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                        <label class="form-label">Packing <span class="text-danger">*</span></label>
                                                    </div>
                                                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                        <input required type="number" step="0.001" name="A1.8" class="w-100 pb-2 pt-2" value="{{$a1_8}}"/>
                                                    </div>
                                                </div>
                                        
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="splitCasePumpAccordion" class="accordion mt-3">
                                <div class="card">
                                    <div class="card-header" id="headingFour">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link text-uppercase cutome_collapsed font-weight-bolder d-flex align-items-center" data-toggle="collapse" data-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive" type="button">
                                                Split Case Horizontal Pump - Motor Alignment ( <span class="text-danger">* </span> Write in HOURS)
                                                <span class="ml-2"><i class="fa fa-chevron-down arrow-icon"></i></span>
                                            </button>
                                        </h5>
                                    </div>
                                    
                                    <div id="collapseFive" class="collapse" aria-labelledby="headingFour" data-parent="#splitCasePumpAccordion">
                                        <div class="card-body">
                                            <div class="row mt-3 m-4">
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                    <label class="form-label">Gather all material for assembly <span class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                    <input required type="number" step="0.001" name="A2.1" class="w-100 pb-2 pt-2" value="{{$a2_1}}"/>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                                </div>
                                            </div>
                                            <div class="row mt-3 m-4">
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                    <label class="form-label" for="">Air test <span class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                    <input required type="number" step="0.001" name="A2.2" class="w-100 pb-2 pt-2" value="{{$a2_2}}"/>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                                </div>
                                            </div>

                                            <div class="row mt-3 m-4">
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                    <label class="form-label" for="">Painting<span class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                    <input required type="number" step="0.001" name="A2.3" class="w-100 pb-2 pt-2" value="{{$a2_3}}"/>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                                </div>
                                            </div>

                                            <div class="row mt-3 m-4">
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                    <label class="form-label" for="">Pump motor alignment <span class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                    <input required type="number" step="0.001" name="A2.4" class="w-100 pb-2 pt-2" value="{{$a2_4}}"/>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                                </div>
                                            </div>

                                            <div class="row mt-3 m-4">
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                    <label class="form-label" for="">Prepare main label <span class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                    <input required type="number" step="0.001" name="A2.5" class="w-100 pb-2 pt-2" value="{{$a2_5}}"/>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                                </div>
                                            </div>

                                            <div class="row mt-3 m-4">
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                    <label class="form-label" for="">Fix nameplate <span class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                    <input required type="number" step="0.001" name="A2.6" class="w-100 pb-2 pt-2" value="{{$a2_6}}"/>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                                </div>
                                            </div>

                                            <div class="row mt-3 m-4">
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                    <label class="form-label" for="">Pump Hydraulic Testing <span class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                    <input required type="number" step="0.001" name="A2.7" class="w-100 pb-2 pt-2" value="{{$a2_7}}"/>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                                </div>
                                            </div>

                                            <div class="row mt-3 m-4">
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                    <label class="form-label" for="">Packing <span class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                    <input required type="number" step="0.001" name="A2.8" class="w-100 pb-2 pt-2" value="{{$a2_8}}"/>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="boosterSetAccordion" class="accordion mt-3">
                                <div class="card">
                                    <div class="card-header" id="headingSix">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link text-uppercase cutome_collapsed font-weight-bolder d-flex align-items-center" data-toggle="collapse" data-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix" type="button">
                                                Booster Set Assembly ( <span class="text-danger">*</span> Write in HOURS)
                                                <span class="ml-2"><i class="fa fa-chevron-down arrow-icon"></i></span>
                                            </button>
                                        </h5>
                                    </div>
                                    
                                    <div id="collapseSix" class="collapse" aria-labelledby="headingSix" data-parent="#boosterSetAccordion">
                                        <div class="card-body">
                                            <div class="row mt-3 m-4">
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                    <label class="form-label">Gather all material for assembly <span class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                    <input required type="number" step="0.001" name="A3.1" class="w-100 pb-2 pt-2" value="{{$a3_1}}"/>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                                </div>
                                            </div>     
                                            <div class="row mt-3 m-4">
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                    <label class="form-label" for="">Cross check BOM<span class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                    <input required type="number" step="0.001" name="A3.2" class="w-100 pb-2 pt-2" value="{{$a3_2}}"/>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                                </div>
                                            </div>

                                            <div class="row mt-3 m-4">
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                    <label class="form-label" for="">Assembly of baseframe<span class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                    <input required type="number" step="0.001" name="A3.3" class="w-100 pb-2 pt-2" value="{{$a3_3}}"/>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                                </div>
                                            </div>

                                            <div class="row mt-3 m-4">
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                    <label class="form-label" for="">Suction Manifold assembly <span class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                    <input required type="number" step="0.001" name="A3.4" class="w-100 pb-2 pt-2" value="{{$a3_4}}"/>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                                </div>
                                            </div>

                                            <div class="row mt-3 m-4">
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                    <label class="form-label" for="">Discharge manifold assembly <span class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                    <input required type="number" step="0.001" name="A3.5" class="w-100 pb-2 pt-2" value="{{$a3_5}}"/>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                                </div>
                                            </div>

                                            <div class="row mt-3 m-4">
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                    <label class="form-label" for="">Assembly of adders <span class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                    <input required type="number" step="0.001" name="A3.6" class="w-100 pb-2 pt-2" value="{{$a3_6}}"/>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                                </div>
                                            </div>

                                            <div class="row mt-3 m-4">
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                    <label class="form-label" for="">Air test<span class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                    <input required type="number" step="0.001" name="A3.7" class="w-100 pb-2 pt-2" value="{{$a3_7}}"/>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                                </div>
                                            </div>

                                            <div class="row mt-3 m-4">
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                    <label class="form-label" for="">Assembly of cabinet <span class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                    <input required type="number" step="0.001" name="A3.8" class="w-100 pb-2 pt-2" value="{{$a3_8}}"/>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                                </div>
                                            </div>

                                            <div class="row mt-3 m-4">
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                    <label class="form-label" for="">Panel testing<span class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                    <input required type="number" step="0.001" name="A3.9" class="w-100 pb-2 pt-2" value="{{$a3_9}}"/>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                                </div>
                                            </div>

                                            <div class="row mt-3 m-4">
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                    <label class="form-label" for="">Panel termination <span class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                    <input required type="number" step="0.001" name="A3.10" class="w-100 pb-2 pt-2" value="{{$a3_10}}"/>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                                </div>
                                            </div>

                                            <div class="row mt-3 m-4">
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                    <label class="form-label" for="">Cabling<span class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                    <input required type="number" step="0.001" name="A3.11" class="w-100 pb-2 pt-2" value="{{$a3_11}}"/>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                                </div>
                                            </div>

                                            <div class="row mt-3 m-4">
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                    <label class="form-label" for="">Prepare main label <span class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                    <input required type="number" step="0.001" name="A3.12" class="w-100 pb-2 pt-2" value="{{$a3_12}}"/>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                                </div>
                                            </div>

                                            <div class="row mt-3 m-4">
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                    <label class="form-label" for="">Pump Hydraulic Testing<span class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                    <input required type="number" step="0.001" name="A3.13" class="w-100 pb-2 pt-2" value="{{$a3_13}}"/>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                                </div>
                                            </div>

                                            <div class="row mt-3 m-4">
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                    <label class="form-label" for="">Packing <span class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                    <input required type="number" step="0.001" name="A3.14" class="w-100 pb-2 pt-2" value="{{$a3_14}}"/>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        

                            {{--
                                <div class="project_approval_section border mt-3">
                                    <h5 class="text-uppercase text-left mb-2 mt-3 ml-3 font-weight-bolder">Control Panel Assembly
                                    ( <span class="text-danger">* </span> Write in HOURS)</h5>
                                    <hr class="mx-3 mt-0" />
                                    <div class="row mt-3 m-4">
                                        <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                            <label class="form-label" for=""><span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                            <input required type="number" step="0.001" min = "0" id="" class="w-100 pb-2 pt-2"/>
                                        </div>
                                        <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                        </div>
                                    </div>

                                    <div class="row mt-3 m-4">
                                        <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                            <label class="form-label" for=""><span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                            <input required type="number" step="0.001" min = "0" class="w-100 pb-2 pt-2"/>
                                        </div>
                                        <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                        </div>
                                    </div>
                                </div>

                                <div class="project_approval_section border mt-3">
                                    <h5 class="text-uppercase text-left mb-2 mt-3 ml-3 font-weight-bolder">Norm pump - Bareshaft
                                        ( <span class="text-danger">* </span> Write in HOURS)</h5>
                                    <hr class="mx-3 mt-0" />
                                    <div class="row mt-3 m-4">
                                        <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                            <label class="form-label" for=""><span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                            <input required type="number" step="0.001" min = "0" id="" class="w-100 pb-2 pt-2"/>
                                        </div>
                                        <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                        </div>
                                    </div>

                                    <div class="row mt-3 m-4">
                                        <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                            <label class="form-label" for=""><span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                            <input required type="number" step="0.001" min = "0" class="w-100 pb-2 pt-2"/>
                                        </div>
                                        <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                        </div>

                                    </div>
                                </div>

                                <div class="project_approval_section border mt-3">
                                    <h5 class="text-uppercase text-left mb-2 mt-3 ml-3 font-weight-bolder">Split case horizontal  - Bareshaft ( <span class="text-danger">*</span> Write in HOURS)</h5>
                                    <hr class="mx-3 mt-0" />
                                    <div class="row mt-3 m-4">
                                        <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                            <label class="form-label" for=""><span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                            <input required type="number" step="0.001" min = "0" id="" class="w-100 pb-2 pt-2"/>
                                        </div>
                                        <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                        </div>
                                    </div>

                                    <div class="row mt-3 m-4">
                                        <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                            <label class="form-label" for=""><span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                            <input required type="number" step="0.001" min = "0" class="w-100 pb-2 pt-2"/>
                                        </div>
                                        <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                        </div>
                                    </div>
                                </div>

                                <div class="project_approval_section border mt-3">
                                    <h5 class="text-uppercase text-left mb-2 mt-3 ml-3 font-weight-bolder">Borehole pump assembly
                                        ( <span class="text-danger">* </span> Write in HOURS)</h5>
                                    <hr class="mx-3 mt-0" />
                                    <div class="row mt-3 m-4">
                                        <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                            <label class="form-label" for=""><span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                            <input required type="number" step="0.001" min = "0" id="" class="w-100 pb-2 pt-2"/>
                                        </div>
                                        <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                        </div>
                                    </div>

                                    <div class="row mt-3 m-4">
                                        <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                            <label class="form-label" for=""><span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                            <input required type="number" step="0.001" min = "0" class="w-100 pb-2 pt-2"/>
                                        </div>
                                        <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                        </div>
                                    </div>
                                </div>

                                <div class="project_approval_section border mt-3">
                                    <h5 class="text-uppercase text-left mb-2 mt-3 ml-3 font-weight-bolder">Helix Pump Assembly
                                    ( <span class="text-danger">* </span> Write in HOURS)</h5>
                                    <hr class="mx-3 mt-0" />
                                    <div class="row mt-3 m-4">
                                        <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                            <label class="form-label" for=""><span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                            <input required type="number" step="0.001" min = "0" id="" class="w-100 pb-2 pt-2"/>
                                        </div>
                                        <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                        </div>
                                    </div>

                                    <div class="row mt-3 m-4">
                                        <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                            <label class="form-label" for=""><span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                            <input required type="number" step="0.001" min = "0" class="w-100 pb-2 pt-2"/>
                                        </div>
                                        <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                        </div>
                                    </div>
                                </div>

                                <div class="project_approval_section border mt-3">
                                    <h5 class="text-uppercase text-left mb-2 mt-3 ml-3 font-weight-bolder">Split case vertical pump -motor alignment ( <span class="text-danger">*</span> Write in HOURS)</h5>
                                    <hr class="mx-3 mt-0" />
                                </div>
                            --}}
                            <div id="controlPanelAccordion" class="accordion mt-3">
                                <div class="card">
                                    <div class="card-header" id="headingSeven">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link text-uppercase cutome_collapsed font-weight-bolder d-flex align-items-center" data-toggle="collapse" data-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven" type="button">
                                                Control Panel Assembly( <span class="text-danger">* </span> Write in HOURS)
                                                <span class="ml-2"><i class="fa fa-chevron-down arrow-icon"></i></span>
                                            </button>
                                        </h5>
                                    </div>
                                    
                                    <div id="collapseSeven" class="collapse " aria-labelledby="headingSeven" data-parent="#controlPanelAccordion">
                                        <div class="card-body">
                                            <div class="row mt-3 m-4">
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                    <label class="form-label">Cross check BOM <span class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                    <input required type="number" step="0.001" name="A4.1" class="w-100 pb-2 pt-2" value="{{$a4_1}}"/>
                                                </div>
                                            </div>
                                            <div class="row mt-3 m-4">
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                    <label class="form-label">Prepare main label <span class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                    <input required type="number" step="0.001" name="A4.2" class="w-100 pb-2 pt-2" value="{{$a4_2}}"/>
                                                </div>
                                            </div>
                                            <div class="row mt-3 m-4">
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                    <label class="form-label">Export packing <span class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                    <input required type="number" step="0.001" name="A4.3" class="w-100 pb-2 pt-2" value="{{$a4_3}}"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="normPumpAccordion" class="accordion mt-3">
                                <div class="card">
                                    <div class="card-header" id="headingNormPump">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link text-uppercase cutome_collapsed font-weight-bolder d-flex align-items-center" data-toggle="collapse" data-target="#collapseNormPump" aria-expanded="false" aria-controls="collapseNormPump" type="button">
                                                Norm Pump - Bareshaft (<span class="text-danger">*</span> Write in HOURS)
                                                <span class="ml-2"><i class="fa fa-chevron-down arrow-icon"></i></span>
                                            </button>
                                        </h5>
                                    </div>
                                    
                                    <div id="collapseNormPump" class="collapse" aria-labelledby="headingNormPump" data-parent="#normPumpAccordion">
                                        <div class="card-body">
                                            <div class="row mt-3 m-4">
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                    <label class="form-label">Cross check BOM <span class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                    <input required type="number" step="0.001" name="A5.1" min="0" class="w-100 pb-2 pt-2" value="{{$a5_1}}"/>
                                                </div>
                                            </div>
                                            
                                            <div class="row mt-3 m-4">
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                    <label class="form-label">Prepare main label <span class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                    <input required type="number" step="0.001" name="A5.2" min="0" class="w-100 pb-2 pt-2" value="{{$a5_2}}"/>
                                                </div>
                                            </div>
                                            
                                            <div class="row mt-3 m-4">
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                    <label class="form-label">Export packing <span class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                    <input required type="number" step="0.001" name="A5.3" min="0" class="w-100 pb-2 pt-2" value="{{$a5_3}}"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="splitCaseAccordion" class="accordion mt-3">
                                <div class="card">
                                    <div class="card-header" id="headingSplitCase">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link text-uppercase cutome_collapsed font-weight-bolder d-flex align-items-center" data-toggle="collapse" data-target="#collapseSplitCase" aria-expanded="false" aria-controls="collapseSplitCase" type="button">
                                                Split Case Horizontal - Bareshaft (<span class="text-danger">* </span> Write in HOURS)
                                                <span class="ml-2"><i class="fa fa-chevron-down arrow-icon"></i></span>
                                            </button>
                                        </h5>
                                    </div>
                                    
                                    <div id="collapseSplitCase" class="collapse" aria-labelledby="headingSplitCase" data-parent="#splitCaseAccordion">
                                        <div class="card-body">
                                            <div class="row mt-3 m-4">
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                    <label class="form-label">Cross check BOM <span class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                    <input required type="number" step="0.001" name="A6.1" min="0" class="w-100 pb-2 pt-2" value="{{$a6_1}}"/>
                                                </div>
                                            </div>
                                            
                                            <div class="row mt-3 m-4">
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                    <label class="form-label">Prepare main label <span class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                    <input required type="number" step="0.001" name="A6.2" min="0" class="w-100 pb-2 pt-2" value="{{$a6_2}}"/>
                                                </div>
                                            </div>
                                            
                                            <div class="row mt-3 m-4">
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                    <label class="form-label">Export packing <span class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                    <input required type="number" step="0.001" name="A6.3" min="0" class="w-100 pb-2 pt-2" value="{{$a6_3}}"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <div id="boreholePumpAccordion" class="accordion mt-3">
                            <div class="card">
                                <div class="card-header" id="headingBoreholePump">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link text-uppercase font-weight-bolder d-flex align-items-center cutome_collapsed" data-toggle="collapse" data-target="#collapseBoreholePump" aria-expanded="false" aria-controls="collapseBoreholePump" type="button">
                                            Borehole Pump Assembly (<span class="text-danger">* </span>Write in HOURS)
                                            <span class="ml-2"><i class="fa fa-chevron-down arrow-icon"></i></span>
                                        </button>
                                    </h5>
                                </div>
                                
                                <div id="collapseBoreholePump" class="collapse" aria-labelledby="headingBoreholePump" data-parent="#boreholePumpAccordion">
                                    <div class="card-body">
                                        <div class="row mt-3 m-4">
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                <label class="form-label">Cross check BOM <span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                <input required type="number" step="0.001" name="A7.1" min="0" class="w-100 pb-2 pt-2" value="{{$a7_1}}"/>
                                            </div>
                                        </div>
                                        <div class="row mt-3 m-4">
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                <label class="form-label">Prepare main label <span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                <input required type="number" step="0.001" name="A7.2" min="0" class="w-100 pb-2 pt-2" value="{{$a7_2}}"/>
                                            </div>
                                        </div>
                                        <div class="row mt-3 m-4">
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                <label class="form-label" for="">Export packing<span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                <input required type="number" step="0.001" name="A7.3" min = "0" id="" class="w-100 pb-2 pt-2" value="{{$a7_3}}"/>
                                            </div>
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="helixPumpAccordion" class="accordion mt-3">
                            <div class="card">
                                <div class="card-header" id="headingHelixPump">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link cutome_collapsed text-uppercase font-weight-bolder d-flex align-items-center" data-toggle="collapse" data-target="#collapseHelixPump" aria-expanded="false" aria-controls="collapseHelixPump" type="button">
                                            Helix Pump Assembly (<span class="text-danger">* </span>   Write in HOURS)
                                            <span class="ml-2"><i class="fa fa-chevron-down arrow-icon"></i></span>
                                        </button>
                                    </h5>
                                </div>
                                
                                <div id="collapseHelixPump" class="collapse" aria-labelledby="headingHelixPump" data-parent="#helixPumpAccordion">
                                    <div class="card-body">
                                        <div class="row mt-3 m-4">
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                <label class="form-label">Cross check BOM <span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                <input required type="number" step="0.001" name="A8.1" min="0" class="w-100 pb-2 pt-2" value="{{$a8_1}}"/>
                                            </div>
                                        </div>
                                        <div class="row mt-3 m-4">
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                <label class="form-label">Prepare main label <span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                <input required type="number" step="0.001" name="A8.2" min="0" class="w-100 pb-2 pt-2" value="{{$a8_2}}"/>
                                            </div>
                                        </div>
                                        <div class="row mt-3 m-4">
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                <label class="form-label">Export packing <span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                <input required type="number" step="0.001" name="A8.3" min="0" class="w-100 pb-2 pt-2" value="{{$a8_3}}"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div id="pumpAlignmentAccordion" class="accordion mt-3">
                            <div class="card">
                                <div class="card-header" id="headingTwo">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link text-uppercase font-weight-bolder d-flex align-items-center cutome_collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo" type="button">
                                            Split Case Vertical Pump - Motor Alignment ( <span class="text-danger">* </span> Write in HOURS)
                                            <span class="ml-2"><i class="fa fa-chevron-down arrow-icon"></i></span>
                                        </button>
                                    </h5>
                                </div>
                                
                                <div id="collapseTwo" class="collapse " aria-labelledby="headingTwo" data-parent="#pumpAlignmentAccordion">
                                    <div class="card-body">
                                            <div class="row mt-3 m-4">
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                    <label class="form-label">Cross check BOM <span class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                    <input required type="number" step="0.001" name="A9.1" min="0" class="w-100 pb-2 pt-2" value="{{$a9_1}}"/>
                                                </div>
                                            </div>

                                            <div class="row mt-3 m-4">
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                    <label class="form-label">Prepare main label <span class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                    <input required type="number" step="0.001" name="A9.2" min="0" class="w-100 pb-2 pt-2" value="{{$a9_2}}"/>
                                                </div>
                                            </div>
                                            
                                            <div class="row mt-3 m-4">
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                    <label class="form-label">Export packing <span class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                    <input required type="number" step="0.001" name="A9.3" min="0" class="w-100 pb-2 pt-2" value="{{$a9_3}}"/>
                                                </div>
                                            </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="fireFightingAccordion" class="accordion mt-3">
                            <div class="card">
                                <div class="card-header" id="headingOne">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link text-uppercase font-weight-bolder cutome_collapsed" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne" type="button">
                                            Fire Fighting Systems ( <span class="text-danger">* </span> Write in HOURS)
                                            <span class="ml-2"><i class="fa fa-chevron-down arrow-icon"></i></span>
                                        </button>
                                    </h5>
                                </div>
                                
                                <div id="collapseOne" class="collapse " aria-labelledby="headingOne" data-parent="#fireFightingAccordion">
                                    <div class="card-body">
                                        <div class="row mt-3 m-4">
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                <label class="form-label">Cross check BOM <span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                <input required type="number" step="0.001" name="A10.1" min="0" class="w-100 pb-2 pt-2" value="{{$a10_1}}"/>
                                            </div>
                                        </div>

                                        <div class="row mt-3 m-4">
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                <label class="form-label">Prepare main label <span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                <input required type="number" step="0.001" name="A10.2" min="0" class="w-100 pb-2 pt-2" value="{{$a10_2}}"/>
                                            </div>
                                        </div>

                                        <div class="row mt-3 m-4">
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                <label class="form-label">Export packing <span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                <input required type="number" step="0.001" name="A10.3" min="0" class="w-100 pb-2 pt-2" value="{{$a10_3}}"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="normPumpAccordionBareshaft" class="accordion mt-3">
                            <div class="card">
                                <div class="card-header" id="normPumpAccordionBareshafts">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link text-uppercase font-weight-bolder cutome_collapsed" data-toggle="collapse" data-target="#collapselev" aria-expanded="false" aria-controls="collapselev" type="button">
                                           Norm pump - Bareshaft + Norm pump - Motor Alignment
                                            <span class="ml-2"><i class="fa fa-chevron-down arrow-icon"></i></span>
                                        </button>
                                    </h5>
                                </div>
                                
                                <div id="collapselev" class="collapse " aria-labelledby="normPumpAccordionBareshafts" data-parent="#normPumpAccordionBareshaft">
                                    <div class="card-body">
                                        <div class="row mt-3 m-4">
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                <label class="form-label">Cross check BOM <span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                <input required type="number" step="0.001" name="A11.1" min="0" class="w-100 pb-2 pt-2" value="{{$a11_1}}"/>
                                            </div>
                                        </div>

                                        <div class="row mt-3 m-4">
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                <label class="form-label">Prepare main label <span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                <input required type="number" step="0.001" name="A11.2" min="0" class="w-100 pb-2 pt-2" value="{{$a11_2}}"/>
                                            </div>
                                        </div>

                                        <div class="row mt-3 m-4">
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                <label class="form-label">Export packing <span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                <input required type="number" step="0.001" name="A11.3" min="0" class="w-100 pb-2 pt-2" value="{{$a11_3}}"/>
                                            </div>
                                        </div>
                                        <div class="row mt-3 m-4">
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                <label class="form-label">Gather all material for assembly <span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                <input required type="number" step="0.001" name="A11.4" min="0" class="w-100 pb-2 pt-2" value="{{$a11_4}}"/>
                                            </div>
                                        </div>
                                        <div class="row mt-3 m-4">
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                <label class="form-label">Air test<span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                <input required type="number" step="0.001" name="A11.5" min="0" class="w-100 pb-2 pt-2" value="{{$a11_5}}"/>
                                            </div>
                                        </div>
                                        <div class="row mt-3 m-4">
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                <label class="form-label">Painting<span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                <input required type="number" step="0.001" name="A11.6" min="0" class="w-100 pb-2 pt-2" value="{{$a11_6}}"/>
                                            </div>
                                        </div>
                                        <div class="row mt-3 m-4">
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                <label class="form-label">Pump motor alignment<span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                <input required type="number" step="0.001" name="A11.7" min="0" class="w-100 pb-2 pt-2" value="{{$a11_7}}"/>
                                            </div>
                                        </div>
                                        <div class="row mt-3 m-4">
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                <label class="form-label">Prepare main label<span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                <input required type="number" step="0.001" name="A11.8" min="0" class="w-100 pb-2 pt-2" value="{{$a11_8}}"/>
                                            </div>
                                        </div>
                                        <div class="row mt-3 m-4">
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                <label class="form-label">Fix nameplate<span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                <input required type="number" step="0.001" name="A11.9" min="0" class="w-100 pb-2 pt-2" value="{{$a11_9}}"/>
                                            </div>
                                        </div>
                                        <div class="row mt-3 m-4">
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                <label class="form-label">Pump Hydraulic Testing<span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                <input required type="number" step="0.001" name="A11.10" min="0" class="w-100 pb-2 pt-2" value="{{$a11_10}}"/>
                                            </div>
                                        </div>
                                        <div class="row mt-3 m-4">
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                <label class="form-label">Packing<span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                <input required type="number" step="0.001" name="A11.11" min="0" class="w-100 pb-2 pt-2" value="{{$a11_11}}"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="splitAccordion" class="accordion mt-3">
                            <div class="card">
                                <div class="card-header" id="splitAccordions">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link text-uppercase font-weight-bolder cutome_collapsed" data-toggle="collapse" data-target="#collapsetwel" aria-expanded="false" aria-controls="collapsetwel" type="button">
                                           Split case horizontal - Bareshaft + Split case horizontal pump - Motor Alignment
                                            <span class="ml-2"><i class="fa fa-chevron-down arrow-icon"></i></span>
                                        </button>
                                    </h5>
                                </div>
                                
                                <div id="collapsetwel" class="collapse " aria-labelledby="splitAccordions" data-parent="#splitAccordion">
                                    <div class="card-body">
                                        <div class="row mt-3 m-4">
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                <label class="form-label">Cross check BOM <span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                <input required type="number" step="0.001" name="A12.1" min="0" class="w-100 pb-2 pt-2" value="{{$a12_1}}"/>
                                            </div>
                                        </div>

                                        <div class="row mt-3 m-4">
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                <label class="form-label">Prepare main label <span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                <input required type="number" step="0.001" name="A12.2" min="0" class="w-100 pb-2 pt-2" value="{{$a12_2}}"/>
                                            </div>
                                        </div>

                                        <div class="row mt-3 m-4">
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                <label class="form-label">Export packing <span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                <input required type="number" step="0.001" name="A12.3" min="0" class="w-100 pb-2 pt-2" value="{{$a12_3}}"/>
                                            </div>
                                        </div>
                                        <div class="row mt-3 m-4">
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                <label class="form-label">Gather all material for assembly <span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                <input required type="number" step="0.001" name="A12.4" min="0" class="w-100 pb-2 pt-2" value="{{$a12_4}}"/>
                                            </div>
                                        </div>
                                        <div class="row mt-3 m-4">
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                <label class="form-label">Air test<span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                <input required type="number" step="0.001" name="A12.5" min="0" class="w-100 pb-2 pt-2" value="{{$a12_5}}"/>
                                            </div>
                                        </div>
                                        <div class="row mt-3 m-4">
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                <label class="form-label">Painting<span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                <input required type="number" step="0.001" name="A12.6" min="0" class="w-100 pb-2 pt-2" value="{{$a12_6}}"/>
                                            </div>
                                        </div>
                                        <div class="row mt-3 m-4">
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                <label class="form-label">Pump motor alignment<span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                <input required type="number" step="0.001" name="A12.7" min="0" class="w-100 pb-2 pt-2" value="{{$a12_7}}"/>
                                            </div>
                                        </div>
                                        <div class="row mt-3 m-4">
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                <label class="form-label">Prepare main label<span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                <input required type="number" step="0.001" name="A12.8" min="0" class="w-100 pb-2 pt-2" value="{{$a12_8}}"/>
                                            </div>
                                        </div>
                                        <div class="row mt-3 m-4">
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                <label class="form-label">Fix nameplate<span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                <input required type="number" step="0.001" name="A12.9" min="0" class="w-100 pb-2 pt-2" value="{{$a12_9}}"/>
                                            </div>
                                        </div>
                                        <div class="row mt-3 m-4">
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                <label class="form-label">Pump Hydraulic Testing<span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                <input required type="number" step="0.001" name="A12.10" min="0" class="w-100 pb-2 pt-2" value="{{$a12_10}}"/>
                                            </div>
                                        </div>
                                        <div class="row mt-3 m-4">
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                                                <label class="form-label">Packing<span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                                                <input required type="number" step="0.001" name="A12.11" min="0" class="w-100 pb-2 pt-2" value="{{$a12_11}}"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                            <div class="d-flex justify-content-center mt-2 mb-4">
                            <button  type="submit" class="btn btn-lg project_index_btn">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
                </div>
            </div>
            </div>
        </div>
    </section>
    </div>
</div>


@endsection