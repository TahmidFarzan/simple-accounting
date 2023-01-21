<?php

namespace App\Utilities;

use Auth;
use Exception;
use Carbon\Carbon;
use App\Models\Setting;
use Illuminate\Support\Str;
use App\Utilities\SystemConstant;
use App\Models\OilAndGasPumpProduct;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Models\OilAndGasPumpPurchase;
use App\Models\OilAndGasPumpInventory;
use Spatie\Activitylog\Facades\LogBatch;
use App\Mail\EmailSendForOilAndGasPumpInventory;

class InventoryConstant
{
    public static function addProductToInventory($pSlug){
        $statusInformation = array("status" => "errors","message" => collect());
        if(Auth::user()->hasUserPermission(["OAGPIMP02"]) == true){
            $osgpProduct = OilAndGasPumpProduct::where("slug",$pSlug)->firstOrFail();

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
                    InventoryConstant::sendEmail("Add","Product has been added to inventory by ".Auth::user()->name.".",$oagpInventory );
                    $statusInformation["status"] = "status";
                    $statusInformation["message"]->push("Successfully added to inventory.");
                }
                else{
                    $statusInformation["status"] = "errors";
                    $statusInformation["message"]->push("Can not added to inventory.");
                }
            }
            catch (Exception $e) {
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

    public static function updateProductToInventoryForPurachase($puSlug){
        $statusInformation = array("status" => "errors","message" => collect());
        $inventoryUpdateCount = 0;

        $oilAndGasPumpPurchase = OilAndGasPumpPurchase::where("slug",$puSlug)->firstOrFail();

        foreach ($oilAndGasPumpPurchase->oagpPurchaseItems as $oagpPurchaseItem) {

            // Product exit not exit in inventory
            if(InventoryConstant::productExitInInventory($oagpPurchaseItem->oagp_product_id) == false){
                InventoryConstant::addProductToInventory($oagpPurchaseItem->oagpProduct->slug);
            }

            $oldQuantity = 0.0;
            $oldSellPrice = 0.0;
            $oldPurchasePrice = 0.0;

            $oagpInProduct = OilAndGasPumpInventory::where("oagp_product_id",$oagpPurchaseItem->oagp_product_id)->firstOrFail();

            // Save old status
            if($oagpInProduct->quantity > 0){
                // Old quantity exit
                $oldQuantity = ($oagpPurchaseItem->old_quantity == 0) ? $oagpPurchaseItem->quantity : ($oagpPurchaseItem->old_quantity + $oagpPurchaseItem->quantity);

                $oldSellPrice = $oagpPurchaseItem->sell_price;
                $oldPurchasePrice = $oagpPurchaseItem->purchase_price;
            }
            $oagpPurchaseItem->old_quantity = $oldQuantity;
            $oagpInProduct->old_sell_price = $oldSellPrice;
            $oagpInProduct->old_purchase_price = $oldPurchasePrice;

            // New status
            $oagpPurchaseItem->quantity = $oagpPurchaseItem->quantity;
            $oagpInProduct->sell_price = $oagpPurchaseItem->sell_price;
            $oagpInProduct->purchase_price = $oagpPurchaseItem->purchase_price;
            $oagpInProduct->updated_at = Carbon::now();
            $oagpInProductUpdate = $oagpInProduct->update();

            if($oagpInProductUpdate){
                $inventoryUpdateCount = $inventoryUpdateCount + 1;
            }
        }

        if($inventoryUpdateCount == $oilAndGasPumpPurchase->oagpPurchaseItems->count()){
            $statusInformation["status"] = "success";
            $statusInformation["message"]->push("All seleted inventory product successfully update.");
        }
        else{
            $statusInformation["message"]->push("Some seleted inventory product fail to update.");
        }

        return $statusInformation;
    }

    public static function productExitInInventory($productId){
        $productExit = false;

        $osgpProduct = OilAndGasPumpInventory::where("oagp_product_id",$productId)->count();

        if($osgpProduct > 1){
            $osgpProduct = true;
        }

        if($osgpProduct == 0){
            $productExit = false;
        }

        return $productExit;
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
