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

        return view('internal user.dashboard.index',compact("oagpSellCMQuickInfo","oagpSellCWQuickInfo","oagpPurchaseCMQuickInfo","oagpPurchaseCWQuickInfo"));
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
}
