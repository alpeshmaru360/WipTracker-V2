<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderTable extends Model
{
    use HasFactory;

    protected $table = 'purchase_order_table';

    protected $fillable = [
        'po_id',
        'position_no',
        'artical_no',
        'vendor_item_no',
        'description',
        'quantity',
        'unit_of_measure',
        'vat_per',
        'direct_unit_cost',
        'vat_amount',
        'amount',
        'amount_eur',
        'currency',
        'oa_date',
        'committed_date',
        'actual_received_date',
        'received_quantity',
        'shipping_refrence',
        'actual_readiness_date',
        'remarks',
        'response_time',
        'delivery_time',
        'is_initial_inspection_started',
        'is_partial_shipment',
        'is_parent',
        'parent_id',
        'boe',
        'eta_date_shipper',
        'eta',
        'pending_slot',
        'ard_added_date'
    ];

    public function purchaseOrder(){
        return $this->belongsTo(PurchaseOrder::class, 'po_id'); // Assuming 'po_id' is the foreign key
    }
    // Define the childRows relationship
    public function childRows(){
        return $this->hasMany(PurchaseOrderTable::class, 'parent_id', 'id');
    }
}
