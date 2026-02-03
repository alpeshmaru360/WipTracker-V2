<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NCR extends Model
{
    use HasFactory;

    protected $table = 'ncr';

    protected $fillable = [
        'cia_no',
        'related_dep',
        'ncr_type',
        'ncr_no',
        'project_no',
        'project',
        'po',
       'material_description',
       'ncr_description',
       'article_number',
       'quantity',
       'name_surname',
       'signature',
       'detected_department',
       'is_corrective_action',
       'is_correction',
       'is_improvement_action',
       'root_cause',
       'action_to_prevent_misuse',
       'planned_action_date',
       'related_authorized_personnel',
       'related_authorized_personnel_signature',
       'quality_management_representative',
       'is_nonconformity_corrected',
       'is_nonconformity_not_corrected',
       'is_additional_time',
       'corrective_preventive_action',
       'follow_up',
       'action_closed_date',
       'related_authorized_personnel_final',
       'quality_management_representative_date',
       'ncr_photos',
       'remark',
    ];
}
