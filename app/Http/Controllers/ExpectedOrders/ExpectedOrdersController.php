<?php

namespace App\Http\Controllers\ExpectedOrders;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SaleManagerOrder;
use App\Models\SaleManagerModule;
use App\Models\ProductType;
use App\Models\AdminSetting;
use App\Models\AdminHoursManagement;
use PDF;
use App\Helpers\helper;

class ExpectedOrdersController extends Controller
{
    public function dashboard(){
        $expected_orders = SaleManagerOrder::orderBy('id', 'desc')->get();
        $modules = SaleManagerModule::all()->keyBy('id'); // Store modules indexed by ID

        $page_title = "";
        return view('expected_orders.dashboard', compact('page_title', 'expected_orders', 'modules'));
    }
    public function index(){
        $page_title = "";
        $product_type = ProductType::get();

        return view('expected_orders.index', compact('page_title', 'product_type'));
    }
    public function project_create(Request $request){
        $sales_manage_module = new SaleManagerModule;
        $sales_manage_module->quotation_number = $request->assembly_quotation_ref;
        $sales_manage_module->status = '0';
        $sales_manage_module->save();

        $admin_setting = AdminSetting::where('key', 'project_number_prefix')->first();
        $project_number_prefix = $admin_setting->value;
        $total_hours = 0;

        $projects_type = $request->product_type;

        if ($request->has('quotation_items')) {
            foreach ($request->quotation_items as $val_projects) {
                $product_type = $val_projects['product_type'];
                $qty = $val_projects['qty'];
                $hours_per_unit = AdminHoursManagement::where('lable', 'like', 'AssemblyProcessTime')
                    ->where('product_type', 'LIKE', "%$product_type%")
                    ->where('is_deleted', 0)
                    ->get();
                foreach ($hours_per_unit as $hour) {
                    $total_hours += $hour->value * $qty;
                }

                $projectProcessStdTime = new SaleManagerOrder;
                $projectProcessStdTime->quotation_number = $request->assembly_quotation_ref;
                $projectProcessStdTime->quotation_from_pricing_tool = $request->quotation_items[0]['quotation_from_pricing_tool'];
                $projectProcessStdTime->article_number = $val_projects['full_article_number'];
                $projectProcessStdTime->full_article_number = $val_projects['full_article_number'];
                $projectProcessStdTime->description = $val_projects['description'];
                $projectProcessStdTime->qty = $val_projects['qty'];
                $projectProcessStdTime->sales_manager_module_id = $sales_manage_module->id;
                if (isset($val_projects['cart_model_name'])) {
                    $projectProcessStdTime->cart_model_name = $val_projects['cart_model_name'];
                } else {
                    $projectProcessStdTime->cart_model_name = null;
                }
                $projectProcessStdTime->product_type = $val_projects['product_type'];
                $projectProcessStdTime->expected_order_date = $request->expected_order_date;
                $projectProcessStdTime->expected_delivery_date = $request->expected_delivery_date;
                $projectProcessStdTime->save();

                $product_type = $val_projects['product_type'] ?? null;
            }
            $get_std_process_time = get_std_process_time();
            $total_hours = $total_hours + $get_std_process_time;
            $estimated_date = get_estimated_date($total_hours);
            $sales_manage_module->estimated_readiness = $estimated_date;
            $sales_manage_module->save();
        }
        return redirect()->route('ExpectedOrdersDashboard')->with('success', 'Order Added Successfully..!!');
    }
    public function generatePDF(Request $request){
        // Get the input data from the request
        $quotation_ref = $request->assembly_quotation_ref;
        $expected_order_date = $request->expected_order_date;
        $expected_delivery_date = $request->expected_delivery_date;
        $quotation_items = $request->quotation_items;

        $data = [
            'quotation_ref' => $quotation_ref,
            'expected_order_date' => $expected_order_date,
            'expected_delivery_date' => $expected_delivery_date,
            'quotation_items' => $quotation_items,
        ];

        $pdf = PDF::loadView('expected_orders.pdf', $data); // Create a PDF from a view
        return $pdf->download('expected_orders.pdf'); // Download the PDF
    }
}
