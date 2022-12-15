<?php

namespace App\Http\Controllers\InternalUser;

use Carbon\Carbon;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Utilities\SystemConstant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Models\ProjectContractClient;
use Spatie\Activitylog\Facades\LogBatch;
use Illuminate\Support\Facades\Validator;
use App\Mail\EmailSendForProjectContractClient;

class ProjectContractClientController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
        $this->middleware(['user.user.permission.check:PCCLMP01'])->only(["index"]);
        $this->middleware(['user.user.permission.check:PCCLMP02'])->only(["create","save"]);
        $this->middleware(['user.user.permission.check:PCCLMP03'])->only(["details"]);
        $this->middleware(['user.user.permission.check:PCCLMP04'])->only(["edit","update"]);
        $this->middleware(['user.user.permission.check:PCCLMP05'])->only(["trash"]);
        $this->middleware(['user.user.permission.check:PCCLMP06'])->only(["restore"]);
    }

    public function index(Request $request){
        $pagination = 5;
        $paginations = array(5,15,30,45,60,75,90,105,120);

        $activeContractClients = ProjectContractClient::orderby("name","asc");
        $trashContractClients = ProjectContractClient::onlyTrashed()->orderby("name","asc");

        if(count($request->input()) > 0){
            if($request->has('pagination')){
                $pagination = (in_array($request->pagination,$paginations)) ? $request->pagination : $pagination;
            }

            if($request->has('search') && !($request->search == null)){
                switch ($request->selected_nav_tab) {
                    case 'Active':
                        $activeContractClients = $activeContractClients->where("name","like","%".$request->search."%")
                                                                        ->orWhere("note","like","%".$request->search."%")
                                                                        ->orWhere("email","like","%".$request->search."%")
                                                                        ->orWhere("mobile_no","like","%".$request->search."%")
                                                                        ->orWhere("description","like","%".$request->search."%");
                    break;

                    case 'Trash':
                        $trashContractClients = $trashContractClients->where("name","like","%".$request->search."%")
                                                                                ->orWhere("note","like","%".$request->search."%")
                                                                                ->orWhere("email","like","%".$request->search."%")
                                                                                ->orWhere("mobile_no","like","%".$request->search."%")
                                                                                ->orWhere("description","like","%".$request->search."%");
                    break;

                    default:
                        abort(404,"Unknown nav.");
                    break;
                }
            }
        }
        $trashContractClients = $trashContractClients->paginate($pagination);
        $activeContractClients = $activeContractClients->paginate($pagination);

        return view('internal user.project contract.client.index',compact("activeContractClients","trashContractClients","paginations"));
    }

    public function create(){
        return view('internal user.project contract.client.create');
    }

    public function details($slug){
        $projectContractClient = ProjectContractClient::withTrashed()->where("slug",$slug)->firstOrFail();
        return view('internal user.project contract.client.details',compact("projectContractClient"));
    }

    public function edit($slug){
        $projectContractClient = ProjectContractClient::withTrashed()->where("slug",$slug)->firstOrFail();
        return view('internal user.project contract.client.edit',compact("projectContractClient"));
    }

    public function save(Request $request){
        $validator = Validator::make($request->all(),
            [
                'name' => 'required|max:200',
                'mobile_no' => 'nullable|max:20|regex:/^([0-9\s\-\+]*)$/|unique:project_contract_clients,mobile_no',
                'email' => 'nullable|email|max:255|unique:project_contract_clients,email',
                'gender' => 'required|in:Male,Female,Other',
                'address' => 'nullable',
                'description' => 'nullable',
                'note' => "nullable",
            ],
            [
                'name.required' => 'Name is required.',
                'name.max' => 'Name length can not greater then 200 chars.',

                'mobile_no.max' => 'Mobile no length can not greater then 20 chars.',
                'mobile_no.regex' => 'Mobile no must be mobile no.',
                'mobile_no.unique' => 'Mobile no must be unique.',

                'email.max' => 'Email length can not greater then 255 chars.',
                'email.email' => 'Email must be email.',
                'email.unique' => 'Email must be unique.',

                'gender.required' => 'Gender is reqired.',
                'gender.in' => 'Gender must one out of [Male,Female,Other].',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $statusInformation=array("status" => "errors","message" => collect());

        LogBatch::startBatch();
            $projectContractClient= new ProjectContractClient();
            $projectContractClient->name = $request->name;
            $projectContractClient->mobile_no = $request->mobile_no;
            $projectContractClient->email = $request->email;
            $projectContractClient->gender = $request->gender;
            $projectContractClient->address = $request->address;
            $projectContractClient->description = $request->description;
            $projectContractClient->note = $request->note;
            $projectContractClient->slug = SystemConstant::slugGenerator($request->name,200);
            $projectContractClient->created_at = Carbon::now();
            $projectContractClient->created_by_id = Auth::user()->id;
            $projectContractClient->updated_at = null;
            $saveProjectContractClient = $projectContractClient->save();
        LogBatch::endBatch();

        if($saveProjectContractClient){
            $this->sendEmail("Create","A new client (project contract) has been created by ".Auth::user()->name.".",$projectContractClient );

            $statusInformation["status"] = "status";
            $statusInformation["message"]->push("Successfully created.");
        }
        else{
            $statusInformation["status"] = "errors";
            $statusInformation["message"]->push("Fail to create.");
        }

        return redirect()->route("project.contract.client.index")->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    public function update(Request $request,$slug){
        $projectContractClientId = (ProjectContractClient::withTrashed()->where("slug",$slug)->firstOrFail())->id;

        $validator = Validator::make($request->all(),
            [
                'name' => 'required|max:200',
                'mobile_no' => 'nullable|max:20|regex:/^([0-9\s\-\+]*)$/|unique:project_contract_clients,mobile_no,'.$projectContractClientId,
                'email' => 'nullable|email|max:255|unique:project_contract_clients,email,'. $projectContractClientId,
                'gender' => 'required|in:Male,Female,Other',
                'address' => 'nullable',
                'description' => "nullable",
                'note' => "nullable",
            ],
            [
                'name.required' => 'Name is required.',
                'name.max' => 'Name length can not greater then 200 chars.',

                'mobile_no.max' => 'Mobile no length can not greater then 20 chars.',
                'mobile_no.regex' => 'Mobile no must be mobile no.',
                'mobile_no.unique' => 'Mobile no must be unique.',

                'email.max' => 'Email length can not greater then 255 chars.',
                'email.email' => 'Email must be email.',
                'email.unique' => 'Email must be unique.',

                'gender.required' => 'Gender is reqired.',
                'gender.in' => 'Gender must one out of [Male,Female,Other].',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $statusInformation=array("status" => "errors","message" => collect());

        LogBatch::startBatch();
            $projectContractClient = ProjectContractClient::withTrashed()->where("slug",$slug)->firstOrFail();
            $projectContractClient->name = $request->name;
            $projectContractClient->mobile_no = $request->mobile_no;
            $projectContractClient->email = $request->email;
            $projectContractClient->gender = $request->gender;
            $projectContractClient->address = $request->address;
            $projectContractClient->description = $request->description;
            $projectContractClient->note = $request->note;
            $projectContractClient->slug = SystemConstant::slugGenerator($request->name,200);
            $projectContractClient->updated_at = Carbon::now();
            $updateProjectContractClient = $projectContractClient->update();
        LogBatch::endBatch();

        if($updateProjectContractClient){
            $this->sendEmail("Update","Client (Project contract) has been updated by ".Auth::user()->name.".",$projectContractClient );

            $statusInformation["status"] = "status";
            $statusInformation["message"]->push("Successfully updated.");
        }
        else{
            $statusInformation["status"] = "errors";
            $statusInformation["message"]->push("Fail to update.");
        }

        return redirect()->route("project.contract.client.index")->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    public function trash($slug){
        $statusInformation = array("status" => "errors","message" => collect());

        if((ProjectContractClient::onlyTrashed()->where("slug",$slug)->count()) == 0){

            LogBatch::startBatch();
                $projectContractClient = ProjectContractClient::where("slug",$slug)->firstOrFail();
                $trashedProjectContractClient = $projectContractClient->delete();
            LogBatch::endBatch();

            if($trashedProjectContractClient){
                $this->sendEmail("Trash","Client (Project contract) has been trashed by ".Auth::user()->name.".",$projectContractClient );

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

        return redirect()->route("project.contract.client.index")->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    public function restore($slug){
        $statusInformation = array("status" => "errors","message" => collect());

        if((ProjectContractClient::where("slug",$slug)->count()) == 0){
            LogBatch::startBatch();
                $projectContractClient = ProjectContractClient::onlyTrashed()->where("slug",$slug)->firstOrFail();
                $restoreProjectContractClient = $projectContractClient->restore();
            LogBatch::endBatch();

            if($restoreProjectContractClient){
                $this->sendEmail("Restore","Client (Project contract) has been restored by ".Auth::user()->name.".",$projectContractClient );

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

        return redirect()->route("project.contract.client.index")->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    private function sendEmail($event,$subject,ProjectContractClient $projectContractClient ){
        $envelope = array();

        $emailSendSetting = Setting::where( 'code','EmailSendSetting')->firstOrFail()->fields_with_values["ProjectContractClient"];

        $envelope["to"] = $emailSendSetting["to"];
        $envelope["cc"] = $emailSendSetting["cc"];
        $envelope["from"] = $emailSendSetting["from"];
        $envelope["reply"] = $emailSendSetting["reply"];

        if(($emailSendSetting["send"] == true) && (($emailSendSetting["event"] == "All") || (!($emailSendSetting["event"] == "All") && ($emailSendSetting["event"] == $event)))){
            Mail::send(new EmailSendForProjectContractClient($event,$envelope,$subject,$projectContractClient));
        }
    }
}
