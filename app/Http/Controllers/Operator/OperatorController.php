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
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\QtyOfProduct;
use App\Models\OperatorTimeTracking;

class OperatorController extends Controller
{
    public function generate(Request $request){
        $data = $request->input('data');
        $qrCode = QrCode::size(300)->generate($data);
        return view('qrcode', compact('qrCode'));
    }

    public function dashboard(){
        $page_title = "Dashboard";
        if (Auth::check()) {
            $operator_id = Auth::user()->id;
        } else {
            return redirect()->route('login');
        }
        $project = '';
        if ($operator_id) {
            $assigned_product = ProductsOfProjects::with('projects')->orderBy('id', 'desc')->where('operator_id', $operator_id)->where('superwisor_status', '1')->count();
            $not_assigned_product = ProductsOfProjects::with('projects')->orderBy('id', 'desc')->where('operator_id', '!=', $operator_id)->where('superwisor_status', '1')->count();
            $total_product = ProductsOfProjects::with('projects')->orderBy('id', 'desc')->where('superwisor_status', '1')->count();

            $product = AssignedProductToOperator::select('assigned_products_operators.*', DB::raw('count(*) as qty'))
                ->with('projects')
                ->with('product')
                // ->where('assigned_products_operators.operator_id', $operator_id)
                ->whereRaw("FIND_IN_SET(?, operator_id)", [$operator_id])
                ->groupBy('assigned_products_operators.project_id', 'assigned_products_operators.product_id')
                ->orderBy('assigned_products_operators.id', 'desc')
                ->get();

            foreach ($product as $val) {
                $rows_exist = DB::table('stock_bom_po')
                    ->where('project_id', $val->project_id)
                    ->where('product_id', $val->product_id)
                    ->exists();

                if (!$rows_exist) {
                    
                } else {
                    // Check for non-stock rows
                    $has_non_stock = DB::table('stock_bom_po')
                        ->where('project_id', $val->project_id)
                        ->where('product_id', $val->product_id)
                        ->where('select_option', '!=', 'stock')
                        ->exists();

                    if (!$has_non_stock) {
                        
                    } else {                   
                        $has_atleast_one_ready = DB::table('stock_bom_po')
                            ->where('project_id', $val->project_id)
                            ->where('product_id', $val->product_id)
                            ->where('select_option', '!=', 'stock')
                            
                            ->exists();                     
                    }
                }
            }
        }
        return view('operator.dashboard', compact('product', 'page_title', 'assigned_product', 'not_assigned_product', 'total_product'));
    }

    public function product_type($product_id, $redirect){
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
                ->whereRaw("FIND_IN_SET(?, operator_id)", [$operator_id])
                ->where('product_id', $product_id)
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

    public function qr_page($project_id){
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

    public function product_type_process($product_id, $project_type_name, $seq_qty){
        $page_title = "";
        $projects = ProductsOfProjects::where('id', $product_id)->with('projects')->with('operator')->first();

        $assignedOperatorProductQtyWiseIDs = AssignedProductToOperator::select('operator_id')
            ->where('seq_qty', $seq_qty)
            ->where('project_id', $projects['projects']->id)
            ->where('product_id', $product_id)
            ->where('seq_qty', $seq_qty)
            ->value('operator_id');
        $assignedOperatorProductQtyWiseIDs = explode(',', $assignedOperatorProductQtyWiseIDs);

        $operators_name = User::whereIn('id', $assignedOperatorProductQtyWiseIDs)->pluck('name', 'id');

        $process_name = ProjectProcessStdTime::with(['operatorTrackings' => function ($q) {
                            $q->orderBy('updated_at', 'desc')->orderBy('id', 'desc');
                        }])
                        ->where('project_type_name', '=', $project_type_name)
                        ->where('product_id', $product_id)
                        ->where('order_qty', $seq_qty)
                        ->get();
        $active_process = ProjectProcessStdTime::where('project_type_name', '=', $project_type_name)->where('product_id', $product_id)->where('order_qty', $seq_qty)->where('project_status', '1')->orderBy('id', 'desc')->first();
        $new_process = 'false';
        if ($active_process == null) {
            $active_process = ProjectProcessStdTime::where('project_type_name', '=', $project_type_name)->where('product_id', $product_id)->where('order_qty', $seq_qty)->first();
            if ($active_process == null) {
                $new_process = 'true';
            }
        }
        if ($new_process == 'true') {
            return response()->json(['status' => '1', 'message' => 'You selected wrong product type while adding the project.']);
        }
        $active_process_name = $active_process->project_process_name;
        return view('operator.product_type_timer', compact('process_name', 'page_title', 'project_type_name', 'product_id', 'projects', 'seq_qty', 'active_process_name', 'assignedOperatorProductQtyWiseIDs', 'operators_name'));
    }

    public function product_type_process_qr_code($product_id, $project_type_name, $seq_qty){
        $page_title = "";
        $projects = ProductsOfProjects::where('id', $product_id)->with('projects')->with('operator')->first();
        $assignedOperatorProductQtyWiseIDs = AssignedProductToOperator::select('operator_id')
            ->where('seq_qty', $seq_qty)
            ->where('project_id', $projects['projects']->id)
            ->where('product_id', $product_id)
            ->where('seq_qty', $seq_qty)
            ->value('operator_id');
        $assignedOperatorProductQtyWiseIDs = explode(',', $assignedOperatorProductQtyWiseIDs);

        $operators_name = User::whereIn('id', $assignedOperatorProductQtyWiseIDs)->pluck('name', 'id');

        $process_name = ProjectProcessStdTime::with(['operatorTrackings' => function ($q) {
                            $q->orderBy('updated_at', 'desc')->orderBy('id', 'desc');
                        }])
                        ->where('project_type_name', '=', $project_type_name)
                        ->where('product_id', $product_id)
                        ->where('order_qty', $seq_qty)
                        ->get();

        $active_process = ProjectProcessStdTime::where('project_type_name', '=', $project_type_name)->where('product_id', $product_id)->where('order_qty', $seq_qty)->where('project_status', '1')->orderBy('id', 'desc')->first();
        if ($active_process == null) {
            $active_process = ProjectProcessStdTime::where('project_type_name', '=', $project_type_name)->where('product_id', $product_id)->where('order_qty', $seq_qty)->first();
            if ($active_process == null) {
                $new_process = 'true';
            }
        }
             $active_process_name = $active_process->project_process_name;
             $process_name = ProjectProcessStdTime::with(['operatorTrackings' => function ($q) {
                                $q->orderBy('updated_at', 'desc')->orderBy('id', 'desc');
                            }])
                            ->where('project_type_name', '=', $project_type_name)
                            ->where('product_id', $product_id)
                            ->where('order_qty', $seq_qty)
                            ->get();
            return view('operator.product_type_timer', compact('page_title', 'project_type_name', 'product_id', 'projects', 'seq_qty','process_name','active_process','active_process_name','assignedOperatorProductQtyWiseIDs','operators_name'));
    }

    public function update_process_status(Request $request){
        $user = Auth::user();
        $startTime = now();
        $productId = $request->product_id;

        event(new ProductProcessStartedEvent($productId, $startTime));

        $project_type_name = $request->project_type_name;
        $project_process_name = $request->project_process_name;
        $actual_time = $request->actual_time;
        $project_id = $request->project_id;
        $product_id = $request->product_id;
        $process = ProjectProcessStdTime::where('projects_id', $project_id)
            ->where('product_id', $product_id)
            ->where('project_type_name', '=', $project_type_name)
            ->where('project_process_name', '=', $project_process_name)
            ->where('order_qty', $request->seqQty)
            ->first();
        if ($process) {
            $process->project_status = "1";
            $process->project_actual_time = $actual_time;
            $process->timer_ends_at = now();
            $process->save();
            return response()->json(['success' => true, 'message' => 'Status and actual time updated successfully']);
        } else {
            return response()->json(['success' => false, 'message' => 'Process not found'], 404);
        }
    }    

    public function pauseTimer(Request $request){
        try {
            DB::transaction(function () use ($request) {
                $process = ProjectProcessStdTime::where('projects_id', $request->projectId)
                    ->where('product_id', $request->productId)
                    ->where('order_qty', $request->orderQty)
                    ->where('project_process_name', $request->process_name)
                    ->first();
                if ($process) {
                    // Update specific operator tracking
                    $operatorTracking = OperatorTimeTracking::where('process_id', $process->id)
                        ->where('operator_id', $request->operatorId)
                        ->first();
                        
                        if ($operatorTracking && $operatorTracking->status === 'running') {
                            // Calculate session time and add to total
                            // $sessionTime = now()->diffInSeconds($operatorTracking->session_start);
                            $sessionTime = $operatorTracking->session_start->diffInSeconds(now());                           
                        
                        $operatorTracking->update([
                            'status' => 'paused',
                            'session_end' => now(),
                            'total_seconds' => $operatorTracking->total_seconds + $sessionTime
                        ]);
                    }

                    // Check if all operators for this process are paused/stopped
                    $activeOperators = OperatorTimeTracking::where('process_id', $process->id)
                        ->where('status', 'running')
                        ->count();

                    if ($activeOperators === 0) {
                        $process->update(['timer_status' => 'paused']);
                    }
                }
            });

            $uniqueId = $request->projectId . '-' . $request->productId . '-' . $request->orderQty . '-' . $request->operatorId . '-' . ($request->key ?? 0);
            
            broadcast(new ProductProcessPausedEvent(
                $uniqueId,
                $request->projectId,
                $request->productId,
                $request->orderQty,
                0 // remaining time - you can calculate this if needed
            ));
            
            return response()->json([
                'success' => true,
                'message' => 'Timer paused successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to pause timer: ' . $e->getMessage()
            ], 500);
        }
    }

    public function stopTimer(Request $request){
        try {
            $allStoppedProcess = "false"; 
            DB::transaction(function () use ($request, &$allStoppedProcess) {
                $process = ProjectProcessStdTime::where('projects_id', $request->projectId)
                    ->where('product_id', $request->productId)
                    ->where('order_qty', $request->orderQty)
                    ->where('project_process_name', $request->project_process_name)
                    ->first();


                if ($process) {
                    // Update specific operator tracking
                    $operatorTracking = OperatorTimeTracking::where('process_id', $process->id)
                        ->where('operator_id', $request->operatorId)
                        ->first();
                    if ($operatorTracking) {
                        // Calculate final time if still running
                        if ($operatorTracking->status === 'running' && $operatorTracking->session_start) {
                            $sessionTime = $operatorTracking->session_start->diffInSeconds(now());
                            $operatorTracking->total_seconds += $sessionTime;
                        }
                        
                        $operatorTracking->update([
                            'status' => 'stopped',
                            'session_end' => now()
                        ]);
                    }
                    $trackingArray = json_decode($process->operators_time_tracking, true) ?? [];
                    foreach ($trackingArray as &$op) {
                        if ($op['id'] == $request->operatorId) {
                            $op['status'] = 'stopped';
                            $op['ended_at'] = now()->toDateTimeString();
                            $op['total_time'] = $operatorTracking->total_seconds ?? 0;
                        }
                    }
                    $process->update([
                        'operators_time_tracking' => json_encode($trackingArray)
                    ]);
                    $trackingArray = json_decode($process->operators_time_tracking, true) ?? [];
                    $allStopped = collect($trackingArray)->every(fn($op) => $op['status'] === 'stopped');                    
                    if ($allStopped) {
                        // Calculate total time from all operators                        
                        $totalTime = collect($trackingArray)->sum(fn($op) => $op['total_time'] ?? 0);
                        
                        $process->update([
                            'project_status' => 1,
                            'project_actual_time' => gmdate("H:i:s", $totalTime),
                            'elapsed_time' => $totalTime,
                            'timer_ends_at' => now(),
                            'timer_status' => 'completed'
                        ]);
                        $allStoppedProcess = "true";
                    }
                }
            });
            // Check remaining processes and update product status
            $remainingProcess = ProjectProcessStdTime::where('projects_id', $request->projectId)
                ->where('product_id', $request->productId)
                ->where('project_status', '0')
                ->count();                
            $allProcessAssembled = "false";
            if ($remainingProcess == 0) {
                $product = ProductsOfProjects::where('id', $request->productId)->first();
                if ($product) {
                    $product->all_process_assembled = "1";
                    $product->save();
                    $allProcessAssembled = "true";
                }
            }
            // Check qty-wise status
            $statusCheck = ProjectProcessStdTime::where('projects_id', $request->projectId)
                ->where('product_id', $request->productId)
                ->where('project_status', '0')
                ->where('order_qty', $request->orderQty)
                ->count();                
            if ($statusCheck == 0) {
                $remainingProcessQtyWise = QtyOfProduct::where('project_id', $request->projectId)
                    ->where('product_id', $request->productId)
                    ->where('qty_number', $request->orderQty)
                    ->first();
                if ($remainingProcessQtyWise) {
                    $remainingProcessQtyWise->is_qty_product_assembled = "1";
                    $remainingProcessQtyWise->save();
                }
            }

            $uniqueId = $request->projectId . '-' . $request->productId . '-' . $request->orderQty . '-' . $request->operatorId . '-' . ($request->key ?? 0);
            broadcast(new ProductProcessStoppedEvent(
                $uniqueId,
                $request->projectId,
                $request->productId,
                $request->orderQty,
                $process->project_actual_time ?? '00:00:00',
                $process->project_status ?? "0"
            ));

            return response()->json([
                'success' => true,
                'message' => 'Timer stopped successfully',
                'uniqueId' => $uniqueId,
                'actualTime' => $process->project_actual_time ?? '00:00:00',
                'status' => $process->project_status ?? "0",
                'all_process_assembled' => $allProcessAssembled,
                'allStoppedProcess' => $allStoppedProcess,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to stop timer: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateProcessStatus(Request $request){
        $request->validate([
            'id' => 'required|integer|exists:project_process_std_time,id',
        ]);
        
        // Update only the project_status to 1 for the given id in project_process_std_time table
        $updated = DB::table('project_process_std_time')
        ->where('id', $request->id)
        ->update(['timer_status' => 'completed']);
        
        if ($updated) {
            return response()->json(['success' => true, 'message' => 'Process status updated to completed']);
        } else {
            return response()->json(['success' => false, 'message' => 'Update failed'], 500);
        }
    }

    public function startTimer(Request $request){
        try {
            DB::transaction(function () use ($request) {
                $process = ProjectProcessStdTime::lockForUpdate()
                    ->where('projects_id', $request->projectId)
                    ->where('product_id', $request->productId)
                    ->where('order_qty', $request->orderQty)
                    ->where('project_process_name', $request->process_name)
                    ->first();

                if (!$process) {
                    throw new \Exception('Process not found');
                }
                // Create or update operator tracking record
                $operatorTracking = OperatorTimeTracking::updateOrCreate(
                    [
                        'process_id' => $process->id,
                        'operator_id' => $request->operatorId
                    ],
                    [
                        'status' => 'running',
                        'session_start' => now(),
                        'session_end' => null
                    ]
                );
                // Update process status if not already running
                if ($process->timer_status !== 'running') {
                    $process->update([
                        'timer_status' => 'running',
                        'timer_started_at' => now(),
                        'project_status' => '0'
                    ]);
                }
                if (is_null($process->timer_started_at)) {
                    $process->timer_started_at = now();
                    $process->timer_status = 'running';
                    $process->save();
                } 
            });
            // Generate consistent unique_id that matches frontend
            $uniqueId = $request->projectId . '-' . $request->productId . '-' . $request->orderQty . '-' . $request->operatorId . '-' . $request->key;

            broadcast(new ProductProcessStartedEvent(
                $uniqueId,
                $request->projectId,
                $request->productId,
                $request->orderQty,
                $request->process_name,
                now()
            ));            
            return response()->json([
                'success' => true,
                'message' => 'Timer started successfully',
                'unique_id' => $uniqueId
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to start timer: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getTimerState(Request $request){
        $processes = ProjectProcessStdTime::where('projects_id', $request->projectId)
            ->where('product_id', $request->productId)
            ->where('order_qty', $request->orderQty)
            ->get();

        if ($processes->isEmpty()) {
            return response()->json(['timer_states' => []]);
        }

        $timerStates = [];
        
        foreach ($processes as $key => $process) {
            // Get all operator tracking records for this specific process
            $operatorTrackings = OperatorTimeTracking::where('process_id', $process->id)->get();

            if ($operatorTrackings->isEmpty()) {
                // If no operator tracking exists, create default states for assigned operators
                $assignedOperators = json_decode($process->assigned_operators ?? '[]', true);
                
                foreach ($assignedOperators as $operatorId) {
                    $uniqueId = $process->projects_id . '-' . $process->product_id . '-' . $process->order_qty . '-' . $operatorId . '-' . $key;
                    
                    $timerStates[] = [
                        'unique_id' => $uniqueId,
                        'operator_id' => $operatorId,
                        'process_name' => $process->project_process_name,
                        'status' => 'stopped',
                        'elapsed_time' => 0,
                        'formatted_time' => '00:00:00',
                        'session_start' => null,
                        // Button states for stopped/new timer
                        'show_start_btn' => true,
                        'show_pause_btn' => false,
                        'show_stop_btn' => false,
                    ];
                }
            } else {
                foreach ($operatorTrackings as $tracking) {
                    // Generate unique_id that matches your frontend format
                    $uniqueId = $process->projects_id . '-' . $process->product_id . '-' . $process->order_qty . '-' . $tracking->operator_id . '-' . $key;

                    $timerStates[] = [
                        'unique_id' => $uniqueId,
                        'operator_id' => $tracking->operator_id,
                        'process_name' => $process->project_process_name,
                        'status' => $tracking->status,
                        'elapsed_time' => $tracking->getCurrentElapsedTime(),
                        'formatted_time' => $tracking->getFormattedTime(),
                        'session_start' => $tracking->session_start?->toISOString(),
                        // Button states based on current status
                        'show_start_btn' => in_array($tracking->status, ['stopped', 'paused']),
                        'show_pause_btn' => $tracking->status === 'running',
                        'show_stop_btn' => in_array($tracking->status, ['running', 'paused']),
                        'hide_all_btns'  => $tracking->status === 'stopped',
                    ];
                }
            }
        }

        return response()->json([
            'timer_states' => $timerStates,
            'serverTime' => now()->timestamp * 1000
        ]);
    }

    private function findOperatorData($operators, $operatorId){
        if (!is_array($operators)) {
            return null;
        }        
        foreach ($operators as $operator) {
            // Check both string and integer comparison
            if (isset($operator['id']) && 
                ($operator['id'] == (string)$operatorId || $operator['id'] == (int)$operatorId)) {
                return $operator;
            }            
            // Also check for operator_id key if id doesn't exist
            if (isset($operator['operator_id']) && 
                ($operator['operator_id'] == (string)$operatorId || $operator['operator_id'] == (int)$operatorId)) {
                return $operator;
            }
        }
        return null;
    }

    private function calculateLiveTime($operatorData, $processStatus){
        if (!is_array($operatorData)) {
            return 0;
        }        
        $operatorStatus = $operatorData['status'] ?? 'stopped';        
        if (($operatorStatus === 'running') && !empty($operatorData['session_start'])) {
            try {
                $sessionStart = Carbon::parse($operatorData['session_start']);
                $liveTime = ($operatorData['total_time'] ?? 0) + $sessionStart->diffInSeconds(now());
                return $liveTime;
            } catch (\Exception $e) {
                return $operatorData['total_time'] ?? 0;
            }
        }        
        return $operatorData['total_time'] ?? 0;
    } 

    private function isResetState(array $operators): bool{
        foreach ($operators as $op) {
            if ($op['status'] !== null || $op['total_time'] !== null || $op['session_start'] !== null) {
                return false;
            }
        }
        return true; // all reset
    }

    private function convertSecondsToHMS($seconds){
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $remainingSeconds = $seconds % 60;
        
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $remainingSeconds);
    }

    public function resetTimer(Request $request){
        $projectTypeName = $request->projectTypeName;
        $projectProcessName = $request->projectProcessName;
        $seqQty = $request->seqQty;
        $process = ProjectProcessStdTime::where('projects_id', $request->project_id)
            ->where('product_id', $request->product_id)
            ->where('project_type_name', '=', $projectTypeName)
            ->where('project_process_name', '=', $projectProcessName)
            ->where('order_qty', $seqQty)
            ->first();
        $process->project_status = '0';
        $process->project_actual_time = null;
        $process->timer_started_at = null;
        $process->timer_current_time = null;
        $process->timer_ends_at = null;
        $process->remaining_time = null;
        $process->elapsed_time = null;
        $process->timer_status = 'stopped';

        $operators = json_decode($process->operators_time_tracking, true) ?? [];

        foreach ($operators as &$op) {
            $op['status']     = null;
            $op['total_time'] = null;
            $op['started_at'] = null;
            $op['ended_at']   = null;
            $op['session_start']   = null;
        }

        unset($op);

        $process->operators_time_tracking = json_encode($operators);
        $process->save();
        $process->refresh();

        $process->forceFill([
            'project_status'          => '0',
            'project_actual_time'     => null,
            'timer_started_at'        => null,
            'timer_current_time'      => null,
            'timer_ends_at'           => null,
            'remaining_time'          => null,
            'elapsed_time'            => null,
            'timer_status'            => 'stopped',
            'operators_time_tracking' => json_encode($operators),
        ])->save();
    
        return response()->json(['success' => true, 'message' => 'Timer reset successfully.']);
    }

    public function saveEditedPDF(Request $request){
        try {
            $request->validate([
                'pdfFile' => 'required|file|mimes:pdf|max:20480',
                'product_id' => 'required|integer|exists:products_of_projects,id'
            ]);

            $file = $request->file('pdfFile');
            $fileName = 'edited_pdf_' . time() . '.pdf';

            $product = ProductsOfProjects::findOrFail($request->product_id);
            $project = \App\Models\Project::findOrFail($product->project_id);
            $project_no = $project->project_no;
            $full_article_number = $product->full_article_number;
            $drawing_type = 'Operators As-Built Drawing';
            $relativePath = "project_document/{$project_no}/Project Data/Drawings/{$full_article_number}/{$drawing_type}";
            $destinationPath = public_path($relativePath);
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $file->move($destinationPath, $fileName);
            $filePath = $relativePath . '/' . $fileName;
            $updateData = [
                'editable_drawing_path' => $filePath,
            ];

            if ($product->is_asbuilt_drawing_pdf_approve_by_estimation_manager == 2 && $product->is_asbuilt_drawing_pdf_approve_by_production_superwisor != 2) {
                $updateData['is_asbuilt_drawing_pdf_approve_by_estimation_manager'] = 3;
                $updateData['asbuilt_drawing_approve_reject_remarks_by_estimation_manager'] = null;
            } elseif ($product->is_asbuilt_drawing_pdf_approve_by_production_superwisor == 2 && $product->is_asbuilt_drawing_pdf_approve_by_estimation_manager != 2) {
                $updateData['is_asbuilt_drawing_pdf_approve_by_production_superwisor'] = 3;
                $updateData['asbuilt_drawing_approve_reject_remarks_by_production_superwisor'] = null;
            } elseif ($filePath) {
                $updateData['is_asbuilt_drawing_pdf_approve_by_estimation_manager'] = 3;
                $updateData['asbuilt_drawing_approve_reject_remarks_by_estimation_manager'] = null;
                $updateData['is_asbuilt_drawing_pdf_approve_by_production_superwisor'] = 3;
                $updateData['asbuilt_drawing_approve_reject_remarks_by_production_superwisor'] = null;
            }
            // Update product record
            $product->update($updateData);
            return response()->json([
                'success' => true,
                'message' => 'Edited PDF saved successfully!',
                'filePath' => asset($filePath),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    public function saveCapturedPhoto(Request $request){
        try {
            $request->validate([
                'photo' => 'required|image|max:10240', // Max 10MB
                'project_no' => 'required|string',
                'project_id' => 'required|integer',
                'product_id' => 'required|integer',
                'seq_qty' => 'required|integer',
                'product_type' => 'required|string',
                'project_process_name' => 'required|string' // Add process name to validation
            ]);
            $projectNo = $request->input('project_no');
            $productType = $request->input('product_type');
            $productId = $request->input('product_id'); // Get product_id
            $seqQty = $request->input('seq_qty'); // Get seq_qty
            $projectProcessName = $request->input('project_process_name'); // Get process name

            // Define directory structure
            $baseDir = public_path('operator');
            $processDir = public_path('operator/product_type_process');
            $captureDir = public_path('operator/product_type_process/capture_imgs');
            $projectDir = public_path("operator/product_type_process/capture_imgs/{$projectNo}");

            // Create directories if they don't exist
            if (!file_exists($baseDir)) {
                mkdir($baseDir, 0777, true);
            }
            if (!file_exists($processDir)) {
                mkdir($processDir, 0777, true);
            }
            if (!file_exists($captureDir)) {
                mkdir($captureDir, 0777, true);
            }
            if (!file_exists($projectDir)) {
                mkdir($projectDir, 0777, true);
            }
            // Generate filename with product_type, product_id, seq_qty, process_name, date, and time
            $date = Carbon::now()->format('d-m-Y'); // Format: DD-MM-YYYY
            $time = Carbon::now()->format('H:i:s'); // Format: HH:MM:SS (24-hour)
            $extension = $request->file('photo')->getClientOriginalExtension();
            $safeProductType = preg_replace('/[^A-Za-z0-9\- ]/', '', $productType);
            $safeProcessName = preg_replace('/[^A-Za-z0-9\- ]/', '', $projectProcessName); // Sanitize process name
            // Base filename with product_id, seq_qty, and process_name included
            $baseFilename = "{$safeProductType}_{$productId}_{$seqQty}_{$safeProcessName}_{$time}__{$date}";
            $filename = $baseFilename . '.' . $extension;
            $filePath = "operator/product_type_process/capture_imgs/{$projectNo}/{$filename}";
            $fullPath = public_path($filePath);
            // Check if file exists and add counter if necessary
            $counter = 1;
            while (file_exists($fullPath)) {
                $filename = "{$baseFilename}-{$counter}.{$extension}";
                $filePath = "operator/product_type_process/capture_imgs/{$projectNo}/{$filename}";
                $fullPath = public_path($filePath);
                $counter++;
            }
            // Save the photo
            $request->file('photo')->move($projectDir, $filename);

            return response()->json([
                'success' => true,
                'message' => 'Photo saved successfully',
                'filePath' => asset($filePath),
                'filename' => $filename
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving photo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getProcessPhotos(Request $request) {
        try {
            $request->validate([
                'project_no' => 'required|string',
                'product_id' => 'required|integer',
                'seq_qty' => 'required|integer',
                'product_type' => 'required|string',
                'project_process_name' => 'required|string'
            ]);

            $projectNo = $request->input('project_no');
            $productId = $request->input('product_id');
            $seqQty = $request->input('seq_qty');
            $productType = $request->input('product_type');
            $projectProcessName = $request->input('project_process_name');

            // Sanitize inputs for filename matching
            $safeProductType = preg_replace('/[^A-Za-z0-9\- ]/', '', $productType);
            $safeProcessName = preg_replace('/[^A-Za-z0-9\- ]/', '', $projectProcessName);

            // Define the directory where photos are stored
            $projectDir = public_path("operator/product_type_process/capture_imgs/{$projectNo}");

            // Check if the directory exists
            if (!file_exists($projectDir)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No photos directory found for this project.',
                    'photos' => []
                ]);
            }

            // Get all files in the directory
            $files = scandir($projectDir);
            $photos = [];

            // Create the pattern prefix and escape special characters
            $prefix = "{$safeProductType}_{$productId}_{$seqQty}_{$safeProcessName}_";
            $escapedPrefix = preg_quote($prefix, '/'); // Escape special characters for regex

            // Pattern to match the filename
            $pattern = "/^{$escapedPrefix}/";

            // Filter files that match the pattern
            foreach ($files as $file) {
                if (in_array(pathinfo($file, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'])) {
                    // Check if the filename matches the pattern
                    if (preg_match($pattern, $file)) {
                        $photos[] = [
                            'filename' => $file,
                            'url' => asset("operator/product_type_process/capture_imgs/{$projectNo}/{$file}")
                        ];
                    }
                }
            }

            return response()->json([
                'success' => true,
                'photos' => $photos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching photos: ' . $e->getMessage(),
                'photos' => []
            ], 500);
        }
    }
    
    public function getPoNumbers(Request $request){
        // Expecting: { items: [ { description, articleNo, projectNo, productId }, … ] }
        $items = $request->input('items', []);

        $results = [];

        foreach ($items as $item) {
            $description = $item['description'] ?? '';
            $articleNo   = $item['articleNo']   ?? '';
            $projectNo   = $item['projectNo']   ?? '';
            $productId   = $item['productId']   ?? '';

            // Resolve project-ID once (null if it doesn’t exist)
            $projectId = null;
            if ($projectNo !== '') {
                $projectId = DB::table('projects')
                    ->where('project_no', $projectNo)
                    ->value('id');   // <-- first() or value() – not get()
            }

            $query = DB::table('stock_bom_po');

            // Always filter by description + article when they are present
            if ($description !== '') {
                // $query->where('description', 'LIKE', '%' . $description . '%');
                $query->where('description', '=', $description);
            }
            if ($articleNo !== '') {
                // $query->where('article_no', 'LIKE', '%' . $articleNo . '%');
                $query->where('article_no', '=', $articleNo);
            }

            // Add project_id / product_id filters only if they are available
            if ($projectId !== null) {
                $query->where('project_id', $projectId);
            }
            if ($productId !== '') {
                $query->where('product_id', $productId);
            }

            // Newest PO for that exact combination
            $poNo = $query->orderByDesc('po_no')->value('po_no');

            $results[] = [
                'description' => $description,
                'articleNo'   => $articleNo,
                'projectNo'   => $projectNo,
                'productId'   => $productId,
                'po'          => $poNo ?? ''
            ];
        }

        return response()->json($results);
    }
}
