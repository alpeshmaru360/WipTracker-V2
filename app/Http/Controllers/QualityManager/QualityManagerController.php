<?php

namespace App\Http\Controllers\QualityManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProjectStatus;
use App\Models\Project;
use App\Models\ProductsOfProjects;
use App\Models\QtyOfProduct;
use App\Models\NCR;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderTable;
use App\Models\InitialInspectionName;
use App\Models\InitialInspectionTable;
use App\Models\FinalInspection;
use App\Models\FinalInspectionTable;
use App\Models\StockMasterModule;
use PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Services\DashboardService;

class QualityManagerController extends Controller
{

    public function dashboard(DashboardService $dashboardService){
        // Common Dashboard Service
        $role = auth()->user()->role;
        $dashboardData = $dashboardService->getDashboardData($role);       

        $page_title = "";
        // Non-Conformities chart data
        $purchaseOrders = PurchaseOrder::with(['poNCR' => function ($query) {
            $query->where('is_nonconformity_corrected', 1);
        }])->get();

        // Process data grouped by supplier
        $categories = [];

        foreach ($purchaseOrders as $po) {
            if ($po->poNCR->count() > 0) { // Only consider orders with at least one corrected non-conformity
                $supplier = $po->supplier ?? 'Unknown Supplier'; // Default to 'Unknown Supplier' if null

                if (!isset($categories[$supplier])) {
                    $categories[$supplier] = 0;
                }

                $categories[$supplier] += $po->poNCR->count(); // Count the number of NCR records
            }
        }
        return view('quality_manager.dashboard', compact('dashboardData', 'page_title', 'categories'));
    }

    public function ncr(){
        $page_title = "Non-Conformance Report";
        $ncrRecords = NCR::orderBy('id', 'desc')
            ->get()
            ->map(function ($ncr) {
                $ncr->pdf_url = asset('ncr_pdf/NCR-' . $ncr->cia_no . '-' . $ncr->updated_at->timestamp . '.pdf');
                return $ncr;
            });
        return view('quality_manager.ncr', compact('page_title', 'ncrRecords'));
    }

    public function inbox(){
        $page_title = "Pending Purchase Orders";

        // Fetch the hours for initial inspection from admin_hours_management
        $initialInspectionHours = DB::table('admin_hours_management')
            ->where('lable', 'StandardProcessTimes')
            ->where('key', 'initial_inspection')
            ->where('is_deleted', 0)
            ->value('value');

        $pendingInitialInspections = DB::table('purchase_order as po')
            ->join('purchase_order_table as pot', 'po.id', '=', 'pot.po_id')
            ->select([
                DB::raw('GROUP_CONCAT(DISTINCT po.id) as po_ids'),
                DB::raw('GROUP_CONCAT(DISTINCT pot.id) as pot_ids'),
                'po.id as po_id',
                'po.po_number',
                'po.project_no',
                'po.project_name',
                'po.supplier',
                'po.order_date',
                'po.product_article_no',
                'po.product_desc',
                'pot.is_initial_inspection_started',
                'pot.id as pot_id',
                'pot.po_id as pot_po_id',
                'pot.position_no',
                'pot.artical_no',
                'pot.description',
                'pot.quantity',
                'pot.unit_of_measure',
                'pot.direct_unit_cost',
                'pot.amount',
                'pot.actual_readiness_date',
                'pot.actual_received_date',
                'pot.ard_added_date',
                'pot.is_parent',
                'pot.is_partial_shipment',
                // DB::raw('GROUP_CONCAT(DISTINCT pot.is_partial_shipment) as shipment_status'), //Add this for status to show Full & Partial
                DB::raw('DATE(pot.actual_received_date) as grouped_received_date'),
            ])
            ->whereNotNull('pot.actual_received_date')
            ->where('pot.is_initial_inspection_started', 0)
            //->where('pot.is_partial_shipment','!=',1) // A Code: 09-01-2026 Commented
            ->groupBy(DB::raw('DATE(pot.actual_received_date)'), 'po.po_number', 'po.supplier', 'po.project_no')
            ->orderBy('pot.ard_added_date', 'DESC')
            ->get();

        // Calculate deadline for each record
        foreach ($pendingInitialInspections as $inspection) {
            if ($inspection->actual_received_date && is_numeric($initialInspectionHours) && $initialInspectionHours > 0) {
                try {
                    $requestDate = Carbon::parse($inspection->actual_received_date);
                    if (!$requestDate->isValid()) {
                        throw new \Exception("Invalid actual_received_date: {$inspection->actual_received_date}");
                    }
                    $deadline = $this->calculateInspectionDeadline($requestDate, (int)$initialInspectionHours);
                    $inspection->deadline = $deadline->format('d-m-y H:i'); // Ensure consistent format
                } catch (\Exception $e) {
                    $inspection->deadline = 'N/A';
                }
            } else {
                $inspection->deadline = 'N/A';
            }
        }
        // For Final Inspection code starts 
        $pendingFinalInspections = QtyOfProduct::with('projects')->with('products')->orderBy('id', 'desc')
            ->where('is_final_inspection_started', '1')
            ->get();
        // For Final Inspection code ends 
        return view('quality_manager.inbox', compact('pendingInitialInspections', 'pendingFinalInspections', 'page_title'));
    }

    //after click on place order from inbox screen this function is called
    public function quality_create_form(Request $request){
        // Decode URL-encoded JSON
        $potIdsRaw = $request->input('pot_ids');
        $decodedPotIds = json_decode(urldecode($potIdsRaw), true);

        // Handle invalid or missing JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            $decodedPotIds = [];
        }

        $data = [
            'id' => $request->input('id'),
            'po_number' => $request->input('po_number'),
            'project_no' => $request->input('project_no', 'N/A'),
            'project_name' => $request->input('project_name', 'N/A'),
            'product_article_no' => $request->input('product_article_no', 'N/A'),
            'product_desc' => $request->input('product_desc', 'N/A'),
            'supplier' => $request->input('supplier'),
            'artical_no' => $request->input('artical_no'),
            'pump_type' => $request->input('pump_type'),
            'description' => $request->input('description'),
            'quantity' => $request->input('quantity'),
            'pot_ids' => $decodedPotIds,
        ];

        $data['inspectionNames'] = InitialInspectionName::all();

        $project_no = $data['project_no'];

        if ($project_no == "N/A") {
            $project_no = null;
        }
        $purchaseOrderTableid =  $request->input('id');
        $purchaseOrderId = PurchaseOrder::where('po_number', $request->input('po_number'))->where('project_no', $project_no)->pluck('id')->first();

        // Modified query to fetch only relevant PurchaseOrderTable rows
        $data['purchaseOrderData'] = PurchaseOrderTable::where('po_id', $purchaseOrderId)
            ->where(function ($query) use ($purchaseOrderTableid) {
                // Include non-parent rows (child rows for partial or pending)
                $query->where('is_parent', 0)
                    // Include parent rows only if they have no child rows
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('is_parent', 1)
                            ->whereNotExists(function ($existsQuery) {
                                $existsQuery->select(DB::raw(1))
                                    ->from('purchase_order_table as child')
                                    ->whereColumn('child.parent_id', 'purchase_order_table.id');
                            });
                    })
                    // Ensure the row with the current ID is included if it's relevant
                    ->orWhere('id', $purchaseOrderTableid);
            })
            ->get();

        return view('quality_manager.create_quality', $data);
    }

    public function getProjectDetails($productId){
        // Get the product with its related project
        $product = ProductsOfProjects::with('projects')
            ->where('id', $productId)
            ->firstOrFail();

        // Access project details
        $projectNo = $product->projects->project_no;    // Assuming 'project_no' is a column in projects table
        $projectName = $product->projects->project_name;

        // You can return these values or pass them to a view
        return view('your.view', [
            'project_no' => $projectNo,
            'project_name' => $projectName,
            'product' => $product
        ]);
    }

    // Quality Initial Inspection Creation form submitting in this function
    public function store(Request $request){
        // Validate the request
        $request->validate([
            'po_number' => 'required|string',
            'supplier' => 'required|string',
            'project_number' => 'required|string',
            'project_name' => 'required|string',
            'pump_type' => 'required|integer',
            'reports_docs' => 'nullable|file|mimes:doc,docx|max:2048',
        ]);

        try {
            // Handle file upload
            $filePath = null;
            if ($request->hasFile('reports_docs')) {
                $file = $request->file('reports_docs');

                // Check if the file is valid
                if (!$file->isValid()) {
                    return redirect()->back()->with('error', 'File upload failed. Please try again.');
                }

                // Determine project folder (use 24-0 if project_number is empty)
                if ($request->project_number == 'N/A' || $request->project_number == NULL) {
                    $projectFolder = '24-0';
                } else {
                    $projectFolder = $request->project_number;
                }

                // Create directory path
                $basePath = public_path('project_document');
                $destinationPath = $basePath . '/' . $projectFolder . '/Quality/Incoming Inspection/' . $request->po_number;

                // Create directories if they don't exist
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true);
                }

                // Generate filename with timestamp
                $timestamp = now()->format('Ymd_His');
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $fileName = "{$timestamp}_{$originalName}.{$extension}";

                // Move file to destination
                $file->move($destinationPath, $fileName);
                $filePath = "project_document/{$projectFolder}/Quality/Incoming Inspection/{$request->po_number}/{$fileName}";
            }

            $articleNos   = (array) ($request->artical_no_group ?? [$request->artical_no ?? '-']);
            $descriptions = (array) ($request->description_group ?? [$request->description]);
            $quantities   = (array) ($request->quantity_group ?? [$request->quantity]);
            //$projectNos   = (array) ($request->project_no_group ?? [$request->project_number]);

            foreach ($articleNos as $index => $articleNo) {
                $description = $descriptions[$index] ?? null;
                $quantity    = $quantities[$index] ?? 0;

                if (empty($articleNo) || empty($description)) {
                    continue; // skip invalid rows
                }

                InitialInspectionTable::create([
                    'po_number'           => $request->po_number,
                    'supplier'            => $request->supplier,
                    'artical_no'          => $articleNo,
                    'project_no'          => $request->project_number,
                    'project_name'        => $request->project_name,
                    'pump_type'           => $request->pump_type,
                    'reports_docs'        => $filePath ?? null,
                    'description'         => $description,
                    'quantity'            => $quantity,
                    'ini_inspection_date' => now(),
                ]);

               // Step 1: Get all PO IDs from purchase_order table for given project number
                $project_number = $request->project_number === 'N/A' ? null : $request->project_number;
                $poIds = PurchaseOrder::where('project_no', $project_number)->where('po_number',$request->po_number)->pluck('id');

                // Step 2: Update records in purchase_order_table using these IDs
                PurchaseOrderTable::whereIn('po_id', $poIds)
                    ->where('artical_no', $articleNo)
                    ->where('description', $description)
                    ->whereNotNull('actual_received_date') // add this in wilo internal server
                    ->update([
                        'is_initial_inspection_started' => 1
                    ]);

                // A Code: 15-01-2026 Start

                // $hasPartialShipment = PurchaseOrderTable::whereIn('po_id', $poIds)
                //     ->where('artical_no', $articleNo)
                //     ->where('description', $description)
                //     ->where('is_partial_shipment', 1)
                //     ->exists();

                // if ($hasPartialShipment) {
                //     // First set all related rows to 0
                //     PurchaseOrderTable::whereIn('po_id', $poIds)
                //         ->where('artical_no', $articleNo)
                //         ->where('description', $description)
                //         ->update([
                //             'is_initial_inspection_started' => 0
                //         ]);

                //     // Then set ONLY parent row to 1
                //     PurchaseOrderTable::whereIn('po_id', $poIds)
                //         ->where('artical_no', $articleNo)
                //         ->where('description', $description)
                //         ->where('is_partial_shipment', 1)
                //         ->update([
                //             'is_initial_inspection_started' => 1
                //         ]);

                // } else {
                //     PurchaseOrderTable::whereIn('po_id', $poIds)
                //         ->where('artical_no', $articleNo)
                //         ->where('description', $description)
                //         ->update([
                //             'is_initial_inspection_started' => 1
                //         ]);
                // }

                // // A Code: 15-01-2026 End

            }

            foreach ($articleNos as $index => $articleNo) {
                $description = $descriptions[$index] ?? null;
                $quantity    = $quantities[$index] ?? 0;

                if (empty($articleNo) || empty($description)) {
                    continue; // Skip invalid or empty entries
                }

                // Fetch existing record
                $record = DB::table('stock_master_module')
                    ->where('article_number', $articleNo)
                    ->where('item_desc', $description)
                    ->first();

                if ($record) {
                    // Update existing record
                    $newQty          = $record->qty + $quantity;
                    $newAvailableQty = $record->available_qty + $quantity;

                    DB::table('stock_master_module')
                        ->where('article_number', $articleNo)
                        ->where('item_desc', $description)
                        ->update([
                            'qty'           => $newQty,
                            'available_qty' => $newAvailableQty,
                            'updated_at'    => now(),
                        ]);
                } else {
                    // Insert a new record
                    DB::table('stock_master_module')->insert([
                        'item_desc'             => $description,
                        'article_number'        => $articleNo,
                        'adder_code'            => '',
                        'qty'                   => $quantity,
                        'hold_qty'              => 0,
                        'available_qty'         => $quantity,
                        'minimum_required_qty'  => 5,
                        'std_time'              => 0,
                        'price'                 => 0.00,
                        'total_price'           => 0.00,
                        'created_at'            => now(),
                        'updated_at'            => now(),
                    ]);
                }
            }
            return redirect()->route('QUALITY')->with('success', 'Initial Inspection created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'File upload failed: ' . $e->getMessage());
        }
    }

    public function quality(){
        $page_title = "Initial Inspection";
        $initialInspectionData = DB::table('initial_inspection_data as ini')
            ->join('purchase_order as po', function ($join) {
                $join->on('po.po_number', '=', 'ini.po_number')
                     ->on('po.supplier', '=', 'ini.supplier')
                     ->on('po.project_no', '=', 'ini.project_no');
            })
            ->join('purchase_order_table as poT', function ($join) {
                $join->on('poT.po_id', '=', 'po.id');
            })
            ->join('initial_inspection_name as ins_name', 'ins_name.id', '=', 'ini.pump_type')
            ->select(
                'ini.*',
                'ins_name.name as pump_type_name',
                DB::raw('DATE(poT.actual_received_date) as actual_received_date')
            )
            ->where('poT.is_initial_inspection_started', 1)
            ->whereNotNull('poT.actual_received_date')
            ->groupBy(
                'ini.po_number',
                'ini.supplier',
                'ini.project_no',
                DB::raw('DATE(poT.actual_received_date)')
            )
            ->orderBy('ini.id', 'desc')
            ->get();
            
        $finalInspectionQuery = FinalInspectionTable::with('pumpType')->orderBy('id', 'desc');
        $finalInspectionData = $finalInspectionQuery->get();

        return view('quality_manager.quality', compact('page_title', 'initialInspectionData', 'finalInspectionData'));
    }
    
    public function showInitialItemList(Request $request){
        $actualReceivedDate = $request->input('actual_received_date');
        $poNumber = $request->input('po_number');
        $supplier = $request->input('item_supplier');
        $projectNo = $request->input('project_no');  

        // A Code: 20-01-2026 Start
        $itemsRecords = DB::table('initial_inspection_data as ini') 
            ->join('purchase_order_table as pot', function ($join) { 
                $join->on('pot.artical_no', '=', 'ini.artical_no') 
                        ->on('pot.description', '=', 'ini.description'); 
            }) 
            ->where('ini.po_number', $poNumber) 
            ->where('ini.project_no', $projectNo) 
            ->where('ini.supplier', $supplier) 
            ->whereDate('pot.actual_received_date', '=', $actualReceivedDate) 
            ->select( 
                'ini.artical_no', 
                'ini.description', 
                'ini.quantity', 
                DB::raw('MIN(pot.actual_received_date) as actual_received_date'),
                'pot.is_parent'
            ) 
            //->groupBy('ini.artical_no', 'ini.description', 'ini.quantity') 
            ->groupBy(
                'ini.id', // just added on 20-01-2026
                'ini.artical_no',
                'ini.description',
                'ini.quantity'
            )
            //->orderBy('ini.id', 'asc') 
            ->orderBy('ini.artical_no', 'asc') // just added on 20-01-2026
            ->get();
         // A Code: 20-01-2026 End  

        return response()->json($itemsRecords);
    }

    public function uploadInitialReport(Request $request){
        $request->validate([
            'inspection_id' => 'required|exists:initial_inspection_data,id',
            'reports_docs' => 'required|file|mimes:doc,docx|max:2048',
        ]);

        $inspection = InitialInspectionTable::findOrFail($request->inspection_id);
        $projectNo = $inspection->project_no;
        $poNumber = $inspection->po_number;
        $file = $request->file('reports_docs');

        // Generate filename with timestamp
        $timestamp = now()->format('Ymd_His');
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $fileName = "{$timestamp}_{$originalName}.{$extension}";

        if ($projectNo == 'N/A' || $projectNo == NULL) {
            $projectFolder = '24-0';
        } else {
            $projectFolder = $projectNo;
        }
        // Construct the destination path using public_path()
        $basePath = public_path('project_document');
        $destinationPath = $basePath . '/' . $projectFolder . '/Quality/Incoming Inspection/' . $poNumber;

        // Create the directory if it doesn't exist
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }
        // Check if files already exist in the target directory and delete them
        $existingFiles = File::files($destinationPath);
        foreach ($existingFiles as $existingFile) {
            File::delete($existingFile);
        }
        // Move the uploaded file to the destination path
        $filePath = 'project_document/' . $projectFolder . '/Quality/Incoming Inspection/' . $poNumber . '/' . $fileName;
        $file->move($destinationPath, $fileName);
        // Update the inspection record with the file path
        $inspection->reports_docs = $filePath;
        $inspection->save();
        return redirect()->back()->with('success', 'Initial report uploaded successfully.');
    }

    public function final_inspection_form(Request $request){
        $data = [
            'id' => $request->input('id'),
            'project_no' => $request->input('project_no', 'N/A'),
            'project_name' => $request->input('project_name', 'N/A'),
            'supplier' => $request->input('supplier'),
            'artical_no' => $request->input('artical_no'),
            'pump_type' => $request->input('pump_type'),
            'description' => $request->input('description'),
            'quantity' => $request->input('quantity'),
            'unit_qty' => $request->input('unit_qty')
        ];
        $data['inspectionNames'] = FinalInspection::all();
        $project_id = Project::where('project_no', $request->input('project_no'))->pluck('id')->first();
        $data['ProductDetailsData'] = ProductsOfProjects::where('project_id', $project_id)->get();
        return view('quality_manager.create_final_inspection', $data);
    }

    public function store_finalinspection(Request $request){
        // Validate the request
        $request->validate([
            'project_no' => 'required|string',
            'project_name' => 'required|string',
            'artical_no' => 'required|string',
            'serial_no' => 'required|string',
            'description' => 'required|string',
            'pump_type' => 'required|integer',
            'product_images' => 'required|array',
            'product_images.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp',
            'reports_docs' => 'required|file|mimes:doc,docx',
            'test_reports_docs' => 'file|mimes:doc,docx',
        ]);

        try {
            // Handle file uploads
            $imgfilePaths = [];
            $filePath = null;
            $testfilePath = null;

            $project_no = $request->project_no;
            $article_no = $request->artical_no;
            $unit_qty = $request->unit_qty;

            // Define base path for images
            $timestamp = now()->format('Ymd_His');
            $images_upload_path = "project_document/{$project_no}/Project Execution/images/{$article_no}/{$unit_qty}/";

            // Handle multiple image uploads
            if ($request->hasFile('product_images')) {
                foreach ($request->file('product_images') as $file) {
                    if (!$file->isValid()) {
                        return redirect()->back()->with('error', 'Image upload failed. Please try again.');
                    }
                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $file->getClientOriginalExtension();
                    $fileName = "{$timestamp}_{$originalName}.{$extension}";
                    $destinationPath = public_path($images_upload_path);
                    if (!File::exists($destinationPath)) {
                        File::makeDirectory($destinationPath, 0755, true);
                    }
                    $file->move($destinationPath, $fileName);
                    $imgfilePaths[] = $images_upload_path . $fileName;
                }
            }

            // Handle Final Inspection Report upload
            $report_upload_path = "project_document/{$project_no}/Quality/Final Inspection/{$article_no}/{$unit_qty}/";
            if ($request->hasFile('reports_docs')) {
                $file = $request->file('reports_docs');

                if (!$file->isValid()) {
                    return redirect()->back()->with('error', 'Final Inspection Report upload failed. Please try again.');
                }

                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $fileName = "{$timestamp}_{$originalName}.{$extension}";

                $destinationPath = public_path($report_upload_path);
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true);
                }

                $file->move($destinationPath, $fileName);
                $filePath = $report_upload_path . $fileName;
            }

            // Handle Test Report upload
            $test_report_upload_path = "project_document/{$project_no}/Quality/Test Reports/{$article_no}/{$unit_qty}/";
            if ($request->hasFile('test_reports_docs')) {
                $file = $request->file('test_reports_docs');

                if (!$file->isValid()) {
                    return redirect()->back()->with('error', 'Test Report upload failed. Please try again.');
                }
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $fileName = "{$timestamp}_{$originalName}.{$extension}";
                $destinationPath = public_path($test_report_upload_path);
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true);
                }
                $file->move($destinationPath, $fileName);
                $testfilePath = $test_report_upload_path . $fileName;
            }

            // Prepare data
            $data = [
                'project_no' => $request->project_no,
                'project_name' => $request->project_name,
                'serial_no' => $request->serial_no,
                'qty' => $request->qty,
                'unit_qty' => $request->unit_qty,
                'pump_type' => $request->pump_type,
                'product_image' => json_encode($imgfilePaths), // Store array of image paths as JSON
                'reports_docs' => $filePath ?? null,
                'test_reports_docs' => $testfilePath ?? null,
                'product_article_no' => $request->artical_no,
                'product_desc' => $request->description
            ];

            // Save data in final_inspection_table
            FinalInspectionTable::create($data);

            // Get product id from project no
            $project_id = Project::where('project_no', $request->project_no)->pluck('id')->first();

            // Update status in products_of_projects table
            ProductsOfProjects::where('article_number', $request->artical_no)
                ->where('project_id', $project_id)
                ->update([
                    'is_final_inspection_started' => 2,
                    'inbox_to_pro_superwisor_to_create_pl' => 1
                ]);

            $product_id = $request->id;
            $record = QtyOfProduct::where('project_id', $project_id)
                ->where('id', $product_id)
                ->first();

            if ($record) {
                $record->is_final_inspection_started = 2;
                $record->save();
            }

            return redirect()->route('QUALITY')->with('success', 'Final Inspection created successfully!');
        } catch (\Exception $e) {
            Log::error('File Upload Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'File upload failed: ' . $e->getMessage());
        }
    }

    public function addNCR(){
        $page_title = "Non-Conformance Report";
        $cia_no = $this->generateCIANumber();
        return view('quality_manager.add_ncr', compact('page_title', 'cia_no'));
    }

    private function generateCIANumber(){
        $currentYear = Carbon::now()->year;
        $lastNCR = NCR::whereYear('created_at', $currentYear)->orderBy('id', 'desc')->first();

        if ($lastNCR) {
            $lastCIANumber = $lastNCR->cia_no;
            $lastNumber = intval(substr($lastCIANumber, 4, 3));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'CIA-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT) . '-' . $currentYear;
    }

    public function generatePDF(Request $request){
        $validationRules = [
            'cia_no' => 'required',
            'related_dep' => 'required',
            'ncr_type' => 'required',
            'project_no' => 'required',
            'project' => 'required',
            'po' => 'required',
            'material_description' => 'required',
            'ncr_description' => 'required',
            'article_number' => 'required',
            'quantity' => 'required|numeric',
            'name_surname' => 'required',
            'signature' => 'required|image',
            'detected_department' => 'required',
            'activity_schedule_type' => 'required|array',
            'root_cause' => 'nullable',
            'action_to_prevent_misuse' => 'nullable',
            'planned_action_date' => 'nullable|date',
            'related_authorized_personnel' => 'nullable',
            'related_authorized_personnel_signature' => 'nullable|image',
            'quality_management_representative' => 'nullable',
            'action_follow_up' => 'nullable|in:Nonconformity is corrected,Nonconformity is not corrected,Additional Time',
            'corrective_preventive_action' => 'nullable',
            'follow_up' => 'nullable',
            'action_closed_date' => 'nullable|date',
            'related_authorized_personnel_final' => 'nullable',
            'quality_management_representative_date' => 'nullable',
            'ncr_photos' => 'nullable|array',
            'ncr_photos.*' => 'image|mimes:jpeg,png,jpg,gif',
        ];

        $validatedData = $request->validate($validationRules);
        try {
            $ncrData = $this->saveNCR($validatedData, $request);            

            // New logic to update estimated_readiness_added_NCR
            $project = Project::where('project_no', $validatedData['project_no'])->first();

            if ($project && is_null($project->estimated_readiness_added_NCR)) {
                // Fetch ncr_creation and ncr_closing_time from admin_hours_management
                $adminHours = AdminHoursManagement::whereIn('key', ['ncr_creation', 'ncr_closing_time'])
                    ->where('is_deleted', 0)
                    ->pluck('value', 'key');

                $ncrCreationHours = (int) $adminHours['ncr_creation'];
                $ncrClosingHours = (int) $adminHours['ncr_closing_time'];
                $totalHours = $ncrCreationHours + $ncrClosingHours - 1;

                // Get the estimated readiness date
                $estimatedReadiness = Carbon::parse($project->estimated_readiness);

                // Calculate the new estimated readiness added NCR date
                $newEstimatedDate = $this->addBusinessHours($estimatedReadiness, $totalHours);

                // Update the project
                $project->estimated_readiness_added_NCR = $newEstimatedDate;
                $project->save();
            }

            $signaturePath = public_path($ncrData['signature']);
            $signaturePath2 = public_path($ncrData['related_authorized_personnel_signature']);

            $ncrData['signature'] = $signaturePath;
            $ncrData['related_authorized_personnel_signature'] = $signaturePath2;
            $pdf = PDF::loadView('quality_manager.ncr_pdf', $ncrData)
                ->setOption('enable-local-file-access', true)
                ->setOption('images', true);

            $pdfDirectory = public_path('ncr_pdf');
            if (!file_exists($pdfDirectory)) {
                mkdir($pdfDirectory, 0755, true);
            }

            $filename = 'NCR-' . $ncrData['cia_no'] . '-' . time() . '.pdf';
            $pdfPath = $pdfDirectory . '/' . $filename;


            $pdf->save($pdfPath);

            if (!file_exists($pdfPath)) {
                throw new \Exception("Failed to save PDF file.");
            }
            $mainSignature = NCR::where('id');

            $pdfUrl = asset('ncr_pdf/' . $filename);
         
            return response()->json([
                'success' => true,
                'message' => 'NCR generated and saved successfully',
                'pdf_url' => $pdfUrl,
                'redirect_url' => route('NCR')
            ]);
        } catch (\Exception $e) {
            Log::error('NCR generation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate NCR: ' . $e->getMessage()
            ], 500);
        }
    }

    private function saveNCR($data, $request){
        $ncrData = [
            'cia_no' => $data['cia_no'],
            'related_dep' => $data['related_dep'],
            'ncr_type' => $data['ncr_type'],
            'project_no' => $data['project_no'],
            'project' => $data['project'],
            'po' => $data['po'],
            'material_description' => $data['material_description'],
            'ncr_description' => $data['ncr_description'],
            'article_number' => $data['article_number'],
            'quantity' => $data['quantity'],
            'name_surname' => $data['name_surname'],
            'detected_department' => $data['detected_department'],
            'activity_schedule_type' => $data['activity_schedule_type'],
            'root_cause' => $data['root_cause'],
            'action_to_prevent_misuse' => $data['action_to_prevent_misuse'],
            'planned_action_date' => $data['planned_action_date'],
            'related_authorized_personnel' => $data['related_authorized_personnel'],
            'quality_management_representative' => $data['quality_management_representative'],
            'corrective_preventive_action' => $data['corrective_preventive_action'],
            'follow_up' => $data['follow_up'],
            'action_closed_date' => $data['action_closed_date'],
            'related_authorized_personnel_final' => $data['related_authorized_personnel_final'],
            'quality_management_representative_date' => $data['quality_management_representative_date'],
            'is_nonconformity_corrected' => false,
            'is_nonconformity_not_corrected' => false,
            'is_additional_time' => false,
        ];

        // Handle signature upload
        if ($request->hasFile('signature')) {
            $signature = $request->file('signature');
            $signatureName = $data['cia_no'] . '-' . time() . '.' . $signature->getClientOriginalExtension();
            $signature->move(public_path('signatures/main'), $signatureName);
            $ncrData['signature'] = 'signatures/main/' . $signatureName;
        }

        // Handle related authorized personnel signature upload
        if ($request->hasFile('related_authorized_personnel_signature')) {
            $signature = $request->file('related_authorized_personnel_signature');
            $signatureName = $data['cia_no'] . '-' . time() . '.' . $signature->getClientOriginalExtension();
            $signature->move(public_path('signatures/related'), $signatureName);
            $ncrData['related_authorized_personnel_signature'] = 'signatures/related/' . $signatureName;
        }

        // Handle NCR photos upload
        $ncrPhotos = [];
        if ($request->hasFile('ncr_photos')) {
            foreach ($request->file('ncr_photos') as $photo) {
                $photoName = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                $photo->move(public_path('ncr_photos'), $photoName);
                $ncrPhotos[] = 'ncr_photos/' . $photoName;
            }
        }

        $ncrData['ncr_photos'] = json_encode($ncrPhotos);

        $ncr = NCR::create($ncrData);

        return $ncrData;
    }

    private function addBusinessHours($startDate, $hours){
        $currentDate = Carbon::parse($startDate);
        $totalHours = $hours;

        while ($totalHours > 0) {
            // Check if the current day is a weekday (Monday to Friday)
            if ($currentDate->isWeekday()) {
                $remainingHoursInDay = 8; // Assuming a standard 8-hour workday
                if ($totalHours >= $remainingHoursInDay) {
                    $totalHours -= $remainingHoursInDay;
                    $currentDate->addDay(); // Move to the next day
                } else {
                    $currentDate->addHours($totalHours); // Add remaining hours
                    $totalHours = 0; // Hours consumed
                }
            } else {
                // Skip weekends (Saturday and Sunday)
                $currentDate->addDay();
            }
        }
        return $currentDate->toDateTimeString();
    }

    public function getProjectName(Request $request){
        $projectNo = $request->query('project_no');
        $project = Project::where('project_no', $projectNo)->first();
        if ($project) {
            return response()->json(['project_name' => $project->project_name]);
        } else {
            return response()->json(['project_name' => null]);
        }
    }

    public function updateRemarks(Request $request){
        $request->validate([
            'id' => 'required|exists:ncr,id',
            'remark' => 'required|string'
        ]);
        $ncr = NCR::findOrFail($request->id);
        $ncr->timestamps = false;
        $ncr->remark = $request->remark;
        $ncr->save();
        return response()->json(['success' => true, 'message' => 'Remarks updated successfully']);
    }

    public function edit($id){
        $ncrData = NCR::findOrFail($id);
        if ($ncrData->is_nonconformity_corrected) {
            $ncrData->action_follow_up = 'Nonconformity is corrected';
        } elseif ($ncrData->is_nonconformity_not_corrected) {
            $ncrData->action_follow_up = 'Nonconformity is not corrected';
        } elseif ($ncrData->is_additional_time) {
            $ncrData->action_follow_up = 'Additional Time';
        } else {
            $ncrData->action_follow_up = null; // or set a default value
        }
        $page_title = "Edit Non-Conformance Report";
        return view('quality_manager.edit_ncr', compact('page_title', 'ncrData'));
    }

    public function update(Request $request, $id){
        $validationRules = [
            'cia_no' => 'required',
            'related_dep' => 'required',
            'ncr_type' => 'required',
            'project_no' => 'nullable',
            'project' => 'required',
            'po' => 'required',
            'material_description' => 'required',
            'ncr_description' => 'required',
            'article_number' => 'required',
            'quantity' => 'required|numeric',
            'name_surname' => 'required',
            'signature' => 'nullable|image',
            'detected_department' => 'required',
            'activity_schedule_type' => 'required|array',
            'root_cause' => 'nullable',
            'action_to_prevent_misuse' => 'nullable',
            'planned_action_date' => 'nullable|date',
            'related_authorized_personnel' => 'nullable',
            'related_authorized_personnel_signature' => 'nullable|image',
            'quality_management_representative' => 'nullable',
            'action_follow_up' => 'nullable|in:Nonconformity is corrected,Nonconformity is not corrected,Additional Time',
            'corrective_preventive_action' => 'nullable',
            'follow_up' => 'nullable',
            'action_closed_date' => 'nullable|date',
            'related_authorized_personnel_final' => 'nullable',
            'quality_management_representative_date' => 'nullable',
            'ncr_photos' => 'nullable|array',
            'ncr_photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'removed_photos' => 'nullable|array',
        ];

        // Validate the request data
        $validatedData = $request->validate($validationRules);
        try {
            $ncr = NCR::findOrFail($id);
            $ncrData = $this->updateNCR($ncr, $validatedData, $request);
            // Generate the new PDF
            $pdf = PDF::loadView('quality_manager.ncr_pdf', $ncrData)->setOption('enable-local-file-access', true);;
            // Save the PDF
            $pdfDirectory = public_path('ncr_pdf');
            if (!file_exists($pdfDirectory)) {
                mkdir($pdfDirectory, 0755, true);
            }
            $filename = 'NCR-' . $ncrData['cia_no'] . '-' . time() . '.pdf';
            $pdfPath = $pdfDirectory . '/' . $filename;
            $pdf->save($pdfPath);
            if (!file_exists($pdfPath)) {
                throw new \Exception("Failed to save PDF file.");
            }
            $pdfUrl = asset('ncr_pdf/' . $filename);
            // Return a success response with the new PDF URL
            return response()->json([
                'success' => true,
                'message' => 'NCR updated and PDF generated successfully',
                'pdf_url' => $pdfUrl,
                'redirect_url' => route('NCR')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update NCR: ' . $e->getMessage()
            ], 500);
        }
    }

    private function updateNCR($ncr, $data, $request){
        $ncrData = [
            'cia_no' => $data['cia_no'],
            'related_dep' => $data['related_dep'],
            'ncr_type' => $data['ncr_type'],
            'project_no' => $data['project_no'],
            'project' => $data['project'],
            'po' => $data['po'],
            'material_description' => $data['material_description'],
            'ncr_description' => $data['ncr_description'],
            'article_number' => $data['article_number'],
            'quantity' => $data['quantity'],
            'name_surname' => $data['name_surname'],
            'detected_department' => $data['detected_department'],
            'activity_schedule_type' => $data['activity_schedule_type'],
            'root_cause' => $data['root_cause'],
            'action_to_prevent_misuse' => $data['action_to_prevent_misuse'],
            'planned_action_date' => $data['planned_action_date'],
            'related_authorized_personnel' => $data['related_authorized_personnel'],
            'quality_management_representative' => $data['quality_management_representative'],
            'corrective_preventive_action' => $data['corrective_preventive_action'],
            'follow_up' => $data['follow_up'],
            'action_closed_date' => $data['action_closed_date'],
            'related_authorized_personnel_final' => $data['related_authorized_personnel_final'],
            'quality_management_representative_date' => $data['quality_management_representative_date'],
            'is_nonconformity_corrected' => false,
            'is_nonconformity_not_corrected' => false,
            'is_additional_time' => false,
        ];

        // Handle signature upload
        if ($request->hasFile('signature')) {
            if ($ncr->signature && file_exists(public_path($ncr->signature))) {
                unlink(public_path($ncr->signature));
            }
            $signature = $request->file('signature');
            $signatureName = $data['cia_no'] . '-' . time() . '.' . $signature->getClientOriginalExtension();
            $signature->move(public_path('signatures/main'), $signatureName);
            $ncrData['signature'] = 'signatures/main/' . $signatureName;
        }

        // Handle related authorized personnel signature upload
        if ($request->hasFile('related_authorized_personnel_signature')) {
            if ($ncr->related_authorized_personnel_signature && file_exists(public_path($ncr->related_authorized_personnel_signature))) {
                unlink(public_path($ncr->related_authorized_personnel_signature));
            }
            $signature = $request->file('related_authorized_personnel_signature');
            $signatureName = $data['cia_no'] . '-' . time() . '.' . $signature->getClientOriginalExtension();
            $signature->move(public_path('signatures/related'), $signatureName);
            $ncrData['related_authorized_personnel_signature'] = 'signatures/related/' . $signatureName;
        }

        // Handle NCR photos upload
        $ncrPhotos = json_decode($ncr->ncr_photos, true) ?? [];
        if ($request->hasFile('ncr_photos')) {
            foreach ($request->file('ncr_photos') as $photo) {
                $photoName = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                $photo->move(public_path('ncr_photos'), $photoName);
                $ncrPhotos[] = 'ncr_photos/' . $photoName;
            }
        }

        // Handle removed photos
        if ($request->has('removed_photos')) {
            foreach ($request->input('removed_photos') as $removedPhoto) {
                if (($key = array_search($removedPhoto, $ncrPhotos)) !== false) {
                    unset($ncrPhotos[$key]);
                    if (file_exists(public_path($removedPhoto))) {
                        unlink(public_path($removedPhoto));
                    }
                }
            }
        }

        $ncrData['ncr_photos'] = json_encode(array_values($ncrPhotos));
        $ncr->update($ncrData);
        // Prepare response data
        $ncrData['ncr_photos'] = array_map(function ($photo) {
            return url($photo);
        }, json_decode($ncrData['ncr_photos'], true));
        return $ncrData;
    }

    public function deleteImage(Request $request){
        try {
            // Retrieve inspection_id and image from the request body
            $inspection_id = $request->input('inspection_id');
            $image = $request->input('image');
            // Validate the inputs
            if (!$inspection_id || !$image) {
                return response()->json([
                    'success' => false,
                    'message' => 'Inspection ID and image are required.'
                ], 400);
            }
            // Find the final inspection record
            $inspection = FinalInspectionTable::findOrFail($inspection_id);
            // Decode the product_image JSON
            $images = json_decode($inspection->product_image, true);
            if ($images && is_array($images)) {
                // Find the image path
                $imagePath = null;
                foreach ($images as $img) {
                    if ($img === $image) { // Compare the full path since $image includes the path
                        $imagePath = $img;
                        break;
                    }
                }
                if ($imagePath) {
                    // Remove the image from the array
                    $images = array_filter($images, function ($img) use ($image) {
                        return $img !== $image;
                    });
                    // Update the product_image field
                    $inspection->product_image = json_encode(array_values($images));
                    $inspection->save();

                    // Delete the image file from storage
                    $fullPath = public_path($imagePath);
                    if (File::exists($fullPath)) {
                        File::delete($fullPath);
                    }

                    return response()->json([
                        'success' => true,
                        'message' => 'Image deleted successfully.'
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Image not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete image: ' . $e->getMessage()
            ], 500);
        }
    }

    public function uploadInspectionImages(Request $request){
        try {
            // Validate the request
            $request->validate([
                'inspection_id' => 'required|exists:final_inspection_data,id',
                'images.*' => 'required|image|mimes:jpeg,jpg,png,webp',
            ]);

            $inspectionId = $request->input('inspection_id');
            $inspection = FinalInspectionTable::findOrFail($inspectionId);

            // Get project_no, article_no, and unit_qty from the inspection record
            $projectNo = $inspection->project_no;
            $articleNo = $inspection->product_article_no;
            $unitQty = $inspection->unit_qty;

            // If project_no is 'N/A' or null, use a default value
            if ($projectNo == 'N/A' || is_null($projectNo)) {
                $projectNo = '24-0';
            }

            // Get existing images
            $existingImages = json_decode($inspection->product_image, true) ?? [];

            // Handle uploaded images
            $newImages = [];
            if ($request->hasFile('images')) {
                // Define the base upload path
                $timestamp = now()->format('Ymd_His');
                $imagesUploadPath = "project_document/{$projectNo}/Project Execution/images/{$articleNo}/{$unitQty}/";

                foreach ($request->file('images') as $image) {
                    // Validate the image file
                    if (!$image->isValid()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid image file.'
                        ], 400);
                    }

                    // Generate the filename with timestamp and original name
                    $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $image->getClientOriginalExtension();
                    $fileName = "{$timestamp}_image_{$originalName}.{$extension}";

                    // Construct the full destination path
                    $destinationPath = public_path($imagesUploadPath);
                    if (!File::exists($destinationPath)) {
                        File::makeDirectory($destinationPath, 0755, true);
                    }

                    // Move the image to the destination path
                    $image->move($destinationPath, $fileName);

                    // Add the full path to the list of images
                    $fullPath = $imagesUploadPath . $fileName;
                    $existingImages[] = $fullPath;
                    $newImages[] = [
                        'url' => asset($fullPath),
                        'name' => basename($fullPath)
                    ];
                }
            }

            // Update the inspection record with the new images
            $inspection->product_image = json_encode($existingImages);
            $inspection->save();

            return response()->json([
                'success' => true,
                'message' => 'Images uploaded successfully.',
                'images' => $newImages
            ]);
        } catch (\Exception $e) {
            Log::error('Image Upload Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload images: ' . $e->getMessage()
            ], 500);
        }
    }

    public function uploadFinalReport(Request $request){
        $request->validate([
            'inspection_id' => 'required|exists:final_inspection_data,id',
            'reports_docs' => 'required|file|mimes:doc,docx',
        ]);

        try {
            $inspection = FinalInspectionTable::findOrFail($request->inspection_id);
            $projectNo = $inspection->project_no;
            $poNumber = $inspection->product_article_no;
            $unitQty = $inspection->unit_qty;
            $file = $request->file('reports_docs');

            // Generate filename with timestamp
            $timestamp = now()->format('Ymd_His');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $fileName = "{$timestamp}_{$originalName}.{$extension}";

            // Determine the project folder
            $projectFolder = ($projectNo == 'N/A' || is_null($projectNo)) ? '24-0' : $projectNo;

            // Construct the destination path
            $basePath = public_path('project_document');
            $destinationPath = $basePath . '/' . $projectFolder . '/Quality/Final Inspection/' . $poNumber . '/' . $unitQty;

            // Create the directory if it doesn't exist
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            // Delete existing report file if it exists
            if ($inspection->reports_docs && File::exists(public_path($inspection->reports_docs))) {
                File::delete(public_path($inspection->reports_docs));
            }

            // Move the uploaded file to the destination path
            $filePath = 'project_document/' . $projectFolder . '/Quality/Final Inspection/' . $poNumber . '/' . $unitQty . '/' . $fileName;
            $file->move($destinationPath, $fileName);

            // Update the inspection record with the new file path
            $inspection->reports_docs = $filePath;
            $inspection->save();

            return redirect()->back()->with('success', 'Final Inspection report uploaded successfully.');
        } catch (\Exception $e) {
            Log::error('Final Report Upload Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to upload Final Inspection report: ' . $e->getMessage());
        }
    }

    public function uploadTestReport(Request $request){
        $request->validate([
            'inspection_id' => 'required|exists:final_inspection_data,id',
            'test_reports_docs' => 'required|file|mimes:doc,docx',
        ]);

        try {
            $inspection = FinalInspectionTable::findOrFail($request->inspection_id);
            $projectNo = $inspection->project_no;
            $poNumber = $inspection->product_article_no;
            $unitQty = $inspection->unit_qty;
            $file = $request->file('test_reports_docs');

            // Generate filename with timestamp
            $timestamp = now()->format('Ymd_His');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $fileName = "{$timestamp}_{$originalName}.{$extension}";

            // Determine the project folder
            $projectFolder = ($projectNo == 'N/A' || is_null($projectNo)) ? '24-0' : $projectNo;

            // Construct the destination path
            $basePath = public_path('project_document');
            $destinationPath = $basePath . '/' . $projectFolder . '/Quality/Test Reports/' . $poNumber . '/' . $unitQty;

            // Create the directory if it doesn't exist
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            // Delete existing test report file if it exists
            if ($inspection->test_reports_docs && File::exists(public_path($inspection->test_reports_docs))) {
                File::delete(public_path($inspection->test_reports_docs));
            }

            // Move the uploaded file to the destination path
            $filePath = 'project_document/' . $projectFolder . '/Quality/Test Reports/' . $poNumber . '/' . $unitQty . '/' . $fileName;
            $file->move($destinationPath, $fileName);

            // Update the inspection record with the new file path
            $inspection->test_reports_docs = $filePath;
            $inspection->save();

            return redirect()->back()->with('success', 'Test report uploaded successfully.');
        } catch (\Exception $e) {
            Log::error('Test Report Upload Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to upload Test report: ' . $e->getMessage());
        }
    }
    
    private function calculateInspectionDeadline($startDate, $hours){
        $currentDate = Carbon::parse($startDate);
        // Add hours directly (24 hours = 1 day, 48 hours = 2 days, etc.)
        $currentDate->addHours($hours);

        // Skip weekends (Saturday or Sunday)
        while ($currentDate->isSaturday() || $currentDate->isSunday()) {
            $currentDate->addDay();
        }
        return $currentDate;
    }
}