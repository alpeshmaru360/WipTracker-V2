<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinalInspectionTable extends Model
{
    use HasFactory;

    protected $table = 'final_inspection_data';

    protected $fillable = [
        'project_no', 
        'project_name', 
        'artical_no',
        'serial_no', 
        'qty',
        'unit_qty',
        'description', 
        'pump_type', 
        'product_image', 
        'reports_docs', 
        'test_reports_docs',
        'product_article_no',
        'product_desc'
    ];

    // Relationship to get Pump Name
    public function pumpType(): BelongsTo
    {
        return $this->belongsTo(FinalInspection::class, 'pump_type');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_no', 'project_no');
    }
}
