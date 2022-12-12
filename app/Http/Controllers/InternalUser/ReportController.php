<?php

namespace App\Http\Controllers\InternalUser;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ProjectContract;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\ProjectContractJournal;
use App\Models\ProjectContractPayment;
use App\Models\ProjectContractCategory;
use App\Models\ProjectContractPaymentMethod;

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

        return view('internal user.report.project contract.index', compact('projectContracts',"paginations","statuses","receivableStatuses","projectContractCategories"));
    }

    public function projectContractDetails($slug)
    {
        $projectContract = ProjectContract::where("slug",$slug)->firstOrFail();
        return view('internal user.report.project contract.details', compact('projectContract'));
    }

    public function projectContractJournalIndex(Request $request){
        $pagination = 5;
        $entryTypes = array('Revenue', 'Loss');
        $statuses = array('Ongoing', 'Complete');
        $paginations = array(5,15,30,45,60,75,90,105,120);
        $receivableStatuses = array('Not started', 'Due', 'Partial', 'Complete');

        $projectContractJournalEntries = collect();

        if(count($request->input()) > 0){
            $projectContractIds = array();

            $projectContractJournalEntries = ProjectContractJournal::orderBy("created_at","desc")->orderBy("name","asc");

            if($request->has('pagination')){
                $pagination = (in_array($request->pagination,$paginations)) ? $request->pagination : $pagination;
            }

            if($request->has('entry_type') && !($request->entry_type == null) && !($request->entry_type == "All")){
                $projectContractJournalEntries = $projectContractJournalEntries->where("entry_type",Str::studly($request->entry_type));
            }

            if($request->has('entry_date') && !($request->entry_date == null) && $request->has('entry_date_condition') && !($request->entry_date_condition == null) && in_array($request->entry_date_condition, array("=",">","<",">=","<="))){
                $projectContractJournalEntries = $projectContractJournalEntries->where(DB::raw("(STR_TO_DATE(entry_date,'%Y-%m-%d'))"),$request->entry_date_condition,date('Y-m-d',strtotime($request->entry_date)) );
            }

            if($request->has('search') && !($request->search == null)){
                $projectContractJournalEntries = $projectContractJournalEntries->where("name","like","%".$request->search."%")
                                                                        ->orWhere("description","like","%".$request->search."%")
                                                                        ->orWhere("code","like","%".$request->search."%")
                                                                        ->orWhere("note","like","%".$request->search."%");
            }

            $projectContractJournalEntries = $projectContractJournalEntries->paginate($pagination);
        }

        return view('internal user.report.project contract journal.index', compact("projectContractJournalEntries","paginations",'entryTypes'));
    }

    public function projectContractJournalDetails($slug)
    {
        $projectContractJournalEntry = ProjectContractJournal::where("slug",$slug)->firstOrFail();
        return view('internal user.report.project contract journal.details', compact('projectContractJournalEntry'));
    }

    public function projectContractPaymentIndex(Request $request){
        $pagination = 5;
        $paginations = array(5,15,30,45,60,75,90,105,120);
        $projectContractPaymentMethods = ProjectContractPaymentMethod::orderby("name","asc")->get();

        $projectContractPayments = collect();

        if(count($request->input()) > 0){
            $projectContractPayments = ProjectContractPayment::orderby("created_at","desc")->orderby("name","asc");

            if($request->has('pagination')){
                $pagination = (in_array($request->pagination,$paginations)) ? $request->pagination : $pagination;
            }

            if($request->has('payment_date') && !($request->payment_date == null) && $request->has('payment_date_condition') && !($request->payment_date_condition == null) && in_array($request->payment_date_condition, array("=",">","<",">=","<="))){
                $projectContractPayments = $projectContractPayments->where(DB::raw("(STR_TO_DATE(payment_date,'%Y-%m-%d'))"),$request->payment_date_condition,date('Y-m-d',strtotime($request->payment_date)) );
            }

            if($request->has('payment_method') && !($request->payment_method == null) && !($request->payment_method == "All") ){
                $projectContractPayments = $projectContractPayments->where('payment_method_id', ProjectContractPaymentMethod::where("slug",$request->payment_method)->firstOrFail()->id );
            }

            if($request->has('search') && !($request->search == null)){
                $projectContractPayments = $projectContractPayments->where("name","like","%".$request->search."%")
                                                                    ->orWhere("description","like","%".$request->search."%");
            }

            $projectContractPayments = $projectContractPayments->paginate($pagination);
        }

        return view('internal user.report.project contract payment.index', compact("projectContractPayments","paginations",'projectContractPaymentMethods'));
    }

    public function projectContractPaymentDetails($slug)
    {
        $projectContractPayment = ProjectContractPayment::where("slug",$slug)->firstOrFail();
        return view('internal user.report.project contract payment.details', compact('projectContractPayment'));
    }
}
