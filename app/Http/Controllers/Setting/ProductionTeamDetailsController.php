<?php
namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\ProductionTeamDetail;
use Illuminate\Http\Request;
use App\Models\Role;

class ProductionTeamDetailsController extends Controller
{

    public function create(){
        $members = ProductionTeamDetail::orderBy('id', 'desc')->get();
        $roles = Role::where('status', 'active')->get(); // Alpesh Maru Date: 15-12-2025 Code
        return view('settings.production_team_details', compact('members', 'roles')); // Alpesh Maru Date: 15-12-2025 Code
    }

    public function store(Request $request){

        $request->validate([
            'profile_pic' => 'nullable|image|mimes:jpeg,png,webp,jpg', 
            'name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'email' => 'required|email',
        ]);

        $profilePicPath = null;
        if ($request->hasFile('profile_pic')) {
            $file = $request->file('profile_pic');
            $destinationPath = public_path('production_team/profile_pic/'); 
            $filename = time() . '.' . $file->getClientOriginalExtension(); 
            $file->move($destinationPath, $filename); 
            $profilePicPath = $filename; 
        }


        ProductionTeamDetail::create([
            'profile_pic' => $profilePicPath, 
            'name' => $request->input('name'),
            'designation' => $request->input('designation'),
            'email' => $request->input('email'),
        ]);

        return redirect()->back()->with('success', 'Member added successfully.');
    }

    public function edit($id){
        $member = ProductionTeamDetail::find($id);
        return response()->json($member); 
    }

    public function update(Request $request, $id){
        $request->validate([
            'profile_pic' => 'nullable|image|mimes:jpeg,png,webp,jpg',
            'name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'email' => 'required|email',
        ]);

        $member = ProductionTeamDetail::find($id);

        $profilePicPath = $member->profile_pic; 
        if ($request->hasFile('profile_pic')) {
            $file = $request->file('profile_pic');
            $destinationPath = public_path('production_team/profile_pic/');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move($destinationPath, $filename);
            $profilePicPath = $filename;
        }

        $member->update([
            'profile_pic' => $profilePicPath,
            'name' => $request->input('name'),
            'designation' => $request->input('designation'),
            'email' => $request->input('email'),
        ]);

        return redirect()->back()->with('success', 'Member updated successfully.');
    }
    
    public function destroy($id){
        $member = ProductionTeamDetail::find($id);
        if ($member) {
            // Optionally, delete the profile picture from the server
            if ($member->profile_pic) {
                $filePath = public_path('production_team/profile_pic/' . $member->profile_pic);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            $member->delete();
            return redirect()->back()->with('success', 'Member deleted successfully.');
        }
        return redirect()->back()->with('error', 'Member not found.');
    }

}
