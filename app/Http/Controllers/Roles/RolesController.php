<?php

namespace App\Http\Controllers\Roles;
use App\Models\Role; 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class RolesController extends Controller
{
    public function roles()
    {
        $roles = Role::orderBy('id', 'desc')->get();

        return view('admin.roles', compact('roles'));  
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rolename' => 'required|string|max:255',
            'status' => 'required|string',
        ]);

        Role::create([
            'rolename' => $validated['rolename'],
            'status' => $validated['status'],
        ]);

        return redirect()->route('AdminRoles')->with('success', 'Role added successfully.');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'rolename' => 'required|string|max:255',
            'status' => 'required|string',
        ]);

        $role = Role::find($id);

        if ($role) {
            $role->update([
                'rolename' => $validated['rolename'],
                'status' => $validated['status'],
            ]);

            return redirect()->route('AdminRoles')->with('success', 'Role updated successfully.');
        }

        return redirect()->route('AdminRoles')->with('error', 'Role not found.');
    }

    public function destroy($id)
    {
        $role = Role::find($id);
    
        if ($role) {
            $role->delete();
            return redirect()->route('AdminRoles')->with('success', 'Role deleted successfully.');
        }
    
        return redirect()->route('AdminRoles')->with('error', 'Role not found.');
    }
    
}
