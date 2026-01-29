<?php

namespace App\Http\Controllers\EstimantionManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProjectProcessStdTime;
use App\Models\ProductsOfProjects;
use App\Models\ProjectStatus;
use App\Models\Project;
use App\Models\AdminHoursManagement;
use Carbon\Carbon;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductBOMImport;
use App\Services\DashboardService;

class EstimationManagerController extends Controller
{
    public function dashboard(DashboardService $dashboardService){
        // Common Dashboard Service
        $role = auth()->user()->role;
        $dashboardData = $dashboardService->getDashboardData($role);        
        $page_title = "";
        $project_working_on = $dashboardData['working'];
        $project_completed = $dashboardData['done'];

        return view('production_manager.dashboard', compact('dashboardData', 'page_title', 'project_working_on', 'project_completed'));
    }

    public function inbox(){
        // Define the page title
        $page_title = "PROJECT STATUS";

        // Fetch pending BOM requests (where bom_req_estimation_manager is '1')
        $bom_req = ProductsOfProjects::with('projects')
            ->orderBy('id', 'desc')
            ->where('bom_req_estimation_manager', '1')
            ->get();

        // Fetch pending drawing requests (where drawing_req_estimation_manager is '1')
        $drawing_req = ProductsOfProjects::with('projects')
            ->orderBy('id', 'desc')
            ->where('drawing_req_estimation_manager', '1')
            ->get();

        // Fetch submitted BOM requests (where bom_req_estimation_manager is '2')
        $submitted_bom_req = ProductsOfProjects::with('projects')
            ->orderBy('id', 'desc')
            ->where('bom_req_estimation_manager', '2')
            ->get();

        // Fetch standard hours for BOM and drawings from AdminHoursManagement
        $standard_hours = AdminHoursManagement::where('lable', 'StandardProcessTimes')
            ->where('key', 'bom_drawings')
            ->where('is_deleted', 0)
            ->value('value');
        $standard_hours = (int) $standard_hours ?? 0; // Cast to integer, default to 0 if null

        // Fetch submitted drawing requests (where drawing_req_estimation_manager is neither '0' nor '1')
        $submitted_drawing_req = ProductsOfProjects::with('projects')
            ->orderBy('id', 'desc')
            ->where('drawing_req_estimation_manager', '!=', '0')
            ->where('drawing_req_estimation_manager', '!=', '1')
            ->get();

        // Fetch PDF requests pending approval (where is_asbuilt_drawing_pdf_approve_by_estimation_manager is '3')
        $pdf_req = ProductsOfProjects::with('projects')
            ->whereNotNull('editable_drawing_path')
            ->where('is_asbuilt_drawing_pdf_approve_by_estimation_manager', '3')
            ->orderBy('id', 'desc')
            ->get();

        $final_pdf_req = ProductsOfProjects::with('projects')
            ->whereNotNull('editable_drawing_path')
            ->whereNull('drawing_upload_by_estimation_manager')
            ->where('is_asbuilt_drawing_pdf_approve_by_estimation_manager', '1')
            ->where('is_asbuilt_drawing_pdf_approve_by_production_superwisor', '1')
            ->orderBy('id', 'desc')
            ->get();

        // Return the view with all necessary data
        return view('estimation_manager.inbox', compact(
            'bom_req',
            'page_title',
            'drawing_req',
            'submitted_bom_req',
            'submitted_drawing_req',
            'pdf_req',
            'final_pdf_req',
            'standard_hours'
        ));
    }

    public function upload_bom_drawing(Request $request){
        $file = $request->file('file');
        $lable = $request->lable;
        $data = ProductsOfProjects::find($request->id);

        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Project not found!'], 404);
        }

        $fileName = time() . '_' . $file->getClientOriginalName();

        if ($lable == "bom") {
            $validation = $request->validate([
                'file' => 'required|file|mimes:xls,xlsx,csv|max:5120',
                'id' => 'required|integer',
                'lable' => 'required|string',
            ]);

            $file->move(public_path('project_document/bom_files'), $fileName);
            $filePath = 'project_document/bom_files/' . $fileName;

            $data->bom_req_estimation_manager = '2';
            $data->bom_check_procurement_manager = '1';
            $data->bom_path = $filePath;
            $data->bom_upload_date = now();
        } else {
            $validation = $request->validate([
                'file' => 'required|file|mimes:pdf,psw|max:5120',
                'id' => 'required|integer',
                'lable' => 'required|string',
            ]);

            // Fetch related project_no
            $project = \App\Models\Project::findOrFail($data->project_id);
            $project_no = $project->project_no;
            $full_article_number = $data->full_article_number;
            $drawing_type = 'Estimation Manager Upload Drawing';

            // Build path
            $relativePath = "project_document/{$project_no}/Project Data/Drawings/{$full_article_number}/{$drawing_type}";
            $destinationPath = public_path($relativePath);

            // Create directory if it doesn't exist
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // Move and save
            $file->move($destinationPath, $fileName);
            $filePath = $relativePath . '/' . $fileName;

            $data->drawing_req_estimation_manager = '2';
            $data->drawing_check_procurement_manager = '1';
            $data->drawing_path = $filePath;
            $data->drawing_upload_date = now();
        }

        $data->save();

        return response()->json([
            'success' => true,
            'message' => 'File uploaded successfully!',
            'file_path' => $filePath,
            // 'full_path' => '/domains/wiptracker.360websitedemo.com/public_html/public/' . $filePath,
        ]);
    }

    public function uploadBom(Request $request){
        // Validate basic input
        $validation = $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
            'id'   => 'required|integer|exists:products_of_projects,id',
        ]);

        if (!$request->hasFile('file')) {
            return response()->json([
                'success' => false,
                'message' => 'No file uploaded!',
            ], 422);
        }

        $product = ProductsOfProjects::find($request->id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found!',
            ], 404);
        }

        $file = $request->file('file');

        if (!$file->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid file uploaded!',
            ], 422);
        }

        // Ensure directory exists
        $directory = public_path('project_document/bom_files');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        // Store file
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = 'project_document/bom_files/' . $fileName;
        $file->move($directory, $fileName);

        // Absolute path for import
        $absoluteFilePath = $directory . '/' . $fileName;

        try {

            // Import BOM with duplicate validation
            Excel::import(new ProductBOMImport($request->id), $absoluteFilePath);

        } catch (\App\Exceptions\DuplicateBOMRowException $e) {
            // Duplicate row found return validation error
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'type'    => 'duplicate_error',
            ], 422);

        } catch (\Exception $e) {

            // Unexpected error
            return response()->json([
                'success' => false,
                'message' => 'Error importing Excel file: ' . $e->getMessage(),
            ], 500);
        }

        // Update product after successful import
        $product->bom_req_estimation_manager = '2';
        $product->bom_check_procurement_manager = '1';
        $product->bom_path = $filePath;
        $product->bom_upload_date = now();
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'BOM uploaded successfully!',
            'file_path' => $filePath,
            // 'full_path' => '/domains/wiptracker.360websitedemo.com/public_html/public/' . $filePath,
        ]);
    }

    public function asBuiltPdf(){
        $drawing_req = DrawingRequest::with('projects')
            ->select('id', 'created_at', 'full_article_number', 'description', 'qty', 'editable_drawing_path')
            ->get();

        return view('as_built_pdf.index', compact('drawing_req'));
    }

    public function updateRemarks(Request $request){
        $validation = $request->validate([
            'id' => 'required|integer|exists:products_of_projects,id',
            'type' => 'required|in:bom,drawing',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $product = ProductsOfProjects::find($request->id);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found!',
            ], 404);
        }

        if ($request->type === 'bom') {
            $product->bom_remarks_by_estimation_manager = $request->remarks;
        } elseif ($request->type === 'drawing') {
            $product->drawing_remarks_by_estimation_manager = $request->remarks;
        }

        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Remarks updated successfully!',
        ]);
    }
    
    public function getRemarks(Request $request){
        $validation = $request->validate([
            'id' => 'required|integer|exists:products_of_projects,id',
            'type' => 'required|in:bom,drawing',
        ]);

        $product = ProductsOfProjects::find($request->id);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found!',
            ], 404);
        }

        $remarks = $request->type === 'bom'
            ? $product->bom_remarks_by_estimation_manager
            : $product->drawing_remarks_by_estimation_manager;

        return response()->json([
            'success' => true,
            'remarks' => $remarks,
        ]);
    }

    public function approvePDF(Request $request){
        $product = ProductsOfProjects::findOrFail($request->product_id);
        $product->is_asbuilt_drawing_pdf_approve_by_estimation_manager = 1;
        $product->save();

        return response()->json(['success' => true, 'message' => 'PDF Approved Successfully.']);
    }

    public function rejectPDF(Request $request){
        $product = ProductsOfProjects::findOrFail($request->product_id);
        $product->is_asbuilt_drawing_pdf_approve_by_estimation_manager = 2;
        $product->asbuilt_drawing_approve_reject_remarks_by_estimation_manager = $request->reason ?? null;
        $product->save();

        return response()->json(['success' => true, 'message' => 'PDF Rejected Successfully.']);
    }

    public function uploadDrawingEstimation(Request $request){
        $request->validate([
            'product_id' => 'required|exists:products_of_projects,id',
            'drawing_estimation' => 'required|mimes:pdf|max:20480', // Max 20MB, PDF only
        ]);

        if ($request->hasFile('drawing_estimation')) {
            $file = $request->file('drawing_estimation');
            $filename = 'drawing_estimation_' . time() . '.' . $file->getClientOriginalExtension();
            $product = ProductsOfProjects::findOrFail($request->product_id);
            $project = Project::findOrFail($product->project_id);
            $project_no = $project->project_no;
            $full_article_number = $product->full_article_number;
            $drawing_type = 'Estimation Manager Final Drawing';
            $relative_path = "project_document/{$project_no}/Project Data/Drawings/{$full_article_number}/{$drawing_type}";
            $destination_path = public_path($relative_path);
            if (!file_exists($destination_path)) {
                mkdir($destination_path, 0755, true);
            }
            $file->move($destination_path, $filename);
            $product->drawing_upload_by_estimation_manager = $relative_path . '/' . $filename;
            $product->save();
        }

        return redirect()->back()->with('success', 'Final PDF Drawing uploaded successfully.');
    }
}
