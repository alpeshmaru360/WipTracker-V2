<?php
// app/Services/DashboardService.php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Project;
use App\Models\ProductsOfProjects;
use App\Models\ProjectStatus;
use Carbon\Carbon;

class DashboardService
{
    /**
     * Return every status array + counters required by one role’s dashboard.
     * Add extra roles by introducing a new private build-method and mapping it here.
     *
     * Example use:
     *     $data = app(DashboardService::class)->getDashboardData(auth()->user()->role);
     */

    public function getDashboardData(string $role): array
    {
        return match ($role) {
            'Production Superwisor'     => $this->buildCommonDashboard(),
            'Assembly Manager'          => $this->buildCommonDashboard(),
            'Quality Engineer'          => $this->buildCommonDashboard(),
            'Production Engineer'       => $this->buildCommonDashboard(),
            'Estimation Manager'        => $this->buildCommonDashboard(),
            'Procurement Specialist'    => $this->buildCommonDashboard(),
            'Admin'                     => $this->buildCommonDashboard(),
            /** fall-through */
            default                     => $this->buildEmpty(),
        };
    }

    private function buildCommonDashboard(): array
    {
        /* ---------- Projects ---------- */
        $projects = Project::where('assembly_quotation_ref', '!=', 'null')->where('is_deleted', 0)->get();// A Code: 26-12-2025
        $working  = Project::whereNull('deleted_at')->where('status', 1)->count();
        $done     = Project::whereNull('deleted_at')->where('status', 2)->count();

        /* ---------- Status arrays keyed by project_id ---------- */
        $project_creation_status     = [];
        $purchase_process_status     = [];
        $items_received_status       = [];
        $initial_inspection_status   = [];
        $material_requisition_status = [];
        $assembly_status             = [];
        $nameplate_status            = [];
        $final_inspection_status     = [];
        $packing_status              = [];        

        foreach ($projects as $project) 
        {

            $totalItems = DB::table('stock_bom_po')->where('project_id', $project->id)->count();
            $stockCount = DB::table('stock_bom_po')
                ->where('project_id', $project->id)
                ->where('select_option', 'stock')
                ->count();
            $allAreStock = ($totalItems > 0 && $totalItems == $stockCount);

            /* ─────────────────────────────────────────────────────────────
             |  Project Creation Status
             ───────────────────────────────────────────────────────────── */
            // Fetch the dynamic hour threshold from admin_hours_management table
            $project_creation_hours = DB::table('admin_hours_management')
                ->where('lable', 'StandardProcessTimes')
                ->where('key', 'create_new_project')
                ->where('is_deleted', 0)
                ->value('value') ?? 24; // Default to 24 hours if no value is found        

            // Get required timestamps
            $createdAt = $project->created_at;
            $wipDate = $project->wip_project_create_date;

            // Check if both dates exist and are valid
            if ($createdAt && $wipDate) {
                try {
                    $startDate = Carbon::parse($createdAt);
                    $projectDeadline = calculateDeadline($startDate, $project_creation_hours)->format('Y-m-d H:i:s');

                    $wip = Carbon::parse($wipDate);

                    if ($project->deleted_at === null) {
                        $project_creation_status[$project->id] = ($wip > $projectDeadline) ? 'red' : 'green';
                    } else {
                        $project_creation_status[$project->id] = 'none';
                    }
                } catch (\Exception $e) {
                    $project_creation_status[$project->id] = 'green'; // Default to green on error
                }
            } else {
                // Default to green if any date is missing
                $project_creation_status[$project->id] = 'green';
            }

            /* ─────────────────────────────────────────────────────────────
            |  Purchase Process Status
            ───────────────────────────────────────────────────────────── */          

            $total = DB::table('products_of_projects')
                ->join('projects', 'products_of_projects.project_id', '=', 'projects.id')
                ->join('purchase_order', 'projects.project_no', '=', 'purchase_order.project_no')
                ->join('stock_bom_po', 'products_of_projects.id', '=', 'stock_bom_po.product_id')  
                ->where('stock_bom_po.select_option', '!=', 'stock')
                ->where('products_of_projects.project_id', $project->id)
                ->pluck('products_of_projects.bom_check_procurement_manager')->count();          

            $bom_checked = DB::table('products_of_projects')
                ->join('projects', 'products_of_projects.project_id', '=', 'projects.id')
                ->join('purchase_order', 'projects.project_no', '=', 'purchase_order.project_no')
                ->join('stock_bom_po', 'products_of_projects.id', '=', 'stock_bom_po.product_id')  
                ->where('stock_bom_po.select_option', '!=', 'stock')
                ->where('products_of_projects.project_id', $project->id)
                ->where('purchase_order.is_production_manager_approved', 1)
                ->where('purchase_order.is_production_engineer_approved', 1)  
                ->where('stock_bom_po.po_added', 1)              
                ->whereNotNull('stock_bom_po.processed_at')
                ->pluck('products_of_projects.bom_check_procurement_manager')->count();

            $project_creation_date_WIP = Project::find($project->id)->wip_project_create_date;

            // Fetch place_po_hours from admin_hours_management
            $place_po_hours = DB::table('admin_hours_management')
                ->where('lable', 'StandardProcessTimes')
                ->where('key', 'check_the_bom_and_place_po')
                ->where('is_deleted', 0)
                ->value('value') ?? 48;

            // Convert to Carbon
            $startDate = Carbon::parse($project_creation_date_WIP);
            $workingHoursToAdd = (int) $place_po_hours;
            $addedHours = 0;

            // Clone date to avoid modifying original
            $finalDate = $startDate->copy();

            while ($addedHours < $workingHoursToAdd) {
                $finalDate->addHour();

                // Check if it's a weekday (Mon to Fri)
                if (!in_array($finalDate->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY])) {
                    $addedHours++;
                }
            }
            $purchase_process_deadline = $finalDate->format('Y-m-d H:i:s');

            // Check if any product is overdue for PO placement
            $is_overdue = DB::table('products_of_projects')
                ->join('stock_bom_po', 'products_of_projects.id', '=', 'stock_bom_po.product_id')
                ->where('products_of_projects.project_id', $project->id)
                ->where('products_of_projects.bom_check_procurement_manager', 3)
                ->where('stock_bom_po.po_added', 1) // added on 07/10/2025
                ->whereNotNull('stock_bom_po.processed_at')
                ->max('stock_bom_po.processed_at');


            if ($total === 0) {
                $purchase_process_status[$project->id] = 'none';
            } elseif ($bom_checked === $total && $is_overdue > $purchase_process_deadline) {
                // Deadline missed or delayed
                $purchase_process_status[$project->id] = 'red';
            } elseif ($bom_checked === $total && $is_overdue <= $purchase_process_deadline) {
                $purchase_process_status[$project->id] = 'green';
            } elseif ($bom_checked > 0) {
                $purchase_process_status[$project->id] = 'yellow';
            } else {
                $purchase_process_status[$project->id] = 'none';
            }      
            
            if($allAreStock){
                $purchase_process_status[$project->id] = 'black';
            }

            /* ─────────────────────────────────────────────────────────────
             |  Items Received Status
             ───────────────────────────────────────────────────────────── */            

            // Get purchase order IDs for the given project
            $poIds  = DB::table('purchase_order')
                ->where('project_no', $project->project_no)
                ->pluck('id');

            // Get actual readiness dates for those purchase orders
            $dates  = DB::table('purchase_order_table')
                ->whereIn('po_id', $poIds)
                ->pluck('actual_received_date');
                //->pluck('actual_readiness_date');                              

            // Count total and completed (non-empty) dates
            $total = count($dates);
            $completed = 0;
            foreach ($dates as $date) {
                if (!empty($date)) {
                    $completed++;
                }
            }       

            if ($total === 0) {
                // Process Pending
                $items_received_status[$project->id] = 'none';
            } elseif ($completed === $total) {
                // Process Completed Done
                $items_received_status[$project->id] = 'green';
            } elseif ($completed > 0) {
                // Process Partially Done
                $items_received_status[$project->id] = 'yellow';
            } else {
                $items_received_status[$project->id] = 'none';
            }

            if($allAreStock){
                $items_received_status[$project->id] = 'black';
            }

            /* ─────────────────────────────────────────────────────────────
             |  Initial Inspection Status
             ───────────────────────────────────────────────────────────── */

            $inspection_hours = DB::table('admin_hours_management')
                ->where('lable', 'StandardProcessTimes')
                ->where('key', 'initial_inspection')
                ->where('is_deleted', 0)
                ->value('value') ?? 48;

            $inspection = DB::table('purchase_order_table')
                ->whereIn('po_id', $poIds)
                ->pluck('is_initial_inspection_started');

            $initial_inspection_start_at = DB::table('initial_inspection_data')
                    ->where('project_no', $project->project_no)
                    ->min('ini_inspection_date');

            $initial_inspection_done_at = DB::table('initial_inspection_data')
                    ->where('project_no', $project->project_no)
                    ->max('ini_inspection_date');

            // Get Deadline for initial_inspection
            $startDateIni = Carbon::parse($initial_inspection_start_at);
            $initial_inspection_deadline = calculateDeadline($startDateIni,$inspection_hours)->format('Y-m-d H:i:s');       

            // Count total and completed (values 1 or 2)
            $total = count($inspection);
            $completed = 0;
            foreach ($inspection as $status) {
                if (in_array($status, [1, 2])) {
                    $completed++;
                }
            }            

            if ($total === 0) {
                // Process Pending
                $initial_inspection_status[$project->id] = 'none';
            } elseif ($completed === $total) {
                // Process Completed Done – check if completed within deadline
                $initial_inspection_status[$project->id] = ($initial_inspection_done_at > $initial_inspection_deadline) ? 'red' : 'green';
            } elseif ($completed > 0) {
                // Process Partially Done                
                $initial_inspection_status[$project->id] = 'yellow';
            } else {
                $initial_inspection_status[$project->id] = 'none';
            }

            if($allAreStock){
                $initial_inspection_status[$project->id] = 'black';
            }


            /* ─────────────────────────────────────────────────────────────
             |  Material Requisition Status
             ───────────────────────────────────────────────────────────── */

            $material_requisition_hours = DB::table('admin_hours_management')
                ->where('lable', 'StandardProcessTimes')
                ->where('is_deleted', 0)
                ->value('value') ?? 24;


            /* ─────────────────────────────────────────────────────────────
             |  Assembly Status
             ───────────────────────────────────────────────────────────── */

            $all_products_types = DB::table('products_of_projects')
                ->where('project_id', $project->id)
                ->pluck('product_type');

            $total_assembly_admin_hours = DB::table('admin_hours_management') 
                ->where('lable', 'AssemblyProcessTime')
                ->whereIn('product_type', $all_products_types)
                ->where('is_deleted', 0)                     
                ->sum('value');  

            // Check first assembly process ends at
            $first_assembly_process_ends_at = DB::table('project_process_std_time')
                ->where('projects_id', $project->id)
                ->where('timer_status','completed')
                ->whereNotNull('timer_ends_at')
                ->min('timer_ends_at'); 

            // Check last assembly process ends at
            $last_assembly_process_ends_at = DB::table('project_process_std_time')
                ->where('projects_id', $project->id)
                ->where('timer_status','completed')
                ->whereNotNull('timer_ends_at')
                ->max('timer_ends_at');

            // Get Deadline for assembly
            $startDateAssembly = Carbon::parse($first_assembly_process_ends_at);
            $assembly_process_deadline = calculateAssemblyDeadline($startDateAssembly,$total_assembly_admin_hours)->format('Y-m-d H:i:s');
            $totQty = DB::table('qty_of_products')->where('project_id', $project->id)->sum('qty_number');

            $assembled = DB::table('qty_of_products')
                ->where('project_id', $project->id)
                ->where('is_qty_product_assembled', 1)
                ->sum('qty_number');            

            if ($totQty === 0) {
                // Process Pending
                $assembly_status[$project->id] = 'none';
            } elseif ($assembled === $totQty) {
                // Process Completed Done – check if completed within deadline
                $assembly_status[$project->id] = ($last_assembly_process_ends_at > $assembly_process_deadline) ? 'red' : 'green';
            } elseif ($assembled > 0) {
                // Process Partially Done
                $assembly_status[$project->id] = 'yellow';
            } else {
                $assembly_status[$project->id] = 'none';
            }            

            /* ─────────────────────────────────────────────────────────────
             |  Final Inspection Status
             ───────────────────────────────────────────────────────────── */

            $final_inspection_hours = DB::table('admin_hours_management')
                ->where('lable', 'StandardProcessTimes')
                ->where('key', 'final_inspection')
                ->where('is_deleted', 0)
                ->value('value') ?? 36;

            $finalDone = DB::table('qty_of_products')
                ->where('project_id', $project->id)
                ->where('is_final_inspection_started', 2)
                ->sum('qty_number');  

            $final_inspection_start_at = DB::table('final_inspection_data')
                                ->where('project_no', $project->project_no)
                                ->min('created_at');              

            $final_inspection_done_at = DB::table('final_inspection_data')
                                ->where('project_no', $project->project_no)
                                ->max('created_at');  

            // Get Deadline for Final Inspection
            $startDateFinal = Carbon::parse($final_inspection_start_at);
            $final_inspection_deadline = calculateDeadline($startDateFinal,$final_inspection_hours)->format('Y-m-d H:i:s');

            if ($totQty === 0) {
                // Process Pending
                $final_inspection_status[$project->id] = 'none';
            } elseif ($finalDone === $totQty) {
                // Process Completed Done – check if completed within deadline
                $final_inspection_status[$project->id] = ($final_inspection_done_at > $final_inspection_deadline) ? 'red' : 'green';
            } elseif ($finalDone > 0) {
                // Process Partially Done
                $final_inspection_status[$project->id] = 'yellow';
            } else {
                $final_inspection_status[$project->id] = 'none';
            }

            /* ─────────────────────────────────────────────────────────────
             |  Packing Status
             ───────────────────────────────────────────────────────────── */  

            $prepare_pl_hours = DB::table('admin_hours_management')
                ->where('lable', 'StandardProcessTimes')
                ->where('key', 'prepare_pl')
                ->where('is_deleted', 0)
                ->value('value') ?? 12;

            $pl_uploaded_start_at = DB::table('qty_of_products')
                                ->where('project_id', $project->id)
                                ->min('pl_uploaded_date');  

            $pl_uploaded_done_at = DB::table('qty_of_products')
                                ->where('project_id', $project->id)
                                ->max('pl_uploaded_date'); 

            $qty_pl_dates = DB::table('qty_of_products')
                ->where('project_id', $project->id)
                ->pluck('pl_uploaded_date');

            $total_qty_lines = $qty_pl_dates->count();        

            $done_qty_lines = 0;
            foreach ($qty_pl_dates as $val) {
                if (!empty($val)) {
                    $done_qty_lines++;
                }
            }

            // Get Deadline for Upload PL
            $startDateUploadPL = Carbon::parse($pl_uploaded_start_at);
            $upload_pl_deadline = calculateDeadline($startDateUploadPL,$prepare_pl_hours)->format('Y-m-d H:i:s');            

            if ($total_qty_lines === 0) {
                // Process Pending
                $packing_status[$project->id] = 'none';
            } elseif ($done_qty_lines === $total_qty_lines) {
                // Process Completed Done – check if completed within deadline
                $packing_status[$project->id] = ($pl_uploaded_done_at > $upload_pl_deadline) ? 'red' : 'green';
            } elseif ($done_qty_lines > 0) {
                // Process Partially Done
                $packing_status[$project->id] = 'yellow';
            } else {
                $packing_status[$project->id] = 'none';
            }

        }

        return compact(
            'projects',
            'working',
            'done',
            'project_creation_status',
            'purchase_process_status',
            'items_received_status',
            'initial_inspection_status',
            'material_requisition_status',
            'assembly_status',
            'final_inspection_status',
            'packing_status'
            
        );
    }
    /* ──────────────────────────────────────────────────────────── */
    private function buildEmpty(): array
    {
        return [];
    }

}
