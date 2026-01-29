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
                    // Scenario: No rows exist
                    $val->mrf_date_auth = false; // Disable button
                } else {
                    // Check for non-stock rows
                    $has_non_stock = DB::table('stock_bom_po')
                                     ->where('project_id', $val->project_id)
                                     ->where('product_id', $val->product_id)
                                     ->where('select_option', '!=', 'stock')
                                     ->exists();

                    if (!$has_non_stock) {
                        // Scenario 1: All rows are 'stock'
                        $val->mrf_date_auth = true; // Enable button
                    } else {
                        // Non-stock rows exist (partial or new_order)
                        // Check if ALL rows with non-stock have mrf_ready_date set (not null)
                        $non_stock_count = DB::table('stock_bom_po')
                                           ->where('project_id', $val->project_id)
                                           ->where('product_id', $val->product_id)
                                           ->where('select_option', '!=', 'stock')
                                           ->count();

                        $non_stock_with_mrf_date = DB::table('stock_bom_po')
                                                  ->where('project_id', $val->project_id)
                                                  ->where('product_id', $val->product_id)
                                                  ->where('select_option', '!=', 'stock')
                                                  ->whereNotNull('mrf_ready_date')
                                                  ->count();

                        // Scenarios 2 & 4: Enable button if ALL non-stock rows have mrf_ready_date
                        // Scenario 3: Disable button if ANY non-stock row has mrf_ready_date null
                        $val->mrf_date_auth = ($non_stock_count === $non_stock_with_mrf_date);
                    }
                }
            }
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
                // ->whereIn('operator_id', $operator_id)
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

        $assignedOperatorProductQtyWiseIDs = AssignedProductToOperator::select('operator_id')
                                                ->where('seq_qty', $seq_qty)
                                                ->where('project_id', $projects['projects']->id)
                                                ->where('product_id', $product_id)
                                                ->where('seq_qty', $seq_qty)
                                                ->value('operator_id');
        $assignedOperatorProductQtyWiseIDs = explode(',', $assignedOperatorProductQtyWiseIDs);
       
        $operators_name = User::whereIn('id', $assignedOperatorProductQtyWiseIDs)->pluck('name', 'id'); 

        $process_name = ProjectProcessStdTime::where('project_type_name', '=', $project_type_name)->where('product_id', $product_id)->where('order_qty', $seq_qty)->get();
        $active_process = ProjectProcessStdTime::where('project_type_name', '=', $project_type_name)->where('product_id', $product_id)->where('order_qty', $seq_qty)->where('project_status', '1')->orderBy('id', 'desc')->first();
        $new_process = 'false';

        if ($active_process == null) {
            $active_process = ProjectProcessStdTime::where('project_type_name', '=', $project_type_name)->where('product_id', $product_id)->where('order_qty', $seq_qty)->first();
            if($active_process == null){
                $new_process = 'true';
            }
        }
        if($new_process == 'true'){
            return response()->json(['status'=>'1','message'=>'You selected wrong product type while adding the project.']);
        }
        $active_process_name = $active_process->project_process_name;
        return view('operator.product_type_timer', compact('process_name', 'page_title', 'project_type_name', 'product_id', 'projects', 'seq_qty', 'active_process_name','assignedOperatorProductQtyWiseIDs','operators_name'));
    }

    public function update_process_status(Request $request)
    {
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

    // public function startTimer(Request $request)
    // {
    //     $process = ProjectProcessStdTime::where('projects_id', $request->projectId)
    //         ->where('product_id', $request->productId)
    //         ->where('order_qty', $request->orderQty)
    //         ->where('project_process_name', $request->process_name)
    //         ->first();

    //     if ($process) {
    //         if ($process->timer_status === 'paused') {
    //             // Resume the timer without resetting elapsed time
    //             $process->timer_status = 'running';
    //             $process->timer_current_time = now(); // IMPORTANT: Always update current time when resuming
    //         } else {
    //             // Starting fresh
    //             $process->timer_started_at = now();
    //             $process->timer_current_time = now();
    //             $process->elapsed_time = 0; // Reset elapsed time only for new session
    //             $process->timer_status = 'running';
    //         }

    //         //
    //             $operators = json_decode($process->operators_time_tracking, true) ?? [];

    //             $found = false;

    //             foreach ($operators as &$op) {
    //                 if ($op['id'] == $request->operatorId) {
    //                     $op['status'] = 'running';
    //                     $op['started_at'] = now()->toDateTimeString();
    //                     $found = true;
    //                     break;
    //                 }
    //             }

    //             // If operator not yet in JSON, push new entry
    //             if (!$found) {
    //                 $operators[] = [
    //                     'id' => $request->operatorId,
    //                     'status' => 'running',
    //                     'total_time' => 0,
    //                     'started_at' => now()->toDateTimeString(),
    //                     'ended_at' => null
    //                 ];
    //             }

    //             $process->operators_time_tracking = json_encode($operators);
    //         //
    //         $process->save();

    //         $remainingTime = $process->calculateRemainingTime();
    //         $elapsedTime = $process->calculateElapsedTime();

    //         broadcast(new ProductProcessStartedEvent(
    //             $request->uniqueId,
    //             $request->projectId,
    //             $request->productId,
    //             $request->orderQty,
    //             $request->process_name,
    //             $process->timer_started_at,
    //             now()->addSeconds($remainingTime),
    //             $remainingTime
    //         ));

    //         return response()->json([
    //             'message' => 'Timer started successfully.',
    //             'startTime' => $process->timer_started_at,
    //             'endTime' => now()->addSeconds($remainingTime),
    //             'remainingTime' => $remainingTime,
    //             'elapsedTime' => $elapsedTime,
    //             'operators' => $operators
    //         ]);
    //     }

    //     return response()->json(['message' => 'Process not found.'], 404);
    // }

    public function startTimer(Request $request)
{
    $process = ProjectProcessStdTime::where('projects_id', $request->projectId)
        ->where('product_id', $request->productId)
        ->where('order_qty', $request->orderQty)
        ->where('project_process_name', $request->process_name)
        ->first();

    if (!$process) {
        return response()->json(['message' => 'Process not found.'], 404);
    }

    // ✅ Only set process timer if it's never started before
    if ($process->timer_status === 'stopped' || $process->timer_status === null) {
        $process->timer_started_at   = now();
        $process->timer_current_time = now();
        $process->elapsed_time       = 0;
        $process->timer_status       = 'running';
    }
    elseif ($process->timer_status === 'paused') {
        // Resume process timer
        $process->timer_status       = 'running';
        $process->timer_current_time = now();
    }
    // ❌ If already running → do NOT reset process timer

    // ✅ Handle operator JSON
    $operators = json_decode($process->operators_time_tracking, true) ?? [];
    $found = false;

    foreach ($operators as &$op) {
        if ($op['id'] == $request->operatorId) {
            // If already running, don’t restart
            if ($op['status'] !== 'running') {
                $op['status']     = 'running';
                $op['started_at'] = now()->toDateTimeString();
                // Keep old total_time (don’t reset to 0)
            }
            $found = true;
            break;
        }
    }
    unset($op);

    if (!$found) {
        $operators[] = [
            'id'         => $request->operatorId,
            'status'     => 'running',
            'total_time' => 0,
            'started_at' => now()->toDateTimeString(),
            'ended_at'   => null,
        ];
    }

    $process->operators_time_tracking = json_encode($operators);
    $process->save();

    // Process-level remaining/elapsed time
    $remainingTime = $process->calculateRemainingTime();
    $elapsedTime   = $process->calculateElapsedTime();

    broadcast(new ProductProcessStartedEvent(
        $request->uniqueId,
        $request->projectId,
        $request->productId,
        $request->orderQty,
        $request->process_name,
        $process->timer_started_at,
        now()->addSeconds($remainingTime),
        $remainingTime
    ));

    return response()->json([
        'message'       => 'Timer started successfully.',
        'startTime'     => $process->timer_started_at,
        'endTime'       => now()->addSeconds($remainingTime),
        'remainingTime' => $remainingTime,
        'elapsedTime'   => $elapsedTime,
        'operators'     => $operators,
    ]);
}

    public function pauseTimer(Request $request)
    {
        // Get the client-side timestamp when the pause was initiated
        $clientPauseTime = $request->has('clientPauseTime')
            ? Carbon::createFromTimestampMs($request->clientPauseTime)
            : now();

        $process = ProjectProcessStdTime::where('projects_id', $request->projectId)
            ->where('product_id', $request->productId)
            ->where('order_qty', $request->orderQty)
            ->where('project_process_name', $request->process_name)
            ->first();

            if ($process && $process->timer_status === 'running') {
                // Calculate elapsed time since last start using the client pause time
            $timerStartedAt = new \DateTime($process->timer_current_time);
            $currentSessionElapsed = $clientPauseTime->getTimestamp() - $timerStartedAt->getTimestamp();
            // Update total elapsed time
            $process->elapsed_time = ($process->elapsed_time ?? 0) + max(0, $currentSessionElapsed);
            // Update status to paused
            $process->timer_status = 'paused';
            //
                $operators = json_decode($process->operators_time_tracking, true) ?? [];
                foreach ($operators as &$op) {
                    if ($op['id'] == $request->operatorId) {
                        $op['status'] = 'paused';
                        $op['ended_at'] = now()->toDateTimeString();
                        // accumulate time
                        if (!empty($op['started_at'])) {
                            $op['total_time'] = ($op['total_time'] ?? 0) + 
                            (strtotime(now()) - strtotime($op['started_at']));
                        }
                        break;
                    }
                }
                $process->operators_time_tracking = json_encode($operators);
                //
                $process->save();
                // Calculate remaining time based on the updated elapsed time
                $remainingTime = $process->calculateRemainingTime();
                
                broadcast(new ProductProcessPausedEvent(
                    $request->uniqueId,
                $request->projectId,
                $request->productId,
                $request->orderQty,
                $remainingTime
            ));
            
            return response()->json([
                'message' => 'Timer paused successfully.',
                'startTime' => $process->timer_started_at,
                'endTime' => $clientPauseTime->addSeconds($remainingTime),
                'remainingTime' => $remainingTime,
                'elapsedTime' => $process->elapsed_time,
                'serverTime' => now()->timestamp * 1000 // Send server time for synchronization
            ]);
        }
        return response()->json(['message' => 'Process not found or not running.'], 404);
    }

    public function stopTimer(Request $request)
    {
        $process = ProjectProcessStdTime::where('projects_id', $request->projectId)
            ->where('product_id', $request->productId)
            ->where('order_qty', $request->orderQty)
            ->where('project_type_name', '=', $request->project_type_name)
            ->where('project_process_name', '=', $request->project_process_name)
            ->first();

        if ($process) {
            //
                $operators = json_decode($process->operators_time_tracking, true) ?? [];
                foreach ($operators as &$op) {
                    if ($op['id'] == $request->operatorId) {
                        $op['status'] = 'completed';
                        $op['ended_at'] = now()->toDateTimeString();
                        if (!empty($op['started_at'])) {
                            $op['total_time'] = ($op['total_time'] ?? 0) + 
                                (strtotime(now()) - strtotime($op['started_at']));
                        }
                        break;
                    }
                }
                $process->operators_time_tracking = json_encode($operators);
                $allCompleted = collect($operators)->every(fn($op) => $op['status'] === 'completed');

            //
            if ($allCompleted) {
                $process->project_status = "1";  // Mark as completed
                // $process->project_actual_time = $this->convertSecondsToHMS($request->actualTime);
                //$process->elapsed_time = $request->actualTime;
                $totalTime = collect($operators)->sum('total_time');
                $process->elapsed_time       = $totalTime;
                $process->project_actual_time = $this->convertSecondsToHMS($totalTime);

                $process->timer_ends_at = now();
                $process->timer_status = 'completed';
            }

            $process->save();

            broadcast(new ProductProcessStoppedEvent(
                $request->uniqueId,
                $request->projectId,
                $request->productId,
                $request->orderQty,
                $process->project_actual_time,
                $process->project_status
            ));
        }

        $remanining_process = ProjectProcessStdTime::where('projects_id', $request->projectId)
            ->where('product_id', $request->productId)
            ->where('project_status', '=', '0')
            ->count();

        $all_process_assembled = "false";

        if ($remanining_process == 0) {
            $product = ProductsOfProjects::where('id', $request->productId)->first();
            $product->all_process_assembled = "1";
            $product->save();
            $all_process_assembled = "true";
        }

        $status_check = ProjectProcessStdTime::where('projects_id', $request->projectId)
            ->where('product_id', $request->productId)
            ->where('project_status', '=', '0')
            ->where('order_qty', $request->orderQty)
            ->count();

        if ($status_check == 0) {
            $remanining_process_qty_wise = QtyOfProduct::where('project_id', $request->projectId)
                ->where('product_id', $request->productId)
                ->where('qty_number', $request->orderQty)
                ->first();

            if ($remanining_process_qty_wise) {
                $remanining_process_qty_wise->is_qty_product_assembled = "1";
                $remanining_process_qty_wise->save();
            }
        }

        return response()->json([
            'message' => 'Timer stopped successfully.',
            'uniqueId' => $request->uniqueId,
            'actualTime' => $process->project_actual_time ?? '00:00:00',
            'status' => $process->project_status ?? "0",
            'all_process_assembled' => $all_process_assembled
        ]);
    }

    // public function getTimerState(Request $request)
    // {
    //     $processes = ProjectProcessStdTime::where('projects_id', $request->projectId)
    //         ->where('product_id', $request->productId)
    //         ->where('order_qty', $request->orderQty)
    //         ->get();

    //     $timerStates = [];

    //     foreach ($processes as $key => $process) {
    //         $assignedOperatorProductQtyWiseIDs = AssignedProductToOperator::select('operator_id')
    //                                                 ->where('seq_qty', $request->orderQty)
    //                                                 ->where('project_id', $request->projectId)
    //                                                 ->where('product_id', $request->productId)
    //                                                 ->value('operator_id');
                
    //         $assignedOperatorProductQtyWiseIDs = explode(',', $assignedOperatorProductQtyWiseIDs);
                
    //         foreach ($assignedOperatorProductQtyWiseIDs as $key => $operatorId) {
    //                 // $uniqueId = $process->projects_id . '-' . $process->product_id . '-' . $process->order_qty . '-' . $key;
    //                 $uniqueId = $process->projects_id . '-' . $process->product_id . '-' . $process->order_qty . '-' . $operatorId . '-' . $key;
                    
    //             if ($process->timer_status !== 'completed') {
    //                 // Calculate elapsed and remaining time
    //                 $elapsedTime = $process->calculateElapsedTime();
    //                 $remainingTime = $process->calculateRemainingTime();

    //                 // If timer is running and has completed
    //                 if ($process->timer_status === 'running' && $remainingTime <= 0) {
    //                     // Auto stop the timer
    //                     $process->timer_status = 'completed';
    //                     $process->project_status = "1";
    //                     $process->timer_ends_at = now();
    //                     $process->elapsed_time = $process->process_std_time * 3600; // Set to full time
    //                     $process->project_actual_time = $this->convertSecondsToHMS($elapsedTime);
    //                     $process->save();
                        
    //                     // Broadcast the auto-stop event
    //                     broadcast(new ProductProcessStoppedEvent(
    //                         $uniqueId,
    //                         $request->projectId,
    //                         $request->productId,
    //                         $request->orderQty,
    //                         $process->project_actual_time,
    //                         $process->project_status
    //                     ));
    //                 }
    //             } else {
    //                 $elapsedTime = $process->elapsed_time;
    //                 $remainingTime = 0;
    //             }

    //             $operators = json_decode($process->operators_time_tracking, true) ?? [];
    //             $operatorData = collect($operators)->firstWhere('id', (string) $operatorId);
    //             // if ($request->operatorId == $operatorId && $operatorData) {
    //             if ($operatorData) {
    //                 if ($operatorData['status'] === 'running' && !empty($operatorData['started_at'])) {
    //                     $startedAt = Carbon::parse($operatorData['started_at']);
    //                     $liveTime = ($operatorData['total_time'] ?? 0) + $startedAt->diffInSeconds(now());
    //                 } else {
    //                     $liveTime = $operatorData['total_time'] ?? 0;
    //                 }

    //                 $timerStates[$uniqueId] = [
    //                     'status' => $operatorData['status'],
    //                     'operator_total_time' => $liveTime,
    //                     'operator_started_at' => $operatorData['started_at'] ?? null,
    //                     'operator_ended_at' => $operatorData['ended_at'] ?? null,
    //                     'process_status' => $process->timer_status,
    //                     'remaining_time' => $remainingTime,
    //                     'elapsed_time' => $elapsedTime,
    //                     'timer_started_at' => $process->timer_started_at,
    //                     'timer_current_time' => $process->timer_current_time,
    //                     'project_status' => $process->project_status,
    //                     'actual_time' => $process->project_actual_time,
    //                     'process_name' => $process->project_process_name
    //                 ];
    //             }
    //         }
    //     }
    //     return response()->json([
    //         'timer_states' => $timerStates,
    //         'serverTime' => now()->timestamp * 1000 // Send server time for synchronization
    //     ]);
    // }

    public function getTimerState(Request $request)
{
    $processes = ProjectProcessStdTime::where('projects_id', $request->projectId)
        ->where('product_id', $request->productId)
        ->where('order_qty', $request->orderQty)
        ->get();

    $timerStates = [];

    foreach ($processes as $key => $process) {
        $assignedOperatorProductQtyWiseIDs = AssignedProductToOperator::select('operator_id')
            ->where('seq_qty', $request->orderQty)
            ->where('project_id', $request->projectId)
            ->where('product_id', $request->productId)
            ->value('operator_id');

        $assignedOperatorProductQtyWiseIDs = explode(',', $assignedOperatorProductQtyWiseIDs);

        foreach ($assignedOperatorProductQtyWiseIDs as $key => $operatorId) {
            $uniqueId = $process->projects_id . '-' . $process->product_id . '-' . $process->order_qty . '-' . $operatorId . '-' . $key;

            if ($process->timer_status !== 'completed') {
                $elapsedTime = $process->calculateElapsedTime();
                $remainingTime = $process->calculateRemainingTime();

                if ($process->timer_status === 'running' && $remainingTime <= 0) {
                    $process->timer_status = 'completed';
                    $process->project_status = "1";
                    $process->timer_ends_at = now();
                    $process->elapsed_time = $process->process_std_time * 3600;
                    $process->project_actual_time = $this->convertSecondsToHMS($elapsedTime);
                    $process->save();

                    broadcast(new ProductProcessStoppedEvent(
                        $uniqueId,
                        $request->projectId,
                        $request->productId,
                        $request->orderQty,
                        $process->project_actual_time,
                        $process->project_status
                    ));
                }
            } else {
                $elapsedTime = $process->elapsed_time;
                $remainingTime = 0;
            }

            // 🔹 Load operators JSON
            $operators = json_decode($process->operators_time_tracking, true) ?? [];
            $updated = false;

            foreach ($operators as &$op) {
                if ($op['id'] == (string)$operatorId) {
                    if ($op['status'] === 'running' && !empty($op['started_at'])) {
                        $startedAt = Carbon::parse($op['started_at']);
                        $liveTime = ($op['total_time'] ?? 0) + $startedAt->diffInSeconds(now());

                        // 🔹 update JSON with new total_time & reset started_at
                        $op['total_time'] = $liveTime;
                        $op['started_at'] = now()->toDateTimeString();
                        $updated = true;
                    } else {
                        $liveTime = $op['total_time'] ?? 0;
                    }

                    // Build response state
                    $timerStates[$uniqueId] = [
                        'status' => $op['status'],
                        'operator_total_time' => $liveTime,
                        'operator_started_at' => $op['started_at'] ?? null,
                        'operator_ended_at' => $op['ended_at'] ?? null,
                        'process_status' => $process->timer_status,
                        'remaining_time' => $remainingTime,
                        'elapsed_time' => $elapsedTime,
                        'timer_started_at' => $process->timer_started_at,
                        'timer_current_time' => $process->timer_current_time,
                        'project_status' => $process->project_status,
                        'actual_time' => $process->project_actual_time,
                        'process_name' => $process->project_process_name
                    ];
                }
            }

            unset($op);

            // 🔹 Save JSON back if updated
            if ($updated) {
                $process->operators_time_tracking = json_encode($operators);
                $process->save();
            }
        }
        return response()->json([
            'timer_states' => $timerStates,
            'serverTime' => now()->timestamp * 1000
        ]);
    }

}

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
        }
        unset($op);

        $process->operators_time_tracking = json_encode($operators);

        $process->save();
        return response()->json(['success' => true, 'message' => 'Timer reset successfully.']);
    }

    public function saveEditedPDF(Request $request)
    {
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

    public function saveCapturedPhoto(Request $request)
    {
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

    public function getProcessPhotos(Request $request)
    {
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
                if (in_array(pathinfo($file, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif'])) {
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

    public function getPoNumbers(Request $request)
    {
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
