@extends('layouts.main')
@section('content')
<link href="{{ asset('css/production_manager.css') }}" rel="stylesheet" />
<script src="https://cdn.datatables.net/responsive/3.0.3/js/dataTables.responsive.min.js"></script>
<style>
    .project_icon:hover {
        color: white !important;
        text-decoration: none;
    }
    .dataTables_wrapper {
        width: 100%;
    }
</style>
<div class="main_section bg-white m-4 pb-5">
    <div class="container mt-3">
        <div class="justify-content-end row">
            <div class="btn-group mt-4">
            </div>
            <div class="btn-group mt-4">
                <a class="btn-lg project_index_btn ml-3" href="{{route('ExpectedOrders')}}" title="click here to add new product">
                    <i class="fa fa-fw fa-plus"></i> Create New
                </a>
            </div>
        </div>
        <div class="row mt-3">
            <div class="w-100" style="overflow-x: auto;">
                <table class="table table-hover table-border w-100 text-center" id="table_orders">
                    <thead>
                        <tr>
                            <th>Quotation Number</th>
                            <th>Article Number</th>
                            <th>Description</th>
                            <th>Quantity</th>
                            <th>Product Type</th>
                            <th>Expected Order Date</th>
                            <th>Expected Delivery Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($expected_orders as $order)
                        <tr>
                            <td>{{ $order->quotation_number }}</td>
                            <td>{{ $order->article_number }}</td>
                            <td>{{ $order->description }}</td>
                            <td>{{ $order->qty }}</td>
                            <td>{{ $order->product_type }}</td>
                            <td>{{ $order->expected_order_date }}</td>
                            <td>{{ $modules[$order->project_id]->estimated_readiness ?? 'N/A' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#table_orders').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "pageLength": 10, // Default entries shown
        });
    });
</script>
@endsection