<?php
namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\ProductType;
use App\Models\User;
use Illuminate\Http\Request;

class ProductTypesController extends Controller
{
    public function index()
    {
        $producttypes = ProductType::with('operator')
            ->orderBy('id', 'desc')
            ->get();


        $operators = User::where('role', 'operator')->get();

        return view('settings.product_types', compact('producttypes', 'operators'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_type_name' => 'required|string|max:255|unique:product_types,project_type_name',
            'product_family_number' => 'required|integer',
            'capacity' => 'required|integer',
            'estimated_weeks' => 'required|integer|min:0',
            'is_active' => 'required|in:0,1',
        ]);

        $productType = ProductType::create([
            'project_type_name' => $request->project_type_name,
            'product_family_number' => $request->product_family_number,
            'limitation_per_shift' => $request->capacity,
            'estimated_product_type_weeks' => $request->estimated_weeks,
            'is_active' => (int) $request->is_active,
        ]);

        return redirect()->route('product-types')->with('success', 'Product type added successfully.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:product_types,id',
            'project_type_name' => 'required|string|max:255|unique:product_types,project_type_name,' . $request->id,
            'product_family_number' => 'required|integer',
            'capacity' => 'required|integer',
            'estimated_weeks' => 'required|integer|min:0',
            'is_active' => 'required|in:0,1',
        ]);

        $productType = ProductType::findOrFail($request->id);
        $productType->project_type_name = $request->project_type_name;
        $productType->product_family_number = $request->product_family_number;
        $productType->limitation_per_shift = $request->capacity;
        $productType->estimated_product_type_weeks = $request->estimated_weeks;
        $productType->is_active = (int) $request->is_active;
        $productType->save();

        return redirect()->back()->with('success', 'Product type updated successfully.');
    }

}
