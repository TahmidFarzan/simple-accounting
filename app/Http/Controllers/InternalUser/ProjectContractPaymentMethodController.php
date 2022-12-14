<?php

namespace App\Http\Controllers\InternalUser;

use Carbon\Carbon;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Utilities\SystemConstant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Spatie\Activitylog\Facades\LogBatch;
use Illuminate\Support\Facades\Validator;
use App\Models\ProjectContractPaymentMethod;
use App\Mail\EmailSendForProjectContractPaymentMethod;

class ProjectContractPaymentMethodController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
        $this->middleware(['user.user.permission.check:PCPMMP01'])->only(["index"]);
        $this->middleware(['user.user.permission.check:PCPMMP02'])->only(["create","save"]);
        $this->middleware(['user.user.permission.check:PCPMMP03'])->only(["details"]);
        $this->middleware(['user.user.permission.check:PCPMMP04'])->only(["edit","update"]);
        $this->middleware(['user.user.permission.check:PCPMMP05'])->only(["trash"]);
        $this->middleware(['user.user.permission.check:PCPMMP06'])->only(["restore"]);
    }

    public function index(Request $request){
        $pagination = 5;
        $paginations = array(5,15,30,45,60,75,90,105,120);

        $activeContractPaymentMethods = ProjectContractPaymentMethod::orderby("name","asc");
        $trashContractPaymentMethods = ProjectContractPaymentMethod::onlyTrashed()->orderby("name","asc");

        if(count($request->input()) > 0){
            if($request->has('pagination')){
                $pagination = (in_array($request->pagination,$paginations)) ? $request->pagination : $pagination;
            }

            if($request->has('search') && !($request->search == null)){
                switch ($request->selected_nav_tab) {
                    case 'Active':
                        $activeContractPaymentMethods = $activeContractPaymentMethods->where("name","like","%".$request->search."%")
                                                                        ->orWhere("description","like","%".$request->search."%");
                    break;

                    case 'Trash':
                        $trashContractPaymentMethods = $trashContractPaymentMethods->where("name","like","%".$request->search."%")
                                                                        ->orWhere("description","like","%".$request->search."%");
                    break;

                    default:
                        abort(404,"Unknown nav.");
                    break;
                }
            }
        }
        $trashContractPaymentMethods = $trashContractPaymentMethods->paginate($pagination);
        $activeContractPaymentMethods = $activeContractPaymentMethods->paginate($pagination);

        return view('internal user.project contract.payment method.index',compact("activeContractPaymentMethods","trashContractPaymentMethods","paginations"));
    }

    public function create(){
        return view('internal user.project contract.payment method.create');
    }

    public function details($slug){
        $projectContractPaymentMethod = ProjectContractPaymentMethod::withTrashed()->where("slug",$slug)->firstOrFail();
        return view('internal user.project contract.payment method.details',compact("projectContractPaymentMethod"));
    }

    public function edit($slug){
        $projectContractPaymentMethod = ProjectContractPaymentMethod::withTrashed()->where("slug",$slug)->firstOrFail();
        return view('internal user.project contract.payment method.edit',compact("projectContractPaymentMethod"));
    }

    public function save(Request $request){
        $validator = Validator::make($request->all(),
            [
                'name' => 'required|max:200|unique:project_contract_payment_methods,name',
                'description' => 'nullable',
            ],
            [
                'name.required' => 'Name is required.',
                'name.max' => 'Name length can not greater then 200 chars.',
                'name.unique' => 'Name must be unique.',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $statusInformation=array("status" => "errors","message" => collect());

        LogBatch::startBatch();
            $projectContractPaymentMethod= new ProjectContractPaymentMethod();
            $projectContractPaymentMethod->name = $request->name;
            $projectContractPaymentMethod->description = $request->description;
            $projectContractPaymentMethod->slug = SystemConstant::slugGenerator($request->name,200);
            $projectContractPaymentMethod->created_at = Carbon::now();
            $projectContractPaymentMethod->created_by_id = Auth::user()->id;
            $projectContractPaymentMethod->updated_at = null;
            $saveProjectContractPaymentMethod = $projectContractPaymentMethod->save();
        LogBatch::endBatch();

        if($saveProjectContractPaymentMethod){
            $this->sendEmail("Create","A new project contract payment method has been created by ".Auth::user()->name.".",$projectContractPaymentMethod );

            $statusInformation["status"] = "status";
            $statusInformation["message"] = "Record successfully created.";
        }
        else{
            $statusInformation["status"] = "errors";
            $statusInformation["message"] = "Fail to Record.";
        }

        return redirect()->route("project.contract.payment.method.index")->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    public function update(Request $request,$slug){
        $projectContractPaymentMethodId = (ProjectContractPaymentMethod::withTrashed()->where("slug",$slug)->firstOrFail())->id;

        $validator = Validator::make($request->all(),
            [
                'name' => 'required|max:200|unique:project_contract_payment_methods,name,'.$projectContractPaymentMethodId,
                'description' => "nullable",
            ],
            [
                'name.required' => 'Name is required.',
                'name.max' => 'Name length can not greater then 200 chars.',
                'name.unique' => 'Name must be unique.',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $statusInformation=array("status" => "errors","message" => collect());

        LogBatch::startBatch();
            $projectContractPaymentMethod = ProjectContractPaymentMethod::withTrashed()->where("slug",$slug)->firstOrFail();
            $projectContractPaymentMethod->name = $request->name;
            $projectContractPaymentMethod->description = $request->description;
            $projectContractPaymentMethod->slug = SystemConstant::slugGenerator($request->name,200);
            $projectContractPaymentMethod->updated_at = Carbon::now();
            $updateProjectContractPaymentMethod = $projectContractPaymentMethod->update();
        LogBatch::endBatch();

        if($updateProjectContractPaymentMethod){
            $statusInformation["status"] = "status";
            $statusInformation["message"] = "Successfully updated.";

            $this->sendEmail("Update","Project contract payment method has been updated by ".Auth::user()->name.".",$projectContractPaymentMethod );
        }
        else{
            $statusInformation["status"] = "errors";
            $statusInformation["message"] = "Fail to update.";
        }

        return redirect()->route("project.contract.payment.method.index")->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    public function trash($slug){
        $statusInformation = array("status" => "errors","message" => collect());

        if((ProjectContractPaymentMethod::onlyTrashed()->where("slug",$slug)->count()) == 0){

            LogBatch::startBatch();
                $projectContractPaymentMethod = ProjectContractPaymentMethod::where("slug",$slug)->firstOrFail();
                $trashedProjectContractPaymentMethod = $projectContractPaymentMethod->delete();
            LogBatch::endBatch();

            if($trashedProjectContractPaymentMethod){
                $this->sendEmail("Trash","Project contract payment method has been trashed by ".Auth::user()->name.".",$projectContractPaymentMethod );
                $statusInformation["status"] = "status";
                $statusInformation["message"]->push("Successfully trashed.");
            }
            else{
                $statusInformation["message"]->push("Fail to trash.");
            }
        }
        else{
            $statusInformation["status"] = "status";
            $statusInformation["message"]->push("Already trashed.");
        }

        return redirect()->route("project.contract.payment.method.index")->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    public function restore($slug){
        $statusInformation = array("status" => "errors","message" => collect());

        if((ProjectContractPaymentMethod::where("slug",$slug)->count()) == 0){
            LogBatch::startBatch();
                $projectContractPaymentMethod = ProjectContractPaymentMethod::onlyTrashed()->where("slug",$slug)->firstOrFail();
                $restoreProjectContractPaymentMethod = $projectContractPaymentMethod->restore();
            LogBatch::endBatch();

            if($restoreProjectContractPaymentMethod){
                $this->sendEmail("Restore","Project contract payment method has been restored by ".Auth::user()->name.".",$projectContractPaymentMethod );

                $statusInformation["status"] = "status";
                $statusInformation["message"]->push("Successfully restored.");
            }
            else{
                $statusInformation["message"]->push("Fail to restore.");
            }
        }
        else{
            $statusInformation["status"] = "status";
            $statusInformation["message"]->push("Already actived.");
        }

        return redirect()->route("project.contract.payment.method.index")->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    private function sendEmail($event,$subject,ProjectContractPaymentMethod $projectContractPaymentMethod ){
        $envelope = array();

        $notificationSetting = Setting::where( 'code','NotificationSetting')->firstOrFail()->fields_with_values["User"];

        $envelope["to"] = $notificationSetting["to"];
        $envelope["cc"] = $notificationSetting["cc"];
        $envelope["from"] = $notificationSetting["from"];
        $envelope["reply"] = $notificationSetting["reply"];

        if(($notificationSetting["send"] == true) && (($notificationSetting["event"] == "All") || (!($notificationSetting["event"] == "All") && ($notificationSetting["event"] == $event)))){
            Mail::send(new EmailSendForProjectContractPaymentMethod($event,$envelope,$subject,$projectContractPaymentMethod));
        }
    }
}
