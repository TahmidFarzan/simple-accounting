<?php

namespace App\Http\Controllers\InternalUser;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Utilities\SystemConstant;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
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
        $authenticationLogSetting = SystemConstant::authenticationLogSetting();
        $statusInformation = array("status" => "errors","message" => array());
        $authenticationLog = AuthenticationLog::where("id",$id)->firstOrFail();

        $dateDiff = strtotime(Carbon::now()) - strtotime($authenticationLog->login_at);
        $dateDiffInDays = abs(round($dateDiff / (60 * 60 * 24)));
        if(($dateDiffInDays < $authenticationLogSetting["delete_records_older_than"])){
            array_push($statusInformation["message"], "Record must be older than ".$authenticationLogSetting["delete_records_older_than"]." days. More ".($authenticationLogSetting["delete_records_older_than"] - $dateDiffInDays)." days need to be passed.");
        }
        else{
            if($authenticationLog->delete()){
                $statusInformation["status"] = "status";
                array_push($statusInformation["message"], "Authentication log successfully deleted.");
            }
            else{
                array_push($statusInformation["message"], "Fali to delete the authentication log.");
            }
        }

        return redirect()->back()->with([$statusInformation["status"] => (count($statusInformation["message"])<=1) ? implode(",",$statusInformation["message"]) : $statusInformation["message"]]);
    }

    public function deleteAllLogs(Request $request)
    {
        $authenticationLogSetting = SystemConstant::authenticationLogSetting();
        $deleteRecordsOlderThan = ($request->delete_records_older_than > 0) ? $request->delete_records_older_than : $authenticationLogSetting["delete_records_older_than"];
        $statusInformation=array("status" => "errors","message" => array());
        if(($authenticationLogSetting["delete_records_older_than"] >= $deleteRecordsOlderThan) && ($deleteRecordsOlderThan <= 365)){
            $cutOffDate = Carbon::now()->subDays($deleteRecordsOlderThan)->format('Y-m-d H:i:s');

            if(AuthenticationLog::where('login_at', '<', $cutOffDate)->count() > 0){
                $authenticationLogId = AuthenticationLog::where('login_at', '<', $cutOffDate)->pluck("id");
                $authenticationLogDelete = AuthenticationLog::whereIn("id",$authenticationLogId)->delete();
                if($authenticationLogDelete){
                    $statusInformation["status"] = "status";
                    array_push($statusInformation["message"], "All authentication log are deleted successfully.");
                }
                else{
                    $statusInformation["status"] = "errors";
                    array_push($statusInformation["message"], "Fail to delete.");
                }
            }
            else{
                $statusInformation["status"] = "status";
                array_push($statusInformation["message"], "No older authentication log exit to be deleted.");
            }
        }
        else{
            $statusInformation["status"] = "errors";
            array_push($statusInformation["message"], "Please enter valid day count.");
            array_push($statusInformation["message"], "The delete records older than must equal or greater then ".$authenticationLogSetting["delete_records_older_than"]." days");
            array_push($statusInformation["message"], "The delete records older than must equal or less than 365.");
        }

        return redirect()->back()->with([$statusInformation["status"] => $statusInformation["message"]]);
    }
}
