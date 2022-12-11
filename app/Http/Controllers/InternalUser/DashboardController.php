<?php

namespace App\Http\Controllers\InternalUser;

use Illuminate\Http\Request;
use App\Models\ProjectContract;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\ProjectContractJournal;
use App\Models\ProjectContractPayment;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $currentDate = now();
        $startDate = $currentDate->startOfMonth()->format('Y-m-d');
        $endDate = $currentDate->endOfMonth()->format('Y-m-d');

        $ongoingPC = ProjectContract::where("status","Ongoing")
                                                        ->where(DB::raw("(STR_TO_DATE(start_date,'%Y-%m-%d'))"),'>=', $startDate)
                                                        ->where(DB::raw("(STR_TO_DATE(start_date,'%Y-%m-%d'))"),'<=', $endDate);

        $ongoingPCCount = $ongoingPC->count();
        $ongoingPCInvestedAmount = $ongoingPC->sum("invested_amount");
        $ongoingPCPCJEntryCount = ProjectContractJournal::whereIn("project_contract_id",$ongoingPC->pluck("id"))->count();
        $ongoingPCPCJRevenueEntryCount = ProjectContractJournal::whereIn("project_contract_id",$ongoingPC->pluck("id"))->where("entry_type","Revenue")->count();
        $ongoingPCPCJLossEntryCount = ProjectContractJournal::whereIn("project_contract_id",$ongoingPC->pluck("id"))->where("entry_type","Loss")->count();
        $ongoingPCPCJRevenueAmount = ProjectContractJournal::whereIn("project_contract_id",$ongoingPC->pluck("id"))->where("entry_type","Revenue")->sum("amount");
        $ongoingPCPCJLossAmount = ProjectContractJournal::whereIn("project_contract_id",$ongoingPC->pluck("id"))->where("entry_type","Loss")->sum("amount");


        $completePC = ProjectContract::where("status","Complete")
                                        ->where(DB::raw("(STR_TO_DATE(start_date,'%Y-%m-%d'))"),'>=', $startDate)
                                        ->where(DB::raw("(STR_TO_DATE(start_date,'%Y-%m-%d'))"),'<=', $endDate);
        $completePCPCJ =  ProjectContractJournal::whereIn("project_contract_id",$completePC->pluck("id"));
        $completePCPCP =  ProjectContractPayment::whereIn("project_contract_id",$completePC->pluck("id"));

        $completePCCount = $completePC->count();
        $completePCInvested = $completePC->sum("invested_amount");
        $completePCPCJEntryCount = ProjectContractJournal::whereIn("project_contract_id",$completePC->pluck("id"))->count();
        $completeTotalPCPCPCount = ProjectContractPayment::whereIn("project_contract_id",$completePC->pluck("id"))->count();
        $completePCPCJLossEntryCount = ProjectContractJournal::whereIn("project_contract_id",$completePC->pluck("id"))->where("entry_type","Loss")->count();
        $completePCPCJRevenueEntryCount = ProjectContractJournal::whereIn("project_contract_id",$completePC->pluck("id"))->where("entry_type","Revenue")->count();
        $completePCPCJRevenueAmount = ProjectContractJournal::whereIn("project_contract_id",$completePC->pluck("id"))->where("entry_type","Revenue")->sum("amount");
        $completePCPCJLossAmount = ProjectContractJournal::whereIn("project_contract_id",$completePC->pluck("id"))->where("entry_type","Loss")->sum("amount");
        $completePCReceivableAmount = (($completePCInvested + $completePCPCJRevenueAmount) - $completePCPCJLossAmount);
        $completePCReceiveAmount = ProjectContractPayment::whereIn("project_contract_id",$completePC->pluck("id"))->sum("amount");


        $ongoingProjectContractQuickView = array(
            "projectContract" => $ongoingPCCount,
            "journalEntry" => $ongoingPCPCJEntryCount,
            "journalRevenueEntry" => $ongoingPCPCJRevenueEntryCount,
            "journalLossEntry" => $ongoingPCPCJLossEntryCount,
            "investedAmount" => $ongoingPCInvestedAmount,
            "revenueAmount" => $ongoingPCPCJRevenueAmount,
            "lossAmount" => $ongoingPCPCJLossAmount,
            "receivableAmount" => (($ongoingPCInvestedAmount + $ongoingPCPCJRevenueAmount) - $ongoingPCPCJLossAmount),
        );

        $completeProjectContractQuickView = array(
            "projectContract" => $completePCCount,
            "journalEntry" => $completePCPCJEntryCount,
            "payment" =>  $completeTotalPCPCPCount,
            "journalRevenueEntry" => $completePCPCJRevenueEntryCount,
            "journalLossEntry" => $completePCPCJLossEntryCount,
            "investedAmount" => $completePCInvested,
            "revenueAmount" => $completePCPCJRevenueAmount,
            "lossAmount" => $completePCPCJLossAmount,
            "receivableAmount" => $completePCReceivableAmount,
            "receiveAmount" => $completePCReceiveAmount,
            "dueAmount" => $completePCReceivableAmount - $completePCReceiveAmount,
        );

        return view('internal user.dashboard.index',compact("ongoingProjectContractQuickView","completeProjectContractQuickView"));
    }
}
