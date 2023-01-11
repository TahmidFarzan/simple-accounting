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
use App\Mail\EmailSendForOilAndGasPumpProduct;

class OilAndGasPumpInventoryController extends Controller
{
    private $oagpSlug = null;
    private $pSlug = null;

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
        $products = OilAndGasPumpProduct::orderby("name","asc")->get();
        $inventories = OilAndGasPumpInventory::orderby("created_at","desc")->orderby("name","asc");

        $inventories = $inventories->get();

        return view('internal user.oil and gas pump.inventory.index',compact("inventories","products","oilAndGasPump"));
    }
}
