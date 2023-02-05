<?php

namespace App\Http\Controllers\InternalUser;

use Carbon\Carbon;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\OilAndGasPump;
use App\Utilities\SystemConstant;
use App\Models\OilAndGasPumpProduct;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\OilAndGasPumpInventory;
use Illuminate\Support\Facades\Mail;
use Spatie\Activitylog\Facades\LogBatch;
use Illuminate\Support\Facades\Validator;
use App\Mail\EmailSendForOilAndGasPumpInventory;

class OilAndGasPumpInventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
        $this->middleware(['user.user.permission.check:OAGPIMP01'])->only(["index"]);
        $this->middleware(['user.user.permission.check:OAGPIMP02'])->only(["add","save"]);
        $this->middleware(['user.user.permission.check:OAGPIMP03'])->only(["details"]);
        $this->middleware(['user.user.permission.check:OAGPIMP04'])->only(["delete"]);
    }

    public function index($oagpSlug){
        $oilAndGasPump = OilAndGasPump::where("slug",$oagpSlug)->firstOrFail();
        $inventories = OilAndGasPumpInventory::orderby("id","asc")->orderby("created_at","desc")->whereIn("oagp_product_id",$oilAndGasPump->oilAndGasPumpProducts->pluck("id"))->get();
        return view('internal user.oil and gas pump.inventory.index',compact("inventories","oilAndGasPump"));
    }

    public function add($oagpSlug){
        $oilAndGasPump = OilAndGasPump::where("slug",$oagpSlug)->firstOrFail();

        $productsInInventory = OilAndGasPumpInventory::orderby("id","asc")->orderby("created_at","desc")->whereIn("oagp_product_id",$oilAndGasPump->oilAndGasPumpProducts->pluck("id"))->pluck("oagp_product_id");
        $products = OilAndGasPumpProduct::orderBy("name","asc")->where("oil_and_gas_pump_id",$oilAndGasPump->id);

        if($productsInInventory->count() > 0){
            $products = $products->whereNotIn("id",$productsInInventory);
        }
        $products = $products->get();

        return view('internal user.oil and gas pump.inventory.add',compact("products","oilAndGasPump"));
    }

    public function details($oagpSlug,$inSlug){
        $inventory = OilAndGasPumpInventory::where("slug",$inSlug)->firstOrFail();
        return view('internal user.oil and gas pump.inventory.details',compact("inventory"));
    }

    public function save($oagpSlug,Request $request){
        $validator = Validator::make($request->all(),
            [
                'product' => 'required',
                'quantity' => 'required|numeric',
                'sell_price' => 'required|numeric',
                'purchase_price' => 'required|numeric',

                'old_quantity' => 'required|numeric',
                'old_sell_price' => 'required|numeric',
                'old_purchase_price' => 'required|numeric',
            ],
            [
                'product.required' => 'Product is required.',

                'quantity.required' => 'Quantity is required.',
                'quantity.numeric' => 'Qount must be numeric.',

                'sell_price.required' => 'Sell price is required.',
                'sell_price.numeric' => 'Sell price must be numeric.',

                'purchase_price.required' => 'Purchase price is required.',
                'purchase_price.numeric' => 'Purchase price must be numeric.',

                'old_quantity.required' => 'Old quantity is required.',
                'old_quantity.numeric' => 'Old quantity must be numeric.',

                'old_sell_price.required' => 'Old sell price is required.',
                'old_sell_price.numeric' => 'Old sell price must be numeric.',

                'old_purchase_price.required' => 'Old purchase price is required.',
                'old_purchase_price.numeric' => 'Old purchase price must be numeric.',
            ]
        );

        $validator->after(function ($validator) {
            $afterValidatorData = $validator->getData();
            $osgpProductFound = OilAndGasPumpProduct::where("slug",$afterValidatorData["product"])->count();

            if($osgpProductFound == 0 ){
                $validator->errors()->add(
                    'product', "Unknown product selected."
                );
            }
            else{
                $osgpProduct = OilAndGasPumpProduct::where("slug",$afterValidatorData["product"])->firstOrFail();
                $productInInventory = OilAndGasPumpInventory::where("oagp_product_id",$osgpProduct->id)->count();

                if($productInInventory > 0){
                    $validator->errors()->add(
                        'product', "Product already exit in inventory."
                    );
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $oilAndGasPump = OilAndGasPump::where("slug",$oagpSlug)->firstOrFail();

        $statusInformation = array("status" => "errors","message" => collect());

        $osgpProduct = OilAndGasPumpProduct::where("slug",$request->product)->firstOrFail();

        LogBatch::startBatch();
            $oagpInventory = new OilAndGasPumpInventory();
            $oagpInventory->oagp_product_id = $osgpProduct->id;
            $oagpInventory->quantity = $request->quantity;
            $oagpInventory->sell_price = $request->sell_price;
            $oagpInventory->purchase_price = $request->purchase_price;
            $oagpInventory->old_quantity = $request->old_quantity;
            $oagpInventory->old_sell_price = $request->old_sell_price;
            $oagpInventory->old_purchase_price = $request->old_purchase_price;
            $oagpInventory->slug = SystemConstant::slugGenerator("Inventory ".$osgpProduct->name,200);
            $oagpInventory->created_at = Carbon::now();
            $oagpInventory->created_by_id = Auth::user()->id;
            $oagpInventory->updated_at = null;
            $saveOAGPIn = $oagpInventory->save();
        LogBatch::endBatch();

        if($saveOAGPIn){
            $this->sendEmail("Add","Product has been added to inventoryby ".Auth::user()->name.".",$oagpInventory );
            $statusInformation["status"] = "status";
            $statusInformation["message"]->push("Successfully added to inventory.");
        }
        else{
            $statusInformation["status"] = "errors";
            $statusInformation["message"]->push("Can not added to inventory.");
        }
        return redirect()->route("oil.and.gas.pump.inventory.index",["oagpSlug" => $oilAndGasPump->slug])->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    public function delete($oagpSlug,$inSlug){
        $statusInformation = array("status" => "errors","message" => collect());

        $oilAndGasPump = OilAndGasPump::where("slug",$oagpSlug)->firstOrFail();
        $deleteValidationStatus = $this->deleteValidation($inSlug);

        if($deleteValidationStatus["status"] == "status"){
            LogBatch::startBatch();
                $inventoryProduct = OilAndGasPumpInventory::where("slug",$inSlug)->firstOrFail();
                $deleteOAGPProduct = $inventoryProduct->delete();
            LogBatch::endBatch();

            if($deleteOAGPProduct){
                $this->sendEmail("Delete","Product has been delete by ".Auth::user()->name.".",$inventoryProduct );

                $statusInformation["status"] = "status";
                $statusInformation["message"]->push("Successfully deleted.");
            }
            else{
                $statusInformation["status"] = "errors";
                $statusInformation["message"]->push("Fail to delete.");
            }
        }
        else{
            $statusInformation["status"] = "errors";
            $statusInformation["message"]->push("Fail to delete.");

            foreach($deleteValidationStatus["message"] as $perMessage){
                $statusInformation["message"]->push($perMessage);
            }
        }

        return redirect()->route("oil.and.gas.pump.inventory.index",["oagpSlug" => $oilAndGasPump->slug])->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    private function deleteValidation($inSlug){
        $statusInformation = array("status" => "errors","message" => collect());

        $inProduct = OilAndGasPumpInventory::where("slug",$inSlug)->firstOrFail();

        if(($inProduct->oagpProduct->oagpPurchaseItems->count() == 0)){
            $statusInformation["status"] = "status";
            $statusInformation["message"]->push("Passed the validation.");
        }
        else{
            $statusInformation["status"] = "errors";
            $statusInformation["message"]->push("Can not delete the product.");

            if($inProduct->oagpProduct->oagpPurchaseItems->count() > 0){
                $statusInformation["message"]->push("The product has been in the purchase.");
            }
        }

        return $statusInformation;
    }

    private function sendEmail($event,$subject,OilAndGasPumpInventory $inventory ){
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
