<?php

namespace App\Http\Controllers\InternalUser;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ProjectContract;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\ProjectContractClient;
use App\Models\ProjectContractCategory;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function projectContractIndex(Request $request)
    {
        $pagination = 5;
        $paginations = array(5,15,30,45,60,75,90,105,120);
        $statuses = array('Ongoing', 'Complete');
        $receivableStatuses = array('Not started', 'Due', 'Partial', 'Complete');
        $projectContractClients = ProjectContractClient::orderby("name","asc")->get();
        $projectContractCategories = ProjectContractCategory::tree()->get()->toTree();

        $projectContracts = collect();

        if(count($request->input()) > 0){
            $projectContracts = ProjectContract::orderBy("created_at","desc")->orderBy("name","asc");

            if($request->has('pagination')){
                $pagination = (in_array($request->pagination,$paginations)) ? $request->pagination : $pagination;
            }

            if($request->has('category') && !($request->category == null) && !($request->category == "All")){
                $projectContractCategory = ProjectContractCategory::where("slug",$request->category)->first();

                if($projectContractCategory){
                    $allCategoryIds =  $projectContractCategory->descendants()->pluck("id")->toArray();
                    array_push($allCategoryIds,$projectContractCategory->id);

                    $projectContracts = $projectContracts->whereIn("category_id",$allCategoryIds);
                }
            }

            if($request->has('client') && !($request->client == null) && !($request->client == "All")){
                $projectContractClient = ProjectContractClient::where("slug",$request->client)->first();

                if($projectContractClient){
                    $projectContracts = $projectContracts->whereIn("client_id",$projectContractClient->id);
                }
            }

            if($request->has('status') && !($request->status == null) && (in_array($request->status,$statuses) == true)){
                $projectContracts = $projectContracts->where("status",Str::studly($request->status));
            }

            if($request->has('receivable_status') && !($request->receivable_status == null) && (in_array($request->receivable_status,$receivableStatuses) == true)){
                $projectContracts = $projectContracts->where("receivable_status",Str::studly($request->receivable_status));
            }


            if( ($request->has('start_date') && !($request->start_date == null))){
                $projectContracts = $projectContracts->where(DB::raw("(STR_TO_DATE(start_date,'%Y-%m-%d'))"),'>=', $request->start_date->format('Y-m-d'))
                                                    ->where(DB::raw("(STR_TO_DATE(end_date,'%Y-%m-%d'))"),'>=', $request->start_date->format('Y-m-d'));
            }

            if(($request->has('end_date') && !($request->end_date == null))){
                $projectContracts = $projectContracts->where(DB::raw("(STR_TO_DATE(start_date,'%Y-%m-%d'))"),'<=', $request->end_date->format('Y-m-d'))
                                                    ->where(DB::raw("(STR_TO_DATE(end_date,'%Y-%m-%d'))"),'<=', $request->end_date->format('Y-m-d'));
            }

            if($request->has('search') && !($request->search == null)){
                $projectContracts = $projectContracts->where("name","like","%".$request->search."%")
                                                                        ->orWhere("description","like","%".$request->search."%")
                                                                        ->orWhere("code","like","%".$request->search."%")
                                                                        ->orWhere("note","like","%".$request->search."%");
            }
            $projectContracts = $projectContracts->paginate($pagination);
        }

        return view('internal user.report.project contract.index', compact('projectContracts',"paginations","statuses","receivableStatuses","projectContractCategories","projectContractClients"));
    }

    public function projectContractDetails($slug)
    {
        $projectContract = ProjectContract::where("slug",$slug)->firstOrFail();
        return view('internal user.report.project contract.details', compact('projectContract'));
    }
}
