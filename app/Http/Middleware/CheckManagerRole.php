<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckManagerRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access this page');
        }

        $user = Auth::user();

        $normalizedUserRole = strtolower(trim($user->role));
        $normalizedAllowedRoles = array_map(fn($r) => strtolower(trim($r)), $roles);

        // A Code: 22-12-2025 Start

        // if (in_array($normalizedUserRole, $normalizedAllowedRoles)) {
        //     return $next($request);
        // }

        if (in_array($normalizedUserRole, $normalizedAllowedRoles) || (int) $user->is_admin_login == 1) {
            return $next($request);
        }
        // A Code: 22-12-2025 End

        switch ($user->role) {
            case 'Production Engineer':
                return redirect()->route('ProductionManagerDashboard')
                    ->with('error', 'Unauthorized access: You do not have the required role');

            // A Code: 22-12-2025 Start
            case 'Production Superwisor':
                return redirect()->route('ProductionSuperwisorDashboard')
                    ->with('error', 'Unauthorized access: You do not have the required role');
            // A Code: 22-12-2025 End

            case 'Estimation Manager':
                return redirect()->route('EstimationManagerDashboard')
                ->with('error', 'Unauthorized access: You do not have the required role');
            case 'Assembly Manager':
                return redirect()->route('AssemblyManagerDashboard')
                    ->with('error', 'Unauthorized access: You do not have the required role');
            case 'Quality Engineer':
                return redirect()->route('QualityManagerDashboard')
                    ->with('error', 'Unauthorized access: You do not have the required role');
            case 'Procurement Specialist':
                return redirect()->route('ProcurementManagerDashboard')
                    ->with('error', 'Unauthorized access: You do not have the required role');
            case 'Admin':
                return redirect()->route('AdminDashboard')
                    ->with('error', 'Unauthorized access: You do not have the required role');
            default:
                return redirect()->route('login')
                    ->with('error', 'Unauthorized access: No dashboard available for your role');
    }
}
}
