<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InitialInspectionName;

class InitialInspectionController extends Controller
{
    public function index(){
        $initialInspections = InitialInspectionName::all();
        return view('settings.initial_inspection', compact('initialInspections'));
    }

    public function update(Request $request){
        $request->validate([
            'id' => 'required|exists:initial_inspection_name,id',
            'name' => 'required|string|max:255',
        ]);

        $inspection = InitialInspectionName::findOrFail($request->id);
        $inspection->update([
            'name' => $request->name,
        ]);

        return redirect()->route('initial.inspection')->with('success', 'Inspection updated successfully.');
    }
}
