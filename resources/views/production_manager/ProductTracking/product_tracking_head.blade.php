<tr>
    <!-- 0 SR NO. -->
    <th scope="col" class="project_table_heading">
        SR NO.
    </th>

    <!-- 1 Status -->
    <th scope="col" class="project_table_heading">
        <div class="dropdown filter_dropdown">
            @php
            $selectedValues = $filters['filter_col_1'] ?? [];
            $filterClass = $selectedValues ? 'filtered_dd' : '';

            $statusCodes = ($last_filter_column == 1 ? $trackingProductsfilter : $trackingProducts)
            ->pluck('projects')
            ->flatten()
            ->pluck('status')
            ->unique()
            ->sort();

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
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter1"> Select All
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
                    <input type="checkbox" name="filters[filter_col_1][]" class="multi-filter multi_filter1"
                        value="{{ $statusCode }}" {{ in_array($statusCode, $selectedValues) ? 'checked' : '' }}>
                    {{ $statusLabel }}
                </label>
                @endforeach
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="1">OK</button>
                </div>
            </div>
        </div>
    </th>

    <!-- 2 Completation Date -->
    <th scope="col" class="project_table_heading">
        <div class="dropdown filter_dropdown">
            @php
            $selectedValues = $filters['filter_col_2'] ?? [];
            $filterClass = $selectedValues ? 'filtered_dd' : '';
            $completationDates = ($last_filter_column == 2 ? $trackingProductsfilter : $trackingProducts)
            ->pluck('projects')
            ->flatten()
            ->pluck('pl_uploaded_date')
            ->filter()
            ->unique()
            ->sort();

            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                type="button"
                data-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false">
                Completion Date
            </button>
            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter2"> Select All
                </label>
                @foreach ($completationDates as $val)
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

    <!-- 3 Sales Number -->
    <th scope="col" class="project_table_heading">
        <div class="dropdown filter_dropdown">
            @php
            $selectedValues = $filters['filter_col_3'] ?? [];
            $filterClass = $selectedValues ? 'filtered_dd' : '';
            $salesNumbers = ($last_filter_column == 3 ? $trackingProductsfilter : $trackingProducts)
            ->pluck('projects')
            ->flatten()
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
                SO Number
            </button>
            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter3"> Select All
                </label>
                @foreach ($salesNumbers as $val)
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

    <!-- 4 Project Number -->
    <th scope="col" class="project_table_heading">
        <div class="dropdown filter_dropdown">

            @php
            $selectedValues = $filters['filter_col_4'] ?? [];
            $filterClass = $selectedValues ? 'filtered_dd' : '';     
            $projectNumbers = ($last_filter_column == 4 ? $trackingProductsfilter : $trackingProducts)
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
                Project Number
            </button>

            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter4"> Select All
                </label>
                @foreach ($projectNumbers as $val)
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

    <!-- 5 Project Name -->
    <th scope="col" class="project_table_heading">
        <div class="dropdown filter_dropdown">

            @php
            $selectedValues = $filters['filter_col_5'] ?? [];
            $filterClass = $selectedValues ? 'filtered_dd' : '';
            $projectNames = ($last_filter_column == 5 ? $trackingProductsfilter : $trackingProducts)
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
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter5"> Select All
                </label>
                @foreach ($projectNames as $val)
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

    <!-- 6 Country -->
    <th scope="col" class="project_table_heading">
        <div class="dropdown filter_dropdown">
            @php
            $selectedValues = $filters['filter_col_6'] ?? [];
            $filterClass = $selectedValues ? 'filtered_dd' : '';
            $countries = ($last_filter_column == 6 ? $trackingProductsfilter : $trackingProducts)
            ->pluck('projects')
            ->flatten()
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
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter6"> Select All
                </label>
                @foreach ($countries as $val)
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

    <!-- 7 Product Article Number -->
    <th scope="col" class="project_table_heading">
        <div class="dropdown filter_dropdown">
            @php
            $selectedValues = $filters['filter_col_7'] ?? [];
            $filterClass = $selectedValues ? 'filtered_dd' : '';
            $fullArticleNumbers = ($last_filter_column == 7 ? $trackingProductsfilter : $trackingProducts)
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
                Product Article Number
            </button>
            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter7"> Select All
                </label>
                @foreach ($fullArticleNumbers as $val)
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

    <!-- 8 Product Description -->
    <th scope="col" class="project_table_heading">
        <div class="dropdown filter_dropdown">
            @php
            $selectedValues = $filters['filter_col_8'] ?? [];
            $filterClass = $selectedValues ? 'filtered_dd' : '';
            $descriptions = ($last_filter_column == 8 ? $trackingProductsfilter : $trackingProducts)
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
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter8"> Select All
                </label>
                @foreach ($descriptions as $val)
                <label class="dropdown-item m-0 pn_label">
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

    <!-- 9 Product Type -->
    <th scope="col" class="project_table_heading">
        <div class="dropdown filter_dropdown">
            @php
            $selectedValues = $filters['filter_col_9'] ?? [];
            $filterClass = $selectedValues ? 'filtered_dd' : '';
            $productTypes = ($last_filter_column == 9 ? $trackingProductsfilter : $trackingProducts)
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
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter9"> Select All
                </label>
                @foreach ($productTypes as $val)
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

    <!-- 10 Product Family Number -->
    <th scope="col" class="project_table_heading">
        <div class="dropdown filter_dropdown">
            @php
            $selectedValues = $filters['filter_col_10'] ?? [];
            $filterClass = $selectedValues ? 'filtered_dd' : '';
            $productTypes = ($last_filter_column == 10 ? $trackingProductsfilter : $trackingProducts)
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
                Product Family Number
            </button>
            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter10"> Select All
                </label>
                @foreach ($productTypes as $val)
                <label class="dropdown-item m-0 pn_label">
                    <input type="checkbox" name="filters[filter_col_10][]" class="multi-filter multi_filter10"
                        value="{{ $val }}" {{ in_array($val, $selectedValues) ? 'checked' : '' }}>
                    {{-- $val --}}
                    {{ \App\Models\ProductType::where('project_type_name', $val)->value('product_family_number') }}
                </label>
                @endforeach
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="10">OK</button>
                </div>
            </div>
        </div>
    </th>

    <!-- 11 Product Quantity -->
    <th scope="col" class="project_table_heading">
        <div class="dropdown filter_dropdown">
            @php
            $selectedValues = $filters['filter_col_11'] ?? [];
            $filterClass = $selectedValues ? 'filtered_dd' : '';
            $productQuantities = ($last_filter_column == 11 ? $trackingProductsfilter : $trackingProducts)
            ->pluck('qty')
            ->unique()
            ->sort();
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                type="button"
                data-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false">
                Product Quantity
            </button>
            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter11"> Select All
                </label>
                @foreach ($productQuantities as $val)
                <label class="dropdown-item m-0 pn_label">
                    <input type="checkbox" name="filters[filter_col_11][]" class="multi-filter multi_filter11"
                        value="{{ $val }}" {{ in_array($val, $selectedValues) ? 'checked' : '' }}>
                    {{ $val }}
                </label>
                @endforeach
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="11">OK</button>
                </div>
            </div>
        </div>
    </th>

    <!-- A Code: 19-01-2026 Start -->
    <!-- 13 Unit Wise Sales Value -->
    <th scope="col" class="project_table_heading">
        <div class="dropdown filter_dropdown">
            @php
            $selectedValues = $filters['filter_col_13'] ?? [];
            $filterClass = $selectedValues ? 'filtered_dd' : '';
            $unitPrices = ($last_filter_column == 13 ? $trackingProductsfilter : $trackingProducts)
            ->pluck('unit_price')
            ->unique()
            ->sort();
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                type="button"
                data-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false">
                <!-- Unit Wise Sales Value -->
                Unit Sales Value [EUR]
            </button>
            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter13"> Select All
                </label>
                @foreach ($unitPrices as $val)
                <label class="dropdown-item m-0 pn_label">
                    <input type="checkbox" name="filters[filter_col_13][]" class="multi-filter multi_filter13"
                        value="{{ $val }}" {{ in_array($val, $selectedValues) ? 'checked' : '' }}>
                    {{ $val }}
                </label>
                @endforeach
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="13">OK</button>
                </div>
            </div>
        </div>
    </th>

    <!-- 14 Total Sales Value -->
    <th scope="col" class="project_table_heading">
        <div class="dropdown filter_dropdown">
            @php
            $selectedValues = $filters['filter_col_14'] ?? [];
            $filterClass = $selectedValues ? 'filtered_dd' : '';
            $totalPrices = ($last_filter_column == 14 ? $trackingProductsfilter : $trackingProducts)
            ->pluck('total_price')
            ->unique()
            ->sort();
            @endphp
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start {{ $filterClass }}"
                type="button"
                data-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false">
                <!-- Total Sales Value -->
                Total Sales Value [EUR]
            </button>
            <div class="dropdown-menu p-2">
                <label class="dropdown-item font-weight-bold">
                    <input type="checkbox" class="select-all-filter" data-target=".multi_filter14"> Select All
                </label>
                @foreach ($totalPrices as $val)
                <label class="dropdown-item m-0 pn_label">
                    <input type="checkbox" name="filters[filter_col_14][]" class="multi-filter multi_filter14"
                        value="{{ $val }}" {{ in_array($val, $selectedValues) ? 'checked' : '' }}>
                    {{ $val }}
                </label>
                @endforeach
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success w-100 apply-filter-btn" data-column="14">OK</button>
                </div>
            </div>
        </div>
    </th>
    <!-- A Code: 19-01-2026 End -->

</tr>