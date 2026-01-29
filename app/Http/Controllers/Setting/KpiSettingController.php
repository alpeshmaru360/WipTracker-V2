<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\KpiSetting;
use Illuminate\Http\Request;

class KpiSettingController extends Controller
{
    public function index(){
        $kpisetting = KpiSetting::all(); 
        return view('settings.kpi_setting', compact('kpisetting'));
    }

    public function update(Request $request){
        $validated = $request->validate([
            'id' => 'required|exists:kpisettings,id',
            'label' => 'required|string|max:255',
            'key' => 'required|string|max:255',
            'value' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        $kpi = KpiSetting::findOrFail($request->id);
        $kpi->label = $validated['label'];
        $kpi->key = $validated['key'];
        $kpi->value = $validated['value'];
        $kpi->status = $validated['status'];
        $kpi->save();

        return redirect()->back()->with('success', 'KPI updated successfully.');
    }

}
