<?php

namespace App\Http\Controllers\InternalUser;

use Carbon\Carbon;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\OilAndGasPump;
use App\Utilities\SystemConstant;
use App\Utilities\InventoryConstant;
use App\Models\OilAndGasPumpProduct;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Spatie\Activitylog\Facades\LogBatch;
use Illuminate\Support\Facades\Validator;
use App\Mail\EmailSendForOilAndGasPumpProduct;

class OilAndGasPumpProductController extends Controller
{
    private $pSlug = null;
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
        $products = OilAndGasPumpProduct::orderby("created_at","desc")->orderby("name","asc")->where("oil_and_gas_pump_id",$oilAndGasPump->id);

        if(count($request->input()) > 0){
            if($request->has('pagination')){
                $pagination = (in_array($request->pagination,$paginations)) ? $request->pagination : $pagination;
            }

            if($request->has('type') && !($request->type == null)){
                $products = $products->where("type",$request->type);
            }

            if($request->has('search') && !($request->search == null)){
                $products = $products->where("name","like","%".$request->search."%")
                                                ->orWhere("code","like","%".$request->search."%");
            }
        }

        $products = $products->paginate($pagination);

        return view('internal user.oil and gas pump.product.index',compact("products","oilAndGasPump","paginations"));
    }

    public function create($oagpSlug){
        $oilAndGasPump = OilAndGasPump::where("slug",$oagpSlug)->firstOrFail();
        return view('internal user.oil and gas pump.product.create',compact("oilAndGasPump"));
    }

    public function details($oagpSlug,$pSlug){
        $product = OilAndGasPumpProduct::where("slug",$pSlug)->firstOrFail();
        return view('internal user.oil and gas pump.product.details',compact("product"));
    }

    public function edit($oagpSlug,$pSlug){
        $product = OilAndGasPumpProduct::where("slug",$pSlug)->firstOrFail();
        return view('internal user.oil and gas pump.product.edit',compact("product"));
    }

    public function save($oagpSlug,Request $request){
        $this->oagpSlug = $oagpSlug;

        $validator = Validator::make($request->all(),
            [
                'name' => 'required|max:200',
                'type' => 'required|in:Oil,Gas',
                'add_to_inventory' => 'required|in:Yes,No',
            ],
            [
                'name.required' => 'Name is required.',
                'name.max' => 'Name length can not greater then 200 chars.',

                'type.required' => 'Type is required.',
                'type.max' => 'Type must be one out of [Oil,Gas].',

                'add_to_inventory.required' => 'Add to inventory is required.',
                'add_to_inventory.max' => 'Add to inventory must be one out of [No,Yes].',
            ]
        );

        $validator->after(function ($validator) {
            $afterValidatorData = $validator->getData();
            $oilAndGasPump = OilAndGasPump::where("slug",$this->oagpSlug)->firstOrFail();
            $productCount = OilAndGasPumpProduct::where("oil_and_gas_pump_id",$oilAndGasPump->id)->where("name",$afterValidatorData["name"])->count();

            if($productCount > 0 ){
                $validator->errors()->add(
                    'name', "Same product exit."
                );
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $oilAndGasPump = OilAndGasPump::where("slug",$oagpSlug)->firstOrFail();

        $statusInformation = array("status" => "errors","message" => collect());

        LogBatch::startBatch();
            $product = new OilAndGasPumpProduct();
            $product->name = $request->name;
            $product->type = $request->type;
            $product->oil_and_gas_pump_id =  $oilAndGasPump->id;
            $product->slug = SystemConstant::slugGenerator($request->name,200);
            $product->created_at = Carbon::now();
            $product->created_by_id = Auth::user()->id;
            $product->updated_at = null;
            $saveProduct = $product->save();
        LogBatch::endBatch();

        if($saveProduct){
                $this->sendEmail("Create","A new product has been created by ".Auth::user()->name.".",$product );

                $statusInformation["status"] = "status";
                $statusInformation["message"]->push("Successfully created.");

                if($request->add_to_inventory == "Yes"){
                    $addProductToInventoryStatus = InventoryConstant::addProductToInventory($product->slug);
                    $statusInformation["status"] = $addProductToInventoryStatus["status"];

                    foreach( $addProductToInventoryStatus["message"] as $inMessage){
                        $statusInformation["message"]->push($inMessage);
                    }
                }
        }
        else{
            $statusInformation["status"] = "errors";
            $statusInformation["message"]->push("Can not update.");
        }

        return redirect()->route("oil.and.gas.pump.product.index",["oagpSlug" => $oilAndGasPump->slug])->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    public function update($oagpSlug,Request $request,$pSlug){
        $this->oagpSlug = $oagpSlug;
        $this->pSlug = $pSlug;

        $validator = Validator::make($request->all(),
            [
                'name' => 'required|max:200',
                'type' => 'required|in:Oil,Gas',
            ],
            [
                'name.required' => 'Name is required.',
                'name.max' => 'Name length can not greater then 200 chars.',

                'type.required' => 'Type is required.',
                'type.max' => 'Type must be one out of [Oil,Gas].',
            ]
        );

        $validator->after(function ($validator) {
            $afterValidatorData = $validator->getData();
            $oilAndGasPump = OilAndGasPump::where("slug",$this->oagpSlug)->firstOrFail();
            $productCount = OilAndGasPumpProduct::where("oil_and_gas_pump_id",$oilAndGasPump->id)->where("name",$afterValidatorData["name"])->whereNot("slug",$this->pSlug)->count();

            if($productCount > 0 ){
                $validator->errors()->add(
                    'name', "Same product exit."
                );
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $statusInformation = array("status" => "errors","message" => collect());
        $oilAndGasPump = OilAndGasPump::where("slug",$oagpSlug)->firstOrFail();

        LogBatch::startBatch();
            $product = OilAndGasPumpProduct::where("slug",$pSlug)->firstOrFail();
            $product->name = $request->name;
            $product->type = $request->type;
            $product->slug = SystemConstant::slugGenerator($request->name,200);
            $product->updated_at = Carbon::now();
            $updateProduct = $product->update();
        LogBatch::endBatch();

        if($updateProduct){
            $this->sendEmail("Update","The product has been updated by ".Auth::user()->name.".",$product );

            $statusInformation["status"] = "status";
            $statusInformation["message"] = "Successfully updated.";
        }
        else{
            $statusInformation["status"] = "errors";
            $statusInformation["message"]->push("Can not update.");
        }

        return redirect()->route("oil.and.gas.pump.product.index",["oagpSlug" => $oilAndGasPump->slug])->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    public function delete($oagpSlug,$pSlug){
        $statusInformation = array("status" => "errors","message" => collect());

        $oilAndGasPump = OilAndGasPump::where("slug",$oagpSlug)->firstOrFail();

        $deleteValidationStatus = $this->deleteValidation($pSlug);

        if($deleteValidationStatus["status"] == "status"){
            LogBatch::startBatch();
                $product = OilAndGasPumpProduct::where("slug",$pSlug)->firstOrFail();
                $deleteOilAndGasPumpProduct = $product->delete();
            LogBatch::endBatch();

            if($deleteOilAndGasPumpProduct){
                $this->sendEmail("Delete","Product has been delete by ".Auth::user()->name.".",$product );

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

        return redirect()->route("oil.and.gas.pump.product.index",["oagpSlug" => $oilAndGasPump->slug])->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    private function deleteValidation($pSlug){
        $statusInformation = array("status" => "errors","message" => collect());

        $product = OilAndGasPumpProduct::where("slug",$pSlug)->firstOrFail();

        if(!($product->oagpInventory) && ($product->oagpPurchaseItems->count() == 0)){
            $statusInformation["status"] = "status";
            $statusInformation["message"]->push("Passed the validation.");
        }
        else{
            $statusInformation["status"] = "errors";
            $statusInformation["message"]->push("Can not delete the product.");

            if($product->oagpPurchaseItems->count() > 0){
                $statusInformation["message"]->push("The product has been in the purchase.");
            }

            if($product->oagpInventory){
                $statusInformation["message"]->push("The product is in the inventory.");
            }
        }

        return $statusInformation;
    }

    private function sendEmail($event,$subject,OilAndGasPumpProduct $product ){
        $envelope = array();

        $emailSendSetting = Setting::where( 'code','EmailSendSetting')->firstOrFail()->fields_with_values;

        $envelope["to"] = $emailSendSetting["to"];
        $envelope["cc"] = $emailSendSetting["cc"];
        $envelope["from"] = $emailSendSetting["from"];
        $envelope["reply"] = $emailSendSetting["reply"];

        $moduleSetting = $emailSendSetting["module"]["OilAndGasPumpProduct"];

        if(($moduleSetting["send"] == true) && (($moduleSetting["event"] == "All") || (!($moduleSetting["event"] == "All") && ($moduleSetting["event"] == $event)))){
            Mail::send(new EmailSendForOilAndGasPumpProduct($event,$envelope,$subject,$product));
        }
    }
}
