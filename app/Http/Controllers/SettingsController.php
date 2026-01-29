<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Settings;

class SettingsController extends Controller
{
    public function index(){
        $settings = Settings::all();
        return view('settings.index', compact('settings'));
    }
    public function update(Request $request, $id){
        $request->validate([
            'value' => 'required|email',
            'reminder_frequency' => 'required|string',
        ]);
        $setting = Settings::findOrFail($id);
        $setting->update([
            'value' => $request->value,
            'reminder_frequency' => $request->reminder_frequency,
        ]);

        return redirect()->route('SettingsPage')->with('success', 'Setting updated successfully.');
    }
}
