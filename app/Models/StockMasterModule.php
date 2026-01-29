<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMasterModule extends Model
{
    use HasFactory;

    protected $table = 'stock_master_module';  // Specify the table name
    
    protected $fillable = [
        'item_desc', 
        'article_number', 
        'adder_code',
        'qty', 
        'hold_qty', 
        'available_qty',
        'qty_in_order',
        'minimum_required_qty', 
        'price', 
        'total_price',
        'std_time'
    ];
    
    public function stockBomPo()
    {
        return $this->hasMany(StockBOMPo  ::class, 'description', 'item_desc');
    }

}
