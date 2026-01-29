<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InitialInspectionTable extends Model
{
    use HasFactory;

    protected $table = 'initial_inspection_data';

    protected $fillable = [
        'po_number', 'supplier', 'artical_no',
        'project_no', 'project_name', 'pump_type', 'reports_docs','description','quantity','ini_inspection_date'
    ];

    // Relationship to get Pump Name
    public function pumpType(): BelongsTo
    {
        return $this->belongsTo(InitialInspectionName::class, 'pump_type');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_no', 'project_no');
    }
}