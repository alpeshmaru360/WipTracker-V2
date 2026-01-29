<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $table = 'projects';
    protected $fillable = ['project_name', 'project_no', 'status', 'is_rejected_by_pro_eng', 
    'rejection_selected_reason', 'rejection_date', 'project_delete_reason', 'is_deleted', 'deleted_at']; // A Code: 01-01-2026

    public function product(){
        return $this->hasMany(ProductsOfProjects::class,'project_id','id');
    }
    public function qty_of_product_of_projects(){
        return $this->hasMany(QtyOfProduct::class,'project_id','id');
    }
    public function productsOfProjects()
    {
        return $this->hasMany(ProductsOfProjects::class, 'project_id', 'id');
    }
    public function productsProcess(){
        return $this->hasMany(ProjectProcessStdTime::class, 'projects_id', 'id');
    }
    public function projectStatus(){
        return $this->hasMany(ProjectStatus::class, 'project_id' ,'id');  
    }
    public function purchaseOrders(){
        return $this->hasMany(PurchaseOrder::class, 'project_no' ,'project_no');
    } 
    public function InitialInspection(){
        return $this->hasMany(InitialInspectionTable::class, 'project_no' ,'project_no');
    } 
    public function FinalInspection(){
        return $this->hasMany(FinalInspectionTable::class, 'project_no' ,'project_no');
    }
    public function check_status_project_create(){
        $project_deadline_time = AdminHoursManagement::where('lable','StandardProcessTimes')
            ->where('key','create_new_project')
            ->where('is_deleted', 0)
            ->value('value');   
        $wiTrack_project_create_date_original = Project::where('id', $this->id)->value('created_at');
        $wiTrack_project_create_date = \Carbon\Carbon::parse($wiTrack_project_create_date_original)->toDateTimeString();
        $wipTrack_project_create_date = ProjectStatus::where('project_id', $this->id)->value('project_creation');

        $wiTrackCarbon = \Carbon\Carbon::parse($wiTrack_project_create_date);
        $wipTrackCarbon = \Carbon\Carbon::parse($wipTrack_project_create_date);

        // Calculate difference in hours
        $difference_in_hours = $wiTrackCarbon->diffInHours($wipTrackCarbon);

        return [
            'deadline_time' => $project_deadline_time,
            'witrack_create_date' => $wiTrackCarbon->toDateTimeString(),
            'wip_create_date' => $wipTrackCarbon->toDateTimeString(),
            'hours_difference' => $difference_in_hours,
        ];
        // return $wiTrack_project_create_date,$wipTrack_project_create_date;
    }
    public function check_status_initial_inspection(){
        $project_deadline_time = AdminHoursManagement::where('lable','StandardProcessTimes')
            ->where('key','initial_inspection')
            ->where('is_deleted', 0)
            ->value('value');   
        $wiTrack_project_create_date_original = Project::where('id', $this->id)->value('created_at');
        $wiTrack_project_create_date = \Carbon\Carbon::parse($wiTrack_project_create_date_original)->toDateTimeString();
        $wipTrack_project_create_date = ProjectStatus::where('project_id', $this->id)->value('project_creation');

        $wiTrackCarbon = \Carbon\Carbon::parse($wiTrack_project_create_date);
        $wipTrackCarbon = \Carbon\Carbon::parse($wipTrack_project_create_date);

        // Calculate difference in hours
        $difference_in_hours = $wiTrackCarbon->diffInHours($wipTrackCarbon);

        return [
            'deadline_time' => $project_deadline_time,
            'witrack_create_date' => $wiTrackCarbon->toDateTimeString(),
            'wip_create_date' => $wipTrackCarbon->toDateTimeString(),
            'hours_difference' => $difference_in_hours,
        ];
        // return $wiTrack_project_create_date,$wipTrack_project_create_date;
    }
    public function check_status_final_inspection(){
        $project_deadline_time = AdminHoursManagement::where('lable','StandardProcessTimes')
            ->where('key','final_inspection')
            ->where('is_deleted', 0)
            ->value('value');   
        $wiTrack_project_create_date_original = Project::where('id', $this->id)->value('created_at');
        $wiTrack_project_create_date = \Carbon\Carbon::parse($wiTrack_project_create_date_original)->toDateTimeString();
        $wipTrack_project_create_date = ProjectStatus::where('project_id', $this->id)->value('project_creation');

        $wiTrackCarbon = \Carbon\Carbon::parse($wiTrack_project_create_date);
        $wipTrackCarbon = \Carbon\Carbon::parse($wipTrack_project_create_date);

        // Calculate difference in hours
        $difference_in_hours = $wiTrackCarbon->diffInHours($wipTrackCarbon);

        return [
            'deadline_time' => $project_deadline_time,
            'witrack_create_date' => $wiTrackCarbon->toDateTimeString(),
            'wip_create_date' => $wipTrackCarbon->toDateTimeString(),
            'hours_difference' => $difference_in_hours,
        ];
        // return $wiTrack_project_create_date,$wipTrack_project_create_date;
    }
    public function productsOfProject(){
        return $this->hasOne(ProductsOfProjects::class, 'project_id');
    }

}
