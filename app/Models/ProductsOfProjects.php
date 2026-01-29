<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductsOfProjects extends Model
{
    use HasFactory;

    protected $table = 'products_of_projects';

    protected $fillable = [
        'project_id',
        'article_number',
        'full_article_number',
        'description',
        'qty',
        'editable_drawing_path',
        'bom_req_estimation_manager',
        'bom_check_procurement_manager',
        'bom_path',
        'drawing_req_estimation_manager',
        'drawing_check_procurement_manager',
        'drawing_path',
        'drawing_upload_by_estimation_manager',
        'bom_remarks_by_estimation_manager',
        'drawing_remarks_by_estimation_manager',
        'is_final_inspection_started',
        'is_asbuilt_drawing_pdf_approve_by_production_superwisor',
        'is_asbuilt_drawing_pdf_approve_by_estimation_manager',
        'asbuilt_drawing_approve_reject_remarks_by_production_superwisor',
        'asbuilt_drawing_approve_reject_remarks_by_estimation_manager',
        'qr',
        'currency_wise_sales_unit_value',
        'currency_wise_sales_total_value',
    ];

    public function projects(){
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }
    public function qty_of_products(){
        return $this->hasMany(QtyOfProduct::class,'product_id','id');
    }
    public function operator(){
        return $this->belongsTo(User::class, 'operator_id', 'id');
    }
    public function assignedProducts(){
        return $this->hasMany(AssignedProductToOperator::class, 'product_id', 'id');
    }
    public function editProject($id){
        $project = Project::with('productsOfProjects')->findOrFail($id);
        return view('production_manager.edit_project', compact('project', 'bom_data'));
    }
    public function projectProcessStdTimes(){
        return $this->hasMany(ProjectProcessStdTime::class, 'product_id', 'id');
    }
    public function project(){
        return $this->belongsTo(Project::class, 'project_id');
    }
    public function assignedOperatorProductQtyWiseIDs(){
        return $this->hasMany(AssignedProductToOperator::class, 'product_id', 'id');
    }
}
