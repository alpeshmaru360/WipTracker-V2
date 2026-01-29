<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GeneralSetting;

class GeneralSettingController extends Controller
{
    public function index(){
        $settings = GeneralSetting::all();
        return view('settings.general_setting', compact('settings'));
    }

    public function update(Request $request, $id){
        $request->validate([
            'value' => 'required|string',
            'reminder_frequency' => 'required|string',
        ]);
        $setting = GeneralSetting::findOrFail($id);
        $setting->update([
            'value' => $request->value,
            'reminder_frequency' => $request->reminder_frequency,
        ]);

        return redirect()->route('SettingsPage')->with('success', 'Setting updated successfully.');
    }

}
