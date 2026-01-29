@extends('layouts.main')


@section('content')

<link href="{{ asset('css/stock_history.css') }}" rel="stylesheet" />

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />


<div class="main_container d-flex">

    @include('layouts.setting')

    <div class="main_section bg-white  flex-grow-1">
        <div class="container stock_historys_container">
            <h1>Stock History</h1>
            @if(session('success'))
                <div class="alert alert-success mt-3">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

 			<table class="table table-bordered text-center stock-historys-table" id="stockhistory_table">
                <thead>
                    <tr>
                        <th>Project</th>
                        <th>Product</th>
                        <th>Item Description</th>
                        <th>Item Article No</th>
                        <th>Used Qty</th>
                        <th>Delivery Status</th>
                        <th>Delivery Status</th>
                    </tr>
                </thead>
 
                	 <tbody>
                    @foreach($StockHistorys as $stockhistory)
                        <tr>
                            <td>{{ $stockhistory->project_name }}</td>
                            <td>{{ $stockhistory->description }}</td>
                            <td>{{ $stockhistory->item_desc }}</td>
                            <td>{{ $stockhistory->item_article_no }}</td>
                            <td>{{ $stockhistory->used_qty }}</td>
                            <td>{{ $stockhistory->delivery_status == 1 ? 'Full' : 'Partial' }}</td>
                            <td>{{ \Carbon\Carbon::parse($stockhistory->created_at)->format('d-m-y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>

             </table>

        </div>
    </div>
</div>


@endsection

@section('scripts')

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

<script src="https://cdn.datatables.net/responsive/3.0.3/js/dataTables.responsive.min.js"></script>


<script>
    $(document).ready(function() {
        
        $('#stockhistory_table').DataTable({
            "order": [
                [6, 'desc']
            ]
        });

        $('#stockhistory_table').removeClass('dataTable');
     });

</script>


@endsection