<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\ProjectStatus;
use App\Models\Project;
use App\Models\AdminSetting;
use App\Models\ProductType;
use App\Models\AdminHoursManagement;
use App\Models\ProductsOfProjects;
use App\Models\ProjectProcessStdTime;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderTable;
use App\Models\ProductBOMItem;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Mail\FinancePersonCreateProject;
use App\Mail\WItrackProjectCreateNotifyProductionTeam;
use App\Mail\WItrackProjectCancelNotifyProductionTeam;
use App\Helpers\helper;
use File;
use App\Models\StockBOMPo;
use App\Models\StockMasterModule;
use App\Models\ProductionTeamDetail;
use Illuminate\Support\Facades\Mail;

class APIWITrackPOController extends Controller
{
    public function get_wiTrack_po_details(Request $request){        
        try {
            $validated = $request->validate([
                'PO_pdf' => 'required|mimes:pdf|max:2048',
                'PO_number' => 'required|string|max:255',
                'project_number' => 'nullable|string|max:255',
                'project_name' => 'nullable|string|max:255',
                'payment_terms' => 'required|string|max:255',
                'shipment_method' => 'required|string|max:255',
                'order_date' => 'required|string',
                'supplier' => 'required|string',
                'currency' => 'nullable|string|max:10',
                'is_Project_Order' => 'nullable|boolean',
                'product_article_no' => 'nullable|string|max:255',
                'product_desc' => 'nullable|string|max:255',
                'product_qty' => 'nullable|integer|min:0',
            ]);

            // Check if coming from inbox (product_id in session)
            $isFromInbox = session()->has('product_id');
            $isProjectOrder = $request->boolean('is_Project_Order');

            // Check PO number uniqueness based on whether it's a project order
            if (!$isFromInbox && !$isProjectOrder) {
                $existingPo = PurchaseOrder::where('po_number', $validated['PO_number'])->first();
                if ($existingPo) {
                    return response()->json([
                        'status' => 0,
                        'error' => 'PO number already exists. When not a project order, PO number must be unique.'
                    ], 400);
                }
            } elseif (!empty($validated['project_number'])) {
                $existingCombination = PurchaseOrder::where('po_number', $validated['PO_number'])
                    ->where('project_no', $validated['project_number'])
                    ->first();

                if ($existingCombination) {
                    return response()->json([
                        'status' => 0,
                        'error' => "The combination of PO Number '{$validated['PO_number']}' and Project Number '{$validated['project_number']}' already exists. Please use a different combination."
                    ], 400);
                }
            }

            $po = new PurchaseOrder();

            if ($request->hasFile('PO_pdf')) {
                $file = $request->file('PO_pdf');
                $destinationPath = public_path('purchase_order_pdf');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move($destinationPath, $fileName);
                $po->PO_pdf = $fileName;
            }

            // Format order date to Y-m-d
            $orderDate = \DateTime::createFromFormat('F d, Y', $validated['order_date']);
            $formattedOrderDate = $orderDate ? $orderDate->format('Y-m-d') : $validated['order_date'];

            // Assign validated data to PO model
            $po->po_number = $validated['PO_number'];
            $po->is_project_order =  $isFromInbox ? 1 : ($request->is_Project_Order ?? 0);
            $po->project_no = $validated['project_number'] ?? null;
            $po->is_production_manager_approved = $request->has('is_production_manager_approved') ? 1 : 0;
            $po->is_production_engineer_approved = $request->has('is_production_engineer_approved') ? 1 : 0;
            $po->project_name = $validated['project_name'] ?? null;
            $po->is_local_supplier = $request->is_local_supplier ?? 0;
            $po->payment_terms = $validated['payment_terms'];
            $po->shipment_method = $validated['shipment_method'];
            $po->order_date = $formattedOrderDate;
            $po->supplier = $validated['supplier'];
            $po->product_article_no = $request->product_article_no;
            $po->product_desc = $request->product_desc;
            $po->product_qty = $request->product_qty;         

            // Alpesh Maru Date: 09-12-2025 Code Start
            // Check project id
            $project_id = Project::where('project_no', $validated['project_number'])->value('id');

            if (!$project_id) {
                return response()->json([
                    'status' => 0,
                    'error' => 'Invalid project number'
                ], 400);
            }

            $tableDataPO = $request->tableData;
            if (is_array($tableDataPO)) {
                foreach ($tableDataPO as $rowPO) {
                    // Check if BOM record exists
                    $exists_record = StockBomPo::where('project_id', $project_id)
                        ->where('description', $rowPO['description'])
                        ->where('article_no', $rowPO['artical_no'])
                        ->exists();

                    if (!$exists_record) {
                        return response()->json([
                            'status' => 0,
                            'error' => 'Purchase Order cannot proceed: Options and supplier selection are need to be completed in the Pending BOM Check section.'
                        ], 400);
                    }   
                    // Fetch total required BOM qty (dynamic)
                    $total_required_qty = ProductBOMItem::where('project_id', $project_id)
                        ->where('item_desc', $rowPO['description'])
                        ->where('wilo_article_no', $rowPO['artical_no'])
                        ->value('total_required_qty');
                    // Qty check
                    if ($rowPO['quantity'] < $total_required_qty) {
                        return response()->json([
                            'status' => 0,
                            'error' => 'Please ensure the Purchase Order PDF Item Quantity matches or exceeds with the BOM Item Quantity.'
                        ], 400);
                    }
                }
            } else {
                return response()->json(['error' => 'Invalid table_data format.'], 422);
            }
            // Alpesh Maru Date: 09-12-2025 Code End

            // Save PO
            $po->save();

            $tableData = $request->tableData;
            $currency = $request->currency;

            if (is_array($tableData)) {
                foreach ($tableData as $row) {
                    $data = [];

                    if (isset($po->id)) $data['po_id'] = $po->id;
                    if (isset($row['position_no'])) $data['position_no'] = $row['position_no'];
                    if (isset($row['artical_no'])) $data['artical_no'] = $row['artical_no'];
                    if (isset($row['vendor_item_no'])) $data['vendor_item_no'] = $row['vendor_item_no'];
                    if (isset($row['description'])) $data['description'] = $row['description'];
                    if (isset($row['quantity'])) $data['quantity'] = $row['quantity'];
                    if (isset($row['unit_of_measure'])) $data['unit_of_measure'] = $row['unit_of_measure'];
                    if (isset($row['vat_per'])) $data['vat_per'] = $row['vat_per'];
                    if (isset($row['direct_unit_cost'])) $data['direct_unit_cost'] = $row['direct_unit_cost'];
                    if (isset($row['vat_amount'])) $data['vat_amount'] = $row['vat_amount'];
                    if (isset($row['amount'])) $data['amount'] = $row['amount'];
                    if (isset($row['amount_eur'])) $data['amount_eur'] = $row['amount_eur'];
                    if (isset($currency)) $data['currency'] = $currency;


                    // Alpesh Maru Date: 09-12-2025 Code Start
                    // Check if BOM record exists
                    $exists_record = StockBomPo::where('project_id', $project_id)
                        ->where('description', $row['description'])
                        ->where('article_no', $row['artical_no'])
                        ->exists();

                    if (!$exists_record) {
                        return response()->json([
                            'status' => 0,
                            'error' => 'BOM item not found for this project'
                        ], 400);
                    }   
                    // Fetch total required BOM qty (dynamic)
                    $total_required_qty = ProductBOMItem::where('project_id', $project_id)
                        ->where('item_desc', $row['description'])
                        ->where('wilo_article_no', $row['artical_no'])
                        ->value('total_required_qty');
                    // Qty check
                    if ($row['quantity'] < $total_required_qty) {
                        return response()->json([
                            'status' => 0,
                            'error' => 'Item Qty should not be less than BOM required qty'
                        ], 400);
                    }
                    // Alpesh Maru Date: 09-12-2025 Code End

                    // Find StockMasterModule by article_number and item_desc/description
                    $stockItem = StockMasterModule::where('article_number', $row['artical_no'])
                        ->where('item_desc', $row['description'])
                        ->first();

                    if (!$stockItem) {
                        $stockItem = new StockMasterModule();
                        $stockItem->article_number = $row['artical_no'];
                        $stockItem->item_desc = $row['description'];                       
                        $stockItem->available_qty = 0;
                        $stockItem->minimum_required_qty = 0;
                        $stockItem->std_time = 0;
                        $stockItem->save();
                    }

                    PurchaseOrderTable::create($data);
                }
            } else {
                return response()->json(['error' => 'Invalid table_data format.'], 422);
            }

            // Update StockBOMPo for project orders
            if (($request->is_Project_Order || $isFromInbox) && $request->project_number) {
                // Fetch the Project ID based on project_number
                $project = Project::where('project_no', $request->project_number)->first();
                if (!$project) {
                   
                } else {
                    foreach ($tableData as $row) {

                        $stockBomPo = StockBOMPo::where('project_id', $project->id)
                            ->where('description', $row['description'])
                            // ->where('article_no', $row['artical_no'])
                            ->where('article_no', 'like', '%' . $row['artical_no'] . '%')
                            ->first();
                        if (!$stockBomPo) {
                            $stockBomPo = StockBOMPo::where('project_id', $project->id)
                                ->where('description', $row['description'])
                                ->whereRaw("REPLACE(article_no, '  ', ' ') LIKE ?", ['%' . $row['artical_no'] . '%'])
                                ->first();
                        }
                        if ($stockBomPo) {
                            $stockBomPo->po_no = $request->PO_number;
                            $stockBomPo->save();
                        }
                        //Update ProductBOMItem for project orders
                        $productBOMItem = ProductBOMItem::where('project_id', $project->id)                          
                            ->where('item_desc', 'like', '%' . $row['description'] . '%')
                            ->where('full_article_no', $row['artical_no'])
                            ->first();
                        if ($productBOMItem) {
                            $productBOMItem->po_no = $request->PO_number;
                            $productBOMItem->save();
                        }
                    }
                }
            }

            // Update StockMasterModule if from stock
            if ($isFromStock = session('stock_id')) {
                $stockItem = StockMasterModule::find($isFromStock);
                if ($stockItem) {
                    // Adjust available_qty based on ordered quantity
                    $orderedQty = $tableData[0]['quantity'] ?? ($stockItem->minimum_required_qty - $stockItem->available_qty);
                    $stockItem->available_qty += (int)$orderedQty;
                    $stockItem->save();
                }
                session()->forget('stock_id');
            }

            session()->forget('product_id');

            return response()->json([
                'status' => 1,
                'message' => 'Purchase order created successfully! It will appear in the Assembly Manager inbox if Production Manager approval is requested.',
                'data' => $po
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 0,
                'error' => 'Validation Failed',
                'messages' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Unexpected error:', ['exception' => $e]);
            return response()->json([
                'status' => 0,
                'error' => 'Something went wrong',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
