<?php

namespace App\Http\Controllers\InternalUser;

use Exception;
use Carbon\Carbon;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\OilAndGasPump;
use App\Utilities\SystemConstant;
use Illuminate\Support\Facades\DB;
use App\Models\OilAndGasPumpProduct;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Models\OilAndGasPumpPurchase;
use App\Models\OilAndGasPumpSupplier;
use Spatie\Activitylog\Facades\LogBatch;
use Illuminate\Support\Facades\Validator;
use App\Models\OilAndGasPumpPurchaseItem;
use App\Mail\EmailSendForOilAndGasPumpPurchase;

class OilAndGasPumpPurchaseController extends Controller
{
    private $puSlug = null;
    private $oagpSlug = null;

    public function __construct()
    {
        $this->middleware(['auth','verified']);
        $this->middleware(['user.user.permission.check:OAGPPUMP01'])->only(["index"]);
        $this->middleware(['user.user.permission.check:OAGPPUMP02'])->only(["create","save"]);
        $this->middleware(['user.user.permission.check:OAGPPUMP03'])->only(["details"]);
        $this->middleware(['user.user.permission.check:OAGPPUMP04'])->only(["edit","update"]);
    }

    public function index($oagpSlug,Request $request){
        $pagination = 5;
        $paginations = array(5,15,30,45,60,75,90,105,120);
        $oilAndGasPump = OilAndGasPump::where("slug",$oagpSlug)->firstOrFail();
        $oagpSuppliers = OilAndGasPumpSupplier::orderby("name","asc")->orderby("created_at","desc")->get();
        $completeOAGPPurchases = OilAndGasPumpPurchase::orderby("name","asc")->orderby("created_at","desc")->where("status","Complete");
        $dueOAGPPurchases = OilAndGasPumpPurchase::orderby("name","asc")->orderby("created_at","desc")->where("status","Due");

        if(count($request->input()) > 0){
            if($request->has('pagination')){
                $pagination = (in_array($request->pagination,$paginations)) ? $request->pagination : $pagination;
            }

            if($request->has('category') && !($request->category == null) && !($request->category == "All")){
                $oagpSupplier = OilAndGasPumpSupplier::where("slug",$request->category)->first();

                if($oagpSupplier){
                    switch ($request->selected_nav_tab) {
                        case 'Due':
                            $dueOAGPPurchases = $dueOAGPPurchases->where("opgp_supplier_id",$oagpSupplier->id);
                        break;

                        case 'Complete':
                            $completeOAGPPurchases = $completeOAGPPurchases->where("opgp_supplier_id",$oagpSupplier->id);
                        break;

                        default:
                            abort(404,"Unknown nav.");
                        break;
                    }
                }
            }

            if($request->has('date') && !($request->date == null) && $request->has('date_condition') && !($request->date_condition == null) && in_array($request->date_condition, array("=",">","<",">=","<="))){
                switch ($request->selected_nav_tab) {
                    case 'Due':
                        $dueOAGPPurchases = $dueOAGPPurchases->where("date",$request->date_condition,$request->date);
                    break;

                    case 'Complete':
                        $completeOAGPPurchases = $completeOAGPPurchases->where("date",$request->date_condition,$request->date);
                    break;

                    default:
                        abort(404,"Unknown nav.");
                    break;
                }
            }

            if($request->has('search') && !($request->search == null)){
                switch ($request->selected_nav_tab) {
                    case 'Due':
                        $dueOAGPPurchases = $dueOAGPPurchases->where("name","like","%".$request->search."%")
                                                    ->orWhere("invoice","like","%".$request->search."%")
                                                    ->orWhere("description","like","%".$request->search."%");
                    break;


                    case 'Complete':
                        $completeOAGPPurchases = $completeOAGPPurchases->where("name","like","%".$request->search."%")
                                                ->orWhere("invoice","like","%".$request->search."%")
                                                ->orWhere("description","like","%".$request->search."%");
                    break;

                    default:
                        abort(404,"Unknown nav.");
                    break;
                }
            }
        }

        $completeOAGPPurchases = $completeOAGPPurchases->paginate($pagination);
        $dueOAGPPurchases = $dueOAGPPurchases->paginate($pagination);

        return view('internal user.oil and gas pump.purchase.index',compact("completeOAGPPurchases","dueOAGPPurchases","oagpSuppliers","oilAndGasPump","paginations"));
    }

    public function create($oagpSlug){
        $oilAndGasPump = OilAndGasPump::where("slug",$oagpSlug)->firstOrFail();
        $oagpSuppliers = OilAndGasPumpSupplier::orderby("name","asc")->where("oil_and_gas_pump_id",$oilAndGasPump->id)->get();
        $oilAndGasPumpProducts = OilAndGasPumpProduct::orderby("name","asc")->where("oil_and_gas_pump_id",$oilAndGasPump->id)->get();

        return view('internal user.oil and gas pump.purchase.create',compact("oilAndGasPump","oagpSuppliers","oilAndGasPumpProducts"));
    }
}
