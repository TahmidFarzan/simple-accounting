<?php

namespace App\Http\Controllers\InternalUser;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Utilities\SystemConstant;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
        $this->middleware(['user.user.permission.check:ACLMP01'])->only(["index"]);
        $this->middleware(['user.user.permission.check:ACLMP02'])->only(["details"]);
        $this->middleware(['user.user.permission.check:ACLMP03'])->only(["delete"]);
        $this->middleware(['user.user.permission.check:ACLMP04'])->only(["deleteAllLogs"]);
    }

    public function index(Request $request)
    {
        $pagination = 5;
        $paginations = array(5,15,30,45,60,75,90,100);
        $causers = User::orderBy("id","asc");
        $subjectTypes = array("All","User");
        $events = array("All","Created","Updated","Deleted","Trashed","Restored");
        $activitLogs = Activity::orderBy("id","desc");

        if(count($request->input()) > 0){
            if($request->has('pagination')){
                $pagination = (in_array($request->pagination,$paginations)) ? $request->pagination : $pagination;
            }

            if($request->has('subject_type')){
                $request->subject_type=(in_array($request->subject_type,$subjectTypes)) ? $request->subject_type : null;
                if(!($request->subject_type == null) && !($request->subject_type == "All")){
                    $activitLogs = $activitLogs->where("subject_type","like","%".$request->subject_type."%");
                }
            }

            if($request->has('event')){
                $request->event = (in_array($request->event,$events)) ? $request->event : null;
                if(!($request->event == null) && !($request->event == "All")){
                    $activitLogs = $activitLogs->where("event",$request->event);
                }
            }

            if($request->has('causer')){
                if(!($request->causer == null) && !($request->causer == "All")){
                    $causerInformation = User::where("slug",$request->causer)->first();
                    if($causerInformation){
                        $activitLogs = $activitLogs->where("causer_id",$causerInformation->id);
                    }
                }
            }

            if(($request->has('created_at')) && ($request->has('created_at_condition'))){
                $createAtCondition = in_array($request->created_at_condition,array("=","<",">", ">=","<=")) ? $request->created_at_condition : null ;
                if(!($request->created_at == null) && !($request->created_at_condition == null)){
                    $activitLogs = $activitLogs->where(DB::raw("(STR_TO_DATE(created_at,'%Y-%m-%d'))"),$request->created_at_condition,$request->created_at);
                }
            }
        }

        $causers = $causers->select("name","slug")->get();
        $activitLogs = $activitLogs->paginate($pagination);
        return view('internal user.activity log.index',compact("activitLogs","paginations","subjectTypes","events","causers"));
    }

    public function details($id)
    {
        $activitLog = Activity::where("id",$id)->firstOrFail();
        return view('internal user.activity log.details',compact("activitLog"));
    }

    public function delete($id)
    {
        $activityLogSetting = SystemConstant::activityLogSetting();
        $statusInformation = array("status" => "errors","message" => array());
        $activitLog = Activity::where("id",$id)->firstOrFail();
        $dateDiff = strtotime(Carbon::now()) - strtotime($activitLog->created_at);
        $dateDiffInDays = abs(round($dateDiff / (60 * 60 * 24)));
        if(($dateDiffInDays < $activityLogSetting["delete_records_older_than"])){
            array_push($statusInformation["message"], "Record must be older than ".$activityLogSetting["delete_records_older_than"]." days. More ".($activityLogSetting["delete_records_older_than"] - $dateDiffInDays)." days need to be passed.");
        }
        else{
            if($activitLog->delete()){
                $statusInformation["status"] = "status";
                array_push($statusInformation["message"], "Activity log successfully deleted.");
            }
            else{
                array_push($statusInformation["message"], "Fali to delete the activity log.");
            }
        }

        return redirect()->back()->with([$statusInformation["status"] => (count($statusInformation["message"])<=1) ? implode(",",$statusInformation["message"]) : $statusInformation["message"]]);
    }

    public function deleteAllLogs(Request $request)
    {
        $activityLogSetting = SystemConstant::activityLogSetting();
        $deleteRecordsOlderThan = ($request->delete_records_older_than > 0) ? $request->delete_records_older_than : $activityLogSetting["delete_records_older_than"];
        $statusInformation = array("status" => "errors","message" => array());
        if(($activityLogSetting["delete_records_older_than"] >= $deleteRecordsOlderThan) && ($deleteRecordsOlderThan <= 365)){
            $cutOffDate = Carbon::now()->subDays($deleteRecordsOlderThan)->format('Y-m-d H:i:s');
            if(Activity::where('created_at', '<', $cutOffDate)->count() > 0){
                $activitLogsId = Activity::where('created_at', '<', $cutOffDate)->pluck("id");
                $activitLogsDelete = Activity::whereIn("id",$activitLogsId)->delete();
                if($activitLogsDelete){
                    $statusInformation["status"] = "status";
                    array_push($statusInformation["message"], "All activity log are deleted successfully.");
                }
                else{
                    $statusInformation["status"] = "errors";
                    array_push($statusInformation["message"], "Fail to delete.");
                }
            }
            else{
                $statusInformation["status"] = "status";
                array_push($statusInformation["message"], "No older activity log exit to be deleted.");
            }
        }
        else{
            $statusInformation["status"]="errors";
            array_push($statusInformation["message"], "Please enter valid day count.");
            array_push($statusInformation["message"], "The delete records older than must equal or greater then ".$activityLogSetting["delete_records_older_than"]." days");
            array_push($statusInformation["message"], "The delete records older than must equal or less than 365.");
        }

        return redirect()->back()->with([$statusInformation["status"] => $statusInformation["message"]]);
    }
}
