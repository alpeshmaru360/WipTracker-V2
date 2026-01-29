<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcurementStandardTime extends Model
{
    use HasFactory;

    protected $table = 'procurement_standard_time';

    protected $fillable = ['product_type_id', 'product_type', 'keyword', 'total_days'];

    public function product_type_name(){
        return $this->belongsTo(ProductType::class, 'product_type_id', 'id');
    }
    public function productTypeRelation(){
        return $this->belongsTo(ProductType::class, 'product_type_id');
    }
}
