<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // A Code: 22-12-2025
use Illuminate\Support\Facades\Route; // A Code: 22-12-2025
use App\Models\User; // A Code: 22-12-2025
use Auth;
use Hash;

class AuthController extends Controller
{
    public function login_form(){
        return view('auth.login');
    }

    public function login(Request $request){
        $credentials = $request->only(['email', 'password']);
        if (Auth::attempt($credentials)) {
            $user = Auth::user(); 
            $role = $user->role;
            switch ($role) {
                case 'Admin':
                    return redirect()->intended(route('AdminDashboard'));
                    break;
                case 'Manager':
                    return redirect()->intended(route('ManagerDashboard'));
                    break;
                    case 'Assembly Manager':
                    return redirect()->intended(route('AssemblyManagerDashboard'));
                    break;
                case 'Quality Engineer':
                    return redirect()->intended(route('QualityManagerDashboard'));
                    break;
                case 'Procurement Specialist':
                    return redirect()->intended(route('ProcurementManagerDashboard'));
                    break;
                case 'Sale Manager':
                    return redirect()->intended(route('ExpectedOrdersDashboard'));
                    break;
                case 'Production Engineer':
                    return redirect()->intended(route('ProductionManagerDashboard'));
                    break;
                case 'Designer Engineer':
                    return redirect()->intended(route('DesignerEngineerDashboard'));
                    break;
                case 'Production Superwisor':
                    return redirect()->intended(route('ProductionSuperwisorDashboard'));
                    break;
                case 'Wilo Operator':
                    return redirect()->intended(route('OperatorDashboard'));
                    break;    
                case '3rd Party Operator':
                    return redirect()->intended(route('OperatorDashboard'));
                    break;
                case 'User':
                    return redirect()->intended(route('UserDashboard'));
                    break;
                case 'Estimation Manager':
                    return redirect()->intended(route('EstimationManagerDashboard'));
                    break;
                default:
                    return redirect()->intended(route('login'));
                    break;
            }
        }
        else{
            return back()->withErrors(['email' => 'Invalid credentials']);
        }
    }

    // A Code: 22-12-2025 Start
    public function authRedirectLogin(Request $request){

        $roleRoutes = [
            'Admin'                  => 'AdminDashboard',
            'Manager'                => 'ManagerDashboard',
            'Assembly Manager'       => 'AssemblyManagerDashboard',
            'Quality Engineer'       => 'QualityManagerDashboard',
            'Procurement Specialist' => 'ProcurementManagerDashboard',
            'Sale Manager'           => 'ExpectedOrdersDashboard',
            'Production Engineer'    => 'ProductionManagerDashboard',
            'Designer Engineer'      => 'DesignerEngineerDashboard',
            'Production Superwisor'  => 'ProductionSuperwisorDashboard',
            'Wilo Operator'          => 'OperatorDashboard',
            '3rd Party Operator'     => 'OperatorDashboard',
            'User'                   => 'UserDashboard',
            'Estimation Manager'     => 'EstimationManagerDashboard',
        ];

        $request->validate([
            'role_name' => 'required|in:' . implode(',', array_keys($roleRoutes)),
            'email'     => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'url'   => route('login'),
                'error' => 'User not found'
            ], 404);
        }

        // Update user role + admin flag
        $user->update([
            'is_admin_login' => 1,
            'role'           => $request->role_name,
        ]);

        // Passwordless login
        Auth::login($user->refresh());

        $routeName = $roleRoutes[$user->role] ?? 'login';

        return response()->json([
            'user_role' => $user->role,
            'url'       => Route::has($routeName) ? route($routeName) : route('login')
        ]);
    }  

    public function logout(Request $request){
        User::where('is_admin_login', 1)
                ->where('id', Auth::id())
                ->update(['role' => 'Admin']);
        Auth::logout();
        return redirect()->route('login');
    }
    // A Code: 22-12-2025 End
    
}


