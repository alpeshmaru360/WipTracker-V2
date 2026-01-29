<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $table = 'stock';  // Specify the table name
    
    // Define fillable attributes to prevent mass assignment vulnerability
    protected $fillable = [
        'arts', 
        'des', 
        'qty_actual_stock', 
        'qty_reserved', 
        'qty_in_order'
    ];
}
