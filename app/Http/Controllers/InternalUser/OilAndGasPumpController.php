<?php

namespace App\Http\Controllers\InternalUser;

use Exception;
use Carbon\Carbon;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\OilAndGasPump;
use App\Utilities\SystemConstant;
use Illuminate\Support\Facades\DB;
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
        $pagination = 1;
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

    public function details($slug){
        $oilAndGasPump = OilAndGasPump::where("slug",$slug)->firstOrFail();
        return view('internal user.oil and gas pump.oil and gas pump.details',compact("oilAndGasPump"));
    }

    public function edit($slug){
        $oilAndGasPump = OilAndGasPump::where("slug",$slug)->firstOrFail();
        return view('internal user.oil and gas pump.oil and gas pump.edit',compact("oilAndGasPump"));
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

        try{
            DB::beginTransaction();
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
                DB::commit();
                $this->sendEmail("Create","A new oil and gas pump has been created by ".Auth::user()->name.".",$oilAndGasPump );

                $statusInformation["status"] = "status";
                $statusInformation["message"] = "Successfully created.";
            }
            else{
                DB::rollBack();
                $statusInformation["status"] = "errors";
                $statusInformation["message"]->push("Can not update.");
            }
        }
        catch (Exception $e) {
            DB::rollBack();
            $statusInformation["status"] = "errors";
            $statusInformation["message"]->push("Error info: ".$e);
        }

        return redirect()->route("oil.and.gas.pump.index")->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    public function update(Request $request,$slug){
        $oilAndGasPumpId = OilAndGasPump::where("slug",$slug)->firstOrFail()->id;
        $validator = Validator::make($request->all(),
            [
                'name' => 'required|max:200',
                'code' => 'required|max:200|unique:oil_and_gas_pumps,code,'.$oilAndGasPumpId,
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

        try{
            DB::beginTransaction();
            LogBatch::startBatch();
                $oilAndGasPump = OilAndGasPump::where("slug",$slug)->firstOrFail();
                $notes = $oilAndGasPump->note;
                array_push($notes,$request->note);

                $oilAndGasPump->name = $request->name;
                $oilAndGasPump->code = $request->code;
                $oilAndGasPump->description = $request->description;
                $oilAndGasPump->note = $notes;
                $oilAndGasPump->slug = SystemConstant::slugGenerator($request->name,200);
                $oilAndGasPump->updated_at = Carbon::now();
                $updateOilAndGasPump = $oilAndGasPump->update();
            LogBatch::endBatch();

            if($updateOilAndGasPump){
                DB::commit();
                $this->sendEmail("Update","The oil and gas pump has been updated by ".Auth::user()->name.".",$oilAndGasPump );

                $statusInformation["status"] = "status";
                $statusInformation["message"] = "Successfully updated.";
            }
            else{
                DB::rollBack();
                $statusInformation["status"] = "errors";
                $statusInformation["message"]->push("Can not update.");
            }
        }
        catch (Exception $e) {
            DB::rollBack();
            $statusInformation["status"] = "errors";
            $statusInformation["message"]->push("Error info: ".$e);
        }

        return redirect()->route("oil.and.gas.pump.index")->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    public function delete($slug){
        $statusInformation = array("status" => "errors","message" => collect());

        $oilAndGasPumpValidationStatus = $this->oilAndGasPumpValidation($slug);

        if($oilAndGasPumpValidationStatus["status"] == "status"){
            try{
                DB::beginTransaction();
                LogBatch::startBatch();
                    $oilAndGasPump = OilAndGasPump::where("slug",$slug)->firstOrFail();
                    $deleteOilAndGasPump = $oilAndGasPump->delete();
                LogBatch::endBatch();
                if($deleteOilAndGasPump){
                    DB::commit();
                    $this->sendEmail("Delete","Oil and gas pump has been delete by ".Auth::user()->name.".",$oilAndGasPump );

                    $statusInformation["status"] = "status";
                    $statusInformation["message"]->push("Successfully deleted.");
                }
                else{
                    DB::rollBack();
                    $statusInformation["status"] = "errors";
                    $statusInformation["message"]->push("Fail to delete.");
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
            $statusInformation["message"]->push("Fail to delete.");

            foreach($oilAndGasPumpValidationStatus["message"] as $perMessage){
                $statusInformation["message"]->push($perMessage);
            }
        }

        return redirect()->route("project.contract.payment.index",["pcSlug" => $slug])->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    private function oilAndGasPumpValidation($slug){
        $statusInformation = array("status" => "errors","message" => collect());

        $oilAndGasPump = OilAndGasPump::where("slug",$slug)->firstOrFail();

        if( $oilAndGasPump->oilAndGasPumpProducts->count() == 0){
            $statusInformation["status"] = "status";
            $statusInformation["message"]->push("Passed the validation.");
        }
        else{
            $statusInformation["status"] = "errors";
            $statusInformation["message"]->push($oilAndGasPump->oilAndGasPumpProducts->count()." product(s) exit.");
            $statusInformation["message"]->push("The oil and gas pump can not delete.");
        }

        return $statusInformation;
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
