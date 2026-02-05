<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockBOMPo extends Model
{
    use HasFactory;

    protected $table = 'stock_bom_po'; 

    protected $fillable = [
        'project_id',
        'product_id',
        'description',
        'article_no',
        'item_quantity',
        'product_quantity',
        'total_required_quantity',
        'hold_qty',
        'stock_quantity',
        'price_aed',
        'select_option',
        'supplier',
        'po_no',
        'po_added',
        'boe',
        'processed_at',
        'release_qty', 
    ];

    public function projects(){
        return $this->belongsTo(Project::class, 'project_id','id');
    }
    public function productsOfProjects(){
        return $this->belongsTo(ProductsOfProjects::class, 'product_id', 'id');
    }
    public function stockMaster(){
        return $this->belongsTo(StockMasterModule::class, 'description', 'item_desc');
    }
}
