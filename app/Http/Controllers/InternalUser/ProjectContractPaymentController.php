<?php

namespace App\Http\Controllers\InternalUser;

use Illuminate\Http\Request;
use App\Models\ProjectContract;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\ProjectContractPayment;

class ProjectContractPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
        $this->middleware(['user.user.permission.check:PCPMP01'])->only(["index"]);
        $this->middleware(['user.user.permission.check:PCPMP02'])->only(["create","save"]);
        $this->middleware(['user.user.permission.check:PCPMP03'])->only(["details"]);
        $this->middleware(['user.user.permission.check:PCPMP04'])->only(["edit","update"]);
        $this->middleware(['user.user.permission.check:PCPMP05'])->only(["delete"]);
    }

    public function index(Request $request,$pcSlug){
        $pagination = 5;
        $paginations = array(5,15,30,45,60,75,90,105,120);
        $projectContract = ProjectContract::where("slug",$pcSlug)->firstOrFail();

        $projectContractPayments = ProjectContractPayment::orderby("created_at","desc")->orderby("name","asc")->where("project_contract_id",$projectContract->id);

        if(count($request->input()) > 0){
            if($request->has('pagination')){
                $pagination = (in_array($request->pagination,$paginations)) ? $request->pagination : $pagination;
            }

            if($request->has('payment_date') && !($request->payment_date == null) && $request->has('payment_date_condition') && !($request->payment_date_condition == null) && in_array($request->payment_date_condition, array("=",">","<",">=","<="))){
                $projectContractPayments = $projectContractPayments->where(DB::raw("(STR_TO_DATE(payment_date,'%Y-%m-%d'))"),$request->payment_date_condition,date('Y-m-d',strtotime($request->payment_date)) );
            }

            if($request->has('search') && !($request->search == null)){
                $projectContractPayments = $projectContractPayments->where("name","like","%".$request->search."%")
                                                                        ->orWhere("description","like","%".$request->search."%");
            }
        }

        $projectContractPayments = $projectContractPayments->paginate($pagination);

        return view('internal user.project contract.payment.index',compact("projectContractPayments","projectContract","paginations"));
    }

    public function details ($pcSlug,$slug){
        $projectContract = ProjectContract::where("slug",$pcSlug)->firstOrFail();
        $projectContractPayment = ProjectContractPayment::where("slug",$slug)->firstOrFail();
        return view('internal user.project contract.payment.details',compact("projectContract","projectContractPayment"));
    }

}
