<?php

namespace App\Http\Controllers\Setting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Project;

class DeletedProjectController extends Controller{

    public function index(){

        $projects = Project::select('*')
            ->orderByRaw("CAST(SUBSTRING_INDEX(project_no, '-', 1) AS UNSIGNED) DESC")
            ->orderByRaw("CAST(SUBSTRING_INDEX(project_no, '-', -1) AS UNSIGNED) DESC")
            ->with('product')->whereNotNull('assembly_quotation_ref')->where('is_deleted', 1)->get();
            
        return view('settings.deleted_projects', compact('projects'));
    }

}
