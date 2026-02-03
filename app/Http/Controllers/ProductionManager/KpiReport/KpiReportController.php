<?php
namespace App\Http\Controllers\ProductionManager\KpiReport;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class KpiReportController extends Controller
{
    public function index()
    {
        $projects = Project::whereNotNull('allocated_month_kpi')->get();
        
        $completedProjects = Project::where('status', 2)
                                    ->whereNull('allocated_month_kpi') 
                                    ->get();

        return view('production_manager.kpi_reports.kpi_reports', compact('projects', 'completedProjects'));
    }


    public function allocateProject(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'allocated_month' => 'required|string',
        ]);

        $project = Project::find($validated['project_id']);

        $project->allocated_month_kpi = $validated['allocated_month'];
        $project->save();

        return response()->json([
            'success' => true,
            'updatedProject' => [
                'id' => $project->id,
                'created_at' => $project->created_at->format('d-m-Y'),
                'project_no' => $project->project_no,
                'project_name' => $project->project_name,
                'country' => $project->country,
                'customer_name' => $project->customer_name,
                'sales_name' => $project->sales_name,
                'project_value' => $project->project_value,
                'allocated_month_kpi' => $project->allocated_month_kpi,
            ]
        ]);
    }

    public function manpowerEfficiency()
    {
        return view('production_manager.kpi_reports.Manpower_efficiency');
    }

    public function throughputTime()
    {
        return view('production_manager.kpi_reports.Throughput_time');
    }

    public function deliveryOnTime()
    {
        return view('production_manager.kpi_reports.Delivery_on_time');
    }

    public function finishedGoodsPerEmployeeHour()
    {
        return view('production_manager.kpi_reports.Employee_hour');
    }

    public function coverageRate()
    {
        return view('production_manager.kpi_reports.Coverage_rate');
    }

    public function vsi()
    {
        return view('production_manager.kpi_reports.VSI');
    }

    public function monthlyKpis()
    {
        return view('production_manager.kpi_reports.Monthly_kpis');
    }

}
