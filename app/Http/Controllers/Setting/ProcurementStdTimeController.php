<?php

namespace App\Http\Controllers\Setting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProcurementStandardTime;
use App\Models\ProductType;

class ProcurementStdTimeController extends Controller
{
    public function index(){
        $settings = ProcurementStandardTime::with('productTypeRelation')->orderBy('id', 'desc')->get();
        $productTypes = ProductType::where('is_active', 1)->pluck('project_type_name', 'id');
        return view('settings.procurement_std_time', compact('settings', 'productTypes'));
    }

    public function update(Request $request){
        $request->validate([
            'id' => 'required',
            'product_type' => 'required|string|max:255',
            'keyword' => 'required|string|max:255',
            'total_days' => 'required|integer|min:1', // minimum 1 to disallow zero
        ]);

        $setting = ProcurementStandardTime::findOrFail($request->id);
        $setting->product_type = $request->product_type;
        $setting->keyword = $request->keyword;
        $setting->total_days = $request->total_days;
        $setting->save();

        return redirect()->back()->with('success', 'Procurement Standard Time updated successfully!');
    }

    public function store(Request $request){
        $request->validate([
            'product_type' => 'required|integer|exists:product_types,id',
            'keyword' => 'required|string|max:255',
            'total_days' => 'required|integer|min:1',
        ]);
        $productTypeName = ProductType::where('id', $request->product_type)->value('project_type_name');
        $exists = ProcurementStandardTime::where('product_type_id', $request->product_type)->exists();
        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', $productTypeName . ' - product type is already exist.');
        }
        ProcurementStandardTime::create([
            'product_type_id' => $request->product_type,
            'product_type' => $productTypeName,
            'keyword' => $request->keyword,
            'total_days' => $request->total_days,
        ]);
        return redirect()->back()->with('success', 'Procurement Standard Time added successfully!');
    }
    
    public function destroy($id){
        $setting = ProcurementStandardTime::findOrFail($id);
        $setting->delete();

        return redirect()->back()->with('success', 'Procurement Standard Time deleted successfully!');
    }
}
