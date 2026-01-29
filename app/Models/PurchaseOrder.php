<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $table = 'purchase_order';

    protected $fillable = [
        'po_pdf',
        'po_number',
        'is_project_order',
        'project_no',
        'is_production_manager_approved',
        'is_production_engineer_approved',
        'project_name',
        'is_local_supplier',
        'payment_terms',
        'shipment_method',
        'currency',
        'order_date',
        'supplier',
        'approved_remarks_production_manager',
        'approved_remarks_production_engineer',
        'rejection_reason_production_manager',
        'rejection_reason_pro',
        'oa_file',
        'invoice_file',
        'boe_file',
        'product_article_no',
        'product_desc',
        'product_qty',
        'production_manager_approved_date',
        'production_manager_reject_date',
    ];

    protected $casts = [
        'oa_file' => 'array',
        'invoice_file' => 'array',
        'boe_file' => 'array',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function poNCR()
    {
        return $this->hasMany(NCR::class, 'po', 'po_number');
    }

    public function purchaseOrderTables()
    {
        return $this->hasMany(PurchaseOrderTable::class, 'po_id', 'id');
    }

    public function isApproved()
    {
        return $this->is_production_manager_approved == 1 && $this->is_production_engineer_approved == 1;
    }

    public function isRejected()
    {
        return $this->is_production_manager_approved == 2 && $this->is_production_engineer_approved == 2;
    }

    public function getStatusAttribute()
    {
        if ($this->is_production_manager_approved == 0) {
            return 'Aproval Not Required';
        } elseif ($this->is_production_manager_approved == 1 && $this->is_production_engineer_approved == 1) {
            return 'Approved';
        }
        return 'Unknown';
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_no', 'project_no');
    }
}
