<?php

namespace App\Http\Controllers\ProductionManager;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderTable;
use App\Models\CurrecyConverter;
use App\Models\Project;
use App\Models\StockMasterModule;
use App\Models\StockBOMPo;
use App\Models\ProductBOMItem;
use App\Models\ProductsOfProjects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;

class PurchaseOrderController extends Controller
{
    public function index(Request $request){
        $page_title = "Purchase Order";
        $purchaseOrdersfilter = (object)[];
        $last_filter_column = $request->input('last_filter_column');
        if (isset($last_filter_column)) {
            $purchaseOrdersfilter = PurchaseOrder::with(['purchaseOrderTables' => function ($query) {
                $query->orderBy('id', 'desc');
            }])
                ->where('is_production_engineer_approved', 1)
                ->whereIn('is_production_manager_approved', [0, 1])
                ->orderBy('id', 'desc')
                ->get();
        }
        $filters = $request->input('filters', []);
        $query = PurchaseOrder::with(['purchaseOrderTables' => function ($q) use ($filters) {
            $q->orderBy('id', 'desc');
            $columnMap = [
                'filter_col_7'  => 'artical_no',
                'filter_col_8'  => 'description',
                'filter_col_9'  => 'quantity',
                'filter_col_12' => 'eta',
                'filter_col_13' => 'is_partial_shipment',
                'filter_col_14' => 'received_quantity',
                'filter_col_15' => 'oa_date',
                'filter_col_16' => 'committed_date',
                'filter_col_17' => 'actual_readiness_date',
                'filter_col_18' => 'eta_date_shipper',
                'filter_col_19' => 'actual_received_date',
                'filter_col_20' => 'shipping_refrence',
                'filter_col_21' => 'boe',
                'filter_col_23' => 'remarks',
                'filter_col_24' => 'response_time',
                'filter_col_25' => 'delivery_time',
            ];
            foreach ($columnMap as $filterKey => $dbColumn) {
                if (!empty($filters[$filterKey])) {
                    $filterValues = $filters[$filterKey];
                    $hasEmpty = in_array('__EMPTY__', $filterValues);
                    $nonEmptyValues = array_filter($filterValues, fn($v) => $v !== '__EMPTY__');
                    $q->where(function ($subQuery) use ($dbColumn, $hasEmpty, $nonEmptyValues) {
                        if (!empty($nonEmptyValues)) {
                            $subQuery->whereIn($dbColumn, $nonEmptyValues);
                        }
                        if ($hasEmpty) {
                            $subQuery->orWhereNull($dbColumn)->orWhere($dbColumn, '');
                        }
                    });
                }
            }
        }])
            ->where('is_production_engineer_approved', 1)
            ->whereIn('is_production_manager_approved', [0, 1])
            ->orderBy('id', 'desc');
        // Apply parent-level filters
        foreach ($filters as $key => $values) {
            if (empty($values)) continue;
            switch ($key) {
                case 'filter_col_0': // order_date - special date parsing
                    $query->where(function ($q) use ($values) {
                        foreach ($values as $val) {
                            try {
                                $date = Carbon::parse($val)->format('Y-m-d');
                                $q->orWhereDate('order_date', $date);
                            } catch (\Exception $e) {
                                // Invalid date, skip
                            }
                        }
                    });
                    break;

                // Direct parent-table filters
                case 'filter_col_1':
                    $query->whereIn('po_number', $values);
                    break;
                case 'filter_col_2':
                    $query->whereIn('project_no', $values);
                    break;
                case 'filter_col_3':
                    $query->whereIn('project_name', $values);
                    break;
                case 'filter_col_4':
                    $query->whereIn('supplier', $values);
                    break;
                case 'filter_col_5':
                    $query->whereIn('is_local_supplier', $values);
                    break;
                case 'filter_col_6':
                    $query->whereIn('shipment_method', $values);
                    break;
                case 'filter_col_10':
                    $query->whereIn('is_production_manager_approved', $values);
                    break;
                case 'filter_col_11':
                    $query->whereIn('is_production_engineer_approved', $values);
                    break;
                case 'filter_col_22':
                    $query->whereIn('payment_terms', $values);
                    break;

                // All child-table filters handled below
                default:
                    $childColumnMap = [
                        'filter_col_7'  => 'artical_no',
                        'filter_col_8'  => 'description',
                        'filter_col_9'  => 'quantity',
                        'filter_col_12' => 'eta',
                        'filter_col_13' => 'is_partial_shipment',
                        'filter_col_14' => 'received_quantity',
                        'filter_col_15' => 'oa_date',
                        'filter_col_16' => 'committed_date',
                        'filter_col_17' => 'actual_readiness_date',
                        'filter_col_18' => 'eta_date_shipper',
                        'filter_col_19' => 'actual_received_date',
                        'filter_col_20' => 'shipping_refrence',
                        'filter_col_21' => 'boe',
                        'filter_col_23' => 'remarks',
                        'filter_col_24' => 'response_time',
                        'filter_col_25' => 'delivery_time',
                    ];

                    if (isset($childColumnMap[$key])) {
                        $column = $childColumnMap[$key];
                        $hasEmpty = in_array('__EMPTY__', $values);
                        $nonEmptyValues = array_filter($values, fn($v) => $v !== '__EMPTY__');
                        $query->whereHas('purchaseOrderTables', function ($q) use ($column, $nonEmptyValues, $hasEmpty) {
                            $q->where(function ($sub) use ($column, $nonEmptyValues, $hasEmpty) {
                                if (!empty($nonEmptyValues)) {
                                    $sub->whereIn($column, $nonEmptyValues);
                                }

                                if ($hasEmpty) {
                                    $sub->orWhereNull($column)->orWhere($column, '');
                                }
                            });
                        });
                    }
                    break;
            }
        }
        $purchaseOrders = $query->get();
        
        $allPurchaseOrders = PurchaseOrder::count();
        return view('production_manager.purchase_order', compact('page_title', 'purchaseOrders', 'allPurchaseOrders', 'filters', 'purchaseOrdersfilter', 'last_filter_column'));
    }

    public function addPo($productId = null, $fromStock = null){
        $currecy_converter = CurrecyConverter::orderBy('created_at', 'desc')->get();
        $usdValue = CurrecyConverter::value('1_USD');
        $aedValue = CurrecyConverter::value('1_AED');
        $eurValue = CurrecyConverter::value('1_EUR');
        $page_title = "Purchase Order";
        $product = null;
        $stockItem = null;
        $isFromStock = false;
        // Check if the URL matches the stock route
        if (request()->is('production-manager/add-po/stock/*')) {
            $isFromStock = true;
            $productId = request()->segment(4);
            $fromStock = 'stock';
        } else {
            $isFromStock = $fromStock === 'stock';
        }
        if ($productId) {
            if ($isFromStock) {
                $stockItem = StockMasterModule::find($productId);
                if (!$stockItem) {
                    return redirect()->route('procurement_manager.inbox')->with('error', 'Stock item not found.');
                }
                session(['stock_id' => $productId]); // Store stock ID in session
                session()->forget('product_id');
            } else {
                $product = ProductsOfProjects::with('projects')->find($productId);
                if (!$product) {
                    return redirect()->route('procurement_manager.inbox')->with('error', 'Product not found.');
                }
                session(['product_id' => $productId]);
            }
        } else {
            session()->forget('product_id');
            session()->forget('stock_id');
        }
        return view('production_manager.add_PO', compact('page_title', 'currecy_converter', 'usdValue', 'aedValue', 'eurValue', 'product', 'isFromStock', 'stockItem'));
    }

    public function getProjectName($project_number){
        $project = Project::where('project_no', $project_number)->first();
        if ($project) {
            return response()->json(['project_name' => $project->project_name]);
        } else {
            return response()->json(['project_name' => null], 404);
        }
    }

    public function store(Request $request){
        $request->validate([
            'PO_pdf' => 'required|mimes:pdf|max:2048',
            'PO_number' => 'required|string|max:255',
            'project_number' => 'nullable|string|max:255',
            'project_name' => 'nullable|string|max:255',
            'payment_terms' => 'required|string|max:255',
            'shipment_method' => 'required|string|max:255',
            'order_date' => 'required|string',
            'supplier' => 'required|string',
            'table_data' => 'required',
            'product_article_no' => 'nullable|string|max:255',
            'product_desc' => 'nullable|string|max:255',
            'product_qty' => 'nullable|integer|min:0',
        ]);
        // Check if coming from inbox (product_id in session)
        $isFromInbox = session('product_id') !== null;
        // Check uniqueness based on whether it's a project order
        if (!$isFromInbox && !$request->is_Project_Order) {
            $existingPo = PurchaseOrder::where('po_number', $request->PO_number)->first();
            if ($existingPo) {
                return response()->json(['error' => 'PO number already exists. When not a project order, PO number must be unique.'], 400);
            }
        } else {
            if ($request->project_number) {
                $existingCombination = PurchaseOrder::where('po_number', $request->PO_number)
                    ->where('project_no', $request->project_number)
                    ->first();

                if ($existingCombination) {
                    return response()->json([
                        'error' => "The combination of PO Number '{$request->PO_number}' and Project Number '{$request->project_number}' already exists. Please use a different combination."
                    ], 400);
                }
            }
        }
        $currency = $request->currency;
        if ($request->hasFile('PO_pdf')) {
            $file = $request->file('PO_pdf');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('purchase_order_pdf'), $fileName);
        }
        $orderDate = \DateTime::createFromFormat('F d, Y', $request->order_date);
        $formattedOrderDate = $orderDate ? $orderDate->format('Y-m-d') : null;
        $purchaseOrderData = [
            'po_pdf' => $fileName ?? null,
            'po_number' => $request->PO_number,
            'is_project_order' => $isFromInbox ? 1 : ($request->is_Project_Order ?? 0),
            'project_no' => $request->project_number,
            'is_production_manager_approved' => $request->has('is_production_manager_approved') ? 4 : 0,
            'is_production_engineer_approved' => $request->has('is_production_engineer_approved') ? 1 : 0,
            'project_name' => $request->project_name,
            'is_local_supplier' => $request->is_local_supplier ?? 0,
            'payment_terms' => $request->payment_terms,
            'shipment_method' => $request->shipment_method,
            'order_date' => $formattedOrderDate,
            'supplier' => $request->supplier,
            'product_article_no' => $request->product_article_no,
            'product_desc' => $request->product_desc,
            'product_qty' => $request->product_qty,
        ];

            // Alpesh Maru Date: 10-12-2025 Code Start
            if ($request->project_number) {
                // Check project id
                $project_id = Project::where('project_no', $request->project_number)->value('id');

                if (!$project_id) {
                    return response()->json(['error' => 'Invalid project number'], 400);
                }

                $tableDataPO = json_decode($request->table_data, true);

                if (!is_array($tableDataPO)) {
                    return response()->json(['error' => 'Invalid table_data format.'], 422);
                }

                foreach ($tableDataPO as $rowPO) {

                    // Validate required columns to avoid undefined index errors
                    if (!isset($rowPO['description']) || !isset($rowPO['artical_no']) || !isset($rowPO['quantity'])) {
                        return response()->json(['error' => 'Invalid PO row format. Required: description, artical_no, quantity'], 422);
                    }

                    // Check if BOM record exists
                    $exists_record = StockBomPo::where('project_id', $project_id)
                        ->where('description', $rowPO['description'])
                        ->where('article_no', $rowPO['artical_no'])
                        ->exists();

                    if (!$exists_record) {
                        return response()->json([
                            'error' => 'Purchase Order cannot proceed: Options and supplier selection need to be completed in the Pending BOM Check section.'
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
                            'error' => 'Please ensure the Purchase Order PDF Item Quantity matches or exceeds the BOM Item Quantity.'
                        ], 400);
                    }

                }
            }
            // Alpesh Maru Date: 10-12-2025 Code End

        try {
            $purchaseOrder = PurchaseOrder::create($purchaseOrderData);
            $tableData = json_decode($request->table_data, true);
            foreach ($tableData as $row) {
                // Look for StockMasterModule by item_desc and article_number
                $stockItem = StockMasterModule::where('item_desc', $row['description'])
                    ->where('article_number', $row['artical_no'])
                    ->first();
                // If no record found, create new StockMasterModule
                if (!$stockItem) {
                    $stockItem = new StockMasterModule();
                    $stockItem->item_desc = $row['description'];
                    $stockItem->article_number = $row['artical_no'];
                    // Set other required columns, replace defaults as needed
                    $stockItem->available_qty = 0;
                    $stockItem->minimum_required_qty = 0;
                    $stockItem->save();
                }
                // Proceed with ETA calculation and existing logic
                $etaDate = null;
                if ($stockItem->std_time !== null) {
                    $stdTimeWeeks = (int)$stockItem->std_time;
                    $daysToAdd = $stdTimeWeeks * 7;
                    $etaDate = \Carbon\Carbon::parse($formattedOrderDate)->addDays($daysToAdd)->toDateString();
                }

                PurchaseOrderTable::create([
                    'po_id' => $purchaseOrder->id,
                    'position_no' => $row['position_no'],
                    'artical_no' => $row['artical_no'],
                    'vendor_item_no' => $row['vendor_item_no'],
                    'description' => $row['description'],
                    'quantity' => $row['quantity'],
                    'unit_of_measure' => $row['unit_of_measure'],
                    'vat_per' => $row['vat_per'],
                    'direct_unit_cost' => $row['direct_unit_cost'],
                    'vat_amount' => $row['vat_amount'],
                    'amount' => $row['amount'],
                    'amount_eur' => $row['amount_eur'],
                    'currency' => $currency,
                    'eta' => $etaDate,
                ]);
            }

            // Update StockBOMPo for project orders
            if (($request->is_Project_Order || $isFromInbox) && $request->project_number) {
                $project = Project::where('project_no', $request->project_number)->first();
                if (!$project) {
                } else {
                    foreach ($tableData as $row) {
                        $stockBomPo = StockBOMPo::where('project_id', $project->id)
                            ->where('description', $row['description'])
                            ->where('article_no', $row['artical_no'])
                            ->first();

                        if ($stockBomPo) {
                            $stockBomPo->po_no = $request->PO_number;
                            $stockBomPo->save();
                        }

                        //Update ProductBOMItem for project orders
                        $productBOMItem = ProductBOMItem::where('project_id', $project->id)
                            ->where('item_desc', $row['description'])
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
                    $orderedQty = $tableData[0]['quantity'] ?? ($stockItem->minimum_required_qty - $stockItem->available_qty);
                    $stockItem->available_qty += (int)$orderedQty;
                    $stockItem->save();
                }
                session()->forget('stock_id');
            }
            session()->forget('product_id');
            return response()->json(['success' => 'Purchase order created successfully! It will appear in the Assembly Manager inbox if Production Manager approval is requested.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while saving the purchase order.'], 500);
        }
    }

    public function upload(Request $request){
        $request->validate([
            'pdf' => 'required|file|mimes:pdf',
        ]);
        if ($request->file('pdf')) {
            $targetDirectory = public_path('purchase_order_pdf');
            $fileName = uniqid() . '_' . $request->file('pdf')->getClientOriginalName();
            $file_setting = array('input_name' => 'pdf_file', 'upload_path' => 'public/purchase_order_pdf', 'allowed_types' => array('pdf'));
            $pdf_name = $request->file('pdf')->move($targetDirectory, $fileName);
            $pdf_url = public_path("purchase_order_pdf/{$fileName}");
            $headers = array(
                'Content-Type: application/json',
                'x-api-key: sec_belqwvMtPQ2jTxFpejznE52Y2pV0iObm'
            );
            $postdata = array(
                'url' => $pdf_url
            );
            $api_url = 'https://api.chatpdf.com/v1/sources/add-url';
            $ch = curl_init($api_url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata, true));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);
            $resp = json_decode($result, true);
            if (!isset($resp['sourceId'])) {
                echo "Issue with upload pdf";
                exit;
            }
            return redirect()->to('chatpdf?sourceId=' . $resp['sourceId']);
        }
        return response()->json([
            'message' => 'PDF uploaded successfully!',
            'path' => $targetDirectory . '/' . $fileName,
        ]);
    }

    public function getPurchaseOrder($id){
        $purchaseOrderTable = PurchaseOrderTable::findOrFail($id);
        // Fetch partial quantities for child rows with is_partial_shipment = 1 and pending_slot = 0
        $partialQuantities = PurchaseOrderTable::where('parent_id', $id)
            ->where('is_partial_shipment', 1)
            ->where('pending_slot', 0)
            ->pluck('quantity')
            ->toArray();
        // Add partial quantities to the response
        $response = $purchaseOrderTable->toArray();
        $response['partial_quantities'] = $partialQuantities;
        return response()->json($response);
    }

    public function updatePurchaseOrder(Request $request){
        $request->validate([
            'purchase_order_id' => 'required|exists:purchase_order_table,id',
            'oa_date' => 'nullable|date',
            'committed_date' => 'nullable|date',
            'actual_received_date' => 'nullable|date',
            'actual_readiness_date' => 'nullable|date',
            'eta_date_shipper' => 'nullable|date',
            'shipping_reference' => 'nullable|string|max:255',
            'received_quantity' => 'nullable|integer|min:0',
            'boe' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:255',
            'partial_quantity' => 'nullable|array',
            'partial_quantity.*' => 'nullable|integer|min:1',
        ]);
        $purchaseOrderTable = PurchaseOrderTable::find($request->purchase_order_id);
        if (!$purchaseOrderTable) {
            return response()->json(['error' => 'Purchase order table entry not found.'], 404);
        }
        DB::beginTransaction();
        try {
            $isPartialShipment = $request->has('is_partial_shipment');
            // Check if actual_received_date is provided or changed
            if ($request->actual_received_date && $request->actual_received_date != $purchaseOrderTable->actual_received_date) {
                $purchaseOrderTable->ard_added_date = \Carbon\Carbon::now();
            }
            // Update PurchaseOrderTable fields
            $purchaseOrderTable->oa_date = $request->oa_date;
            $purchaseOrderTable->committed_date = $request->committed_date;
            // recommended for DB        
            $purchaseOrderTable->actual_received_date = $request->actual_received_date
                ? Carbon::parse($request->actual_received_date)
                    ->setTimeFromTimeString(now()->format('H:i:s'))
                    ->format('Y-m-d H:i:s')
                : null;
            $purchaseOrderTable->actual_readiness_date = $request->actual_readiness_date;
            $purchaseOrderTable->eta_date_shipper = $request->eta_date_shipper;
            $purchaseOrderTable->shipping_refrence = $request->shipping_reference;
            $purchaseOrderTable->received_quantity = $request->received_quantity;
            $purchaseOrderTable->boe = $request->boe;
            $purchaseOrderTable->remarks = $request->remarks;
            $purchaseOrderTable->is_partial_shipment = $isPartialShipment ? 1 : 0;
            if ($isPartialShipment && $purchaseOrderTable->is_parent == 1) {
                $purchaseOrderTable->response_time = null;
                $purchaseOrderTable->delivery_time = null;
            } else {
                // Calculate response time
                if ($request->oa_date) {
                    $purchaseOrder = $purchaseOrderTable->purchaseOrder;
                    if ($purchaseOrder && $purchaseOrder->order_date) {
                        $orderDate = \Carbon\Carbon::parse($purchaseOrder->order_date);
                        $oaDate = \Carbon\Carbon::parse($request->oa_date);
                        $responseTime = $orderDate->diffInDays($oaDate, false);
                        $purchaseOrderTable->response_time = $responseTime;
                    }
                }
                // Calculate delivery time
                if ($request->actual_received_date && $request->committed_date) {
                    $committedDate = \Carbon\Carbon::parse($request->committed_date);
                    $actualReceivedDate = \Carbon\Carbon::parse($request->actual_received_date);
                    $deliveryTime = $actualReceivedDate->diffInDays($committedDate);
                    $purchaseOrderTable->delivery_time = $deliveryTime;
                }
            }

            $purchaseOrderTable->save();
            // Update StockBOMPo BOE field if BOE is provided
            if ($request->boe) {
                $purchaseOrder = $purchaseOrderTable->purchaseOrder;
                if ($purchaseOrder && $purchaseOrder->project_no) {
                    $project = Project::where('project_no', $purchaseOrder->project_no)->first();
                    if ($project) {
                        $articleNo = trim($purchaseOrderTable->artical_no);
                        $description = trim($purchaseOrderTable->description);
                        $stockBomPoRecords = StockBOMPo::where('project_id', $project->id)
                            ->where(function ($query) use ($description) {
                                $query->where('description', 'LIKE', '%' . trim($description) . '%')
                                    ->orWhere('description', trim($description));
                            })
                            ->where(function ($query) use ($articleNo) {
                                $query->where('article_no', $articleNo)
                                    ->orWhereNull('article_no')
                                    ->orWhere('article_no', '');
                            })
                            ->get();

                        foreach ($stockBomPoRecords as $stockBomPo) {
                            $stockBomPo->boe = $request->boe;
                            $stockBomPo->save();
                        }
                    }
                }
            }

            // Handle partial shipment logic
            if ($isPartialShipment && !empty($request->partial_quantity) && is_array($request->partial_quantity)) {
                $partialQuantities = array_filter($request->partial_quantity, function ($qty) {
                    return !empty($qty) && $qty > 0;
                });
                $originalQuantity = $purchaseOrderTable->quantity;
                // Get existing child rows and calculate the sum of existing partial shipments
                $existingChildren = PurchaseOrderTable::where('parent_id', $purchaseOrderTable->id)->get();
                $existingPartialSum = $existingChildren->where('pending_slot', 0)->sum('quantity'); // Sum of existing partial shipments
                $newPartialSum = array_sum($partialQuantities);
                $totalPartialSum = $existingPartialSum + $newPartialSum;
                // Validate that total partial quantities (existing + new) do not exceed original quantity
                if ($totalPartialSum > $originalQuantity) {
                    throw new \Exception("Total of partial quantities ($totalPartialSum) cannot exceed the original quantity ($originalQuantity).");
                }
                // Delete only the remaining quantity child row (pending_slot = 1), if it exists
                $remainingChild = $existingChildren->where('pending_slot', 1)->first();
                if ($remainingChild) {
                    $remainingChild->delete();
                }

                // Create new child rows for the new partial quantities
                foreach ($partialQuantities as $newQty) {
                    $childRecord = $purchaseOrderTable->replicate();
                    $childRecord->is_parent = 0;
                    $childRecord->is_partial_shipment = 1;
                    $childRecord->parent_id = $purchaseOrderTable->id;
                    $childRecord->quantity = $newQty;
                    $childRecord->pending_slot = 0;
                    $childRecord->oa_date = $request->oa_date;
                    $childRecord->committed_date = $request->committed_date;
                    $childRecord->actual_readiness_date = $request->actual_readiness_date;
                    $childRecord->eta_date_shipper = $request->eta_date_shipper;
                    $childRecord->actual_received_date = $request->actual_received_date;
                    $childRecord->shipping_refrence = $request->shipping_reference;
                    $childRecord->received_quantity = $request->received_quantity;
                    $childRecord->boe = $request->boe;
                    $childRecord->remarks = $request->remarks;
                    if ($request->actual_received_date) {
                        $childRecord->ard_added_date = \Carbon\Carbon::now();
                    }
                    if ($request->oa_date) {
                        $purchaseOrder = $purchaseOrderTable->purchaseOrder;
                        if ($purchaseOrder && $purchaseOrder->order_date) {
                            $orderDate = \Carbon\Carbon::parse($purchaseOrder->order_date);
                            $oaDate = \Carbon\Carbon::parse($request->oa_date);
                            $childRecord->response_time = $orderDate->diffInDays($oaDate, false);
                        }
                    }
                    if ($request->actual_received_date && $request->committed_date) {
                        $committedDate = \Carbon\Carbon::parse($request->committed_date);
                        $actualReceivedDate = \Carbon\Carbon::parse($request->actual_received_date);
                        $childRecord->delivery_time = $actualReceivedDate->diffInDays($committedDate);
                    }
                    $childRecord->save();
                }

                // Create or update a child row for the remaining quantity, if any
                $remainingQuantity = $originalQuantity - $totalPartialSum;
                if ($remainingQuantity > 0) {
                    $childRecord = $purchaseOrderTable->replicate();
                    $childRecord->is_parent = 0;
                    $childRecord->is_partial_shipment = 0;
                    $childRecord->parent_id = $purchaseOrderTable->id;
                    $childRecord->quantity = $remainingQuantity;
                    $childRecord->pending_slot = 1;
                    $childRecord->oa_date = $request->oa_date;
                    $childRecord->committed_date = $request->committed_date;
                    $childRecord->actual_readiness_date = $request->actual_readiness_date;
                    $childRecord->eta_date_shipper = $request->eta_date_shipper;
                    $childRecord->actual_received_date = $request->actual_received_date;
                    $childRecord->shipping_refrence = $request->shipping_reference;
                    $childRecord->received_quantity = $request->received_quantity;
                    $childRecord->boe = $request->boe;
                    $childRecord->remarks = $request->remarks;
                    if ($request->actual_received_date) {
                        $childRecord->ard_added_date = \Carbon\Carbon::now();
                    }
                    if ($request->oa_date) {
                        $purchaseOrder = $purchaseOrderTable->purchaseOrder;
                        if ($purchaseOrder && $purchaseOrder->order_date) {
                            $orderDate = \Carbon\Carbon::parse($purchaseOrder->order_date);
                            $oaDate = \Carbon\Carbon::parse($request->oa_date);
                            $childRecord->response_time = $orderDate->diffInDays($oaDate, false);
                        }
                    }
                    if ($request->actual_received_date && $request->committed_date) {
                        $committedDate = \Carbon\Carbon::parse($request->committed_date);
                        $actualReceivedDate = \Carbon\Carbon::parse($request->actual_received_date);
                        $childRecord->delivery_time = $actualReceivedDate->diffInDays($committedDate);
                    }
                    $childRecord->save();
                }
            } else {
                // If partial shipment is not selected, delete all child rows
                $existingChildren = PurchaseOrderTable::where('parent_id', $purchaseOrderTable->id)->get();
                foreach ($existingChildren as $child) {
                    $child->delete();
                }
            }

            DB::commit();

            return response()->json(['success' => 'Purchase order updated successfully!'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function edit($id){
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        $purchaseOrderTables = PurchaseOrderTable::where('po_id', $id)->get();
        $currecy_converter = CurrecyConverter::orderBy('created_at', 'desc')->get();

        $usdValue = CurrecyConverter::value('1_USD');
        $aedValue = CurrecyConverter::value('1_AED');
        $eurValue = CurrecyConverter::value('1_EUR');

        $page_title = "Edit Purchase Order";

        return view('production_manager.edit_PO', compact(
            'purchaseOrder', 'purchaseOrderTables',
            'currecy_converter', 'usdValue', 'aedValue', 'eurValue', 'page_title'
        ));
    }
    
    public function update(Request $request, $id){
        try {
            $purchaseOrder = PurchaseOrder::findOrFail($id);

            /* ---------------- VALIDATION ---------------- */
            $poNumber  = $request->PO_number;
            $isProject = $request->is_Project_Order;
            $projectNo = $request->input('is_Project_Order') == 1 ? $request->input('project_number') : null;
            $exists = PurchaseOrder::where('po_number', $poNumber)
                ->when($isProject, fn ($q) => $q->where('project_no', $projectNo))
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => $isProject
                        ? "PO + Project Number already exists."
                        : "PO number already exists."
                ], 400);
            }

            /* ---------------- UPDATE MAIN FIELDS ---------------- */
            $purchaseOrder->update([
                'po_number'        => $poNumber,
                'is_project_order' => $isProject ? 1 : 0,
                'payment_terms'    => $request->payment_terms,
                'shipment_method'  => $request->shipment_method,
                'order_date'       => Carbon::parse($request->order_date)->toDateString(),
                'is_local_supplier'=> $request->is_local_supplier ?? 0,
                'project_no'       => $isProject ? $projectNo : null,
                'project_name'     => $isProject ? $request->project_name : null,
            ]);

            /* ---------------- CREATE PROJECT FOLDERS ---------------- */
            $this->buildProjectFolders($projectNo);
            /* ---------------- DELETE SELECTED FILES ---------------- */
           if ($request->delete_oa_files) {
                $this->deletePOFiles($request->delete_oa_files, $purchaseOrder, 'oa');

                // IMPORTANT: Reload updated file array
                $purchaseOrder->refresh();
            }
            if ($request->delete_invoice_files) {
                $this->deletePOFiles($request->delete_invoice_files, $purchaseOrder, 'invoice');
                $purchaseOrder->refresh();
            }
            if ($request->delete_boe_files) {
                $this->deletePOFiles($request->delete_boe_files, $purchaseOrder, 'boe');
                $purchaseOrder->refresh();
            }

            /* ---------------- MULTI UPLOAD: OA + INVOICE + BOE ---------------- */
            $purchaseOrder->oa_file  = $this->uploadPOFiles($request, $purchaseOrder, $projectNo, $poNumber, 'oa');
            $purchaseOrder->invoice_file = $this->uploadPOFiles($request, $purchaseOrder, $projectNo, $poNumber, 'invoice');
            $purchaseOrder->boe_file    = $this->uploadPOFiles($request, $purchaseOrder, $projectNo, $poNumber, 'boe');
            $purchaseOrder->save();

            return response()->json([
                'success' => true,
                'message' => 'Purchase order updated successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Error: " . $e->getMessage()
            ], 500);
        }
    }

    private function buildProjectFolders($projectNo){
        $types = ['oa', 'invoice', 'boe'];

        foreach ($types as $type) {
            $path = public_path("project_document/{$projectNo}/po_and_invoices/{$type}");
            if (!File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }
        }
    }

    private function uploadPOFiles($request, $purchaseOrder, $projectNo, $poNumber, $type){
        $allowed = ['doc', 'docx', 'pdf', 'xls', 'xlsx'];
        $maxSize = 2048; // KB

        $existing = $purchaseOrder->{$type . '_file'} ?? [];
        $new = [];
        $files = $request->file($type . '_files');

        if (!$files) return $existing;

        $basePath = public_path("project_document/{$projectNo}/po_and_invoices/{$type}");

        foreach ($files as $file) {

            if (!in_array($file->getClientOriginalExtension(), $allowed) ||
                $file->getSize() > $maxSize * 1024) {

                throw new \Exception("Invalid {$type} file.");
            }

            $original = $file->getClientOriginalName();
            $fileName = "{$poNumber}_{$projectNo}_" . time() . "_{$original}";
            $file->move($basePath, $fileName);

            $new[] = "project_document/{$projectNo}/po_and_invoices/{$type}/{$fileName}";
        }

        return array_values(array_merge($existing, $new));
    }

    private function deletePOFiles($deleteList, $purchaseOrder, $type){
        $existing = $purchaseOrder->{$type . '_file'} ?? [];
        foreach ($deleteList as $filePath) {
            if (File::exists(public_path($filePath))) {
                File::delete(public_path($filePath));
            }
            $existing = array_filter($existing, fn($f) => $f !== $filePath);
        }
        $purchaseOrder->{$type . '_file'} = array_values($existing);
        $purchaseOrder->save();
    }

    public function exportCSV(Request $request){
        $filters = $request->input('filters', []);

        // Load only matching child rows
        $query = PurchaseOrder::with(['purchaseOrderTables' => function ($q) use ($filters) {
            $q->orderBy('id', 'desc');

            $columnMap = [
                'filter_col_7'  => 'artical_no',
                'filter_col_8'  => 'description',
                'filter_col_9'  => 'quantity',
                'filter_col_12' => 'eta',
                'filter_col_13' => 'is_partial_shipment',
                'filter_col_14' => 'received_quantity',
                'filter_col_15' => 'oa_date',
                'filter_col_16' => 'committed_date',
                'filter_col_17' => 'actual_readiness_date',
                'filter_col_18' => 'eta_date_shipper',
                'filter_col_19' => 'actual_received_date',
                'filter_col_20' => 'shipping_refrence',
                'filter_col_21' => 'boe',
                'filter_col_23' => 'remarks',
                'filter_col_24' => 'response_time',
                'filter_col_25' => 'delivery_time',
            ];

            foreach ($columnMap as $filterKey => $dbColumn) {
                if (!empty($filters[$filterKey])) {
                    $filterValues = $filters[$filterKey];

                    $hasEmpty = in_array('__EMPTY__', $filterValues);
                    $nonEmptyValues = array_filter($filterValues, fn($v) => $v !== '__EMPTY__');

                    $q->where(function ($subQuery) use ($dbColumn, $hasEmpty, $nonEmptyValues) {
                        if (!empty($nonEmptyValues)) {
                            $subQuery->whereIn($dbColumn, $nonEmptyValues);
                        }

                        if ($hasEmpty) {
                            $subQuery->orWhereNull($dbColumn)->orWhere($dbColumn, '');
                        }
                    });
                }
            }
        }])
            ->where('is_production_engineer_approved', 1)
            ->whereIn('is_production_manager_approved', [0, 1])
            ->orderBy('id', 'desc');

        // Apply parent-level filters
        foreach ($filters as $key => $values) {
            if (empty($values)) continue;

            switch ($key) {
                case 'filter_col_0': // order_date - special date parsing
                    $query->where(function ($q) use ($values) {
                        foreach ($values as $val) {
                            try {
                                $date = Carbon::parse($val)->format('Y-m-d');
                                $q->orWhereDate('order_date', $date);
                            } catch (\Exception $e) {
                                // Invalid date, skip
                            }
                        }
                    });
                    break;

                // Direct parent-table filters
                case 'filter_col_1':
                    $query->whereIn('po_number', $values);
                    break;
                case 'filter_col_2':
                    $query->whereIn('project_no', $values);
                    break;
                case 'filter_col_3':
                    $query->whereIn('project_name', $values);
                    break;
                case 'filter_col_4':
                    $query->whereIn('supplier', $values);
                    break;
                case 'filter_col_5':
                    $query->whereIn('is_local_supplier', $values);
                    break;
                case 'filter_col_6':
                    $query->whereIn('shipment_method', $values);
                    break;
                case 'filter_col_10':
                    $query->whereIn('is_production_manager_approved', $values);
                    break;
                case 'filter_col_11':
                    $query->whereIn('is_production_engineer_approved', $values);
                    break;
                case 'filter_col_22':
                    $query->whereIn('payment_terms', $values);
                    break;

                // All child-table filters handled below
                default:
                    $childColumnMap = [
                        'filter_col_7'  => 'artical_no',
                        'filter_col_8'  => 'description',
                        'filter_col_9'  => 'quantity',
                        'filter_col_12' => 'eta',
                        'filter_col_13' => 'is_partial_shipment',
                        'filter_col_14' => 'received_quantity',
                        'filter_col_15' => 'oa_date',
                        'filter_col_16' => 'committed_date',
                        'filter_col_17' => 'actual_readiness_date',
                        'filter_col_18' => 'eta_date_shipper',
                        'filter_col_19' => 'actual_received_date',
                        'filter_col_20' => 'shipping_refrence',
                        'filter_col_21' => 'boe',
                        'filter_col_23' => 'remarks',
                        'filter_col_24' => 'response_time',
                        'filter_col_25' => 'delivery_time',
                    ];

                    if (isset($childColumnMap[$key])) {
                        $column = $childColumnMap[$key];

                        $hasEmpty = in_array('__EMPTY__', $values);
                        $nonEmptyValues = array_filter($values, fn($v) => $v !== '__EMPTY__');

                        $query->whereHas('purchaseOrderTables', function ($q) use ($column, $nonEmptyValues, $hasEmpty) {
                            $q->where(function ($sub) use ($column, $nonEmptyValues, $hasEmpty) {
                                if (!empty($nonEmptyValues)) {
                                    $sub->whereIn($column, $nonEmptyValues);
                                }

                                if ($hasEmpty) {
                                    $sub->orWhereNull($column)->orWhere($column, '');
                                }
                            });
                        });
                    }
                    break;
            }
        }
        // Final query
        $purchaseOrders = $query->get();
        // Export CSV Code
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=purchase_order_details_filter_export_{$timestamp}.csv",
        ];
        $columns = [
            'SR No',
            'Date',
            'PO NO.',
            'Project no',
            'Project Name',
            'Supplier',
            'Local Supplier',
            'Shipment Method',
            'Article',
            'Description',
            'Ordered Qty',
            'Prod. Manager Status',
            'Prod. Engineer Status',
            'ETA Date',
            'Partial Shipment',
            'Received Qty',
            'OA Date',
            'Committed Date',
            'Actual Readiness Date',
            'ETA Date (Shipper)',
            'Actual Received Date',
            'Shipping reference',
            'BOE',
            'Payment Terms',
            'Remarks',
            'Response Time',
            'Delivery time'
        ];
        $callback = function () use ($purchaseOrders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            $sr_no = 1;

            foreach ($purchaseOrders as $order) {
                foreach ($order->purchaseOrderTables as $table) {
                    if ($table->parent_id !== null) continue;

                    $isPartialParent = $table->is_partial_shipment == 1 && $table->is_parent == 1;

                    // Conditional: Only fetch statuses & times when not a partial parent
                    $prodManagerStatus = $prodEngineerStatus = $responseTime = $deliveryTime = '';
                    if (!$isPartialParent) {
                        [$mgrStatus, $mgrDate, $engStatus, $engDate] = $this->getProductionStatuses($order);
                        $prodManagerStatus  = $mgrStatus . ' ' . $mgrDate;
                        $prodEngineerStatus = $engStatus . ' ' . $engDate;
                        $responseTime       = $this->formatDays($table->response_time);
                        $deliveryTime       = $this->formatDays($table->delivery_time);
                    }

                    // Parent Row
                    fputcsv($file, [
                        $sr_no++,
                        $this->formatDate($order->order_date),
                        '="' . $order->po_number . '"',
                        '="' . ($order->project_no ?? 'N/A') . '"',
                        $order->project_name ?? 'N/A',
                        $order->supplier,
                        $order->is_local_supplier == 1 ? 'Yes' : 'No',
                        $order->shipment_method,
                        '="' . $table->artical_no . '"',
                        $table->description,
                        '="' . $table->quantity . '"',
                        $prodManagerStatus,
                        $prodEngineerStatus,
                        $this->formatDate($table->eta),
                        $isPartialParent ? '' : ($table->is_partial_shipment ? 'Yes' : 'No'),
                        $isPartialParent ? '' : ($table->received_quantity ?? 'N/A'),
                        $isPartialParent ? '' : $this->formatDate($table->oa_date),
                        $isPartialParent ? '' : $this->formatDate($table->committed_date),
                        $isPartialParent ? '' : $this->formatDate($table->actual_readiness_date),
                        $isPartialParent ? '' : $this->formatDate($table->eta_date_shipper),
                        $isPartialParent ? '' : $this->formatDate($table->actual_received_date),
                        $isPartialParent ? '' : ('="' . ($table->shipping_refrence ?? 'N/A') . '"'),
                        $isPartialParent ? '' : ('="' . ($table->boe ?? 'N/A') . '"'),
                        $isPartialParent ? '' : $order->payment_terms,
                        $isPartialParent ? '' : ($table->remarks ?? 'N/A'),
                        $responseTime,
                        $deliveryTime,
                    ]);

                    // --- Child Rows ---
                    foreach ($order->purchaseOrderTables as $childTable) {
                        if ($childTable->parent_id != $table->id) continue;

                        [$mgrStatus, $mgrDate, $engStatus, $engDate] = $this->getProductionStatuses($order);

                        fputcsv($file, [
                            $sr_no++,
                            $this->formatDate($order->order_date),
                            '="' . $order->po_number . '"',
                            '="' . ($order->project_no ?? 'N/A') . '"',
                            $order->project_name ?? 'N/A',
                            $order->supplier,
                            $childTable->is_local_supplier == 1 ? 'Yes' : 'No',
                            $order->shipment_method,
                            '="' . $childTable->artical_no . '"',
                            $childTable->description,
                            '="' . $childTable->quantity . '"',
                            $mgrStatus . ' ' . $mgrDate,
                            $engStatus . ' ' . $engDate,
                            $this->formatDate($childTable->eta),
                            $childTable->pending_slot == 0 ? 'Yes' : 'No',
                            $childTable->received_quantity ?? 'N/A',
                            $this->formatDate($childTable->oa_date),
                            $this->formatDate($childTable->committed_date),
                            $this->formatDate($childTable->actual_readiness_date),
                            $this->formatDate($childTable->eta_date_shipper),
                            $this->formatDate($childTable->actual_received_date),
                            '="' . ($childTable->shipping_refrence ?? 'N/A') . '"',
                            '="' . ($childTable->boe ?? 'N/A') . '"',
                            $order->payment_terms,
                            $childTable->remarks ?? 'N/A',
                            $this->formatDays($childTable->response_time),
                            $this->formatDays($childTable->delivery_time),
                        ]);
                    }
                }
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    // Utility: format date
    public function formatDate($date){
        return $date ? Carbon::parse($date)->format('d-m-Y') : 'N/A';
    }

    // Utility: format numeric days
    public function formatDays($value){
        if ($value === null) return 'N/A';
        return $value < 0 ? abs($value) . ' days delay' : $value . ' days';
    }

    // Utility: get production statuses
    public function getProductionStatuses($order){
        $managerStatus = 'Not Required';
        $managerDate = null;

        if ($order->is_production_manager_approved == 1) {
            $managerStatus = 'Approved';
            $managerDate = $order->production_manager_approved_date
                ? Carbon::parse($order->production_manager_approved_date)->format('d-m-Y')
                : null;
        } elseif ($order->production_manager_reject_date) {
            $managerStatus = 'Rejected';
            $managerDate = Carbon::parse($order->production_manager_reject_date)->format('d-m-Y');
        }

        $engineerStatus = 'Approved';
        $engineerDate = Carbon::parse($order->production_engineer_approved_date)->format('d-m-Y');

        return [$managerStatus, $managerDate, $engineerStatus, $engineerDate];
    }
    
    public function updateReceivedDate(Request $request){
        $request->validate([
            'id'   => 'required|integer|exists:purchase_order_table,id',
            'date' => 'required|date_format:Y-m-d H:i:s'  // safer: ensures 2025-10-17 12:15:52 format
        ]);
        $order = PurchaseOrderTable::findOrFail($request->id);
        // Save as proper DB date (Y-m-d)
        $order->actual_received_date = Carbon::parse($request->date)
                ->setTimeFromTimeString(now()->format('H:i:s'))
                ->format('Y-m-d H:i:s');

        $order->ard_added_date = \Carbon\Carbon::now();
        $order->save();

        return response()->json([
            'success' => true,
            'date'    => $order->actual_received_date
        ]);
    }
}
