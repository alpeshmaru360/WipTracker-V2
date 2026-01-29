<tr>    
    <th class="project_table_heading">#</th>    
    <th class="d-none">ID</th>

    <!-- Date --> 
    <th class="project_table_heading">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_0'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';

                $query_data = isset($last_filter_column) && $last_filter_column == 0 ? $purchaseOrdersfilter : $purchaseOrders;
                $values = $query_data->pluck('order_date')->filter()->unique()->sort();                    
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                    type="button"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">
               &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Date 
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter0"> Select All
                </label>
                @foreach($values as $val)
                    @if(!empty($val))
                    @php
                        $formatted = \Carbon\Carbon::parse($val)->format('d-m-Y');
                    @endphp
                    <label class="dropdown-item m-0 pn_label">
                        <input type="checkbox" name="filters[filter_col_0][]" class="multi-filter multi_filter0" 
                        value="{{ $val }}" {{ in_array($val, $selectedValues) ? 'checked' : '' }}>
                        {{ $formatted }}
                    </label>
                    @endif
                @endforeach
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="0">OK</button>
                </div>
            </div>
        </div>
    </th>

    <!-- PO NO.--> 
    <th class="project_table_heading">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_1'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';
                $poNumbers = ($last_filter_column == 1 ? $purchaseOrdersfilter : $purchaseOrders)
                    ->sortByDesc('id') 
                    ->pluck('po_number')
                    ->filter()
                    ->unique();
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                 type="button"
                 data-toggle="dropdown"
                 aria-haspopup="true"
                 aria-expanded="false">
                PO NO.
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter1"> Select All
                </label>
                @foreach ($poNumbers as $val)
                    <label class="dropdown-item m-0 pn_label">
                        <input type="checkbox" name="filters[filter_col_1][]" class="multi-filter multi_filter1" 
                        value="{{ $val }}" {{ in_array($val, $selectedValues) ? 'checked' : '' }}>
                        {{ $val }}
                    </label>
                @endforeach
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="1">OK</button>
                </div>
            </div>
        </div>
    </th> 

    <!-- Project no -->   
    <th class="project_table_heading">   
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_2'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';   
                $projectNumbers = ($last_filter_column == 2 ? $purchaseOrdersfilter : $purchaseOrders)
                    ->pluck('project_no')
                    ->filter()
                    ->unique()
                    ->sortByDesc(function ($projectNo) {
                        [$year, $num] = explode('-', $projectNo);
                        return [(int)$year, (int)$num];
                    })
                    ->values();
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                    type="button"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">
                Project no
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter2"> Select All
                </label>
                @foreach ($projectNumbers as $val)
                    <label class="dropdown-item m-0 pn_label">
                        <input type="checkbox" name="filters[filter_col_2][]" class="multi-filter multi_filter2" 
                        value="{{ $val }}" {{ in_array($val, $selectedValues) ? 'checked' : '' }}>
                        {{ $val }}
                    </label>
                @endforeach
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="2">OK</button>
                </div>
            </div>
        </div>        
    </th>

    <!-- Project Name --> 
    <th class="project_table_heading">
       <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_3'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';
                $projectNames = ($last_filter_column == 3 ? $purchaseOrdersfilter : $purchaseOrders)
                    ->sortByDesc('id') 
                    ->pluck('project_name')
                    ->filter()
                    ->unique();
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                    type="button"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">
                Project Name
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter3"> Select All
                </label>
                @foreach($projectNames as $val)
                    <label class="dropdown-item m-0 pn_label pn_label_{{ $val }}">
                        <input type="checkbox" name="filters[filter_col_3][]" class="multi-filter multi_filter3" 
                        value="{{ $val }}" {{ in_array($val, $selectedValues) ? 'checked' : '' }}>
                        {{ $val }}
                    </label>
                @endforeach

                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="3">OK</button>
                </div>
            </div>
        </div>
    </th>

    <!-- Supplier -->
    <th class="project_table_heading">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_4'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';
                $suppliers = ($last_filter_column == 4 ? $purchaseOrdersfilter : $purchaseOrders)
                    ->pluck('supplier')
                    ->filter()
                    ->unique()
                    ->sort();
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                 type="button"
                 data-toggle="dropdown"
                 aria-haspopup="true"
                 aria-expanded="false">
                Supplier
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter4"> Select All
                </label>
                @foreach($suppliers as $val)
                    <label class="dropdown-item m-0">
                        <input type="checkbox" name="filters[filter_col_4][]" class="multi-filter multi_filter4" 
                        value="{{ $val }}" {{ in_array($val, $selectedValues) ? 'checked' : '' }}>
                        {{ $val }}
                    </label>
                @endforeach
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="4">OK</button>
                </div>
            </div>
        </div>
    </th>

    <!-- Local Supplier -->
    <th class="project_table_heading toggle-col">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_5'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';
                $statusCodes = ($last_filter_column == 5 ? $purchaseOrdersfilter : $purchaseOrders)
                    ->pluck('is_local_supplier')
                    ->unique();
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                 type="button"
                 data-toggle="dropdown"
                 aria-haspopup="true"
                 aria-expanded="false">
                Local Supplier
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter5"> Select All
                </label>            
                @foreach($statusCodes as $statusCode)
                    @php
                        switch($statusCode) {
                            case "0": $statusLabel = "No"; break;
                            case "1": $statusLabel = "Yes"; break;
                            default: $statusLabel = "Unknown"; break;
                        }
                    @endphp
                    <label class="dropdown-item m-0">
                        <input type="checkbox" name="filters[filter_col_5][]" class="multi-filter multi_filter5" 
                        value="{{ $statusCode }}" {{ in_array($statusCode, $selectedValues) ? 'checked' : '' }}>
                        {{ $statusLabel }}
                    </label>
                @endforeach
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="5">OK</button>
                </div>
            </div>
        </div>
    </th>

    <!-- Shipment Method -->
    <th class="project_table_heading toggle-col">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_6'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';
                $shipmentMethods = ($last_filter_column == 6 ? $purchaseOrdersfilter : $purchaseOrders)
                    ->pluck('shipment_method')
                    ->filter()
                    ->unique()
                    ->sort();
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                 type="button"
                 data-toggle="dropdown"
                 aria-haspopup="true"
                 aria-expanded="false">
                Shipment Method
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter6"> Select All
                </label>            
                @foreach($shipmentMethods as $val)
                    <label class="dropdown-item m-0">
                        <input type="checkbox" name="filters[filter_col_6][]" class="multi-filter multi_filter6" 
                        data-column="6" value="{{ $val }}" {{ in_array($val, $selectedValues) ? 'checked' : '' }}>
                        {{ $val }}
                    </label>
                @endforeach
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="6">OK</button>
                </div>
            </div>
        </div>
    </th>

    <!-- Article -->
    <th class="project_table_heading">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_7'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';
                $articalNos = ($last_filter_column == 7 ? $purchaseOrdersfilter : $purchaseOrders)
                    ->pluck('purchaseOrderTables')
                    ->flatten()
                    ->pluck('artical_no')
                    ->filter()
                    ->unique()
                    ->sortByDesc('id');
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                 type="button"
                 data-toggle="dropdown"
                 aria-haspopup="true"
                 aria-expanded="false">
                Article
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter7"> Select All
                </label>            
                @foreach($articalNos as $val)
                    <label class="dropdown-item m-0">
                        <input type="checkbox" name="filters[filter_col_7][]" class="multi-filter multi_filter7" 
                        value="{{ $val }}" {{ in_array($val, $selectedValues) ? 'checked' : '' }}>
                        {{ $val }}
                    </label>
                @endforeach
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="7">OK</button>
                </div>
            </div>
        </div>
    </th>

    <!-- Description -->
    <th class="project_table_heading">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_8'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';
                $descriptions = ($last_filter_column == 8 ? $purchaseOrdersfilter : $purchaseOrders)
                    ->pluck('purchaseOrderTables')
                    ->flatten()
                    ->pluck('description')
                    ->filter()
                    ->unique()
                    ->sortByDesc('id');
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                 type="button"
                 data-toggle="dropdown"
                 aria-haspopup="true"
                 aria-expanded="false">
                Description
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter8"> Select All
                </label>            
                @foreach($descriptions as $val)
                    <label class="dropdown-item m-0">
                        <input type="checkbox" name="filters[filter_col_8][]" class="multi-filter multi_filter8" 
                        value="{{ $val }}" {{ in_array($val, $selectedValues) ? 'checked' : '' }}>
                        {{ $val }}
                    </label>
                @endforeach
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="8">OK</button>
                </div>
            </div>
        </div>
    </th>

    <!-- Ordered Qty -->
    <th class="project_table_heading">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_9'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';
                $quantities = ($last_filter_column == 9 ? $purchaseOrdersfilter : $purchaseOrders)
                    ->pluck('purchaseOrderTables')
                    ->flatten()
                    ->pluck('quantity')
                    ->unique()
                    ->sort();
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                 type="button"
                 data-toggle="dropdown"
                 aria-haspopup="true"
                 aria-expanded="false">
                Ordered Qty
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter9"> Select All
                </label>                
                @foreach($quantities as $val)
                    <label class="dropdown-item m-0">
                        <input type="checkbox" name="filters[filter_col_9][]" class="multi-filter multi_filter9" 
                        value="{{ $val }}" {{ in_array($val, $selectedValues) ? 'checked' : '' }}>
                        {{ $val }}
                    </label>
                @endforeach
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="9">OK</button>
                </div>
            </div>
        </div>
    </th>

    <!-- Replace Status column with two new columns -->

    <!-- Prod. Manager Status -->
    <th class="project_table_heading toggle-col">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_10'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';
                $statusCodes = ($last_filter_column == 10 ? $purchaseOrdersfilter : $purchaseOrders)
                    ->pluck('is_production_manager_approved')
                    ->unique();
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                    type="button"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">
                Prod. Manager Status
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter10"> Select All
                </label>       
                @foreach($statusCodes as $statusCode)
                    @php
                        switch($statusCode) {
                            case "0": $statusLabel = "Not Required"; break;
                            case "1": $statusLabel = "Approved"; break;
                            case "2": $statusLabel = "Rejected"; break;
                            case "4": $statusLabel = "Pending"; break;
                            default: $statusLabel = "Unknown"; break;
                        }
                    @endphp
                    <label class="dropdown-item m-0">
                        <input type="checkbox" name="filters[filter_col_10][]" class="multi-filter multi_filter10" 
                        value="{{ $statusCode }}" {{ in_array($statusCode, $selectedValues) ? 'checked' : '' }}>
                        {{ $statusLabel }}
                    </label>
                @endforeach
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="10">OK</button>
                </div>
            </div>
        </div>
    </th>

    <!-- Prod. Engineer Status -->
    <th class="project_table_heading toggle-col">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_11'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';
                $statusCodes = ($last_filter_column == 11 ? $purchaseOrdersfilter : $purchaseOrders)
                    ->pluck('is_production_engineer_approved')
                    ->unique();
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                    type="button"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">
                Prod. Engineer Status
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter11"> Select All
                </label>
                @foreach($statusCodes as $statusCode)
                    @php
                        switch($statusCode) {
                            case "0": $statusLabel = "Pending"; break;
                            case "1": $statusLabel = "Approved"; break;
                            case "2": $statusLabel = "Rejected"; break;
                            default: $statusLabel = "Unknown"; break;
                        }
                    @endphp
                    <label class="dropdown-item m-0">
                        <input type="checkbox" name="filters[filter_col_11][]" class="multi-filter multi_filter11" 
                        value="{{ $statusCode }}" {{ in_array($statusCode, $selectedValues) ? 'checked' : '' }}>
                        {{ $statusLabel }}
                    </label>
                @endforeach
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="11">OK</button>
                </div>
            </div>
        </div>
    </th>

    <!-- ETA Date -->
    <th class="project_table_heading">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_12'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';
                $values = ($last_filter_column == 12 ? $purchaseOrdersfilter : $purchaseOrders)
                    ->pluck('purchaseOrderTables')
                    ->flatten()
                    ->pluck('eta')
                    ->filter()
                    ->unique()
                    ->sort();
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                    type="button"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">
                ETA Date
            </button>

            <div class="dropdown-menu p-2" data-column="12">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter12"> Select All
                </label>
                @foreach($values as $val)
                    @if(!empty($val))
                    @php
                        $formatted = \Carbon\Carbon::parse($val)->format('d-m-Y');
                    @endphp
                    <label class="dropdown-item m-0">
                        <input type="checkbox" name="filters[filter_col_12][]" class="multi-filter multi_filter12"
                        value="{{ $val }}" {{ in_array($val, $selectedValues) ? 'checked' : '' }}>
                        {{ $formatted }}
                    </label>
                    @endif
                @endforeach
                <label class="dropdown-item m-0">
                    <input type="checkbox" name="filters[filter_col_12][]" class="multi-filter multi_filter12" 
                    value="__EMPTY__" {{ in_array('__EMPTY__', $selectedValues) ? 'checked' : '' }}> N/A
                </label>
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="12">OK</button>
                </div>
            </div>
        </div>
    </th>

    <!-- Partial Shipment -->
    <th class="project_table_heading toggle-col">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_13'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';

                // Get all unique partial_shipment values (0, 1)
                $values = ($last_filter_column == 13 ? $purchaseOrdersfilter : $purchaseOrders)
                    ->pluck('purchaseOrderTables')
                    ->flatten()
                    ->pluck('is_partial_shipment')
                    ->unique();
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                    type="button"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                    title="Check Result in Sub Records As Well">
                Partial Shipment
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter13"> Select All
                </label>
                @foreach($values as $val)
                    @php
                        switch($val) {
                            case "0": $label = "No"; break;
                            case "1": $label = "Yes"; break;
                            default: $label = "Unknown"; break;
                        }
                    @endphp
                    <label class="dropdown-item m-0">
                        <input type="checkbox" name="filters[filter_col_13][]" class="multi-filter multi_filter13" 
                        value="{{ $val }}" {{ in_array($val, $selectedValues) ? 'checked' : '' }}>
                        {{ $label }}
                    </label>
                @endforeach
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="13">OK</button>
                </div>
            </div>
        </div>
    </th>

    <!-- Rest of the headers remain the same -->

    <!-- Received Qty -->
    <th class="project_table_heading">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_14'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';

                // Get all unique partial_shipment values (0, 1)
                $values = ($last_filter_column == 14 ? $purchaseOrdersfilter : $purchaseOrders)
                    ->pluck('purchaseOrderTables')
                    ->flatten()
                    ->filter()
                    ->pluck('received_quantity')
                    ->unique()
                    ->sort();
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                    type="button"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                    title="Check Result in Sub Records As Well">
                Received Qty
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter14"> Select All
                </label>            
                @foreach($values as $val)
                    @if(!empty($val))
                    <label class="dropdown-item m-0">
                        <input type="checkbox" name="filters[filter_col_14][]" class="multi-filter multi_filter14" 
                        value="{{ $val }}" {{ in_array($val, $selectedValues) ? 'checked' : '' }}>
                        {{ $val }}
                    </label>
                    @endif
                @endforeach
                <label class="dropdown-item m-0">
                    <input type="checkbox" name="filters[filter_col_14][]" class="multi-filter multi_filter14" 
                    value="__EMPTY__" {{ in_array('__EMPTY__', $selectedValues) ? 'checked' : '' }}> N/A
                </label>
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="14">OK</button>
                </div>
            </div>
        </div>
    </th>    

    <!-- OA Date -->
    <th class="project_table_heading toggle-col">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_15'] ?? [];  
                $filterClass = $selectedValues ? 'filtered_dd' : '';                 
                $values = ($last_filter_column == 15 ? $purchaseOrdersfilter : $purchaseOrders)
                    ->pluck('purchaseOrderTables')
                    ->flatten()
                    ->filter()
                    ->pluck('oa_date')
                    ->unique()
                    ->sort();
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                    type="button"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                    title="Check Result in Sub Records As Well">
                OA Date
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter15"> Select All
                </label>            
                @foreach($values as $val)
                    @if(!empty($val))
                    @php
                        $formatted = \Carbon\Carbon::parse($val)->format('d-m-Y');
                    @endphp
                    <label class="dropdown-item m-0">
                        <input type="checkbox" name="filters[filter_col_15][]" class="multi-filter multi_filter15" 
                        value="{{ $val }}" {{ in_array($val, $selectedValues) ? 'checked' : '' }}>
                        {{ $formatted }}
                    </label>
                    @endif
                @endforeach
                <label class="dropdown-item m-0">
                    <input type="checkbox" name="filters[filter_col_15][]" class="multi-filter multi_filter15" 
                    value="__EMPTY__" {{ in_array('__EMPTY__', $selectedValues) ? 'checked' : '' }}> N/A
                </label>
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="15">OK</button>
                </div>
            </div>
        </div>
    </th>

    <!-- Committed Date -->
    <th class="project_table_heading toggle-col">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_16'] ?? []; 
                $filterClass = $selectedValues ? 'filtered_dd' : '';                   
                $values = ($last_filter_column == 16 ? $purchaseOrdersfilter : $purchaseOrders)
                    ->pluck('purchaseOrderTables')
                    ->flatten()
                    ->filter()
                    ->pluck('committed_date')
                    ->unique()
                    ->sort();
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                    type="button"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                    title="Check Result in Sub Records As Well">
                Committed Date
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter16"> Select All
                </label>            
                @foreach($values as $val)
                    @if(!empty($val))
                    @php
                        $formatted = \Carbon\Carbon::parse($val)->format('d-m-Y');
                    @endphp
                    <label class="dropdown-item m-0">
                        <input type="checkbox" name="filters[filter_col_16][]" class="multi-filter multi_filter16" 
                        value="{{ $val }}" {{ in_array($val, $selectedValues) ? 'checked' : '' }}>
                        {{ $formatted }}
                    </label>
                    @endif
                @endforeach
                <label class="dropdown-item m-0">
                    <input type="checkbox" name="filters[filter_col_16][]" class="multi-filter multi_filter16" 
                    value="__EMPTY__" {{ in_array('__EMPTY__', $selectedValues) ? 'checked' : '' }}> N/A
                </label>
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="16">OK</button>
                </div>
            </div>
        </div>
    </th>

    <!-- Actual Readiness Date -->
    <th class="project_table_heading toggle-col">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_17'] ?? [];    
                $filterClass = $selectedValues ? 'filtered_dd' : '';                
                $values = ($last_filter_column == 17 ? $purchaseOrdersfilter : $purchaseOrders)
                    ->pluck('purchaseOrderTables')
                    ->flatten()
                    ->filter()
                    ->pluck('actual_readiness_date')
                    ->unique()
                    ->sort();
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                    type="button"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                    title="Check Result in Sub Records As Well">
                Actual Readiness Date
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter17"> Select All
                </label>            
                @foreach($values as $val)
                    @if(!empty($val))
                    @php
                        $formatted = \Carbon\Carbon::parse($val)->format('d-m-Y');
                    @endphp
                    <label class="dropdown-item m-0">
                        <input type="checkbox" name="filters[filter_col_17][]" class="multi-filter multi_filter17" 
                        value="{{ $val }}" {{ in_array($val, $selectedValues) ? 'checked' : '' }}>
                        {{ $formatted }}
                    </label>
                    @endif
                @endforeach
                <label class="dropdown-item m-0">
                    <input type="checkbox" name="filters[filter_col_17][]" class="multi-filter multi_filter17" 
                    value="__EMPTY__" {{ in_array('__EMPTY__', $selectedValues) ? 'checked' : '' }}> N/A
                </label>
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="17">OK</button>
                </div>
            </div>
        </div>
    </th>

    <!-- ETA Date (Shipper) -->
    <th class="project_table_heading toggle-col">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_18'] ?? []; 
                $filterClass = $selectedValues ? 'filtered_dd' : '';                   
                $values = ($last_filter_column == 18 ? $purchaseOrdersfilter : $purchaseOrders)
                    ->pluck('purchaseOrderTables')
                    ->flatten()
                    ->filter()
                    ->pluck('eta_date_shipper')
                    ->unique()
                    ->sort();
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                    type="button"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                    title="Check Result in Sub Records As Well">
                ETA Date (Shipper)
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter18"> Select All
                </label>            
                @foreach($values as $val)
                    @if(!empty($val))
                    @php
                        $formatted = \Carbon\Carbon::parse($val)->format('d-m-Y');
                    @endphp
                    <label class="dropdown-item m-0">
                        <input type="checkbox" name="filters[filter_col_18][]" class="multi-filter multi_filter18" 
                        value="{{ $val }}" {{ in_array($val, $selectedValues) ? 'checked' : '' }}>
                        {{ $formatted }}
                    </label>
                    @endif
                @endforeach
                <label class="dropdown-item m-0">
                    <input type="checkbox" name="filters[filter_col_18][]" class="multi-filter multi_filter18" 
                    value="__EMPTY__" {{ in_array('__EMPTY__', $selectedValues) ? 'checked' : '' }}> N/A
                </label>
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="18">OK</button>
                </div>
            </div>
        </div>
    </th>

    <!-- Actual Received Date -->
    <th class="project_table_heading">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_19'] ?? [];   
                $filterClass = $selectedValues ? 'filtered_dd' : '';                 
                $values = ($last_filter_column == 19 ? $purchaseOrdersfilter : $purchaseOrders)
                    ->pluck('purchaseOrderTables')
                    ->flatten()
                    ->filter()
                    ->pluck('actual_received_date')
                    ->unique()
                    ->sort();
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                    type="button"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                    title="Check Result in Sub Records As Well">
                Actual Received Date
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter19"> Select All
                </label>            
                @foreach($values as $val)
                    @if(!empty($val))
                    @php
                        $formatted = \Carbon\Carbon::parse($val)->format('d-m-Y');
                    @endphp
                    <label class="dropdown-item m-0">
                        <input type="checkbox" name="filters[filter_col_19][]" class="multi-filter multi_filter19" 
                        value="{{ $val }}" {{ in_array($val, $selectedValues) ? 'checked' : '' }}>
                        {{ $formatted }}
                    </label>
                    @endif
                @endforeach
                <label class="dropdown-item m-0">
                    <input type="checkbox" name="filters[filter_col_19][]" class="multi-filter multi_filter19" 
                    value="__EMPTY__" {{ in_array('__EMPTY__', $selectedValues) ? 'checked' : '' }}> N/A
                </label>
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="19">OK</button>
                </div>
            </div>
        </div>
    </th>

    <!-- Shipping reference -->
    <th class="project_table_heading toggle-col">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_20'] ?? [];     
                $filterClass = $selectedValues ? 'filtered_dd' : '';               
                $shippingReferences = ($last_filter_column == 20 ? $purchaseOrdersfilter : $purchaseOrders)
                    ->pluck('purchaseOrderTables')
                    ->flatten()
                    ->pluck('shipping_refrence')
                    ->filter()
                    ->unique()
                    ->sort();
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                    type="button"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                    title="Check Result in Sub Records As Well">
                Shipping reference
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter20"> Select All
                </label>            
                @foreach($shippingReferences as $val)
                    @if(!empty($val))
                    <label class="dropdown-item m-0">
                        <input type="checkbox" name="filters[filter_col_20][]" class="multi-filter multi_filter20" 
                        value="{{ $val }}" {{ in_array($val, $selectedValues) ? 'checked' : '' }}>
                        {{ $val }}
                    </label>
                    @endif
                @endforeach
                <label class="dropdown-item m-0">
                    <input type="checkbox" name="filters[filter_col_20][]" class="multi-filter multi_filter20" 
                    value="__EMPTY__" {{ in_array('__EMPTY__', $selectedValues) ? 'checked' : '' }}> N/A
                </label>
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="20">OK</button>
                </div>
            </div>
        </div>
    </th>

    <!-- BOE -->
    <th class="project_table_heading toggle-col">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_21'] ?? [];   
                $filterClass = $selectedValues ? 'filtered_dd' : '';                 
                $boeValues = ($last_filter_column == 21 ? $purchaseOrdersfilter : $purchaseOrders)
                    ->pluck('purchaseOrderTables')
                    ->flatten()
                    ->filter()
                    ->pluck('boe')
                    ->unique()
                    ->sort();
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                    type="button"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                    title="Check Result in Sub Records As Well">
                BOE
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter21"> Select All
                </label>            
                @foreach($boeValues as $val)
                    @if(!empty($val))
                    <label class="dropdown-item m-0">
                        <input type="checkbox" name="filters[filter_col_21][]" class="multi-filter multi_filter21" 
                        value="{{ $val }}" {{ in_array($val, $selectedValues) ? 'checked' : '' }}>
                        {{ $val }}
                    </label>
                    @endif
                @endforeach
                <label class="dropdown-item m-0">
                    <input type="checkbox" name="filters[filter_col_21][]" class="multi-filter multi_filter21" 
                    value="__EMPTY__" {{ in_array('__EMPTY__', $selectedValues) ? 'checked' : '' }}> N/A
                </label>
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="21">OK</button>
                </div>
            </div>
        </div>
    </th>

    <!-- Payment Terms -->
    <th class="project_table_heading toggle-col">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_22'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';
                $paymentTerms = ($last_filter_column == 22 ? $purchaseOrdersfilter : $purchaseOrders)
                    ->pluck('payment_terms')
                    ->filter()
                    ->unique()
                    ->sort();
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                    type="button"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                    title="Check Result in Sub Records As Well">
                Payment Terms
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter22"> Select All
                </label>            
                @foreach($paymentTerms as $val)
                    @if(!empty($val))
                    <label class="dropdown-item m-0">
                        <input type="checkbox" name="filters[filter_col_22][]" class="multi-filter multi_filter22" 
                        value="{{ $val }}" {{ in_array($val, $selectedValues) ? 'checked' : '' }}>
                        {{ $val }}
                    </label>
                    @endif
                @endforeach
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="22">OK</button>
                </div>
            </div>
        </div>
    </th>

    <!-- Remarks -->
    <th class="project_table_heading toggle-col">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_23'] ?? [];  
                $filterClass = $selectedValues ? 'filtered_dd' : '';                  
                $remarksValues = ($last_filter_column == 23 ? $purchaseOrdersfilter : $purchaseOrders)
                    ->pluck('purchaseOrderTables')
                    ->flatten()
                    ->filter()
                    ->pluck('remarks')
                    ->unique()
                    ->sort();
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                    type="button"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                    title="Check Result in Sub Records As Well">
                Remarks
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter23"> Select All
                </label>            
                @foreach($remarksValues as $val)
                    @if(!empty($val))
                    <label class="dropdown-item m-0">
                        <input type="checkbox" name="filters[filter_col_23][]" class="multi-filter multi_filter23" 
                        value="{{ $val }}" {{ in_array($val, $selectedValues) ? 'checked' : '' }}>
                        {{ $val }}
                    </label>
                    @endif
                @endforeach
                <label class="dropdown-item m-0">
                    <input type="checkbox" name="filters[filter_col_23][]" class="multi-filter multi_filter23" 
                    value="__EMPTY__" {{ in_array('__EMPTY__', $selectedValues) ? 'checked' : '' }}> N/A
                </label>
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="23">OK</button>
                </div>
            </div>
        </div>
    </th>

    @if ($is_procurement_login)
    <!-- Response Time -->
    <th class="project_table_heading toggle-col">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_24'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';                    
                $responseTimes = ($last_filter_column == 24 ? $purchaseOrdersfilter : $purchaseOrders)
                    ->pluck('purchaseOrderTables')
                    ->flatten()
                    ->filter()
                    ->pluck('response_time')
                    ->unique()
                    ->sort();
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                    type="button"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                    title="Check Result in Sub Records As Well">
                Response Time
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter24"> Select All
                </label>            
                @foreach($responseTimes as $val)
                    @if(!empty($val))
                    <label class="dropdown-item m-0">
                        <input type="checkbox" name="filters[filter_col_24][]" class="multi-filter multi_filter24" 
                        value="{{ $val }}" {{ in_array($val, $selectedValues) ? 'checked' : '' }}>
                        {{ $val . ' days' }}
                    </label>
                    @endif
                @endforeach
                <label class="dropdown-item m-0">
                    <input type="checkbox" name="filters[filter_col_24][]" class="multi-filter multi_filter24" 
                    value="__EMPTY__" {{ in_array('__EMPTY__', $selectedValues) ? 'checked' : '' }}> N/A
                </label>
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="24">OK</button>
                </div>
            </div>
        </div>
    </th>

    <!-- Delivery time -->
    <th class="project_table_heading toggle-col">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_25'] ?? [];  
                $filterClass = $selectedValues ? 'filtered_dd' : '';                  
                $deliveryTimes = ($last_filter_column == 25 ? $purchaseOrdersfilter : $purchaseOrders)
                    ->pluck('purchaseOrderTables')
                    ->flatten()
                    ->filter()
                    ->pluck('delivery_time')
                    ->unique()
                    ->sort();
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                    type="button"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                    title="Check Result in Sub Records As Well">
                Delivery time
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter25"> Select All
                </label>            
                @foreach($deliveryTimes as $val)
                    @if(!empty($val))
                    <label class="dropdown-item m-0">
                        <input type="checkbox" name="filters[filter_col_25][]" class="multi-filter multi_filter25" 
                        value="{{ $val }}" {{ in_array($val, $selectedValues) ? 'checked' : '' }}>
                        {{ $val . ' days' }}
                    </label>
                    @endif
                @endforeach
                <label class="dropdown-item m-0">
                    <input type="checkbox" name="filters[filter_col_25][]" class="multi-filter multi_filter25" 
                    value="__EMPTY__" {{ in_array('__EMPTY__', $selectedValues) ? 'checked' : '' }}> N/A
                </label>
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="25">OK</button>
                </div>
            </div>
        </div>
    </th>
    @endif
    
    @if ($is_procurement_login)
    <th class="project_table_heading">Action</th>
    @endif

</tr>
