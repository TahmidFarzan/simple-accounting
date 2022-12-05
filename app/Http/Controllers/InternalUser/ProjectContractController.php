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
}
