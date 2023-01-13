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
                'count' => 'required|numeric',
                'sell_price' => 'required|numeric',
                'purchase_price' => 'required|numeric',

                'previous_count' => 'required|numeric',
                'previous_sell_price' => 'required|numeric',
                'previous_purchase_price' => 'required|numeric',
            ],
            [
                'product.required' => 'Product is required.',

                'count.required' => 'Count is required.',
                'count.numeric' => 'Count must be numeric.',

                'sell_price.required' => 'Sell price is required.',
                'sell_price.numeric' => 'Sell price must be numeric.',

                'purchase_price.required' => 'Purchase price is required.',
                'purchase_price.numeric' => 'Purchase price must be numeric.',

                'previous_count.required' => 'Previous count is required.',
                'previous_count.numeric' => 'Previous count must be numeric.',

                'previous_sell_price.required' => 'Previous sell price is required.',
                'previous_sell_price.numeric' => 'Previous sell price must be numeric.',

                'previous_purchase_price.required' => 'Previous purchase price is required.',
                'previous_purchase_price.numeric' => 'Previous purchase price must be numeric.',
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

        try{
            DB::beginTransaction();
            $osgpProduct = OilAndGasPumpProduct::where("slug",$request->product)->firstOrFail();

            LogBatch::startBatch();
                $oagpInventory = new OilAndGasPumpInventory();
                $oagpInventory->oagp_product_id = $osgpProduct->id;
                $oagpInventory->count = $request->count;
                $oagpInventory->sell_price = $request->sell_price;
                $oagpInventory->purchase_price = $request->purchase_price;
                $oagpInventory->previous_count = $request->previous_count;
                $oagpInventory->previous_sell_price = $request->previous_sell_price;
                $oagpInventory->previous_purchase_price = $request->previous_purchase_price;
                $oagpInventory->slug = SystemConstant::slugGenerator("Inventory ".$osgpProduct->name,200);
                $oagpInventory->created_at = Carbon::now();
                $oagpInventory->created_by_id = Auth::user()->id;
                $oagpInventory->updated_at = null;
                $saveOAGPIn = $oagpInventory->save();
            LogBatch::endBatch();

            if($saveOAGPIn){
                DB::commit();
                $this->sendEmail("Add","Product has been added to inventoryby ".Auth::user()->name.".",$oagpInventory );
                $statusInformation["status"] = "status";
                $statusInformation["message"]->push("Successfully added to inventory.");
            }
            else{
                DB::rollBack();
                $statusInformation["status"] = "errors";
                $statusInformation["message"]->push("Can not added to inventory.");
            }
        }
        catch (Exception $e) {
            DB::rollBack();
            $statusInformation["status"] = "errors";
            $statusInformation["message"]->push("Error info: ".$e);
        }

        return redirect()->route("oil.and.gas.pump.inventory.index",["oagpSlug" => $oilAndGasPump->slug])->with([$statusInformation["status"] => $statusInformation["message"]]);
    }
}
