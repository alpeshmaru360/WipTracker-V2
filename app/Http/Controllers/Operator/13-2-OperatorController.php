<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProjectStatus;
use App\Models\Project;
use App\Models\ProjectProcessStdTime;
use App\Models\AdminHoursManagement;
use App\Models\User;
use Auth;
use DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\ProductsOfProjects;
use App\Models\AssignedProductToOperator;
use App\Events\ProductProcessStarted;
use App\Events\ProductProcessStartedEvent;
use App\Events\ProductProcessPausedEvent;
use App\Events\ProductProcessStoppedEvent;
use Log;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Str;

class OperatorController extends Controller
{
    public function generate(Request $request)
    {
        $data = $request->input('data');
        $qrCode = QrCode::size(300)->generate($data);
        return view('qrcode', compact('qrCode'));
    }

    public function dashboard()
    {
        $page_title = "Dashboard";
        if (Auth::check()) {
            $operator_id = Auth::user()->id;
        } else {
            return redirect()->route('AuthLoginForm');
        }
        $project = '';
        if ($operator_id) {
            $assigned_product = ProductsOfProjects::with('projects')->orderBy('id', 'desc')->where('operator_id', $operator_id)->where('superwisor_status', '1')->count();
            $not_assigned_product = ProductsOfProjects::with('projects')->orderBy('id', 'desc')->where('operator_id', '!=', $operator_id)->where('superwisor_status', '1')->count();
            $total_product = ProductsOfProjects::with('projects')->orderBy('id', 'desc')->where('superwisor_status', '1')->count();

            $product = AssignedProductToOperator::select('*', DB::raw('count(*) as qty'))
                ->with('projects')
                ->with('product')
                ->where('operator_id', $operator_id)
                ->groupBy('project_id', 'product_id')
                // ->orderByRaw('IF((SELECT is_priotize FROM projects WHERE projects.id = assigned_products_to_operators.project_id) = 1, 0, 1)')
                ->orderBy('id', 'desc')
                ->get();

        }
        return view('operator.dashboard', compact('product', 'page_title', 'assigned_product', 'not_assigned_product', 'total_product'));
    }

    public function product_type($product_id, $redirect)
    {
        if ($redirect == "1") {
            if (Auth::check()) {
                $operator_id = Auth::user()->id;
            } else {
                $operator_id = 0;
            }
            $page_title = "";
            $product_operator_id = "0";
            $projects = ProductsOfProjects::where('id', $product_id)->with('projects')->with('operator')->first();

            $product = AssignedProductToOperator::with('product')
                ->where('operator_id', $operator_id)
                ->where('product_id', $product_id)
                // ->orderBy('id','desc')
                ->get();
            $project_type = $product[0]->product->product_type;
            $project_type_name = DB::table('product_types')->get();
            foreach ($project_type_name as $val) {
                if ($val->project_type_name == $product[0]->product->product_type) {
                    $product_operator_id = $val->operator_id;
                }
            }

            if ($project_type) {
                return view('operator.product_type', compact('project_type', 'page_title', 'product', 'project_type_name', 'product_operator_id', 'projects'));
            }
        }
    }

    public function qr_page($project_id)
    {
        $page_title = "Project Details";

        // Fetch project details
        $projects = Project::where('id', $project_id)->with('product')->first();

        if (!$projects) {
            abort(404, 'Project not found');
        }

        // Fetch related products
        $products = ProductsOfProjects::where('project_id', $project_id)->with('projectProcessStdTimes')->with('projects')->get();

        $product_operator_id = null;
        $project_type = $products->first()->product_type ?? null;

        $project_type_name = DB::table('product_types')->get();
        foreach ($project_type_name as $val) {
            if ($val->project_type_name == $project_type) {
                $product_operator_id = $val->operator_id;
            }
        }

        return view('operator.qr_page', compact(
            'project_type',
            'page_title',
            'products',
            'project_type_name',
            'product_operator_id',
            'projects'
        ));
    }

    public function product_type_process($product_id, $project_type_name, $seq_qty)
    {
        $page_title = "";
        $projects = ProductsOfProjects::where('id', $product_id)->with('projects')->with('operator')->first();
        $process_name = ProjectProcessStdTime::where('project_type_name', 'LIKE', "%$project_type_name%")->where('product_id', $product_id)->where('order_qty', $seq_qty)->get();
        return view('operator.product_type_timer', compact('process_name', 'page_title', 'project_type_name', 'product_id', 'projects','seq_qty'));
    }

    public function update_process_status(Request $request)
    {
        $user = Auth::user();
        $startTime = now();
        $productId = $request->product_id;
        Log::info('Dispatching ProductProcessStartedEvent', [
            'productId' => $productId,
            'startTime' => $startTime
        ]);
        event(new ProductProcessStartedEvent($productId, $startTime));

        $project_type_name = $request->project_type_name;
        $project_process_name = $request->project_process_name;
        $actual_time = $request->actual_time;
        $project_id = $request->project_id;
        $product_id = $request->product_id;
        $process = ProjectProcessStdTime::where('projects_id', $project_id)
            ->where('product_id', $product_id)
            ->where('project_type_name', 'LIKE', "%$project_type_name%")
            ->where('project_process_name', 'LIKE', "%$project_process_name%")
            ->where('order_qty', $request->seqQty)
            ->first();
        if ($process) {
            $process->project_status = "1";  // Set the status to completed
            $process->project_actual_time = $actual_time;  // Store the actual time
            // $process->timer_started_at = $timer_started_at; // Save start time
            // $process->remaining_time = $remaining_time; // Save remaining time if needed
            $process->timer_ends_at = now();
            $process->save();

            return response()->json(['success' => true, 'message' => 'Status and actual time updated successfully']);
        } else {
            return response()->json(['success' => false, 'message' => 'Process not found'], 404);
        }
    }

    public function startTimer(Request $request)
    {
        $process = ProjectProcessStdTime::where('projects_id', $request->projectId)
            ->where('product_id', $request->productId)
            ->where('order_qty', $request->orderQty)
            ->where('project_process_name', $request->process_name)
            ->first();

        if (!$process) {
            return response()->json(['error' => 'Process not found'], 404);
        }

        if ($process->timer_status === 'running') {
            return response()->json([
                'message' => 'Timer is already running',
                'remainingTime' => $process->remaining_time
            ]);
        }

        $process->timer_status = 'running';
        $process->timer_started_at = now();
        $process->remaining_time = $request->remainingTime;
        $process->last_active_time = now();
        $process->save();
        
        $startTime = now();
        $endTime = (clone $startTime)->addSeconds($request->remainingTime);
        $remainingTime = $request->remainingTime;

        broadcast(new ProductProcessStartedEvent(
            $request->uniqueId,
            $request->projectId,
            $request->productId,
            $request->orderQty,
            $request->process_name,
            $startTime,
            $endTime,
            $remainingTime 
        ));

        return response()->json([
            'message' => 'Timer started successfully.',
            'startTime' => $startTime,
            'endTime' => $endTime,
            'remainingTime' => $remainingTime
        ]);
    }

    public function pauseTimer(Request $request)
    {
        $process = ProjectProcessStdTime::where('projects_id', $request->projectId)
            ->where('product_id', $request->productId)
            ->where('order_qty', $request->orderQty)
            ->first();

        if ($process) {
            $process->timer_status = 'paused';
            $process->elapsed_time = $process->elapsed_time + now()->diffInSeconds($process->timer_started_at);
            $process->remaining_time = $request->remainingTime;
            $process->save();
        }

        broadcast(new ProductProcessPausedEvent(
            $request->uniqueId,
            $request->projectId,
            $request->productId,
            $request->orderQty,
            $request->remainingTime
        ));

        return response()->json(['message' => 'Timer paused successfully.']);
    }

    public function stopTimer(Request $request)
    {
        $project_type_name = $request->project_type_name;
        $project_process_name = $request->project_process_name;
        
        $process = ProjectProcessStdTime::where('projects_id', $request->projectId)
            ->where('product_id', $request->productId)
            ->where('order_qty', $request->orderQty)
            ->where('project_type_name', 'LIKE', "%$project_type_name%")
            ->where('project_process_name', 'LIKE', "%$project_process_name%")
            ->first();

        if ($process) {
            $process->project_status = "1";  // Set the status to completed
            $process->project_actual_time = $this->convertSecondsToHMS($request->actualTime);
            $process->timer_ends_at = now();
            $process->save();
        }

        broadcast(new ProductProcessStoppedEvent(
            $request->uniqueId,
            $request->projectId,
            $request->productId,
            $request->orderQty,
            $process->project_actual_time,
            $process->project_status
        ));

        return response()->json([
            'message' => 'Timer stopped successfully.',
            'uniqueId' => $request->uniqueId,
            'actualTime' => $process->project_actual_time,
            'status' => $process->project_status
        ]);
    }

    // public function checkTimerStatus($projectId, $productId, $orderQty)
    // {
    //     $process = ProjectProcessStdTime::where('projects_id', $projectId)
    //         ->where('product_id', $productId)
    //         ->where('order_qty', $orderQty)
    //         ->first();

    //     if (!$process) {
    //         return response()->json(['status' => 'not_found']);
    //     }

    //     if ($process->timer_status === 'running') {
    //         $elapsedSeconds = now()->diffInSeconds($process->timer_started_at);
    //         $remainingTime = max(0, $process->remaining_time - $elapsedSeconds);

    //         return response()->json([
    //             'status' => 'running',
    //             'remainingTime' => $remainingTime,
    //             'startedAt' => $process->timer_started_at,
    //             'lastActiveTime' => $process->last_active_time
    //         ]);
    //     }

    //     return response()->json([
    //         'status' => $process->timer_status,
    //         'remainingTime' => $process->remaining_time
    //     ]);
    // }

    private function convertSecondsToHMS($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    public function resetTimer(Request $request)
    {
        $projectTypeName = $request->projectTypeName;
        $projectProcessName = $request->projectProcessName;
        $seqQty = $request->seqQty;
        $process = ProjectProcessStdTime::where('projects_id', $request->project_id)
            ->where('product_id', $request->product_id)
            ->where('project_type_name', 'LIKE', "%$projectTypeName%")
            ->where('project_process_name', 'LIKE', "%$projectProcessName%")
            ->where('order_qty', $seqQty)
            ->first();
        $process->project_status = '0';
        $process->project_actual_time = null;
        $process->timer_ends_at = null;
        $process->last_active_time = null;
        $process->timer_started_at = null;
        $process->timer_status = 'stopped';
        $process->remaining_time = null;
        $process->save();
        return response()->json(['success' => true, 'message' => 'Timer reset successfully.']);
    }

    public function getTimerState(Request $request) 
    {
        $process = ProjectProcessStdTime::where('projects_id', $request->projectId)
            ->where('product_id', $request->productId)
            ->where('order_qty', $request->orderQty)
            ->first();

        if ($process) {
            return response()->json([
                'status' => $process->timer_status,
                'remainingTime' => $process->remaining_time,
                'elapsedTime' => $process->elapsed_time,
                'startedAt' => $process->timer_started_at
            ]);
        }

        return response()->json(['status' => 'stopped']);
    }

    public function checkTimerStatus(Request $request)
    {
        $process = ProjectProcessStdTime::where('projects_id', $request->projectId)
            ->where('product_id', $request->productId)
            ->where('order_qty', $request->orderQty)
            ->where('project_process_name', 'LIKE' , "%$request->process_name%")
            ->first();
        if (!$process) {
            return response()->json(['error' => 'Process not found'], 404);
        }

        $status = $process->timer_status;
        $remainingTime = $process->remaining_time;

        if ($status === 'running') {
            $elapsedSeconds = now()->diffInSeconds($process->timer_started_at);
            $remainingTime = max(0, $process->remaining_time - $elapsedSeconds);
        }

        return response()->json([
            'status' => $status,
            'remainingTime' => $remainingTime,
            'startedAt' => $process->timer_started_at,
            'projectStatus' => $process->project_status,
            'actualTime' => $process->project_actual_time
        ]);
    }

    public function saveEditedPDF(Request $request)
    {
        try {
            $request->validate([
                'pdfData' => 'required|string',
                'productId' => 'required|exists:products_of_projects,id'
            ]);
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('project_document/drawing_edited_pdf_files'), $fileName);
            $filePath = 'project_document/drawing_edited_pdf_files/' . $fileName;
            $data->drawing_check_procurement_manager = '3';
            $data->editable_drawing_path = $filePath;
            $products_of_projects = ProductsOfProjects::findOrFail($request->productId);
            $products_of_projects->save();            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving PDF: ' . $e->getMessage()
            ], 500);
        }
    }
}


