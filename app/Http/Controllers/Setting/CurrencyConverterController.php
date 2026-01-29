<?php

namespace App\Http\Controllers\Setting;  

use App\Models\CurrencyConverter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CurrencyConverterController extends Controller
{
    public function index(){
        $currencyConverters = CurrencyConverter::all();
        return view('settings.currency_converter', compact('currencyConverters'));
    }

    public function update(Request $request){
        $request->validate([
            '1_AED' => 'nullable|numeric',
            '1_USD' => 'nullable|numeric',
            '1_EUR' => 'nullable|numeric',
        ]);

        $currency = CurrencyConverter::findOrFail($request->id);

        // Update only the fields that were provided in the request
        if ($request->has('1_AED')) {
            $currency->{'1_AED'} = $request->input('1_AED');
        }

        if ($request->has('1_USD')) {
            $currency->{'1_USD'} = $request->input('1_USD');
        }

        if ($request->has('1_EUR')) {
            $currency->{'1_EUR'} = $request->input('1_EUR');
        }

        // Save the updated data
        $currency->save();

        return redirect()->route('currency')->with('success', 'Currency updated successfully.');
    }
    
}
