<?php

namespace App\Http\Controllers\InternalUser;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ProjectContract;
use App\Utilities\SystemConstant;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\ProjectContractJournal;
use Spatie\Activitylog\Facades\LogBatch;
use Illuminate\Support\Facades\Validator;

class ProjectContractJournalController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
        $this->middleware(['user.user.permission.check:PCJMP01'])->only(["index"]);
        $this->middleware(['user.user.permission.check:PCJMP02'])->only(["create","save"]);
        $this->middleware(['user.user.permission.check:PCJMP03'])->only(["details"]);
        $this->middleware(['user.user.permission.check:PCJMP04'])->only(["edit","update"]);
        $this->middleware(['user.user.permission.check:PCJMP05'])->only(["delete"]);
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

    public function details ($pcSlug,$slug){
        $projectContract = ProjectContract::where("slug",$pcSlug)->firstOrFail();
        $projectContractJournal = ProjectContractJournal::where("slug",$slug)->firstOrFail();
        return view('internal user.project contract.journal.details',compact("projectContract","projectContractJournal"));
    }

    public function create (Request $request,$pcSlug){
        $statusInformation = array("status" => "errors","message" => collect());

        if(ProjectContract::where("slug",$pcSlug)->firstOrFail()->status == "Ongoing"){
            $projectContract = ProjectContract::where("slug",$pcSlug)->firstOrFail();
            return view('internal user.project contract.journal.create',compact("projectContract"));
        }
        else{
            $statusInformation["status"] = "errors";
            $statusInformation["message"]->push("Project contract is not in ongoing.");
            return redirect()->route("project.contract.journal.index",["pcSlug" => $pcSlug])->with([$statusInformation["status"] => $statusInformation["message"]]);
        }
    }

    public function save(Request $request,$pcSlug){
        $validator = Validator::make($request->all(),
            [
                'name' => 'required|max:200',
                'entry_date' => 'required|date',
                'note' => 'required',
                'description' => 'nullable',
                'entry_type' => 'required|in:Revenue,Loss',
                'amount' => 'required|numeric|min:0',

            ],
            [
                'name.required' => 'Name is required.',
                'name.max' => 'Name length can not greater then 200 chars.',

                'entry_date.required' => 'Entry date is required.',
                'entry_date.date' => 'Entry date must be a date.',

                'entry_type.required' => 'Entry type is required.',
                'entry_type.in' => 'Entry type must be one out of [Revenue,Loss].',

                'amount.required' => 'Amount is required.',
                'amount.min' => 'Amount must be at lease 0.',
                'amount.numeric' => 'Amount must be unumeric.',

                'note.required' => 'Note is required.',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $statusInformation = array("status" => "errors","message" => collect());

        if(ProjectContract::where("slug",$pcSlug)->firstOrFail()->status == "Ongoing"){
            LogBatch::startBatch();
                $projectContractJournal = new ProjectContractJournal();
                $projectContractJournal->name = $request->name;
                $projectContractJournal->entry_date = $request->entry_date ;
                $projectContractJournal->description = $request->description;
                $projectContractJournal->note = array($request->note);
                $projectContractJournal->entry_type = $request->entry_type;
                $projectContractJournal->amount = $request->amount;
                $projectContractJournal->project_contract_id = ProjectContract::where("slug",$pcSlug)->firstOrFail()->id;
                $projectContractJournal->slug = SystemConstant::slugGenerator($request->name,200);
                $projectContractJournal->created_at = Carbon::now();
                $projectContractJournal->created_by_id = Auth::user()->id;
                $projectContractJournal->updated_at = null;
                $saveProjectContractJournal = $projectContractJournal->save();
            LogBatch::endBatch();

            if($saveProjectContractJournal){
                $statusInformation["status"] = "status";
                $statusInformation["message"] = "Journal entry successfully created.";
            }
            else{
                $statusInformation["status"] = "errors";
                $statusInformation["message"] = "Fail to create journal entry.";
            }
        }
        else{
            $statusInformation["status"] = "errors";
            $statusInformation["message"]->push("Project contract is not in ongoing.");
        }

        return redirect()->route("project.contract.journal.index",["pcSlug" => $pcSlug])->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    public function delete($pcSlug,$slug){
        $statusInformation = array("status" => "errors","message" => collect());

        if(ProjectContract::where("slug",$pcSlug)->firstOrFail()->status == "Ongoing"){
            $projectContractJournal = ProjectContractJournal::where("slug",$slug)->firstOrFail();
            $deleteProjectContractJournal = $projectContractJournal->delete();

            if($deleteProjectContractJournal){
                $statusInformation["status"] = "status";
                $statusInformation["message"]->push("Journal entry successfully deleted.");
            }
            else{
                $statusInformation["status"] = "errors";
                $statusInformation["message"]->push("Fail to delete journal entry.");
            }
        }
        else{
            $statusInformation["status"] = "errors";
            $statusInformation["message"]->push("Project contract is not in ongoing.");
        }

        return redirect()->route("project.contract.journal.index",["pcSlug" => $pcSlug])->with([$statusInformation["status"] => $statusInformation["message"]]);
    }
}
