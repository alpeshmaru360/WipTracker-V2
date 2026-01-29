<?php

namespace App\Http\Controllers\AssemblyManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProjectStatus;
use App\Models\Project;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\DashboardService;

class AssemblyManagerController extends Controller
{
    public function dashboard(DashboardService $dashboardService){
        // Common Dashboard Service
        $role = auth()->user()->role;
        $dashboardData = $dashboardService->getDashboardData($role);
        $page_title = "ORDERS INTAKE";        
        $projects_complate_count = $dashboardData['done'];
        $projects_working_count = $dashboardData['working'];
        $startDate = Carbon::now()->subMonths(3)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        $projectOrders = Project::with('product')->whereBetween('created_at', [$startDate, $endDate])->get();

        $chartData = [];
        $productTypes = [];
        $labels = [];
        for ($i = 3; $i >= 0; $i--) {
            $labels[] = Carbon::now()->subMonths($i)->format('M Y');
        }

        foreach ($labels as $month) {
            $chartData[$month] = [];
        }

        foreach ($projectOrders as $project) {
            foreach ($project->product as $product) {
                $month = Carbon::parse($project->created_at)->format('M Y');
                $type = $product->cart_model_name;
                $qty = $product->qty;
                if (!isset($chartData[$month][$type])) {
                    $chartData[$month][$type] = 0;
                }
                $chartData[$month][$type] += $qty;
                $productTypes[$type] = true;
            }
        }

        $datasets = [];
        $colors = [];
        $colorPalette = ["#FF5733", "#33FF57", "#3357FF", "#F3FF33", "#FF33E3", "#33FFF0", "#FF8C33", "#8C33FF"];

        $productTypes = array_keys($productTypes);
        foreach ($productTypes as $index => $type) {
            $colors[$type] = $colorPalette[$index % count($colorPalette)];
        }

        foreach ($productTypes as $type) {
            $data = [];
            foreach ($labels as $label) {
                $data[] = $chartData[$label][$type] ?? 0;
            }
            $datasets[] = [
                "label" => $type,
                "backgroundColor" => $colors[$type],
                "data" => $data
            ];
        }

        $chartJson = json_encode(["label" => $labels, "datasets" => $datasets]);
        return view('assembly_manager.dashboard', compact('dashboardData', 'page_title', 'projects_complate_count', 'projects_working_count', 'chartJson'));
    }

    public function inbox(){
        $page_title = "PROJECT STATUS";

        $pendingPurchaseOrders = DB::table('purchase_order as po')
            ->where('po.is_production_manager_approved', 4)
            ->select('po.id as po_id', 'po.po_number', 'po.project_no', 'po.project_name', 'po.supplier', 'po.order_date')
            ->get();

        return view('assembly_manager.inbox', compact('pendingPurchaseOrders', 'page_title'));
    }

    public function approve(Request $request, $id){
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        $purchaseOrder->is_production_manager_approved = 1;
        $purchaseOrder->approved_remarks_production_manager = $request->remarks;
        $purchaseOrder->production_manager_approved_date = Carbon::now();
        if (!is_null($purchaseOrder->production_manager_reject_date)) {
            $purchaseOrder->production_manager_reject_date = null; // Clear reject date if it exists
        }
        $purchaseOrder->save();

        return response()->json(['success' => 'Purchase order approved successfully!']);
    }

    public function reject(Request $request, $id){
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        $purchaseOrder->is_production_manager_approved = 2;
        $purchaseOrder->rejection_reason_production_manager = $request->reason;
        $purchaseOrder->production_manager_reject_date = Carbon::now();
        if (!is_null($purchaseOrder->production_manager_approved_date)) {
            $purchaseOrder->production_manager_approved_date = null; // Clear approve date if it exists
        }
        $purchaseOrder->save();

        return response()->json(['success' => 'Purchase order rejected successfully!']);
    }

    public function view($id){
        $purchaseOrder = PurchaseOrder::with('purchaseOrderTables')->findOrFail($id);
        return view('assembly_manager.view_po', compact('purchaseOrder'));
    }
}
