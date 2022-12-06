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
    public function __construct()
    {
        $this->middleware(['auth','verified']);
        $this->middleware(['user.user.permission.check:PCMP01'])->only(["index"]);
        $this->middleware(['user.user.permission.check:PCMP02'])->only(["create","save"]);
        $this->middleware(['user.user.permission.check:PCMP03'])->only(["details"]);
        $this->middleware(['user.user.permission.check:PCMP04'])->only(["edit","update"]);
    }

    public function index(Request $request){
        $pagination = 5;
        $paginations = array(5,15,30,45,60,75,90,105,120);
        $receivableStatuses = array('Not started', 'Due', 'Partial', 'Full');
        $projectContractClients = ProjectContractClient::orderby("name","asc")->get();
        $projectContractCategories = ProjectContractCategory::tree()->get()->toTree();

        $ongoingProjectContracts = ProjectContract::orderby("created_at","desc")->orderby("name","asc")->where("status","Ongoing");
        $upcomingProjectContracts = ProjectContract::orderby("created_at","desc")->orderby("name","asc")->where("status","Upcoming");
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

                        case 'Upcoming':
                            $upcomingProjectContracts = $upcomingProjectContracts->where("client_id",$projectContractClient->id);
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

                        case 'Upcoming':
                            $upcomingProjectContracts = $upcomingProjectContracts->whereIn("category_id",$allCategoryIds);
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

                    case 'Upcoming':
                        $upcomingProjectContracts = $upcomingProjectContracts->where("receivable_status",Str::studly($request->receivable_status));
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

                    case 'Upcoming':
                        $upcomingProjectContracts = $upcomingProjectContracts->where("start_date",$request->start_date_condition,$request->start_date);
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

                    case 'Upcoming':
                        $upcomingProjectContracts = $upcomingProjectContracts->where("end_date",$request->end_date_condition,$request->end_date);
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

                    case 'Upcoming':
                        $upcomingProjectContracts = $upcomingProjectContracts->where("name","like","%".$request->search."%")
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

        $upcomingProjectContracts = $upcomingProjectContracts->paginate($pagination);
        $completeProjectContracts = $completeProjectContracts->paginate($pagination);
        $ongoingProjectContracts = $ongoingProjectContracts->paginate($pagination);

        return view('internal user.project contract.project contract.index',compact("ongoingProjectContracts","upcomingProjectContracts","completeProjectContracts","paginations","receivableStatuses","projectContractClients","projectContractCategories"));
    }

    public function create(){
        $statuses = array('Ongoing', 'Upcoming', 'Complete');
        $receivableStatuses = array('Not started', 'Due', 'Partial', 'Full');
        $clients = ProjectContractClient::orderby("name","asc")->get();
        $categories = ProjectContractCategory::tree()->get()->toTree();
        return view('internal user.project contract.project contract.create',compact("statuses","receivableStatuses","clients","categories"));
    }

    public function details($slug){
        $projectContract = ProjectContract::where("slug",$slug)->firstOrFail();
        return view('internal user.project contract.project contract.details',compact("projectContract"));
    }


    public function save(Request $request){
        $validator = Validator::make($request->all(),
            [
                'name' => 'required|max:200',
                'code' => 'required|max:200|unique:project_contracts,code',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date',
                'note' => 'nullable',
                'description' => 'nullable',
                'status' => 'required|in:Ongoing,Upcoming,Complete',
                'invested_amount' => 'required|numeric|min:0',
                'receivable_status' => 'required|in:NotStarted,Due,Partial,Full',
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
                'end_date.date' => 'End date must be a date.',

                'status.required' => 'Status is required.',
                'status.in' => 'Status must be one out of [Ongoing,Upcoming,Complete].',

                'invested_amount.required' => 'Invested amount is required.',
                'invested_amount.min' => 'Invested amount must be at lease 0.',
                'invested_amount.numeric' => 'Invested amount must be unumeric.',

                'receivable_status.required' => 'Receivable status is required.',
                'receivable_status.in' => 'Receivable status must be one out of [Not started,Due,Partial,Full].',

                'client.required' => 'Client is required.',
                'category.required' => 'Category is required.',
            ]
        );

        $validator->after(function ($validator) {
            $afterValidatorData = $validator->getData();
            $currentDate = Carbon::now();;
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

            if(array_key_exists('end_date', $afterValidatorData) && !($afterValidatorData["end_date"] == null) ){

                $endDateToTime = strtotime($afterValidatorData["end_date"]);

                if( array_key_exists('start_date', $afterValidatorData)  && !($afterValidatorData["start_date"] == null )){

                    $startDateToTime = strtotime($afterValidatorData["start_date"]);

                    if($startDateToTime > $endDateToTime){
                        $validator->errors()->add(
                            'start_date', "Start date can not greater then end date."
                        );
                    }

                    if( $endDateToTime < $startDateToTime){
                        $validator->errors()->add(
                            'end_date', "Start date is can not less then end date."
                        );
                    }

                    if(!( ($startDateToTime == $endDateToTime) || ($startDateToTime < $endDateToTime))){
                        $validator->errors()->add(
                            'start_date', "Incorrect start date."
                        );

                        $validator->errors()->add(
                            'end_date', "Incorrect end date."
                        );
                    }
                }
                else{
                    $validator->errors()->add(
                        'start_date', "Start date is required."
                    );
                }
            }

            if(array_key_exists('start_date', $afterValidatorData) && !($afterValidatorData["start_date"] == null)){
                $currentDateToTime = strtotime($currentDate);
                $startDateToTime = strtotime($afterValidatorData["start_date"]);

                if($startDateToTime <= $currentDateToTime){

                    if(in_array($afterValidatorData["status"],array("Ongoing","Complete")) == false){
                        $validator->errors()->add(
                            'status', "Incorrect status for date range. Status must be one out of [Ongoing,Complete]"
                        );
                    }

                    if(array_key_exists('end_date', $afterValidatorData) && !($afterValidatorData["end_date"] == null)){
                        $endDateToTime = strtotime($afterValidatorData["end_date"]);

                        if(($endDateToTime == $currentDateToTime) || ($endDateToTime > $currentDateToTime)){
                            if($endDateToTime == $currentDateToTime){
                                if(in_array($afterValidatorData["status"],array("Ongoing","Complete")) == false){
                                    $validator->errors()->add(
                                        'status', "Incorrect status for date range. Status must be one out of [Ongoing,Complete]."
                                    );
                                }
                            }
                            else{
                                if(!($afterValidatorData["status"] == "Ongoing")){
                                    $validator->errors()->add(
                                        'status', "Incorrect status for date range. Status must be 'Ongoing'."
                                    );
                                }
                            }
                        }
                        else{
                            if(!($afterValidatorData["status"] == "Complete")){
                                $validator->errors()->add(
                                    'status', "Incorrect status for date range. Status must be 'Complete'."
                                );
                            }
                        }


                    }
                }
                else{
                    if(!($afterValidatorData["status"] == "Upcoming")){
                        $validator->errors()->add(
                            'status', "Incorrect status for date range. Status must be 'Upcoming'."
                        );
                    }
                }
            }

            if(in_array($afterValidatorData["status"],array("Ongoing","Upcoming"))){
                if(!($afterValidatorData["receivable_status"] == "NotStarted")){
                    $validator->errors()->add(
                        'receivable_status', "Incorrect receivable status for status. Receivable status must be 'Not started'."
                    );
                }
            }

            if($afterValidatorData["status"] == "Complete"){
                if(in_array($afterValidatorData["receivable_status"],array("NotStarted","Due","Partial","Full")) == false){
                    $validator->errors()->add(
                        'receivable_status', "Incorrect receivable status for status. Receivable status must be one out of [Not started,Due,Partial,Full]"
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
            $projectContract->note = $request->note;
            $projectContract->status = $request->status;
            $projectContract->invested_amount = $request->invested_amount;
            $projectContract->receivable_status = $request->receivable_status;
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
            $statusInformation["message"] = "Project contract successfully created.";
        }
        else{
            $statusInformation["status"] = "errors";
            $statusInformation["message"] = "Fail to create project contract.";
        }

        return redirect()->route("project.contract.index")->with([$statusInformation["status"] => $statusInformation["message"]]);

    }
}
