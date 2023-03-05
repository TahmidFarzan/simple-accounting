<?php

namespace App\Http\Controllers\InternalUser;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\ProjectContract;
use App\Models\OilAndGasPumpSell;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\OilAndGasPumpPurchase;
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
        $currentDate = Carbon::now();
        $oagpSellCMQuickInfo = $this->oagpSellQuickInfo("CurrentMonth");
        $oagpSellCWQuickInfo = $this->oagpSellQuickInfo("CurrentWeek");

        $oagpPurchaseCMQuickInfo = $this->oagpPurchaseQuickInfo("CurrentMonth");
        $oagpPurchaseCWQuickInfo = $this->oagpPurchaseQuickInfo("CurrentWeek");
        $pcCMQuickInfo = $this->pcQuickInfo("CurrentMonth");
        $pcCWQuickInfo = $this->pcQuickInfo("CurrentWeek");

        return view('internal user.dashboard.index',compact("oagpSellCMQuickInfo","oagpSellCWQuickInfo","oagpPurchaseCMQuickInfo","oagpPurchaseCWQuickInfo","pcCMQuickInfo","pcCWQuickInfo"));
    }

    private function oagpSellQuickInfo($frequency){
        $totalPrice = 0;
        $totalIncome = 0;
        $totalDue = 0;
        $totalPaid = 0;
        $totalDuePaymentCount = 0;
        $totalPayable = 0;
        $totalCompletePaymentCount = 0;

        $startDate = Carbon::now();
        $endDate = Carbon::now();

        if($frequency == "CurrentMonth"){
            $startDate =  Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        }
        if($frequency == "CurrentWeek"){
            $startDate =  Carbon::now()->startOfWeek();
            $endDate = Carbon::now()->endOfWeek();
        }

        $startDate = date("Y-m-d",strtotime($startDate));
        $endDate = date("Y-m-d",strtotime($endDate));

        $sellquickinfo = array();
        $oagpSells = OilAndGasPumpSell::orderBy("created_at","desc")->orderBy("id","desc");

        if(!($startDate == null)){
            $oagpSells = $oagpSells->where("date",'>=',$startDate);
        }

        if(!($endDate == null)){
            $oagpSells = $oagpSells->where('date','<=',$endDate);
        }
        $oagpSells = $oagpSells->get();

        foreach($oagpSells as $oagpSell){
            $totalPrice += $oagpSell->totalPrice();
            $totalIncome += $oagpSell->totalIncome();
            $totalDue += $oagpSell->totalDue();
            $totalPaid += $oagpSell->totalPaid();
            $totalPayable += $oagpSell->totalPayable();

            if($oagpSell->status == "Due"){
                $totalDuePaymentCount += 1;
            }

            if($oagpSell->status == "Complete"){
                $totalCompletePaymentCount += 1;
            }
        }

        $sellquickinfo["total_price"] = $totalPrice;
        $sellquickinfo["total_income"] = $totalIncome;
        $sellquickinfo["total_due_amount"] = $totalDue;
        $sellquickinfo["total_paid_amount"] = $totalPaid;
        $sellquickinfo["total_payable_amount"] = $totalPayable;
        $sellquickinfo["total_due_payment_count"] = $totalDuePaymentCount;
        $sellquickinfo["total_complete_payment_count"] = $totalCompletePaymentCount;

        return $sellquickinfo;
    }


    private function oagpPurchaseQuickInfo($frequency){
        $totalPrice = 0;
        $totalDue = 0;
        $totalPaid = 0;
        $totalDuePaymentCount = 0;
        $totalPayable = 0;
        $totalCompletePaymentCount = 0;

        $startDate = Carbon::now();
        $endDate = Carbon::now();

        if($frequency == "CurrentMonth"){
            $startDate =  Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        }
        if($frequency == "CurrentWeek"){
            $startDate =  Carbon::now()->startOfWeek();
            $endDate = Carbon::now()->endOfWeek();
        }

        $startDate = date("Y-m-d",strtotime($startDate));
        $endDate = date("Y-m-d",strtotime($endDate));

        $purchaseQuickInfo = array();
        $oagpPurchases = OilAndGasPumpPurchase::orderBy("created_at","desc")->orderBy("id","desc");

        if(!($startDate == null)){
            $oagpPurchases = $oagpPurchases->where("date",'>=',$startDate);
        }

        if(!($endDate == null)){
            $oagpPurchases = $oagpPurchases->where('date','<=',$endDate);
        }
        $oagpPurchases = $oagpPurchases->get();

        foreach($oagpPurchases as $oagpPurchase){
            $totalPrice += $oagpPurchase->totalPrice();
            $totalDue += $oagpPurchase->totalDue();
            $totalPaid += $oagpPurchase->totalPaid();
            $totalPayable += $oagpPurchase->totalPayable();

            if($oagpPurchase->status == "Due"){
                $totalDuePaymentCount += 1;
            }

            if($oagpPurchase->status == "Complete"){
                $totalCompletePaymentCount += 1;
            }
        }

        $purchaseQuickInfo["total_price"] = $totalPrice;
        $purchaseQuickInfo["total_due_amount"] = $totalDue;
        $purchaseQuickInfo["total_paid_amount"] = $totalPaid;
        $purchaseQuickInfo["total_payable_amount"] = $totalPayable;
        $purchaseQuickInfo["total_due_payment_count"] = $totalDuePaymentCount;
        $purchaseQuickInfo["total_complete_payment_count"] = $totalCompletePaymentCount;

        return $purchaseQuickInfo;
    }

    private function pcQuickInfo($frequency){
        $totalDue = 0;
        $totalLoss = 0;
        $totalIncome = 0;
        $totalReceive = 0;
        $totalRevenue = 0;
        $totalReceivable = 0;

        $totalOngingPC = 0;
        $totalCompletePC = 0;

        $startDate = Carbon::now();
        $endDate = Carbon::now();

        if($frequency == "CurrentMonth"){
            $startDate =  Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        }
        if($frequency == "CurrentWeek"){
            $startDate =  Carbon::now()->startOfWeek();
            $endDate = Carbon::now()->endOfWeek();
        }

        $startDate = date("Y-m-d",strtotime($startDate));
        $endDate = date("Y-m-d",strtotime($endDate));

        $pcQuickInfo = array();
        $pcs = ProjectContract::orderBy("created_at","desc")->orderBy("id","desc");

        if(!($startDate == null)){
            $pcs = $pcs->where(DB::raw("(STR_TO_DATE(start_date,'%Y-%m-%d'))"),'>=', $startDate)
                        ->where(DB::raw("(STR_TO_DATE(end_date,'%Y-%m-%d'))"),'>=', $startDate);
        }

        if(!($endDate == null)){
            $pcs = $pcs->where(DB::raw("(STR_TO_DATE(start_date,'%Y-%m-%d'))"),'<=', $endDate)
                        ->where(DB::raw("(STR_TO_DATE(end_date,'%Y-%m-%d'))"),'<=', $endDate);
        }

        $pcs = $pcs->get();

        foreach($pcs as $pc){
            $totalDue += $pc->totalDue();
            $totalLoss += $pc->totalLoss();
            $totalIncome +=  $pc->totalIncome();
            $totalReceive +=  $pc->totalReceive();
            $totalRevenue +=  $pc->totalRevenue();
            $totalReceivable +=  $pc->totalReceivable();

            if($pc->status == "Ongoing"){
                $totalOngingPC += 1;
            }

            if($pc->status == "Complete"){
                $totalCompletePC += 1;
            }
        }

        $pcQuickInfo["total_due"] = $totalDue;
        $pcQuickInfo["total_loss"] = $totalLoss;
        $pcQuickInfo["total_income"] = $totalIncome;
        $pcQuickInfo["total_receive"] =  $totalReceive;
        $pcQuickInfo["total_revenue"] =  $totalRevenue ;
        $pcQuickInfo["total_receivable"] = $totalReceivable;
        $pcQuickInfo["total_ongoing_pc"] = $totalOngingPC;
        $pcQuickInfo["total_complete_pc"] = $totalCompletePC;

        return $pcQuickInfo;
    }
}
