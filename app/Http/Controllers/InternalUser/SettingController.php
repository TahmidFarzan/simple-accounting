<?php

namespace App\Http\Controllers\InternalUser;

use Carbon\Carbon;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Mail\EmailSendForSetting;
use App\Utilities\SystemConstant;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Facades\LogBatch;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
        // Controller access permissions.
        $this->middleware(['user.user.permission.check:SMP01'])->only(["index"]);

        // Business setting permissions.
        $this->middleware(['user.user.permission.check:SMP02.01'])->only(["businessSettingIndex"]);
        $this->middleware(['user.user.permission.check:SMP02.02'])->only(["businessSettingDetails"]);
        $this->middleware(['user.user.permission.check:SMP02.03'])->only(["businessSettingEdit","businessSettingUpdate"]);


        // Activity log setting permissions.
        $this->middleware(['user.user.permission.check:SMP03.01'])->only(["activityLogSettingIndex"]);
        $this->middleware(['user.user.permission.check:SMP03.02'])->only(["activityLogSettingDetails"]);
        $this->middleware(['user.user.permission.check:SMP03.03'])->only(["activityLogSettingEdit","activityLogSettingUpdate"]);

        // Authentication log setting permissions.
        $this->middleware(['user.user.permission.check:SMP04.01'])->only(["authenticationLogSettingIndex"]);
        $this->middleware(['user.user.permission.check:SMP04.02'])->only(["authenticationLogSettingDetails"]);
        $this->middleware(['user.user.permission.check:SMP04.03'])->only(["authenticationLogSettingEdit","authenticationLogSettingUpdate"]);

        // Email send setting permission.
        $this->middleware(['user.user.permission.check:SMP05.01'])->only(["emailSendSettingIndex"]);
        $this->middleware(['user.user.permission.check:SMP05.02'])->only(["emailSendSettingDetails"]);
        $this->middleware(['user.user.permission.check:SMP05.03'])->only(["emailSendSettingEdit","emailSendSettingUpdate"]);
    }

    public function index()
    {
        return view('internal user.setting.index');
    }

    // Business information.
    public function businessSettingIndex()
    {
        $businessSetting = Setting::where("code","BusinessSetting")->firstOrFail();
        return view('internal user.setting.business setting.index',compact("businessSetting"));
    }

    public function businessSettingDetails($slug)
    {
        $businessSetting = Setting::where("slug",$slug)->firstOrFail();
        return view('internal user.setting.business setting.details',compact("businessSetting"));
    }

    public function businessSettingEdit($slug)
    {
        $businessSetting = Setting::where("slug",$slug)->firstOrFail();
        return view('internal user.setting.business setting.edit',compact("businessSetting"));
    }

    public function businessSettingUpdate(Request $request,$slug){
        $validator = Validator::make($request->all(),
            [
                'name' => 'required|string|max:50',
                'short_name' => 'required|string|max:15',
                'email' => 'required|string|email',
                'mobile_no' => 'required|max:20|regex:/^([0-9\s\-\+]*)$/',
                'url' => 'nullable|url',
                'address' => 'string|nullable',
                'description' => 'string|nullable',
                'logo' => 'nullable|image|mimes:png,jpg,jpeg,webp',
                'country' => 'required|string|max:100',
                'country_code' => 'required|string|max:3',
                'currency' => 'required|string|max:50',
                'currency_code' => 'required|string|max:3',
                'currency_symbol' => 'required|string|max:2',
            ],
            [
                'name.required' => 'Name is required.',
                'name.string' => 'Name must be a string.',
                'name.max' => 'Name length can not greater then 50 chars.',

                'short_name.required' => 'Short name is required.',
                'short_name.string' => 'Short name must be a string.',
                'short_name.max' => 'Short name length can not greater then 10 chars.',

                'url.url' => 'Url must be a valid url.',

                'mobile_no.required' => 'Mobile no is required.',
                'mobile_no.max' => 'Mobile no length can not greater then 20 chars.',
                'mobile_no.regex' => 'Mobile no must be a valid phone.',

                'address.required' => 'Address is required.',
                'address.string' => 'Address must be string.',
                'description.required' => 'Description is required.',
                'description.string' => 'Description must be string.',

                'logo.image' => 'Logo must be image.',
                'logo.mimes' => 'Logo must be valid format (png,jpg,jpeg or webp).',

                'country.required' => 'Country is required.',
                'country.string' => 'Country must be a string.',
                'country.max' => 'Country length can not greater then 10 chars.',

                'country_code.required' => 'Country code is required.',
                'country_code.string' => 'Country code must be a string.',
                'country_code.max' => 'Country code length can not greater then 3 chars.',

                'currency.required' => 'Currency is required.',
                'currency.string' => 'Currency must be a string.',
                'currency.max' => 'Currency length can not greater then 50 chars.',

                'currency_code.required' => 'Currency code is required.',
                'currency_code.string' => 'Currency code must be a string.',
                'currency_code.max' => 'Currency code length can not greater then 3 chars.',

                'currency_symbol.required' => 'Currency symbol is required.',
                'currency_symbol.string' => 'Currency symbol must be a string.',
                'currency_symbol.max' => 'Currency symbol length can not greater then 2 chars.',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $newLogoName = null;
        $statusInformation = array("status" => "errors","message" => collect());
        $oldLogoName = Setting::where("slug",$slug)->where("code","BusinessSetting")->firstOrFail()->fields_with_values["logo"];

        if(!($request->hasFile('logo'))){
            $newLogoName = $oldLogoName;
        }

        if($request->hasFile('logo')){
            $newLogoName = SystemConstant::generateFileName("current logo",$request->file("logo")->getClientOriginalExtension(),200);
            Storage::putFileAs('public/images/setting/',$request->file('logo'), $newLogoName);
            if( strlen($oldLogoName) > 0 ){
                if (Storage::disk('public')->exists('/images/setting/'.$oldLogoName)) {
                    Storage::disk('public')->delete('/images/setting/'.$oldLogoName);
                }
            }
        }

        LogBatch::startBatch();
            $businessSetting = Setting::where("slug",$slug)->where("code","BusinessSetting")->firstOrFail();
            $businessSettingFieldsWithValues = $businessSetting->fields_with_values;

            $businessSettingFieldsWithValues['name'] = $request->name;
            $businessSettingFieldsWithValues['email'] = $request->email;
            $businessSettingFieldsWithValues['short_name'] = $request->short_name;
            $businessSettingFieldsWithValues['mobile_no'] = $request->mobile_no;
            $businessSettingFieldsWithValues['url'] = $request->url;
            $businessSettingFieldsWithValues['address'] = $request->address;
            $businessSettingFieldsWithValues['country'] = $request->country;
            $businessSettingFieldsWithValues['country_code'] = $request->country_code;
            $businessSettingFieldsWithValues['currency'] = $request->currency;
            $businessSettingFieldsWithValues['currency_code'] = $request->currency_code;
            $businessSettingFieldsWithValues['currency_symbol'] = $request->currency_symbol;
            $businessSettingFieldsWithValues['description'] = $request->description;
            $businessSettingFieldsWithValues['logo'] = $newLogoName;

            $businessSetting->fields_with_values = $businessSettingFieldsWithValues;
            $businessSetting->updated_at = Carbon::now();
            $updateBusinessSetting = $businessSetting->update();
        LogBatch::endBatch();

        if($updateBusinessSetting){
            $this->sendEmail("Update","Business setting has been updated by ".Auth::user()->name.".",$businessSetting );

            $statusInformation["status"] = "status";
            $statusInformation["message"]->push("Successfully updated.");
        }
        else{
            $statusInformation["status"] = "errors";
            $statusInformation["message"]->push("Please update again.");
            $statusInformation["message"]->push("Fail to update some setting.");
        }

        // Redirect logic.
        return redirect()->route("setting.business.setting.index")->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    // Activity log setting.
    public function activityLogSettingIndex()
    {
        $activityLogSetting = Setting::where("code","ActivityLogSetting")->firstOrFail();
        return view('internal user.setting.activity log setting.index',compact("activityLogSetting"));
    }

    public function activityLogSettingDetails($slug)
    {
        $activityLogSetting = Setting::where("slug",$slug)->firstOrFail();
        return view('internal user.setting.activity log setting.details',compact("activityLogSetting"));
    }

    public function activityLogSettingEdit($slug)
    {
        $activityLogSetting = Setting::where("slug",$slug)->firstOrFail();
        return view('internal user.setting.activity log setting.edit',compact("activityLogSetting"));
    }

    public function activityLogSettingUpdate(Request $request,$slug){
        $validator = Validator::make($request->all(),
            [
                'delete_records_older_than' => 'required|min:1|max:50|numeric',
                'auto_delete' => 'required|string|in:Yes,No',
            ],
            [
                'delete_records_older_than.required' => 'Delete records older than is required.',
                'delete_records_older_than.min' => 'Delete records older than must have min 1.',
                'delete_records_older_than.max' => 'Delete records older than must have max 365.',
                'delete_records_older_than.numeric' => 'Delete records older than must a numeric numeric.',

                'auto_delete.required' => 'Auto delete is required.',
                'auto_delete.string' => 'Auto delete must be a string.',
                'auto_delete.in' => 'Auto delete either Yes or No.',
            ]
        );
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $statusInformation = array("status" => "errors","message" => collect());

        LogBatch::startBatch();
            $activityLogSetting = Setting::where("slug",$slug)->where("code","ActivityLogSetting")->firstOrFail();
            $activityLogSettingFieldsWithValues = $activityLogSetting->fields_with_values;

            $activityLogSettingFieldsWithValues['delete_records_older_than'] = $request->delete_records_older_than;
            $activityLogSettingFieldsWithValues['auto_delete'] = $request->auto_delete;

            $activityLogSetting->fields_with_values = $activityLogSettingFieldsWithValues;
            $activityLogSetting->updated_at = Carbon::now();
            $updateActivityLogSetting = $activityLogSetting->update();
        LogBatch::endBatch();

        if($updateActivityLogSetting){
            $this->sendEmail("Update","Activity log setting has been updated by ".Auth::user()->name.".",$activityLogSetting );

            $statusInformation["status"] = "status";
            $statusInformation["message"]->push("Successfully updated.");
        }
        else{
            $statusInformation["status"] = "errors";
            $statusInformation["message"]->push("Please update again.");
            $statusInformation["message"]->push("Fail to update some setting.");

        }

        // Redirect logic.
        return redirect()->route("setting.activity.log.setting.index")->with([$statusInformation["status"] => $statusInformation["message"] ]);
    }

    // Authentication log setting.
    public function authenticationLogSettingIndex()
    {
        $authenticationLogSetting = Setting::where("code","AuthenticationLogSetting")->firstOrFail();
        return view('internal user.setting.authentication log setting.index',compact("authenticationLogSetting"));
    }

    public function authenticationLogSettingDetails($slug)
    {
        $authenticationLogSetting = Setting::where("slug",$slug)->firstOrFail();
        return view('internal user.setting.authentication log setting.details',compact("authenticationLogSetting"));
    }

    public function authenticationLogSettingEdit($slug)
    {
        $authenticationLogSetting = Setting::where("slug",$slug)->firstOrFail();
        return view('internal user.setting.authentication log setting.edit',compact("authenticationLogSetting"));
    }

    public function authenticationLogSettingUpdate(Request $request,$slug){
        $validator = Validator::make($request->all(),
            [
                'delete_records_older_than' => 'required|min:1|max:50|numeric',
                'auto_delete' => 'required|string|in:Yes,No',
            ],
            [
                'delete_records_older_than.required' => 'Delete records older than is required.',
                'delete_records_older_than.min' => 'Delete records older than must have min 1.',
                'delete_records_older_than.max' => 'Delete records older than must have max 365.',
                'delete_records_older_than.numeric' => 'Delete records older than must a numeric numeric.',

                'auto_delete.required' => 'Auto delete is required.',
                'auto_delete.string' => 'Auto delete must be a string.',
                'auto_delete.in' => 'Auto delete either Yes or No.',
            ]
        );
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $statusInformation = array("status" => "errors","message" => collect());

        LogBatch::startBatch();
            $authenticationLogSetting = Setting::where("slug",$slug)->where("code","AuthenticationLogSetting")->firstOrFail();
            $authenticationLogSettingFieldsWithValues = $authenticationLogSetting->fields_with_values;

            $authenticationLogSettingFieldsWithValues['delete_records_older_than'] = $request->delete_records_older_than;
            $authenticationLogSettingFieldsWithValues['auto_delete'] = $request->auto_delete;

            $authenticationLogSetting->fields_with_values = $authenticationLogSettingFieldsWithValues;
            $authenticationLogSetting->updated_at = Carbon::now();
            $updateActivityLogSetting = $authenticationLogSetting->update();
        LogBatch::endBatch();

        if($updateActivityLogSetting){
            $this->sendEmail("Update","Authentication log setting has been updated by ".Auth::user()->name.".",$authenticationLogSetting );

            $statusInformation["status"] = "status";
            $statusInformation["message"]->push("Successfully updated.");
        }
        else{
            $statusInformation["status"] = "errors";
            $statusInformation["message"]->push("Please update again.");
            $statusInformation["message"]->push("Fail to update some setting.");
        }

        // Redirect logic.
        return redirect()->route("setting.authentication.log.setting.index")->with([$statusInformation["status"] => $statusInformation["message"]]);
    }

    public function emailSendSettingIndex(){
        $emailSendSetting = Setting::where("code","emailSendSetting")->firstOrFail();
        return view('internal user.setting.email send setting.index',compact("emailSendSetting"));
    }

    public function emailSendSettingDetails(){
        $emailSendSetting = Setting::where("code","emailSendSetting")->firstOrFail();
        return view('internal user.setting.email send setting.details',compact("emailSendSetting"));
    }

    public function emailSendSettingEdit(){
        $emailSendSetting = Setting::where("code","emailSendSetting")->firstOrFail();
        return view('internal user.setting.email send setting.edit',compact("emailSendSetting"));
    }

    public function emailSendSettingUpdate(Request $request,$slug){
        $validator = Validator::make($request->all(),
            [
                'from' => 'required|email|max:255',
                'to' => 'required|email|max:255',
                'cc' => 'required|email|max:255',
                'reply' => 'required|email|max:255',

                'event_for_user' => 'required|in:All,Create,Update,Trash,Restore',
                'send_for_user' => 'required|string|in:1,0',

                'event_for_activity_log' => 'required|in:All,Delete,DeleteAll',
                'send_for_activity_log' => 'required|string|in:1,0',

                'event_for_authentication_log' => 'required|in:All,Delete,DeleteAll',
                'send_for_authentication_log' => 'required|string|in:1,0',

                'event_for_project_contract' => 'required|in:All,Create,Update,Delete,Complete,ReceivingPayment,CompleteReceivePayment',
                'send_for_project_contract' => 'required|string|in:1,0',

                'event_for_project_contract_journal' => 'required|in:All,Create,Update,Delete',
                'send_for_project_contract_journal' => 'required|string|in:1,0',

                'event_for_project_contract_payment' => 'required|in:All,Create,Update,Delete',
                'send_for_project_contract_payment' => 'required|string|in:1,0',

                'event_for_project_contract_payment_method' => 'required|in:All,Create,Update,Trash,Restore',
                'send_for_project_contract_payment_method' => 'required|string|in:1,0',

                'event_for_project_contract_category' => 'required|in:All,Create,Update,Trash,Restore',
                'send_for_project_contract_category' => 'required|string|in:1,0',

                'event_for_user_permission_group' => 'required|in:All,Create,Update,Trash,Restore',
                'send_for_user_permission_group' => 'required|string|in:1,0',

                'event_for_setting' => 'required|in:All,Update',
                'send_for_setting' => 'required|string|in:1,0',

                'event_for_report' => 'required|in:All',
                'send_for_report' => 'required|string|in:1,0',
            ],
            [
                'from.required' => 'From is required.',
                'from.email' => 'From must be a email.',
                'from.max' => 'From length must not more than 255.',
                'to.required' => 'To is required.',
                'to.email' => 'To must be a email.',
                'to.max' => 'To length must not more than 255.',
                'cc.required' => 'CC is required.',
                'cc.email' => 'CC must be a email.',
                'cc.max' => 'CC length must not more than 255.',
                'reply.required' => 'Reply is required.',
                'reply.email' => 'Reply must be a email.',
                'reply.max' => 'Reply length must not more than 255.',

                'event_for_user.required' => 'Event is required.',
                'event_for_user.in' => 'Event must be one out of [All,Create,Update,Trash,Restore].',
                'send_for_user.required' => 'Send is required.',
                'send_for_user.in' => 'Send must be one out of [Yes,No].',

                'event_for_activity_log.required' => 'Event is required.',
                'event_for_activity_log.in' => 'Event must be one out of [All,Delete,Delete all].',
                'send_for_activity_log.required' => 'Send is required.',
                'send_for_activity_log.in' => 'Send must be one out of [Yes,No].',

                'event_for_authentication_log.required' => 'Event is required.',
                'event_for_authentication_log.in' => 'Event must be one out of [All,Delete,Delete all].',
                'send_for_authentication_log.required' => 'Send is required.',
                'send_for_authentication_log.in' => 'Send must be one out of [Yes,No].',

                'event_for_project_contract.required' => 'Event is required.',
                'event_for_project_contract.in' => 'Event must be one out of [All,Create,Update,Delete,Complete,Receiving payment,Complete receive payment].',
                'send_for_project_contract.required' => 'Send is required.',
                'send_for_project_contract.in' => 'Send must be one out of [Yes,No].',

                'event_for_project_contract_journal.required' => 'Event is required.',
                'event_for_project_contract_journal.in' => 'Event must be one out of [All,Create,Update,Delete].',
                'send_for_project_contract_journal.required' => 'Send is required.',
                'send_for_project_contract_journal.in' => 'Send must be one out of [Yes,No].',

                'event_for_project_contract_payment.required' => 'Event is required.',
                'event_for_project_contract_payment.in' => 'Event must be one out of [All,Create,Update,Delete].',
                'send_for_project_contract_payment.required' => 'Send is required.',
                'send_for_project_contract__payment.in' => 'Send must be one out of [Yes,No].',

                'event_for_project_contract_payment_method.required' => 'Event is required.',
                'event_for_project_contract_payment_method.in' => 'Event must be one out of [All,Create,Update,Trash,Restore].',
                'send_for_project_contract_payment_method.required' => 'Send is required.',
                'send_for_project_contract_payment_method.in' => 'Send must be one out of [Yes,No].',

                'event_for_project_contract_category.required' => 'Event is required.',
                'event_for_project_contract_category.in' => 'Event must be one out of [All,Create,Update,Trash,Restore].',
                'send_for_project_contract_category.required' => 'Send is required.',
                'send_for_project_contract_category.in' => 'Send must be one out of [Yes,No].',

                'event_for_user_permission_group.required' => 'Event is required.',
                'event_for_user_permission_group.in' => 'Event must be one out of [All,Create,Update,Trash,Restore].',
                'send_for_user_permission_group.required' => 'Send is required.',
                'send_for_user_permission_group.in' => 'Send must be one out of [Yes,No].',

                'event_for_setting.required' => 'Event is required.',
                'event_for_setting.in' => 'Event must be one out of [All,Update].',
                'send_for_setting.required' => 'Send is required.',
                'send_for_setting.in' => 'Send must be one out of [Yes,No].',

                'event_for_report.required' => 'Event is required.',
                'event_for_report.in' => 'Event must be one out of [All].',
                'send_for_report.required' => 'Send is required.',
                'send_for_report.in' => 'Send must be one out of [Yes,No].',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $statusInfromation = array("status" => "errors","message" => collect());

        LogBatch::startBatch();
            $emailSendSetting = Setting::where("slug",$slug)->where("code","EmailSendSetting")->firstOrFail();
            $emailSendSettingFieldsWithValues = $emailSendSetting->fields_with_values;

            $emailSendSettingFieldsWithValues['from'] = $request->from;
            $emailSendSettingFieldsWithValues['to'] = $request->to;
            $emailSendSettingFieldsWithValues['cc'] = $request->cc;
            $emailSendSettingFieldsWithValues['reply'] = $request->reply;

            $emailSendSettingFieldsWithValues['module']["User"]["event"] = $request->event_for_user;
            $emailSendSettingFieldsWithValues['module']["User"]["send"] = ($request->send_for_user == 1) ? true : false;

            $emailSendSettingFieldsWithValues['module']["ActivityLog"]["event"] = $request->event_for_activity_log;
            $emailSendSettingFieldsWithValues['module']["ActivityLog"]["send"] = ($request->send_for_activity_log == 1) ? true : false;

            $emailSendSettingFieldsWithValues['module']["AuthenticationLog"]["event"] = $request->event_for_authentication_log;
            $emailSendSettingFieldsWithValues['module']["AuthenticationLog"]["send"] = ($request->send_for_authentication_log == 1) ? true : false;

            $emailSendSettingFieldsWithValues['module']["ProjectContract"]["event"] = $request->event_for_project_contract;
            $emailSendSettingFieldsWithValues['module']["ProjectContract"]["send"] = ($request->send_for_project_contract == 1) ? true : false;

            $emailSendSettingFieldsWithValues['module']["ProjectContractJournal"]["event"] = $request->event_for_project_contract_journal;
            $emailSendSettingFieldsWithValues['module']["ProjectContractJournal"]["send"] = ($request->send_for_project_contract_journal == 1) ? true : false;

            $emailSendSettingFieldsWithValues['module']["ProjectContractPayment"]["event"] = $request->event_for_project_contract_payment;
            $emailSendSettingFieldsWithValues['module']["ProjectContractPayment"]["send"] = ($request->send_for_project_contract_payment == 1) ? true : false;

            $emailSendSettingFieldsWithValues['module']["ProjectContractPaymentMethod"]["event"] = $request->event_for_project_contract_payment_method;
            $emailSendSettingFieldsWithValues['module']["ProjectContractPaymentMethod"]["send"] = ($request->send_for_project_contract_payment_method == 1) ? true : false;

            $emailSendSettingFieldsWithValues['module']["ProjectContractCategory"]["event"] = $request->event_for_project_contract_category;
            $emailSendSettingFieldsWithValues['module']["ProjectContractCategory"]["send"] = ($request->send_for_project_contract_category == 1) ? true : false;

            $emailSendSettingFieldsWithValues['module']["UserPermissionGroup"]["event"] = $request->event_for_user_permission_group;
            $emailSendSettingFieldsWithValues['module']["UserPermissionGroup"]["send"] = ($request->send_for_user_permission_group == 1) ? true : false;

            $emailSendSettingFieldsWithValues['module']["Setting"]["event"] = $request->event_for_setting;
            $emailSendSettingFieldsWithValues['module']["Setting"]["send"] = ($request->send_for_setting == 1) ? true : false;

            $emailSendSettingFieldsWithValues['module']["Report"]["event"] = $request->event_for_report;
            $emailSendSettingFieldsWithValues['module']["Report"]["send"] = ($request->send_for_report == 1) ? true : false;;

            $emailSendSetting->fields_with_values = $emailSendSettingFieldsWithValues;
            $emailSendSetting->updated_at = Carbon::now();
            $updateEmailSendSetting = $emailSendSetting->update();
        LogBatch::endBatch();

        if($updateEmailSendSetting ){
            $this->sendEmail("Update","Email send setting has been updated by ".Auth::user()->name.".",$emailSendSetting );
            $statusInfromation["status"] = "status";
            $statusInfromation["message"]->push("Successfully updated.");
        }
        else{
            $statusInfromation["status"] = "errors";
            $statusInfromation["message"]->push("Please update again.");
            $statusInfromation["message"]->push("Fail to update some setting.");
        }

        // Redirect logic.
        return redirect()->route("setting.email.send.setting.index")->with([$statusInfromation["status"] => $statusInfromation["message"]]);
    }

    private function sendEmail($event,$subject,Setting $setting ){
        $envelope = array();

        $emailSendSetting = Setting::where('code','EmailSendSetting')->firstOrFail()->fields_with_values;

        $envelope["to"] = $emailSendSetting["to"];
        $envelope["cc"] = $emailSendSetting["cc"];
        $envelope["from"] = $emailSendSetting["from"];
        $envelope["reply"] = $emailSendSetting["reply"];

        $moduleSetting = $emailSendSetting["module"]["Setting"];
        if(($moduleSetting["send"] == true) && (($moduleSetting["event"] == "All") || (!($moduleSetting["event"] == "All") && ($moduleSetting["event"] == $event)))){
            Mail::send(new EmailSendForSetting($envelope,$subject,$setting));
        }
    }
}
