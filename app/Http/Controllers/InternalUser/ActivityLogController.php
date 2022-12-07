<?php

namespace App\Http\Controllers\InternalUser;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
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
        $subjectTypes = array("All","User","Setting","User permission group");
        $events = array("All","Created","Updated","Deleted","Trashed","Restored");
        $activitLogs = Activity::orderBy("id","desc");

        if(count($request->input()) > 0){
            if($request->has('pagination')){
                $pagination = (in_array($request->pagination,$paginations)) ? $request->pagination : $pagination;
            }

            if($request->has('subject_type')){
                $request->subject_type=(in_array($request->subject_type,$subjectTypes)) ? $request->subject_type : null;
                if(!($request->subject_type == null) && !($request->subject_type == "All")){
                    $activitLogs = $activitLogs->where("subject_type","like","%".Str::studly($request->subject_type)."%");
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
        $statusInformation = array("status" => "errors","message" => collect());

        $activityLogSetting = SystemConstant::activityLogSetting();

        $activitLog = Activity::where("id",$id)->firstOrFail();

        $dateDiff = strtotime(Carbon::now()) - strtotime($activitLog->created_at);
        $dateDiffInDays = abs(round($dateDiff / (60 * 60 * 24)));

        if(($dateDiffInDays < $activityLogSetting["delete_records_older_than"])){
            $statusInformation["message"]->push("Record must be older than ".$activityLogSetting["delete_records_older_than"]." days.");
            $statusInformation["message"]->push("More ".($activityLogSetting["delete_records_older_than"] - $dateDiffInDays)." days need to be passed.");
        }
        else{
            if($activitLog->delete()){
                $statusInformation["status"] = "status";
                $statusInformation["message"]->push("Record successfully deleted.");
            }
            else{
                $statusInformation["message"]->push("Fali to delete record.");
            }
        }

        return redirect()->back()->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    public function deleteAllLogs(Request $request)
    {
        $statusInformation = array("status" => "errors","message" => collect());

        $activityLogSetting = SystemConstant::activityLogSetting();
        $deleteRecordsOlderThan = ($request->delete_records_older_than > 0) ? $request->delete_records_older_than : $activityLogSetting["delete_records_older_than"];

        if(($activityLogSetting["delete_records_older_than"] >= $deleteRecordsOlderThan) && ($deleteRecordsOlderThan <= 365)){

            $cutOffDate = Carbon::now()->subDays($deleteRecordsOlderThan)->format('Y-m-d H:i:s');

            if(Activity::where('created_at', '<', $cutOffDate)->count() > 0){
                $activitLogsId = Activity::where('created_at', '<', $cutOffDate)->pluck("id");
                $activitLogsDelete = Activity::whereIn("id",$activitLogsId)->delete();
                if($activitLogsDelete){
                    $statusInformation["status"] = "status";
                    $statusInformation["message"]->push( "All record are deleted successfully.");
                }
                else{
                    $statusInformation["status"] = "errors";
                    $statusInformation["message"]->push("Fail to delete records.");
                }
            }
            else{
                $statusInformation["status"] = "status";
                $statusInformation["message"]->push("No older record exit to be deleted.");
            }
        }
        else{
            $statusInformation["status"]="errors";
            $statusInformation["message"]->push("Please enter valid day count.");
            $statusInformation["message"]->push("The delete records older than must equal or greater then ".$activityLogSetting["delete_records_older_than"]." days");
            $statusInformation["message"]->push("The delete records older than must equal or less than 365.");
        }

        return redirect()->back()->with([$statusInformation["status"] => $statusInformation["message"]]);
    }
}
