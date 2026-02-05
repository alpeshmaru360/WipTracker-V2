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
use App\Models\StockBOMPo;
use App\Models\StockMasterModule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Log;

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

        $all_projects_full = ProductsOfProjects::with('projects')->with('operator')->orderBy('id', 'desc')->where('delivery', '1')->get();
        $all_projects_partials = ProductsOfProjects::with('projects')->with('operator')->orderBy('id', 'desc')->where('delivery', '2')->get();

        return view('production_superwisor.inbox', compact(
            'page_title',
            'operators',
            'all_projects_full',
            'all_projects_partials',
            'pdf_req',
            'upload_pl_req',
            'completed_projects',
            'completed_process_of_assembly_products_qty_wise',
            'upload_pl_partial_dilivery_req',
            'pendingNameplateProductCreationAsPerQtyWise'
        ));
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
            'PL_PDF_path' => $project->PL_PDF_path,
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
                Log::info("FINAL PATH BEFORE MAIL: " . $fullPLFilePath);
                Log::info("EXISTS? " . (file_exists($fullPLFilePath) ? 'YES' : 'NO'));

                Mail::to($sendEmail->email)
                    ->send(new WItrackProjectCompleteNotifyProductionTeam($emailData, $fullPLFilePath));

            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to send completion email'
                ], 500);
            }
        }

        // Send Witrack API request if witrack_no exists
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

        // Get full path for email attachment
        $fullPLFilePath = public_path($fullPath);

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
            // Send full order email to production team & also API to wiTrack project

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
                    Mail::to($sendEmail->email)
                        ->send(new WItrackProjectFullCompleteNotifyProductionTeam($emailData, $fullPLFilePath));
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

        // Get full path for email attachment
        $fullPLFilePath = public_path($fullPath);

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
            Log::info("uploadPartialPlDoc isProjectFullyUploaded: " . $fullPLFilePath);
            $this->handleProjectCompletion($project, $fullPLFilePath);
        } else {
            Log::info("uploadPartialPlDoc Not isProjectFullyUploaded: " . $fullPLFilePath);
            // Send partial order email to production team & also API to wiTrack project
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
                    Mail::to($sendEmail->email)
                        ->send(new WItrackProjectPartialCompleteNotifyProductionTeam($emailData, $fullPLFilePath));
                } catch (\Exception $e) {
                }
            }
        }
        return response()->json(['success' => true, 'message' => 'File uploaded successfully!', 'file_path' => $fullPath]);
    }
}