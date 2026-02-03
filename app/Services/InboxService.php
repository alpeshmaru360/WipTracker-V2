<?php
// app/Services/InboxService.php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\QtyOfProduct;
use App\Models\ProductsOfProjects;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderTable;
use App\Models\StockMasterModule;

class InboxService
{
    /**
     * Return “how many things are waiting” for the red-dot badge.
     */
    public function getUnreadCount(string $role): int
    {
        $count = 0;

        /* ─────────────────────────────────────────────────────────────
         |  ASSEMBLY MANAGER
         ───────────────────────────────────────────────────────────── */
        if ($role === 'Assembly Manager') {
            // 1 Purchase orders that still need assembly-side approval [Pending Purchase Order Approvals]
            $count = DB::table('purchase_order')
                ->where('is_production_manager_approved', 4)   // same filter as controller
                ->count();
        }

        /* ─────────────────────────────────────────────────────────────
         |  QUALITY ENGINEER
         ───────────────────────────────────────────────────────────── */
        if ($role === 'Quality Engineer') {

            // 1 Ready-for-initial-inspection lines [Pending Initial Inspection]
            $initialPending = DB::table('purchase_order as po')
                ->join('purchase_order_table as pot', 'po.id', '=', 'pot.po_id')
                ->whereNotNull('pot.actual_received_date')
                ->where('pot.is_initial_inspection_started', 0)
                ->where('pot.is_partial_shipment', '!=', 1)
                ->selectRaw('
                    DATE(pot.actual_received_date) as received_date,
                    po.po_number,
                    po.supplier,
                    po.project_no
                ')
                ->groupBy(
                    DB::raw('DATE(pot.actual_received_date)'),
                    'po.po_number',
                    'po.supplier',
                    'po.project_no'
                )
                ->count();

            // 2 Final inspection started lines (from qty_of_products table) [Pending Final Inspection]
            $finalPending = DB::table('qty_of_products')
                ->where('is_final_inspection_started', 1)
                ->count();

            $count = $initialPending + $finalPending;
        }


        /* ─────────────────────────────────────────────────────────────
         |  PROCUREMENT SPECIALIST
         ───────────────────────────────────────────────────────────── */
        if ($role === 'Procurement Specialist') {

            // 1 BOMs that need to be checked [Pending BOM Check]
            $bomCheck = ProductsOfProjects::where('bom_check_procurement_manager', 1)->count();

            // 2 Items that still need a Purchase Order [BOM Items - Place PO]
            $pendingPO = ProductsOfProjects::where('bom_check_procurement_manager', 3)
                ->whereNotIn('full_article_number', function ($q) {
                    $q->select('product_article_no')
                      ->from('purchase_order')
                      ->whereNotNull('product_article_no');
                })
                ->whereExists(function ($q) {
                    $q->select(DB::raw(1))
                      ->from('stock_bom_po as sbp')
                      ->whereColumn('sbp.project_id', 'products_of_projects.project_id')
                      ->whereColumn('sbp.product_id', 'products_of_projects.id')
                      ->where('sbp.po_added', '!=', 1)
                      ->where('sbp.select_option', '!=', 'stock');
                })
                ->count();

            // 3 Rejected POs that need revision [Rejected Purchase Orders]
            $rejectedPOs = PurchaseOrder::where(function ($q) {
                    $q->where('is_production_manager_approved', 2)
                      ->orWhere('is_production_engineer_approved', 2);
                })
                ->count();

            // 4 Projects still missing a BOM file [Pending BOM Upload from Estimation Manager Side]
            $pendingBOM = ProductsOfProjects::whereNull('bom_path')->count();

            // 5 Materials below minimum stock [Minimum Low Stock Alert]
            $lowStock = StockMasterModule::whereColumn('available_qty', '<=', 'minimum_required_qty')->count();            

            $count = $bomCheck
                   + $pendingPO                                      
                   + $rejectedPOs
                   + $pendingBOM
                   + $lowStock;
        }

        /* ─────────────────────────────────────────────────────────────
         |  PRODUCTION ENGINEER
         ───────────────────────────────────────────────────────────── */
        if ($role === 'Production Engineer') {

            
        }

        /* ─────────────────────────────────────────────────────────────
         |  PRODUCTION SUPERVISOR 
         ───────────────────────────────────────────────────────────── */
        if ($role === 'Production Superwisor') {

            // 1  Pending Projects (email not sent but PO exists) [Pending MRF (Completed Initial Inspection)]
            $pendingProjects = DB::table('products_of_projects as pop')
                ->join('stock_bom_po as sbp', function ($join) {
                    $join->on('sbp.product_id', '=', 'pop.id')
                         ->on('sbp.project_id', '=', 'pop.project_id');
                })
                ->where('sbp.is_email_sent', 0)
                ->whereNotNull('sbp.po_no')
                ->count();        

            // 5  Ready MRF (email sent = 2 but PO exists) [Ready MRF (Full Or Partial)]
            $readyMRF = DB::table('products_of_projects as pop')
                ->join('stock_bom_po as sbp', function ($join) {
                    $join->on('sbp.product_id', '=', 'pop.id')
                         ->on('sbp.project_id', '=', 'pop.project_id');
                })
                ->where('sbp.is_email_sent', 2)
                ->whereNotNull('sbp.po_no')
                ->count();           

            // 6  Fully Delivered Projects [Orders]
            $deliveredProjects = DB::table('products_of_projects')
                ->where('delivery', 1)
                ->count();

            // 7  Partially Delivered Projects [Partial Orders]
            $partialDeliveries = DB::table('products_of_projects')
                ->where('delivery', 2)
                ->count();

            // 8  As-built PDF approvals pending [Pending As-Built Drawing PDF Approval]
            $pdfRequests  = DB::table('products_of_projects')
                ->whereNotNull('editable_drawing_path')
                ->where('is_asbuilt_drawing_pdf_approve_by_production_superwisor', 3)
                ->count();            

            // 9  Qty Assembled but Nameplate not created [Completed process of assembly]
            $assembledQty = DB::table('qty_of_products')
                ->where('is_qty_product_assembled', 1)
                ->where('nameplate_create_inbox_to_pro_eng', 0)
                ->count();                       
            
            // 10  Packing-List requests (distinct projects) [Upload PL For Full Order]
            // $uploadPLReq = DB::table('products_of_projects as pop')
            //     ->join('projects as p', 'pop.project_id', '=', 'p.id')
            //     ->where('pop.inbox_to_pro_superwisor_to_create_pl', 1)
            //     ->where('p.QE_req_to_PS_create_PL_inbox', 0)
            //     ->where('p.status', '!=', 2)
            //     ->count(); 

            // 10  Packing-List requests (distinct projects) [Upload PL For Full Order]
            $uploadPLReq = DB::table('products_of_projects as pop')
                ->join('projects as p', 'pop.project_id', '=', 'p.id')
                ->where('pop.inbox_to_pro_superwisor_to_create_pl', 1)
                ->where('pop.delivery', 1)
                ->where('p.QE_req_to_PS_create_PL_inbox', 0)
                ->whereNull('p.PL_PDF_path')
                ->groupBy('pop.project_id')
                ->selectRaw('COUNT(DISTINCT pop.project_id) as total')
                ->value('total');

            $count = $pendingProjects
                   + $readyMRF
                   + $deliveredProjects
                   + $partialDeliveries
                   + $pdfRequests
                   + $assembledQty
                   + $uploadPLReq;
        }

        /* ─────────────────────────────────────────────────────────────
         |  ESTIMATION MANAGER
         ───────────────────────────────────────────────────────────── */
        if ($role === 'Estimation Manager') {

            // 1  BOM requests waiting for action [Pending BOM Upload]
            $bomReq = DB::table('products_of_projects')
                ->where('bom_req_estimation_manager', 1)
                ->count();

            // 2  Drawing requests waiting for action [Pending Basic Drawing Upload]
            $drawingReq = DB::table('products_of_projects')
                ->where('drawing_req_estimation_manager', 1)
                ->count();

            // 3  As-built PDF approvals pending [Pending As-Built Drawing PDF Approval]
            $pdfReq = DB::table('products_of_projects')
                ->whereNotNull('editable_drawing_path')
                ->where('is_asbuilt_drawing_pdf_approve_by_estimation_manager', 3)
                ->count();

            // 4  Uploaded and Approved Final PDF [Upload Final PDF]
            $finalPdfReqCount = DB::table('products_of_projects')
                ->whereNotNull('editable_drawing_path')
                ->whereNull('drawing_upload_by_estimation_manager')
                ->where('is_asbuilt_drawing_pdf_approve_by_estimation_manager', 1)
                ->where('is_asbuilt_drawing_pdf_approve_by_production_superwisor', 1)
                ->count();

            $count = $bomReq + $drawingReq + $pdfReq + $finalPdfReqCount;
        }

        return $count;
    }
}
