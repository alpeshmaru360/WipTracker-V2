<?php

namespace App\Http\Controllers\ProductionManager;
//test
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\StockMasterModule;
use App\Models\ProjectStatus;
use App\Models\Project;
use App\Models\AdminSetting;
use App\Models\CurrencyConverter;
use App\Models\ProductType;
use App\Models\AdminHoursManagement;
use Illuminate\Support\Facades\Validator;
use App\Models\QtyOfProduct;
use App\Models\ProductsOfProjects;
use App\Models\ProjectProcessStdTime;
use App\Models\PurchaseOrder;
use App\Models\ProcurementStandardTime;
use App\Models\PurchaseOrderTable;
use App\Models\ProductBOMItem;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Mail\FinancePersonCreateProject;
use App\Mail\ProjectRejectedNotification;
use Illuminate\Support\Facades\Mail;
use App\Helpers\helper;
use File;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;

class ProductionManagerController extends Controller
{
    public function dashboard(DashboardService $dashboardService)
    {
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

    public function generateAndDownloadQRCode($projectId, $projectName)
    {
        $qrContent = route('QRPage', ['product_id' => $projectId, 'redirect' => '0']);
        $fileName = $projectName . '_QrCode.png';
        $filePath = public_path($fileName);
        QrCode::size(400)->margin(5)->format('png')->merge('/public/sales_manager/uploads/logo/wilo_logo.png', 0.4)->generate($qrContent, $filePath);
        $qrImage = imagecreatefrompng($filePath);
        $text = $projectName;
        $fontSize = strlen($text) > 25 ? 16 : 20;
        $textColor = imagecolorallocate($qrImage, 0, 0, 0);
        $fontPath = public_path('fassets/fonts/WiloPlusGlobalBold.woff');
        $textBox = imagettfbbox($fontSize, 0, $fontPath, $text);
        $textWidth = $textBox[2] - $textBox[0];
        $textX = (imagesx($qrImage) - $textWidth) / 2;
        $textY = imagesy($qrImage) - 20;

        imagettftext($qrImage, $fontSize, 0, $textX, $textY, $textColor, $fontPath, $text);
        imagepng($qrImage, $filePath);
        imagedestroy($qrImage);
        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function index(Request $request)
    {
        $page_title = "PROJECT STATUS";

        $query = Project::select('*')
            ->orderByRaw("CAST(SUBSTRING_INDEX(project_no, '-', 1) AS UNSIGNED) DESC")
            ->orderByRaw("CAST(SUBSTRING_INDEX(project_no, '-', -1) AS UNSIGNED) DESC");

        if ($request->filled('status') && $request->status != "3") {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority') && $request->priority != "2") {
            $query->where('is_priotize', $request->priority);
        }
        $projectfilter = (object) [];
        $last_filter_column = $request->input('last_filter_column');
        if (isset($last_filter_column)) {
            $projectfilter = $query->with('product')->whereNotNull('assembly_quotation_ref')->where('is_deleted', 0)->get(); // A Code: 26-12-2025
        }
        $filters = $request->input('filters', []);
        // Map filter keys to column names
        $filterableColumns = [
            'filter_col_0' => 'status',
            'filter_col_1' => 'wip_project_create_date',
            'filter_col_2' => 'customer_ref',
            'filter_col_10' => 'sales_order_number',
            'filter_col_3' => 'project_no',
            'filter_col_4' => 'project_name',
            'filter_col_5' => 'country',
            'filter_col_6' => 'customer_name',
            'filter_col_7' => 'sales_name',
            'filter_col_8' => 'estimated_readiness',
        ];

        foreach ($filters as $key => $values) {
            $values = array_filter((array) $values); // Convert and skip empty values
            if (empty($values))
                continue;

            switch ($key) {
                case 'filter_col_1': // wip_project_create_date
                case 'filter_col_8': // estimated_readiness
                    $column = $filterableColumns[$key] ?? null;
                    if ($column) {
                        $query->where(function ($q) use ($values, $column) {
                            foreach ($values as $val) {
                                try {
                                    // Special case: filter only by DATE part of datetime field
                                    $date = \Carbon\Carbon::parse($val)->toDateString();
                                    $q->orWhereDate($column, $date);
                                } catch (\Exception $e) {
                                    // Invalid date, skip
                                }
                            }
                        });
                    }
                    break;

                case 'filter_col_9': // Special readiness logic
                    $query->where(function ($q) use ($values) {
                        foreach ($values as $val) {
                            try {
                                // Special case: filter only by DATE part of datetime field
                                $date = \Carbon\Carbon::parse($val)->toDateString();
                                $q->orWhere(function ($sub) use ($date) {
                                    $sub->whereDate('actual_readiness', $date)
                                        ->orWhere(function ($q2) use ($date) {
                                            $q2->whereNull('actual_readiness')
                                                ->whereDate('estimated_readiness', $date);
                                        });
                                });
                            } catch (\Exception $e) {
                                // Invalid date, skip
                            }
                        }
                    });
                    break;

                default:
                    if (isset($filterableColumns[$key])) {
                        $column = $filterableColumns[$key];
                        $query->whereIn($column, $values);
                    }
                    break;
            }
        }
        // Final query
        $project = $query->with('product')->whereNotNull('assembly_quotation_ref')->where('is_deleted', 0)->get(); // A Code: 26-12-2025
        // AJAX response
        if ($request->ajax()) {
            $rowView = view('production_manager.project_rows', compact('project'))->render();
            $headView = view('production_manager.project_head', compact('project', 'filters', 'projectfilter', 'last_filter_column'))->render();

            $project_numbers = $project->pluck('project_no')
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

        // Non-AJAX: return full view
        return view('production_manager.index', compact('page_title', 'project', 'filters', 'projectfilter', 'last_filter_column'));
    }

    public function getProjectExecutionImageList(Request $request, $projectId)
    {
        $bomData = ProductsOfProjects::where('project_id', $projectId)
            ->select('cart_model_name', 'description', 'full_article_number', 'qty')
            ->get();
        return response()->json(['imageList' => $bomData]);
    }

    public function getProjectExecutionImages(Request $request, $projectId, $articleNumber, $qtyNo)
    {
        $project = Project::find($projectId);
        if (!$project) {
            return response()->json(['success' => false, 'message' => 'Project not found'], 404);
        }
        $path = public_path("project_document/{$project->project_no}/Project Execution/images/{$articleNumber}/{$qtyNo}");
        $images = [];

        if (File::exists($path)) {
            $files = File::files($path);
            foreach ($files as $file) {
                $images[] = [
                    'name' => $file->getFilename(),
                    'path' => "project_document/{$project->project_no}/Project Execution/images/{$articleNumber}/{$qtyNo}/{$file->getFilename()}"
                ];
            }
        }
        return response()->json([
            'success' => true,
            'images' => $images
        ]);
    }

    public function uploadProjectExecutionImage(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'article_number' => 'required|string',
            'qty_no' => 'required|integer|min:1',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120'
        ]);

        $project = Project::find($request->project_id);
        $articleNumber = $request->article_number;
        $qtyNo = $request->qty_no;
        $directory = public_path("project_document/{$project->project_no}/Project Execution/images/{$articleNumber}/{$qtyNo}");
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0777, true);
        }
        $uploadedFiles = [];
        $timestamp = Carbon::now()->format('Ymd_His');

        foreach ($request->file('images') as $index => $image) {
            $fileName = "{$timestamp}_{$index}_" . $image->getClientOriginalName();
            $image->move($directory, $fileName);

            $uploadedFiles[] = [
                'name' => $fileName,
                'path' => "project_document/{$project->project_no}/Project Execution/images/{$articleNumber}/{$qtyNo}/{$fileName}"
            ];
            $uploadedFinalInspectionimages[] = "project_document/{$project->project_no}/Project Execution/images/{$articleNumber}/{$qtyNo}/{$fileName}";
        }
        // Get existing product_image data
        $existing = DB::table('final_inspection_data')
            ->where('project_no', $project->project_no)
            ->where('unit_qty', $qtyNo)
            ->where('product_article_no', $articleNumber)
            ->value('product_image');
        $existingImages = $existing ? json_decode($existing, true) : [];
        // Merge arrays (append new images)
        $merged = array_merge($existingImages, $uploadedFinalInspectionimages);
        // Update final_inspection_data
        DB::table('final_inspection_data')
            ->where('project_no', $project->project_no)
            ->where('unit_qty', $qtyNo)
            ->where('product_article_no', $articleNumber)
            ->update(['product_image' => json_encode($merged)]);

        return response()->json([
            'success' => true,
            'message' => count($uploadedFiles) . ' image(s) uploaded successfully',
            'files' => $uploadedFiles
        ]);
    }

    public function deleteProjectExecutionImage(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'file_path' => 'required|string'
        ]);
        $project = Project::find($request->project_id);
        $relativePath = $request->file_path;
        $filePath = public_path($relativePath);

        // 1. Delete the file from disk
        if (File::exists($filePath)) {
            File::delete($filePath);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Image file not found on server.'
            ], 404);
        }

        // 2. Extract article_number and qty_no from file_path
        // Example path: project_document/24-27/Project Execution/images/68318403/2/20251027_150022_img.png
        $segments = explode('/', str_replace('\\', '/', $relativePath));

        $projectNo = $segments[1] ?? null; // e.g. 24-27
        $articleNumber = $segments[4] ?? null; // e.g. 68318403
        $qtyNo = $segments[5] ?? null; // e.g. 1

        if (!$projectNo || !$articleNumber || !$qtyNo) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid file path structure.'
            ], 422);
        }

        // 3. Remove from final_inspection_data.product_image
        $existing = DB::table('final_inspection_data')
            ->where('project_no', $projectNo)
            ->where('unit_qty', $qtyNo)
            ->where('product_article_no', $articleNumber)
            ->value('product_image');

        if ($existing) {
            $existingImages = json_decode($existing, true);

            $filtered = array_filter($existingImages, function ($item) use ($relativePath) {
                if (is_array($item) && isset($item['path'])) {
                    return $item['path'] !== $relativePath;
                }
                return $item !== $relativePath;
            });

            DB::table('final_inspection_data')
                ->where('project_no', $projectNo)
                ->where('unit_qty', $qtyNo)
                ->where('product_article_no', $articleNumber)
                ->update(['product_image' => json_encode(array_values($filtered))]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully and database updated.'
        ]);
    }

    public function getProjectExecutionWorkOrdersList(Request $request, $projectId)
    {
        $workOrdersData = ProductsOfProjects::where('project_id', $projectId)
            ->select('cart_model_name', 'description', 'full_article_number')
            ->get();

        return response()->json(['workOrdersList' => $workOrdersData]);
    }

    // Project Execution Documents (Full_Order_PL, Work Orders)
    public function getProjectExecutionDocs(Request $request, $projectId, $type, $articleNumber = null)
    {
        $validTypes = ['Full_Order_PL', 'Work Orders'];
        if (!in_array($type, $validTypes)) {
            return response()->json(['success' => false, 'message' => 'Invalid document type'], 400);
        }

        $project = Project::find($projectId);
        if (!$project) {
            return response()->json(['success' => false, 'message' => 'Project not found'], 404);
        }

        $directory = ($type === 'Work Orders') && $articleNumber
            ? public_path("project_document/{$project->project_no}/Project Execution/{$type}/{$articleNumber}")
            : public_path("project_document/{$project->project_no}/Project Execution/{$type}");
        $files = [];

        if (File::exists($directory)) {
            $allFiles = File::files($directory);
            foreach ($allFiles as $file) {
                $files[] = [
                    'name' => $file->getFilename(),
                    'path' => ($type === 'Work Orders') && $articleNumber
                        ? "project_document/{$project->project_no}/Project Execution/{$type}/{$articleNumber}/{$file->getFilename()}"
                        : "project_document/{$project->project_no}/Project Execution/{$type}/{$file->getFilename()}",
                    'size' => $file->getSize(),
                    'created_at' => date('Y-m-d H:i:s', $file->getCTime())
                ];
            }
        }

        return response()->json([
            'success' => true,
            'documents' => $files,
            'project_no' => $project->project_no,
            'type' => $type
        ]);
    }

    public function uploadProjectExecutionDoc(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'type' => 'required|in:Full_Order_PL,Work Orders',
            'article_number' => 'required_if:type,Work Orders|string',
            'document' => 'required|mimes:pdf,doc,docx,xls,xlsx|max:10240'
        ]);

        $project = Project::find($request->project_id);
        $type = $request->type;
        $articleNumber = ($type === 'Work Orders') ? $request->article_number : null;
        $directory = $articleNumber
            ? public_path("project_document/{$project->project_no}/Project Execution/{$type}/{$articleNumber}")
            : public_path("project_document/{$project->project_no}/Project Execution/{$type}");

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0777, true);
        }

        $timestamp = Carbon::now()->format('Ymd_His');
        $fileName = "{$timestamp}_" . $request->file('document')->getClientOriginalName();
        $request->file('document')->move($directory, $fileName);

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded successfully',
            'file' => [
                'name' => $fileName,
                'path' => $articleNumber
                    ? "project_document/{$project->project_no}/Project Execution/{$type}/{$articleNumber}/{$fileName}"
                    : "project_document/{$project->project_no}/Project Execution/{$type}/{$fileName}"
            ]
        ]);
    }

    public function deleteProjectExecutionDoc(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'file_path' => 'required|string'
        ]);
        $filePath = public_path($request->file_path);
        if (File::exists($filePath)) {
            File::delete($filePath);
            return response()->json([
                'success' => true,
                'message' => 'Document deleted successfully'
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Document not found'
        ], 404);
    }

    // Quality Documents Methods
    public function getQualityDocs(Request $request, $projectId, $type, $articleNumber = null, $qtyNo = null)
    {
        $validTypes = ['Final Inspection', 'Incoming Inspection', 'Test Reports'];
        if (!in_array($type, $validTypes)) {
            return response()->json(['success' => false, 'message' => 'Invalid document type'], 400);
        }
        $project = Project::find($projectId);
        if (!$project) {
            return response()->json(['success' => false, 'message' => 'Project not found'], 404);
        }
        // Determine directory path based on type
        if ($type === 'Incoming Inspection') {
            // Incoming Inspection uses PO number, handled separately
            return response()->json(['success' => false, 'message' => 'Use getIncomingInspectionDocs for Incoming Inspection'], 400);
        } else {
            // Final Inspection and Test Reports require articleNumber and qtyNo
            if (!$articleNumber || !$qtyNo) {
                return response()->json(['success' => false, 'message' => 'Article number and quantity number are required for this type'], 400);
            }
            $directory = public_path("project_document/{$project->project_no}/Quality/{$type}/{$articleNumber}/{$qtyNo}");
        }

        $files = [];

        if (File::exists($directory)) {
            $allFiles = File::files($directory);
            foreach ($allFiles as $file) {
                $files[] = [
                    'name' => $file->getFilename(),
                    'path' => "project_document/{$project->project_no}/Quality/{$type}/{$articleNumber}/{$qtyNo}/{$file->getFilename()}",
                    'size' => $file->getSize(),
                    'created_at' => date('Y-m-d H:i:s', $file->getCTime())
                ];
            }
        }

        return response()->json([
            'success' => true,
            'documents' => $files,
            'project_no' => $project->project_no,
            'type' => $type
        ]);
    }

    public function getProjectById($id)
    {
        $project = Project::find($id);
        if ($project) {
            return response()->json(['success' => true, 'project' => $project]);
        }
        return response()->json(['success' => false, 'message' => 'Project not found'], 404);
    }

    public function uploadQualityDoc(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'type' => 'required|in:Final Inspection,Incoming Inspection,Test Reports',
            'document' => 'required|mimes:pdf,doc,docx,xls,xlsx',
            'article_number' => 'required_if:type,Final Inspection,Test Reports|string',
            'qty_no' => 'required_if:type,Final Inspection,Test Reports|integer|min:1'
        ]);

        $project = Project::find($request->project_id);
        if (!$project) {
            return response()->json(['success' => false, 'message' => 'Project not found'], 404);
        }

        $type = $request->type;
        $directory = '';

        if ($type === 'Incoming Inspection') {
            // Incoming Inspection uses PO number, handled separately
            return response()->json(['success' => false, 'message' => 'Use dedicated endpoint for Incoming Inspection uploads'], 400);
        } else {
            // Final Inspection and Test Reports
            $articleNumber = $request->article_number;
            $qtyNo = $request->qty_no;
            $directory = public_path("project_document/{$project->project_no}/Quality/{$type}/{$articleNumber}/{$qtyNo}");
        }

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0777, true);
        }

        $timestamp = Carbon::now()->format('Ymd_His');
        $fileName = "{$timestamp}_" . $request->file('document')->getClientOriginalName();
        $request->file('document')->move($directory, $fileName);

        $filePath = "project_document/{$project->project_no}/Quality/{$type}/{$articleNumber}/{$qtyNo}/{$fileName}";

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded successfully',
            'file' => [
                'name' => $fileName,
                'path' => $filePath
            ]
        ]);
    }

    public function deleteQualityDoc(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'file_path' => 'required|string'
        ]);

        $filePath = public_path($request->file_path);

        if (File::exists($filePath)) {
            File::delete($filePath);
            return response()->json([
                'success' => true,
                'message' => 'Document deleted successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Document not found'
        ], 404);
    }

    public function getIncomingInspectionPOs(Request $request)
    {
        $projectId = $request->input('id');
        $project = Project::find($projectId);

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found'
            ], 404);
        }
        // Fetch all purchase orders for the project
        $purchaseOrders = PurchaseOrder::where('project_no', $project->project_no)
            ->select('po_number')
            ->get();

        if ($purchaseOrders->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No purchase orders found'
            ]);
        }
        return response()->json([
            'success' => true,
            'purchaseOrders' => $purchaseOrders
        ]);
    }

    public function getIncomingInspectionDocs(Request $request)
    {
        $projectId = $request->input('id');
        $poNumber = $request->input('po_number');
        $project = Project::find($projectId);

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found'
            ], 404);
        }

        $directory = public_path("project_document/{$project->project_no}/Quality/Incoming Inspection/{$poNumber}");
        $documents = [];

        if (File::exists($directory)) {
            $files = File::files($directory);
            foreach ($files as $file) {
                $documents[] = [
                    'name' => $file->getFilename(),
                    'path' => "project_document/{$project->project_no}/Quality/Incoming Inspection/{$poNumber}/{$file->getFilename()}"
                ];
            }
        }

        return response()->json([
            'success' => true,
            'documents' => $documents
        ]);
    }

    public function deleteIncomingInspectionDoc(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'file_path' => 'required|string'
        ]);
        $filePath = public_path($request->file_path);
        if (File::exists($filePath)) {
            File::delete($filePath);
            return response()->json([
                'success' => true,
                'message' => 'Document deleted successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Document not found'
        ], 404);
    }

    public function getProjectsBOM(Request $request)
    {
        $projectId = $request->projectId;
        $bomData = ProductsOfProjects::where('project_id', $projectId)
            ->select('full_article_number', 'description', 'cart_model_name', 'project_id', 'bom_path', 'bom_req_estimation_manager')
            ->get();
        $allProductsBOM = json_decode($bomData, true);

        return response()->json([
            'allProductsBOM' => $allProductsBOM,
        ]);
    }

    public function downloadBOM($projectId)
    {
        $bomData = ProductsOfProjects::where('project_id', $projectId)
            ->select('full_article_number', 'description', 'cart_model_name', 'project_id', 'bom_path', 'bom_req_estimation_manager', 'quantity')
            ->get();

        return Excel::download(new BOMExport($bomData), 'BOM-' . $projectId . '.xlsx');
    }

    private function fetchBOM($item_id, $item_name)
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://wilo.360websitedemo.com/api/getBOM',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => http_build_query(['item_id' => $item_id, 'item_name' => $item_name]),
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded']
        ]);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public function project_create_form(Request $request)
    {
        $project_name = $request->project_name;
        $product_type = ProductType::where('is_active', 1)->whereNotNull('project_type_name')->get(); // Exclude null project_type_name

        // Debug the product_type data
        if ($product_type->isEmpty()) {
        }

        $id = $request->query('id');
        $customer_ref_no = $request->query('customer_ref_no');
        $witrack_no = $request->query('witrack_no');
        $project_no = $request->query('project_no');
        $project_name_value = $request->query('internal_project_name');
        $country_name = $request->query('country_name');
        $customer_name = $request->query('customer_name');
        $sales_name = $request->query('sales_name');

        $docs = $request->query('docs');
        $documents = json_decode($docs);

        if ($project_name == "wi_track") {
            return view('production_manager.create_project', compact(
                'product_type',
                'project_name',
                'id',
                'customer_ref_no',
                'witrack_no',
                'project_no',
                'project_name_value',
                'country_name',
                'customer_name',
                'sales_name',
                'docs',
                'documents'
            ));
        } else {
            return view('production_manager.create_project', compact('product_type', 'project_name'));
        }
    }

    public function project_create(Request $request)
    {
        $main_project_name = $request->main_pro_name;
        $auth_id = "0";
        if (Auth::check()) {
            $auth_id = Auth::user()->id;
        }

        $admin_setting = AdminSetting::where('key', 'project_number_prefix')->first();
        $project_number_prefix = $admin_setting->value ?? '24-';
        $existing_count = Project::where('project_no', 'like', $project_number_prefix . '%')->count();
        $next_project_number = $existing_count + 1;
        $project_no = $project_number_prefix . $next_project_number;

        if ($main_project_name == "wip_tracker") {
            $project = new Project;
            $project->project_name = $request->project_name;
            $project->country = $request->country_name;
            $project->customer_name = $request->customer_name;
            $project->article_no = rand(1000000, 99999999);
            $project->sales_name = $request->sales_person;
            $project->customer_ref = $request->customer_ref;

            if ($request->hasFile('customer_documents')) {
                $filePaths = [];
                $baseDir = public_path("project_document/customer_documents");
                if (!File::exists($baseDir)) {
                    File::makeDirectory($baseDir, 0777, true);
                }
                foreach ($request->file('customer_documents') as $file) {
                    $fileName = time() . '-' . Str::random(2) . '-' . $file->getClientOriginalName();
                    $file->move($baseDir, $fileName);
                    $filePaths[] = "project_document/customer_documents/{$fileName}";
                }
                $project->documents = json_encode($filePaths);
            } else {
                $project->documents = json_encode([]);
            }
        } else {
            $project = Project::find($request->id);
        }

        $project->project_no = $project_no;
        $project->assembly_quotation_ref = $request->assembly_quotation_ref;
        $project->wip_project_create_date = now();
        $project->is_created_by = $auth_id;

        if ($request->has('quotation_items')) {
            if ($request->quotation_items[0]['quotation_from_pricing_tool'] == 1) {
                $project->is_pricing_tool_quotation_number = "1";
            }
        }

        $project->save();
        $projects_id = $project->id;

        if ($request->has('quotation_items')) {
            // Collect all product types from quotation items
            $allProductTypes = [];
            $anyDrawingRequested = false;

            foreach ($request->quotation_items as $val_projects) {
                $product_type = $val_projects['product_type'];
                $qty = $val_projects['qty'];

                // Collect product types
                if (!in_array($product_type, $allProductTypes)) {
                    $allProductTypes[] = $product_type;
                }

                // Check if any drawing is requested
                if (isset($val_projects['download_excel_drawing']) && $val_projects['download_excel_drawing'] == 1) {
                    $anyDrawingRequested = true;
                }

                // Fetch product type keywords for procurement
                $product_type_keyword = ProcurementStandardTime::with('product_type_name')
                    ->whereHas('product_type_name', function ($query) use ($product_type) {
                        $query->where('project_type_name', '=', $product_type);
                    })
                    ->get()
                    ->map(function ($item) {
                        return [
                            'project_type_name' => $item->product_type_name->project_type_name ?? '',
                            'keyword' => $item->keyword ?? '',
                            'value' => $item->total_days ?? 0,
                            'cart_model_name' => $item->cart_model_name ?? '',
                        ];
                    });

                $keywords_by_cart_model = $product_type_keyword
                    ->groupBy('cart_model_name')
                    ->map(function ($group) {
                        return $group->map(function ($item) {
                            return [
                                'keyword' => $item['keyword'],
                                'total_days' => $item['value'],
                            ];
                        })->toArray();
                    })->toArray();

                // Save product details
                $projectProcessStdTime = new ProductsOfProjects;
                $projectProcessStdTime->project_id = $projects_id;
                $projectProcessStdTime->quotation_number = $request->assembly_quotation_ref;
                $projectProcessStdTime->quotation_from_pricing_tool = $request->quotation_items[0]['quotation_from_pricing_tool'];
                $projectProcessStdTime->article_number = $val_projects['full_article_number'];
                $projectProcessStdTime->full_article_number = $val_projects['full_article_number'];
                $projectProcessStdTime->description = $val_projects['description'];
                $projectProcessStdTime->qty = $qty;
                $projectProcessStdTime->cart_model_name = $val_projects['cart_model_name'] ?? null;
                $projectProcessStdTime->product_type = $product_type;
                $projectProcessStdTime->bom_req_estimation_manager = $val_projects['download_excel_bom'];
                $projectProcessStdTime->drawing_req_estimation_manager = $val_projects['download_excel_drawing'];
                $projectProcessStdTime->delivery = $val_projects['partial_delivery'];
                $projectProcessStdTime->unit_price = $val_projects['unit_price'];
                $projectProcessStdTime->total_price = $val_projects['total_price'];

                // Currency conversion
                $setting_key = '1_AED_TO_' . strtoupper($project->currency);
                if ($project->currency !== 'N/A') {
                    $rate = AdminSetting::where('key', $setting_key)->value('value') ?? 1;
                    $projectProcessStdTime->currency_wise_sales_unit_value = $val_projects['unit_price'] * $rate;
                    $projectProcessStdTime->currency_wise_sales_total_value = $val_projects['total_price'] * $rate;
                } else {
                    $projectProcessStdTime->currency_wise_sales_unit_value = $val_projects['unit_price'];
                    $projectProcessStdTime->currency_wise_sales_total_value = $val_projects['total_price'];
                }

                $projectProcessStdTime->save();
                $product_id = $projectProcessStdTime->id;

                // Generate QR codes
                $qrPaths = $this->generateProductQrCode($project_no, $val_projects['full_article_number']);
                $projectProcessStdTime->qr_codes = json_encode($qrPaths);
                $projectProcessStdTime->save();

                // Calculate product-wise estimated date (for individual product tracking)
                $total_days_for_std_process = 1;
                $current_cart_model = $val_projects['cart_model_name'] ?? '';

                if (isset($keywords_by_cart_model[$current_cart_model])) {
                    foreach ($keywords_by_cart_model[$current_cart_model] as $item) {
                        $total_days_for_std_process = max($total_days_for_std_process, $item['total_days']);
                    }
                }

                $get_product_wise_estimated_date = get_product_wise_estimated_date(
                    $product_type,
                    $total_days_for_std_process,
                    $qty
                );

                $projectProcessStdTime->estimated_readiness_date = $get_product_wise_estimated_date;
                $projectProcessStdTime->save();

                // Create quantity records
                for ($i = 0; $i < $qty; $i++) {
                    $qty_of_product = new QtyOfProduct;
                    $qty_of_product->project_id = $projects_id;
                    $qty_of_product->product_id = $product_id;
                    $qty_of_product->qty_number = $i + 1;
                    $qty_of_product->save();
                }

                // Create process standard time records
                $admin_hours_setting = AdminHoursManagement::where('product_type', '=', $product_type)
                    ->where('is_deleted', '0')
                    ->get();

                for ($i = 0; $i < $qty; $i++) {
                    foreach ($admin_hours_setting as $admin_hour) {
                        $add_project_time = new ProjectProcessStdTime;
                        $add_project_time->projects_id = $projects_id;
                        $add_project_time->product_id = $product_id;
                        $add_project_time->order_qty = $i + 1;
                        $add_project_time->project_type_name = $admin_hour->product_type;
                        $add_project_time->project_process_name = $admin_hour->process_name;
                        $add_project_time->process_std_time = $admin_hour->value;
                        $add_project_time->save();
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | NEW ESTIMATED READINESS DATE CALCULATION
            |--------------------------------------------------------------------------
            | This replaces the old calculation logic with the new requirements:
            | 
            | PROJECT TYPE DETERMINATION:
            | - If is_pricing_tool_quotation_number == "1" → Standard Project
            | - Otherwise → Non-Standard Project
            | 
            | STANDARD PROJECT LOGIC:
            | - Add BOM/Drawing days ONLY if drawing is requested
            | - Add Final Inspection days
            | - Add product type weeks (highest among all products)
            | - Skip weekends in calculation
            | 
            | NON-STANDARD PROJECT LOGIC:
            | - ALWAYS add BOM/Drawing days (regardless of drawing request)
            | - Do NOT add Final Inspection days
            | - Add product type weeks (highest among all products)
            | - Skip weekends in calculation
            |--------------------------------------------------------------------------
            */

            // Determine project type based on is_pricing_tool_quotation_number
            // Standard = pricing tool quotation, Non-Standard = manually created
            $projectType = ($project->is_pricing_tool_quotation_number == "1") ? 'Standard' : 'Non-Standard';

            // Calculate the estimated readiness date
            $projectCreationDate = Carbon::parse($project->wip_project_create_date);
            $estimatedReadinessDate = $this->calculateEstimatedReadinessDate(
                $projectType,
                $anyDrawingRequested,
                $allProductTypes,
                $projectCreationDate
            );

            // Update the project with the calculated estimated readiness date
            $project->estimated_readiness = $estimatedReadinessDate;
            $project->save();
        }

        return redirect()
            ->route('ProductionManagerProjectIndex')
            ->with('success', 'Project Added Successfully.');
    }

    protected function generateProductQrCode($project_no, $full_article_number)
    {
        // Fetch the product
        $product = ProductsOfProjects::where('project_id', function ($query) use ($project_no) {
            $query->select('id')->from('projects')->where('project_no', $project_no)->firstOrFail();
        })
            ->where('full_article_number', $full_article_number)
            ->firstOrFail();

        // Fetch the project to get the project name
        $project = Project::where('project_no', $project_no)->firstOrFail();
        $project_name = str_replace(' ', '_', $project->project_name); // Replace spaces with underscores

        // Get the product quantity
        $qty = $product->qty;

        // Array to store QR code paths
        $qrCodePaths = [];

        // Loop through each unit of quantity
        for ($i = 1; $i <= $qty; $i++) {
            $url = url("qr_code/{$project_no}/{$full_article_number}/{$i}");
            $qrCodeName = "{$project_no}_{$project_name}_{$full_article_number}_{$i}.png";
            $qrCodePath = "project_document/{$project_no}/product_qr/{$full_article_number}/{$qrCodeName}";
            $fullPath = public_path($qrCodePath);
            $directory = dirname($fullPath);

            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0777, true);
            }

            $qrCode = QrCode::size(400)
                ->margin(10) // Increased margin for better padding
                ->format('png')
                ->errorCorrection('H') // High error correction for better scannability
                ->merge('/public/sales_manager/uploads/logo/wilo_logo.png', 0.3) // Reduced opacity
                ->generate($url, $fullPath);

            $qrImage = imagecreatefrompng($fullPath);

            // Text for top: project_no_project_name
            $topText = $project_no . '_' . $project_name;
            $topFontSize = strlen($topText) > 25 ? 16 : 20;
            $textColor = imagecolorallocate($qrImage, 0, 0, 0);
            $fontPath = public_path('fassets/fonts/WiloPlusGlobalBold.woff');

            $topTextBox = imagettfbbox($topFontSize, 0, $fontPath, $topText);
            $topTextWidth = $topTextBox[2] - $topTextBox[0];
            $topTextX = (imagesx($qrImage) - $topTextWidth) / 2;
            $topTextY = 30 + $topFontSize; // Increased padding from top

            imagettftext($qrImage, $topFontSize, 0, $topTextX, $topTextY, $textColor, $fontPath, $topText);

            // Text for bottom: _full_article_number_i/qty
            $bottomText = $full_article_number . '_' . $i . '/' . $qty;
            $bottomFontSize = strlen($bottomText) > 25 ? 16 : 20;

            $bottomTextBox = imagettfbbox($bottomFontSize, 0, $fontPath, $bottomText);
            $bottomTextWidth = $bottomTextBox[2] - $bottomTextBox[0];
            $bottomTextX = (imagesx($qrImage) - $bottomTextWidth) / 2;
            $bottomTextY = imagesy($qrImage) - 30; // Increased offset from bottom

            imagettftext($qrImage, $bottomFontSize, 0, $bottomTextX, $bottomTextY, $textColor, $fontPath, $bottomText);

            imagepng($qrImage, $fullPath);
            imagedestroy($qrImage);

            $qrCodePaths[] = $qrCodePath;
        }

        return $qrCodePaths;
    }

    public function showProductDetails($project_no, $full_article_number, $qty_index = null)
    {
        // Fetch the project
        $project = Project::where('project_no', $project_no)->with('projectStatus')->firstOrFail();

        // Fetch the product
        $product = ProductsOfProjects::where('project_id', $project->id)
            ->where('full_article_number', $full_article_number)
            ->firstOrFail();

        // Get the QR code for the specific quantity (if provided)
        $qrCode = null;
        if ($qty_index) {
            $qrCodes = json_decode($product->qr_codes, true);
            if (is_array($qrCodes) && isset($qrCodes[$qty_index - 1])) {
                $qrCode = $qrCodes[$qty_index - 1];
            } else {
                $qrCode = $product->qr;
            }
        } else {
            $qrCodes = json_decode($product->qr_codes, true);
            $qrCode = is_array($qrCodes) && !empty($qrCodes) ? $qrCodes[0] : $product->qr;
        }

        // Get unique process names
        $processNames = ProjectProcessStdTime::where('product_id', $product->id)
            ->where('projects_id', $project->id)
            ->pluck('project_process_name')
            ->unique();

        // Group processes by order_qty
        $groupedProcesses = ProjectProcessStdTime::where('product_id', $product->id)
            ->where('projects_id', $project->id)
            ->get()
            ->groupBy('order_qty');

        // Fetch the last completed action for the specific quantity (if provided) or product
        $lastAction = DB::table('project_process_std_time')
            ->where('projects_id', $project->id)
            ->where('product_id', $product->id)
            ->when($qty_index, function ($query) use ($qty_index) {
                return $query->where('order_qty', $qty_index);
            })
            ->where('project_status', '1')
            ->orderBy('id', 'desc')
            ->first();

        // Get latest purchase order dates
        $latest_create_po_date = $project->purchaseOrders()->orderBy('id', 'desc')->first();

        // Check project creation status
        $check_status_project_create = $project->check_status_project_create();

        // Pass total quantity explicitly
        $totalQty = $product->qty;

        // Fetch initial_inspection, and final_inspection
        $initial_inspection = $product->initial_inspection_date ?? null;
        
        $final_inspection = $product->final_inspection_date ?? null;

        return view('production_manager.product_qr', compact(
            'project',
            'product',
            'processNames',
            'groupedProcesses',
            'check_status_project_create',
            'latest_create_po_date',
            'qrCode',
            'qty_index',
            'totalQty',
            'initial_inspection',
            'final_inspection',
            'lastAction' // New variable passed to the view
        ));
    }

    public function getProjectQrCodes(Request $request, $projectId, $articleNumber)
    {
        $project = Project::find($projectId);
        if (!$project) {
            return response()->json(['success' => false, 'message' => 'Project not found'], 404);
        }

        $directory = public_path("project_document/{$project->project_no}/product_qr/{$articleNumber}");
        $qrCodes = [];

        if (File::exists($directory)) {
            $files = File::files($directory);
            foreach ($files as $file) {
                $qrCodes[] = [
                    'name' => $file->getFilename(),
                    'path' => "project_document/{$project->project_no}/product_qr/{$articleNumber}/{$file->getFilename()}"
                ];
            }
        }

        return response()->json([
            'success' => true,
            'qrCodes' => $qrCodes
        ]);
    }

    public function downloadAllProjectQrCodes($projectId)
    {
        try {
            // Fetch project and products
            $project = Project::findOrFail($projectId);
            $projectNo = $project->project_no;
            $products = ProductsOfProjects::where('project_id', $projectId)->get();

            if ($products->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No products found for this project'], 404);
            }

            // Set up ZIP file with the new name
            $zipFileName = "project_no_{$projectNo}_qr_codes.zip";
            $zipPath = storage_path("app/temp/{$zipFileName}");
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }

            $zip = new \ZipArchive();
            if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                Log::error('Failed to create ZIP file', ['path' => $zipPath]);
                return response()->json(['success' => false, 'message' => 'Failed to create ZIP file'], 500);
            }

            $addedFiles = false;

            // Iterate through products and add existing QR code files
            foreach ($products as $product) {
                $articleNumber = $product->full_article_number;
                $directory = public_path("project_document/{$projectNo}/product_qr/{$articleNumber}");

                if (File::exists($directory)) {
                    $files = File::files($directory);
                    foreach ($files as $file) {
                        $filePath = $file->getRealPath();
                        $fileName = $file->getFilename();
                        $relativePath = "{$articleNumber}/{$fileName}";
                        $zip->addFile($filePath, $relativePath);
                        $addedFiles = true;
                    }
                } else {
                    Log::warning('QR directory not found', ['path' => $directory]);
                }
            }

            $zip->close();

            if (!$addedFiles) {
                unlink($zipPath);
                return response()->json(['success' => false, 'message' => 'No QR codes found for this project'], 404);
            }

            // Return the ZIP file for download with the new name
            return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Error in downloadAllProjectQrCodes: ' . $e->getMessage(), [
                'projectId' => $projectId,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Server error occurred'], 500);
        }
    }

    public function downloadAllQrCodes(Request $request, $projectId, $articleNumber)
    {
        $project = Project::find($projectId);
        if (!$project) {
            return response()->json(['success' => false, 'message' => 'Project not found'], 404);
        }

        $directory = public_path("project_document/{$project->project_no}/product_qr/{$articleNumber}");
        if (!File::exists($directory)) {
            return response()->json(['success' => false, 'message' => 'No QR codes found for this article number'], 404);
        }

        // Create a temporary ZIP file
        $zipFileName = "qr_codes_{$project->project_no}_{$articleNumber}.zip";
        $zipFilePath = storage_path("app/temp/{$zipFileName}");
        File::makeDirectory(storage_path('app/temp'), 0777, true, true);

        $zip = new \ZipArchive();
        if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return response()->json(['success' => false, 'message' => 'Failed to create ZIP file'], 500);
        }

        // Add all QR code images to the ZIP
        $files = File::files($directory);
        foreach ($files as $file) {
            $filePath = $file->getRealPath();
            $fileName = $file->getFilename();
            $zip->addFile($filePath, $fileName);
        }

        $zip->close();

        if (!File::exists($zipFilePath)) {
            return response()->json(['success' => false, 'message' => 'Failed to generate ZIP file'], 500);
        }

        // Return the ZIP file as a download response
        return response()->download($zipFilePath, $zipFileName)->deleteFileAfterSend(true);
    }

    public function getProjectDocuments(Request $request)
    {
        $projectId = $request->input('id');
        $folder = $request->input('folder');
        $subfolder = $request->input('subfolder'); // Add subfolder parameter to handle BOE, INVOICE, OA, PO

        $project = Project::find($projectId);

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found'
            ], 404);
        }

        $documents = [];

        // Handle "PO and Invoices" folder and its subfolders
        if ($folder === 'PO and Invoices') {
            $purchaseOrders = PurchaseOrder::where('project_no', $project->project_no)
                ->where('is_production_engineer_approved', 1) // Optional: Only approved POs
                ->get();

            if ($purchaseOrders->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No documents found.'
                ]);
            }

            foreach ($purchaseOrders as $po) {
                if ($subfolder === 'BOE' && $po->boe_file) {
                    foreach ($po->boe_file as $file) {
                        $documents[] = [
                            'name' => basename($file),
                            'path' => $file
                        ];
                    }
                } elseif ($subfolder === 'INVOICE' && $po->invoice_file) {
                    foreach ($po->invoice_file as $file) {
                        $documents[] = [
                            'name' => basename($file),
                            'path' => $file
                        ];
                    }
                } elseif ($subfolder === 'OA' && $po->oa_file) {
                    foreach ($po->oa_file as $file) {
                        $documents[] = [
                            'name' => basename($file),
                            'path' => $file
                        ];
                    }
                } elseif ($subfolder === 'PO' && $po->po_pdf) {
                    $documents[] = [
                        'name' => basename($po->po_pdf),
                        'path' => "purchase_order_pdf/{$po->po_pdf}"
                    ];
                }
            }

            if (empty($documents)) {
                return response()->json([
                    'success' => false,
                    'message' => "No documents found in $subfolder"
                ]);
            }

            return response()->json([
                'success' => true,
                'documents' => $documents
            ]);
        }

        // Handle "From Customers" folder
        if ($folder === 'From Customers') {
            // Fetch documents from the documents column
            $documents = json_decode($project->documents, true) ?? [];

            // Format documents to match the expected response structure
            $formattedDocuments = array_map(function ($doc) {
                return [
                    'name' => basename($doc), // Extract the file name from the path
                    'path' => $doc // Full path stored in the documents field
                ];
            }, $documents);

            if (empty($formattedDocuments)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No documents found in From Customers'
                ]);
            }

            return response()->json([
                'success' => true,
                'documents' => $formattedDocuments
            ]);
        }

        // Fallback for other folders (if any)
        $documents = json_decode($project->documents, true) ?? [];

        return response()->json([
            'success' => true,
            'documents' => $documents
        ]);
    }

    public function getProjectDocumentsForInbox(Request $request)
    {
        $projectId = $request->input('id');
        $folder = $request->input('folder');
        $subfolder = $request->input('subfolder'); // Add subfolder parameter if needed

        $project = Project::find($projectId);

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found'
            ], 404);
        }

        $documents = [];

        // Handle "From Customers" folder
        if ($folder === 'From Customers') {
            // Fetch documents from the documents column
            $documents = json_decode($project->documents, true) ?? [];

            // Format documents to match the expected response structure
            $formattedDocuments = array_map(function ($doc) {
                return [
                    'name' => basename($doc), // Extract the file name from the path
                    'path' => $doc // Full path stored in the documents field
                ];
            }, $documents);

            if (empty($formattedDocuments)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No documents found in From Customers'
                ]);
            }

            return response()->json([
                'success' => true,
                'documents' => $formattedDocuments
            ]);
        }

        // Fallback for other folders (if any)
        $documents = json_decode($project->documents, true) ?? [];

        return response()->json([
            'success' => true,
            'documents' => $documents
        ]);
    }

    public function check_project_status($id)
    {
        $project = Project::where('id', $id)
            ->with('product')
            ->with('productsProcess')
            ->with('InitialInspection')
            ->with(['purchaseOrders' => function ($query) {
                $query->with(['purchaseOrderTables' => function ($subQuery) {
                    $subQuery->orderBy('id', 'desc');
                }])
                ->where('is_production_engineer_approved', 1) // Mandatory condition
                ->orderBy('id', 'desc');
            }])
            ->first();

        $project_no = Project::where('id', $id)->value('project_no');
        $intial_inspection = DB::table('initial_inspection_data')
            ->where('project_no', $project_no)
            ->orderBy('ini_inspection_date', 'desc')
            ->value('ini_inspection_date');

        $stdTimes = ProjectProcessStdTime::where('projects_id', $id)->get();

        // Fetch the hours threshold from admin_hours_management
        $create_project_hours = DB::table('admin_hours_management')
            ->where('lable', 'StandardProcessTimes')
            ->where('key', 'create_new_project')
            ->where('is_deleted', 0)
            ->value('value');      

        $final_inspection_hours = DB::table('admin_hours_management')
            ->where('lable', 'StandardProcessTimes')
            ->where('key', 'final_inspection')
            ->where('is_deleted', 0)
            ->value('value');


        $prepare_pl_hours = DB::table('admin_hours_management')
            ->where('lable', 'StandardProcessTimes')
            ->where('key', 'prepare_pl')
            ->where('is_deleted', 0)
            ->value('value');

        $total_hours = $create_project_hours + $bom_drawings_hours + $check_bom_place_po_hours + $intial_inspection_hours + $final_inspection_hours + $prepare_pl_hours;
        $total_hours = $create_project_hours + $final_inspection_hours + $request_mrf_hours + $prepare_pl_hours;        

        return view('production_manager.check_project_status', compact(
            'project',
            'intial_inspection',
            'stdTimes',
            'create_project_hours',
            'final_inspection_hours',
            'prepare_pl_hours',
            'total_hours'
        )); 
        
    }

    public function project_edit_form($id)
    {
        $project = Project::find($id);
        $documents = json_decode($project->documents);
        return view('production_manager.edit_project', compact('documents', 'project'));
    }

    public function project_update(Request $request)
    {
        $project = Project::find($request->id);

        if (!$project) {
            return back()->with('error', 'Project not found.');
        }

        // Initialize existing documents
        $existingDocuments = json_decode($project->documents, true) ?? [];

        // Handle document deletion
        if ($request->has('delete_documents')) {
            foreach ($request->delete_documents as $deleteDocument) {
                // Remove the file from storage
                $filePath = public_path($deleteDocument);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }

                // Remove from array
                $existingDocuments = array_filter($existingDocuments, function ($doc) use ($deleteDocument) {
                    return $doc !== $deleteDocument;
                });
            }
            // Reindex array to avoid gaps
            $existingDocuments = array_values($existingDocuments);
        }

        // Handle new document uploads
        if ($request->hasFile('customer_documents')) {
            // Define the base directory for project documents
            $baseDir = public_path("project_document/customer_documents");

            // Create directories if they don't exist
            if (!File::exists($baseDir)) {
                File::makeDirectory($baseDir, 0777, true);
            }

            foreach ($request->file('customer_documents') as $file) {
                $fileName = time() . '-' . Str::random(2) . '-' . $file->getClientOriginalName();
                // Move the file to the project-specific directory
                $file->move($baseDir, $fileName);
                // Store the relative path for database
                $existingDocuments[] = "project_document/customer_documents/{$fileName}";
            }
        }

        // Update project fields (use existing values if input is null)
        $project->project_name = $request->project_name ?? $project->project_name;
        $project->product_type = $request->product_type ? json_encode($request->product_type) : $project->product_type;
        $project->assembly_quotation_ref = $request->assembly_quotation_ref ?? $project->assembly_quotation_ref;
        $project->country = $request->country_name ?? $project->country;
        $project->customer_name = $request->customer_name ?? $project->customer_name;
        $project->sales_name = $request->sales_person ?? $project->sales_name;
        $project->documents = json_encode($existingDocuments);

        // Save the updated project
        $project->save();

        foreach ($request->input('products', []) as $projectId => $articles) {
            foreach ($articles as $articleNumber => $row) {

                ProductsOfProjects::where('project_id', $projectId)
                    ->where('article_number', $articleNumber)
                    ->update([
                        'delivery' => $row['delivery'] ?? null,
                    ]);
            }
        }

        return back()->with('success', 'Project Updated Successfully.');
    }

    public function get_quotation_items(Request $request)
    {
        $quotation_number = $request->quotation_number;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://wilo.360websitedemo.com/api/get_all_products',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => http_build_query(['quotation_number' => $quotation_number]),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $decodedResponse = json_decode($response);
        return response()->json($decodedResponse);
    }

    //This function is called when production engineer clicks on download the BOM
    public function getBOM(Request $request)
    {
        $quotation_number = $request->quotation_number;
        $full_article_number = $request->full_article_number;

        $item_id = $request->item_id;
        $item_name = $request->item_name;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://wilo.360websitedemo.com/api/getBOM',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => http_build_query(['item_id' => $item_id, 'item_name' => $item_name]),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $decodedResponse = json_decode($response);
        return response()->json($decodedResponse);
    }

    public function getBOMForCheckStatus(Request $request)
    {
        dd("testt");
        $quotation_number = $request->quotation_number;
        $full_article_number = $request->full_article_number;
        $cart_model_name = $request->cart_model_name;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://wilo.360websitedemo.com/api/getBOMCheckStatus',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => http_build_query(['quotation_number' => $quotation_number, 'full_article_number' => $full_article_number, 'cart_model_name' => $cart_model_name]),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $decodedResponse = json_decode($response);
        return response()->json($decodedResponse);
    }

    public function inbox()
    {
        $page_title = "PROJECT STATUS";

        return view('production_manager.inbox', compact('page_title'));
    }

    public function approve(Request $request, $id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        $purchaseOrder->is_production_engineer_approved = 1;
        $purchaseOrder->approved_remarks_production_engineer = $request->remarks;
        $purchaseOrder->production_engineer_approved_date = Carbon::now();
        if (!is_null($purchaseOrder->production_engineer_reject_date)) {
            $purchaseOrder->production_engineer_reject_date = null; // Clear reject date if it exists
        }
        $purchaseOrder->save();

        return response()->json(['success' => 'Purchase order approved successfully!']);
    }

    public function reject(Request $request, $id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        $purchaseOrder->is_production_engineer_approved = 2;
        $purchaseOrder->rejection_reason_production_engineer = $request->reason;
        $purchaseOrder->production_engineer_reject_date = Carbon::now();
        if (!is_null($purchaseOrder->production_engineer_approved_date)) {
            $purchaseOrder->production_engineer_approved_date = null; // Clear approve date if it exists
        }
        $purchaseOrder->save();

        return response()->json(['success' => 'Purchase order rejected successfully!']);
    }

    public function view($id)
    {
        $purchaseOrder = PurchaseOrder::with('purchaseOrderTables')->findOrFail($id);
        return view('assembly_manager.view_po', compact('purchaseOrder'));
    }

    public function upload_nameplate_img(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'id' => 'required|integer',
            'lable' => 'required|string',
            'is_edit' => 'nullable|boolean',
            'project_id' => 'required|exists:projects,id',
            'article_number' => 'required|string',
            'qty_no' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $nameplate = QtyOfProduct::findOrFail($request->id);
            $project = Project::findOrFail($request->project_id);
            $file = $request->file('file');
            $isEdit = $request->input('is_edit', 0);
            $articleNumber = $request->article_number;
            $qtyNo = $request->qty_no;

            // Define the storage path
            $destinationPath = public_path("project_document/{$project->project_no}/NamePlate/{$articleNumber}/{$qtyNo}");
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // Delete existing image if this is an edit action and an image exists
            if ($isEdit && !empty($nameplate->nameplate_img) && file_exists(public_path($nameplate->nameplate_img))) {
                unlink(public_path($nameplate->nameplate_img));
            }

            // Generate a unique filename
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = "project_document/{$project->project_no}/NamePlate/{$articleNumber}/{$qtyNo}/{$fileName}";

            // Move the file to the destination
            $file->move($destinationPath, $fileName);

            // Update the database
            $nameplate->nameplate_img = $filePath;
            $nameplate->nameplate_create_inbox_to_pro_eng = "2";
            $nameplate->name_plate_upload_date = Carbon::now();
            $nameplate->save();

            return response()->json([
                'success' => 'File ' . ($isEdit ? 'updated' : 'uploaded') . ' successfully!',
                'file' => [
                    'name' => $fileName,
                    'path' => $filePath
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error processing file: ' . $e->getMessage()], 500);
        }
    }

    public function getNamePlateImages(Request $request, $projectId, $articleNumber, $qtyNo)
    {
        $project = Project::find($projectId);
        if (!$project) {
            return response()->json(['success' => false, 'message' => 'Project not found'], 404);
        }

        $path = public_path("project_document/{$project->project_no}/NamePlate/{$articleNumber}/{$qtyNo}");
        $images = [];

        if (File::exists($path)) {
            $files = File::files($path);
            foreach ($files as $file) {
                $images[] = [
                    'name' => $file->getFilename(),
                    'path' => "project_document/{$project->project_no}/NamePlate/{$articleNumber}/{$qtyNo}/{$file->getFilename()}",
                    'size' => $file->getSize(),
                    'created_at' => date('Y-m-d H:i:s', $file->getCTime())
                ];
            }
        }

        return response()->json([
            'success' => true,
            'images' => $images
        ]);
    }

    public function updateCheckStatus(Request $request)
    {
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

    public function getWIPPhotos(Request $request)
    {
        $projectId = $request->input('id');
        $project = Project::find($projectId);

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found'
            ], 404);
        }

        $projectNo = $project->project_no; // e.g., "24-57"
        $photoDir = public_path("operator/product_type_process/capture_imgs/{$projectNo}");
        $photos = [];

        // Debugging info
        $debug = [
            'project_id' => $projectId,
            'project_no' => $projectNo,
            'photo_dir' => $photoDir,
            'dir_exists' => File::exists($photoDir),
        ];

        if (File::exists($photoDir)) {
            $files = File::files($photoDir);
            $debug['file_count'] = count($files);
            $debug['files_found'] = [];

            foreach ($files as $file) {
                $filename = $file->getFilename();
                $photos[] = [
                    'name' => $filename,
                    'path' => "operator/product_type_process/capture_imgs/{$projectNo}/{$filename}"
                ];
                $debug['files_found'][] = $filename;
            }
        } else {
            $debug['error'] = 'Directory does not exist';
        }

        return response()->json([
            'success' => true,
            'photos' => $photos,
            'debug' => $debug // Include debug info in response
        ]);
    }

    public function getProjectsDrawings(Request $request)
    {
        $projectId = $request->input('projectId');
        $folder = $request->input('folder');
        $subsubfolder = $request->input('subsubfolder');

        // Validate project
        $project = Project::find($projectId);
        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found'
            ], 404);
        }

        // Fetch products associated with the project
        $products = ProductsOfProjects::where('project_id', $projectId)
            ->select('full_article_number', 'description', 'cart_model_name', 'project_id', 'drawing_path', 'drawing_req_estimation_manager')
            ->get();

        $allProductsDrawings = [];

        foreach ($products as $product) {
            $drawingPath = null;

            // Construct the directory path for the subsubfolder
            if ($subsubfolder) {
                // Replace spaces with underscores for file system compatibility
                $subsubfolderSafe = str_replace(' ', ' ', $subsubfolder); // Adjust if folder names use underscores or other conventions
                $directory = public_path("project_document/{$project->project_no}/Project Data/Drawings/{$product->full_article_number}/{$subsubfolderSafe}");

                // Check if the directory exists and contains any .pdf files
                if (File::exists($directory)) {
                    $files = File::files($directory);
                    foreach ($files as $file) {
                        if ($file->getExtension() === 'pdf') {
                            $drawingPath = "project_document/{$project->project_no}/Project Data/Drawings/{$product->full_article_number}/{$subsubfolderSafe}/{$file->getFilename()}";
                            break; // Use the first .pdf file found
                        }
                    }
                }
            }

            $allProductsDrawings[] = [
                'full_article_number' => $product->full_article_number,
                'description' => $product->description,
                'cart_model_name' => $product->cart_model_name,
                'project_id' => $product->project_id,
                'drawing_path' => $drawingPath, // Path to the specific drawing file or null
                'drawing_req_estimation_manager' => $product->drawing_req_estimation_manager
            ];
        }

        return response()->json([
            'success' => true,
            'allProductsDrawings' => $allProductsDrawings
        ]);
    }

    public function downloadDrawing($projectId, $articleNumber, $type)
    {
        // Validate project
        $project = Project::find($projectId);
        if (!$project) {
            return response()->json(['success' => false, 'message' => 'Project not found'], 404);
        }

        // Decode the type (since it may contain spaces)
        $type = urldecode($type);

        // Construct the directory path
        $directory = public_path("project_document/{$project->project_no}/Project Data/Drawings/{$articleNumber}/{$type}");

        // Find the first .pdf file in the directory
        $filePath = null;
        $fileName = null;
        if (File::exists($directory)) {
            $files = File::files($directory);
            foreach ($files as $file) {
                if ($file->getExtension() === 'pdf') {
                    $filePath = $file->getRealPath();
                    $fileName = $file->getFilename();
                    break; // Use the first .pdf file found
                }
            }
        }

        // Check if a file was found
        if ($filePath && File::exists($filePath)) {
            $downloadFileName = str_replace(' ', '_', $type) . "-{$articleNumber}.pdf"; // e.g., Estimation_Manager_Upload_Drawing-ARTICLE123.pdf
            return response()->download($filePath, $downloadFileName)->deleteFileAfterSend(false);
        }

        return response()->json(['success' => false, 'message' => "No {$type} drawing found for article number {$articleNumber}"], 404);
    }

    public function getCancellationDetails(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
        ]);

        $project = DB::table('projects')
            ->select('isWITrack_project_cancelled_reason')
            ->where('id', $request->project_id)
            ->first();

        return response()->json([
            'cancellation_reason' => $project->isWITrack_project_cancelled_reason,
        ]);
    }

    public function getProjectExecutionPartialOrderPLList($projectId)
    {
        try {
            $project = Project::findOrFail($projectId);
            $products = ProductsOfProjects::where('project_id', $projectId)->get();

            $filteredProducts = [];
            foreach ($products as $product) {
                $articleDir = public_path("project_document/{$project->project_no}/Project Execution/Partial_Order_PL/{$product->full_article_number}");

                // Check if the article directory exists and has at least one file (including in subfolders)
                if (File::exists($articleDir) && count(File::allFiles($articleDir)) > 0) {
                    $filteredProducts[] = [
                        'cart_model_name' => $product->cart_model_name,
                        'description' => $product->description,
                        'full_article_number' => $product->full_article_number,
                        'qty' => $product->qty ?? 1,
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'partialOrderPLList' => $filteredProducts,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch Partial Order PL data',
            ], 500);
        }
    }

    public function getPartialOrderPLDocs($projectId, $articleNumber, $qtyNo)
    {
        try {
            $project = Project::findOrFail($projectId);
            $projectNo = $project->project_no;

            // Construct the directory path
            $directory = public_path("project_document/{$projectNo}/Project Execution/Partial_Order_PL/{$articleNumber}/{$qtyNo}");

            // Check if the directory exists
            if (!is_dir($directory)) {
                return response()->json([
                    'success' => true,
                    'documents' => [],
                    'message' => 'No documents found in the specified directory',
                ]);
            }

            // Get all files in the directory
            $files = array_diff(scandir($directory), ['.', '..']);
            $documents = [];

            foreach ($files as $file) {
                $filePath = "project_document/{$projectNo}/Project Execution/Partial_Order_PL/{$articleNumber}/{$qtyNo}/{$file}";
                $documents[] = [
                    'name' => $file,
                    'path' => $filePath,
                ];
            }

            return response()->json([
                'success' => true,
                'documents' => $documents,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch documents',
            ], 500);
        }
    }

    public function getNamePlateList($projectId)
    {
        $products = ProductsOfProjects::where('project_id', $projectId)
            ->select('cart_model_name', 'description', 'full_article_number', 'qty')
            ->get();
        return response()->json(['namePlateList' => $products]);
    }

    public function exportCSV(Request $request)
    {
        $query = Project::select('*')
            ->orderByRaw("CAST(SUBSTRING_INDEX(project_no, '-', 1) AS UNSIGNED) DESC")
            ->orderByRaw("CAST(SUBSTRING_INDEX(project_no, '-', -1) AS UNSIGNED) DESC");

        if ($request->filled('status') && $request->status != "3") {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority') && $request->priority != "2") {
            $query->where('is_priotize', $request->priority);
        }

        $filters = $request->input('filters', []);

        // Map filter keys to column names
        $filterableColumns = [
            'filter_col_0' => 'status',
            'filter_col_1' => 'wip_project_create_date',
            'filter_col_2' => 'customer_ref',
            'filter_col_3' => 'project_no',
            'filter_col_4' => 'project_name',
            'filter_col_5' => 'country',
            'filter_col_6' => 'customer_name',
            'filter_col_7' => 'sales_name',
            'filter_col_8' => 'estimated_readiness',
        ];

        foreach ($filters as $key => $values) {
            $values = array_filter((array) $values); // Convert and skip empty values
            if (empty($values))
                continue;

            switch ($key) {
                case 'filter_col_1': // wip_project_create_date
                case 'filter_col_8': // estimated_readiness
                    $column = $filterableColumns[$key] ?? null;
                    if ($column) {
                        $query->where(function ($q) use ($values, $column) {
                            foreach ($values as $val) {
                                try {
                                    // Special case: filter only by DATE part of datetime field
                                    $date = \Carbon\Carbon::parse($val)->toDateString();
                                    $q->orWhereDate($column, $date);
                                } catch (\Exception $e) {
                                    // Invalid date, skip
                                }
                            }
                        });
                    }
                    break;

                case 'filter_col_9': // Special readiness logic
                    $query->where(function ($q) use ($values) {
                        foreach ($values as $val) {
                            try {
                                // Special case: filter only by DATE part of datetime field
                                $date = \Carbon\Carbon::parse($val)->toDateString();
                                $q->orWhere(function ($sub) use ($date) {
                                    $sub->whereDate('actual_readiness', $date)
                                        ->orWhere(function ($q2) use ($date) {
                                            $q2->whereNull('actual_readiness')
                                                ->whereDate('estimated_readiness', $date);
                                        });
                                });
                            } catch (\Exception $e) {
                                // Invalid date, skip
                            }
                        }
                    });
                    break;

                default:
                    if (isset($filterableColumns[$key])) {
                        $column = $filterableColumns[$key];
                        $query->whereIn($column, $values);
                    }
                    break;
            }
        }
        // Final query
        $projects = $query->with('product')->whereNotNull('assembly_quotation_ref')->where('is_deleted', 0)->get(); // A Code: 26-12-2025
        // Export CSV Code
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="project_details_filter_export_' . $timestamp . '.csv"',
        ];
        $columns = ['SR No', 'Status', 'Date', 'Customer Ref.', 'Project No.', 'Project Name', 'Country', 'Customer Name', 'Sales Name', 'Estimated Readiness', 'Actual Readiness'];

        $callback = function () use ($projects, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            $sr_no = 1;
            foreach ($projects as $project) {

                switch ($project->status) {
                    case "0":
                        $statusLabel = "Open";
                        break;
                    case "1":
                        $statusLabel = "InProgress";
                        break;
                    case "2":
                        $statusLabel = "Completed";
                        break;
                    default:
                        $statusLabel = "Unknown";
                        break;
                }

                $colSrNo = $sr_no++;
                $colStatus = $statusLabel;
                $colDate = $project->wip_project_create_date
                    ? '="' . Carbon::parse($project->wip_project_create_date)->format('d-m-Y') . '"'
                    : 'N/A';
                $colCustomerRef = $project->customer_ref ?? 'N/A';
                $colProjectNo = '="' . ($project->project_no ?? 'N/A') . '"'; // Excel-safe
                $colProjectName = $project->project_name ?? 'N/A';
                $colCountry = $project->country ?? 'N/A';
                $colCustomerName = $project->customer_name ?? 'N/A';
                $colSalesName = $project->sales_name ?? 'N/A';
                $colEstimatedReadiness = $project->estimated_readiness
                    ? '="' . Carbon::parse($project->estimated_readiness)->format('d-m-Y') . '"'
                    : 'N/A';
                $actual_readiness_date = $project->actual_readiness ?? $project->estimated_readiness;
                $colActualReadiness = '="' . Carbon::parse($actual_readiness_date)->format('d-m-Y') . '"';

                fputcsv($file, [
                    $colSrNo,
                    $colStatus,
                    $colDate,
                    $colCustomerRef,
                    $colProjectNo,
                    $colProjectName,
                    $colCountry,
                    $colCustomerName,
                    $colSalesName,
                    $colEstimatedReadiness,
                    $colActualReadiness
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    // A Code: 10-01-2026 Start
    public function project_delete(Request $request, $id)
    {

        // Role check
        if (!(Auth::user()->role === 'Admin' || Auth::user()->is_admin_login)) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }

        $request->validate([
            'reason' => 'required|string'
        ]);

        try {

            DB::transaction(function () use ($id, $request) {

                $project = Project::findOrFail($id);

                // ================= Folder Clean =================
                $pathsToClean = [
                    public_path('project_document/' . $project->project_no),
                    public_path('operator/product_type_process/capture_imgs/' . $project->project_no),
                ];

                foreach ($pathsToClean as $path) {
                    if (File::exists($path)) {

                        foreach (File::directories($path) as $directory) {
                            File::deleteDirectory($directory);
                        }

                        foreach (File::files($path) as $file) {
                            File::delete($file);
                        }
                    }
                }

                // ================= Soft Delete =================
                DB::table('projects')
                    ->where('id', $id)
                    ->update([
                        'is_deleted' => 1,
                        'deleted_at' => now(),
                        'project_delete_reason' => $request->reason,
                    ]);

                // ================= Related Tables =================
                DB::table('initial_inspection_data')->where('project_no', $project->project_no)->delete();
                DB::table('final_inspection_data')->where('project_no', $project->project_no)->delete();
                DB::table('ncr')->where('project_no', $project->project_no)->delete();

                DB::table('products_of_projects')->where('project_id', $id)->delete();
                DB::table('product_BOM_item')->where('project_id', $id)->delete();
                DB::table('project_status')->where('project_id', $id)->delete();

                DB::table('purchase_order_table')
                    ->whereIn('po_id', function ($query) use ($project) {
                        $query->select('id')
                            ->from('purchase_order')
                            ->where('project_no', $project->project_no);
                    })
                    ->delete();

                DB::table('purchase_order')->where('project_no', $project->project_no)->delete();
                DB::table('qty_of_products')->where('project_id', $id)->delete();
                DB::table('stock_bom_po')->where('project_id', $id)->delete();
                DB::table('assigned_products_operators')->where('project_id', $id)->delete();
                DB::table('project_process_std_time')->where('projects_id', $id)->delete();
                DB::table('stock_history')->where('project_id', $id)->delete();
            });

            session()->flash('success', 'Project Deleted Successfully.');
            return response()->json([
                'status' => true,
                'message' => 'Project Deleted Successfully.',
                'redirect' => route('ProductionManagerProjectIndex')
            ]);

        } catch (\Exception $e) {

            Log::error('Project Delete Failed', [
                'project_id' => $id,
                'error' => $e->getMessage(),
            ]);

            session()->flash('error', 'Project delete failed. Please try again.');

            return response()->json([
                'status' => false,
                'message' => 'Project delete failed. Please try again.'
            ], 500);
        }
    }
    // A Code: 10-01-2026 End

    private function calculateEstimatedReadinessDate($projectType, $drawingRequested, $productTypes, $projectCreationDate)
    {
        // Initialize total days counter
        $totalDays = 0;

        /*
        |--------------------------------------------------------------------------
        | STEP 1: Determine if BOM/Drawing days should be added
        |--------------------------------------------------------------------------
        */
        $shouldAddBomDrawing = false;

        if ($projectType === 'Standard') {
            // For Standard projects: Only add if drawing is requested
            if ($drawingRequested) {
                $shouldAddBomDrawing = true;
            }
        } else {
            // For Non-Standard projects: ALWAYS add BOM/Drawing hours
            // This is mandatory regardless of whether drawing is requested or not
            $shouldAddBomDrawing = true;
        }

        /*
        |--------------------------------------------------------------------------
        | STEP 2: Add BOM/Drawing days if required
        |--------------------------------------------------------------------------
        | Fetch hours from admin_hours_management table where:
        | - lable = 'StandardProcessTimes'
        | - key = 'bom_drawings'
        | - is_deleted = 0
        | 
        | Convert hours to days: 8 hours = 1 day
        | Example: 24 hours = 24/8 = 3 days
        */
        if ($shouldAddBomDrawing) {
            $bomDrawingHours = AdminHoursManagement::where('lable', 'StandardProcessTimes')
                ->where('key', 'bom_drawings')
                ->where('is_deleted', 0)
                ->value('value');

            if ($bomDrawingHours) {
                // Convert hours to days using ceiling to round up
                // Example: 8 hours = 1 day, 9 hours = 2 days (rounded up)
                $bomDrawingDays = ceil($bomDrawingHours / 8);
                $totalDays += $bomDrawingDays;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | STEP 3: Add Final Inspection days (ONLY for Standard projects)
        |--------------------------------------------------------------------------
        | Fetch hours from admin_hours_management table where:
        | - lable = 'StandardProcessTimes'
        | - key = 'final_inspection'
        | - is_deleted = 0
        | 
        | Convert hours to days: 8 hours = 1 day
        | Example: 36 hours = 36/8 = 4.5 = 5 days (rounded up)
        */
        if ($projectType === 'Standard') {
            $finalInspectionHours = AdminHoursManagement::where('lable', 'StandardProcessTimes')
                ->where('key', 'final_inspection')
                ->where('is_deleted', 0)
                ->value('value');

            if ($finalInspectionHours) {
                // Convert hours to days using ceiling to round up
                $finalInspectionDays = ceil($finalInspectionHours / 8);
                $totalDays += $finalInspectionDays;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | STEP 4: Find the highest estimated weeks from all product types
        |--------------------------------------------------------------------------
        | Loop through all product types in the project and find which one
        | has the highest estimated_product_type_weeks value.
        | 
        | Example:
        | - Product Type A: 2 weeks
        | - Product Type B: 3 weeks
        | - Product Type C: 1 week
        | Result: Use 3 weeks (the maximum)
        */
        $maxWeeks = 0;

        foreach ($productTypes as $productTypeName) {
            // Find the product type record from database
            $productTypeRecord = ProductType::where('project_type_name', $productTypeName)
                ->where('is_active', 1)
                ->first();

            // Check if record exists and has estimated weeks
            if ($productTypeRecord && $productTypeRecord->estimated_product_type_weeks) {
                $weeks = (int) $productTypeRecord->estimated_product_type_weeks;

                // Keep track of the maximum weeks
                if ($weeks > $maxWeeks) {
                    $maxWeeks = $weeks;
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | STEP 5: Convert weeks to days and add to total
        |--------------------------------------------------------------------------
        | 1 week = 7 days (including weekends initially, but they'll be skipped
        | in the addBusinessDays function)
        | 
        | Example: 3 weeks = 3 × 7 = 21 days
        */
        $weekDays = $maxWeeks * 7;
        $totalDays += $weekDays;

        /*
        |--------------------------------------------------------------------------
        | STEP 6: Calculate final date by adding business days
        |--------------------------------------------------------------------------
        | Use the addBusinessDays function to add the total calculated days
        | to the project creation date, automatically skipping weekends.
        | 
        | Example:
        | - Project creation: 29-01-2026 (Wednesday)
        | - Total days: 24
        | - Result: Add 24 business days, skipping all Saturdays and Sundays
        */
        $estimatedDate = $this->addBusinessDays($projectCreationDate, $totalDays);

        return $estimatedDate;
    }

    private function addBusinessDays($startDate, $days)
    {
        // Create a copy of the start date to avoid modifying the original
        $currentDate = $startDate->copy();

        // Track how many business days we still need to add
        $daysToAdd = $days;

        // Loop until we've added all required business days
        while ($daysToAdd > 0) {
            // Move to the next day
            $currentDate->addDay();

            // Check if this day is a weekday (not Saturday or Sunday)
            // Carbon::SATURDAY = 6, Carbon::SUNDAY = 0
            if ($currentDate->dayOfWeek != Carbon::SATURDAY && $currentDate->dayOfWeek != Carbon::SUNDAY) {
                // It's a weekday, so count it
                $daysToAdd--;
            }
            // If it's a weekend, the loop continues without decrementing daysToAdd
        }

        return $currentDate;
    }

}
