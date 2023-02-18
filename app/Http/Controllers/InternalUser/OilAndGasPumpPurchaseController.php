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
use App\Models\OilAndGasPumpPurchase;
use App\Models\OilAndGasPumpSupplier;
use Spatie\Activitylog\Facades\LogBatch;
use Illuminate\Support\Facades\Validator;
use App\Models\OilAndGasPumpPurchaseItem;
use App\Models\OilAndGasPumpPurchasePayment;
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
        $this->middleware(['user.user.permission.check:OAGPPUMP05'])->only(["addPayment","savePayment"]);
    }

    public function index($oagpSlug,Request $request){
        $pagination = 5;
        $paginations = array(5,15,30,45,60,75,90,105,120);
        $oilAndGasPump = OilAndGasPump::where("slug",$oagpSlug)->firstOrFail();
        $completeOAGPPurchases = OilAndGasPumpPurchase::orderby("name","asc")->orderby("created_at","desc")->where("status","Complete");
        $dueOAGPPurchases = OilAndGasPumpPurchase::orderby("name","asc")->orderby("created_at","desc")->where("status","Due");

        if(count($request->input()) > 0){
            if($request->has('pagination')){
                $pagination = (in_array($request->pagination,$paginations)) ? $request->pagination : $pagination;
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

        return view('internal user.oil and gas pump.purchase.index',compact("completeOAGPPurchases","dueOAGPPurchases","oilAndGasPump","paginations"));
    }

    public function details($oagpSlug,$puSlug){
        $oilAndGasPumpPurchase = OilAndGasPumpPurchase::where("slug",$puSlug)->firstOrFail();
        return view('internal user.oil and gas pump.purchase.details',compact("oilAndGasPumpPurchase"));
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

    public function edit($oagpSlug,$puSlug){
        $oilAndGasPump = OilAndGasPump::where("slug",$oagpSlug)->firstOrFail();
        $oagpSuppliers = OilAndGasPumpSupplier::orderby("name","asc")->where("oil_and_gas_pump_id",$oilAndGasPump->id)->get();
        $oilAndGasPumpProducts = OilAndGasPumpProduct::orderby("name","asc")->where("oil_and_gas_pump_id",$oilAndGasPump->id)->get();

        $oilAndGasPumpPurchase = OilAndGasPumpPurchase::where("slug",$puSlug)->firstOrFail();
        return view('internal user.oil and gas pump.purchase.edit',compact("oilAndGasPumpPurchase","oagpSuppliers","oilAndGasPumpProducts"));
    }

    public function add($oagpSlug){
        $oilAndGasPump = OilAndGasPump::where("slug",$oagpSlug)->firstOrFail();
        $oagpSuppliers = OilAndGasPumpSupplier::orderby("name","asc")->where("oil_and_gas_pump_id",$oilAndGasPump->id)->get();
        $oilAndGasPumpProducts = OilAndGasPumpProduct::orderby("name","asc")->where("oil_and_gas_pump_id",$oilAndGasPump->id)->get();

        return view('internal user.oil and gas pump.purchase.add',compact("oilAndGasPump","oagpSuppliers","oilAndGasPumpProducts"));
    }

    public function addPayment($oagpSlug,$puSlug){
        $oilAndGasPump = OilAndGasPump::where("slug",$oagpSlug)->firstOrFail();
        $oilAndGasPumpPurchase = OilAndGasPumpPurchase::where("slug",$puSlug)->firstOrFail();

        if($oilAndGasPumpPurchase->status == "Due"){
            return view('internal user.oil and gas pump.purchase.add payment',compact("oilAndGasPump","oilAndGasPumpPurchase"));
        }
        else{
            return redirect()->route("oil.and.gas.pump.purchase.index",["oagpSlug" => $oilAndGasPump->slug])->with(["errors" => "The purchase status is complete. So can not add payment."]);
        }
    }

    public function save($oagpSlug,Request $request){
        $this->oagpSlug = $oagpSlug;

        $validator = Validator::make($request->all(),
            [
                'date' => 'required|date|before_or_equal:today',
                'invoice' => 'required|string|max:200',
                'supplier' => 'required',
                'table_row' => 'required|numeric|min:1',
                'product.*' => 'required|distinct',
                'quantity.*' => 'required|numeric|min:0',
                'purchase_price.*' => 'required|numeric|min:0',
                'purchase_discount.*' => 'required|numeric|min:0',
                'sell_price.*' => 'required|numeric|min:0',
                'total_purchase_price.*' => 'required|numeric|min:0',
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

                'quantity.*.required' => 'Quantity is required.',
                'quantity.*.numeric' => 'Quantity must be numeric.',
                'quantity.*.min' => 'Quantity at least 0.',

                'purchase_price.*.required' => 'Purchase price is required.',
                'purchase_price.*.numeric' => 'Purchase price must be numeric.',
                'purchase_price.*.min' => 'Purchase price at least 0.',

                'purchase_discount.*.required' => 'Row discount is required.',
                'purchase_discount.*.numeric' => 'Row discount must be numeric.',
                'purchase_discount.*.min' => 'Purchase discount at least 0.',

                'sell_price.*.required' => 'Sell price is required.',
                'sell_price.*.numeric' => 'Sell price must be numeric.',
                'sell_discount.*.min' => 'Sell discount at least 0.',

                'total_purchase_price.*.required' => 'Total purchase price is required.',
                'total_purchase_price.*.numeric' => 'Total purchase price must be numeric.',
                'total_purchase_price.*.min' => 'Total purchase price at least 0.',

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
            $oagpSupplier = OilAndGasPumpSupplier::where("slug",$afterValidatorData["supplier"])->count();
            $samePurchaseName = OilAndGasPumpPurchase::where("oil_and_gas_pump_id",$oilAndGasPump->id)->where("name",$afterValidatorData["name"])->count();
            $samePurchaseInvoice = OilAndGasPumpPurchase::where("oil_and_gas_pump_id",$oilAndGasPump->id)->where("invoice",$afterValidatorData["invoice"])->count();

            if($oagpSupplier == 0){
                $validator->errors()->add(
                    'supplier', "Unknown supplier."
                );
            }

            if($samePurchaseName > 0){
                $validator->errors()->add(
                    'name', "Same name exit."
                );
            }

            if($samePurchaseInvoice > 0){
                $validator->errors()->add(
                    'invoice', "Same invoice exit."
                );
            }

            for($i = 0; $i < $afterValidatorData["table_row"]; $i++){
                //Product validation
                $productCount = OilAndGasPumpProduct::where("oil_and_gas_pump_id",$oilAndGasPump->id)->where("slug",$afterValidatorData["product"][$i])->count();

                if($productCount == 0 ){
                    $validator->errors()->add(
                        'product.'.$i, "Unknown product selected."
                    );
                }

                // Product sell price validation
                if( $afterValidatorData["sell_price"][$i] < $afterValidatorData["purchase_price"][$i] ){
                    $validator->errors()->add(
                        'sell_price.'.$i, "Sell price can not less the purchase price."
                    );
                }

                // Product purchase price validation
                if(!(($afterValidatorData["purchase_price"][$i] < $afterValidatorData["sell_price"][$i]) || ($afterValidatorData["purchase_price"][$i] == $afterValidatorData["sell_price"][$i])) ){
                    $validator->errors()->add(
                        'purchase_price.'.$i, "Purchase price must be equal or less then sell price."
                    );
                }

                // Calculate total price
                $totalQuantityPurchasePrice = $afterValidatorData["purchase_price"][$i] * $afterValidatorData["quantity"][$i];
                $totalQuantityDiscount = $totalQuantityPurchasePrice * ($afterValidatorData["purchase_discount"][$i]/100);
                $totalPrice = round(($totalPrice + ($totalQuantityPurchasePrice - $totalQuantityDiscount)),2);

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
        $oagpSupplier = OilAndGasPumpSupplier::where("oil_and_gas_pump_id",$oilAndGasPump->id)->where("slug",$request->supplier)->firstOrFail();

        LogBatch::startBatch();
        $oagpPurchase = new OilAndGasPumpPurchase();
        $oagpPurchase->updated_at = null;
        $oagpPurchase->name = $request->name;
        $oagpPurchase->date = $request->date;
        $oagpPurchase->status = $request->status;
        $oagpPurchase->created_at = Carbon::now();
        $oagpPurchase->invoice = $request->invoice;
        $oagpPurchase->note = array($request->note);
        $oagpPurchase->discount = $request->discount;
        $oagpPurchase->created_by_id = Auth::user()->id;
        $oagpPurchase->description = $request->description;
        $oagpPurchase->oagp_supplier_id = $oagpSupplier->id;
        $oagpPurchase->oil_and_gas_pump_id = $oilAndGasPump->id;
        $oagpPurchase->slug = SystemConstant::slugGenerator($request->name,200);

        $savePurchase = $oagpPurchase->save();
        if($savePurchase){
            $oagpPurchaseItemCount = 0;
            $statusInformation["status"] = "status";
            $statusInformation["message"]->push("Purchase has been done.");

            for($i = 0; $i < $request->table_row; $i++){
                $product = OilAndGasPumpProduct::where("oil_and_gas_pump_id",$oilAndGasPump->id)->where("slug",$request->product[$i])->firstOrFail();

                $oagpPurchaseItem = new OilAndGasPumpPurchaseItem();
                $oagpPurchaseItem->updated_at = null;
                $oagpPurchaseItem->created_at = Carbon::now();
                $oagpPurchaseItem->oagp_product_id = $product->id;
                $oagpPurchaseItem->created_by_id = Auth::user()->id;
                $oagpPurchaseItem->quantity = $request->quantity[$i];
                $oagpPurchaseItem->sell_price = $request->sell_price[$i];
                $oagpPurchaseItem->discount = $request->purchase_discount[$i];
                $oagpPurchaseItem->purchase_price = $request->purchase_price[$i];
                $oagpPurchaseItem->oagp_purchase_id =  $oagpPurchase->id;
                $oagpPurchaseItem->slug = SystemConstant::slugGenerator($request->name." purchase Item",200);

                $saveOAGPPurchaseItem =  $oagpPurchaseItem->save();

                if($saveOAGPPurchaseItem){
                    $oagpPurchaseItemCount =  $oagpPurchaseItemCount + 1;
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

            $oagpPurchasePayment = new OilAndGasPumpPurchasePayment();
            $oagpPurchasePayment->updated_at = null;
            $oagpPurchasePayment->note = $paymentNote;
            $oagpPurchasePayment->created_at = Carbon::now();
            $oagpPurchasePayment->amount = $request->paid_amount;
            $oagpPurchasePayment->created_by_id = Auth::user()->id;
            $oagpPurchasePayment->oagp_purchase_id =  $oagpPurchase->id;
            $oagpPurchasePayment->slug = SystemConstant::slugGenerator($request->name." purchase payment",200);
            $oagpPurchasePayment->save();

            $this->sendEmail("Add","The purchase has been added by ".Auth::user()->name.".", $oagpPurchase );

            if( $oagpPurchaseItemCount == count($request->product)){
                $inventoryStatus = InventoryConstant::updateProductToInventoryForPurachaseAdd( $oagpPurchase->slug);

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
        return redirect()->route("oil.and.gas.pump.purchase.index",["oagpSlug" => $oilAndGasPump->slug])->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    public function update($oagpSlug,$puSlug,Request $request){
        $this->puSlug = $puSlug;
        $this->oagpSlug = $oagpSlug;

        $validator = Validator::make($request->all(),
            [
                'date' => 'required|date|before_or_equal:today',
                'invoice' => 'required|string|max:200',
                'supplier' => 'required',
                'table_row' => 'required|numeric|min:1',
                'product.*' => 'required|distinct',
                'quantity.*' => 'required|numeric|min:0',
                'purchase_price.*' => 'required|numeric|min:0',
                'purchase_discount.*' => 'required|numeric|min:0',
                'sell_price.*' => 'required|numeric|min:0',
                'total_purchase_price.*' => 'required|numeric|min:0',
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

                'quantity.*.required' => 'Quantity is required.',
                'quantity.*.numeric' => 'Quantity must be numeric.',
                'quantity.*.min' => 'Quantity at least 0.',

                'purchase_price.*.required' => 'Purchase price is required.',
                'purchase_price.*.numeric' => 'Purchase price must be numeric.',
                'purchase_price.*.min' => 'Purchase price at least 0.',

                'purchase_discount.*.required' => 'Row discount is required.',
                'purchase_discount.*.numeric' => 'Row discount must be numeric.',
                'purchase_discount.*.min' => 'Purchase discount at least 0.',

                'sell_price.*.required' => 'Sell price is required.',
                'sell_price.*.numeric' => 'Sell price must be numeric.',
                'sell_discount.*.min' => 'Sell discount at least 0.',

                'total_purchase_price.*.required' => 'Total purchase price is required.',
                'total_purchase_price.*.numeric' => 'Total purchase price must be numeric.',
                'total_purchase_price.*.min' => 'Total purchase price at least 0.',

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
            $oagpSupplier = OilAndGasPumpSupplier::where("slug",$afterValidatorData["supplier"])->count();

            $samePurchaseName = OilAndGasPumpPurchase::where("oil_and_gas_pump_id",$oilAndGasPump->id)->whereNot("name",$afterValidatorData["name"])->where("slug",$this->puSlug)->count();
            $samePurchaseInvoice = OilAndGasPumpPurchase::where("oil_and_gas_pump_id",$oilAndGasPump->id)->whereNot("invoice",$afterValidatorData["invoice"])->where("slug",$this->puSlug)->count();

            if($samePurchaseName > 0){
                $validator->errors()->add(
                    'name', "Same name exit."
                );
            }

            if($oagpSupplier == 0){
                $validator->errors()->add(
                    'supplier', "Unknown supplier."
                );
            }

            if($samePurchaseInvoice > 0){
                $validator->errors()->add(
                    'invoice', "Same invoice exit."
                );
            }

            for($i = 0; $i < $afterValidatorData["table_row"]; $i++){
                //Product validation
                $productCount = OilAndGasPumpProduct::where("oil_and_gas_pump_id",$oilAndGasPump->id)->where("slug",$afterValidatorData["product"][$i])->count();

                if($productCount == 0 ){
                    $validator->errors()->add(
                        'product.'.$i, "Unknown product selected."
                    );
                }

                // Product sell price validation
                if( $afterValidatorData["sell_price"][$i] < $afterValidatorData["purchase_price"][$i] ){
                    $validator->errors()->add(
                        'sell_price.'.$i, "Sell price can not less the purchase price."
                    );
                }

                // Product purchase price validation
                if(!(($afterValidatorData["purchase_price"][$i] < $afterValidatorData["sell_price"][$i]) || ($afterValidatorData["purchase_price"][$i] == $afterValidatorData["sell_price"][$i])) ){
                    $validator->errors()->add(
                        'purchase_price.'.$i, "Purchase price must be equal or less then sell price."
                    );
                }

                // Calculate total price
                $totalQuantityPurchasePrice = $afterValidatorData["purchase_price"][$i] * $afterValidatorData["quantity"][$i];
                $totalQuantityDiscount = $totalQuantityPurchasePrice * ($afterValidatorData["purchase_discount"][$i]/100);
                $totalPrice = round(($totalPrice + ($totalQuantityPurchasePrice - $totalQuantityDiscount)),2);

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
        $purchaseNote = array();
        $statusInformation = array("status" => "errors","message" => collect());
        $oilAndGasPump = OilAndGasPump::where("slug",$oagpSlug)->firstOrFail();
        $oilAndGasPumpSupplier = OilAndGasPumpSupplier::where("oil_and_gas_pump_id",$oilAndGasPump->id)->where("slug",$request->supplier)->firstOrFail();

        LogBatch::startBatch();
        $oagpPurchase = OilAndGasPumpPurchase::where("slug",$puSlug)->firstOrFail();
        $purchaseNote =  $oagpPurchase->note;
        array_push($purchaseNote,$request->note);

        $oagpPurchase->note = $purchaseNote;
        $oagpPurchase->name = $request->name;
        $oagpPurchase->date = $request->date;
        $oagpPurchase->status = $request->status;
        $oagpPurchase->updated_at =  Carbon::now();
        $oagpPurchase->invoice = $request->invoice;
        $oagpPurchase->discount = $request->discount;
        $oagpPurchase->description = $request->description;
        $oagpPurchase->oil_and_gas_pump_id = $oilAndGasPump->id;
        $oagpPurchase->oagp_supplier_id = $oilAndGasPumpSupplier->id;

        $savePurchase =  $oagpPurchase->update();

        if($savePurchase){
            $oagpPurchaseItemCount = 0;
            $statusInformation["status"] = "status";
            $statusInformation["message"]->push("Purchase has been updated.");

            for($i = 0; $i < $request->table_row; $i++){
                $puItemSlug = null;
                $updatedQuentity = 0;
                $requestType = "AddItem";
                $product = OilAndGasPumpProduct::where("oil_and_gas_pump_id",$oilAndGasPump->id)->where("slug",$request->product[$i])->firstOrFail();
                $productCountInItem = OilAndGasPumpPurchaseItem::where("oagp_purchase_id", $oagpPurchase->id)->where("oagp_product_id",$product->id)->count();

                if($productCountInItem == 0){
                    $requestType = "AddItem";
                    $oagpPurchaseNewItem = new OilAndGasPumpPurchaseItem();
                    $oagpPurchaseNewItem->updated_at = null;
                    $oagpPurchaseNewItem->created_at = Carbon::now();
                    $oagpPurchaseNewItem->created_by_id = Auth::user()->id;
                    $oagpPurchaseNewItem->quantity = $request->quantity[$i];
                    $oagpPurchaseNewItem->sell_price = $request->sell_price[$i];
                    $oagpPurchaseNewItem->discount = $request->purchase_discount[$i];
                    $oagpPurchaseNewItem->purchase_price = $request->purchase_price[$i];
                    $oagpPurchaseNewItem->oagp_purchase_id =  $oagpPurchase->id;
                    $oagpPurchaseNewItem->slug = SystemConstant::slugGenerator($request->name." purchase Item",200);

                    $saveOAGPPurchaseNewItem = $oagpPurchaseNewItem->save();

                    $puItemSlug = $oagpPurchaseNewItem->slug;

                    if($saveOAGPPurchaseNewItem){
                        $oagpPurchaseItemCount =  $oagpPurchaseItemCount + 1;
                    }
                    else{
                        $statusInformation["message"]->push("Fail to save product(".$product->name.").");
                    }
                }
                else{
                    $oldOAGPPurchaseItem = OilAndGasPumpPurchaseItem::where("oagp_purchase_id", $oagpPurchase->id)->where("oagp_product_id",$product->id)->firstOrFail();

                    $requestType = "UpdateItem";
                    $updatedQuentity = $request->quantity[$i] - $oldOAGPPurchaseItem->quantity;

                    $oagpPurchaseItem = OilAndGasPumpPurchaseItem::where("oagp_purchase_id", $oagpPurchase->id)->where("oagp_product_id",$product->id)->firstOrFail();
                    $oagpPurchaseItem->updated_at = Carbon::now();
                    $oagpPurchaseItem->oagp_product_id = $product->id;
                    $oagpPurchaseItem->quantity = $request->quantity[$i];
                    $oagpPurchaseItem->sell_price = $request->sell_price[$i];
                    $oagpPurchaseItem->discount = $request->purchase_discount[$i];
                    $oagpPurchaseItem->purchase_price = $request->purchase_price[$i];
                    $oagpPurchaseItem->oagp_purchase_id =  $oagpPurchase->id;

                    $updateOAGPPurchaseItem = $oagpPurchaseItem->update();

                    $puItemSlug = $oagpPurchaseItem->slug;

                    if($updateOAGPPurchaseItem){
                        $oagpPurchaseItemCount =  $oagpPurchaseItemCount + 1;
                    }
                    else{
                        $statusInformation["message"]->push("Fail to update product(".$product->name.").");
                    }
                }

                $inventoryStatus = InventoryConstant::updateProductToInventoryForPurachaseUpdate($puSlug,$puItemSlug,$requestType,$updatedQuentity);

                if($inventoryStatus["status"] == "status"){
                    $oagpPurchaseItemCount = $oagpPurchaseItemCount +1 ;
                }
            }

            $this->sendEmail("Update","The purchase has been updated by ".Auth::user()->name.".", $oagpPurchase );

            if( $oagpPurchaseItemCount == count($request->product)){
                $statusInformation["message"]->push("Inventory successfully updated.");
            }
            else{
                $statusInformation["message"]->push("Can not update inventory.Please update the inventory manually.");
            }

            LogBatch::endBatch();
        }
        else{
            $statusInformation["message"]->push("Fail to save.");
        }

        return redirect()->route("oil.and.gas.pump.purchase.index",["oagpSlug" => $oilAndGasPump->slug])->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    public function savePayment($oagpSlug,$puSlug,Request $request){
        $this->puSlug = $puSlug;
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

            $oagpPurchase = OilAndGasPumpPurchase::where("oil_and_gas_pump_id",$oilAndGasPump->id)->where("slug",$this->puSlug)->firstOrFail();

            $oagpPuchasePayable = $oagpPurchase->oagpPurchasePayableAmount();
            $oagpPuchasePaidAmount = $oagpPurchase->oagpPurchaseTotalPaidAmount() + $afterValidatorData["amount"];

            if($oagpPuchasePaidAmount > $oagpPuchasePayable){
                $validator->errors()->add(
                    'amount', "Total payment amount(".$oagpPuchasePaidAmount.") can not greater then payable amount (".$oagpPuchasePayable.")."
                );
            }

            if($oagpPuchasePaidAmount < 0 ){
                $validator->errors()->add(
                    'amount', "Total payment amount(".$oagpPuchasePaidAmount.") can not less then 0."
                );
            }

            if($oagpPurchase->oagpPurchaseDueAmount() == 0){
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
        $oilAndGasPumpPurchase = OilAndGasPumpPurchase::where("slug",$puSlug)->firstOrFail();

        if($oilAndGasPumpPurchase->status == "Due"){
            LogBatch::startBatch();
            $oagpPurchasePayment = new OilAndGasPumpPurchasePayment();
            $oagpPurchasePayment->updated_at = null;
            $oagpPurchasePayment->note = array($request->note);
            $oagpPurchasePayment->created_at = Carbon::now();
            $oagpPurchasePayment->amount = $request->amount;
            $oagpPurchasePayment->created_by_id = Auth::user()->id;
            $oagpPurchasePayment->oagp_purchase_id =  $oilAndGasPumpPurchase->id;
            $oagpPurchasePayment->slug = SystemConstant::slugGenerator($oilAndGasPumpPurchase->name." purchase payment",200);
            $saveOAGPPurchasePayment = $oagpPurchasePayment->save();
            if($saveOAGPPurchasePayment){
                $statusInformation["status"]="status";

                if($oilAndGasPumpPurchase->oagpPurchaseDueAmount() == 0){
                    $oilAndGasPumpPurchase->status = "Complete";
                    $oilAndGasPumpPurchase->updated_at = Carbon::now();
                    $oilAndGasPumpPurchase->update();

                    $statusInformation["message"]->push("Purchase status successfully updated.");
                }
                LogBatch::endBatch();
            }
            else{
                $statusInformation["message"]->push("Fail to add payment.");
            }
        }
        else{
            $statusInformation["message"]->push("The purchase status is complete. So can not add payment.");
        }

        return redirect()->route("oil.and.gas.pump.purchase.index",["oagpSlug" => $oilAndGasPump->slug])->with([$statusInformation["status"] => $statusInformation["message"] ]);
    }

    private function sendEmail($event,$subject,OilAndGasPumpPurchase $purchase ){
        $envelope = array();

        $emailSendSetting = Setting::where( 'code','EmailSendSetting')->firstOrFail()->fields_with_values;

        $envelope["to"] = $emailSendSetting["to"];
        $envelope["cc"] = $emailSendSetting["cc"];
        $envelope["from"] = $emailSendSetting["from"];
        $envelope["reply"] = $emailSendSetting["reply"];

        $moduleSetting = $emailSendSetting["module"]["OilAndGasPumpPurchase"];

        if(($moduleSetting["send"] == true) && (($moduleSetting["event"] == "All") || (!($moduleSetting["event"] == "All") && ($moduleSetting["event"] == $event)))){
            Mail::send(new EmailSendForOilAndGasPumpPurchase($event,$envelope,$subject,$purchase));
        }
    }
}
