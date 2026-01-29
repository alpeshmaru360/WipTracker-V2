@extends('layouts.main')
@section('content')
<link rel="stylesheet" href="{{ asset('css/product_superwisor.css') }}" />
<script src="https://cdn.datatables.net/responsive/3.0.3/js/dataTables.responsive.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<div class="main_section bg-white m-4 pb-5 project_bg">
    <div class="d-flex justify-content-between align-items-center px-5 pt-3">
        <h3 class="pt-4 text-bold text-left text-uppercase">Operator Tracking</h3>  
        <div class="d-flex gap-3 justify-content-end pt-3 me-1">  
            <button type="button" class="btn btn-lg export_reset_btn" onclick="download_csv()">
                <i class="fas fa-download"></i> Export
            </button>
            <a type="button" href="{{route('OperatorTracking')}}" class="btn btn-lg export_reset_btn ml-2">
                <i class="fas fa-sync-alt"></i> Reset
            </a>
        </div>
    </div>
    <hr class="mx-5 mt-3 mb-2" />
    <div class="container-fluid px-5 mx-3">           
        <form action="{{ route('OperatorTrackingFilter') }}" method="POST" id="filter_form">
            @csrf     
            <div class="row mt-3 table-responsive">   
                <table class="table table-bordered table-hover align-middle" id="operators_tracking_table">
                    <thead id="project_table_head">
                        @include('production_superwisor.OperatorTracking.operator_tracking_head')
                    </thead> 
                    <tbody id="project_table_body">                      
                        @include('production_superwisor.OperatorTracking.operator_tracking_body')                       
                    </tbody>
                </table>
                <input type="hidden" name="last_filter_column" id="last_filter_column">
            </div>
        </form>          
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#operators_tracking_table').DataTable({
        paging: true,
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        searching: true,
        ordering: false
    });
    $('#operators_tracking_table').removeClass('dataTable');
});

$(document).ready(function () {
    // Prevent dropdown from closing when clicking inside
    $(document).on('click', '.dropdown-menu', function (e) {
        e.stopPropagation(); // stop click from bubbling and closing dropdown
    });
});

$(document).on('change', '.select-all-filter', function () {
    var targetClass = $(this).data('target');
    var isChecked = $(this).is(':checked');
    $(targetClass).attr('checked', isChecked ? 'checked' : false);
    if(isChecked){
        $(this).closest('.dropdown').find('.dropdown-item').addClass('checked');
    }else{
        $(this).closest('.dropdown').find('.dropdown-item').removeClass('checked');
    } 
});

$(document).ready(function () {
    const oTFilterRoute = "{{ route('OperatorTrackingFilter') }}";
    $(document).on('click', '.apply-filter-btn', function () {
        var last_filter_column = $(this).data('column');
        $('#last_filter_column').val(last_filter_column);        
        $('#filter_form').attr('action', oTFilterRoute);       
        if ($('#last_filter_column').val() && $('#filter_form').attr('action') === oTFilterRoute) {
            $('#filter_form').submit();
        } else {
            alert("Form action does not match OperatorTrackingFilter route.");
        }
    });
});

function download_csv() {
    var $form = $('#filter_form');
    // Temporarily change action to export route
    $form.attr('action', "{{ route('OperatorTracking.export.csv') }}");
    $form.attr('method', 'POST');

    // Add CSRF token (needed for POST)
    if ($form.find('input[name="_token"]').length === 0) {
        $form.prepend('<input type="hidden" name="_token" value="{{ csrf_token() }}">');
    }
    $form.submit();
    // Reset form back to normal filter action (so Apply Filter works again)
    $form.attr('action', "{{ route('OperatorTracking') }}");
    $form.attr('method', 'POST');
}
</script>
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->
@endsection