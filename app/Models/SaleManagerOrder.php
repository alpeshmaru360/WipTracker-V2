<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class SaleManagerOrder extends Model
{
    use HasFactory;
    protected $table = 'sales_manager_order';

    protected $fillable = [
        'project_id',
        'quotation_number',
        'quotation_from_pricing_tool',
        'article_number',
        'full_article_number',
        'description',
        'qty',
        'product_type',
        'cart_model_name',
        'expected_order_date',
        'expected_delivery_date',
    ];
}
