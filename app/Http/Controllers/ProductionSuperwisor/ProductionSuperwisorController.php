<?php

namespace App\Http\Controllers\ProductionSuperwisor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProjectStatus;
use App\Models\Project;
use App\Models\AdminHoursManagement;
use App\Models\ProductionTeamDetail;
use App\Models\User;
use Illuminate\Support\Facades\Response;
use App\Models\ProjectProcessStdTime;
use App\Models\ProductsOfProjects;
use App\Models\AssignedProductToOperator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use App\Models\QtyOfProduct;
use App\Mail\WItrackProjectCompleteNotifyProductionTeam;
use App\Mail\WItrackProjectPartialCompleteNotifyProductionTeam;
use App\Mail\WItrackProjectFullCompleteNotifyProductionTeam;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MRFToWarehouseExport;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\StockBOMPo;
use App\Models\StockMasterModule;
use App\Mail\SendMRFToWarehouse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Log; // Alpesh Maru Date: 12-12-2025 Code

class ProductionSuperwisorController extends Controller
{
    public function dashboard(DashboardService $dashboardService){
        // Common Dashboard Service
        $role = auth()->user()->role;
        $dashboardData = $dashboardService->getDashboardData($role);

        $page_title = "";
        $project_working_on = $dashboardData['working'];
        $project_completed = $dashboardData['done'];

        return view('production_manager.dashboard', compact(
            'dashboardData',
            'page_title',
            'project_working_on',
            'project_completed'
        ));
    }

    private function calculateDeadline($startDate, $hours){
        $start = \Carbon\Carbon::parse($startDate);
        $currentDate = $start->copy();
        $hoursRemaining = $hours;

        while ($hoursRemaining > 0) {
            $currentDate->addHour();
            // Skip Saturday (6) and Sunday (0)
            if ($currentDate->dayOfWeek != 0 && $currentDate->dayOfWeek != 6) {
                $hoursRemaining--;
            }
        }

        // Ensure the final date is not a weekend
        while ($currentDate->dayOfWeek == 0 || $currentDate->dayOfWeek == 6) {
            $currentDate->addHour();
        }

        return $currentDate;
    }

    public function inbox(){
        $page_title = "PROJECT STATUS";

        // Fetch the hours from admin_hours_management
        $hoursEntry = AdminHoursManagement::where('lable', 'StandardProcessTimes')
            ->where('key', 'request_mrf_to_warehouse')
            ->where('is_deleted', 0)
            ->first();
        $hours = $hoursEntry ? (int)$hoursEntry->value : 24;

        // Get all products Pending MRF (Completed Initial Inspection)
        $all_pending_projects = ProductsOfProjects::with('projects')
            ->select('products_of_projects.*', 'initial_inspection_data.ini_inspection_date')
            ->join('projects', 'products_of_projects.project_id', '=', 'projects.id')
            ->leftJoin('initial_inspection_data', function ($join) {
                $join->on('initial_inspection_data.project_no', '=', 'projects.project_no')
                    ->on('initial_inspection_data.artical_no', '=', 'products_of_projects.full_article_number')
                    ->on('initial_inspection_data.description', '=', 'products_of_projects.description');
            })
            ->orderBy('products_of_projects.id', 'desc')
            ->whereExists(function ($query) {
                $query->select(\DB::raw(1))
                    ->from('stock_bom_po')
                    ->whereColumn('stock_bom_po.product_id', 'products_of_projects.id')
                    ->whereColumn('stock_bom_po.project_id', 'products_of_projects.project_id')
                    ->where('stock_bom_po.select_option', '!=', 'stock')
                    ->where('stock_bom_po.is_email_sent', 0);
            })

            // ->whereNotExists(function ($query) {
            //     $query->select(\DB::raw(1))
            //         ->from('stock_bom_po')
            //         ->whereColumn('stock_bom_po.product_id', 'products_of_projects.id')
            //         ->whereColumn('stock_bom_po.project_id', 'products_of_projects.project_id')
            //         ->whereNull('stock_bom_po.po_no');
            // })

            ->get()
            ->map(function ($item) use ($hours) {
                $orderDate = $item->ini_inspection_date ? \Carbon\Carbon::parse($item->ini_inspection_date) : $item->created_at;
                $item->deadline = $this->calculateDeadline($orderDate, $hours);

                // Get all BOM items for this product
                $bom_items = DB::table('stock_bom_po')
                    ->where('product_id', $item->id)
                    ->where('project_id', $item->project_id)
                    ->where('is_email_sent', 0)
                    ->whereNotNull('po_no')
                    ->where('select_option', '!=', 'stock')
                    ->get();
                $project = DB::table('projects')
                    ->where('id', $item->project_id)
                    ->first();
                $project_no = $project ? $project->project_no : null;

                // Count inspected vs not inspected items
                $inspected_count = 0;
                $not_inspected_count = 0;

                if ($project_no && $bom_items->isNotEmpty()) {
                    foreach ($bom_items as $bom_item) {
                        $inspection_exists = DB::table('initial_inspection_data')
                            ->where('project_no', $project_no)
                            ->where('po_number', $bom_item->po_no)
                            ->whereRaw("REPLACE(artical_no, ' ', '') = REPLACE(?, ' ', '')", [$bom_item->article_no])
                            ->where('description', 'like', '%' . $bom_item->description . '%')
                            ->exists();
                        if ($inspection_exists) {
                            $inspected_count++;
                        } else {
                            $not_inspected_count++;
                        }
                    }
                }
                $item->inspected_items_count = $inspected_count;
                $item->not_inspected_items_count = $not_inspected_count;
                $item->total_items_count = $inspected_count + $not_inspected_count;
                return $item;
            });

        // Filter for products that have at least one inspected item (MRF)
        $pending_project_with_inspection = $all_pending_projects->filter(function ($item) {
            return $item->inspected_items_count > 0;
        });

        //
        $all_items_are_from_stock_pending_mrf = ProductsOfProjects::with('projects')
            ->orderBy('products_of_projects.id', 'desc')
            ->get()
            ->filter(function ($item) use ($hours) {

                // Get ALL BOM items for this product
                $bom_items_all = DB::table('stock_bom_po')
                    ->where('product_id', $item->id)
                    ->where('project_id', $item->project_id)
                    ->where('is_email_sent', 0)
                    ->whereNotNull('po_no')
                    ->get();

                // If no BOM items exist → skip
                if ($bom_items_all->isEmpty()) {
                    return false;
                }

                // TRUE only when every item is stock
                $all_stock = $bom_items_all->every(function ($bom) {
                    return $bom->select_option === 'stock';
                });

                if ($all_stock) {

                    // Assign processed_at from FIRST BOM item
                    $item->processed_at = $bom_items_all->first()->processed_at;

                    // Order date = processed_at OR created_at fallback
                    $orderDate = $item->processed_at
                        ? \Carbon\Carbon::parse($item->processed_at)
                        : $item->created_at;

                    // Calculate deadline using your existing method
                    $item->deadline = $this->calculateDeadline($orderDate, $hours);

                    return true;
                }

                return false;
            });
        //

        // Filter for products that have at least one non-inspected item (MRF)
        $pending_project_without_inspection = $all_pending_projects->filter(function ($item) {
            return $item->not_inspected_items_count > 0;
        });

        // Separate into two collections
        $pending_project_ready = $all_pending_projects->filter(function ($item) {
            return $item->all_items_ready === true;
        });

        $pending_project_not_ready = $all_pending_projects->filter(function ($item) {
            return $item->all_items_ready === false;
        });

        $processing_mrf = ProductsOfProjects::with('projects')
            ->select('products_of_projects.*', 'initial_inspection_data.ini_inspection_date')
            ->join('projects', 'products_of_projects.project_id', '=', 'projects.id')
            ->leftJoin('initial_inspection_data', function ($join) {
                $join->on('initial_inspection_data.project_no', '=', 'projects.project_no')
                    ->on('initial_inspection_data.artical_no', '=', 'products_of_projects.full_article_number')
                    ->on('initial_inspection_data.description', '=', 'products_of_projects.description');
            })
            ->orderBy('products_of_projects.id', 'desc')
            ->whereExists(function ($query) {
                $query->select(\DB::raw(1))
                    ->from('stock_bom_po')
                    ->whereColumn('stock_bom_po.product_id', 'products_of_projects.id')
                    ->whereColumn('stock_bom_po.project_id', 'products_of_projects.project_id')
                    ->where('stock_bom_po.is_email_sent', 1)
                    ->whereNotNull('stock_bom_po.po_no');
            })
            ->get()
            ->map(function ($item) use ($hours) {
                $orderDate = $item->ini_inspection_date ? \Carbon\Carbon::parse($item->ini_inspection_date) : $item->created_at;
                $item->deadline = $this->calculateDeadline($orderDate, $hours);
                return $item;
            });

        $ready_mrf = ProductsOfProjects::with('projects')
            ->select('products_of_projects.*', 'initial_inspection_data.ini_inspection_date')
            ->join('projects', 'products_of_projects.project_id', '=', 'projects.id')
            ->leftJoin('initial_inspection_data', function ($join) {
                $join->on('initial_inspection_data.project_no', '=', 'projects.project_no')
                    ->on('initial_inspection_data.artical_no', '=', 'products_of_projects.full_article_number')
                    ->on('initial_inspection_data.description', '=', 'products_of_projects.description');
            })
            ->orderBy('products_of_projects.id', 'desc')
            ->whereExists(function ($query) {
                $query->select(\DB::raw(1))
                    ->from('stock_bom_po')
                    ->whereColumn('stock_bom_po.product_id', 'products_of_projects.id')
                    ->whereColumn('stock_bom_po.project_id', 'products_of_projects.project_id')
                    ->where('stock_bom_po.is_email_sent', 2);
            })
            ->whereNotExists(function ($query) {
                $query->select(\DB::raw(1))
                    ->from('stock_bom_po')
                    ->whereColumn('stock_bom_po.product_id', 'products_of_projects.id')
                    ->whereColumn('stock_bom_po.project_id', 'products_of_projects.project_id')
                    ->whereNull('stock_bom_po.po_no');
            })
            ->get()
            ->map(function ($item) use ($hours) {
                $orderDate = $item->ini_inspection_date ? \Carbon\Carbon::parse($item->ini_inspection_date) : $item->created_at;
                $item->deadline = $this->calculateDeadline($orderDate, $hours);
                return $item;
            });

        $all_projects_full = ProductsOfProjects::with('projects')->with('operator')->orderBy('id', 'desc')->where('delivery', '1')->get();
        $all_projects_partials = ProductsOfProjects::with('projects')->with('operator')->orderBy('id', 'desc')->where('delivery', '2')->get();
        $operators = User::whereIn('role', ['Wilo Operator', '3rd Party Operator'])->get();

        $completed_process_of_assembly_products_qty_wise = QtyOfProduct::where('is_qty_product_assembled', '1')
            ->where('is_final_inspection_started', '0')
            ->with('projects')
            ->with('products')
            ->get();

        $pdf_req = ProductsOfProjects::with('projects')
            ->whereNotNull('editable_drawing_path')
            ->where('is_asbuilt_drawing_pdf_approve_by_production_superwisor', '3')
            ->orderBy('id', 'desc')
            ->get();

        $pendingNameplateProductCreation = ProductsOfProjects::where('all_process_assembled', '1')
            ->where('is_final_inspection_started', '0')
            ->with('projects')
            ->get();

        $pendingNameplateProductCreationAsPerQtyWise = QtyOfProduct::orderBy('id', 'desc')->where('is_qty_product_assembled', '1')
            ->whereIn('nameplate_create_inbox_to_pro_eng', ['1', '2'])
            ->where('is_final_inspection_started', '0')->with('projects')->with('products')
            ->get();

        $upload_pl_req = ProductsOfProjects::with('projects')
            ->where('inbox_to_pro_superwisor_to_create_pl', '1')
            ->where('delivery', 1)
            ->whereHas('projects', function ($query) {
                $query->where('QE_req_to_PS_create_PL_inbox', '0')
                    ->whereNull('PL_PDF_path');
            })
            ->orderBy('id', 'desc')
            ->groupBy('project_id')
            ->get();        

        $upload_pl_partial_dilivery_req = DB::table('qty_of_products')
            ->select(
                'qty_of_products.id',
                'qty_of_products.project_id',
                'qty_of_products.product_id',
                'qty_of_products.qty_number',
                'qty_of_products.is_qty_product_assembled',
                'qty_of_products.is_final_inspection_started',
                'qty_of_products.PL_PDF_path',
                'projects.project_no',
                'projects.project_name',
                'products_of_projects.description',
                'products_of_projects.full_article_number',
                'products_of_projects.qty'
            )
            ->join('projects', 'projects.id', '=', 'qty_of_products.project_id')
            ->join('products_of_projects', function ($join) {
                $join->on('products_of_projects.project_id', '=', 'qty_of_products.project_id')
                    ->on('products_of_projects.id', '=', 'qty_of_products.product_id');
            })
            ->where('qty_of_products.is_qty_product_assembled', 1)
            ->where('qty_of_products.is_final_inspection_started', 2)
            ->where('products_of_projects.delivery', 2)
            ->whereNull('qty_of_products.PL_PDF_path')
            ->orderBy('qty_of_products.id', 'desc')
            ->get();

        $completed_projects = Project::whereNull('deleted_at')->where('status', '2')->get();

        return view('production_superwisor.inbox', compact(
            'pending_project_with_inspection',
            'pending_project_without_inspection',
            'page_title',
            'operators',
            'all_projects_full',
            'all_projects_partials',
            'pdf_req',
            'upload_pl_req',
            'completed_projects',
            'completed_process_of_assembly_products_qty_wise',
            'ready_mrf',
            'upload_pl_partial_dilivery_req',
            'processing_mrf',
            'pendingNameplateProductCreationAsPerQtyWise',
            'all_items_are_from_stock_pending_mrf'
        ));
    }

    // Download Excel for COMPLETED initial inspection items
    public function mrf_excel_download_inspected(Request $request){
        $articleNumber = $request->input('article_number');
        $description = $request->input('description');
        $qty = $request->input('qty');
        $projectNo = $request->input('project_no'); // Add project number parameter

        // Fetch project_id using project number
        $project = Project::where('project_no', $projectNo)->first();

        if (!$project) {
            return response()->json(['error' => 'Project not found'], 404);
        }

        $projectId = $project->id;

        // Now use the projectId in the product query
        $product = ProductsOfProjects::where('full_article_number', $articleNumber)
            ->where('description', $description)
            ->where('qty', $qty)
            ->where('project_id', $projectId) // Add this condition
            ->first();

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $productId = $product->id;

        $sanitizedArticleNumber = preg_replace('/[^A-Za-z0-9_-]/', '_', $articleNumber);
        if($request->all_item_from_stock == false){
            $fileName = "MRF_Inspected_{$project->project_no}_{$sanitizedArticleNumber}.xlsx";
        }
        else{
            $fileName = "MRF_From_Stock_{$project->project_no}_{$sanitizedArticleNumber}.xlsx";
        }

        $templatePath = public_path('storage/templates/MRF_to_warehouse.xlsx');

        return Excel::download(
            new MRFToWarehouseExport($productId, $projectId, 'inspected'),
            $fileName,
            \Maatwebsite\Excel\Excel::XLSX,
            ['template' => $templatePath]
        );
    }

    public function uploadMRFFile(Request $request){
        $validator = Validator::make($request->all(), [
            'mrf_file' => 'required|mimes:xlsx|max:2048',
            'product_id' => 'required|integer|exists:products_of_projects,id',
            'project_id' => 'required|integer|exists:projects,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $file = $request->file('mrf_file');
        $productId = $request->input('product_id');
        $projectId = $request->input('project_id');

        // Get product and project details
        $product = ProductsOfProjects::findOrFail($productId);
        $project = Project::findOrFail($projectId);
        $articleNumber = $product->full_article_number;
        $projectNo = $project->project_no;

        // Sanitize article number for filename matching
        $sanitizedArticleNumber = preg_replace('/[^A-Za-z0-9_-]/', '_', $articleNumber);

        // Validate filename - accept multiple formats
        $fileName = $file->getClientOriginalName();

        // Expected filename patterns
        $expectedPatterns = [
            "MRF_{$projectNo}_{$sanitizedArticleNumber}.xlsx",              // Original format
            "MRF_Inspected_{$projectNo}_{$sanitizedArticleNumber}.xlsx",    // Inspected format
            "MRF_Pending_{$projectNo}_{$sanitizedArticleNumber}.xlsx",      // Pending format
        ];

        $isValidFileName = false;
        foreach ($expectedPatterns as $pattern) {
            if ($fileName === $pattern) {
                $isValidFileName = true;
                break;
            }
        }

        if (!$isValidFileName) {
            return response()->json([
                'success' => false,
                'message' => "Uploaded file name does not match expected format. Expected one of: " .
                    "MRF_{$projectNo}_{$sanitizedArticleNumber}.xlsx OR " .
                    "MRF_Inspected_{$projectNo}_{$sanitizedArticleNumber}.xlsx OR " .
                    "MRF_Pending_{$projectNo}_{$sanitizedArticleNumber}.xlsx"
            ], 400);
        }

        try {
            // Load spreadsheet
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();

            $startRow = 12;
            $slColumn = 'B';
            $articleNoColumn = 'C';
            $descriptionColumn = 'E';
            $quantityColumn = 'H';
            $poNoColumn = 'J';
            $boeColumn = 'K'; // BOE column mapping
            $maxRows = 10000;

            // Fetch all old BOM items before update
            $oldItems = StockBOMPo::where('product_id', $productId)
                ->where('project_id', $projectId)
                ->get()
                ->keyBy(function ($item) {
                    return ($item->article_no ?? '') . '|' . trim($item->description ?? '');
                });

            $row = $startRow;
            $updatedCount = 0;
            $createdCount = 0;

            while (
                $row < $maxRows &&
                $sheet->getCell($slColumn . $row)->getValue() &&
                $sheet->getCell($articleNoColumn . $row)->getValue()
            ) {
                $sl = $sheet->getCell($slColumn . $row)->getValue();
                $articleNo = $sheet->getCell($articleNoColumn . $row)->getValue();
                $description = trim($sheet->getCell($descriptionColumn . $row)->getValue());
                $quantity = $sheet->getCell($quantityColumn . $row)->getValue();
                $poNo = $sheet->getCell($poNoColumn . $row)->getValue();
                $boe = $sheet->getCell($boeColumn . $row)->getValue();

                $compositeKey = ($articleNo ?? '') . '|' . ($description ?? '');

                if ($sl && $articleNo && $description && $quantity !== null) {
                    if ($oldItems->has($compositeKey)) {
                        // Update existing record
                        $existingItem = $oldItems[$compositeKey];
                        $dataToUpdate = [];

                        if ($articleNo !== null) $dataToUpdate['article_no'] = $articleNo;
                        if ($description !== null) $dataToUpdate['description'] = $description;
                        if ($quantity !== null) $dataToUpdate['item_quantity'] = $quantity;
                        if ($poNo !== null) $dataToUpdate['po_no'] = $poNo;
                        if ($boe !== null) $dataToUpdate['boe'] = $boe;

                        if (!empty($dataToUpdate)) {
                            $existingItem->update($dataToUpdate);
                            $updatedCount++;
                        }
                    } else {
                        // Create new record
                        StockBOMPo::create([
                            'product_id' => $productId,
                            'project_id' => $projectId,
                            'article_no' => $articleNo ?: null,
                            'description' => $description,
                            'item_quantity' => $quantity,
                            'po_no' => $poNo ?: null,
                            'boe' => $boe ?: null,
                            'po_added' => null,
                            'select_option' => null,
                        ]);
                        $createdCount++;
                    }
                }

                $row++;
            }

            return response()->json([
                'success' => true,
                'message' => "MRF file uploaded successfully! Updated: {$updatedCount}, Created: {$createdCount}"
            ]);
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error reading Excel file: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing file: ' . $e->getMessage()
            ], 500);
        }
    }

    // Download Excel for PENDING initial inspection items
    public function mrf_excel_download_not_inspected(Request $request){
        $articleNumber = $request->input('article_number');
        $description = $request->input('description');
        $qty = $request->input('qty');
        $projectNo = $request->input('project_no'); // Add project number parameter

        // Fetch project_id using project number
        $project = Project::where('project_no', $projectNo)->first();

        if (!$project) {
            return response()->json(['error' => 'Project not found'], 404);
        }

        $projectId = $project->id;

        // Now use the projectId in the product query
        $product = ProductsOfProjects::where('full_article_number', $articleNumber)
            ->where('description', $description)
            ->where('qty', $qty)
            ->where('project_id', $projectId) // Add this condition
            ->first();

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $productId = $product->id;

        $sanitizedArticleNumber = preg_replace('/[^A-Za-z0-9_-]/', '_', $articleNumber);
        $fileName = "MRF_Pending_{$project->project_no}_{$sanitizedArticleNumber}.xlsx";

        $templatePath = public_path('storage/templates/MRF_to_warehouse.xlsx');

        return Excel::download(
            new MRFToWarehouseExport($productId, $projectId, 'not_inspected'),
            $fileName,
            \Maatwebsite\Excel\Excel::XLSX,
            ['template' => $templatePath]
        );
    }

    // Send email for COMPLETED inspection items
    public function send_mrf_email_inspected(Request $request){
        $productId = $request->input('product_id');
        $projectId = $request->input('project_id');

        $project = Project::find($projectId);
        if (!$project) {
            return response()->json(['error' => 'Project not found'], 404);
        }

        $product = ProductsOfProjects::find($productId);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $project_no = $project->project_no;

        // Get only inspected items
        $inspected_items = DB::table('stock_bom_po')
            ->where('product_id', $productId)
            ->where('project_id', $projectId)
            ->where('is_email_sent', 0)
            ->whereNotNull('po_no')
            // ->where('select_option', '!=', 'stock')
            ->whereExists(function ($query) use ($project_no) {
                $query->select(DB::raw(1))
                    ->from('initial_inspection_data')
                    ->where('project_no', $project_no)
                    ->whereColumn('po_number', 'stock_bom_po.po_no')
                    ->whereRaw("REPLACE(artical_no, ' ', '') = REPLACE(stock_bom_po.article_no, ' ', '')")
                    ->whereRaw("description LIKE CONCAT('%', stock_bom_po.description, '%')");
            })
            ->get();

        if ($inspected_items->isEmpty()) {
            return response()->json(['error' => 'No inspected items found'], 404);
        }

        try {
            // Generate Excel with inspected items only
            $sanitizedArticleNumber = preg_replace('/[^A-Za-z0-9_-]/', '_', $product->full_article_number);
            $fileName = "MRF_Inspected_{$project->project_no}_{$sanitizedArticleNumber}.xlsx";

            // Store in public disk
            $filePath = 'temp/' . $fileName;

            // Make sure temp directory exists
            if (!Storage::disk('public')->exists('temp')) {
                Storage::disk('public')->makeDirectory('temp');
            }

            // Generate and store Excel file - CORRECTED: Use 'public' as disk name, not XLSX
            Excel::store(
                new MRFToWarehouseExport($productId, $projectId, 'inspected'),
                $filePath,
                'public'  // Disk name, not file type
            );

            // Get full path for email attachment
            $fullFilePath = storage_path('app/public/' . $filePath);

            // Fetch Warehouse Person emails
            $warehouseUsers = User::where('role', 'Warehouse Person')->pluck('email')->toArray();

            if (empty($warehouseUsers)) {
                return response()->json(['error' => 'No Warehouse Person users found.'], 404);
            }

            $batchId = uniqid('mrf_', true);
                DB::table('stock_bom_po')
                ->where('product_id', $productId)
                ->where('project_id', $projectId)
                // Exclude already emailed records
                ->where(function ($exclude) {
                    $exclude->where('is_email_sent', '!=', 1)
                            ->orWhereNull('mrf_email_sent_date');
                })
                ->where(function ($mainQuery) use ($project_no) {
                    $mainQuery
                        // Condition 1: Inspected items
                        ->whereExists(function ($query) use ($project_no) {
                            $query->select(DB::raw(1))
                                ->from('initial_inspection_data')
                                ->where('project_no', $project_no)
                                ->whereColumn('po_number', 'stock_bom_po.po_no')
                                ->whereRaw("REPLACE(artical_no, ' ', '') = REPLACE(stock_bom_po.article_no, ' ', '')")
                                ->whereRaw("description LIKE CONCAT('%', stock_bom_po.description, '%')");
                        })
                        // OR Condition 2: Items where select_option = 'stock' and is_email_sent = 0
                        ->orWhere(function ($orQuery) {
                            $orQuery->where('select_option', '=', 'stock')
                                    ->where('is_email_sent', 0);
                        });
                })
                ->update([
                    'is_email_sent' => 1,
                    'mrf_email_sent_date' => now(),
                    'mrf_email_batch' => $batchId,
                ]);

                // Prepare email data
            $emailDataBase = [
                'project_no' => $project->project_no,
                'project_name' => $project->project_name,
                'description' => $product->description,
                'full_article_number' => $product->full_article_number,
                'product_id' => $productId,
                'project_id' => $projectId,
                'batch' => $batchId,
            ];

            // Send email to each Warehouse Person
            foreach ($warehouseUsers as $email) {
                $name = User::where('email', $email)->value('name') ?? '';
                $emailData = array_merge($emailDataBase, [
                    'recipient_email' => $email,
                    'recipient_name'  => $name,
                ]);
                Log::info('MRF File Full Path for Email Attachment: ' . $fullFilePath);
                Mail::to($email)->send(new SendMRFToWarehouse($emailData, $fullFilePath));
            }
                
           // Delete temp file
            Storage::disk('public')->delete($filePath);
            Project::where('id', $projectId)->update(['status' => 1]);
            return response()->json(['success' => true, 'message' => 'Email sent successfully for inspected items!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to send email: ' . $e->getMessage()], 500);
        }
    }

    // Send email for COMPLETED inspection items
    public function send_mrf_email_from_stock(Request $request){
        $productId = $request->input('product_id');
        $projectId = $request->input('project_id');

        $project = Project::find($projectId);
        if (!$project) {
            return response()->json(['error' => 'Project not found'], 404);
        }

        $product = ProductsOfProjects::find($productId);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $project_no = $project->project_no;

        // Get only from stock items
        $mrf_from_stock_items = DB::table('stock_bom_po')
            ->where('product_id', $productId)
            ->where('project_id', $projectId)
            ->where('is_email_sent', 0)
            ->whereNotNull('po_no')
            ->get();

        if ($mrf_from_stock_items->isEmpty()) {
            return response()->json(['error' => 'No from stock items found'], 404);
        }

        try {
            // Generate Excel with inspected items only
            $sanitizedArticleNumber = preg_replace('/[^A-Za-z0-9_-]/', '_', $product->full_article_number);
            $fileName = "MRF_Inspected_{$project->project_no}_{$sanitizedArticleNumber}.xlsx";

            // Store in public disk
            $filePath = 'temp/' . $fileName;

            // Make sure temp directory exists
            if (!Storage::disk('public')->exists('temp')) {
                Storage::disk('public')->makeDirectory('temp');
            }

            // Generate and store Excel file - CORRECTED: Use 'public' as disk name, not XLSX
            Excel::store(
                new MRFToWarehouseExport($productId, $projectId, 'from_stock'),
                $filePath,
                'public'  // Disk name, not file type
            );

            // Get full path for email attachment
            $fullFilePath = storage_path('app/public/' . $filePath);

            // Fetch Warehouse Person emails
            $warehouseUsers = User::where('role', 'Warehouse Person')->pluck('email')->toArray();

            if (empty($warehouseUsers)) {
                return response()->json(['error' => 'No Warehouse Person users found.'], 404);
            }

            $batchId = uniqid('mrf_', true);
                DB::table('stock_bom_po')
                ->where('product_id', $productId)
                ->where('project_id', $projectId)
                ->update([
                    'is_email_sent' => 1,
                    'mrf_email_sent_date' => now(),
                    'mrf_email_batch' => $batchId,
                ]);

                // Prepare email data
            $emailDataBase = [
                'project_no' => $project->project_no,
                'project_name' => $project->project_name,
                'description' => $product->description,
                'full_article_number' => $product->full_article_number,
                'product_id' => $productId,
                'project_id' => $projectId,
                'batch' => $batchId,
            ];

            // Send email to each Warehouse Person
            foreach ($warehouseUsers as $email) {
                $name = User::where('email', $email)->value('name') ?? '';
                $emailData = array_merge($emailDataBase, [
                    'recipient_email' => $email,
                    'recipient_name'  => $name,
                ]);
                Mail::to($email)->send(new SendMRFToWarehouse($emailData, $fullFilePath));
            }
                
           // Delete temp file
            Storage::disk('public')->delete($filePath);
            Project::where('id', $projectId)->update(['status' => 1]);
            return response()->json(['success' => true, 'message' => 'Email sent successfully for from stock items!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to send email: ' . $e->getMessage()], 500);
        }
    }

    // Send email for PENDING inspection items
    public function send_mrf_email_not_inspected(Request $request){
        $productId = $request->input('product_id');
        $projectId = $request->input('project_id');

        $project = Project::find($projectId);
        if (!$project) {
            return response()->json(['error' => 'Project not found'], 404);
        }

        $product = ProductsOfProjects::find($productId);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $project_no = $project->project_no;

        // Get only NOT inspected items
        $not_inspected_items = DB::table('stock_bom_po')
            ->where('product_id', $productId)
            ->where('project_id', $projectId)
            ->where('is_email_sent', 0)
            ->whereNotNull('po_no')
            ->where('select_option', '!=', 'stock')
            ->whereNotExists(function ($query) use ($project_no) {
                $query->select(DB::raw(1))
                    ->from('initial_inspection_data')
                    ->where('project_no', $project_no)
                    ->whereColumn('po_number', 'stock_bom_po.po_no')
                    ->whereRaw("REPLACE(artical_no, ' ', '') = REPLACE(stock_bom_po.article_no, ' ', '')")
                    ->whereRaw("description LIKE CONCAT('%', stock_bom_po.description, '%')");
            })
            ->get();

        if ($not_inspected_items->isEmpty()) {
            return response()->json(['error' => 'No pending inspection items found'], 404);
        }

        try {
            // Generate Excel with not inspected items only
            $sanitizedArticleNumber = preg_replace('/[^A-Za-z0-9_-]/', '_', $product->full_article_number);
            $fileName = "MRF_Pending_{$project->project_no}_{$sanitizedArticleNumber}.xlsx";

            // Store in public disk
            $filePath = 'temp/' . $fileName;

            // Make sure temp directory exists
            if (!Storage::disk('public')->exists('temp')) {
                Storage::disk('public')->makeDirectory('temp');
            }

            // Generate and store Excel file - CORRECTED: Use 'public' as disk name
            Excel::store(
                new MRFToWarehouseExport($productId, $projectId, 'not_inspected'),
                $filePath,
                'public'  // Disk name, not file type
            );

            // Get full path for email attachment
            $fullFilePath = storage_path('app/public/' . $filePath);

            // Fetch Warehouse Person emails
            $warehouseUsers = User::where('role', 'Warehouse Person')->pluck('email')->toArray();

            if (empty($warehouseUsers)) {
                return response()->json(['error' => 'No Warehouse Person users found.'], 404);
            }

            // Prepare email data
            $emailDataBase = [
                'project_no' => $project->project_no,
                'project_name' => $project->project_name,
                'description' => $product->description,
                'full_article_number' => $product->full_article_number,
                'product_id' => $productId,
                'project_id' => $projectId,
            ];

            // Send email to each Warehouse Person
            foreach ($warehouseUsers as $email) {
                $emailData = array_merge($emailDataBase, ['recipient_email' => $email]);
                Mail::to($email)->send(new SendMRFToWarehouse($emailData, $fullFilePath));
            }

            // Mark these specific items as email sent
            DB::table('stock_bom_po')
                ->where('product_id', $productId)
                ->where('project_id', $projectId)
                ->where('is_email_sent', 0)
                ->whereNotNull('po_no')
                ->where('select_option', '!=', 'stock')
                ->whereNotExists(function ($query) use ($project_no) {
                    $query->select(DB::raw(1))
                        ->from('initial_inspection_data')
                        ->where('project_no', $project_no)
                        ->whereColumn('po_number', 'stock_bom_po.po_no')
                        ->whereRaw("REPLACE(artical_no, ' ', '') = REPLACE(stock_bom_po.article_no, ' ', '')")
                        ->whereRaw("description LIKE CONCAT('%', stock_bom_po.description, '%')");
                })
                ->update(['is_email_sent' => 1, 'mrf_email_sent_date' => now()]);

            // Delete temp file
            Storage::disk('public')->delete($filePath);

            return response()->json(['success' => true, 'message' => 'Email sent successfully for pending inspection items!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to send email: ' . $e->getMessage()], 500);
        }
    }

    public function updateCheckStatus(Request $request){
        $id = $request->input('id');
        $type = $request->input('type');
        $checked = $request->input('checked');

        try {
            if ($type === 'nameplate_img') {
                $product = QtyOfProduct::findOrFail($id);
                $product->is_final_inspection_started = 1; // Set to 1 when confirmed
                $product->save();
            } else {
                return response()->json(['success' => false, 'message' => 'Invalid type']);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function assign_task(Request $request){
        $product = ProductsOfProjects::find($request->productId);
        $product->assigned_qty = $product->assigned_qty + $request->quantity;

        if ($product->qty == $product->assigned_qty) {
            $product->superwisor_status = "1";
        }

        $product->operator_id = $request->operator_id;
        $product->save();

        // Assign the same quantity to each selected operator
        $operator_ids_string = implode(",", $request->operator_ids); // e.g., "1,2,3"

        for ($i = 0; $i < $request->quantity; $i++) {
            $assigned = new AssignedProductToOperator();
            $assigned->project_id   = $request->project_id;
            $assigned->product_id   = $request->productId;
            $assigned->operator_id  = $operator_ids_string; // store as comma-separated string
            $assigned->assigned_qty = 1;

            // order_qty (global, since no individual operator used here)
            $lastEntry = AssignedProductToOperator::where('project_id', $request->project_id)
                ->where('product_id', $request->productId)
                ->orderBy('id', 'desc')
                ->first();

            $assigned->order_qty = $lastEntry ? $lastEntry->order_qty + 1 : $i + 1;
            $assigned->seq_qty = $lastEntry ? $lastEntry->seq_qty + 1 : $i + 1;
            $assigned->save();

            $processes = ProjectProcessStdTime::where('projects_id', $request->project_id)
                ->where('product_id', $request->productId)
                ->where('order_qty', $assigned->order_qty) // same order
                ->get();

            foreach ($processes as $process) {
                $operatorsArray = [];

                foreach ($request->operator_ids as $opId) {
                    $operatorsArray[] = [
                        "id"          => $opId,
                        "status"      => null, // initially null
                        "total_time"  => null,
                        "started_at"  => null,
                        "ended_at"    => null
                    ];
                }

                $process->operators_time_tracking = json_encode($operatorsArray);
                $process->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Task assigned successfully..!!',
            'redirect_url' => route('ProductionSuperwisorInbox')
        ]);
    }

    public function mrf_excel_download(Request $request){
        $articleNumber = $request->input('article_number');
        $description = $request->input('description');
        $qty = $request->input('qty');

        // Find the product in products_of_projects
        $product = ProductsOfProjects::where('full_article_number', $articleNumber)
            ->where('description', $description)
            ->where('qty', $qty)
            ->first();

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $productId = $product->id;
        $projectId = $product->project_id;

        // Get project number
        $project = Project::find($projectId);
        if (!$project) {
            return response()->json(['error' => 'Project not found'], 404);
        }

        // Sanitize article number to remove invalid filename characters
        $sanitizedArticleNumber = preg_replace('/[^A-Za-z0-9_-]/', '_', $articleNumber);

        // Construct filename
        $fileName = "MRF_{$project->project_no}_{$sanitizedArticleNumber}.xlsx";

        // Use the template file
        $templatePath = public_path('storage/templates/MRF_to_warehouse.xlsx');
        return Excel::download(new MRFToWarehouseExport($productId, $projectId), $fileName, \Maatwebsite\Excel\Excel::XLSX, ['template' => $templatePath]);
    }

    public function showOperatorList(Request $request){
        $projectId = $request->input('project_id');
        $productId = $request->input('product_id');

        // Fetch assigned records
        $assignedRecords = AssignedProductToOperator::where('project_id', $projectId)
            ->where('product_id', $productId)
            ->orderBy('id', 'asc')
            ->get();

        // Prepare response
        $response = [];

        foreach ($assignedRecords as $record) {
            // Split the comma-separated operator_ids
            $operatorIds = explode(',', $record->operator_id);

            // Fetch the actual operator details
            $operators = User::whereIn('id', $operatorIds)->get();

            $response[] = [
                'id'             => $record->id,
                'project_id'     => $record->project_id,
                'product_id'     => $record->product_id,
                'assigned_qty'   => $record->assigned_qty,
                'order_qty'      => $record->order_qty,
                'seq_qty'        => $record->seq_qty,
                'operator_ids'   => $operatorIds,
                'operators'      => $operators,
                'created_at'     => $record->created_at,
                'updated_at'     => $record->updated_at,
            ];
        }

        return response()->json($response);
    }

    public function assemblyProcessConfirm(Request $request){
        $id = $request->input('id');
        try {
            $unit_of_qty_of_product = QtyOfProduct::findOrFail($id);
            $unit_of_qty_of_product->is_final_inspection_started = 1;
            $unit_of_qty_of_product->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function approvePDF(Request $request){
        $product = ProductsOfProjects::findOrFail($request->product_id);

        $product->is_asbuilt_drawing_pdf_approve_by_production_superwisor = 1;
        $product->save();

        return response()->json(['success' => true, 'message' => 'PDF Approved Successfully.']);
    }

    public function rejectPDF(Request $request){
        $product = ProductsOfProjects::findOrFail($request->product_id);

        $product->is_asbuilt_drawing_pdf_approve_by_production_superwisor = 2;
        $product->asbuilt_drawing_approve_reject_remarks_by_production_superwisor = $request->reason ?? null;
        $product->save();

        return response()->json(['success' => true, 'message' => 'PDF Rejected Successfully.']);
    }

    public function sendMRFEmail(Request $request){
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer|exists:products_of_projects,id',
            'project_id' => 'required|integer|exists:projects,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $productId = $request->input('product_id');
        $projectId = $request->input('project_id');

        // Fetch product and project details
        $product = ProductsOfProjects::findOrFail($productId);
        $project = Project::findOrFail($projectId);

        // Fetch Warehouse Person emails
        $warehouseUsers = User::where('role', 'Warehouse Person')->pluck('email')->toArray();
        if (empty($warehouseUsers)) {
            return response()->json(['success' => false, 'message' => 'No Warehouse Person users found.'], 404);
        }

        try {
            // Generate the Excel file
            $sanitizedArticleNumber = preg_replace('/[^A-Za-z0-9_-]/', '_', $product->full_article_number);
            $fileName = "MRF_{$project->project_no}_{$sanitizedArticleNumber}.xlsx";
            $excelPath = storage_path('app/public/' . $fileName);

            // Ensure the directory exists
            $directory = dirname($excelPath);
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            // Generate and store the Excel file
            Excel::store(new MRFToWarehouseExport($productId, $projectId), 'public/' . $fileName, 'local', \Maatwebsite\Excel\Excel::XLSX);

            // Prepare base email data
            $emailDataBase = [
                'project_no' => $project->project_no,
                'project_name' => $project->project_name,
                'description' => $product->description,
                'full_article_number' => $product->full_article_number,
                'product_id' => $productId,
                'project_id' => $projectId,
            ];

            // Send email to each Warehouse Person
            foreach ($warehouseUsers as $email) {
                $emailData = array_merge($emailDataBase, ['recipient_email' => $email]);
                Mail::to($email)->send(new SendMRFToWarehouse($emailData, $excelPath));
            }

            // Update is_email_sent and mrf_email_sent_date in stock_bom_po
            StockBOMPo::where('product_id', $productId)
                ->where('project_id', $projectId)
                ->update([
                    'is_email_sent' => 1,
                    'mrf_email_sent_date' => now(),
                ]);

            Project::where('id', $projectId)->update(['status' => 1]);

            // Clean up the Excel file
            File::delete($excelPath);

            return response()->json(['success' => true, 'message' => 'Email sent successfully to Warehouse Persons!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error sending email: ' . $e->getMessage()], 500);
        }
    }

    public function markMaterialsReady(Request $request){
        $productId = $request->query('product_id');
        $projectId = $request->query('project_id');
        $email = $request->query('email');
        $batch_order_mrf_email = $request->query('batch');

        // Validate input
        $validator = Validator::make($request->query(), [
            'product_id' => 'required|integer|exists:products_of_projects,id',
            'project_id' => 'required|integer|exists:projects,id',
            'email' => 'required|email|exists:users,email',
            'batch' => 'required',
        ]);

        if ($validator->fails()) {
            return view('production_superwisor.materials_ready_response', [
                'message' => $validator->errors()->first(),
                'status' => 'error'
            ]);
        }

        try {
            // Check if already marked as ready (is_email_sent = 2)
            $stockBOM = StockBOMPo::where('product_id', $productId)
                ->where('project_id', $projectId)
                ->where('is_email_sent', 2)
                ->where('mrf_email_batch', $batch_order_mrf_email)
                ->first();

            if ($stockBOM) {
                // Fetch the user who confirmed the MRF
                $confirmedByUser = User::where('email', $stockBOM->confirmed_by_email)->first();
                $confirmedByName = $confirmedByUser ? $confirmedByUser->name : 'Unknown User';

                return view('production_superwisor.materials_ready_response', [
                    'message' => "This MRF has already been marked as ready by {$confirmedByName}.",
                    'status' => 'already_responded'
                ]);
            }

            // Update is_email_sent to 2, confirmed_by_email, and mrf_ready_date
            $updated = StockBOMPo::where('product_id', $productId)
                ->where('project_id', $projectId)
                ->where('is_email_sent', 1)
                ->where('mrf_email_batch', $batch_order_mrf_email)
                ->update([
                    'is_email_sent' => 2,
                    'confirmed_by_email' => $email,
                    'mrf_ready_date' => now(),
                ]);

            if ($updated) {
                // Fetch all stock_bom_po records for this product and project to check each item individually
                $bomRecords = StockBOMPo::where('product_id', $productId)
                    ->where('project_id', $projectId)
                    ->get();

                foreach ($bomRecords as $bomRecord) {
                    if ($bomRecord->is_email_sent == 2 && !is_null($bomRecord->mrf_ready_date) && !is_null($bomRecord->confirmed_by_email) && $bomRecord->select_option != 'stock') {
                        // Fetch available_qty from stock_master_module using description and article_no for this specific item
                        $stockMasterRecord = DB::table('stock_master_module')
                            ->where('item_desc', $bomRecord->description)
                            ->where('article_number', $bomRecord->article_no)
                            ->first();

                        // Replace hold_qty with total_required_quantity for this specific item
                        $bomRecord->update(['hold_qty' => $bomRecord->total_required_quantity]);

                        // Update stock_master_module after stock_bom_po changes are complete
                        $stockMasterRecord = DB::table('stock_master_module')
                            ->where('item_desc', $bomRecord->description)
                            ->where('article_number', $bomRecord->article_no)
                            ->first();

                        if ($stockMasterRecord) {
                            $newHoldQty = ($stockMasterRecord->hold_qty ?? 0) + $bomRecord->hold_qty;
                            $newAvailableQty = $stockMasterRecord->available_qty - $bomRecord->hold_qty;
                            DB::table('stock_master_module')
                                ->where('item_desc', $bomRecord->description)
                                ->where('article_number', $bomRecord->article_no)
                                ->update([
                                    'hold_qty' => $newHoldQty,
                                    'available_qty' => $newAvailableQty
                                ]);
                        }
                    }
                }
            }

            if ($updated) {
                return view('production_superwisor.materials_ready_response', [
                    'message' => 'Materials marked as ready successfully!',
                    'status' => 'success'
                ]);
            } else {
                return view('production_superwisor.materials_ready_response', [
                    'message' => 'No pending MRF found for this product and project.',
                    'status' => 'error'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error marking materials ready: ' . $e->getMessage(), ['productId' => $productId, 'projectId' => $projectId, 'email' => $email]);
            return view('production_superwisor.materials_ready_response', [
                'message' => 'Error processing request: ' . $e->getMessage(),
                'status' => 'error'
            ]);
        }
    }

    public function view_project_details(Request $request){
        $projectId = $request->project_id;

        $products = DB::table('qty_of_products')
            ->join('products_of_projects', 'qty_of_products.product_id', '=', 'products_of_projects.id')
            ->select(
                'products_of_projects.full_article_number',
                'products_of_projects.description',
                'products_of_projects.qty',
                'qty_of_products.qty_number',
                'qty_of_products.is_final_inspection_started'
            )
            ->where('qty_of_products.project_id', $projectId)
            ->where('products_of_projects.delivery', '!=', '2')
            ->get();
        return response()->json($products);
    }

    private function handleProjectCompletion(Project $project, $fullPLFilePath){
    
        // Update Project table
        Project::where('id', $project->id)->update([
            'PL_PDF_path' => $project->PL_PDF_path, // Already set in full order upload
            'actual_readiness' => now(),
            'pl_uploaded_date' => now(),
            'status' => 2
        ]);

        // Fetch production team members
        $emails = ProductionTeamDetail::select('email', 'name', 'designation')->where('designation','!=','Order Management')->get();
        $redirectLink = route('ProductionManagerProjectIndex');

        // Send email to each production team member
        foreach ($emails as $sendEmail) {
            $emailData = [
                'project_name' => $project->project_name,
                'project_no' => $project->project_no,
                'sales_name' => $project->sales_name,
                'customer_name' => $project->customer_name,
                'country' => $project->country,
                'designation' => $sendEmail->designation,
                'email' => $sendEmail->email,
                'name' => $sendEmail->name,
                'redirect_link' => $redirectLink,
            ];
            try {

                // Alpesh Maru Date: 13-12-2025 Code Start
                
                //Mail::to($sendEmail->email)->send(new WItrackProjectCompleteNotifyProductionTeam($emailData));

                Log::info("FINAL PATH BEFORE MAIL: " . $fullPLFilePath);
                Log::info("EXISTS? " . (file_exists($fullPLFilePath) ? 'YES' : 'NO'));

                Mail::to($sendEmail->email)
                    ->send(new WItrackProjectCompleteNotifyProductionTeam($emailData, $fullPLFilePath));

                // Alpesh Maru Date: 13-12-2025 Code End


            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to send completion email'
                ], 500);
            }
        }

        // Send Witrack API request if witrack_no exists // This is for complete order
        if ($project->witrack_no) {
            try {
                $response = Http::post(route('api.send-project-complete'), [
                    'witrack_no' => $project->witrack_no
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to send Witrack API request'
                ], 500);
            }
        }
    }

    private function isProjectFullyUploaded(Project $project){
        // Check if full order PL is uploaded
        $fullOrderUploaded = !empty($project->PL_PDF_path);

        // Check if all partial orders are uploaded
        $partialOrders = QtyOfProduct::where('qty_of_products.project_id', $project->id)
            ->join('products_of_projects', 'qty_of_products.product_id', '=', 'products_of_projects.id')
            ->where('products_of_projects.delivery', 2)
            ->select('qty_of_products.*')
            ->get();

        $allPartialUploaded = $partialOrders->isEmpty() || $partialOrders->every(function ($qty) {
            return !empty($qty->PL_PDF_path);
        });

        return $fullOrderUploaded && $allPartialUploaded;
    }

    public function uploadPlDoc(Request $request){
        $project = Project::find($request->input('id'));
        if (!$project) {
            return response()->json(['success' => false, 'message' => 'Project not found'], 404);
        }

        // Validate the request
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:pdf,xls,xlsx',
            'id' => 'required|integer',
            'lable' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Retrieve the validated input
        $file = $request->file('file');
        $id = $request->input('id');
        $label = $request->input('lable');

        // Define folder and file path
        $folderPath = public_path("project_document/{$project->project_no}/Project Execution/Full_Order_PL/");
        $fileName = time() . '_' . $file->getClientOriginalName();
        $fullPath = "project_document/{$project->project_no}/Project Execution/Full_Order_PL/" . $fileName;

        // Check if directory exists, if not create it
        if (!File::exists($folderPath)) {
            File::makeDirectory($folderPath, 0755, true);
        }
        // Move file to target directory
        $file->move($folderPath, $fileName);
        // Update Project table with PL_PDF_path only
        $project->PL_PDF_path = $fullPath;
        $project->pl_uploaded_date = now();
        $project->save();

        $fullOrderProductIds = ProductsOfProjects::where('project_id', $project->id)
            ->where('delivery', 1)
            ->pluck('id');

        $qty_of_product_of_project = QtyOfProduct::where('project_id', $project->id)->whereIn('product_id', $fullOrderProductIds)->get();

        if ($qty_of_product_of_project) {
            foreach ($qty_of_product_of_project as $val) {
                $val->PL_PDF_path = $fullPath;
                $val->pl_uploaded_date = now();
                $val->save();
            }
        }

        // Alpesh Maru Date: 12-12-2025 Code Start

        // Get full path for email attachment
        $fullPLFilePath = public_path($fullPath);

        // Alpesh Maru Date: 12-12-2025 Code End

        // START: Updated logic to update stock_master_module qty and transfer hold_qty to release_qty
        DB::transaction(function () use ($project, $fullOrderProductIds) {
            $stockBomPoItems = StockBOMPo::where('project_id', $project->id)
                ->whereIn('product_id', $fullOrderProductIds)
                ->where('hold_qty', '>', 0)
                ->get();

            foreach ($stockBomPoItems as $bomItem) {
                $stockMaster = StockMasterModule::where('item_desc', $bomItem->description)
                    ->where('article_number', $bomItem->article_no)
                    ->first();

                if ($stockMaster) {
                    StockMasterModule::where('item_desc', $bomItem->description)
                        ->where('article_number', $bomItem->article_no)
                        ->update([
                            'qty' => $stockMaster->qty - $bomItem->hold_qty,
                            'hold_qty' => $stockMaster->hold_qty - $bomItem->hold_qty,
                            'updated_at' => now()
                        ]);
                }
            }

            // Existing logic to transfer hold_qty to release_qty
            StockBOMPo::where('project_id', $project->id)
                ->whereIn('product_id', $fullOrderProductIds)
                ->where('hold_qty', '>', 0)
                ->update([
                    'release_qty' => DB::raw('hold_qty'),
                    'hold_qty' => 0
                ]);
        });
        // END: Updated logic

        // Check if all documents are uploaded
        if ($this->isProjectFullyUploaded($project)) {

            Log::info("uploadPlDoc isProjectFullyUploaded: " . $fullPLFilePath);
            $this->handleProjectCompletion($project, $fullPLFilePath);
        } else {
            Log::info("uploadPlDoc Not isProjectFullyUploaded: " . $fullPLFilePath);
            // Send full order email to production team & also API to wiTrack project = Email = Tested

            if ($project) {
                try {
                    $response = Http::post(route('api.send-project-full-order-complete'), [
                        'project_id' => $project->id
                    ]);
                } catch (\Exception $e) {
                }
            }

            $emails = ProductionTeamDetail::select('email', 'name', 'designation')->where('designation','!=','Order Management')->get();
            $redirectLink = route('ProductionManagerProjectIndex');

            foreach ($emails as $sendEmail) {
                $emailData = [
                    'project_name' => $project->project_name,
                    'project_no' => $project->project_no,
                    'sales_name' => $project->sales_name,
                    'customer_name' => $project->customer_name,
                    'country' => $project->country,
                    'designation' => $sendEmail->designation,
                    'email' => $sendEmail->email,
                    'name' => $sendEmail->name,
                    'redirect_link' => $redirectLink,
                ];
                try {
                    

                    // Alpesh Maru Date: 12-12-2025 Code Start
                    
                    // Mail::to($sendEmail->email)->send(new WItrackProjectFullCompleteNotifyProductionTeam($emailData));

                    Mail::to($sendEmail->email)
                        ->send(new WItrackProjectFullCompleteNotifyProductionTeam($emailData, $fullPLFilePath));

                    // Alpesh Maru Date: 12-12-2025 Code End

                    
                } catch (\Exception $e) {
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'File uploaded successfully.!', 'file_path' => $fullPath]);
    }

    public function uploadPartialPlDoc(Request $request){
        // Validate the request
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:pdf,xls,xlsx',
            'id' => 'required|integer|exists:qty_of_products,id',
            'lable' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Retrieve the validated input
        $file = $request->file('file');
        $qtyId = $request->input('id');
        $label = $request->input('lable');

        // Find the qty_of_products record
        $qty = QtyOfProduct::find($qtyId);
        if (!$qty) {
            return response()->json(['success' => false, 'message' => 'Quantity record not found'], 404);
        }

        // Find the associated project
        $project = Project::find($qty->project_id);
        if (!$project) {
            return response()->json(['success' => false, 'message' => 'Project not found'], 404);
        }

        // Find the associated product
        $product = ProductsOfProjects::find($qty->product_id);
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        // Sanitize article number for safe file path
        $sanitizedArticleNumber = preg_replace('/[^A-Za-z0-9_-]/', '_', $product->full_article_number);

        // Define folder and file path
        $folderPath = public_path("project_document/{$project->project_no}/Project Execution/Partial_Order_PL/{$sanitizedArticleNumber}/{$qty->qty_number}/");
        $fileName = time() . '_' . $file->getClientOriginalName();
        $fullPath = "project_document/{$project->project_no}/Project Execution/Partial_Order_PL/{$sanitizedArticleNumber}/{$qty->qty_number}/{$fileName}";

        // Check if directory exists, if not create it
        if (!File::exists($folderPath)) {
            File::makeDirectory($folderPath, 0755, true);
        }

        // Move file to target directory
        $file->move($folderPath, $fileName);

        // Update qty_of_products table
        $qty->update([
            'PL_PDF_path' => $fullPath,
            'pl_uploaded_date' => now(),
        ]);

        // Alpesh Maru Date: 12-12-2025 Code Start

        // Get full path for email attachment
        $fullPLFilePath = public_path($fullPath);

        // Alpesh Maru Date: 12-12-2025 Code End

        // START: Updated logic for partial stock release
        DB::transaction(function () use ($project, $product) {
            $stockBomPoItems = DB::table('stock_bom_po')
                ->where('project_id', $project->id)
                ->where('product_id', $product->id)
                ->where('hold_qty', '>', 0)
                ->get();

            foreach ($stockBomPoItems as $bomItem) {
                $stockMaster = DB::table('stock_master_module')
                    ->where('item_desc', $bomItem->description)
                    ->where('article_number', $bomItem->article_no)
                    ->first();

                if ($stockMaster) {
                    $newQty = $stockMaster->qty - $bomItem->item_quantity;
                    $newHoldQty = $stockMaster->hold_qty - $bomItem->item_quantity;
                    if ($newQty < 0 || $newHoldQty < 0) {
                        continue;
                    }
                    DB::table('stock_master_module')
                        ->where('item_desc', $bomItem->description)
                        ->where('article_number', $bomItem->article_no)
                        ->update([
                            'qty' => $newQty,
                            'hold_qty' => $newHoldQty,
                            'updated_at' => now()
                        ]);
                } else {
                }
            }

            // Existing logic to transfer item_quantity from hold_qty to release_qty
            DB::table('stock_bom_po')
                ->where('project_id', $project->id)
                ->where('product_id', $product->id)
                ->update([
                    'hold_qty' => DB::raw('hold_qty - item_quantity'),
                    'release_qty' => DB::raw('COALESCE(release_qty, 0) + item_quantity')
                ]);
        });

        // Check if all documents are uploaded
        if ($this->isProjectFullyUploaded($project)) {
            Log::info("uploadPartialPlDoc Not isProjectFullyUploaded: " . $fullPLFilePath);
            $this->handleProjectCompletion($project, $fullPLFilePath);
        } else {
            Log::info("uploadPartialPlDoc Not isProjectFullyUploaded: " . $fullPLFilePath);
            // Send partial order email to production team & also API to wiTrack project = Email = Tested
            if ($qty) {
                try {
                    $response = Http::post(route('api.send-project-partial-order-complete'), [
                        'qty' => $qty->id
                    ]);
                } catch (\Exception $e) {
                }
            }

            $emails = ProductionTeamDetail::select('email', 'name', 'designation')->where('designation','!=','Order Management')->get();
            $redirectLink = route('ProductionManagerProjectIndex');

            foreach ($emails as $sendEmail) {
                $emailData = [
                    'project_name' => $project->project_name,
                    'project_no' => $project->project_no,
                    'sales_name' => $project->sales_name,
                    'customer_name' => $project->customer_name,
                    'country' => $project->country,
                    'designation' => $sendEmail->designation,
                    'email' => $sendEmail->email,
                    'name' => $sendEmail->name,
                    'redirect_link' => $redirectLink,
                    'qty_number' => $qty->qty_number,
                    'article_number' => $product->full_article_number,
                    'product_description' => $product->description,
                ];
                try {

                    // Alpesh Maru Date: 12-12-2025 Code Start

                    //Mail::to($sendEmail->email)->send(new WItrackProjectPartialCompleteNotifyProductionTeam($emailData));
                    
                    Mail::to($sendEmail->email)
                        ->send(new WItrackProjectPartialCompleteNotifyProductionTeam($emailData, $fullPLFilePath));                        

                    // Alpesh Maru Date: 12-12-2025 Code End

                } catch (\Exception $e) {
                }
            }
        }
        return response()->json(['success' => true, 'message' => 'File uploaded successfully!', 'file_path' => $fullPath]);
    }
}
