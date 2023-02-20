<?php

namespace App\Http\Controllers\InternalUser;

use Carbon\Carbon;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\OilAndGasPump;
use App\Models\OilAndGasPumpSell;
use App\Utilities\SystemConstant;
use App\Utilities\InventoryConstant;
use App\Models\OilAndGasPumpProduct;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Models\OilAndGasPumpSellItem;
use App\Models\OilAndGasPumpInventory;
use App\Models\OilAndGasPumpSellPayment;
use Spatie\Activitylog\Facades\LogBatch;
use Illuminate\Support\Facades\Validator;
use App\Mail\EmailSendForOilAndGasPumpSell;

class OilAndGasPumpSellController extends Controller
{
    private $seSlug = null;
    private $oagpSlug = null;

    public function __construct()
    {
        $this->middleware(['auth','verified']);
        $this->middleware(['user.user.permission.check:OAGPSEMP01'])->only(["index"]);
        $this->middleware(['user.user.permission.check:OAGPSEMP02'])->only(["create","save"]);
        $this->middleware(['user.user.permission.check:OAGPSEMP03'])->only(["details"]);
        $this->middleware(['user.user.permission.check:OAGPSEMP04'])->only(["addPayment","savePayment"]);
    }

    public function index($oagpSlug,Request $request){
        $pagination = 5;
        $paginations = array(5,15,30,45,60,75,90,105,120);
        $oilAndGasPump = OilAndGasPump::where("slug",$oagpSlug)->firstOrFail();
        $completeOAGPSells = OilAndGasPumpSell::orderby("name","asc")->orderby("created_at","desc")->where("status","Complete");
        $dueOAGPSells = OilAndGasPumpSell::orderby("name","asc")->orderby("created_at","desc")->where("status","Due");

        if(count($request->input()) > 0){
            if($request->has('pagination')){
                $pagination = (in_array($request->pagination,$paginations)) ? $request->pagination : $pagination;
            }

            if($request->has('search') && !($request->search == null)){
                switch ($request->selected_nav_tab) {
                    case 'Due':
                        $dueOAGPSells = $dueOAGPSells->where("name","like","%".$request->search."%")
                                                    ->orWhere("invoice","like","%".$request->search."%")
                                                    ->orWhere("customer","like","%".$request->search."%")
                                                    ->orWhere("customer_info","like","%".$request->search."%")
                                                    ->orWhere("description","like","%".$request->search."%");
                    break;


                    case 'Complete':
                        $completeOAGPSells = $completeOAGPSells->where("name","like","%".$request->search."%")
                                                ->orWhere("invoice","like","%".$request->search."%")
                                                ->orWhere("customer","like","%".$request->search."%")
                                                ->orWhere("customer_info","like","%".$request->search."%")
                                                ->orWhere("description","like","%".$request->search."%");
                    break;

                    default:
                        abort(404,"Unknown nav.");
                    break;
                }
            }
        }

        $completeOAGPSells = $completeOAGPSells->paginate($pagination);
        $dueOAGPSells = $dueOAGPSells->paginate($pagination);

        return view('internal user.oil and gas pump.sell.index',compact("completeOAGPSells","dueOAGPSells","oilAndGasPump","paginations"));
    }

    public function details($oagpSlug,$puSlug){
        $oilAndGasPumpSell = OilAndGasPumpSell::where("slug",$puSlug)->firstOrFail();
        return view('internal user.oil and gas pump.sell.details',compact("oilAndGasPumpSell"));
    }

    public function getProduct($oagpSlug,Request $request){
        $oilAndGasPump = OilAndGasPump::where("slug",$oagpSlug)->firstOrFail();
        $oagpProducts = OilAndGasPumpProduct::orderby("name","asc")->where("oil_and_gas_pump_id",$oilAndGasPump->id);
        if($request->has('selected_products') && (count($request->selected_products) > 0)){
            $oagpProducts = $oagpProducts->whereNotIn("slug", $request->selected_products);
        }
        $oagpProducts = $oagpProducts->get();
        return $oagpProducts;
    }

    public function edit($oagpSlug,$seSlug){
        $oilAndGasPump = OilAndGasPump::where("slug",$oagpSlug)->firstOrFail();
        $oilAndGasPumpProducts = OilAndGasPumpProduct::orderby("name","asc")->where("oil_and_gas_pump_id",$oilAndGasPump->id)->get();

        $oilAndGasPumpSell = OilAndGasPumpSell::where("slug",$seSlug)->firstOrFail();
        return view('internal user.oil and gas pump.sell.edit',compact("oilAndGasPumpSell","oagpSuppliers"));
    }

    public function add($oagpSlug){
        $oilAndGasPump = OilAndGasPump::where("slug",$oagpSlug)->firstOrFail();
        $oilAndGasPumpProducts = OilAndGasPumpProduct::orderby("name","asc")->where("oil_and_gas_pump_id",$oilAndGasPump->id)->get();

        return view('internal user.oil and gas pump.sell.add',compact("oilAndGasPump","oilAndGasPumpProducts"));
    }

    public function addPayment($oagpSlug,$seSlug){
        $oilAndGasPump = OilAndGasPump::where("slug",$oagpSlug)->firstOrFail();
        $oilAndGasPumpSell = OilAndGasPumpSell::where("slug",$seSlug)->firstOrFail();

        if($oilAndGasPumpSell->status == "Due"){
            return view('internal user.oil and gas pump.sell.add payment',compact("oilAndGasPump","oilAndGasPumpSell"));
        }
        else{
            return redirect()->route("oil.and.gas.pump.sell.index",["oagpSlug" => $oilAndGasPump->slug])->with(["errors" => "The sell status is complete. So can not add payment."]);
        }
    }

    public function save($oagpSlug,Request $request){
        $this->oagpSlug = $oagpSlug;

        $validator = Validator::make($request->all(),
            [
                'date' => 'required|date|before_or_equal:today',
                'invoice' => 'required|string|max:200',
                'customer' => 'required',
                'customer_info' => 'nullable',
                'table_row' => 'required|numeric|min:1',
                'product.*' => 'required|distinct',
                'product_inventory.*' => 'required|in:Old,Current',
                'quantity.*' => 'required|numeric|min:0',
                'product_price.*' => 'required|numeric|min:0',
                'product_discount.*' => 'required|numeric|min:0',
                'total_product_price.*' => 'required|numeric|min:0',
                'total_price' => 'required|numeric|min:0',
                'discount' => 'required|numeric|min:0',
                'payable_amount' => 'required|numeric|min:0',
                'paid_amount' => 'required|numeric|min:0',
                'due_amount' => 'required|numeric|min:0',
                'status' => 'required|in:Due,Complete',
                'note' => 'required',
                'description' => 'nullable',
            ],
            [
                'date.required' => 'Date is required.',
                'date.date' => 'Date must be date.',
                'date.before_or_equal' => 'Date must be before or equal today.',

                'invoice.required' => 'Invoice is required.',
                'invoice.string' => 'Invoice is string.',
                'invoice.max' => 'Invoice max length is 200.',
                'invoice.unique' => 'Invoice must be unique.',

                'supplier.required' => 'Supplier is required.',

                'name.required' => 'Name is required.',
                'name.required' => 'Name is required.',
                'name.required' => 'Name max length is 200.',

                'table_row.required' => 'Row count is required.',
                'table_row.numeric' => 'Row count must be numeric.',
                'table_row.required' => 'Row count at lease 1.',

                'product.*.required' => 'Product is required.',
                'product.*.distinct' => 'Product must be unique.',

                'product_inventory.*.required' => 'Product inventory in required',
                'product_inventory.*' => 'Product inventory must be one out of [Old,Current].',

                'product_quantity.*.required' => 'Quantity is required.',
                'product_quantity.*.numeric' => 'Quantity must be numeric.',
                'product_quantity.*.min' => 'Quantity at least 0.',

                'product_price.*.required' => 'Product price is required.',
                'product_price.*.numeric' => 'Product price must be numeric.',
                'product_price.*.min' => 'Product price at least 0.',

                'product_discount.*.required' => 'Product discount is required.',
                'product_discount.*.numeric' => 'Product discount must be numeric.',
                'product_discount.*.min' => 'Productdiscount at least 0.',

                'total_product_price.*.required' => 'Total product price is required.',
                'total_product_price.*.numeric' => 'Total product price must be numeric.',
                'total_product_price.*.min' => 'Total product price at least 0.',

                'total_price.required' => 'Total price is required.',
                'total_price.numeric' => 'Total price must be numeric.',
                'total_price.min' => 'Total price at least 0.',

                'discount.required' => 'Discount is required.',
                'discount.numeric' => 'Discount must be numeric.',
                'discount.min' => 'Discount at least 0.',

                'payable_amount.required' => 'Payable amount is required.',
                'payable_amount.numeric' => 'Payable amount must be numeric.',
                'payable_amount.min' => 'Payable amount at least 0.',

                'paid_amount.required' => 'Paid amount is required.',
                'paid_amount.numeric' => 'Paid amount must be numeric.',
                'paid_amount.min' => 'Paid amount at least 0.',

                'due_amount.required' => 'Due amount is required.',
                'due_amount.numeric' => 'Due amount must be numeric.',
                'due_amount.min' => 'Due amount at least 0.',

                'status.required' => 'Status is required.',
                'status.in' => 'Status must be on out of [Due,Complete].',

                'note.required' => 'Note is required.',
            ]
        );

        $validator->after(function ($validator) {
            $afterValidatorData = $validator->getData();

            $totalPrice = 0;

            $oilAndGasPump = OilAndGasPump::where("slug",$this->oagpSlug)->firstOrFail();
            $sameSellName = OilAndGasPumpSell::where("oil_and_gas_pump_id",$oilAndGasPump->id)->where("name",$afterValidatorData["name"])->count();
            $sameSellInvoice = OilAndGasPumpSell::where("oil_and_gas_pump_id",$oilAndGasPump->id)->where("invoice",$afterValidatorData["invoice"])->count();

            if($sameSellName > 0){
                $validator->errors()->add(
                    'name', "Same name exit."
                );
            }

            if($sameSellInvoice > 0){
                $validator->errors()->add(
                    'invoice', "Same invoice exit."
                );
            }

            for($i = 0; $i < $afterValidatorData["table_row"]; $i++){
                //Product validation
                $productCount = OilAndGasPumpProduct::where("oil_and_gas_pump_id",$oilAndGasPump->id)->where("slug",$afterValidatorData["product"][$i])->count();
                $product = OilAndGasPumpProduct::where("oil_and_gas_pump_id",$oilAndGasPump->id)->where("slug",$afterValidatorData["product"][$i])->firstOrFail();
                $productInventoryCount = OilAndGasPumpInventory::where("oagp_product_id",$product->id)->firstOrFail();

                if($productCount == 0 ){
                    $validator->errors()->add(
                        'product.'.$i, "Unknown product selected."
                    );
                }

                if($productInventoryCount == 0 ){
                    $validator->errors()->add(
                        'product.'.$i, "Product is valid but does not exit in inventory."
                    );
                }

                $productInventory = OilAndGasPumpInventory::where("oagp_product_id",$product->id)->firstOrFail();

                if($afterValidatorData["product_inventory"][$i] == "Old"){
                    // Product price validation
                    if( ($afterValidatorData["product_price"][$i] < $productInventory->old_sell_price) ){
                            $validator->errors()->add(
                                'product_price.'.$i, "Product price can not less then ".$productInventory->old_sell_price."."
                        );
                    }
                    // Product qty validation
                    if( ($afterValidatorData["product_quantity"][$i] > $productInventory->old_quantity) ){
                        $validator->errors()->add(
                            'product_price.'.$i, "Product price can not bigger then ".$productInventory->old_quantity."."
                        );
                    }
                }

                if($afterValidatorData["product_inventory"][$i] == "Current"){
                    // Product price validation
                    if( ($afterValidatorData["product_price"][$i] < $productInventory->sell_price) ){
                            $validator->errors()->add(
                                'product_price.'.$i, "Product price can not less then ".$productInventory->sell_price."."
                        );
                    }
                    // Product qty validation
                    if( ($afterValidatorData["product_quantity"][$i] > $productInventory->quantity) ){
                        $validator->errors()->add(
                            'product_price.'.$i, "Product price can not bigger then ".$productInventory->quantity."."
                        );
                    }
                }

                // Calculate total price
                $totalQuantitySellPrice = $afterValidatorData["product_price"][$i] * $afterValidatorData["product_quantity"][$i];
                $totalQuantityDiscount = $totalQuantitySellPrice * ($afterValidatorData["product_discount"][$i]/100);
                $totalPrice = round(($totalPrice + ($totalQuantitySellPrice - $totalQuantityDiscount)),2);

            }

            // Due status validation
            if (($afterValidatorData["due_amount"] > 0)  && !($afterValidatorData["status"] == "Due")) {
                $validator->errors()->add(
                    'status', "Wrong Status. Status must be 'Due'."
                );
            }

            // Complete status validation
            if (($afterValidatorData["due_amount"] == 0) && !($afterValidatorData["status"] == "Complete")) {
                $validator->errors()->add(
                    'status', "Wrong Status. Status must be 'Complete'."
                );
            }

            // Total price validation
            if(!($totalPrice == $afterValidatorData["total_price"])){
                $validator->errors()->add(
                    'total_price', "Wrong calculation of total price."
                );
            }

            // Pay able amount validation
            if (!($afterValidatorData["payable_amount"] == ($afterValidatorData["total_price"] - ($afterValidatorData["total_price"] * ($afterValidatorData["discount"]/100))))) {
                $validator->errors()->add(
                    'paid_amount', "Wrong payable amount. Payable must be equal to subtraction of total amount and discount."
                );
            }

            // Paid amount validation
            if ( $afterValidatorData["payable_amount"] < $afterValidatorData["paid_amount"]) {
                $validator->errors()->add(
                    'paid_amount', "Wrong paid amount. Paid amount can not greater than payable amount."
                );
            }

            // Due amount validation
            if ( $afterValidatorData["payable_amount"] < $afterValidatorData["due_amount"]) {
                $validator->errors()->add(
                    'paid_amount', "Wrong due amount. Due amount can not greater than payable amount."
                );
            }

            if ( !(($afterValidatorData["payable_amount"] - $afterValidatorData["paid_amount"]) == $afterValidatorData["due_amount"]) ) {
                $validator->errors()->add(
                    'paid_amount', "Wrong due amount. Wrong calculation of due amount."
                );
            }

        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $statusInformation = array("status" => "errors","message" => collect());
        $oilAndGasPump = OilAndGasPump::where("slug",$oagpSlug)->firstOrFail();

        LogBatch::startBatch();
        $oagpSell = new OilAndGasPumpSell();
        $oagpSell->updated_at = null;
        $oagpSell->name = $request->name;
        $oagpSell->date = $request->date;
        $oagpSell->status = $request->status;
        $oagpSell->created_at = Carbon::now();
        $oagpSell->invoice = $request->invoice;
        $oagpSell->note = array($request->note);
        $oagpSell->discount = $request->discount;
        $oagpSell->customer = $request->customer;
        $oagpSell->created_by_id = Auth::user()->id;
        $oagpSell->description = $request->description;

        $oagpSell->customer_info = $request->customer_info;
        $oagpSell->oil_and_gas_pump_id = $oilAndGasPump->id;
        $oagpSell->slug = SystemConstant::slugGenerator($request->name,200);

        $saveSell = $oagpSell->save();
        if($saveSell){
            $oagpSellItemCount = 0;
            $statusInformation["status"] = "status";
            $statusInformation["message"]->push("Sell has been done.");

            for($i = 0; $i < $request->table_row; $i++){
                $product = OilAndGasPumpProduct::where("oil_and_gas_pump_id",$oilAndGasPump->id)->where("slug",$request->product[$i])->firstOrFail();

                $oagpSellItem = new OilAndGasPumpSellItem();
                $oagpSellItem->updated_at = null;
                $oagpSellItem->created_at = Carbon::now();
                $oagpSellItem->oagp_product_id = $product->id;
                $oagpSellItem->created_by_id = Auth::user()->id;
                $oagpSellItem->product_inventory = $request->product_inventory;
                $oagpSellItem->quantity = $request->product_quantity[$i];
                $oagpSellItem->price = $request->product_price[$i];
                $oagpSellItem->discount = $request->product_discount[$i];

                $oagpSellItem->oagp_sell_id =  $oagpSell->id;
                $oagpSellItem->slug = SystemConstant::slugGenerator($request->name." sell Item",200);

                $saveOAGPSellItem =  $oagpSellItem->save();

                if($saveOAGPSellItem){
                    $oagpSellItemCount =  $oagpSellItemCount + 1;
                }
                else{
                    $statusInformation["message"]->push("Fail to save product(".$product->name.").");
                }
            }

            $paymentNote = array();

            if($request->paid_amount == $request->payable_amount){
                array_push($paymentNote,"One time full payment.");
            }

            if($request->payable_amount>$request->paid_amount){
                array_push($paymentNote,"Partial payment 1.");
            }

            $oagpSellPayment = new OilAndGasPumpSellPayment();
            $oagpSellPayment->updated_at = null;
            $oagpSellPayment->note = $paymentNote;
            $oagpSellPayment->created_at = Carbon::now();
            $oagpSellPayment->amount = $request->paid_amount;
            $oagpSellPayment->created_by_id = Auth::user()->id;
            $oagpSellPayment->oagp_sell_id =  $oagpSell->id;
            $oagpSellPayment->slug = SystemConstant::slugGenerator($request->name." sell payment",200);
            $oagpSellPayment->save();

            $this->sendEmail("Add","The sell has been added by ".Auth::user()->name.".", $oagpSell );

            if( $oagpSellItemCount == count($request->product)){
                $inventoryStatus = InventoryConstant::updateProductToInventoryForSellAdd( $oagpSell->slug);

                foreach($inventoryStatus["message"] as $perMessage){
                    $statusInformation["message"]->push($perMessage);
                }
            }
            else{
                $statusInformation["message"]->push("Can not update inventory.Please update the inventory manually.");
            }
            LogBatch::endBatch();
        }
        else{
            $statusInformation["message"]->push("Fail to save.");
        }
        return redirect()->route("oil.and.gas.pump.sell.index",["oagpSlug" => $oilAndGasPump->slug])->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    public function savePayment($oagpSlug,$seSlug,Request $request){
        $this->seSlug = $seSlug;
        $this->oagpSlug = $oagpSlug;

        $validator = Validator::make($request->all(),
            [
                'note' => 'required',
                'description' => 'nullable',
                'amount' => 'required|numeric|min:0',
            ],
            [
                'amount.required' => 'Amount is required.',
                'amount.numeric' => 'Amount must be numeric.',
                'amount.min' => 'Amount at least 0.',

                'note.required' => 'Note is required.',
            ]
        );

        $validator->after(function ($validator) {
            $afterValidatorData = $validator->getData();

            $oilAndGasPump = OilAndGasPump::where("slug",$this->oagpSlug)->firstOrFail();

            $oagpSell = OilAndGasPumpSell::where("oil_and_gas_pump_id",$oilAndGasPump->id)->where("slug",$this->seSlug)->firstOrFail();

            $oagpSellPayable = $oagpSell->oagpSellPayableAmount();
            $oagpSellPaidAmount = $oagpSell->oagpSellTotalPaidAmount() + $afterValidatorData["amount"];

            if($oagpSellPaidAmount > $oagpSellPayable){
                $validator->errors()->add(
                    'amount', "Total payment amount(".$oagpSellPaidAmount.") can not greater then payable amount (".$oagpSellPayable.")."
                );
            }

            if($oagpSellPaidAmount < 0 ){
                $validator->errors()->add(
                    'amount', "Total payment amount(".$oagpSellPaidAmount.") can not less then 0."
                );
            }

            if($oagpSell->oagpSellDueAmount() == 0){
                $validator->errors()->add(
                    'amount', "Can not add payment as the status is complete or due amount is 0."
                );
            }

        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $statusInformation = array("status" => "errors","message" => collect());

        $oilAndGasPump = OilAndGasPump::where("slug",$oagpSlug)->firstOrFail();
        $oilAndGasPumpSell = OilAndGasPumpSell::where("slug",$seSlug)->firstOrFail();

        if($oilAndGasPumpSell->status == "Due"){
            LogBatch::startBatch();
            $oagpSellPayment = new OilAndGasPumpSellPayment();
            $oagpSellPayment->updated_at = null;
            $oagpSellPayment->note = array($request->note);
            $oagpSellPayment->created_at = Carbon::now();
            $oagpSellPayment->amount = $request->amount;
            $oagpSellPayment->created_by_id = Auth::user()->id;
            $oagpSellPayment->oagp_sell_id =  $oilAndGasPumpSell->id;
            $oagpSellPayment->slug = SystemConstant::slugGenerator($oilAndGasPumpSell->name." sell payment",200);
            $saveOAGPSellPayment = $oagpSellPayment->save();
            if($saveOAGPSellPayment){
                $statusInformation["status"]="status";

                if($oilAndGasPumpSell->oagpSellDueAmount() == 0){
                    $oilAndGasPumpSell->status = "Complete";
                    $oilAndGasPumpSell->updated_at = Carbon::now();
                    $oilAndGasPumpSell->update();

                    $statusInformation["message"]->push("Sell status successfully updated.");
                }
                LogBatch::endBatch();
            }
            else{
                $statusInformation["message"]->push("Fail to add payment.");
            }
        }
        else{
            $statusInformation["message"]->push("The sell status is complete. So can not add payment.");
        }

        return redirect()->route("oil.and.gas.pump.sell.index",["oagpSlug" => $oilAndGasPump->slug])->with([$statusInformation["status"] => $statusInformation["message"] ]);
    }

    private function sendEmail($event,$subject,OilAndGasPumpSell $sell ){
        $envelope = array();

        $emailSendSetting = Setting::where( 'code','EmailSendSetting')->firstOrFail()->fields_with_values;

        $envelope["to"] = $emailSendSetting["to"];
        $envelope["cc"] = $emailSendSetting["cc"];
        $envelope["from"] = $emailSendSetting["from"];
        $envelope["reply"] = $emailSendSetting["reply"];

        $moduleSetting = $emailSendSetting["module"]["OilAndGasPumpSell"];

        if(($moduleSetting["send"] == true) && (($moduleSetting["event"] == "All") || (!($moduleSetting["event"] == "All") && ($moduleSetting["event"] == $event)))){
            Mail::send(new EmailSendForOilAndGasPumpSell($event,$envelope,$subject,$sell));
        }
    }
}
