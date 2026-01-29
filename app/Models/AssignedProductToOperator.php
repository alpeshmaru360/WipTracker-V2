<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignedProductToOperator extends Model
{
    use HasFactory;
    protected $table = 'assigned_products_operators';

    public function product()
    {
        return $this->belongsTo(ProductsOfProjects::class, 'product_id', 'id');
    }

    public function projects()
    {
        return $this->belongsTo(Project::class, 'project_id','id');
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id', 'id');
    }
}
