<?php

namespace App\Http\Controllers\QualityManager;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\InitialInspectionTable;
use PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Services\DashboardService;

class QualityActionController extends Controller
{
    // Alpesh test [2]
    //after click on place order from inbox screen this function is called
    public function qualityAction(){
        return view('quality_manager.quality_action_form');
    }

    public function qualityActionSave(Request $request){
        $request->validate([
            'po_number'     => 'required|string',
            'reports_docs'  => 'nullable|file|mimes:doc,docx|max:2048',
        ]);
        try {
            $filePath = null;
            if ($request->hasFile('reports_docs')) {
                $file = $request->file('reports_docs');
                if (!$file->isValid()) {
                    return back()->with('error', 'File upload failed. Please try again.');
                }            
                $timestamp = now()->format('Ymd_His');
                $folderName = "{$request->po_number}_{$timestamp}";              
                $destinationPath = public_path("project_document/Quality/Action/{$folderName}");
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true);
                }
                $timestamp     = now()->format('Ymd_His');
                $originalName  = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension     = $file->getClientOriginalExtension();
                $fileName      = "{$timestamp}_{$originalName}.{$extension}";
                $file->move($destinationPath, $fileName);
                $filePath = "project_document/Quality/Action/{$folderName}/{$fileName}";
            }
            DB::table('quality_action')->insert([
                'po_number'                  => $request->po_number,
                'reports_docs'               => $filePath,
                'initial_inspection_date'    => now(),
                'created_at'                 => now(),
                'updated_at'                 => now(),
            ]);
            return redirect()
                ->route('qualityAction')
                ->with('success', 'Quality Action created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
}