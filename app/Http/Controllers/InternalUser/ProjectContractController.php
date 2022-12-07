<?php

namespace App\Http\Controllers\Internaluser;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ProjectContract;
use App\Utilities\SystemConstant;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\ProjectContractClient;
use App\Models\ProjectContractCategory;
use Spatie\Activitylog\Facades\LogBatch;
use Illuminate\Support\Facades\Validator;

class ProjectContractController extends Controller
{
    private $projectContractId = null;

    public function __construct()
    {
        $this->middleware(['auth','verified']);
        $this->middleware(['user.user.permission.check:PCMP01'])->only(["index"]);
        $this->middleware(['user.user.permission.check:PCMP02'])->only(["create","save"]);
        $this->middleware(['user.user.permission.check:PCMP03'])->only(["details"]);
        $this->middleware(['user.user.permission.check:PCMP04'])->only(["edit","update"]);
        $this->middleware(['user.user.permission.check:PCMP05'])->only(["delete"]);
        $this->middleware(['user.user.permission.check:PCMP06'])->only(["completeProjectContract"]);
        $this->middleware(['user.user.permission.check:PCMP07'])->only(["startReceivingPayment"]);
        $this->middleware(['user.user.permission.check:PCMP08'])->only(["completeReceivingPayment"]);
    }

    public function index(Request $request){
        $pagination = 5;
        $paginations = array(5,15,30,45,60,75,90,105,120);
        $receivableStatuses = array('Not started', 'Due', 'Partial', 'Complete');
        $projectContractClients = ProjectContractClient::orderby("name","asc")->get();
        $projectContractCategories = ProjectContractCategory::tree()->get()->toTree();

        $ongoingProjectContracts = ProjectContract::orderby("created_at","desc")->orderby("name","asc")->where("status","Ongoing");
        $completeProjectContracts = ProjectContract::orderby("created_at","desc")->orderby("name","asc")->where("status","Complete");

        if(count($request->input()) > 0){
            if($request->has('pagination')){
                $pagination = (in_array($request->pagination,$paginations)) ? $request->pagination : $pagination;
            }

            if($request->has('client') && !($request->client == null) && !($request->client == "All")){
                $projectContractClient = ProjectContractClient::where("slug",$request->client)->first();

                if($projectContractClient){
                    switch ($request->selected_nav_tab) {
                        case 'Ongoing':
                            $ongoingProjectContracts = $ongoingProjectContracts->where("client_id",$projectContractClient->id);
                        break;


                        case 'Complete':
                            $completeProjectContracts = $completeProjectContracts->where("client_id",$projectContractClient->id);
                        break;

                        default:
                            abort(404,"Unknown nav.");
                        break;
                    }
                }
            }

            if($request->has('category') && !($request->category == null) && !($request->category == "All")){
                $projectContractCategory = ProjectContractCategory::where("slug",$request->category)->first();

                if($projectContractCategory){
                    $allCategoryIds =  $projectContractCategory->descendants()->pluck("id")->toArray();
                    array_push($allCategoryIds,$projectContractCategory->id);

                    switch ($request->selected_nav_tab) {
                        case 'Ongoing':
                            $ongoingProjectContracts = $ongoingProjectContracts->whereIn("category_id",$allCategoryIds);
                        break;

                        case 'Complete':
                            $completeProjectContracts = $completeProjectContracts->whereIn("category_id",$allCategoryIds);
                        break;

                        default:
                            abort(404,"Unknown nav.");
                        break;
                    }
                }
            }

            if($request->has('receivable_status') && !($request->receivable_status == null) && !($request->receivable_status == "All") && in_array($request->receivable_status,$receivableStatuses)){
                switch ($request->selected_nav_tab) {
                    case 'Ongoing':
                        $ongoingProjectContracts = $ongoingProjectContracts->where("receivable_status",Str::studly($request->receivable_status));
                    break;

                    case 'Complete':
                        $completeProjectContracts = $completeProjectContracts->where("receivable_status",Str::studly($request->receivable_status));
                    break;

                    default:
                        abort(404,"Unknown nav.");
                    break;
                }
            }

            if($request->has('start_date') && !($request->start_date == null) && $request->has('start_date_condition') && !($request->start_date_condition == null) && in_array($request->start_date_condition, array("=",">","<",">=","<="))){
                switch ($request->selected_nav_tab) {
                    case 'Ongoing':
                        $ongoingProjectContracts = $ongoingProjectContracts->where("start_date",$request->start_date_condition,$request->start_date);
                    break;

                    case 'Complete':
                        $completeProjectContracts = $completeProjectContracts->where("start_date",$request->start_date_condition,$request->start_date);
                    break;

                    default:
                        abort(404,"Unknown nav.");
                    break;
                }
            }

            if($request->has('end_date') && !($request->end_date == null) && $request->has('end_date_condition') && !($request->end_date_condition == null) && in_array($request->end_date_condition, array("=",">","<",">=","<="))){
                switch ($request->selected_nav_tab) {
                    case 'Ongoing':
                        $ongoingProjectContracts = $ongoingProjectContracts->where("start_date",$request->end_date_condition,$request->end_date);
                    break;

                    case 'Complete':
                        $completeProjectContracts = $completeProjectContracts->where("end_date",$request->end_date_condition,$request->end_date);
                    break;

                    default:
                        abort(404,"Unknown nav.");
                    break;
                }
            }

            if($request->has('search') && !($request->search == null)){
                switch ($request->selected_nav_tab) {
                    case 'Ongoing':
                        $ongoingProjectContracts = $ongoingProjectContracts->where("name","like","%".$request->search."%")
                                                                        ->orWhere("description","like","%".$request->search."%")
                                                                        ->orWhere("code","like","%".$request->search."%")
                                                                        ->orWhere("note","like","%".$request->search."%");
                    break;

                    case 'Complete':
                        $completeProjectContracts = $completeProjectContracts->where("name","like","%".$request->search."%")
                                                                        ->orWhere("description","like","%".$request->search."%")
                                                                        ->orWhere("code","like","%".$request->search."%")
                                                                        ->orWhere("note","like","%".$request->search."%");
                    break;

                    default:
                        abort(404,"Unknown nav.");
                    break;
                }
            }
        }

        $completeProjectContracts = $completeProjectContracts->paginate($pagination);
        $ongoingProjectContracts = $ongoingProjectContracts->paginate($pagination);

        return view('internal user.project contract.project contract.index',compact("ongoingProjectContracts","completeProjectContracts","paginations","receivableStatuses","projectContractClients","projectContractCategories"));
    }

    public function create(){
        $statuses = array('Ongoing', 'Upcoming', 'Complete');
        $clients = ProjectContractClient::orderby("name","asc")->get();
        $categories = ProjectContractCategory::tree()->get()->toTree();
        $receivableStatuses = array('Not started', 'Due', 'Partial', 'Full');
        return view('internal user.project contract.project contract.create',compact("statuses","receivableStatuses","clients","categories"));
    }

    public function details($slug){
        $projectContract = ProjectContract::where("slug",$slug)->firstOrFail();
        return view('internal user.project contract.project contract.details',compact("projectContract"));
    }

    public function edit($slug){
        $statusInformation = array("status" => "errors","message" => collect());

        if(!(ProjectContract::where("slug",$slug)->firstOrFail()->status == "Complete")){
            $statuses = array('Ongoing', 'Upcoming', 'Complete');
            $clients = ProjectContractClient::orderby("name","asc")->get();
            $categories = ProjectContractCategory::tree()->get()->toTree();
            $receivableStatuses = array('Not started', 'Due', 'Partial', 'Full');

            $projectContract = ProjectContract::where("slug",$slug)->firstOrFail();

            return view('internal user.project contract.project contract.edit',compact("statuses","receivableStatuses","clients","categories","projectContract"));
        }
        else{
            $statusInformation["status"] = "errors";
            $statusInformation["message"]->push("Can not edit completed project contract.");
            return redirect()->route("project.contract.index")->with([$statusInformation["status"] => $statusInformation["message"]]);
        }

    }

    public function save(Request $request){
        $validator = Validator::make($request->all(),
            [
                'name' => 'required|max:200',
                'code' => 'required|max:200|unique:project_contracts,code',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'note' => 'required',
                'description' => 'nullable',
                'invested_amount' => 'required|numeric|min:0',
                'client' => 'required',
                'category' => 'required',
            ],
            [
                'name.required' => 'Name is required.',
                'name.max' => 'Name length can not greater then 200 chars.',

                'code.required' => 'Code is required.',
                'code.max' => 'Code length can not greater then 200 chars.',
                'code.unique' => 'Code must be unique.',

                'start_date.date' => 'Start date must be a date.',
                'start_date.required' => 'Start date is required.',

                'end_date.date' => 'End date must be a date.',
                'end_date.required' => 'End date is required.',

                'invested_amount.required' => 'Invested amount is required.',
                'invested_amount.min' => 'Invested amount must be at lease 0.',
                'invested_amount.numeric' => 'Invested amount must be unumeric.',

                'client.required' => 'Client is required.',
                'category.required' => 'Category is required.',

                'note.required' => 'Note is required.',
            ]
        );

        $validator->after(function ($validator) {
            $afterValidatorData = $validator->getData();
            $clientFound = ProjectContractClient::where("slug",$afterValidatorData["client"])->count();
            $categoryFound = ProjectContractCategory::where("slug",$afterValidatorData["category"])->count();

            if($clientFound == 0){
                $validator->errors()->add(
                    'client', "Unknown client."
                );
            }

            if($categoryFound == 0){
                $validator->errors()->add(
                    'category', "Unknown category."
                );
            }

            if((array_key_exists('start_date', $afterValidatorData) && !($afterValidatorData["start_date"] == null)) && (array_key_exists('end_date', $afterValidatorData) && !($afterValidatorData["end_date"] == null)) ){

                $endDateToTime = strtotime($afterValidatorData["end_date"]);
                $startDateToTime = strtotime($afterValidatorData["start_date"]);

                if($startDateToTime > $endDateToTime)
                {
                    $validator->errors()->add(
                        'start_date', "Start date can not greater then end date."
                    );
                }

                if($endDateToTime < $startDateToTime)
                {
                    $validator->errors()->add(
                        'end_date', "End date can not less then start date."
                    );
                }
            }

        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $statusInformation = array("status" => "errors","message" => collect());

        LogBatch::startBatch();
            $projectContract = new ProjectContract();
            $projectContract->name = $request->name;
            $projectContract->code = $request->code;
            $projectContract->start_date = $request->start_date;
            $projectContract->end_date = $request->end_date;
            $projectContract->description = $request->description;
            $projectContract->note = array($request->note);
            $projectContract->status = "Ongoing";
            $projectContract->invested_amount = $request->invested_amount;
            $projectContract->receivable_status = "NotStarted";
            $projectContract->client_id = ProjectContractClient::where("slug",$request->client)->firstOrFail()->id;
            $projectContract->category_id = ProjectContractCategory::where("slug",$request->category)->firstOrFail()->id;
            $projectContract->slug = SystemConstant::slugGenerator($request->name,200);
            $projectContract->created_at = Carbon::now();
            $projectContract->created_by_id = Auth::user()->id;
            $projectContract->updated_at = null;
            $saveProjectContract = $projectContract->save();
        LogBatch::endBatch();

        if($saveProjectContract){
            $statusInformation["status"] = "status";
            $statusInformation["message"] = "Successfully created.";
        }
        else{
            $statusInformation["status"] = "errors";
            $statusInformation["message"]->push("Can not update.");
        }

        return redirect()->route("project.contract.index")->with([$statusInformation["status"] => $statusInformation["message"]]);

    }

    public function update(Request $request,$slug){
        $this->projectContractId = ProjectContract::where("slug",$slug)->firstOrFail()->id;

        $validator = Validator::make($request->all(),
            [
                'name' => 'required|max:200',
                'code' => 'required|max:200|unique:project_contracts,code,'.$this->projectContractId,
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'note' => 'required',
                'description' => 'nullable',
                'invested_amount' => 'required|numeric|min:0',
                'client' => 'required',
                'category' => 'required',
            ],
            [
                'name.required' => 'Name is required.',
                'name.max' => 'Name length can not greater then 200 chars.',

                'code.required' => 'Code is required.',
                'code.max' => 'Code length can not greater then 200 chars.',
                'code.unique' => 'Code must be unique.',

                'start_date.date' => 'Start date must be a date.',
                'start_date.required' => 'Start date is required.',

                'end_date.date' => 'End date must be a date.',
                'end_date.required' => 'End date is required.',

                'invested_amount.required' => 'Invested amount is required.',
                'invested_amount.min' => 'Invested amount must be at lease 0.',
                'invested_amount.numeric' => 'Invested amount must be unumeric.',

                'client.required' => 'Client is required.',
                'category.required' => 'Category is required.',

                'note.required' => 'Note is required.',
            ]
        );

        $validator->after(function ($validator) {
            $afterValidatorData = $validator->getData();
            $projectContract = ProjectContract::where("id",$this->projectContractId)->firstOrFail();
            $clientFound = ProjectContractClient::where("slug",$afterValidatorData["client"])->count();
            $categoryFound = ProjectContractCategory::where("slug",$afterValidatorData["category"])->count();

            $currentEndDateToTime = strtotime($projectContract->end_date);
            $currentStartDateToTime = strtotime($projectContract->start_date);

            if($clientFound == 0){
                $validator->errors()->add(
                    'client', "Unknown client."
                );
            }

            if($categoryFound == 0){
                $validator->errors()->add(
                    'category', "Unknown category."
                );
            }

            if((array_key_exists('start_date', $afterValidatorData) && !($afterValidatorData["start_date"] == null)) && (array_key_exists('end_date', $afterValidatorData) && !($afterValidatorData["end_date"] == null)) ){

                $endDateToTime = strtotime($afterValidatorData["end_date"]);
                $startDateToTime = strtotime($afterValidatorData["start_date"]);

                if($endDateToTime < $currentStartDateToTime)
                {
                    $validator->errors()->add(
                        'end_date', "End date can not less then ".$projectContract->start_date."."
                    );
                }

                if($startDateToTime > $currentEndDateToTime)
                {
                    $validator->errors()->add(
                        'start_date', "Start date can not grater then ".$projectContract->end_date."."
                    );
                }

                if($startDateToTime < $currentStartDateToTime)
                {
                    $validator->errors()->add(
                        'start_date', "Start date can not less then ".$projectContract->start_date."."
                    );
                }

                if($startDateToTime > $endDateToTime)
                {
                    $validator->errors()->add(
                        'start_date', "Start date can not greater then end date."
                    );
                }

                if($endDateToTime < $startDateToTime)
                {
                    $validator->errors()->add(
                        'end_date', "End date can not less then start date."
                    );
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $statusInformation = array("status" => "errors","message" => collect());

        if(!(ProjectContract::where("slug",$slug)->firstOrFail()->status == "Complete")){
            LogBatch::startBatch();
                $projectContract = ProjectContract::where("slug",$slug)->firstOrFail();

                $notes = $projectContract->note;
                array_push($notes,$request->note);

                $projectContract->name = $request->name;
                $projectContract->code = $request->code;
                $projectContract->start_date = $request->start_date;
                $projectContract->end_date = $request->end_date;
                $projectContract->description = $request->description;
                $projectContract->note = $notes;
                $projectContract->invested_amount = $request->invested_amount;
                $projectContract->client_id = ProjectContractClient::where("slug",$request->client)->firstOrFail()->id;
                $projectContract->category_id = ProjectContractCategory::where("slug",$request->category)->firstOrFail()->id;
                $projectContract->slug = SystemConstant::slugGenerator($request->name,200);
                $projectContract->updated_at = Carbon::now();
                $updateProjectContract = $projectContract->update();
            LogBatch::endBatch();

            if($updateProjectContract){
                $statusInformation["status"] = "status";
                $statusInformation["message"] = "Successfully updated.";
            }
            else{
                $statusInformation["status"] = "errors";
                $statusInformation["message"] = "Fail to update.";
            }
        }
        else{
            $statusInformation["status"] = "errors";
            $statusInformation["message"]->push("Can not update.");
            $statusInformation["message"]->push("Project contract is completed.");
        }

        return redirect()->route("project.contract.index")->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    public function delete($slug){
        $statusInformation = array("status" => "errors","message" => collect());

        if(!(ProjectContract::where("slug",$slug)->firstOrFail()->status == "Complete")){
            $projectContract = ProjectContract::where("slug",$slug)->firstOrFail();

            if($projectContract->journals->count() > 0){
                //
            }

            if($projectContract->payments->count() > 0){
                //
            }

            $deleteProjectContract =  $projectContract->delete();

            if($deleteProjectContract){
                $statusInformation["status"] = "status";
                $statusInformation["message"]->push("Successfully deleted.");
            }
            else{
                $statusInformation["status"] = "errors";
                $statusInformation["message"]->push("Fail to delete record .");
            }
        }
        else{
            $statusInformation["status"] = "errors";
            $statusInformation["message"]->push("Can not delete the record.");
            $statusInformation["message"]->push("Project contract is completed.");
        }
    }

    public function completeProjectContract($slug){
        $statusInformation = array("status" => "errors","message" => collect());

        if(ProjectContract::where("slug",$slug)->firstOrFail()->status == "Ongoing"){
            $projectContract = ProjectContract::where("slug",$slug)->firstOrFail();

            $projectContract->status = "Complete";
            $projectContract->updated_at = Carbon::now();

            $statusUpdate =  $projectContract->update();

            if($statusUpdate){
                $statusInformation["status"] = "status";
                $statusInformation["message"]->push("Successfully completed.");
            }
            else{
                $statusInformation["status"] = "errors";
                $statusInformation["message"]->push("Fail to complete project contract.");
            }
        }
        else{
            $statusInformation["status"] = "status";
            $statusInformation["message"]->push("Not need to completed.");
            $statusInformation["message"]->push("Project contract is completed.");
        }
        return redirect()->route("project.contract.index")->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    public function startReceivingPayment($slug){
        $statusInformation = array("status" => "errors","message" => collect());

        $currentProjectContract = ProjectContract::where("slug",$slug)->firstOrFail();

        if($currentProjectContract->status == "Complete"){
            if(($currentProjectContract->receivable_status == "NotStarted")){
                $projectContract = ProjectContract::where("slug",$slug)->firstOrFail();
                $projectContract->receivable_status = "Due";
                $projectContract->updated_at = Carbon::now();

                $receivableStatusUpdate =  $projectContract->update();

                if($receivableStatusUpdate){
                    $statusInformation["status"] = "status";
                    $statusInformation["message"]->push("Receiving payment start successfully.");
                }
                else{
                    $statusInformation["status"] = "errors";
                    $statusInformation["message"]->push("Fail to start receiving payment.");
                }
            }
            else{
                $statusInformation["status"] = "status";
                $statusInformation["message"]->push("Payment can be received.");
                $statusInformation["message"]->push("No need to start receiving payment.");
            }
        }
        else{
            $statusInformation["status"] = "errors";
            $statusInformation["message"]->push("Project contract is ongoing.");
            $statusInformation["message"]->push("Can not start receiving payment.");
        }
        return redirect()->route("project.contract.index")->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    public function completeReceivingPayment($slug){
        $statusInformation = array("status" => "errors","message" => collect());

        $currentProjectContract = ProjectContract::where("slug",$slug)->firstOrFail();

        if($currentProjectContract->status == "Complete"){
            if(!($currentProjectContract->receivable_status == "NotStarted")){
                if($currentProjectContract->totalDueAmount() == 0){
                    $projectContract = ProjectContract::where("slug",$slug)->firstOrFail();
                    $projectContract->receivable_status = "Complete";
                    $projectContract->updated_at = Carbon::now();

                    $receivableStatusUpdate =  $projectContract->update();

                    if($receivableStatusUpdate){
                        $statusInformation["status"] = "status";
                        $statusInformation["message"]->push("Receiving payment successfully completed.");
                    }
                    else{
                        $statusInformation["status"] = "errors";
                        $statusInformation["message"]->push("Fail to complete receiving payment.");
                    }
                }
                else{
                    $statusInformation["status"] = "errors";
                    $statusInformation["message"]->push("Fail to complete receiving payment.");
                    $statusInformation["message"]->push("Some due amount are needed to pay.");
                }
            }
            else{
                $statusInformation["status"] = "errors";
                $statusInformation["message"]->push("Fail to complete receiving payment.");
                $statusInformation["message"]->push("Receving payment is not started.");
            }
        }
        else{
            $statusInformation["status"] = "errors";
            $statusInformation["message"]->push("Project contract is ongoing.");
            $statusInformation["message"]->push("Can not start receiving payment.");
        }

        return redirect()->route("project.contract.index")->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

}
