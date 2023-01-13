<?php

namespace App\Utilities;

use Auth;
use Exception;
use Carbon\Carbon;
use App\Models\Setting;
use Illuminate\Support\Str;
use App\Utilities\SystemConstant;
use Illuminate\Support\Facades\DB;
use App\Models\OilAndGasPumpProduct;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Models\OilAndGasPumpInventory;
use Spatie\Activitylog\Facades\LogBatch;
use App\Mail\EmailSendForOilAndGasPumpInventory;

class InventoryConstant
{
    public static function addProductToInventory($pSlug){
        $statusInformation = array("status" => "errors","message" => collect());
        if(Auth::user()->hasUserPermission(["OAGPIMP02"]) == true){
            $osgpProduct = OilAndGasPumpProduct::where("slug",$pSlug)->firstOrFail();
            DB::beginTransaction();
            try{
                LogBatch::startBatch();
                    $oagpInventory = new OilAndGasPumpInventory();
                    $oagpInventory->oagp_product_id = $osgpProduct->id;
                    $oagpInventory->slug = SystemConstant::slugGenerator("Inventory ".$osgpProduct->name,200);
                    $oagpInventory->created_at = Carbon::now();
                    $oagpInventory->created_by_id = Auth::user()->id;
                    $oagpInventory->updated_at = null;
                    $saveOAGPIn = $oagpInventory->save();
                LogBatch::endBatch();

                if($saveOAGPIn){
                    DB::commit();
                    InventoryConstant::sendEmail("Add","Product has been added to inventory.",$oagpInventory );
                    $statusInformation["status"] = "status";
                    $statusInformation["message"]->push("Successfully added to inventory.");
                }
                else{
                    $statusInformation["status"] = "errors";
                    $statusInformation["message"]->push("Can not added to inventory.");
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
            $statusInformation["message"]->push("User does not have permission to add product to inventory.");
        }
        return $statusInformation;
    }

    public static function sendEmail($event,$subject,OilAndGasPumpInventory $inventory ){
        $envelope = array();

        $emailSendSetting = Setting::where( 'code','EmailSendSetting')->firstOrFail()->fields_with_values;

        $envelope["to"] = $emailSendSetting["to"];
        $envelope["cc"] = $emailSendSetting["cc"];
        $envelope["from"] = $emailSendSetting["from"];
        $envelope["reply"] = $emailSendSetting["reply"];

        $moduleSetting = $emailSendSetting["module"]["OilAndGasPumpInventory"];

        if(($moduleSetting["send"] == true) && (($moduleSetting["event"] == "All") || (!($moduleSetting["event"] == "All") && ($moduleSetting["event"] == $event)))){
            Mail::send(new EmailSendForOilAndGasPumpInventory($event,$envelope,$subject,$inventory));
        }
    }
}
