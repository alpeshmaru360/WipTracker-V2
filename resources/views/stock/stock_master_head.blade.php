<tr>
    <th>SR No.</th>

    <!-- Article Number -->
    <th>
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_0'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';

                $query_data = isset($last_filter_column) && $last_filter_column == 0 ? $stocksfilter : $stocks;
                $articleNumbers = $query_data->pluck('article_number')->filter()->unique()->sort();
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                 type="button"
                 data-toggle="dropdown"
                 aria-haspopup="true"
                 aria-expanded="false">
                Article Number
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter0"> Select All
                </label>
                @foreach ($articleNumbers as $val)
                    <label class="dropdown-item m-0 pn_label">
                        <input type="checkbox" name="filters[filter_col_0][]" class="multi-filter multi_filter0" 
                        value="{{ $val }}" {{ in_array($val, $selectedValues) ? 'checked' : '' }}>
                        {{ $val }}
                    </label>
                @endforeach
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="0">OK</button>
                </div>
            </div>
        </div>
    </th>

    <!-- Item Description -->
    <th>
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_1'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';
                $itemDescriptions =  ($last_filter_column == 1 ? $stocksfilter : $stocks)
                    ->pluck('item_desc')
                    ->filter()
                    ->unique()
                    ->sort();
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                 type="button"
                 data-toggle="dropdown"
                 aria-haspopup="true"
                 aria-expanded="false">
                Item Description
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter1"> Select All
                </label>
                @foreach ($itemDescriptions as $val)
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

    <!-- Qty -->
    <th>
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_2'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';
                $quantities = ($last_filter_column == 2 ? $stocksfilter : $stocks)
                    ->pluck('qty')
                    ->unique()
                    ->sort();
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                 type="button"
                 data-toggle="dropdown"
                 aria-haspopup="true"
                 aria-expanded="false" title = "Total QTY">
                Qty
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter2"> Select All
                </label>                
                @foreach ($quantities as $val)
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

    <!-- Reserved [Hold] Qty -->
    <th>
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_3'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';
                $quantities = ($last_filter_column == 3 ? $stocksfilter : $stocks)
                    ->pluck('hold_qty')
                    ->unique()
                    ->sort();
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                 type="button"
                 data-toggle="dropdown"
                 aria-haspopup="true"
                 aria-expanded="false"
                 title = "HOLD QTY">
                Reserved [wip] Qty
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter3"> Select All
                </label>
                @foreach ($quantities as $val)
                    <label class="dropdown-item m-0 pn_label">
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

    <!-- Available Qty -->
    <th>
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_4'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';
                $available_quantities = ($last_filter_column == 4 ? $stocksfilter : $stocks)
                    ->pluck('available_qty')
                    ->unique()
                    ->sort();
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                 type="button"
                 data-toggle="dropdown"
                 aria-haspopup="true"
                 aria-expanded="false"
                 title = "Available QTY">
                Warehouse Qty
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter4"> Select All
                </label>                
                @foreach ($available_quantities as $val)
                    <label class="dropdown-item m-0 pn_label">
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

    <!-- Minimum Required Qty -->
    <th>
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_5'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';
                $minimum_required_quantities = ($last_filter_column == 5 ? $stocksfilter : $stocks)
                    ->pluck('minimum_required_qty')
                    ->unique()
                    ->sort();            
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                 type="button"
                 data-toggle="dropdown"
                 aria-haspopup="true"
                 aria-expanded="false">
                Minimum Required Qty
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter5"> Select All
                </label>                
                @foreach ($minimum_required_quantities as $val)
                    <label class="dropdown-item m-0 pn_label">
                        <input type="checkbox" name="filters[filter_col_5][]" class="multi-filter multi_filter5" 
                        value="{{ $val }}" {{ in_array($val, $selectedValues) ? 'checked' : '' }}>
                        {{ $val }}
                    </label>
                @endforeach
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="5">OK</button>
                </div>
            </div>
        </div>
    </th>

    <!-- [ <span class = "text-danger"> * </span> In Weeks] ETA STD Weeks  -->
    <th>
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_6'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';                
                $stdTimes = ($last_filter_column == 6 ? $stocksfilter : $stocks)
                    ->pluck('std_time')
                    ->unique()
                    ->sort();
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                 type="button"
                 data-toggle="dropdown"
                 aria-haspopup="true"
                 aria-expanded="false">
                [ <span class = "text-danger"> * </span> In Weeks] ETA STD Weeks 
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter6"> Select All
                </label>                
                @foreach ($stdTimes as $val)
                    <label class="dropdown-item m-0 pn_label">
                        <input type="checkbox" name="filters[filter_col_6][]" class="multi-filter multi_filter6" 
                        value="{{ $val }}" {{ in_array($val, $selectedValues) ? 'checked' : '' }}>
                        {{ $val }}
                    </label>
                @endforeach
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="6">OK</button>
                </div>
            </div>
        </div>
    </th>

    <!-- Qty In Order -->
    <th>
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_7'] ?? [];                
                $filterClass = $selectedValues ? 'filtered_dd' : '';
                $values = $stocks
                    ->pluck('total_po_qty')
                    ->unique()
                    ->sort();         
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                 type="button"
                 data-toggle="dropdown"
                 aria-haspopup="true"
                 aria-expanded="false">
                Qty In Order
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter7"> Select All
                </label>   
                <label class="dropdown-item m-0 pn_label">
                    <input type="checkbox" name="filters[filter_col_7][]" class="multi-filter multi_filter7" 
                    value="0" {{ in_array(0, $selectedValues) ? 'checked' : '' }}> 0
                </label>             
                @foreach ($values as $val)
                    @if(!empty($val))
                    <label class="dropdown-item m-0 pn_label">
                        <input type="checkbox" name="filters[filter_col_7][]" class="multi-filter multi_filter7" 
                        value="{{ $val }}" {{ in_array($val, $selectedValues) ? 'checked' : '' }}>
                        {{ $val }}
                    </label>
                    @endif
                @endforeach
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="7">OK</button>
                </div>
            </div>
        </div>
    </th>

    @if($role =="Admin" || Auth::user()->is_admin_login) <!-- A Code: 17-12-2025 -->
    <th>Actions</th>
    @endif
</tr>