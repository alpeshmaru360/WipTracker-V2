<?php

namespace App\Http\Controllers\StockMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockMasterModule;
use App\Models\StockBOMPo;
use App\Models\ProductsOfProjects;
use App\Models\Project;
use App\Models\InitialInspectionTable;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use App\Imports\StockImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderTable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class StockMasterController extends Controller
{
    public function index(Request $request){
        $page_title = "Stock Master";        
        $query = StockMasterModule::query()->orderBy('id', 'desc');
        $stocksfilter = (object)[];
        $last_filter_column =  $request->input('last_filter_column');

        if(isset($last_filter_column)){
            $stocksfilter = $query->get();
        }

        // Apply filters to StockMasterModule fields
        $filters = $request->input('filters', []);
        
        $totalPoQtyFilter = [];

        // Map filter keys to actual DB columns
        $filterableColumns = [
            'filter_col_0' => 'article_number',
            'filter_col_1' => 'item_desc',
            'filter_col_2' => 'qty',
            'filter_col_3' => 'hold_qty',
            'filter_col_4' => 'available_qty',
            'filter_col_5' => 'minimum_required_qty',
            'filter_col_6' => 'std_time',
        ];

        // Apply filters to query
        foreach ($filters as $key => $values) {
            $values = (array) $values; // normalize to array

            if (array_key_exists($key, $filterableColumns)) {
                $query->whereIn($filterableColumns[$key], $values);
            }

            // Special case: total_po_qty
            if ($key === 'filter_col_7') {
                $totalPoQtyFilter = $values;
            }
        }

        // Get filtered stocks
        $stocks = $query->get();

        // Step 2: For each stock item, calculate total PO quantity using all required joins
        foreach ($stocks as $stock) {
            $total_po_qty = DB::table('purchase_order_table')
                            ->join('purchase_order', 'purchase_order.id', '=', 'purchase_order_table.po_id')
                            ->leftJoin('stock_bom_po', function ($join) {
                                $join
                                    ->on(DB::raw("CONVERT(purchase_order_table.artical_no USING utf8mb4)"), '=', DB::raw("CONVERT(stock_bom_po.article_no USING utf8mb4)"))
                                    ->on(DB::raw("CONVERT(purchase_order_table.description USING utf8mb4)"), '=', DB::raw("CONVERT(stock_bom_po.description USING utf8mb4)"))
                                    ->on('purchase_order.po_number', '=', 'stock_bom_po.po_no');
                            })
                            ->whereRaw("CONVERT(purchase_order_table.artical_no USING utf8mb4) = CONVERT(? USING utf8mb4)", [$stock->article_number])
                            ->whereRaw("CONVERT(purchase_order_table.description USING utf8mb4) = CONVERT(? USING utf8mb4)", [$stock->item_desc])
                            ->where(function ($query) {
                                $query->whereNull('stock_bom_po.mrf_ready_date')
                                    ->orWhereNull('stock_bom_po.id'); // include rows not in stock_bom_po
                            })
                            ->where('purchase_order_table.is_initial_inspection_started','!=','1')
                            ->sum('purchase_order_table.quantity');

            $stock->total_po_qty = $total_po_qty;         
        } 

        // Qty In Order Filter  
        if (isset($filters['filter_col_7']) && is_array($filters['filter_col_7'])) {
            $stocksArray = $stocks->toArray();

            // Filter by array of allowed total_po_qty values
            $filteredStocks1 = array_filter($stocksArray, function ($stock) use ($filters) {
                return isset($stock['total_po_qty']) &&
                       in_array((int) $stock['total_po_qty'], $filters['filter_col_7']);
            });

            $filteredStocks1 = array_values($filteredStocks1); // Reindex

            // Convert to collection of objects
            $stocks = collect(array_map(function ($stock) {
                return (object) $stock;
            }, $filteredStocks1));
        }

        
        return view('stock.stock_master', compact('page_title', 'stocks', 'filters', 'stocksfilter', 'last_filter_column'));
    }

    public function store(Request $request){
        $request->validate([
            'article_number' => 'required|string|max:255',
            'item_desc' => [
                'required',
                'string',
                'max:255',
                Rule::unique('stock_master_module')->where(function ($query) use ($request) {
                    return $query->where('article_number', $request->article_number);
                }),
            ],
            'qty' => 'required|integer|min:0',
        ]);

        StockMasterModule::create([
            'article_number' => $request->article_number,
            'item_desc' => $request->item_desc,
            'qty' => $request->qty,
            'hold_qty' => 0,
            'available_qty' => $request->qty,
            'minimum_required_qty' => $request->minimum_required_qty,
            'std_time' => $request->std_time,
        ]);

        return redirect()->route('Stock')->with('success', 'Stock added successfully.');
    }

    public function update(Request $request, $id){
        $request->validate([
            'article_number' => 'required|string|max:255',
            'item_desc' => [
                'required',
                'string',
                'max:255',
                Rule::unique('stock_master_module')
                    ->ignore($id) // Excludes current record by ID
                    ->where(function ($query) use ($request) {
                        return $query->where('article_number', $request->article_number);
                    }),
            ],
        ]);

        $stock = StockMasterModule::findOrFail($id);
        $addedQty = $request->added_qty ?? 0;
        $updatedValues = [
            'article_number' => $request->article_number,
            'item_desc' => $request->item_desc,
            'qty' => $stock->qty + $addedQty,
            'hold_qty' => $request->hold_qty,
            'available_qty' => $stock->available_qty + $addedQty,
            'minimum_required_qty' => $request->minimum_required_qty,
            'std_time' => $request->std_time,
        ];

        $stock->update($updatedValues);

        return redirect()->route('Stock')->with('success', 'Stock updated successfully.');
    }

    public function destroy($id){
        $stock = StockMasterModule::findOrFail($id);
        $stock->delete();

        return redirect()->route('Stock')->with('success', 'Stock deleted successfully.');
    }

    public function viewHideQty(Request $request){
        $articleNumber = $request->input('article_number');
        $itemDesc = $request->input('item_desc');

        $stock_details = StockBOMPo::where('description', $itemDesc)
            ->where('article_no', $articleNumber)
            ->where('hold_qty', '!=', 0)
            //->where('select_option', '!=', 'new_order')
            ->orderBy('id', 'asc')
            ->with('projects')
            ->get();

        return response()->json($stock_details);
    }

    public function viewQtyInOrder(Request $request){
        $articleNumber = $request->input('article_number');
        $itemDesc = $request->input('item_desc');
        $stock_details = DB::table('purchase_order_table')
            ->select(
                'purchase_order_table.id as PurchaseOrderTableId',
                'purchase_order_table.po_id',
                'purchase_order_table.artical_no as PurchaseArticleNo',
                'purchase_order_table.description as PurchaseItemDesc',
                'purchase_order_table.quantity as PurchaseItemQty',
                'purchase_order.po_number as PurchaseOrderNumber',
                'purchase_order.project_no as PurchaseOrderProjectNo',
                'stock_bom_po.po_no as StockBomPONO',
                'stock_bom_po.mrf_ready_date as StockBomMRFReadyDate',
                'stock_bom_po.description as StockBomItemDesc',
                'stock_bom_po.article_no as StockBomItemArticleNo',
                'stock_bom_po.project_id as StockBomProjectId',
                'stock_bom_po.product_id as StockBomProductId',
                DB::raw('SUM(purchase_order_table.quantity) OVER () as TotalPOQty')
            )
            ->join('purchase_order', 'purchase_order.id', '=', 'purchase_order_table.po_id')
            ->leftJoin('stock_bom_po', function ($join) {
                $join->on(DB::raw("CONVERT(purchase_order_table.artical_no USING utf8mb4)"), '=', DB::raw("CONVERT(stock_bom_po.article_no USING utf8mb4)"))
                    ->on(DB::raw("CONVERT(purchase_order_table.description USING utf8mb4)"), '=', DB::raw("CONVERT(stock_bom_po.description USING utf8mb4)"))
                    ->on('purchase_order.po_number', '=', 'stock_bom_po.po_no');
            })
            ->whereRaw("CONVERT(purchase_order_table.artical_no USING utf8mb4) = CONVERT(? USING utf8mb4)", [$articleNumber])
            ->whereRaw("CONVERT(purchase_order_table.description USING utf8mb4) = CONVERT(? USING utf8mb4)", [$itemDesc])
            ->where(function ($query) {
                $query->whereNull('stock_bom_po.mrf_ready_date')
                    ->orWhereNull('stock_bom_po.id'); // include unmatched rows
            })
            ->where('purchase_order_table.is_initial_inspection_started','!=','1')
            ->orderBy('purchase_order_table.id', 'asc')
            ->get();
            
        $response = [];

        foreach ($stock_details as $stockBomPo) {
            $projectNo = null;
            $projectName = null;
            if ($stockBomPo->PurchaseOrderProjectNo) {
                $project = Project::where('project_no',$stockBomPo->PurchaseOrderProjectNo)->first();
                $projectNo = $project->project_no ?? null;
                $projectName = $project->project_name ?? null;
            }
            $productDescription = null;
            $productArticleNo = null;
            if ($stockBomPo->PurchaseOrderProjectNo) {
                $product = ProductsOfProjects::find($stockBomPo->PurchaseOrderProjectNo);
                $productDescription = $product->description ?? null;
                $productArticleNo = $product->full_article_number ?? null;
            }
            $response[] = [
                'project_no' => $projectNo ?? 'N/A',
                'project_name' => $projectName ?? 'N/A',
                'product_description' => $productDescription ?? 'N/A',
                'article_number' => $productArticleNo ?? 'N/A',
                'article_no' => $stockBomPo->PurchaseArticleNo ?? 'N/A',
                'description' => $stockBomPo->PurchaseItemDesc ?? 'N/A',
                'po_no' => $stockBomPo->PurchaseOrderNumber ?? 'N/A',
                'po_qty' => $stockBomPo->PurchaseItemQty ?? 'N/A',
            ];
        }
        return response()->json($response);
    }

    public function import(Request $request){
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        try {
            $file = $request->file('excel_file');

            // Check for required headers
            $headings = (new \Maatwebsite\Excel\HeadingRowImport)->toArray($file)[0][0];
            $requiredHeaders = ['art_no', 'product_name', 'total_qty', 'minimum_required_stock_alert', 'eta_weeks'];
            $missingHeaders = array_diff(array_map('strtolower', $requiredHeaders), array_map('strtolower', $headings));

            if (!empty($missingHeaders)) {
                // Map technical column names to friendly names
                $friendlyNames = [
                    'art_no' => 'article number',
                    'product_name' => 'product name',
                    'total_qty' => 'total qty',
                    'minimum_required_stock_alert' => 'Minimum Required Stock Alert',
                    'eta_weeks' => 'ETA Weeks',
                ];
                $missingFriendlyHeaders = array_map(function ($header) use ($friendlyNames) {
                    return $friendlyNames[$header] ?? $header;
                }, $missingHeaders);

                return redirect()->route('Stock')->with('error', 'The Excel file seems to be missing some important columns: ' . implode(', ', $missingFriendlyHeaders) . '. Please check the file and try again.');
            }

            // Truncate the table before importing new data
            StockMasterModule::query()->truncate();

            // Proceed with import
            Excel::import(new StockImport, $file);

            return redirect()->route('Stock')->with('success', 'Stock data imported successfully.');
        } catch (\Maatwebsite\Excel\Exceptions\SheetNotFoundException $e) {
            return redirect()->route('Stock')->with('error', 'No valid sheet found in the Excel file.');
        } catch (\Exception $e) {
            return redirect()->route('Stock')->with('error', 'Error importing stock data: ' . $e->getMessage());
        }
    }

    public function exportCSV(Request $request){
        $query = StockMasterModule::query()->orderBy('id', 'desc');
        // Apply filters to StockMasterModule fields
        $filters = $request->input('filters', []);        
        $totalPoQtyFilter = [];
        // Map filter keys to actual DB columns
        $filterableColumns = [
            'filter_col_0' => 'article_number',
            'filter_col_1' => 'item_desc',
            'filter_col_2' => 'qty',
            'filter_col_3' => 'hold_qty',
            'filter_col_4' => 'available_qty',
            'filter_col_5' => 'minimum_required_qty',
            'filter_col_6' => 'std_time',
        ];
        // Apply filters to query
        foreach ($filters as $key => $values) {
            $values = (array) $values; // normalize to array
            if (array_key_exists($key, $filterableColumns)) {
                $query->whereIn($filterableColumns[$key], $values);
            }
            // Special case: total_po_qty
            if ($key === 'filter_col_7') {
                $totalPoQtyFilter = $values;
            }
        }
        // Get filtered stocks
        $stocks = $query->get();
        // Step 2: For each stock item, calculate total PO quantity using all required joins
        foreach ($stocks as $stock) {
            $total_po_qty = DB::table('purchase_order_table')
                            ->join('purchase_order', 'purchase_order.id', '=', 'purchase_order_table.po_id')
                            ->leftJoin('stock_bom_po', function ($join) {
                                $join
                                    ->on(DB::raw("CONVERT(purchase_order_table.artical_no USING utf8mb4)"), '=', DB::raw("CONVERT(stock_bom_po.article_no USING utf8mb4)"))
                                    ->on(DB::raw("CONVERT(purchase_order_table.description USING utf8mb4)"), '=', DB::raw("CONVERT(stock_bom_po.description USING utf8mb4)"))
                                    ->on('purchase_order.po_number', '=', 'stock_bom_po.po_no');
                            })
                            ->whereRaw("CONVERT(purchase_order_table.artical_no USING utf8mb4) = CONVERT(? USING utf8mb4)", [$stock->article_number])
                            ->whereRaw("CONVERT(purchase_order_table.description USING utf8mb4) = CONVERT(? USING utf8mb4)", [$stock->item_desc])
                            ->where(function ($query) {
                                $query->whereNull('stock_bom_po.mrf_ready_date')
                                    ->orWhereNull('stock_bom_po.id'); // include rows not in stock_bom_po
                            })
                            ->sum('purchase_order_table.quantity');

            $stock->total_po_qty = $total_po_qty;         
        } 

        // Qty In Order Filter  
        if (isset($filters['filter_col_7']) && is_array($filters['filter_col_7'])) {
            $stocksArray = $stocks->toArray();
            // Filter by array of allowed total_po_qty values
            $filteredStocks1 = array_filter($stocksArray, function ($stock) use ($filters) {
                return isset($stock['total_po_qty']) &&
                       in_array((int) $stock['total_po_qty'], $filters['filter_col_7']);
            });
            $filteredStocks1 = array_values($filteredStocks1); // Reindex
            // Convert to collection of objects
            $stocks = collect(array_map(function ($stock) {
                return (object) $stock;
            }, $filteredStocks1));
        }
        // Export CSV Code
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="stock_details_filter_export_' . $timestamp . '.csv"',
        ];
        $columns = ['SR No', 'Article Number', 'Item Description', 'Qty', 'Reserved [WIP] Qty', 'Warehouse Qty', 'Minimum Required Qty', '[In Weeks] ETA STD Weeks', 'Qty In Order'];

        $callback = function () use ($stocks, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            $sr_no = 1;
            foreach ($stocks as $stock) {
                fputcsv($file, [
                    $sr_no++,
                    $stock->article_number,
                    $stock->item_desc,
                    $stock->qty,
                    $stock->hold_qty,
                    $stock->available_qty,
                    $stock->minimum_required_qty,
                    $stock->std_time,
                    $stock->total_po_qty
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
