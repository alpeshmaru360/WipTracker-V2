<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductBOMItem extends Model
{
    use HasFactory;

    protected $table = 'product_BOM_item';

    protected $fillable = [
        'item_desc',
        'wilo_article_no',
        'item_qty',
        'product_qty',
        'total_required_qty',
        'project_id',
        'product_id',
        'full_article_no',
        'cart_model_name',
        'quotation_no',
        'created_at',
        'updated_at',
    ];
}
