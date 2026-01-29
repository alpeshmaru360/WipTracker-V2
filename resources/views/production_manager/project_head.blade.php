<tr id="column-filters">
    <!-- Status -->
    <th scope="col" class="project_table_heading p-1">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_0'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';

                $query_data = isset($last_filter_column) && $last_filter_column == 0 ? $projectfilter : $project;
                $statusCodes = $query_data->pluck('status')->unique();
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                 type="button"
                 data-toggle="dropdown"
                 aria-haspopup="true"
                 aria-expanded="false">
                Status
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter0"> Select All
                </label>                
                @foreach($statusCodes as $statusCode)
                    @php
                        switch($statusCode) {
                            case "0": $statusLabel = "Open"; break;
                            case "1": $statusLabel = "InProgress"; break;
                            case "2": $statusLabel = "Completed"; break;
                            default: $statusLabel = "Unknown"; break;
                        }
                    @endphp
                    <label class="dropdown-item m-0">
                        <input type="checkbox" name="filters[filter_col_0][]" class="multi-filter multi_filter0" 
                        value="{{ $statusCode }}" {{ in_array($statusCode, $selectedValues) ? 'checked' : '' }}>
                        {{ $statusLabel }}
                    </label>
                @endforeach
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="0">OK</button>
                </div>

                <!-- <div class="d-flex justify-content-between gap-2 mt-3 px-1">
                    <button type="button" class="btn btn-sm btn-success apply-filter-btn" data-column="0">
                        <i class="fas fa-check-circle mr-1"></i> 
                        OK
                    </button>
                    <button type="button" class="btn btn-outline-secondary reset-filter-btn w-50" data-column="0">
                        <i class="fas fa-undo mr-1"></i> 
                        Reset
                    </button>
                </div> -->

            </div>
        </div>
    </th> 

    <!-- Date -->
    <th scope="col" class="project_table_heading p-1" style="width: 8% !important;">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_1'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';
                $projectCreateDates = ($last_filter_column == 1 ? $projectfilter : $project)
                    ->pluck('wip_project_create_date')
                    ->filter()
                    ->map(function ($date) {
                        return \Carbon\Carbon::parse($date)->toDateString();
                    })
                    ->unique()
                    ->sort()
                    ->values();
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                 type="button"
                 data-toggle="dropdown"
                 aria-haspopup="true"
                 aria-expanded="false">
                Date
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter1"> Select All
                </label>                
                @foreach($projectCreateDates as $rawDate)
                    @php
                        $formatted = \Carbon\Carbon::parse($rawDate)->format('d-m-Y');
                    @endphp
                    <label class="dropdown-item m-0">
                        <input type="checkbox" name="filters[filter_col_1][]" class="multi-filter multi_filter1" 
                        value="{{ $rawDate }}" {{ in_array($rawDate, $selectedValues) ? 'checked' : '' }}>
                        {{ $formatted }}
                    </label>
                @endforeach
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="1">OK</button>
                </div>
            </div>
        </div>
    </th>

    <!-- Customer Ref. -->
    <th scope="col" class="project_table_heading p-1">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_2'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';
                $customerRef = ($last_filter_column == 2 ? $projectfilter : $project)
                    ->pluck('customer_ref')
                    ->filter()
                    ->unique()
                    ->sort();
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                 type="button"
                 data-toggle="dropdown"
                 aria-haspopup="true"
                 aria-expanded="false">
                Customer Ref.
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter2"> Select All
                </label>
                @foreach($customerRef as $val)
                    <label class="dropdown-item m-0">
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

     <!-- Sales Number. -->
    <th scope="col" class="project_table_heading p-1">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_10'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';
                $sales_order_number = ($last_filter_column == 10 ? $projectfilter : $project)
                    ->pluck('sales_order_number')
                    ->filter()
                    ->unique()
                    ->sort();
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                 type="button"
                 data-toggle="dropdown"
                 aria-haspopup="true"
                 aria-expanded="false">
                Sales Order No.
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter10"> Select All
                </label>
                @foreach($sales_order_number as $val)
                    <label class="dropdown-item m-0">
                        <input type="checkbox" name="filters[filter_col_10][]" class="multi-filter multi_filter10" 
                        value="{{ $val }}" {{ in_array($val, $selectedValues) ? 'checked' : '' }}>
                        {{ $val }}
                    </label>
                @endforeach
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="10">OK</button>
                </div>
            </div>
        </div>
    </th>

    <!-- Project No. -->
    <th scope="col" class="project_table_heading p-1">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_3'] ?? []; 
                $filterClass = $selectedValues ? 'filtered_dd' : '';                   
                $projectNumbers = ($last_filter_column == 3 ? $projectfilter : $project)
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
                Project No.
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter3"> Select All
                </label>
                @foreach($projectNumbers as $val)
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

    <!-- Project Name -->
    <th scope="col" class="project_table_heading p-1">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_4'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';
                $projectNames = ($last_filter_column == 4 ? $projectfilter : $project)
                    ->pluck('project_name')
                    ->filter()
                    ->unique()
                    ->sort();
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
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter4"> Select All
                </label>

                @foreach($projectNames as $val)
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

    <!-- Country -->
    <th scope="col" class="project_table_heading p-1">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_5'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';
                $countries = ($last_filter_column == 5 ? $projectfilter : $project)
                    ->pluck('country')
                    ->filter()
                    ->unique()
                    ->sort();
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                 type="button"
                 data-toggle="dropdown"
                 aria-haspopup="true"
                 aria-expanded="false">
                Country
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter5"> Select All
                </label>
                @foreach($countries as $val)
                    <label class="dropdown-item m-0">
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

    <!-- Customer Name -->
    <th scope="col" class="project_table_heading p-1">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_6'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';
                $customerNames = ($last_filter_column == 6 ? $projectfilter : $project)
                    ->pluck('customer_name')
                    ->filter()
                    ->unique()
                    ->sort();
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                 type="button"
                 data-toggle="dropdown"
                 aria-haspopup="true"
                 aria-expanded="false">
                Customer Name
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter6"> Select All
                </label>
                @foreach($customerNames as $val)
                    <label class="dropdown-item m-0">
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

    <!-- Sales Name -->
    <th scope="col" class="project_table_heading p-1">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_7'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';
                $salesNames = ($last_filter_column == 7 ? $projectfilter : $project)
                    ->pluck('sales_name')
                    ->filter()
                    ->unique()
                    ->sort();
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                 type="button"
                 data-toggle="dropdown"
                 aria-haspopup="true"
                 aria-expanded="false">
                Sales Name
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter7"> Select All
                </label>
                @foreach($salesNames as $val)
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

    <!-- Estimated Readiness -->
    <th scope="col" class="project_table_heading p-1">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_8'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';
                $estimatedReadiness = ($last_filter_column == 8 ? $projectfilter : $project)
                    ->pluck('estimated_readiness')
                    ->filter()
                    ->unique()
                    ->sort();
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                 type="button"
                 data-toggle="dropdown"
                 aria-haspopup="true"
                 aria-expanded="false">
                Estimated Readiness
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter8"> Select All
                </label>
                @foreach($estimatedReadiness as $rawDate)
                    @php
                        $formatted = \Carbon\Carbon::parse($rawDate)->format('d-m-Y');
                    @endphp
                    <label class="dropdown-item m-0">
                        <input type="checkbox" name="filters[filter_col_8][]" class="multi-filter multi_filter8" 
                        value="{{ $rawDate }}" {{ in_array($rawDate, $selectedValues) ? 'checked' : '' }}>
                        {{ $formatted }}
                    </label>
                @endforeach
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="8">OK</button>
                </div>
            </div>
        </div>
    </th>

    <!-- Actual Readiness -->
    <th scope="col" class="project_table_heading p-1">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_9'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                 type="button"
                 data-toggle="dropdown"
                 aria-haspopup="true"
                 aria-expanded="false">
                Actual Readiness
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter9"> Select All
                </label>
                @foreach(
                    $project->map(function ($item) {
                        return $item->actual_readiness ?? $item->estimated_readiness;
                    })->unique()->sort() as $rawDate
                )
                    @php
                        $formatted = \Carbon\Carbon::parse($rawDate)->format('d-m-Y');
                    @endphp
                    <label class="dropdown-item m-0">
                        <input type="checkbox" name="filters[filter_col_9][]" class="multi-filter multi_filter9" 
                        value="{{ $formatted }}" {{ in_array($formatted, $selectedValues) ? 'checked' : '' }}>
                        {{ $formatted }}
                    </label>
                @endforeach
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="9">OK</button>
                </div>
            </div>
        </div>
    </th>

    <th scope="col" class="project_table_heading p-1">Docs</th>
    <th scope="col" class="project_table_heading p-1">Check Status</th>
    <th scope="col" class="project_table_heading p-1" style="width: 8% !important;">Action</th>
</tr>                    
