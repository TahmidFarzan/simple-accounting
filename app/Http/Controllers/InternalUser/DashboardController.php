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


        $projectContractJournalEntries = ProjectContractJournal::orderBy("created_at","desc")->orderBy("name","asc")->get();
        $projectContractPayments = ProjectContractPayment::orderBy("created_at","desc")->orderBy("name","asc")->get();

        $ongoingProjectContractCount = ProjectContract::where("status","Ongoing")
                                                        ->where(DB::raw("(STR_TO_DATE(start_date,'%Y-%m-%d'))"),'>=', $startDate)
                                                        ->where(DB::raw("(STR_TO_DATE(start_date,'%Y-%m-%d'))"),'<=', $endDate)
                                                        ->count();


        $completeProjectContractCount = ProjectContract::where("status","Complete")
                                                        ->where(DB::raw("(STR_TO_DATE(end_date,'%Y-%m-%d'))"),'>=', $startDate)
                                                        ->where(DB::raw("(STR_TO_DATE(end_date,'%Y-%m-%d'))"),'<=', $endDate)
                                                        ->count();

        $revenueAmountProjectContractJournal = ProjectContractJournal::where("entry_type","Revenue")
                                                        ->where(DB::raw("(STR_TO_DATE(entry_date,'%Y-%m-%d'))"),'>=', $startDate)
                                                        ->where(DB::raw("(STR_TO_DATE(entry_date,'%Y-%m-%d'))"),'<=', $endDate)
                                                        ->sum("amount");


        $lossAmountProjectContractJournal = ProjectContractJournal::where("entry_type","Loss")
                                                        ->where(DB::raw("(STR_TO_DATE(entry_date,'%Y-%m-%d'))"),'>=', $startDate)
                                                        ->where(DB::raw("(STR_TO_DATE(entry_date,'%Y-%m-%d'))"),'<=', $endDate)
                                                        ->sum("amount");
        $paymentProjectContractPayment = ProjectContractPayment::where(DB::raw("(STR_TO_DATE(payment_date,'%Y-%m-%d'))"),'>=', $startDate)
                                                            ->where(DB::raw("(STR_TO_DATE(payment_date,'%Y-%m-%d'))"),'<=', $endDate)
                                                            ->sum("amount");

        $projectContractQuickView = array("ongoing" => $ongoingProjectContractCount,"complete" => $completeProjectContractCount);
        $projectContractJournalQuickView = array("revenue" => $revenueAmountProjectContractJournal,"loss" => $lossAmountProjectContractJournal);
        $projectContractPaymentQuickView = array("payment" => $paymentProjectContractPayment);


        return view('internal user.dashboard.index',compact("projectContractQuickView","projectContractJournalQuickView","projectContractPaymentQuickView"));
    }
}
