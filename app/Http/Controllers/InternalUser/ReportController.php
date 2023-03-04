<?php

namespace App\Http\Controllers\InternalUser;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\OilAndGasPump;
use App\Models\ProjectContract;
use App\Models\OilAndGasPumpSell;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\OilAndGasPumpSupplier;
use App\Models\OilAndGasPumpPurchase;
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

    public function oilAndGasPumpGetSupplierByOAGP(Request $request){
        $oilAndGasPump = OilAndGasPump::where("slug",$request->oil_and_gas_pump)->firstOrFail();

        $oilAndGasPumpSupplier = OilAndGasPumpSupplier::orderBy("created_at","desc")->orderBy("name","desc");

        return $oilAndGasPumpSupplier->where('oil_and_gas_pump_id',$oilAndGasPump->id)->get();
    }

    public function oilAndGasPumpIndex(Request $request){
        $pagination = 5;
        $generatedReport = false;
        $endDate = Carbon::now();
        $startDate = Carbon::now()->startOfMonth();

        $paginations = array(5,15,30,45,60,75,90,105,120);

        $models = array(
            'PurchaseAll' => 'Purchase (All)',
            'PurchaseDue' => 'Purchase (Due)',
            'PurchaseComplete' => 'Purchase (Complete)',
            'SellAll' => 'Sell (All)',
            'SellDue' => 'Sell (Due)',
            'SellComplete' => 'Sell (Complete)',
        );

        $modelRecords = collect();
        $selectedModel = "None";

        $oilAndGasPumps = OilAndGasPump::orderBy("created_at","desc")->orderBy("name","desc")->get();
        $suppliers = OilAndGasPumpSupplier::orderBy("created_at","desc")->orderBy("name","desc")->get();

        if((count($request->input())) > 0 ){
            $generatedReport = true;

            if($request->has('pagination')){
                $pagination = (in_array($request->pagination,$paginations)) ? $request->pagination : $pagination;
            }

            if ($request->has('model') && !($request->model == null)) {
                $selectedModel = $request->model;
                switch ( Str::studly($request->model)) {
                    case 'PurchaseAll':
                        $modelRecords = OilAndGasPumpPurchase::orderBy("date","desc")->orderBy("id","desc");
                    break;

                    case 'PurchaseDue':
                        $modelRecords = OilAndGasPumpPurchase::orderBy("date","desc")->orderBy("id","desc")->where("status","Due");
                    break;

                    case 'PurchaseComplete':
                        $modelRecords = OilAndGasPumpPurchase::orderBy("date","desc")->orderBy("id","desc")->where("status","Complete");
                    break;

                    case 'SellAll':
                        $modelRecords = OilAndGasPumpSell::orderBy("date","desc")->orderBy("id","desc");
                    break;

                    case 'SellDue':
                        $modelRecords = OilAndGasPumpSell::orderBy("date","desc")->orderBy("id","desc")->where("status","Due");
                    break;

                    case 'SellComplete':
                        $modelRecords = OilAndGasPumpSell::orderBy("date","desc")->orderBy("id","desc")->where("status","Complete");
                    break;

                    default:
                        abort(404);
                    break;
                }
            }
            else{
                abort(404);
            }

            if($request->has('supplier') && !( ($request->supplier == "All") || ($request->supplier == null)) && (strpos($request->model, "Purchase") == true)){
                $supplier = OilAndGasPumpSupplier::where("slug",$request->supplier)->firstOrFail();

                $modelRecords = $modelRecords->where("oagp_supplier_id",$supplier->id);
            }

            if($request->has('oil_and_gas_pump') && !($request->oil_and_gas_pump == null) && !($request->oil_and_gas_pump == "All")){
                $oilAndGasPump = OilAndGasPump::where("slug",$request->oil_and_gas_pump)->firstOrFail();

                $modelRecords = $modelRecords->where("oil_and_gas_pump_id",$oilAndGasPump->id);
            }

            if($request->has('start_date') && !($request->start_date == null)){
                $startDate = ($request->start_date == null) ? $startDate : $request->start_date;
                $modelRecords = $modelRecords->where("date",'>=',date("Y-m-d",strtotime($startDate)));
            }

            if($request->has('end_date') && !($request->end_date == null)){
                $endDate = ($request->start_date == null) ? $endDate : $request->end_date;
                $modelRecords = $modelRecords->where("date",'<=',date("Y-m-d",strtotime($endDate)));
            }

            $modelRecords = $modelRecords->paginate($pagination);
        }

        return view('internal user.report.oil and gas pump.index', compact("modelRecords","paginations",'models',"oilAndGasPumps","suppliers",'selectedModel','generatedReport'));
    }

    public function incomeIndex(Request $request){
        $selectedNavTab = "OilAndGasPump";

        $oagpIncomeReport = collect();
        $pcIncomeReport = collect();

        $endDate = Carbon::now();
        $startDate = Carbon::now();
        $oilAndGasPumps = OilAndGasPump::orderBy("created_at","desc")->orderBy("name","desc")->get();

        if( (count($request->input())) > 0 ){

            if(!in_array($request->selected_nav_tab, array("All","OilAndGasPump","ProjectContract"))){
                abort(404);
            }

            $selectedNavTab = $request->selected_nav_tab;

            if($request->has('start_date') && !($request->start_date == null)){
                $startDate = ($request->start_date == null) ? $startDate : $request->start_date;
            }

            if($request->has('end_date') && !($request->end_date == null)){
                $endDate = ($request->start_date == null) ? $endDate : $request->end_date;
            }

            if($selectedNavTab == "OilAndGasPump"){
                $sOilAndGasPumpSlug = null;
                if( $request->has('oil_and_gas_pump') && ( !($request->oil_and_gas_pump == null) && !($request->oil_and_gas_pump == "All") )){
                    $sOilAndGasPumpSlug = $request->oil_and_gas_pump;
                }

                $oagpIncomeReport = $this->generateOAGPIncomes($startDate,$endDate,$sOilAndGasPumpSlug);
            }

            if($selectedNavTab == "ProjectContract"){
                $pcIncomeReport = $this->generatePCIncomes($startDate,$endDate);
            }

            if($selectedNavTab == "All"){

            }
        }

        return view('internal user.report.income.index',compact("selectedNavTab","oilAndGasPumps","oagpIncomeReport","pcIncomeReport"));
    }

    private function generateOAGPIncomes($startDate,$endDate,$oagpSlug){

        $startDate = date("Y-m-d",strtotime($startDate));
        $endDate = date("Y-m-d",strtotime($endDate));

        $oagpSell = OilAndGasPumpSell::orderBy("created_at","desc");

        if(!($oagpSlug == null)){
            $sOAGP = OilAndGasPump::where("slug",$oagpSlug)->firstOrFail();
            $oagpSell = $oagpSell->where("oil_and_gas_pump_id",$sOAGP->id);
        }

        if(!($startDate == null)){
            $oagpSell = $oagpSell->where('date','>=',$startDate);
        }

        if(!($endDate == null)){
            $oagpSell = $oagpSell->where('date','<=',$endDate);
        }

        $oagpSell = $oagpSell->get()->groupBy(['date',"oil_and_gas_pump_id"]);
        return $oagpSell;
    }

    private function generatePCIncomes($startDate,$endDate){
        $startDate = date("Y-m-d",strtotime($startDate));
        $endDate = date("Y-m-d",strtotime($endDate));

        $pcIncomes = collect();

        $projectContracts = ProjectContract::orderBy("name","asc");

        if(!($startDate == null)){
            $projectContracts =  $projectContracts->where('start_date','>=',$startDate);
            $projectContracts =  $projectContracts->where('end_date','>=',$startDate);
        }

        if(!($endDate == null)){
            $projectContracts =  $projectContracts->where('start_date','<=',$endDate);
            $projectContracts =  $projectContracts->where('end_date','<=',$endDate);
        }

        $pcIncomes = $projectContracts->get()->groupBy(["start_date",'end_date']);
        return  $pcIncomes;
    }
}
