<?php

namespace App\Http\Controllers\InternalUser;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Utilities\SystemConstant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Mail\EmailSendForAuthenticationLog;
use Rappasoft\LaravelAuthenticationLog\Models\AuthenticationLog;


class AuthenticationLogController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
        $this->middleware(['user.user.permission.check:AULMP01'])->only(["index"]);
        $this->middleware(['user.user.permission.check:AULMP02'])->only(["details"]);
        $this->middleware(['user.user.permission.check:AULMP03'])->only(["delete"]);
        $this->middleware(['user.user.permission.check:AULMP04'])->only(["deleteAllLogs"]);
    }

    public function index(Request $request)
    {
        $pagination = 5;
        $paginations = array(5,15,30,45,60,75,90,100);
        $users = User::orderBy("name","asc")->get();
        $authenticationLogs = AuthenticationLog::orderBy("id","desc");
        if(count($request->input()) > 0){
            if($request->has('pagination')){
                $pagination = (in_array($request->pagination,$paginations)) ? $request->pagination : $pagination;
            }

            if($request->has('user')){
                if(!($request->user == null) && !(Str::upper($request->user) == "ALL")){
                    $userInformation = User::where("slug",$request->user)->first();
                    if($userInformation){
                        $authenticationLogs = $authenticationLogs->where("authenticatable_id",$userInformation->authenticatable_id);
                    }
                }
            }

            if($request->has('login_status')){
                if(!($request->login_status == null) && !(Str::upper($request->login_status) == "ALL")){
                    $request->login_status = ($request->login_status == 1) ? 1 : (($request->login_status == 0) ? 0 : (($request->login_status == null) ? null : "All"));
                    $authenticationLogs = $authenticationLogs->where("login_successful",$request->login_status);
                }
            }

            if(($request->has('date')) && ($request->has('date_type')) && ($request->has('date_condition'))){
                $dateCondition = in_array($request->date_condition,array("=","<",">",">=","<=")) ? $request->date_condition : null ;
                $dateType = (in_array($request->date_type,array("login_at","logout_at"))) ? $request->date_type : null;
                if(!($dateType == null) && !($request->date == null) && !($dateCondition == null)){
                    $aauthenticationLogs = $authenticationLogs->where(DB::raw("(STR_TO_DATE(".$dateType.",'%Y-%m-%d'))"),$request->date_condition,$request->date);
                }
            }
        }

        $authenticationLogs = $authenticationLogs->paginate($pagination);

        return view('internal user.authentication log.index',compact("authenticationLogs","paginations","users"));
    }

    public function details($id)
    {
        $authenticationLog = AuthenticationLog::where("id",$id)->firstOrFail();
        return view('internal user.authentication log.details',compact("authenticationLog"));
    }

    public function delete($id)
    {
        $statusInformation = array("status" => "errors","message" => collect());

        $authenticationLogSetting = SystemConstant::authenticationLogSetting();

        $authenticationLog = AuthenticationLog::where("id",$id)->firstOrFail();

        $dateDiff = strtotime(Carbon::now()) - strtotime($authenticationLog->login_at);
        $dateDiffInDays = abs(round($dateDiff / (60 * 60 * 24)));

        if(($dateDiffInDays < $authenticationLogSetting["delete_records_older_than"])){
            $statusInformation["message"]->push("Record must be older than ".$authenticationLogSetting["delete_records_older_than"]." days.");
            $statusInformation["message"]->push("More ".($authenticationLogSetting["delete_records_older_than"] - $dateDiffInDays)." days need to be passed.");
        }
        else{
            if($authenticationLog->delete()){

                $this->sendEmail("Delete","Authenticate log has been deleted by ".Auth::user()->name.".",$authenticationLog);

                $statusInformation["status"] = "status";
                $statusInformation["message"]->push("Successfully deleted.");
            }
            else{
                $statusInformation["message"]->push("Fali to delete record.");
            }
        }

        return redirect()->back()->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    public function deleteAllLogs(Request $request)
    {
        $statusInformation=array("status" => "errors","message" => collect());

        $authenticationLogSetting = SystemConstant::authenticationLogSetting();

        $deleteRecordsOlderThan = ($request->delete_records_older_than > 0) ? $request->delete_records_older_than : $authenticationLogSetting["delete_records_older_than"];

        if(($authenticationLogSetting["delete_records_older_than"] >= $deleteRecordsOlderThan) && ($deleteRecordsOlderThan <= 365)){
            $cutOffDate = Carbon::now()->subDays($deleteRecordsOlderThan)->format('Y-m-d H:i:s');

            if(AuthenticationLog::where('login_at', '<', $cutOffDate)->count() > 0){
                $authenticationLogDelete = 0;

                $authenticationLogId = AuthenticationLog::where('login_at', '<', $cutOffDate)->pluck("id");
                $authenticationLogs = AuthenticationLog::whereIn("id",$authenticationLogId)->get();

                foreach($authenticationLogs as $perAuthticationLog){
                    $authenticationLog = AuthenticationLog::where("id",$perAuthticationLog->id)->firstOrFail();
                    if($authenticationLog->delete()){
                        $authenticationLogDelete = $authenticationLogDelete + 1;
                    }
                }

                if($authenticationLogDelete == $authenticationLogs->count()){
                    $this->sendEmail("DeleteAll","Authenticate logs has been deleted by ".Auth::user()->name.".",$authenticationLogs);

                    $statusInformation["status"] = "status";
                    $statusInformation["message"]->push("Selected logs successfully deleted.");
                }
                else{
                    $statusInformation["status"] = "errors";
                    $statusInformation["message"]->push("Fail to delete.");
                }
            }
            else{
                $statusInformation["status"] = "status";
                $statusInformation["message"]->push("No older log exit to be deleted.");
            }
        }
        else{
            $statusInformation["status"] = "errors";
            $statusInformation["message"]->push("Please enter valid day count.");
            $statusInformation["message"]->push("The selected logs must older than equal or greater then ".$authenticationLogSetting["delete_records_older_than"]." days");
            $statusInformation["message"]->push("The selected logs must older than must equal or less than 365.");
        }

        return redirect()->back()->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    private function sendEmail($event,$subject,$authenticationLogs){
        $envelope = array();

        $emailSendSetting = Setting::where( 'code','EmailSendSetting')->firstOrFail()->fields_with_values["AuthenticationLog"];

        $envelope["to"] = $emailSendSetting["to"];
        $envelope["cc"] = $emailSendSetting["cc"];
        $envelope["from"] = $emailSendSetting["from"];
        $envelope["reply"] = $emailSendSetting["reply"];

        if(($emailSendSetting["send"] == true) && (($emailSendSetting["event"] == "All") || (!($emailSendSetting["event"] == "All") && ($emailSendSetting["event"] == $event)))){
            Mail::send(new EmailSendForAuthenticationLog($envelope,$subject,$authenticationLogs));
        }
    }
}
