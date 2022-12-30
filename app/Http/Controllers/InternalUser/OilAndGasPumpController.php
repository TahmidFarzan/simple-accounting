<?php

namespace App\Http\Controllers\InternalUser;

use Carbon\Carbon;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\OilAndGasPump;
use App\Utilities\SystemConstant;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailSendForOilAndGasPump;
use Spatie\Activitylog\Facades\LogBatch;
use Illuminate\Support\Facades\Validator;


class OilAndGasPumpController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
        $this->middleware(['user.user.permission.check:OAGPMP01'])->only(["index"]);
        $this->middleware(['user.user.permission.check:OAGPMP02'])->only(["create","save"]);
        $this->middleware(['user.user.permission.check:OAGPMP03'])->only(["details"]);
        $this->middleware(['user.user.permission.check:OAGPMP04'])->only(["edit","update"]);
        $this->middleware(['user.user.permission.check:OAGPMP05'])->only(["delete"]);
    }

    public function index(Request $request){
        $pagination = 5;
        $paginations = array(5,15,30,45,60,75,90,105,120);

        $oilAndGasPumps = OilAndGasPump::orderby("created_at","desc")->orderby("name","asc");

        if(count($request->input()) > 0){
            if($request->has('pagination')){
                $pagination = (in_array($request->pagination,$paginations)) ? $request->pagination : $pagination;
            }

            if($request->has('search') && !($request->search == null)){
                $oilAndGasPumps = $oilAndGasPumps->where("name","like","%".$request->search."%")
                                                ->orWhere("description","like","%".$request->search."%")
                                                ->orWhere("code","like","%".$request->search."%");
            }
        }

        $oilAndGasPumps = $oilAndGasPumps->paginate($pagination);

        return view('internal user.oil and gas pump.oil and gas pump.index',compact("oilAndGasPumps","paginations"));
    }

    public function create(){
        return view('internal user.oil and gas pump.oil and gas pump.create');
    }

    public function save(Request $request){
        $validator = Validator::make($request->all(),
            [
                'name' => 'required|max:200',
                'code' => 'required|max:200|unique:oil_and_gas_pumps,code',
                'note' => 'required',
                'description' => 'nullable',
            ],
            [
                'name.required' => 'Name is required.',
                'name.max' => 'Name length can not greater then 200 chars.',

                'code.required' => 'Code is required.',
                'code.max' => 'Code length can not greater then 200 chars.',
                'code.unique' => 'Code must be unique.',

                'note.required' => 'Note is required.',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $statusInformation = array("status" => "errors","message" => collect());

        LogBatch::startBatch();
            $oilAndGasPump = new OilAndGasPump();
            $oilAndGasPump->name = $request->name;
            $oilAndGasPump->code = $request->code;
            $oilAndGasPump->description = $request->description;
            $oilAndGasPump->note = array($request->note);
            $oilAndGasPump->slug = SystemConstant::slugGenerator($request->name,200);
            $oilAndGasPump->created_at = Carbon::now();
            $oilAndGasPump->created_by_id = Auth::user()->id;
            $oilAndGasPump->updated_at = null;
            $saveOilAndGasPump = $oilAndGasPump->save();
        LogBatch::endBatch();

        if($saveOilAndGasPump){
            $this->sendEmail("Create","A new oil and gas pump has been created by ".Auth::user()->name.".",$oilAndGasPump );

            $statusInformation["status"] = "status";
            $statusInformation["message"] = "Successfully created.";
        }
        else{
            $statusInformation["status"] = "errors";
            $statusInformation["message"]->push("Can not update.");
        }

        return redirect()->route("oil.and.gas.pump.index")->with([$statusInformation["status"] => $statusInformation["message"]]);
    }


    private function sendEmail($event,$subject,OilAndGasPump $oilAndGasPump ){
        $envelope = array();

        $emailSendSetting = Setting::where( 'code','EmailSendSetting')->firstOrFail()->fields_with_values;

        $envelope["to"] = $emailSendSetting["to"];
        $envelope["cc"] = $emailSendSetting["cc"];
        $envelope["from"] = $emailSendSetting["from"];
        $envelope["reply"] = $emailSendSetting["reply"];

        $moduleSetting = $emailSendSetting["module"]["OilAndGasPump"];

        if(($moduleSetting["send"] == true) && (($moduleSetting["event"] == "All") || (!($moduleSetting["event"] == "All") && ($moduleSetting["event"] == $event)))){
            Mail::send(new EmailSendForOilAndGasPump($event,$envelope,$subject,$oilAndGasPump));
        }
    }
}
