<?php

namespace App\Http\Controllers\InternalUser;

use Carbon\Carbon;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\ProjectContract;
use App\Utilities\SystemConstant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Models\ProjectContractPayment;
use Spatie\Activitylog\Facades\LogBatch;
use Illuminate\Support\Facades\Validator;
use App\Models\ProjectContractPaymentMethod;
use App\Mail\EmailSendForProjectContractPayment;

class ProjectContractPaymentController extends Controller
{
    private $pcSlug = null;

    public function __construct()
    {
        $this->middleware(['auth','verified']);
        $this->middleware(['user.user.permission.check:PCPMP01'])->only(["index"]);
        $this->middleware(['user.user.permission.check:PCPMP02'])->only(["create","save"]);
        $this->middleware(['user.user.permission.check:PCPMP03'])->only(["details"]);
        $this->middleware(['user.user.permission.check:PCPMP04'])->only(["edit","update"]);
        $this->middleware(['user.user.permission.check:PCPMP05'])->only(["delete"]);
    }

    public function index(Request $request,$pcSlug){
        $pagination = 5;
        $paginations = array(5,15,30,45,60,75,90,105,120);
        $projectContract = ProjectContract::where("slug",$pcSlug)->firstOrFail();

        $projectContractPayments = ProjectContractPayment::orderby("created_at","desc")->orderby("name","asc")->where("project_contract_id",$projectContract->id);

        if(count($request->input()) > 0){
            if($request->has('pagination')){
                $pagination = (in_array($request->pagination,$paginations)) ? $request->pagination : $pagination;
            }

            if($request->has('payment_date') && !($request->payment_date == null) && $request->has('payment_date_condition') && !($request->payment_date_condition == null) && in_array($request->payment_date_condition, array("=",">","<",">=","<="))){
                $projectContractPayments = $projectContractPayments->where(DB::raw("(STR_TO_DATE(payment_date,'%Y-%m-%d'))"),$request->payment_date_condition,date('Y-m-d',strtotime($request->payment_date)) );
            }

            if($request->has('search') && !($request->search == null)){
                $projectContractPayments = $projectContractPayments->where("name","like","%".$request->search."%")
                                                                        ->orWhere("description","like","%".$request->search."%");
            }
        }

        $projectContractPayments = $projectContractPayments->paginate($pagination);

        return view('internal user.project contract.payment.index',compact("projectContractPayments","projectContract","paginations"));
    }

    public function details ($pcSlug,$slug){
        $projectContract = ProjectContract::where("slug",$pcSlug)->firstOrFail();
        $projectContractPayment = ProjectContractPayment::where("slug",$slug)->firstOrFail();
        return view('internal user.project contract.payment.details',compact("projectContract","projectContractPayment"));
    }

    public function create($pcSlug){
        $statusInformation = array("status" => "errors","message" => collect());

        $projectContractValidationStatus = $this->projectContractValidation($pcSlug);

        if($projectContractValidationStatus["status"] == "status"){
            $projectContract = ProjectContract::where("slug",$pcSlug)->firstOrFail();
            $projectContractPaymentMethods = ProjectContractPaymentMethod::orderby("name","asc")->get();
            return view('internal user.project contract.payment.create',compact("projectContract","projectContractPaymentMethods"));
        }
        else{
            $statusInformation["status"] = "errors";

            foreach($projectContractValidationStatus["message"] as $perMessage){
                $statusInformation["message"]->push($perMessage);
            }

            return redirect()->route("project.contract.payment.index",["pcSlug" => $pcSlug])->with([$statusInformation["status"] => $statusInformation["message"]]);
        }
    }

    public function edit($pcSlug,$slug){
        $statusInformation = array("status" => "errors","message" => collect());

        $projectContractValidationStatus = $this->projectContractValidation($pcSlug);

        if($projectContractValidationStatus["status"] == "status"){
            $projectContract = ProjectContract::where("slug",$pcSlug)->firstOrFail();
            $projectContractPayment = ProjectContractPayment::where("slug",$slug)->firstOrFail();
            $projectContractPaymentMethods = ProjectContractPaymentMethod::orderby("name","asc")->get();
            return view('internal user.project contract.payment.edit',compact("projectContract","projectContractPayment","projectContractPaymentMethods"));
        }
        else{
            $statusInformation["status"] = "errors";

            foreach($projectContractValidationStatus["message"] as $perMessage){
                $statusInformation["message"]->push($perMessage);
            }

            return redirect()->route("project.contract.payment.index",["pcSlug" => $pcSlug])->with([$statusInformation["status"] => $statusInformation["message"]]);
        }
    }


    public function save(Request $request,$pcSlug){
        $this->pcSlug = $pcSlug;

        $validator = Validator::make($request->all(),
            [
                'name' => 'required|max:200',
                'payment_date' => 'required|date',
                'note' => 'required',
                'description' => 'nullable',
                'payment_method' => 'required',
                'amount' => 'required|numeric|min:0',
                'due' => 'required|numeric|min:0',
            ],
            [
                'name.required' => 'Name is required.',
                'name.max' => 'Name length can not greater then 200 chars.',

                'payment_date.required' => 'Payment date is required.',
                'payment_date.date' => 'Payment date must be a date.',

                'payment_method.required' => 'Payment method is required.',

                'amount.required' => 'Amount is required.',
                'amount.min' => 'Amount must be at least 0.',
                'amount.numeric' => 'Amount must be numeric.',

                'due.required' => 'Amount is required.',
                'due.min' => 'Amount must be at least 0.',
                'due.numeric' => 'Amount must be unumeric.',

                'note.required' => 'Note is required.',
            ]
        );

        $validator->after(function ($validator) {
            $afterValidatorData = $validator->getData();
            $projectContract = ProjectContract::where("slug", $this->pcSlug)->firstOrFail();

            if(array_key_exists('payment_method', $afterValidatorData) && !($afterValidatorData["payment_method"] == null)){
                $projectContractPaymentMethodFound = ProjectContractPaymentMethod::where("slug",$afterValidatorData["payment_method"])->count();

                if($projectContractPaymentMethodFound == 0 ){
                    $validator->errors()->add(
                        'payment_method', "Unknown payment method."
                    );
                }
            }

            if( $projectContract){
                $pcTotalReceiveAmount = $projectContract->totalReceiveAmount() + $afterValidatorData["amount"];

                if($afterValidatorData["amount"] > $projectContract->totalReceivableAmount()){
                    $validator->errors()->add(
                        'amount', "Amount can not bigger then receivable amount."
                    );
                }

                if($pcTotalReceiveAmount > $projectContract->totalReceivableAmount()){
                    $validator->errors()->add(
                        'amount', "Total receive amount can not bigger then total receivable amount."
                    );
                }

                if(($afterValidatorData["amount"] + $afterValidatorData["due"]) > $projectContract->totalReceivableAmount()){
                    $validator->errors()->add(
                        'amount', "Sum of amount and due can not bigger then total receivable amount."
                    );
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $statusInformation = array("status" => "errors","message" => collect());

        $projectContractValidationStatus = $this->projectContractValidation($pcSlug);

        if($projectContractValidationStatus["status"] == "status"){
            LogBatch::startBatch();
                $projectContractPayment = new ProjectContractPayment();
                $projectContractPayment->name = $request->name;
                $projectContractPayment->payment_date = $request->payment_date ;
                $projectContractPayment->description = $request->description;
                $projectContractPayment->note = array($request->note);
                $projectContractPayment->payment_method_id = ProjectContractPaymentMethod::where("slug",$request->payment_method)->firstOrFail()->id;
                $projectContractPayment->amount = $request->amount;
                $projectContractPayment->project_contract_id = ProjectContract::where("slug",$pcSlug)->firstOrFail()->id;
                $projectContractPayment->slug = SystemConstant::slugGenerator($request->name,200);
                $projectContractPayment->created_at = Carbon::now();
                $projectContractPayment->created_by_id = Auth::user()->id;
                $projectContractPayment->updated_at = null;
                $saveProjectContractPayment = $projectContractPayment->save();
            LogBatch::endBatch();

            if($saveProjectContractPayment){
                $this->sendEmail("Create","A new project contract payment has been created by ".Auth::user()->name.".",$projectContractPayment );

                $statusInformation["status"] = "status";
                $statusInformation["message"]->push("Successfully created.");

                $projectContractReceivableStatusUpdateStatus = $this->projectContractPaymentReceivableUpdate($pcSlug);

                $statusInformation["status"] = $projectContractReceivableStatusUpdateStatus["status"];
                foreach($projectContractReceivableStatusUpdateStatus["message"] as $perMessage){
                    $statusInformation["message"]->push($perMessage);
                }
            }
            else{
                $statusInformation["status"] = "errors";
                $statusInformation["message"]->push("Fail to create.");
            }
        }
        else{
            $statusInformation["status"] = "errors";
            $statusInformation["message"]->push("Fail to create.");

            foreach($projectContractValidationStatus["message"] as $perMessage){
                $statusInformation["message"]->push($perMessage);
            }
        }

        return redirect()->route("project.contract.payment.index",["pcSlug" => $pcSlug])->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    public function update(Request $request,$pcSlug,$slug){
        $this->pcSlug = $pcSlug;

        $validator = Validator::make($request->all(),
            [
                'name' => 'required|max:200',
                'payment_date' => 'required|date',
                'note' => 'required',
                'description' => 'nullable',
                'payment_method' => 'required',
                'amount' => 'required|numeric|min:0',
                'current_amount' => 'required|numeric|min:0',
                'due' => 'required|numeric|min:0',
            ],
            [
                'name.required' => 'Name is required.',
                'name.max' => 'Name length can not greater then 200 chars.',

                'payment_date.required' => 'Payment date is required.',
                'payment_date.date' => 'Payment date must be a date.',

                'payment_method.required' => 'Payment method is required.',

                'amount.required' => 'Amount is required.',
                'amount.min' => 'Amount must be at least 0.',
                'amount.numeric' => 'Amount must be numeric.',

                'current_amount.required' => 'Current amount is required.',
                'current_amount.min' => 'Current amount must be at least 0.',
                'current_amount.numeric' => 'Current amount must be numeric.',

                'due.required' => 'Amount is required.',
                'due.min' => 'Amount must be at least 0.',
                'due.numeric' => 'Amount must be unumeric.',

                'note.required' => 'Note is required.',
            ]
        );

        $validator->after(function ($validator) {
            $afterValidatorData = $validator->getData();
            $projectContract = ProjectContract::where("slug", $this->pcSlug)->firstOrFail();

            if(array_key_exists('payment_method', $afterValidatorData) && !($afterValidatorData["payment_method"] == null)){
                $projectContractPaymentMethodFound = ProjectContractPaymentMethod::withTrashed()->where("slug",$afterValidatorData["payment_method"])->count();

                if($projectContractPaymentMethodFound == 0 ){
                    $validator->errors()->add(
                        'payment_method', "Unknown payment method."
                    );
                }
            }

            if( $projectContract){
                $pcTotalReceiveAmount = $projectContract->totalReceiveAmount() + $afterValidatorData["amount"];

                if($afterValidatorData["amount"] > $projectContract->totalReceivableAmount()){
                    $validator->errors()->add(
                        'amount', "Amount can not bigger then receivable amount."
                    );
                }

                if($pcTotalReceiveAmount > $projectContract->totalReceivableAmount()){
                    $validator->errors()->add(
                        'amount', "Total receive amount can not bigger then total receivable amount."
                    );
                }

                if(($afterValidatorData["current_amount"] + $afterValidatorData["amount"] + $afterValidatorData["due"]) > $projectContract->totalReceivableAmount()){
                    $validator->errors()->add(
                        'amount', "Sum of current amount,amount and due can not bigger then total receivable amount."
                    );
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $statusInformation = array("status" => "errors","message" => collect());

        $projectContractValidationStatus = $this->projectContractValidation($pcSlug);

        if($projectContractValidationStatus["status"] == "status"){
            $projectContractPaymentNotes =  ProjectContractPayment::where("slug",$slug)->firstOrFail()->note;
            array_push($projectContractPaymentNotes,$request->note);

            LogBatch::startBatch();
                $projectContractPayment = ProjectContractPayment::where("slug",$slug)->firstOrFail();
                $projectContractPayment->name = $request->name;
                $projectContractPayment->payment_date = $request->payment_date ;
                $projectContractPayment->description = $request->description;
                $projectContractPayment->note = $projectContractPaymentNotes;
                $projectContractPayment->payment_method_id = ProjectContractPaymentMethod::withTrashed()->where("slug",$request->payment_method)->firstOrFail()->id;
                $projectContractPayment->amount = $projectContractPayment->amount + $request->amount;
                $projectContractPayment->project_contract_id = ProjectContract::where("slug",$pcSlug)->firstOrFail()->id;
                $projectContractPayment->slug = SystemConstant::slugGenerator($request->name,200);
                $projectContractPayment->updated_at = Carbon::now();
                $updateProjectContractPayment = $projectContractPayment->update();
            LogBatch::endBatch();

            if($updateProjectContractPayment){
                $this->sendEmail("Update","Project contract payment has been updated by ".Auth::user()->name.".",$projectContractPayment );

                $statusInformation["status"] = "status";
                $statusInformation["message"]->push("Successfully updated.");

                $projectContractReceivableStatusUpdateStatus = $this->projectContractPaymentReceivableUpdate($pcSlug);

                $statusInformation["status"] = $projectContractReceivableStatusUpdateStatus["status"];
                foreach($projectContractReceivableStatusUpdateStatus["message"] as $perMessage){
                    $statusInformation["message"]->push($perMessage);
                }
            }
            else{
                $statusInformation["status"] = "errors";
                $statusInformation["message"]->push("Fail to update.");
            }
        }
        else{
            $statusInformation["status"] = "errors";
            $statusInformation["message"]->push("Fail to update.");

            foreach($projectContractValidationStatus["message"] as $perMessage){
                $statusInformation["message"]->push($perMessage);
            }
        }

        return redirect()->route("project.contract.payment.index",["pcSlug" => $pcSlug])->with([$statusInformation["status"] => $statusInformation["message"]]);
    }


    public function delete($pcSlug,$slug){
        $statusInformation = array("status" => "errors","message" => collect());

        $projectContractValidationStatus = $this->projectContractValidation($pcSlug);

        if($projectContractValidationStatus["status"] == "status"){
            $projectContractPayment = ProjectContractPayment::where("slug",$slug)->firstOrFail();
            $deleteProjectContractPayment = $projectContractPayment->delete();

            if($deleteProjectContractPayment){
                $this->sendEmail("Delete","Project contract payment has been delete by ".Auth::user()->name.".",$projectContractPayment );

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

            foreach($projectContractValidationStatus["message"] as $perMessage){
                $statusInformation["message"]->push($perMessage);
            }
        }

        return redirect()->route("project.contract.payment.index",["pcSlug" => $pcSlug])->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    private function projectContractValidation($slug){
        $statusInformation = array("status" => "errors","message" => collect());

        $projectContract = ProjectContract::where("slug",$slug)->firstOrFail();

        if(($projectContract->status == "Complete") && !($projectContract->receivable_status == "NotStarted") && !($projectContract->receivable_status == "Complete")){
            $statusInformation["status"] = "status";
            $statusInformation["message"]->push("Passed the validation.");
        }
        else{
            $statusInformation["status"] = "errors";
            $statusInformation["message"]->push("Project contract is not complete.");
            $statusInformation["message"]->push("Project contract payment must be not started.");
        }

        return $statusInformation;
    }

    private function projectContractPaymentReceivableUpdate($pcSlug){
        $statusInformation = array("status" => "errors","message" => collect());

        $projectContractValidationStatus = $this->projectContractValidation($pcSlug);

        if($projectContractValidationStatus["status"] == "status"){

            $projectContractReceivableStatus = "Due";

            $sProjectContract = ProjectContract::where("slug", $pcSlug)->firstOrFail();
            $projectContractNotes = $sProjectContract->note;

            if($sProjectContract->totalReceivableAmount() == $sProjectContract->totalReceiveAmount()){
                $projectContractReceivableStatus = "Complete";
            }
            else{
                if($sProjectContract->totalReceiveAmount() < $sProjectContract->totalReceivableAmount()){
                    $projectContractReceivableStatus = "Partial";
                }
                else{
                    $projectContractReceivableStatus = "Due";
                }
            }

            array_push($projectContractNotes, "Update project contract receivable status to ".$projectContractReceivableStatus." .");

            $projectContract = ProjectContract::where("slug", $pcSlug)->firstOrFail();
            $projectContract->receivable_status =  $projectContractReceivableStatus;
            $projectContract->note = $projectContractNotes;
            $projectContract->updated_at = Carbon::now();

            $updateProjectContract = $projectContract->update();
            if( $updateProjectContract){
                $statusInformation["status"] = "status";
                $statusInformation["message"]->push("Project contract successfully update.");

                $statusInformation["message"]->push( ( $projectContract->receivable_status == "Complete") ? "Receiving payment successfully completed." : "Current receivable status is '".$projectContract->receivable_status."'.");
            }
            else{
                $statusInformation["status"] = "errors";
                $statusInformation["message"]->push("Fail to update project contract.");
                $statusInformation["message"]->push("Fail to update project contrac receivable status.");
            }
        }
        else{
            $statusInformation["status"] = "errors";
            $statusInformation["message"]->push("Project contract is not complete.");
            $statusInformation["message"]->push("Project contract payment must be not started.");
        }

        return $statusInformation;
    }

    private function sendEmail($event,$subject,ProjectContractPayment $projectContractPayment ){
        $envelope = array();

        $notificationSetting = Setting::where( 'code','NotificationSetting')->firstOrFail()->fields_with_values["User"];

        $envelope["to"] = $notificationSetting["to"];
        $envelope["cc"] = $notificationSetting["cc"];
        $envelope["from"] = $notificationSetting["from"];
        $envelope["reply"] = $notificationSetting["reply"];

        if(($notificationSetting["send"] == true) && (($notificationSetting["event"] == "All") || (!($notificationSetting["event"] == "All") && ($notificationSetting["event"] == $event)))){
            Mail::send(new EmailSendForProjectContractPayment($event,$envelope,$subject,$projectContractPayment));
        }
    }
}
