@extends('layouts.main')
@section('content')
<link rel="stylesheet" href="{{ asset('css/stock.css') }}" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<div class="stock_master_index_page main_section bg-white m-4">
    <div class="container stock_container"> 
        <div class="row">  
            <div class="col-md-6">     
                <h1>Stock Table</h1>
            </div>
            <div class="col-md-6">  
                <div class="justify-content-end row">
                    <div class="btn-group mt-4"></div>
                    <div class="btn-group mt-4">
                        <a class="btn-lg project_index_btn ml-3" href="" title="click here to add new">
                            <i class="fa fa-fw fa-plus"></i> Add New
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12" style="overflow-x: auto;">
                <table id="stockTable" class="stock-table display table table-hover table-border w-100 text-center">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Arts</th>
                            <th>Description</th>
                            <th>Actual Stock</th>
                            <th>Reserved Quantity</th>
                            <th>Quantity in Order</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stocks as $stock)
                            <tr>
                                <td>{{ $stock->id }}</td>
                                <td>{{ $stock->arts }}</td>
                                <td>{{ $stock->des }}</td>
                                <td>{{ $stock->qty_actual_stock }}</td>
                                <td>{{ $stock->qty_reserved }}</td>
                                <td>{{ $stock->qty_in_order }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#stockTable').DataTable({
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            order: [[0, 'asc']],
            searching: true,
            info: true,
            paging: true,
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            }
        });
    });
</script>
@endsection

