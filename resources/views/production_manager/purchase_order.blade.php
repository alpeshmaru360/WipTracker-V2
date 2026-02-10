@extends('layouts.main')
@section('content')
<link rel="stylesheet" href="{{ asset('css/purchase_order.css') }}" />
<script src="https://cdn.datatables.net/responsive/3.0.3/js/dataTables.responsive.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<div class="purchase_order_page main_section bg-white m-4 pb-5 po_bg">
    <div class="container-fluid mt-3 py-3 px-5">

        @php
            $is_procurement_login = Auth::user()->role === 'Procurement Specialist';
        @endphp     
        
        <div class="row">
            <div class="d-flex gap-3 justify-content-end w-100 pt-3">           
                <a type="button" class="btn btn-lg btn-primary export_reset_btn" id="toggleColumnsBtn">
                    <i class="fas fa-eye-slash"></i> View All Columns
                </a>
                <a type="button" class="btn btn-lg export_reset_btn" onclick="download_csv();">
                    <i class="fas fa-download"></i> Export
                </a>
                <a type="button" href="{{ route('PurchaseOrder') }}" class="btn btn-lg export_reset_btn">
                    <i class="fas fa-sync-alt"></i> Reset
                </a>
                @if ($is_procurement_login)
                <a class="btn btn-lg export_reset_btn" href="{{ route('addPO') }}" title="Click here to add new product">
                    <i class="fa fa-fw fa-plus"></i> Add New
                </a>
                @endif
            </div> 
        </div>      
        <div class="row mt-3">
            <div class="section_po">
                @if(count($purchaseOrders) > 0)
                <form action="{{ route('PurchaseOrderFilter') }}" method="POST" id="filter_form">
                @csrf
                <table class="table table-hover table-border w-100 text-center" id="project_table-2">                  
                    <thead>
                        @include('production_manager.purchase_order_head', ['purchaseOrders' => $purchaseOrders])
                    </thead> 
                    <tbody>
                        @php $counter = 1; @endphp
                        @foreach ($purchaseOrders as $order)
                        @foreach ($order->purchaseOrderTables as $table)
                        @if (is_null($table->parent_id))
                            @php                                
                            $is_po_added = DB::table('stock_bom_po')
                                            ->where('po_no', $order->po_number)
                                            ->where('description', $table->description)
                                            ->where('article_no', $table->artical_no)
                                            ->value('po_added');
                            @endphp
                        <tr data-id="{{ $table->id }}">
                            <td>{{ $counter++ }}</td>
                            <td class="d-none">{{ $table->id }}</td>
                            <td>{{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('d-m-Y') : 'N/A' }}</td>
                            <td>{{ $order->po_number }}</td>
                            <td>{{ $order->project_no ?? 'N/A' }}</td>
                            <td>{{ $order->project_name ?? 'N/A' }}</td>
                            <td>{{ $order->supplier }}</td>

                            <td class="toggle-col">{{ $order->is_local_supplier == 1 ? 'Yes' : 'No' }}</td>

                            <td class="toggle-col">{{ $order->shipment_method }}</td>
                            <td>{{ $table->artical_no }}</td>
                            <td>{{ $table->description }}</td>
                            <td class="original-quantity">
                                {{ $table->quantity }}
                                @if ($table->is_partial_shipment == 1 && $table->is_parent == 1)
                                <button type="button" class="btn btn-success btn-sm ms-2 toggle-child-rows"                                   
                                    data-id="{{ $table->id }}">
                                    +
                                </button>
                                @endif
                            </td>
                            <!-- Status columns -->
                            <td class="toggle-col">
                                @if ($table->is_partial_shipment == 1 && $table->is_parent == 1)
                                    <!-- No status for parent row with partial shipment -->
                                @else
                                    Approved
                                    @if ($order->production_engineer_approved_date)
                                        <br>{{ \Carbon\Carbon::parse($order->production_engineer_approved_date)->format('d-m-Y') }}
                                    @endif
                                @endif
                            </td>
                            <!-- Updated ETA Date column with color coding -->
                            <td class="{{ ($table->eta && $table->actual_readiness_date) ? (\Carbon\Carbon::parse($table->eta)->lessThan(\Carbon\Carbon::parse($table->actual_readiness_date)) ? 'text-danger' : 'text-success') : '' }}">
                                {{ $table->eta ? \Carbon\Carbon::parse($table->eta)->format('d-m-Y') : 'N/A' }}
                            </td>

                            <td class="toggle-col">{{ ($table->is_partial_shipment == 1 && $table->is_parent == 1) ? '' : ($table->is_partial_shipment ? 'Yes' : 'No') }}</td>

                            <!-- Rest of the row remains the same -->
                            <td>{{ ($table->is_partial_shipment == 1 && $table->is_parent == 1) ? '' : ($table->received_quantity ?? 'N/A') }}</td>

                            <td class="toggle-col">{{ ($table->is_partial_shipment == 1 && $table->is_parent == 1) ? '' : ($table->oa_date ? \Carbon\Carbon::parse($table->oa_date)->format('d-m-Y') : 'N/A') }}</td>

                            <td class="toggle-col">{{ ($table->is_partial_shipment == 1 && $table->is_parent == 1) ? '' : ($table->committed_date ? \Carbon\Carbon::parse($table->committed_date)->format('d-m-Y') : 'N/A') }}</td>

                            <td class="toggle-col {{ ($table->is_partial_shipment == 1 && $table->is_parent == 1) ? '' : ($table->actual_readiness_date && $table->committed_date ? (\Carbon\Carbon::parse($table->actual_readiness_date)->greaterThan(\Carbon\Carbon::parse($table->committed_date)) ? 'text-danger' : 'text-success') : '') }}">
                                {{ ($table->is_partial_shipment == 1 && $table->is_parent == 1) ? '' : ($table->actual_readiness_date ? \Carbon\Carbon::parse($table->actual_readiness_date)->format('d-m-Y') : 'N/A') }}
                            </td>

                            <td class="toggle-col">{{ ($table->is_partial_shipment == 1 && $table->is_parent == 1) ? '' : ($table->eta_date_shipper ? \Carbon\Carbon::parse($table->eta_date_shipper)->format('d-m-Y') : 'N/A') }}</td>

                            <td>
                                {{ ($table->is_partial_shipment == 1 && $table->is_parent == 1) ? '' : ($table->actual_received_date ? \Carbon\Carbon::parse($table->actual_received_date)->format('d-m-Y') : 'N/A') }}
                             
                                <!-- A Code: 15-01-2026 Start -->
                                {{-- @if (Auth::user()->role === 'Procurement Specialist' && empty($table->actual_received_date)) --}}

                                @if(!($table->is_partial_shipment == 1 && $table->is_parent == 1)
                                    && Auth::user()->role === 'Procurement Specialist'
                                    && empty($table->actual_received_date))
                                    
                                <!-- A Code: 15-01-2026 End -->

                                    @if($is_po_added)
                                    <button aria-label="Mark as Received" type="button" 
                                            class="btn btn-success btn-sm rounded-circle mark-received p-1" 
                                            data-id="{{ $table->id }}" 
                                            title="Click here to set Actual Received Date">
                                        <i class="fa fa-calendar-check fa-2x mark_received_icon" aria-hidden="true"></i>
                                    </button>
                                    @else
                                    <button aria-label="Mark as Received" type="button"
                                            class="btn btn-secondary btn-sm rounded-circle mark_received_inactive p-1"
                                            title="Click here to set Actual Received Date"
                                            onclick="alert('Kindly ensure this Item Description & Item Article Number field is added under PO Status from BOM Items – Place Purchase Order.');">
                                        <i class="fa fa-calendar-check fa-2x mark_received_icon"></i>
                                    </button>
                                    @endif

                                @endif
                            </td>

                            <td class="toggle-col">{{ ($table->is_partial_shipment == 1 && $table->is_parent == 1) ? '' : ($table->shipping_refrence ?? 'N/A') }}</td>

                            <td class="toggle-col">{{ ($table->is_partial_shipment == 1 && $table->is_parent == 1) ? '' : ($table->boe ?? 'N/A') }}</td>

                            <td class="toggle-col">{{ ($table->is_partial_shipment == 1 && $table->is_parent == 1) ? '' : $order->payment_terms }}</td>

                            <td class="toggle-col">{{ ($table->is_partial_shipment == 1 && $table->is_parent == 1) ? '' : ($table->remarks ?? 'N/A') }}</td>

                            @if ($is_procurement_login)
                            <td class="toggle-col {{ ($table->is_partial_shipment == 1 && $table->is_parent == 1) ? '' : ($table->response_time !== null ? ($table->response_time < 0 ? 'text-danger' : 'text-success') : '') }}">
                                @if (!($table->is_partial_shipment == 1 && $table->is_parent == 1))
                                    @if ($table->response_time < 0)
                                        <span>{{ abs($table->response_time) }} days delay</span>
                                    @else
                                        {{ $table->response_time ? $table->response_time . ' days' : 'N/A' }}
                                    @endif
                                @endif
                            </td>
                            <td class="toggle-col">
                                @if (!($table->is_partial_shipment == 1 && $table->is_parent == 1))
                                    @if ($table->delivery_time < 0)
                                        <span class="text-danger">{{ abs($table->delivery_time) }} days delay</span>
                                    @else
                                        {{ $table->delivery_time ? $table->delivery_time . ' days' : 'N/A' }}
                                    @endif
                                @endif
                            </td>                           
                            <td>
                                <!-- Show calendar button if not a parent with partial shipment OR if any child has pending_slot = 1 -->
                                @php
                                    $hasPendingSlot = $order->purchaseOrderTables->where('parent_id', $table->id)->contains('pending_slot', 1);
                                @endphp
                                @if (!($table->is_partial_shipment == 1 && $table->is_parent == 1) || $hasPendingSlot)
                                    <button type="button" title="Add Dates" class="p-2 m-1 fa fa-calendar project_icon" data-is_po_added="{{ $is_po_added }}"></button>
                                @endif
                                @if (!$table->actual_received_date)
                                    <a href="{{ route('editPO', $order->id) }}" title="Edit" class="p-2 m-1 fa fa-pen project_icon"></a>
                                @endif
                            </td>
                            @endif
                        </tr>

                        <!-- Child rows -->
                        @foreach ($order->purchaseOrderTables as $childTable)
                        @if ($childTable->parent_id == $table->id)
                            @php                                
                            $is_child_po_added = DB::table('stock_bom_po')
                                            ->where('po_no', $order->po_number)
                                            ->where('description', $childTable->description)
                                            ->where('article_no', $childTable->artical_no)
                                            ->value('po_added');
                            @endphp
                        <tr class="child-row bg-light-green" data-parent-id="{{ $table->id }}" style="display: none;">
                            <td>{{ $counter++ }}</td>
                            <td class="d-none">{{ $childTable->id }}</td>
                            <td>{{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('d-m-Y') : 'N/A' }}</td>
                            <td>{{ $order->po_number }}</td>
                            <td>{{ $order->project_no ?? 'N/A' }}</td>
                            <td>{{ $order->project_name ?? 'N/A' }}</td>
                            <td>{{ $order->supplier }}</td>
                            <td class="toggle-col">{{ $childTable->is_local_supplier == 1 ? 'Yes' : 'No' }}</td>
                            <td class="toggle-col">{{ $order->shipment_method }}</td>
                            <td>{{ $childTable->artical_no }}</td>
                            <td>{{ $childTable->description }}</td>
                            <td>{{ $childTable->quantity }}</td>
                            <!-- Child row status columns -->
                            <td class="toggle-col">
                                Approved
                                @if ($order->production_engineer_approved_date)
                                <br>{{ \Carbon\Carbon::parse($order->production_engineer_approved_date)->format('d-m-Y') }}
                                @endif
                            </td>
                            <!-- Updated ETA Date column for child rows with color coding -->
                            <td class="{{ ($childTable->eta && $childTable->actual_readiness_date) ? (\Carbon\Carbon::parse($childTable->eta)->lessThan(\Carbon\Carbon::parse($childTable->actual_readiness_date)) ? 'text-danger' : 'text-success') : '' }}">
                                {{ $childTable->eta ? \Carbon\Carbon::parse($childTable->eta)->format('d-m-Y') : 'N/A' }}
                            </td>
                            <td class="toggle-col">{{ $childTable->pending_slot == 0 ? 'Yes' : 'No' }}</td>
                            <!-- Rest of the child row remains the same -->
                            <td>{{ $childTable->received_quantity ?? 'N/A' }}</td>
                            <td class="toggle-col">{{ $childTable->oa_date ? \Carbon\Carbon::parse($childTable->oa_date)->format('d-m-Y') : 'N/A' }}</td>
                            <td class="toggle-col">{{ $childTable->committed_date ? \Carbon\Carbon::parse($childTable->committed_date)->format('d-m-Y') : 'N/A' }}</td>
                            <td class="toggle-col {{ ($childTable->actual_readiness_date && $childTable->committed_date) ? (\Carbon\Carbon::parse($childTable->actual_readiness_date) <= \Carbon\Carbon::parse($childTable->committed_date) ? 'text-success' : 'text-danger') : '' }}">
                                {{ $childTable->actual_readiness_date ? \Carbon\Carbon::parse($childTable->actual_readiness_date)->format('d-m-Y') : 'N/A' }}
                            </td>
                            <td class="toggle-col">{{ $childTable->eta_date_shipper ? \Carbon\Carbon::parse($childTable->eta_date_shipper)->format('d-m-Y') : 'N/A' }}</td>
                            <td>
                                {{ $childTable->actual_received_date ? \Carbon\Carbon::parse($childTable->actual_received_date)->format('d-m-Y') : 'N/A' }}                                
                                @if (Auth::user()->role === 'Procurement Specialist' && empty($childTable->actual_received_date) && $childTable->pending_slot != 1)    

                                    @if($is_child_po_added)
                                    <button aria-label="Mark as Received" type="button" 
                                            class="btn btn-success btn-sm rounded-circle mark-received p-1" 
                                            data-id="{{ $childTable->id }}" 
                                            title="Click here to set Actual Received Date">
                                        <i class="fa fa-calendar-check fa-2x mark_received_icon" aria-hidden="true"></i>
                                    </button>
                                    @else
                                    <button aria-label="Mark as Received" type="button"
                                            class="btn btn-secondary btn-sm rounded-circle mark_received_inactive p-1"
                                            title="Click here to set Actual Received Date"
                                            onclick="alert('Kindly ensure this Item Description & Item Article Number field is added under PO Status from BOM Items – Place Purchase Order.');">
                                        <i class="fa fa-calendar-check fa-2x mark_received_icon"></i>
                                    </button>
                                    @endif
                                    
                                @endif
                            </td>
                            <td class="toggle-col">{{ $childTable->shipping_refrence ?? 'N/A' }}</td>
                            <td class="toggle-col">{{ $childTable->boe ?? 'N/A' }}</td>
                            <td class="toggle-col">{{ $order->payment_terms }}</td>
                            <td class="toggle-col">{{ $childTable->remarks ?? 'N/A' }}</td>
                            @if ($is_procurement_login)
                            <td class="toggle-col {{ $childTable->response_time !== null ? ($childTable->response_time < 0 ? 'text-danger' : 'text-success') : '' }}">
                                @if ($childTable->response_time < 0)
                                    <span>{{ abs($childTable->response_time) }} days delay</span>
                                @else
                                    {{ $childTable->response_time ? $childTable->response_time . ' days' : 'N/A' }}
                                @endif
                            </td>
                            <td class="toggle-col">
                                @if($childTable->delivery_time < 0)
                                    <span class="text-danger">{{ abs($childTable->delivery_time) }} days delay</span>
                                @else
                                    {{ $childTable->delivery_time ? $childTable->delivery_time . ' days' : 'N/A' }}
                                @endif
                            </td>                            
                            <td>
                                @if($childTable->pending_slot != 1)
                                <button type="button" title="Add Dates" class="p-2 m-1 fa fa-calendar project_icon" data-is_po_added="{{ $is_child_po_added }}"></button>
                                @endif
                            </td>
                            @endif
                        </tr>
                        @endif
                        @endforeach
                        @endif
                        @endforeach
                        @endforeach
                    </tbody>
                </table>
                    <input type="hidden" name="last_filter_column" id="last_filter_column">
                </form>
                @else
                @if($allPurchaseOrders == 0)
                <div class="alert alert-info">
                    No purchase orders found.
                </div>
                @else
                <div class="alert alert-info">
                    Only purchase orders approved by Production Engineer will appear here.
                </div>
                @endif
                @endif
            </div>
        </div>
    </div>


    <!-- Modal for updating Dates, Shipping Reference, and Remarks -->
    <div class="modal fade" id="combinedModal" tabindex="-1" aria-labelledby="combinedModalLabel" aria-hidden="true">
        <div class="modal-dialog date-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-light" id="combinedModalLabel">Update Dates, Shipping Reference, and Remarks</h5>
                </div>
                <div class="modal-body">
                    <form id="combinedForm">
                        @csrf
                        <div class="mb-3">
                            <label for="oa_date" class="form-label">OA Date</label>
                            <input type="date" class="form-control" id="oa_date" name="oa_date">
                        </div>
                        <div class="mb-3">
                            <label for="committed_date" class="form-label">Committed Date</label>
                            <input type="date" class="form-control" id="committed_date" name="committed_date">
                        </div>
                        <div class="mb-3">
                            <label for="actual_readiness_date" class="form-label">Actual Readiness Date</label>
                            <input type="date" class="form-control" id="actual_readiness_date" name="actual_readiness_date">
                        </div>
                        <div class="mb-3">
                            <!-- A Code: 12-12-2025 Start -->
                            <input type="hidden" id="po_is_added">
                            <!-- A Code: 12-12-2025 End -->
                            <label for="actual_received_date" class="form-label">Actual Received Date</label>
                            <input type="date" class="form-control" id="actual_received_date" name="actual_received_date">                            
                        </div>
                        <div class="mb-3">
                            <label for="received_quantity" class="form-label">Received Qty</label>
                            <input type="number" class="form-control rounded-2" id="received_quantity" name="received_quantity">
                        </div>
                        <div class="mb-3">
                            <label for="shipping_reference" class="form-label">Shipping Reference</label>
                            <textarea class="form-control" id="shipping_reference" name="shipping_reference"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="eta_date_shipper" class="form-label">ETA Date (Shipper)</label>
                            <input type="date" class="form-control" id="eta_date_shipper" name="eta_date_shipper">
                        </div>
                        <div class="mb-3">
                            <label for="boe" class="form-label">BOE</label>
                            <input type="text" class="form-control" id="boe" name="boe">
                        </div>
                        <div class="mb-3">
                            <label for="remarks" class="form-label">Remarks</label>
                            <textarea class="form-control" id="remarks" name="remarks"></textarea>
                        </div>
                        <div class="partial_section">
                            <div class="mb-2">
                                <label for="is_partial_shipment" class="form-label">Partial Shipment</label>
                                <input class="me-2 req_for_finance_approval" type="checkbox" name="is_partial_shipment" id="is_partial_shipment" />
                            </div>
                            <!-- Container for existing partial quantities (read-only) -->
                            <div id="existing_partial_quantities" class="mb-3" style="display: none;">
                                <label class="form-label">Existing Partial Quantities</label>
                                <div id="existing_quantities_list"></div>
                            </div>
                            <div id="partial_shipment_container" style="display: none;">
                                <div class="input-group">
                                    <input type="text" class="form-control partial_quantity" name="partial_quantity[]"
                                        placeholder="Enter positive quantity"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                    <button type="button" class="btn btn-outline-secondary" id="add_partial_quantity">+</button>
                                </div>
                                <div id="error_message" class="text-danger mb-2" style="display: none;"></div>
                            </div>
                        </div>

                        <input type="hidden" name="purchase_order_id" id="purchase_order_id">
                        <input type="hidden" name="original_quantity" id="original_quantity">                        
                        <div class="d-flex mt-3">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <button type="button" class="btn btn-secondary ml-3 close-button"
                                data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script> 
    $(document).ready(function() {
        // Set up CSRF token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let columnsVisible = false;

        // Initialize DataTable
        let table = $('#project_table-2').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": false,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "pageLength": 10,
            "drawCallback": function(settings) {
                // Re-apply hide/show on pagination
                if (columnsVisible) {
                    $('.toggle-col').show();
                } else {
                    $('.toggle-col').hide();
                }
            }
        });  

        // Default hidden
        $('.toggle-col').hide();

        $('#toggleColumnsBtn').on('click', function () {
            columnsVisible = !columnsVisible;

            if (columnsVisible) {
                $('.toggle-col').show();
                $(this).html('<i class="fas fa-eye"></i> Hide Columns');
            } else {
                $('.toggle-col').hide();
                $(this).html('<i class="fas fa-eye-slash"></i> View All Columns');
            }
        });

        // Function to toggle inputs visibility and set values
        function toggleInputsVisibility(isChecked) {
            const $modalBody = $('#combinedModal .modal-body');
            const $inputsAbovePartial = $modalBody.find('.mb-3').filter(function() {
                return $(this).nextAll('.partial_section').length > 0;
            });

            if (isChecked) {
                $inputsAbovePartial.hide();
                $inputsAbovePartial.find('input, textarea').each(function() {
                    $(this).val('');
                });
                $('#partial_shipment_container').show();
            } else {
                $inputsAbovePartial.show();
                $('#partial_shipment_container').hide();
            }
        }

        // Use event delegation for the calendar button click
        $('#project_table-2').on('click', '.fa-calendar', function() {

            // A Code: 12-12-2025 Start
            // Read value from button
            var isPoAdded = $(this).data('is_po_added');  
            $('#po_is_added').val(isPoAdded);
            // A Code: 12-12-2025 End

            var $row = $(this).closest('tr');
            var purchaseOrderTableId = $row.find('td.d-none').text();
            var originalQuantity = $row.find('td.original-quantity').text().trim().split(' ')[0];

            $('#purchase_order_id').val(purchaseOrderTableId);
            $('#original_quantity').val(originalQuantity);

            $.ajax({
                url: '/purchase-order/' + purchaseOrderTableId,
                method: 'GET',
                success: function(data) {

                    let readinessDate = data.actual_readiness_date 
                        ? data.actual_readiness_date.split(' ')[0] 
                        : '';
                    let receivedDate = data.actual_received_date 
                        ? data.actual_received_date.split(' ')[0] 
                        : '';

                    $('#oa_date').val(data.oa_date || '');
                    $('#committed_date').val(data.committed_date || '');
                    $('#actual_readiness_date').val(readinessDate);

                    $('#eta_date_shipper').val(data.eta_date_shipper || '');
                    $('#actual_received_date').val(receivedDate);

                    $('#shipping_reference').val(data.shipping_refrence || '');
                    $('#boe').val(data.boe || '');
                    $('#remarks').val(data.remarks || '');
                    $('#is_partial_shipment').prop('checked', data.is_parent == 1 && data.is_partial_shipment == 1);
                    $('#received_quantity').val(data.received_quantity || '');

                    // Handle partial quantities
                    const partialQuantities = data.partial_quantities || [];
                    const $partialContainer = $('#partial_shipment_container');
                    $partialContainer.find('.input-group').not(':first').remove();
                    $partialContainer.find('.partial_quantity').val('');

                    // Display existing partial quantities as read-only
                    const $existingQuantitiesList = $('#existing_quantities_list');
                    $existingQuantitiesList.empty();
                    if (partialQuantities.length > 0) {
                        partialQuantities.forEach(qty => {
                            $existingQuantitiesList.append(
                                `<div class="form-control mb-2" readonly>${qty}</div>`
                            );
                        });
                        $('#existing_partial_quantities').show();
                    } else {
                        $('#existing_partial_quantities').hide();
                    }

                    // Calculate remaining quantity
                    const originalQty = parseInt(originalQuantity);
                    const existingSum = partialQuantities.reduce((a, b) => a + b, 0);
                    const remainingQty = originalQty - existingSum;

                    // Hide the "Add" button and input fields if no quantity remains
                    if (remainingQty <= 0) {
                        $('#partial_shipment_container').hide();
                        $('#is_partial_shipment').prop('checked', true);
                    } else {
                        if (data.is_partial_shipment == 1) {
                            $('#partial_shipment_container').show();
                        }
                    }

                    if (data.is_parent == 1) {
                        $('.partial_section').show();
                        toggleInputsVisibility($('#is_partial_shipment').is(':checked'));
                    } else {
                        $('.partial_section').hide();
                        toggleInputsVisibility(false);
                    }
                    if(receivedDate == ''){
                        $('.partial_section').show();                        
                    }else{
                        $('.partial_section').hide();
                    }

                    $('#combinedModal').modal('show');
                },
                error: function(error) {
                    alert('Error fetching purchase order details: ' + (error.responseJSON ? error.responseJSON.message : 'Unknown error'));
                }
            });
        });

        // Handle partial shipment checkbox change
        $('#is_partial_shipment').on('change', function() {
            toggleInputsVisibility($(this).is(':checked'));
        });

        $('#actual_received_date').on('change', function() {
            var selectedDate = $(this).val();           
            if (selectedDate) {                
                $('.partial_section').hide();
            }else{
                $('.partial_section').show();
            }
        });  

        // Handle form submission
        $('#combinedForm').on('submit', function(e) {
            e.preventDefault();
            const isPartialChecked = $('#is_partial_shipment').is(':checked');

            if (isPartialChecked) {
                const $modalBody = $('#combinedModal .modal-body');
                const $inputsAbovePartial = $modalBody.find('.mb-3').filter(function() {
                    return $(this).nextAll('.partial_section').length > 0;
                });
                $inputsAbovePartial.find('input, textarea').each(function() {
                    $(this).val('');
                });
            }          

            // A Code: 12-12-2025 Start
            const isPoAdded = $('#po_is_added').val() == "1";
            const actualReceivedDate = $('#actual_received_date').val();

            if (!isPoAdded && actualReceivedDate) {
                console.log(isPoAdded);
                console.log(actualReceivedDate);
                alert('Kindly ensure this Item Description & Item Article Number field is added under PO Status from BOM Items – Place Purchase Order.');
                return false;
            }
            // A Code: 12-12-2025 End

            const formData = $(this).serialize();

            $.ajax({
                url: "/update-purchase-order",
                method: "POST",
                data: formData,
                success: (response) => {
                    $("#combinedModal").modal("hide");
                    alert(response.success);
                    location.reload();
                },
                error: (error) => {
                    alert(
                        "Error updating purchase order: " +
                        (error.responseJSON ? error.responseJSON.message || error.responseJSON.error : "Unknown error")
                    );
                }
            });
        });
    });

    // Toggle child rows
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".toggle-child-rows").forEach(button => {
            button.addEventListener("click", function() {
                let parentId = this.getAttribute("data-id");
                let childRows = document.querySelectorAll(`tr[data-parent-id="${parentId}"]`);
                let isAnyChildVisible = Array.from(childRows).some(row => row.style.display !== "none");

                childRows.forEach(row => {
                    row.style.display = isAnyChildVisible ? "none" : "table-row";
                });
            });
        });
    });

    // Set min date for date inputs
    document.addEventListener("DOMContentLoaded", function() {
        let today = new Date().toISOString().split("T")[0];
        let dateInputs = document.querySelectorAll("#combinedModal input[type='date']");
        dateInputs.forEach(input => {
            input.setAttribute("min", today);
        });
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let today = new Date().toISOString().split("T")[0];

        let dateInputs = document.querySelectorAll("#combinedModal input[type='date']");

        dateInputs.forEach(input => {
            input.setAttribute("min", today);
        });
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
        const poFilterRoute = "{{ route('PurchaseOrderFilter') }}";
        $(document).on('click', '.apply-filter-btn', function () {
            var last_filter_column = $(this).data('column');
            $('#last_filter_column').val(last_filter_column);
            
            $('#filter_form').attr('action', poFilterRoute);
        
            if ($('#last_filter_column').val() && $('#filter_form').attr('action') === poFilterRoute) {
                $('#filter_form').submit();
            } else {
                alert("Form action does not match ProjectFilter route.");
            }
        });
    });

    function download_csv() {
        var exportRoute = "{{ route('po.export.csv') }}";
        var $form = $('#filter_form');

        // Set form action to the export route
        $form.attr('action', exportRoute);

        // Confirm the action is correctly set before submitting
        if ($form.attr('action') === exportRoute) {
            $form.submit();
        } else {
            alert("Form route did not match. Please try again.");
        }
    }

    $(document).on('click', '.mark-received', function () {
        let id = $(this).data('id');
        let btn = $(this);
        let now = new Date();
        let formattedDateTime = now.getFullYear() + '-' +
            String(now.getMonth() + 1).padStart(2, '0') + '-' +
            String(now.getDate()).padStart(2, '0') + ' ' +
            String(now.getHours()).padStart(2, '0') + ':' +
            String(now.getMinutes()).padStart(2, '0') + ':' +
            String(now.getSeconds()).padStart(2, '0');

        $.post('/update-received-date', { id: id, date: formattedDateTime }, function (res) {
            if (res.success) {

                let d = new Date(res.date.replace(' ', 'T'));

                let formatted =
                    String(d.getDate()).padStart(2, '0') + '-' +
                    String(d.getMonth() + 1).padStart(2, '0') + '-' +
                    d.getFullYear();

                // Display only the date (no time)
                btn.closest('td').html(formatted);

            } else {
                alert('Failed to update date.');
            }
        }).fail(function () {
            alert('Something went wrong while updating.');
        });
    });

</script>
<script src="{{ asset('js/partial_shipment.js') }}"></script>
@endsection
