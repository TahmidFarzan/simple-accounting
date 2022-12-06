<?php

namespace App\Http\Controllers\InternalUser;

use Illuminate\Http\Request;
use App\Models\ProjectContract;
use App\Http\Controllers\Controller;
use App\Models\ProjectContractJournal;

class ProjectContractJournalController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
        $this->middleware(['user.user.permission.check:PCJMP01'])->only(["index"]);
    }

    public function index(Request $request,$pcSlug){
        $pagination = 5;
        $paginations = array(5,15,30,45,60,75,90,105,120);
        $projectContract = ProjectContract::where("slug",$pcSlug)->firstOrFail();

        $projectContractJournalRevenueEntries = ProjectContractJournal::orderby("created_at","desc")->orderby("name","asc")->where("project_contract_id",$projectContract->id)->where("entry_type","Revenue");
        $projectContractJournalLossEntries = ProjectContractJournal::orderby("created_at","desc")->orderby("name","asc")->where("project_contract_id",$projectContract->id)->where("entry_type","Loss");

        if(count($request->input()) > 0){
            if($request->has('pagination')){
                $pagination = (in_array($request->pagination,$paginations)) ? $request->pagination : $pagination;
            }

            if($request->has('entry_date') && !($request->entry_date == null) && $request->has('entry_date_condition') && !($request->entry_date_condition == null) && in_array($request->entry_date_condition, array("=",">","<",">=","<="))){
                switch ($request->selected_nav_tab) {
                    case 'Revenue':
                        $projectContractJournalRevenueEntries = $projectContractJournalRevenueEntries->where("entry_date",$request->entry_date_condition,$request->entry_date);
                    break;

                    case 'Loss':
                        $projectContractJournalLossEntries = $projectContractJournalLossEntries->where("entry_date",$request->entry_date_condition,$request->entry_date);
                    break;

                    default:
                        abort(404,"Unknown nav.");
                    break;
                }
            }

            if($request->has('search') && !($request->search == null)){
                switch ($request->selected_nav_tab) {
                    case 'Revenue':
                        $projectContractJournalRevenueEntries = $projectContractJournalRevenueEntries->where("name","like","%".$request->search."%")
                                                                        ->orWhere("description","like","%".$request->search."%");
                    break;

                    case 'Loss':
                        $projectContractJournalLossEntries = $projectContractJournalLossEntries->where("name","like","%".$request->search."%")
                                                                        ->orWhere("description","like","%".$request->search."%");
                    break;

                    default:
                        abort(404,"Unknown nav.");
                    break;
                }
            }
        }

        $projectContractJournalLossEntries = $projectContractJournalLossEntries->paginate($pagination);
        $projectContractJournalRevenueEntries = $projectContractJournalRevenueEntries->paginate($pagination);

        return view('internal user.project contract.journal.index',compact("projectContractJournalRevenueEntries","projectContractJournalLossEntries","projectContract","paginations"));
    }
}
