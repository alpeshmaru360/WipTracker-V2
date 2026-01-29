<?php
namespace App\Http\Controllers\Setting;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Http\Controllers\Controller;

class SupplierController extends Controller
{
    public function index(){
        $suppliers = Supplier::orderBy('id', 'desc')->get();
        return view('settings.supplier_list', compact('suppliers'));
    }

    public function update(Request $request){
        $request->validate([
            'id' => 'required|exists:suppliers_list,id',
            'supplier_name' => 'required|string|max:255',
        ]);

        $supplier = Supplier::findOrFail($request->id);
        $supplier->update(['supplier_name' => $request->supplier_name]);

        return redirect()->route('suppliers.list')->with('success', 'Supplier updated successfully.');
    }

    public function store(Request $request){
        $request->validate([
            'supplier_name' => 'required|string|max:255',
        ]);

        Supplier::create(['supplier_name' => $request->supplier_name]);

        return redirect()->route('suppliers.list')->with('success', 'Supplier added successfully.');
    }

    public function destroy(Request $request){
        $request->validate([
            'id' => 'required|exists:suppliers_list,id',
        ]);

        $supplier = Supplier::findOrFail($request->id);
        $supplier->delete();

        return redirect()->route('suppliers.list')->with('success', 'Supplier deleted successfully.');
    }

}
