<?php
namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FinalInspection;

class FinalInspectionController extends Controller
{
    public function index(){
        $finalInspections = FinalInspection::all();
        return view('settings.final_inspection', compact('finalInspections'));
    }

    public function update(Request $request){
        $request->validate([
            'id' => 'required|exists:final_inspection_name,id',
            'name' => 'required|string|max:255',
        ]);

        $inspection = FinalInspection::findOrFail($request->id);
        $inspection->update(['name' => $request->name]);

        return redirect()->route('final.inspection')->with('success', 'Inspection updated successfully.');
    }

}
