<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectStatus extends Model
{
    use HasFactory;
    protected $table = 'project_status';
    
    protected $fillable = [
        'project_id',
        // add other fillable attributes here
    ];

    public function projects(){
        return $this->belongsTo(Project::class, 'project_id','id');
    }

}
