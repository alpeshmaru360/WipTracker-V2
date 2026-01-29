<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\ProjectStatus;
use App\Models\Project;
use App\Models\AdminSetting;
use App\Models\ProductType;
use App\Models\AdminHoursManagement;
use App\Models\ProductsOfProjects;
use App\Models\ProjectProcessStdTime;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Mail\FinancePersonCreateProject;
use App\Mail\WItrackProjectCreateNotifyProductionTeam;
use App\Mail\WItrackProjectCancelNotifyProductionTeam;
use App\Helpers\helper;
use File;
use App\Models\ProductionTeamDetail;
use Illuminate\Support\Facades\Mail;

class APIWITrackProjectController extends Controller
{
    public function get_wiTrack_project_details(Request $request){
        try {
            $validation = $request->validate([
                'project_name' => 'required|string|max:255',
                'customer_ref' => 'required|string|max:255',
                'witrack_no' => 'required|max:255',
                'country' => 'required|string|max:100',
                'customer_name' => 'required|string|max:255',
                'sales_order_number' => 'required|string|max:255',
                'sales_order_s_currency' => 'required|string|max:255',
                'sales_name' => 'required|string|max:255',
                'customer_documents.*' => 'required|file', // Allow multiple files
            ]);

            $isWITrack_project = "1";

            $last_project = Project::orderBy('id', 'desc')->first();

            $admin_setting = AdminSetting::where('key', 'project_number_prefix')->first();
            $project_number_prefix = $admin_setting->value ?? '24-'; // Default prefix

            // Determine next number
            if ($last_project && Str::startsWith($last_project->project_no, $project_number_prefix)) {
                $last_number = (int) Str::after($last_project->project_no, $project_number_prefix);
                $next_number = $last_number + 1;
            } else {
                $next_number = 1;
            }

            $project_no = $project_number_prefix . $next_number;

            $filePaths = [];
            if ($request->hasFile('customer_documents')) {
                foreach ($request->file('customer_documents') as $file) {
                    $fileName = time() . '-' . Str::random(2) . '-' . $file->getClientOriginalName();
                    $file->move(public_path('project_document/customer_documents/'), $fileName);
                    $filePaths[] = 'project_document/customer_documents/' . $fileName;
                }
            }
            // Create new project
            $project = new Project();
            $project->project_name = $request->project_name;
            $project->customer_ref = $request->customer_ref;
            $project->witrack_no = $request->witrack_no;
            $project->country = $request->country;
            $project->customer_name = $request->customer_name;
            $project->sales_name = $request->sales_name;
            $project->sales_order_number = $request->sales_order_number;
            $project->currency = $request->sales_order_s_currency;
            $project->article_no = rand(1000000, 99999999);
            $project->isWITrack_project = "1";
            $project->inbox_production_eng_WiTrack_project_created = "1";
            $project->documents = json_encode($filePaths);
            $project->save();

            $emails = ProductionTeamDetail::select('email', 'name', 'designation')->where('designation','!=','Order Management')->get();
            foreach ($emails as $sendEmail) {
                $redirectLink = route('ProductionManagerProjectIndex');

                if ($sendEmail->designation == "Production Engineer" && $project->assembly_quotation_ref == null) {
                    $redirectLink = route('ProductionManagerInbox');
                }
                $emailData = [
                    'project_name' => $request->project_name,
                    'witrack_no' => $request->witrack_no,
                    'assembly_quotation_ref' => $project->assembly_quotation_ref,
                    'sales_name' => $request->sales_name,
                    'customer_name' => $request->customer_name,
                    'country' => $request->country,
                    'designation' => $sendEmail->designation,
                    'email' => $sendEmail->email,
                    'name' => $sendEmail->name,
                    'redirect_link' => $redirectLink,
                ];
                Mail::to($sendEmail->email)->send(new WItrackProjectCreateNotifyProductionTeam($emailData));
            }
            // Notify production team code Ends

            $data['project_details'] = $project;
            $data['project_details']['documents'] = $filePaths;
            $data['emails'] = $emails;

            return response()->json(['status' => '1', 'message' => 'Order added successfully..!!', 'data' => $data], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                "status" => 0,
                "error" => "Validation Failed",
                "messages" => $e->errors()
            ], 422);
        }
    }

    public function notify_wiTrack_project_cancelled(Request $request){
        $wiTrack_no = $request->witrack_no;
        $project = Project::where('witrack_no','=',$wiTrack_no)->first();
        if ($wiTrack_no == null) {
            return response()->json(['status' => '1', 'message' => 'Order not found..!!', 'data' => []], 200);
        } else {
            $isWITrack_project = $project->isWITrack_project;
            if ($isWITrack_project != "1") {
                return response()->json(['status' => '1', 'message' => 'Sorry this is not WITrack Order.', 'data' => []], 200);
            } else {
                $project->isWITrack_project_cancelled = "1";
                $project->isWITrack_project_cancelled_reason = $request->cancelled_reason;
                $project->save();
                $emails = ProductionTeamDetail::select('email', 'name', 'designation')->get();

                // Email code starts
                foreach ($emails as $sendEmail) {
                    $redirectLink = route('ProductionManagerProjectIndex');
                    $emailData = [
                        'project_name' => $project->project_name,
                        'witrack_no' => $project->witrack_no,
                        'assembly_quotation_ref' => $project->assembly_quotation_ref,
                        'sales_name' => $project->sales_name,
                        'customer_name' => $project->customer_name,
                        'country' => $project->country,
                        'designation' => $sendEmail->designation,
                        'email' => $sendEmail->email,
                        'name' => $sendEmail->name,
                        'redirect_link' => $redirectLink,
                    ];
                    Mail::to($sendEmail->email)->send(new WItrackProjectCancelNotifyProductionTeam($emailData));
                }
                // Email code ends
                $data['emails'] = $emails;
                return response()->json(['status' => '1', 'message' => 'This WITrack order is cancelled.', 'wiTrack_no' => $wiTrack_no], 200);
            }
        }
    }

    public function getBOM(Request $request){
        $quotation_number = $request->quotation_number;
        $full_article_number = $request->full_article_number;

        $item_id =  $request->item_id;
        $item_name =  $request->item_name;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://wilo.360websitedemo.com/api/getBOM',
            //CURLOPT_URL => 'http://wme-estimationtool/api/getBOM',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => http_build_query(['item_id' => $item_id, 'item_name' => $item_name]),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $decodedResponse = json_decode($response);

        return response()->json($decodedResponse);
    }

    // complete order
    public function sendProjectCompleteMsgToWitrack(Request $request){
        $witrack_no = $request->witrack_no;
        if ($witrack_no == null) {
            return response()->json(['status' => '0', 'message' => 'Please enter WITrack number', 'data' => []]);
        } else {
            return response()->json(['status' => '1', 'message' => 'Project Order completed successfully..!!', 'witrack_no' => $witrack_no, 'Project Status' => 'Completed'], 200);
        }
    }

    // Full complete order
    public function sendProjectFullCompleteMsgToWitrack(Request $request){
        $project_id = $request->project_id;
        if ($project_id == null) {
            return response()->json(['status' => '0', 'message' => 'Please enter Project id', 'data' => []]);
        } else {
            return response()->json(['status' => '1', 'message' => 'Projects full Order is completed successfully..!!', 'project_id' => $project_id, 'Project Status' => 'Full Order Only Completed'], 200);
        }
    }

    // Partial order completed
    public function sendProjectPartialCompleteMsgToWitrack(Request $request){
        $qty = $request->qty;
        if ($qty == null) {
            return response()->json(['status' => '0', 'message' => 'Please enter qty  number', 'data' => []]);
        } else {
            return response()->json(['status' => '1', 'message' => 'Projects partial Order is completed successfully..!!', 'qty' => $qty, 'Project Status' => 'Partial Order Only Completed'], 200);
        }
    }

    public function orderRejectByProductionEng(Request $request){
        $project = Project::find($request->projectId);
        $reject_reason = $project->rejection_selected_reason;

        $curl = curl_init();
        
        curl_setopt_array($curl, [
            CURLOPT_URL => 'http://wilo.infoaim.in/register/CancelOrder',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => http_build_query(['WIRefNo' => $project->witrack_no, 'Comment' => $reject_reason]),
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded']
        ]);
        
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;

        if($request->WIRefNo == null){
            return response()->json(['success' => true, 'message' => 'Please enter WITrack Number to reject.'], 200);
        }
        else if ($project) {
            return response()->json(['status' => '1', 'success' => true, 'message' => 'This Project Order is rejected by Production Engineer.','Reject Reason'=> $project->rejection_selected_reason], 200);
        }
        else{
            return response()->json(['success' => false, 'message' => 'No Project found'], 400);
        }
    }
}
