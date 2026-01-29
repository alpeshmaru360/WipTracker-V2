<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockHistory extends Model
{
    use HasFactory;

    // Define the table name explicitly if it's different from the default plural form
    protected $table = 'stock_history';

    // Define the fillable columns
    protected $fillable = ['project_id','product_id','item_desc','item_article_no','used_qty','delivery_status','created_at','updated_at'];
}
