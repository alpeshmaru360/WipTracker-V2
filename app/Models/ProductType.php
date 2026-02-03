<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    use HasFactory;
    protected $table = 'product_types';

    protected $fillable = [
        'project_type_name',
        'product_family_number',
        'limitation_per_shift',
        'estimated_product_type_weeks',
        'operator_id',
        'is_active'
    ];

    public function operator(){
        return $this->belongsTo(User::class, 'operator_id', 'id');
    }
    public function product_wise_std_time_as_per_keyword(){
        return $this->hasMany(ProcurementStandardTime::class,'product_type_id','id');
    }
}
