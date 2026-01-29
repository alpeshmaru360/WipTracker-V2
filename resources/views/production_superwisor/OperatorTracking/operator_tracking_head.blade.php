<tr>
    <!-- 1. Month/Year -->
    <th scope="col" class="project_table_heading">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_1'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';

                $monthYears = ($last_filter_column == 1 ? $trackingOperatorsfilter : $trackingOperators)
                    ->pluck('projectProcessStdTimes')
                    ->flatten()
                    ->pluck('timer_started_at')
                    ->filter()
                    ->map(function($date){
                        return \Carbon\Carbon::parse($date)->format('d-m-Y'); // only date
                    })
                    ->unique()
                    ->sort()
                    ->values(); // optional: reset array keys
            @endphp                                               
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                    type="button"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">
                Month/Year
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter1"> Select All
                </label>
                @foreach($monthYears as $rawDate)
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

    <!-- 2. Project No. -->
    <th scope="col" class="project_table_heading">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_2'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';
                $projectNumbers = ($last_filter_column == 2 ? $trackingOperatorsfilter : $trackingOperators)
                    ->pluck('projects')
                    ->flatten()
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

    <!-- 3. Project Name [From Sub Table] -->
    <th scope="col" class="project_table_heading">
        <div class="dropdown filter_dropdown">

            @php
                $selectedValues = $filters['filter_col_3'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';
                $projectNames = ($last_filter_column == 3 ? $trackingOperatorsfilter : $trackingOperators)
                    ->pluck('projects')
                    ->flatten()
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
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter3"> Select All
                </label>
                @foreach ($projectNames as $val)
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

    <!-- 4. Product Article No. [From Main Table] -->
    <th scope="col" class="project_table_heading">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_4'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';
                $fullArticleNumbers = ($last_filter_column == 4 ? $trackingOperatorsfilter : $trackingOperators)
                    ->pluck('full_article_number')
                    ->filter()
                    ->unique()
                    ->sort();
            @endphp                                               
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                    type="button"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">
                Product Article No.
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter4"> Select All
                </label>
                @foreach ($fullArticleNumbers as $val)
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

    <!-- 5. Product Description -->
    <th scope="col" class="project_table_heading">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_5'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';
                $descriptions = ($last_filter_column == 5 ? $trackingOperatorsfilter : $trackingOperators)
                    ->pluck('description')
                    ->filter()
                    ->unique()
                    ->sort();
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                    type="button"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">
                Product Description
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter5"> Select All
                </label>
                @foreach ($descriptions as $val)
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

    <!-- 6. Product Type -->
    <th scope="col" class="project_table_heading">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_6'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';
                $productTypes = ($last_filter_column == 6 ? $trackingOperatorsfilter : $trackingOperators)
                    ->pluck('product_type')
                    ->filter()
                    ->unique()
                    ->sort();
            @endphp                                               
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                    type="button"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">
                Product Type
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter6"> Select All
                </label>
                @foreach ($productTypes as $val)
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

    <!-- 7. Total Product Qty -->
    <th scope="col" class="project_table_heading">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_7'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';
                $totalProductQty = ($last_filter_column == 7 ? $trackingOperatorsfilter : $trackingOperators)
                    ->pluck('qty')
                    ->unique()
                    ->sort();
            @endphp                                    
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                    type="button"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">
                Total Product Qty
            </button>            
            
            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter7"> Select All
                </label>
                @foreach ($totalProductQty as $val)
                    <label class="dropdown-item m-0 pn_label">
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

    <!-- 8. Completed Product Qty -->
    <th scope="col" class="project_table_heading">
        <div class="dropdown filter_dropdown"> 
            @php
                $selectedValues = $filters['filter_col_8'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';

                $projects = ($last_filter_column == 8 ? $trackingOperatorsfilter : $trackingOperators);

                // Compute completed qty exactly like Blade
                $completedProductQty = $projects->map(function($item){

                    $completed_qty = $item->projectProcessStdTimes
                        ->groupBy('order_qty')
                        ->filter(function ($group) {
                            return $group->every(fn($row) => (int)$row->project_status === 1);
                        })
                        ->count();

                    return $completed_qty;

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
                Completed Product Qty
            </button>
            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter8"> Select All
                </label>
                @foreach ($completedProductQty as $val)
                    <label class="dropdown-item m-0 pn_label">
                        <input type="checkbox" name="filters[filter_col_8][]"
                            class="multi-filter multi_filter8"
                            value="{{ $val }}"
                            {{ in_array($val, $selectedValues) ? 'checked' : '' }}>
                        {{ $val }}
                    </label>
                @endforeach
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="8">OK</button>
                </div>
            </div>
        </div>
    </th>

    <!-- 9. Qty of Total Operators -->
    <th scope="col" class="project_table_heading">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_9'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';                
                //$qtyofTotalOperators = [1,2,3,4,5,6,7,8,9,10,11];

                $projects = ($last_filter_column == 9 ? $trackingOperatorsfilter : $trackingOperators);

                // Compute total distinct operators per product
                $qtyofTotalOperators = $projects->map(function($item){

                    $operatorIds = $item->projectProcessStdTimes
                        ->pluck('operators_time_tracking')
                        ->filter()
                        ->map(fn($json) => json_decode($json, true))
                        ->filter(fn($arr) => is_array($arr))
                        ->flatten(1)
                        ->pluck('id')
                        ->unique();

                    return $operatorIds->count();

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
                Qty of Total Operators
            </button>
            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter9"> Select All
                </label>
                @foreach ($qtyofTotalOperators as $val)
                    <label class="dropdown-item m-0 pn_label">
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

    <!-- 10. Qty Wilo Operators -->
    <th scope="col" class="project_table_heading">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_10'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';
                //$qtyWiloOperators = [0,1,2,3,4,5,6,7];

                $projects = ($last_filter_column == 10 ? $trackingOperatorsfilter : $trackingOperators);

                // Compute total Wilo operators per product
                $qtyWiloOperators = $projects->map(function($item){

                    $operatorIds = $item->projectProcessStdTimes
                        ->pluck('operators_time_tracking')
                        ->filter()
                        ->map(fn($json) => json_decode($json, true))
                        ->filter(fn($arr) => is_array($arr))
                        ->flatten(1)
                        ->pluck('id')
                        ->unique();

                    // Count only Wilo Operators
                    return \App\Models\User::whereIn('id', $operatorIds)
                        ->where('role', 'Wilo Operator')
                        ->count();

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
                Qty Wilo Operators
            </button>
            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter10"> Select All
                </label>
                @foreach ($qtyWiloOperators as $val)
                    <label class="dropdown-item m-0 pn_label">
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

    <!-- 11. Qty 3rd Party Operators -->
    <th scope="col" class="project_table_heading">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_11'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';
                //$qty3rdPartyOperators = [0,1,2,3,4,5,6,7];

                $projects = ($last_filter_column == 11 ? $trackingOperatorsfilter : $trackingOperators);

                // Compute total 3rd Party operators per product
                $qty3rdPartyOperators = $projects->map(function($item){

                    $operatorIds = $item->projectProcessStdTimes
                        ->pluck('operators_time_tracking')
                        ->filter()
                        ->map(fn($json) => json_decode($json, true))
                        ->filter(fn($arr) => is_array($arr))
                        ->flatten(1)
                        ->pluck('id')
                        ->unique();

                    // Count only 3rd Party Operators
                    return \App\Models\User::whereIn('id', $operatorIds)
                        ->where('role', '3rd Party Operator')
                        ->count();

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
                Qty 3rd Party Operators
            </button>
            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter11"> Select All
                </label>
                @foreach ($qty3rdPartyOperators as $val)
                    <label class="dropdown-item m-0 pn_label">
                        <input type="checkbox" name="filters[filter_col_11][]"
                            class="multi-filter multi_filter11"
                            value="{{ $val }}"
                            {{ in_array($val, $selectedValues) ? 'checked' : '' }}>
                        {{ $val }}
                    </label>
                @endforeach
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="11">OK</button>
                </div>
            </div>
        </div>
    </th>

    <!-- 12. Total Man hours -->
    <th scope="col" class="project_table_heading">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_12'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';

                $projects = ($last_filter_column == 12 ? $trackingOperatorsfilter : $trackingOperators);
                // Extract numeric hour values
                $totalManHours = $projects->map(function($item){

                    $operatorTimes = $item->projectProcessStdTimes->pluck('operators_time_tracking')
                        ->filter()
                        ->map(fn($json) => json_decode($json, true))
                        ->filter(fn($arr) => is_array($arr))
                        ->flatten(1);

                    return round($operatorTimes->sum('total_time') / 60, 2); // SAME AS YOUR BLADE COLUMN
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
                Total Man hours
            </button>
            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter12"> Select All
                </label>
                @foreach ($totalManHours as $val)
                    <label class="dropdown-item m-0 pn_label">
                        <input type="checkbox" name="filters[filter_col_12][]" class="multi-filter multi_filter12" 
                        value="{{ $val }}" {{ in_array($val, $selectedValues) ? 'checked' : '' }}>
                        {{ $val }}
                    </label>
                @endforeach
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="12">OK</button>
                </div>
            </div>
        </div>
    </th>

    <!-- 13. Total Wilo Operator Hours -->
    <th scope="col" class="project_table_heading">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_13'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';  
                
                $projects = ($last_filter_column == 13 ? $trackingOperatorsfilter : $trackingOperators);
                $wiloHoursList = $projects->map(function($item){
                    $operatorTimes = $item->projectProcessStdTimes
                        ->pluck('operators_time_tracking')
                        ->filter()
                        ->map(fn($json) => json_decode($json, true))
                        ->filter(fn($arr) => is_array($arr))
                        ->flatten(1);
                    // Filter only Wilo operators
                    $wiloHours = $operatorTimes->filter(function($op){
                        return \App\Models\User::where('id', $op['id'])
                            ->where('role', 'Wilo Operator')
                            ->exists();
                    })->sum('total_time') / 60;
                    return round($wiloHours, 2);
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
                Total Wilo Operator Hours
            </button>
            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter13"> Select All
                </label>
                @foreach ($wiloHoursList as $val)
                    <label class="dropdown-item m-0 pn_label">
                        <input type="checkbox"
                            name="filters[filter_col_13][]"
                            class="multi-filter multi_filter13"
                            value="{{ $val }}"
                            {{ in_array($val, $selectedValues) ? 'checked' : '' }}>
                        {{ $val }}
                    </label>
                @endforeach
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="13">OK</button>
                </div>
            </div>
        </div>
    </th>

    <!-- 14. Total 3rd Party Operators Hours -->
    <th scope="col" class="project_table_heading">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_14'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';          

                $projects = ($last_filter_column == 14 ? $trackingOperatorsfilter : $trackingOperators);

                $thirdPartyHoursList = $projects->map(function($item){

                    $operatorTimes = $item->projectProcessStdTimes
                        ->pluck('operators_time_tracking')
                        ->filter()
                        ->map(fn($json) => json_decode($json, true))
                        ->filter(fn($arr) => is_array($arr))
                        ->flatten(1);

                    // Filter only 3rd Party Operators
                    $thirdPartyHours = $operatorTimes->filter(function($op){
                        return \App\Models\User::where('id', $op['id'])
                            ->where('role', '3rd Party Operator')
                            ->exists();
                    })->sum('total_time') / 60;

                    return round($thirdPartyHours, 2);

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
                Total 3rd Party Operators Hours
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter14"> Select All
                </label>
                @foreach ($thirdPartyHoursList as $val)
                    <label class="dropdown-item m-0 pn_label">
                        <input type="checkbox"
                            name="filters[filter_col_14][]"
                            class="multi-filter multi_filter14"
                            value="{{ $val }}"
                            {{ in_array($val, $selectedValues) ? 'checked' : '' }}>
                        {{ $val }}
                    </label>
                @endforeach
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="14">OK</button>
                </div>
            </div>
        </div>
    </th>

    <!-- 15. Total Hours/Unit -->
    <th scope="col" class="project_table_heading">
        <div class="dropdown filter_dropdown">
            @php
                $selectedValues = $filters['filter_col_15'] ?? [];
                $filterClass = $selectedValues ? 'filtered_dd' : '';         

                $projects = ($last_filter_column == 15 ? $trackingOperatorsfilter : $trackingOperators);

                $totalHoursUnitList = $projects->map(function($item){

                    // ---------- 1. Calculate TOTAL MAN HOURS ----------
                    $operatorTimes = $item->projectProcessStdTimes
                        ->pluck('operators_time_tracking')
                        ->filter()
                        ->map(fn($json) => json_decode($json, true))
                        ->filter(fn($arr) => is_array($arr))
                        ->flatten(1);

                    $totalHours = $operatorTimes->sum('total_time') / 60;

                    // ---------- 2. Calculate COMPLETED QTY ----------
                    $completedQty = $item->projectProcessStdTimes
                        ->groupBy('order_qty')
                        ->filter(function ($group) {
                            return $group->every(fn($row) => (int)$row->project_status === 1);
                        })
                        ->count();


                    // ---------- 3. Calculate TOTAL HOURS / UNIT ----------
                    $totalUnitHours = $completedQty > 0 ? round($totalHours / $completedQty, 2) : 0;
                    return $totalUnitHours;

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
                Total Hours/Unit
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter15"> Select All
                </label>
                 @foreach ($totalHoursUnitList as $val)
                    <label class="dropdown-item m-0 pn_label">
                        <input type="checkbox"
                            name="filters[filter_col_15][]"
                            class="multi-filter multi_filter15"
                            value="{{ $val }}"
                            {{ in_array($val, $selectedValues) ? 'checked' : '' }}>
                        {{ $val }}
                    </label>
                @endforeach
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="15">OK</button>
                </div>
            </div>
        </div>
    </th>
</tr>