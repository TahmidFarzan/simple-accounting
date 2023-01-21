<?php

namespace App\Http\Controllers\InternalUser;

use Carbon\Carbon;
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

    public function save($oagpSlug,Request $request){
        $this->oagpSlug = $oagpSlug;

        $validator = Validator::make($request->all(),
            [
                'date' => 'required|date|before_or_equal:today',
                'invoice' => 'required|string|max:200|unique:oil_and_gas_pump_purchases,invoice',
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

            if($oagpSupplier == 0){
                $validator->errors()->add(
                    'supplier', "Unknown supplier."
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
        return $request;

        $oilAndGasPumpPurchase = new OilAndGasPumpPurchase();
        $oilAndGasPumpPurchase->updated_at = null;
        $oilAndGasPumpPurchase->name = $request->name;
        $oilAndGasPumpPurchase->date = $request->date;
        $oilAndGasPumpPurchase->status = $request->status;
        $oilAndGasPumpPurchase->created_at = Carbon::now();
        $oilAndGasPumpPurchase->invoice = $request->invoice;
        $oilAndGasPumpPurchase->note = array($request->note);
        $oilAndGasPumpPurchase->discount = $request->discount;
        $oilAndGasPumpPurchase->created_by_id = Auth::user()->id;
        $oilAndGasPumpPurchase->paid_amount = $request->paid_amount;
        $oilAndGasPumpPurchase->description = $request->description;
        $oilAndGasPumpPurchase->slug = SystemConstant::slugGenerator($request->name,200);
        $oilAndGasPumpPurchase->oagp_supplier_id = OilAndGasPumpSupplier::where("oil_and_gas_pump_id",$oilAndGasPump->id)->where("slug",$request->supplier)->firstOrFail()->id;

        $savePurchase = $oilAndGasPumpPurchase->save();

        if($savePurchase){
            $oilAndGasPumpPurchaseItemCount = 0;
            $statusInformation["status"] = "success";
            $statusInformation["message"]->push("Purchase has been done.");
            for($i = 0; $i < $request->table_row; $i++){
                $product = OilAndGasPumpProduct::where("oil_and_gas_pump_id",$oilAndGasPump->id)->where("slug",$request->product[$i])->firstOrFail();

                $oilAndGasPumpPurchaseItem = new OilAndGasPumpPurchaseItem();
                $oilAndGasPumpPurchaseItem->updated_at = null;
                $oilAndGasPumpPurchaseItem->created_at = Carbon::now();
                $oilAndGasPumpPurchaseItem->oagp_product_id = $product->id;
                $oilAndGasPumpPurchaseItem->created_by_id = Auth::user()->id;
                $oilAndGasPumpPurchaseItem->quantity = $request->quantity[$i];
                $oilAndGasPumpPurchaseItem->sell_price = $request->sell_price[$i];
                $oilAndGasPumpPurchaseItem->purchase_price = $request->purchase_price[$i];
                $oilAndGasPumpPurchaseItem->oagp_purchase_id = $oilAndGasPumpPurchase->id;
                $oilAndGasPumpPurchaseItem->slug = SystemConstant::slugGenerator($request->name." Item",200);

                $oilAndGasPumpPurchaseItem = $oilAndGasPumpPurchaseItem->save();

                if($oilAndGasPumpPurchaseItem){
                    $oilAndGasPumpPurchaseItemCount = $oilAndGasPumpPurchaseItemCount + 1;
                }
                else{
                    $statusInformation["message"]->push("Fail to save product(".$product.").");
                }
            }

            if($oilAndGasPumpPurchaseItemCount == count($request->product)){
                $inventoryStatus = InventoryConstant::updateProductToInventoryForPurachase($oilAndGasPumpPurchase->slug);

                foreach($inventoryStatus["message"] as $perMessage){
                    $statusInformation["message"]->push($perMessage);
                }
            }
            else{
                $statusInformation["message"]->push("Can not update inventory.Please update the inventory manually.");
            }
        }
        else{
            $statusInformation["message"]->push("Fail to save.");
        }

        return redirect()->route("oil.and.gas.pump.purchase.index",["oagpSlug" => $oilAndGasPump->slug])->with([$statusInformation["status"] => $statusInformation["message"]]);
    }
}
