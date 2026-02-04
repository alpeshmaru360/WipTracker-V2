<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProjectStatus;
use App\Models\AdminHoursManagement;
use App\Models\Project;
use App\Models\User;
use App\Models\ProductType;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use DB;
use App\Services\DashboardService;

class AdminController extends Controller
{
    public function dashboard(DashboardService $dashboardService){
        // Common Dashboard Service
        $role = auth()->user()->role;
        $dashboardData = $dashboardService->getDashboardData($role);
        $page_title = "Role Redirection";        
        
        return view('admin.dashboard',compact('dashboardData', 'page_title'));
    }

    public function hours_settings(Request $request){    
        $product_types = ProductType::orderBy('id', 'asc')->where('is_active','=','1')->pluck('project_type_name');

        $process_type = $request->process_type;   
        $product_type = $request->product_type;     
        $query = AdminHoursManagement::select('*');

        if (isset($process_type) && $process_type != "All") {
            $query->where('lable', $process_type);
        }
        if (isset($product_type)) {
            $query->where('product_type', $product_type);
        }
        $processes = $query->where('is_deleted', 0)->orderBy('id', 'asc')->get();

        if ($request->ajax()) {
            $view = view('admin.process_rows', compact('processes'))->render();
            return response()->json(['html' => $view]);
        }  
        return view('admin.hours_settings', compact('processes', 'product_types'));
    }

    public function admin_hours_store(Request $request){   
        $request->validate([
            'lable'        => 'required|in:StandardProcessTimes,AssemblyProcessTime',
            'process_name' => 'required|string|max:255',
            'value'        => 'required',
            'product_type' => 'required_if:lable,AssemblyProcessTime',
        ], [
            'lable.required'        => 'The label field is required.',
            'lable.in'              => 'The selected label is invalid.',
            'process_name.required' => 'The process name is required.',
            'value.required'        => 'The hrs field is required.',
            'product_type.required_if' => 'The product type field is required for AssemblyProcessTime.',
        ]);

        // Generate process code form last process code
        $generated_process_code = null;
        // Proceed only if product_type is provided
        if (!empty($request->product_type)) {

            // Step 1: Get all matching process codes
            $processCodes = AdminHoursManagement::where('lable', $request->lable)
                ->where('product_type', $request->product_type)
                ->where('is_deleted', 0)
                ->whereNotNull('process_code')
                ->pluck('process_code')
                ->toArray();

            // Step 2: Get the latest sorted process code
            $lastProcessCode = $this->getLastProcessCode($processCodes);

            // Step 3: If found, increment suffix
            if ($lastProcessCode && preg_match('/^([A-Za-z]+)(\d+)\.(\d+)$/', $lastProcessCode, $matches)) {
                $prefixAlpha = $matches[1];
                $prefixNum = (int)$matches[2];
                $suffix = (int)$matches[3];

                $generated_process_code = $prefixAlpha . $prefixNum . '.' . ($suffix + 1);

            } else {
                // Step 4: If no valid code for this combo, get global latest and increment prefix
                $globalLastCode = AdminHoursManagement::where('is_deleted', 0)
                    ->whereNotNull('process_code')
                    ->pluck('process_code')
                    ->toArray();

                $latestGlobalCode = $this->getLastProcessCode($globalLastCode);

                if ($latestGlobalCode && preg_match('/^([A-Za-z]+)(\d+)\.(\d+)$/', $latestGlobalCode, $matches)) {
                    $prefixAlpha = $matches[1];
                    $prefixNum = (int)$matches[2];

                    $generated_process_code = $prefixAlpha . ($prefixNum + 1) . '.1';
                }
            }
        }
        // Generate key from process name
        $generated_key = strtolower(str_replace(' ', '_', $request->process_name));
        $process = AdminHoursManagement::create([
            'lable' => $request->lable,
            'process_code' => $generated_process_code,
            'product_type' => $request->product_type,
            'process_name' => $request->process_name,
            'key' => $generated_key,
            'value' => $request->value,
        ]);

        return redirect()->route('AdminHoursSettings')->with('success', 'Process added successfully.');
    }

    private function getLastProcessCode(array $processCodes): ?string {
        // Filter out invalid codes (optional safety)
        $processCodes = array_filter($processCodes, function ($code) {
            return preg_match('/^([A-Za-z]+)(\d+)\.(\d+)$/', $code);
        });

        // Sort descending using numeric-aware comparison
        usort($processCodes, function ($a, $b) {
            preg_match('/^([A-Za-z]+)(\d+)\.(\d+)$/', $a, $matchA);
            preg_match('/^([A-Za-z]+)(\d+)\.(\d+)$/', $b, $matchB);

            $mainA = (int)$matchA[2];
            $subA = (int)$matchA[3];
            $mainB = (int)$matchB[2];
            $subB = (int)$matchB[3];

            return $mainB <=> $mainA ?: $subB <=> $subA;
        });

        return $processCodes[0] ?? null;
    }

    public function admin_hours_update(Request $request, $id){
        $request->validate([
            'value' => 'required',
            'process_name' => 'required|string|max:255',
        ], [
            'value.required' => 'The hrs field is required.',
            'process_name.required' => 'The process name is required.',
        ]);

        $process = AdminHoursManagement::findOrFail($id);
        $process->process_name = $request->process_name;
        $process->value = $request->value;
        $process->save();

        return redirect()->route('AdminHoursSettings')->with('success', 'Process updated successfully.');
    }

    public function admin_hours_destroy($id){
        try {
            $process = AdminHoursManagement::findOrFail($id);    
            //$process->delete();  
            $process->is_deleted = 1;
            $process->save();       

            return redirect()->route('AdminHoursSettings')->with('success', 'Process deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('AdminHoursSettings')->with('error', 'Failed to delete the process. Please try again.');
        }
    }

    public function settings_form(){
        $page_title = "System Settings";
        // StandardProcessTimes
        $create_new_project = AdminHoursManagement::where('lable','like','StandardProcessTimes')->where('key','create_new_project')->where('is_deleted', 0)->value('value');
        $bom_drawings = AdminHoursManagement::where('lable','like','StandardProcessTimes')->where('key','bom_drawings')->where('is_deleted', 0)->value('value');
        $check_the_bom_and_place_po = AdminHoursManagement::where('lable','like','StandardProcessTimes')->where('key','check_the_bom_and_place_po')->where('is_deleted', 0)->value('value');
        $gather_qa_from_supplier = AdminHoursManagement::where('lable','like','StandardProcessTimes')->where('key','gather_qa_from_supplier')->where('is_deleted', 0)->value('value');
        $record_qa = AdminHoursManagement::where('lable','like','StandardProcessTimes')->where('key','record_qa')->where('is_deleted', 0)->value('value');
        $inform_customer_about_material_readiness = AdminHoursManagement::where('lable','like','StandardProcessTimes')->where('key','inform_customer_about_material_readiness')->where('is_deleted', 0)->value('value');
        $initial_inspection = AdminHoursManagement::where('lable','like','StandardProcessTimes')->where('key','initial_inspection')->where('is_deleted', 0)->value('value');
        $ncr_creation = AdminHoursManagement::where('lable','like','StandardProcessTimes')->where('key','ncr_creation')->where('is_deleted', 0)->value('value');
        $ncr_closing_time = AdminHoursManagement::where('lable','like','StandardProcessTimes')->where('key','ncr_closing_time')->where('is_deleted', 0)->value('value');
        $final_inspection = AdminHoursManagement::where('lable','like','StandardProcessTimes')->where('key','final_inspection')->where('is_deleted', 0)->value('value');
        $prepare_pl = AdminHoursManagement::where('lable','like','StandardProcessTimes')->where('key','prepare_pl')->where('is_deleted', 0)->value('value');
        $sent_pl_to_om = AdminHoursManagement::where('lable','like','StandardProcessTimes')->where('key','sent_pl_to_om')->where('is_deleted', 0)->value('value');

        // Norm pump - Motor Alignment
        $a1_1 = AdminHoursManagement::where('process_code','like','A1.1')->where('is_deleted', 0)->value('value');
        $a1_2 = AdminHoursManagement::where('process_code','like','A1.2')->where('is_deleted', 0)->value('value');
        $a1_3 = AdminHoursManagement::where('process_code','like','A1.3')->where('is_deleted', 0)->value('value');
        $a1_4 = AdminHoursManagement::where('process_code','like','A1.4')->where('is_deleted', 0)->value('value');
        $a1_5 = AdminHoursManagement::where('process_code','like','A1.5')->where('is_deleted', 0)->value('value');
        $a1_6 = AdminHoursManagement::where('process_code','like','A1.6')->where('is_deleted', 0)->value('value');
        $a1_7 = AdminHoursManagement::where('process_code','like','A1.7')->where('is_deleted', 0)->value('value');
        $a1_8 = AdminHoursManagement::where('process_code','like','A1.8')->where('is_deleted', 0)->value('value');

        $a2_1 = AdminHoursManagement::where('process_code','like','A2.1')->where('is_deleted', 0)->value('value');
        $a2_2 = AdminHoursManagement::where('process_code','like','A2.2')->where('is_deleted', 0)->value('value');
        $a2_3 = AdminHoursManagement::where('process_code','like','A2.3')->where('is_deleted', 0)->value('value');
        $a2_4 = AdminHoursManagement::where('process_code','like','A2.4')->where('is_deleted', 0)->value('value');
        $a2_5 = AdminHoursManagement::where('process_code','like','A2.5')->where('is_deleted', 0)->value('value');
        $a2_6 = AdminHoursManagement::where('process_code','like','A2.6')->where('is_deleted', 0)->value('value');
        $a2_7 = AdminHoursManagement::where('process_code','like','A2.7')->where('is_deleted', 0)->value('value');
        $a2_8 = AdminHoursManagement::where('process_code','like','A2.8')->where('is_deleted', 0)->value('value');

        $a3_1 = AdminHoursManagement::where('process_code','like','A3.1')->where('is_deleted', 0)->value('value');
        $a3_2 = AdminHoursManagement::where('process_code','like','A3.2')->where('is_deleted', 0)->value('value');
        $a3_3 = AdminHoursManagement::where('process_code','like','A3.3')->where('is_deleted', 0)->value('value');
        $a3_4 = AdminHoursManagement::where('process_code','like','A3.4')->where('is_deleted', 0)->value('value');
        $a3_5 = AdminHoursManagement::where('process_code','like','A3.5')->where('is_deleted', 0)->value('value');
        $a3_6 = AdminHoursManagement::where('process_code','like','A3.6')->where('is_deleted', 0)->value('value');
        $a3_7 = AdminHoursManagement::where('process_code','like','A3.7')->where('is_deleted', 0)->value('value');
        $a3_8 = AdminHoursManagement::where('process_code','like','A3.8')->where('is_deleted', 0)->value('value');
        $a3_9 = AdminHoursManagement::where('process_code','like','A3.9')->where('is_deleted', 0)->value('value');
        $a3_10 = AdminHoursManagement::where('process_code','like','A3.10')->where('is_deleted', 0)->value('value');
        $a3_11 = AdminHoursManagement::where('process_code','like','A3.11')->where('is_deleted', 0)->value('value');
        $a3_12 = AdminHoursManagement::where('process_code','like','A3.12')->where('is_deleted', 0)->value('value');
        $a3_13 = AdminHoursManagement::where('process_code','like','A3.13')->where('is_deleted', 0)->value('value');
        $a3_14 = AdminHoursManagement::where('process_code','like','A3.14')->where('is_deleted', 0)->value('value');
        
        $a10_1 = AdminHoursManagement::where('process_code','like','A10.1')->where('is_deleted', 0)->value('value');
        $a10_2 = AdminHoursManagement::where('process_code','like','A10.2')->where('is_deleted', 0)->value('value');
        $a10_3 = AdminHoursManagement::where('process_code','like','A10.3')->where('is_deleted', 0)->value('value');

        // Control Panel Assembly
        $a4_1 = AdminHoursManagement::where('process_code','like','A4.1')->where('is_deleted', 0)->value('value');
        $a4_2 = AdminHoursManagement::where('process_code','like','A4.2')->where('is_deleted', 0)->value('value');
        $a4_3 = AdminHoursManagement::where('process_code','like','A4.3')->where('is_deleted', 0)->value('value');

         // Norm pump - Bareshaft
        $a5_1 = AdminHoursManagement::where('process_code','like','A5.1')->where('is_deleted', 0)->value('value');
        $a5_2 = AdminHoursManagement::where('process_code','like','A5.2')->where('is_deleted', 0)->value('value');
        $a5_3 = AdminHoursManagement::where('process_code','like','A5.3')->where('is_deleted', 0)->value('value');

        // Split case horizontal  - Bareshaft
        $a6_1 = AdminHoursManagement::where('process_code','like','A6.1')->where('is_deleted', 0)->value('value');
        $a6_2 = AdminHoursManagement::where('process_code','like','A6.2')->where('is_deleted', 0)->value('value');
        $a6_3 = AdminHoursManagement::where('process_code','like','A6.3')->where('is_deleted', 0)->value('value');

        // Borehole pump assembly
        $a7_1 = AdminHoursManagement::where('process_code','like','A7.1')->where('is_deleted', 0)->value('value');
        $a7_2 = AdminHoursManagement::where('process_code','like','A7.2')->where('is_deleted', 0)->value('value');
        $a7_3 = AdminHoursManagement::where('process_code','like','A7.3')->where('is_deleted', 0)->value('value');

        // Helix Pump Assembly
        $a8_1 = AdminHoursManagement::where('process_code','like','A8.1')->where('is_deleted', 0)->value('value');
        $a8_2 = AdminHoursManagement::where('process_code','like','A8.2')->where('is_deleted', 0)->value('value');
        $a8_3 = AdminHoursManagement::where('process_code','like','A8.3')->where('is_deleted', 0)->value('value');

        // Split case vertical pump -motor alignment 
        $a9_1 = AdminHoursManagement::where('process_code','like','A9.1')->where('is_deleted', 0)->value('value');
        $a9_2 = AdminHoursManagement::where('process_code','like','A9.2')->where('is_deleted', 0)->value('value');
        $a9_3 = AdminHoursManagement::where('process_code','like','A9.3')->where('is_deleted', 0)->value('value');

         // Norm pump - Bareshaft + Norm pump - Motor Alignment 
        $a11_1 = AdminHoursManagement::where('process_code','like','A11.1')->where('is_deleted', 0)->value('value');
        $a11_2 = AdminHoursManagement::where('process_code','like','A11.2')->where('is_deleted', 0)->value('value');
        $a11_3 = AdminHoursManagement::where('process_code','like','A11.3')->where('is_deleted', 0)->value('value');
        $a11_4 = AdminHoursManagement::where('process_code','like','A11.4')->where('is_deleted', 0)->value('value');
        $a11_5 = AdminHoursManagement::where('process_code','like','A11.5')->where('is_deleted', 0)->value('value');
        $a11_6 = AdminHoursManagement::where('process_code','like','A11.6')->where('is_deleted', 0)->value('value');
        $a11_7 = AdminHoursManagement::where('process_code','like','A11.7')->where('is_deleted', 0)->value('value');
        $a11_8 = AdminHoursManagement::where('process_code','like','A11.8')->where('is_deleted', 0)->value('value');
        $a11_9 = AdminHoursManagement::where('process_code','like','A11.9')->where('is_deleted', 0)->value('value');
        $a11_10 = AdminHoursManagement::where('process_code','like','A11.10')->where('is_deleted', 0)->value('value');
        $a11_11 = AdminHoursManagement::where('process_code','like','A11.11')->where('is_deleted', 0)->value('value');
        $a12_1 = AdminHoursManagement::where('process_code','like','A12.1')->where('is_deleted', 0)->value('value');
        $a12_2 = AdminHoursManagement::where('process_code','like','A12.2')->where('is_deleted', 0)->value('value');
        $a12_3 = AdminHoursManagement::where('process_code','like','A12.3')->where('is_deleted', 0)->value('value');
        $a12_4 = AdminHoursManagement::where('process_code','like','A12.4')->where('is_deleted', 0)->value('value');
        $a12_5 = AdminHoursManagement::where('process_code','like','A12.5')->where('is_deleted', 0)->value('value');
        $a12_6 = AdminHoursManagement::where('process_code','like','A12.6')->where('is_deleted', 0)->value('value');
        $a12_7 = AdminHoursManagement::where('process_code','like','A12.7')->where('is_deleted', 0)->value('value');
        $a12_8 = AdminHoursManagement::where('process_code','like','A12.8')->where('is_deleted', 0)->value('value');
        $a12_9 = AdminHoursManagement::where('process_code','like','A12.9')->where('is_deleted', 0)->value('value');
        $a12_10 = AdminHoursManagement::where('process_code','like','A12.10')->where('is_deleted', 0)->value('value');
        $a12_11 = AdminHoursManagement::where('process_code','like','A12.11')->where('is_deleted', 0)->value('value');

        return view('admin.settings',compact('create_new_project','page_title','bom_drawings','check_the_bom_and_place_po','gather_qa_from_supplier','record_qa','inform_customer_about_material_readiness','initial_inspection','ncr_creation','ncr_closing_time','prepare_pl','sent_pl_to_om','final_inspection','a1_1','a1_2','a1_3','a1_4','a1_5','a1_6','a1_7','a1_8','a2_1','a2_2','a2_3','a2_4','a2_5','a2_6','a2_7','a2_8','a3_1','a3_2','a3_3','a3_4','a3_5','a3_6','a3_7','a3_8','a3_9','a3_10','a3_11','a3_12','a3_13','a3_14','a10_1','a10_2','a10_3','a4_1','a4_2','a4_3','a5_1','a5_2','a5_3','a6_1','a6_2','a6_3','a7_1','a7_2','a7_3','a8_1','a8_2','a8_3','a9_1','a9_2','a9_3','a11_1','a11_2','a11_3','a11_4','a11_5','a11_6','a11_7','a11_8','a11_9','a11_10','a11_11','a12_1','a12_2','a12_3','a12_4','a12_5','a12_6','a12_7','a12_8','a12_9','a12_10','a12_11'));
    }

    public function settings(Request $request){
        $data = $request->except('_token'); 
        foreach ($data as $key => $value) {
            AdminHoursManagement::where('lable', 'StandardProcessTimes') 
                ->where('key', $key)
                ->where('is_deleted', 0)
                ->update(['value' => $value]);
        }

        foreach ($data as $key => $value) {
            $key = str_replace("_",".",$key);
            $test1 = AdminHoursManagement::where('lable', 'AssemblyProcessTime') 
                ->where('process_code', $key)
                ->where('is_deleted', 0)           
                ->update(['value' => $value]);               
        }
        return redirect()->route('AdminSettingsForm')->with('success', 'Standard Process Times updated successfully.');
    }

    public function generateImpersonationLink($id){
        $userToLogin = User::findOrFail($id);

        // Generate a unique token
        $token = Str::random(40);

        // Store token + admin ID in cache for 5 minutes
        Cache::put("impersonation_token_{$token}", [
            'user_id' => $userToLogin->id,
            'admin_id' => Auth::id()
        ], now()->addMinutes(5));

        // Generate URL for the new tab
        $url = route('admin.performImpersonation', ['token' => $token]);

        return redirect()->away($url); // or return JSON if you want to open via JS
    }

    public function performImpersonation($token){
        $data = Cache::pull("impersonation_token_{$token}");

        if (!$data) {
            abort(403, 'Invalid or expired impersonation token.');
        }

        // Save admin ID in session
        session()->put('impersonate_admin_id', $data['admin_id']);

        // Log in as the selected user
        Auth::loginUsingId($data['user_id']);
        session()->put('impersonating', true);

        	$user = User::findOrFail($data['user_id']);
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
            exit;
    }

    public function stopImpersonation(){
        if (session()->has('impersonate_admin_id')) {
            $adminId = session()->pull('impersonate_admin_id');
            session()->forget('impersonating');
            Auth::loginUsingId($adminId);
            return redirect()->route('admin.dashboard');
        }

        return redirect()->back();
    }

    // Auto logout via JS beacon
    public function autoLogout(Request $request){
        if (session()->get('impersonating')) {
            Auth::logout();
            session()->flush();
        }

        return response()->noContent();
    }
}
