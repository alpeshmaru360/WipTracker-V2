<?php

namespace App\Http\Controllers\DesignerEngineer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProjectStatus;
use App\Models\Project;

class DesignerEngineerController extends Controller
{
    public function dashboard(){
        $page_title = "tet";
        $projects = Project::all();
        $project_status = ProjectStatus::whereNull('project_status.deleted_at')
                            ->leftJoin('projects','projects.id','=','project_status.project_id')
                            ->select('project_status.*', 'projects.*')
                            ->get();
        $page_title = "";
        $project_working_on = Project::whereNull('deleted_at')->where('status','1')->count();
        $project_completed = Project::whereNull('deleted_at')->where('status','3')->count();
        $project_status = ProjectStatus::whereNull('deleted_at')->get();
        return view('designer_engineer.dashboard',compact('project_working_on','project_completed','project_status','page_title', 'projects'));
    }

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
