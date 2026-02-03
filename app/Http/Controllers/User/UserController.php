<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProjectStatus;
use App\Models\Project;
use App\Models\ProjectProcessStdTime;
use App\Models\AdminHoursManagement;
use App\Models\User;
use App\Models\Role;
use Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;// A Code: 15-12-2025 Code
use Illuminate\Support\Facades\Log;// A Code: 15-12-2025 Code
use DB;
class UserController extends Controller
{
    public function dashboard(){
        $page_title = "Orders Status";
        if(Auth::check()){
            $user_id = Auth::user()->id;
            if($user_id)
            {
                $project = Project::orderBy('id','desc')->get();
                return view('user.dashboard',compact('project','page_title'));
            }
        }
        else{
            return view('auth.login');
        }
    }

    public function index(){
        $users = User::orderBy('id', 'desc')->get();
        $roles = Role::where('status', 'active')->get();
        return view('admin.index', compact('users', 'roles'));
    }
    
    public function create(){
        return view('admin.users.create', compact('roles')); 
    }

    public function store(Request $request){ 

        $validator = Validator::make($request->all(), [
            'profile_pic' => 'nullable|image|mimes:jpeg,png,webp,jpg', // A Code: 15-12-2025 Code
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|exists:roles,rolename',

        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        // A Code: 15-12-2025 Start
        $profilePicPath = null;
        if ($request->hasFile('profile_pic')) {
            $file = $request->file('profile_pic');
            $destinationPath = public_path('production_team/all_user_profile_pic/'); 
            $filename = time() . '.' . $file->getClientOriginalExtension(); 
            $file->move($destinationPath, $filename); 
            $profilePicPath = $filename; 
        }
        // A Code: 15-12-2025 End

        $user = User::create([
            'profile_pic' => $profilePicPath,  // A Code: 15-12-2025 Code
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),  
            'role' => $request->role,  
        ]);

        return redirect()->route('AdminUsersIndex')->with('success', 'User added successfully.');
    }

    public function edit($id){
        $user = User::findOrFail($id);  

        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }

    public function update(Request $request, $id){        

        // A Code: 15-12-2025 Start     
        $rules = [
            'profile_pic' => 'nullable|image|mimes:jpeg,png,webp,jpg',
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email,' . $id,
            'role'        => 'required|string',
        ];
        
        // Password REQUIRED only if user clicked change password
        if ($request->change_password == 1) {
            $rules['password'] = 'required|string|min:8';
        }
        $request->validate($rules);
        // A Code: 15-12-2025 End

        $user = User::findOrFail($id);

        // A Code: 15-12-2025 Start
        $profilePicPath = $user->profile_pic; 
        if ($request->hasFile('profile_pic')) {
            $file = $request->file('profile_pic');
            $destinationPath = public_path('production_team/all_user_profile_pic/');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move($destinationPath, $filename);
            $profilePicPath = $filename;
        }
        $user->profile_pic = $profilePicPath;
        // A Code: 15-12-2025 End

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->change_password == 1) {
            $user->password = Hash::make($request->password);
        }
        $user->role = $request->role;
        $user->save();

        return redirect()->route('AdminUsersIndex')->with('success', 'User updated successfully.');
    }

    public function destroy($id){
        try {
            $user = User::findOrFail($id);
    
            $user->delete();    
            return redirect()->route('AdminUsersIndex')->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('AdminUsersIndex')->with('error', 'Failed to delete the user. Please try again.');
        }
    }

    public function truncate_table(){
        DB::statement('SET FOREIGN_KEY_CHECKS=0;'); // Disable foreign key checks
        
        DB::table('project_status')->truncate();
        DB::table('qty_of_products')->truncate();
        DB::table('ncr')->truncate();
        DB::table('sales_manager_module')->truncate();
        DB::table('projects')->truncate();
        DB::table('products_of_projects')->truncate();
        DB::table('project_process_std_time')->truncate();
        DB::table('assigned_products_operators')->truncate();
        DB::table('final_inspection_data')->truncate();
        DB::table('initial_inspection_data')->truncate();
        DB::table('product_BOM_item')->truncate();
        DB::table('purchase_order')->truncate();
        DB::table('stock_bom_po')->truncate();
        DB::table('purchase_order_table')->truncate();
        DB::table('operators_time_tracking')->truncate();

        // Update stock_master_module table
        DB::table('stock_master_module')->update([
            'hold_qty' => 0,
            'available_qty' => DB::raw('qty') // Set available_qty = qty
        ]);
    
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        return response()->json([
            'status' => true,
            'message'=> 'Table projects, products_of_projects, project_process_std_time Truncated successfully'
        ]);
    }
}