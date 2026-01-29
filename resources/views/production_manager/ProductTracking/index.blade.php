@extends('layouts.main')
@section('content')
<link rel="stylesheet" href="{{ asset('css/production_manager.css') }}" />
<script src="https://cdn.datatables.net/responsive/3.0.3/js/dataTables.responsive.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<div class="product_tracking_page main_section bg-white m-4 pb-5 project_bg">
    <div class="d-flex justify-content-between align-items-center px-5 pt-3">
        <h3 class="pt-4 text-bold text-left text-uppercase">Product Tracking</h3>  
       
        <!-- A Code: 15-12-2025 Start -->
        <div class="justify-content-end row mx-1 px-1">            
            <div class="btn-group mt-4 mx-3 h-fit">
                <select class="project_status_dd ml-2 p-11-14" name="status">
                    <option class="All" value="3">Project status</option>
                    <option class="Open" value="0">Open</option>
                    <option class="work_in_process" value="1">Work In Progress</option>
                    <option class="completed" value="2">completed</option>
                </select>
            </div>  
            <div class="btn-group mt-4">
                <div class="mb-4 ml-2 d-flex justify-content-end">          
                    <button type="button" class="btn btn-primary mr-2" onclick="download_csv();">
                        <i class="fas fa-download"></i> Export
                    </button> 
                    <a href="{{route('product_tracking')}}" class="btn btn-primary mr-2">              
                        <i class="fas fa-sync-alt"></i> Reset               
                    </a> 
                </div>
            </div>            
        </div>
        <!-- A Code: 15-12-2025 End -->

    </div>
    <hr class="mx-5 mt-3 mb-2" />

    <div class="container-fluid px-5 mx-3">
        <form action="{{ route('ProductTrackingFilter') }}" method="POST" id="filter_form">
            @csrf
            <div class="row mt-3 table-responsive">
                <table class="table table-bordered table-hover align-middle" id="tracking_table">
                    <thead id="tracking_table_head">
                        @include('production_manager.ProductTracking.product_tracking_head')
                    </thead> 
                    <tbody id="tracking_table_body">                      
                        @include('production_manager.ProductTracking.product_tracking_body')                        
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
    $('#tracking_table').DataTable({
        paging: true,
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        searching: true,
        ordering: false,
    });
    $('#tracking_table').removeClass('dataTable');
});

$(document).ready(function () {
    $(document).on('click', '.dropdown-menu', function (e) {
        e.stopPropagation();
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
    const oTFilterRoute = "{{ route('ProductTrackingFilter') }}";
    $(document).on('click', '.apply-filter-btn', function () {
        var last_filter_column = $(this).data('column');
        $('#last_filter_column').val(last_filter_column);        
        $('#filter_form').attr('action', oTFilterRoute);       
        if ($('#last_filter_column').val() && $('#filter_form').attr('action') === oTFilterRoute) {
            $('#filter_form').submit();
        } else {
            alert("Form action does not match ProductTrackingFilter route.");
        }
    });
});
function download_csv() {
    var $form = $('#filter_form');
    // Temporarily change action to export route
    $form.attr('action', "{{ route('ProductTracking.export.csv') }}");
    $form.attr('method', 'POST');
    // Add CSRF token (needed for POST)
    if ($form.find('input[name="_token"]').length === 0) {
        $form.prepend('<input type="hidden" name="_token" value="{{ csrf_token() }}">');
    }
    $form.submit();
    // Reset form back to normal filter action (so Apply Filter works again)
    $form.attr('action', "{{ route('product_tracking') }}");
    $form.attr('method', 'POST');
}
</script>
<script type="text/javascript">
    // A Code: 15-12-2025 Start   
    $(".project_status_dd").on('change', function () {

        var status = $(this).val();

        $.ajax({
            url: "{{ route('product_tracking') }}",
            type: "GET",
            data: { status: status },
            success: function (response) {

                if (response.html) {
                    $("#tracking_table_body").html(response.html);
                } else {
                    $("#tracking_table_body").html(
                        '<tr><td colspan="17">No record Found!</td></tr>'
                    );
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
            }
        });
    });
    // A Code: 15-12-2025 End
</script>
@endsection