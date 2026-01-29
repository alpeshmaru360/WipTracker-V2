@extends('layouts.main')
@section('content')

<link href="{{ asset('css/production_manager.css') }}" rel="stylesheet" />
<style type="text/css">
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
              <form action="{{route('ProductionManagerProjectCreateDo')}}" enctype="multipart/form-data" method="post">
                @csrf
                <div class="row mt-3">
                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                        <label class="form-label" for="">Project Name<span class="text-danger">*</span></label>
                    </div>
                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                        <input type="text" id="" class="w-100 pb-2 pt-2" name = "project_name" required placeholder="Please Enter Project Name"  />
                    </div>
                    <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                        <label class="form-label" for="">Assembly Quotation Ref.<span class="text-danger">*</span></label>
                    </div>
                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                        <input type="text" id="" class="w-100 pb-2 pt-2" name = "assembly_quotation_ref" required placeholder="Please Enter Assembly Quotation Reference Number" />
                    </div>
                    <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                        <label class="form-label" for="">Product Type<span class="text-danger">*</span></label>
                    </div>
                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                        <select class="select2" name="product_type[]" multiple="multiple">
                            @foreach($product_type as $product_type_val)
                                <option value="{{$product_type_val->project_type_name}}">{{$product_type_val->project_type_name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                        <label class="form-label" for="">Sales Person<span class="text-danger">*</span></label>
                    </div>
                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                        <input type="text" id="" class="w-100 pb-2 pt-2" name = "sales_person" required placeholder="Please Enter Sales Persons Name" />
                    </div>
                    <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                        <label class="form-label" for="">Customer Name<span class="text-danger">*</span></label>
                    </div>
                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                        <input type="text" id="" class="w-100 pb-2 pt-2" name = "customer_name" required placeholder="Please Enter Customer Name" />
                    </div>
                    <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                        <label class="form-label" for="">Customer Documents<span class="text-danger">*</span></label>
                    </div>
                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                        <input type="file" id="" class="w-100 pb-2 pt-2" name = "customer_documents[]" multiple  accept=".pdf, .doc, .docx, .jpg, .jpeg, .png, .xlsx, .csv" />
                    </div>
                    <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-3 col-md-6 col-lg-6 col-xl-3">
                        <label class="form-label" for="">Country<span class="text-danger">*</span></label>
                    </div>
                    <div class="col-3 col-md-6 col-lg-6 col-xl-7">
                        <input type="text" id="" class="w-100 pb-2 pt-2" name = "country_name" required placeholder="Please Enter Country Name" />
                    </div>
                    <div class="col-3 col-md-6 col-lg-6 col-xl-2">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-3 col-md-6 col-lg-6 col-xl-6">
                        <input class="form-check-input me-2" type="checkbox" value="" id="" />
                        <label class="form-label" for="">Request for Finance Approval</label>
                    </div>
                    <div class="col-3 col-md-6 col-lg-6 col-xl-6">
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-3 col-md-6 col-lg-6 col-xl-6">
                        <input class="form-check-input me-2" type="checkbox" value="" id="" />
                        <label class="form-label" for="">Approved</label>
                    </div>
                </div>

                <div class="d-flex justify-content-center mt-2">
                  <button  type="submit" class="btn btn-lg" >Create</button>
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
<script type="text/javascript">
    $(document).ready(function() {
        $('.select2').select2();
        $(".select2-search__field").css('display','none');  
        $(".select2-selection__choice__remove select2-selection__rendered").css('display','none');  
        $(".select2 button").css('display','none'); 

    })

    $('.select2').on('click',function(){
        $(".select2-search__field").css('display','block');    

    });
</script>
@endsection