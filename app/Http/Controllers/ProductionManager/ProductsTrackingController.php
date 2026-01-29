<?php

namespace App\Http\Controllers\ProductionManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Services\DashboardService;
use App\Models\Project;
use App\Models\ProductsOfProjects;
use App\Models\ProductType;

class ProductsTrackingController extends Controller
{   
    public function index(Request $request){
        $role = auth()->user()->role;
        $page_title = "Products Tracking";

        $trackingProductsfilter = (object)[];
        $last_filter_column = $request->input('last_filter_column');

        $status = $request->input('status'); // A Code: 15-12-2025

        if(isset($last_filter_column)){
            $trackingProductsfilter = ProductsOfProjects::with('projects')              
                ->orderBy('id', 'desc')
                ->get();
        }

        $filters = $request->input('filters', []); 

        // Load only matching child rows
        $query = ProductsOfProjects::select('*')
                ->with(['projects' => function ($q) use ($filters) {
                    $q->orderBy('id', 'desc');

                $columnMap = [
                    'filter_col_1'  => 'status',
                    'filter_col_2'  => 'pl_uploaded_date',
                    'filter_col_3'  => 'sales_order_number',
                    'filter_col_4'  => 'project_no',
                    'filter_col_5'  => 'project_name',
                    'filter_col_6'  => 'country'
                    //'filter_col_12'  => 'currency' // A Code: 19-01-2026 Commented
                ];

                foreach ($columnMap as $filterKey => $dbColumn) {
                    if (!empty($filters[$filterKey])) {
                        $filterValues = $filters[$filterKey];

                        $hasEmpty = in_array('__EMPTY__', $filterValues);
                        $nonEmptyValues = array_filter($filterValues, fn($v) => $v !== '__EMPTY__');

                        $q->where(function ($subQuery) use ($dbColumn, $hasEmpty, $nonEmptyValues) {
                            if (!empty($nonEmptyValues)) {
                                $subQuery->whereIn($dbColumn, $nonEmptyValues);
                            }

                            if ($hasEmpty) {
                                $subQuery->orWhereNull($dbColumn)->orWhere($dbColumn, '');
                            }
                        });
                    }
                }
        }])
        ->orderBy('id', 'desc');

        // Apply parent-level filters
        foreach ($filters as $key => $values) {
            if (empty($values)) continue;

            switch ($key) {               

                // Direct parent-table filters
                case 'filter_col_7': $query->whereIn('full_article_number', $values); break;
                case 'filter_col_8': $query->whereIn('description', $values); break;
                case 'filter_col_9': $query->whereIn('product_type', $values); break;
                case 'filter_col_11': $query->whereIn('qty', $values); break;
                case 'filter_col_13': $query->whereIn('unit_price', $values); break;
                case 'filter_col_14': $query->whereIn('total_price', $values); break;
                //case 'filter_col_15': $query->whereIn('currency_wise_sales_unit_value', $values); break; // A Code: 19-01-2026 Commented
                //case 'filter_col_16': $query->whereIn('currency_wise_sales_total_value', $values); break; // A Code: 19-01-2026 Commented

                // All child-table filters handled below
                default:
                    $childColumnMap = [
                        'filter_col_1'  => 'status',
                        'filter_col_2'  => 'pl_uploaded_date',
                        'filter_col_3'  => 'sales_order_number',
                        'filter_col_4'  => 'project_no',
                        'filter_col_5'  => 'project_name',
                        'filter_col_6'  => 'country'
                        //'filter_col_12'  => 'currency' // A Code: 19-01-2026 Commented
                    ];
                    
                    if (isset($childColumnMap[$key])) {
                        $column = $childColumnMap[$key];

                        $hasEmpty = in_array('__EMPTY__', $values);
                        $nonEmptyValues = array_filter($values, fn($v) => $v !== '__EMPTY__');

                        $query->whereHas('projects', function ($q) use ($column, $nonEmptyValues, $hasEmpty) {
                            $q->where(function ($sub) use ($column, $nonEmptyValues, $hasEmpty) {
                                if (!empty($nonEmptyValues)) {
                                    $sub->whereIn($column, $nonEmptyValues);
                                }

                                if ($hasEmpty) {
                                    $sub->orWhereNull($column)->orWhere($column, '');
                                }
                            });
                        });
                    }
                    break;
            }
        }

        // A Code: 15-12-2025 Start
        // PROJECT STATUS DROPDOWN FILTER
        // 0 = Open, 1 = Work In Progress, 2 = Completed, 3 = All
        if (isset($status) && $status !== '3') {
            $query->whereHas('projects', function ($q) use ($status) {
                $q->where('status', $status);
            });
        }
        // A Code: 15-12-2025 End

        // A Code: 26-12-2025 Start
        $query->whereHas('projects', function ($q) {
            $q->where('is_deleted', 0);
        });
        // A Code: 26-12-2025 End

        $trackingProducts = $query->get();

        // A Code: 15-12-2025 Start
        // AJAX response
        if ($request->ajax()) {
            $rowView = view('production_manager.ProductTracking.product_tracking_body', compact('trackingProducts'))->render();
            $headView = view('production_manager.ProductTracking.product_tracking_head', compact('trackingProducts', 'filters', 'trackingProductsfilter', 'last_filter_column'))->render();

            $project_numbers = $trackingProducts->pluck('project_no')
                ->filter()
                ->unique()
                ->sort()
                ->values()
                ->all();

            return response()->json([
                'html' => $rowView,
                'head' => $headView,
                'project_numbers' => $project_numbers
            ]);
        }
        // A Code: 15-12-2025 End

        return view('production_manager.ProductTracking.index', compact(
            'page_title', 'trackingProducts', 'filters', 'trackingProductsfilter', 'last_filter_column'
        ));
    }
    
    public function exportCSV(Request $request){

        $filters = $request->input('filters', []); 
        // Load only matching child rows
        $query = ProductsOfProjects::with(['projects' => function ($q) use ($filters) {
            $q->orderBy('id', 'desc');

            $columnMap = [
                'filter_col_1'  => 'status',
                'filter_col_2'  => 'pl_uploaded_date',
                'filter_col_3'  => 'sales_order_number',
                'filter_col_4'  => 'project_no',
                'filter_col_5'  => 'project_name',
                'filter_col_6'  => 'country'
                //'filter_col_12'  => 'currency' // A Code: 19-01-2026 Commented
            ];

            foreach ($columnMap as $filterKey => $dbColumn) {
                if (!empty($filters[$filterKey])) {
                    $filterValues = $filters[$filterKey];

                    $hasEmpty = in_array('__EMPTY__', $filterValues);
                    $nonEmptyValues = array_filter($filterValues, fn($v) => $v !== '__EMPTY__');

                    $q->where(function ($subQuery) use ($dbColumn, $hasEmpty, $nonEmptyValues) {
                        if (!empty($nonEmptyValues)) {
                            $subQuery->whereIn($dbColumn, $nonEmptyValues);
                        }

                        if ($hasEmpty) {
                            $subQuery->orWhereNull($dbColumn)->orWhere($dbColumn, '');
                        }
                    });
                }
            }

        }])
        ->orderBy('id', 'desc');

        // Apply parent-level filters
        foreach ($filters as $key => $values) {
            if (empty($values)) continue;

            switch ($key) {               

                // Direct parent-table filters
                case 'filter_col_7': $query->whereIn('full_article_number', $values); break;
                case 'filter_col_8': $query->whereIn('description', $values); break;
                case 'filter_col_9': $query->whereIn('product_type', $values); break;
                case 'filter_col_11': $query->whereIn('qty', $values); break;
                case 'filter_col_13': $query->whereIn('unit_price', $values); break;
                case 'filter_col_14': $query->whereIn('total_price', $values); break;
                //case 'filter_col_15': $query->whereIn('currency_wise_sales_unit_value', $values); break; // A Code: 19-01-2026 Commented
                //case 'filter_col_16': $query->whereIn('currency_wise_sales_total_value', $values); break; // A Code: 19-01-2026 Commented

                // All child-table filters handled below
                default:
                    $childColumnMap = [
                        'filter_col_1'  => 'status',
                        'filter_col_2'  => 'pl_uploaded_date',
                        'filter_col_3'  => 'sales_order_number',
                        'filter_col_4'  => 'project_no',
                        'filter_col_5'  => 'project_name',
                        'filter_col_6'  => 'country'
                        //'filter_col_12'  => 'currency' // A Code: 19-01-2026 Commented
                    ];
                    
                    if (isset($childColumnMap[$key])) {
                        $column = $childColumnMap[$key];

                        $hasEmpty = in_array('__EMPTY__', $values);
                        $nonEmptyValues = array_filter($values, fn($v) => $v !== '__EMPTY__');

                        $query->whereHas('projects', function ($q) use ($column, $nonEmptyValues, $hasEmpty) {
                            $q->where(function ($sub) use ($column, $nonEmptyValues, $hasEmpty) {
                                if (!empty($nonEmptyValues)) {
                                    $sub->whereIn($column, $nonEmptyValues);
                                }

                                if ($hasEmpty) {
                                    $sub->orWhereNull($column)->orWhere($column, '');
                                }
                            });
                        });
                    }
                    break;
            }
        }

        // A Code: 26-12-2025 Start
        $query->whereHas('projects', function ($q) {
            $q->where('is_deleted', 0);
        });
        // A Code: 26-12-2025 End

        // Final query
        $trackingProducts = $query->get();

        // Export CSV Code
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d_H-i-s');

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="products_filter_export_' . $timestamp . '.csv"',
        ];      

        // A Code: 19-01-2026 Start 
        $columns = ['SR No', 'Status', 'Completation Date', 'SO Number', 'Project Number', 'Project Name', 
        'Country', 'Product Article Number', 'Product Description', 'Product Type', 'Product Family Number', 
        'Product Quantity', 'Unit Sales Value [EUR]', 'Total Sales Value [EUR]'];
        // A Code: 19-01-2026 End

        $callback = function () use ($trackingProducts, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            $sr_no = 1;
            foreach ($trackingProducts as $val) {

                $colSrNo = $sr_no++; 

                    switch($val->projects['status']) {
                        case "0": $statusLabel = "Open"; break;
                        case "1": $statusLabel = "InProgress"; break;
                        case "2": $statusLabel = "Completed"; break;
                        default: $statusLabel = "Unknown"; break;
                    }

                $colStatus = $statusLabel;
                    $pl_uploaded_date = $val->projects['pl_uploaded_date'];
                $colCompletationDate = $pl_uploaded_date 
                    ? '="' . \Carbon\Carbon::parse($pl_uploaded_date)->format('d-m-Y') . '"' 
                    : 'N/A';
                $colSalesNumber = $val->projects['sales_order_number'] ?? 'N/A'; 
                $colProjectNo = '="' . ($val->projects['project_no'] ?? 'N/A') . '"'; // Excel-safe                
                $colProjectName = $val->projects['project_name'] ?? 'N/A';
                $colCountry = $val->projects['country'] ?? 'N/A';
                $colProductArticleNo = $val->full_article_number ?? 'N/A';
                $colProductDescription = $val->description ?? 'N/A';
                $colProductType = $val->product_type ?? 'N/A';
                $colProductFamilyNumber = ProductType::where('project_type_name', $val->product_type)->value('product_family_number');
                $colProductQuantity = $val->qty;   
                //$colCurrency = $val->projects['currency'] ?? 'N/A'; // A Code: 19-01-2026 Commented
                $colUnitWiseSalesValue = $val->unit_price;
                $colTotalSalesValue = $val->total_price;
                //$colCurrencyWiseSalesUnitValue = $val->currency_wise_sales_unit_value; // A Code: 19-01-2026 Commented
                //$colCurrencyWiseSalesTotalValue = $val->currency_wise_sales_total_value; // A Code: 19-01-2026 Commented

              
                fputcsv($file, [
                    $colSrNo,
                    $colStatus,
                    $colCompletationDate,
                    $colSalesNumber,
                    $colProjectNo,
                    $colProjectName,
                    $colCountry,
                    $colProductArticleNo,
                    $colProductDescription,
                    $colProductType,
                    $colProductFamilyNumber,
                    $colProductQuantity,
                    //$colCurrency, // A Code: 19-01-2026 Commented
                    $colUnitWiseSalesValue,
                    $colTotalSalesValue
                    //$colCurrencyWiseSalesUnitValue, // A Code: 19-01-2026 Commented
                    //$colCurrencyWiseSalesTotalValue // A Code: 19-01-2026 Commented
                ]);


            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
