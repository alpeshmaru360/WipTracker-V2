@extends('layouts.main')
@section('content')
<link rel="stylesheet" href="{{ asset('css/quality_manager.css') }}" />

<div class="main_section bg-white m-4 pb-5">
    <div class="d-flex justify-content-between align-items-center px-5 pt-3">
        <h3 class="pt-4 text-bold text-left text-uppercase">Pending Initial Inspection</h3>
        <a class="mt-4 mb-2 btn btn-primary btn-sm py-2 px-3" onclick="location.reload();">
            <i class="fa fa-refresh text-light">&#xf021;</i>
        </a>
    </div>
    <hr class="mx-5 mt-2 mb-2" />
    <div class="container-fluid px-5 mx-3">
        <div class="row mt-3">
            <div class="col-md-12">
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
                @endif
                @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif
            </div>
        </div>
        <div class="row mt-3 table-responsive">
            @if(count($pendingInitialInspections) > 0)
            <table class="table table-hover table-bordered w-100 text-center" id="pending_purchase_order">
                <thead>
                    <tr>
                        <th scope="col" class="project_table_heading p-1">Request Date</th>
                        <th scope="col" class="project_table_heading p-1">Deadline</th>
                        <th scope="col" class="project_table_heading p-1">Project No.</th>
                        <th scope="col" class="project_table_heading p-1">Project Name</th>                       
                        <th scope="col" class="project_table_heading p-1">PO No.</th>
                        <th scope="col" class="project_table_heading p-1">Supplier</th>                       
                        <th scope="col" class="project_table_heading p-1">Order Status</th>
                        <th scope="col" class="project_table_heading p-1">Action</th>
                    </tr>
                </thead>
                <tbody id="project_table_body">
                    @foreach($pendingInitialInspections as $val)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($val->actual_received_date)->format('d-m-y H:i') ?? 'N/A' }}</td>
                        <td class="{{ $val->deadline !== 'N/A' && \Carbon\Carbon::canBeCreatedFromFormat($val->deadline, 'd-m-y H:i') && \Carbon\Carbon::createFromFormat('d-m-y H:i', $val->deadline)->isPast() ? 'text-red' : 'text-green' }}">
                            {{ $val->deadline }}
                        </td>
                        <td>{{$val->project_no ?? 'N/A'}}</td>
                        <td>{{$val->project_name ?? 'N/A'}}</td>                        
                        <td>{{$val->po_number}}</td>
                        <td>{{$val->supplier}}</td>                       
                        <td>{{ $val->is_parent == 1 ? 'Full Order' : 'Partial Order' }}</td>
                        <td>
                            <a href="{{route('QualityManagerQualityCreate',[
                                'id' => $val->pot_id,
                                'pot_ids' => json_encode(explode(',', $val->pot_ids)),
                                'po_number' => $val->po_number,
                                'project_no' => $val->project_no,
                                'project_name' => $val->project_name,
                                'product_article_no' => $val->product_article_no,
                                'product_desc' => $val->product_desc,
                                'supplier' => $val->supplier,
                                'artical_no' => $val->artical_no,
                                'description' => $val->description,
                                'quantity' => $val->quantity,
                            ])
                            }}">
                                <i class="p-2 m-1 fa project_icon">Conduct Initial Insepction</i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="alert alert-info w-100">
                No Pending Purchase Orders found.
            </div>
            @endif
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center px-5 pt-3">
        <h3 class="pt-4 text-bold text-left text-uppercase">Pending Final Inspection</h3>
        <a class="mt-4 mb-2 btn btn-primary btn-sm py-2 px-3" onclick="location.reload();">
            <i class="fa fa-refresh text-light">&#xf021;</i>
        </a>
    </div>
    <hr class="mx-5 mt-2 mb-2" /> 
    <div class="container-fluid px-5 mx-3"> 
        <div class="row mt-3 table-responsive">
            @if(count($pendingFinalInspections) > 0)
            <table class="table table-hover table-bordered w-100 text-center" id="pending_final_inspection">
                <thead>
                    <tr>
                        <th scope="col" class="project_table_heading p-1">Project No</th>
                        <th scope="col" class="project_table_heading p-1">Project Name</th>
                        <th scope="col" class="project_table_heading p-1">Product Article No </th>
                        <th scope="col" class="project_table_heading p-1">Product Description</th>
                        <th scope="col" class="project_table_heading p-1">Total Product Qty</th>
                        <th scope="col" class="project_table_heading p-1">Unit No From Total Qty</th>
                        <th scope="col" class="project_table_heading p-1">Action</th>
                    </tr>
                </thead>
                <tbody id="project_table_body">
                    @foreach($pendingFinalInspections as $val)
                    <tr>
                        <td>{{ $val->projects->project_no }}</td>
                        <td>{{ $val->projects->project_name }}</td>
                        <td>{{ $val->products->full_article_number }}</td>
                        <td>{{ $val->products->description }}</td>
                        <td>{{ $val->products->qty }}</td>
                        <td>{{$val->qty_number}} of {{$val->products->qty}}</td>
                        <td>                           
                            <a href="{{route('QualityManagerFinalinspectionCreate',[
                                'id' => $val->id,
                                'project_no' => $val->projects->project_no,
                                'project_name' => $val->projects->project_name,
                                'supplier' => $val->supplier,
                                'artical_no' => $val->products->full_article_number,
                                'description' => $val->products->description,
                                'quantity' => $val->products->qty,
                                'unit_qty' => $val->qty_number
                            ])
                            }}">
                                <i class="p-2 m-1 fa project_icon">Conduct Final Insepction</i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="alert alert-info w-100">
                No Pending Final Inspection found.
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#pending_purchase_order').DataTable({
                paging: true,
                pageLength: 2,
                lengthMenu: [2, 5, 10, 25, 50, 100],
                searching: true,
                ordering: false
            });
            $('#pending_purchase_order').removeClass('dataTable');
            $('#pending_final_inspection').DataTable({
                paging: true,
                pageLength: 2,
                lengthMenu: [2, 5, 10, 25, 50, 100],
                searching: true,
                ordering: false
            });
            $('#pending_final_inspection').removeClass('dataTable');
        });
    </script>
@endsection