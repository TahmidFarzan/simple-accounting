<?php

namespace App\Http\Controllers\InternalUser;

use Exception;
use Carbon\Carbon;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\OilAndGasPump;
use App\Utilities\SystemConstant;
use Illuminate\Support\Facades\DB;
use App\Models\OilAndGasPumpSupplier;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Spatie\Activitylog\Facades\LogBatch;
use Illuminate\Support\Facades\Validator;
use App\Mail\EmailSendForOilAndGasPumpSupplier;

class OilAndGasPumpSupplierController extends Controller
{
    private $sSlug = null;
    private $oagpSlug = null;

    public function __construct()
    {
        $this->middleware(['auth','verified']);
        $this->middleware(['user.user.permission.check:OAGPPMP01'])->only(["index"]);
        $this->middleware(['user.user.permission.check:OAGPPMP02'])->only(["create","save"]);
        $this->middleware(['user.user.permission.check:OAGPPMP03'])->only(["details"]);
        $this->middleware(['user.user.permission.check:OAGPPMP04'])->only(["edit","update"]);
        $this->middleware(['user.user.permission.check:OAGPPMP05'])->only(["delete"]);
    }

    public function index($oagpSlug,Request $request){
        $pagination = 5;
        $paginations = array(5,15,30,45,60,75,90,105,120);
        $oilAndGasPump = OilAndGasPump::where("slug",$oagpSlug)->firstOrFail();
        $suppliers = OilAndGasPumpSupplier::orderby("name","asc")->orderby("created_at","desc");

        if(count($request->input()) > 0){
            if($request->has('pagination')){
                $pagination = (in_array($request->pagination,$paginations)) ? $request->pagination : $pagination;
            }

            if($request->has('search') && !($request->search == null)){
                $suppliers = $suppliers->where("name","like","%".$request->search."%")
                                                ->orWhere("email","like","%".$request->search."%")
                                                ->orWhere("mobile_no","like","%".$request->search."%");
            }
        }

        $suppliers = $suppliers->paginate($pagination);

        return view('internal user.oil and gas pump.supplier.index',compact("suppliers","oilAndGasPump","paginations"));
    }

    public function details($oagpSlug,$sSlug){
        $supplier = OilAndGasPumpSupplier::where("slug",$sSlug)->firstOrFail();
        return view('internal user.oil and gas pump.supplier.details',compact("supplier"));
    }

    public function delete($oagpSlug,$sSlug){
        $statusInformation = array("status" => "errors","message" => collect());

        $oilAndGasPump = OilAndGasPump::where("slug",$oagpSlug)->firstOrFail();

        $deteValidationStatus = $this->deleteValidation($sSlug);

        if(true){
            try{
                DB::beginTransaction();
                LogBatch::startBatch();
                    $supplier = OilAndGasPumpSupplier::where("slug",$sSlug)->firstOrFail();
                    $deleteOilAndGasPumpSupplier = $supplier->delete();
                LogBatch::endBatch();

                if($deleteOilAndGasPumpSupplier){
                    DB::commit();
                    $this->sendEmail("Delete","Supplier has been delete by ".Auth::user()->name.".",$supplier );

                    $statusInformation["status"] = "status";
                    $statusInformation["message"]->push("Successfully deleted.");
                }
                else{
                    DB::rollBack();
                    $statusInformation["status"] = "errors";
                    $statusInformation["message"]->push("Fail to delete.");
                }
            }
            catch (Exception $e) {
                DB::rollBack();
                $statusInformation["status"] = "errors";
                $statusInformation["message"]->push("Error info: ".$e);
            }
        }
        else{
            $statusInformation["status"] = "errors";
            $statusInformation["message"]->push("Fail to delete.");

            foreach($deteValidationStatus["message"] as $perMessage){
                $statusInformation["message"]->push($perMessage);
            }
        }

        return redirect()->route("oil.and.gas.pump.supplier.index",["oagpSlug" => $oilAndGasPump->slug])->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    private function deleteValidation($sSlug){
        $statusInformation = array("status" => "errors","message" => collect());

        $oilAndGasPumpSupplier = OilAndGasPumpSupplier::where("slug",$sSlug)->firstOrFail();

        if( $oilAndGasPumpSupplier->oagpPurchases->count() == 0){
            $statusInformation["status"] = "status";
            $statusInformation["message"]->push("Passed the validation.");
        }
        else{
            $statusInformation["status"] = "errors";
            $statusInformation["message"]->push($oilAndGasPumpSupplier->oagpPurchases->count()." supplier(s) exit.");
        }

        return $statusInformation;
    }

    private function sendEmail($event,$subject,OilAndGasPumpSupplier $supplier ){
        $envelope = array();

        $emailSendSetting = Setting::where( 'code','EmailSendSetting')->firstOrFail()->fields_with_values;

        $envelope["to"] = $emailSendSetting["to"];
        $envelope["cc"] = $emailSendSetting["cc"];
        $envelope["from"] = $emailSendSetting["from"];
        $envelope["reply"] = $emailSendSetting["reply"];

        $moduleSetting = $emailSendSetting["module"]["OilAndGasPumpSupplier"];

        if(($moduleSetting["send"] == true) && (($moduleSetting["event"] == "All") || (!($moduleSetting["event"] == "All") && ($moduleSetting["event"] == $event)))){
            Mail::send(new EmailSendForOilAndGasPumpSupplier($event,$envelope,$subject,$supplier));
        }
    }
}
