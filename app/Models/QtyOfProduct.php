<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QtyOfProduct extends Model
{
    use HasFactory;
    protected $table = 'qty_of_products';
    
    protected $fillable = [
        'PL_PDF_path',
        'pl_uploaded_date', 
    ];

    public function projects()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function products()
    {
        return $this->belongsTo(ProductsOfProjects::class, 'product_id', 'id');
    }
}
