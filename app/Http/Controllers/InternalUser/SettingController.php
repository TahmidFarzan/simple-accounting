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
        $oldLogoName = Setting::where("slug",$slug)->firstOrFail()->fields_with_values["logo"];

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
            $businessSetting = Setting::where("slug",$slug)->firstOrFail();
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
            $activityLogSetting = Setting::where("slug",$slug)->firstOrFail();
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
            $authenticationLogSetting = Setting::where("slug",$slug)->firstOrFail();
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
                'mail_send_event_for_user' => 'required|in:All,Create,Update,Trash,Restore',
                'mail_send_send_for_user' => 'required|string|in:Yes,No',
                'mail_send_from_for_user' => 'required|email',
                'mail_send_to_from_user' => 'required|email',
                'mail_send_cc_for_user' => 'required|email',
                'mail_send_reply_for_user' => 'required|email',

                'mail_send_event_for_activity_log' => 'required|in:All,Delete,DeleteAll',
                'mail_send_send_for_activity_log' => 'required|string|in:Yes,No',
                'mail_send_from_for_activity_log' => 'required|email',
                'mail_send_to_from_activity_log' => 'required|email',
                'mail_send_cc_for_activity_log' => 'required|email',
                'mail_send_reply_for_activity_log' => 'required|email',

                'mail_send_event_for_authentication_log' => 'required|in:All,Delete,DeleteAll',
                'mail_send_send_for_authentication_log' => 'required|string|in:Yes,No',
                'mail_send_from_for_authentication_log' => 'required|email',
                'mail_send_to_from_authentication_log' => 'required|email',
                'mail_send_cc_for_authentication_log' => 'required|email',
                'mail_send_reply_for_authentication_log' => 'required|email',

                'mail_send_event_for_project_contract' => 'required|in:All,Create,Update,Delete,Complete,ReceivingPayment,CompleteReceivePayment',
                'mail_send_send_for_project_contract' => 'required|string|in:Yes,No',
                'mail_send_from_for_project_contract' => 'required|email',
                'mail_send_to_from_project_contract' => 'required|email',
                'mail_send_cc_for_project_contract' => 'required|email',
                'mail_send_reply_for_project_contract' => 'required|email',

                'mail_send_event_for_project_contract_project_journal' => 'required|in:All,Create,Update,Delete',
                'mail_send_send_for_project_contract_project_journal' => 'required|string|in:Yes,No',
                'mail_send_from_for_project_contract_project_journal' => 'required|email',
                'mail_send_to_from_project_contract_project_journal' => 'required|email',
                'mail_send_cc_for_project_contract_project_journal' => 'required|email',
                'mail_send_reply_for_project_contract_project_journal' => 'required|email',

                'mail_send_event_for_project_contract_project_payment' => 'required|in:All,Create,Update,Delete',
                'mail_send_send_for_project_contract_project_payment' => 'required|string|in:Yes,No',
                'mail_send_from_for_project_contract_project_payment' => 'required|email',
                'mail_send_to_from_project_contract_project_payment' => 'required|email',
                'mail_send_cc_for_project_contract_project_payment' => 'required|email',
                'mail_send_reply_for_project_contract_project_payment' => 'required|email',

                'mail_send_event_for_project_contract_project_payment_method' => 'required|in:All,Create,Update,Trash,Restore',
                'mail_send_send_for_project_contract_project_payment_method' => 'required|string|in:Yes,No',
                'mail_send_from_for_project_contract_project_payment_method' => 'required|email',
                'mail_send_to_from_project_contract_project_payment_method' => 'required|email',
                'mail_send_cc_for_project_contract_project_payment_method' => 'required|email',
                'mail_send_reply_for_project_contract_project_payment_method' => 'required|email',

                'mail_send_event_for_project_contract_category' => 'required|in:All,Create,Update,Trash,Restore',
                'mail_send_send_for_project_contract_category' => 'required|string|in:Yes,No',
                'mail_send_from_for_project_contract_category' => 'required|email',
                'mail_send_to_from_project_contract_category' => 'required|email',
                'mail_send_cc_for_project_contract_category' => 'required|email',
                'mail_send_reply_for_project_contract_category' => 'required|email',

                'mail_send_event_for_user_permission_group' => 'required|in:All,Create,Update,Trash,Restore',
                'mail_send_send_for_user_permission_group' => 'required|string|in:Yes,No',
                'mail_send_from_for_user_permission_group' => 'required|email',
                'mail_send_to_from_user_permission_group' => 'required|email',
                'mail_send_cc_for_user_permission_group' => 'required|email',
                'mail_send_reply_for_user_permission_group' => 'required|email',

                'mail_send_event_for_setting' => 'required|in:All,Update',
                'mail_send_send_for_setting' => 'required|string|in:Yes,No',
                'mail_send_from_for_setting' => 'required|email',
                'mail_send_to_from_setting' => 'required|email',
                'mail_send_cc_for_setting' => 'required|email',
                'mail_send_reply_for_setting' => 'required|email',

                'mail_send_event_for_report' => 'required|in:All',
                'mail_send_send_for_report' => 'required|string|in:Yes,No',
                'mail_send_from_for_report' => 'required|email',
                'mail_send_to_from_report' => 'required|email',
                'mail_send_cc_for_report' => 'required|email',
                'mail_send_reply_for_report' => 'required|email',
            ],
            [
                'mail_send_event_for_user.required' => 'Event is required.',
                'mail_send_event_for_user.in' => 'Event must be one out of [All,Create,Update,Trash,Restore].',
                'mail_send_send_for_user.required' => 'Send is required.',
                'mail_send_send_for_user.in' => 'Send must be one out of [Yes,No].',
                'mail_send_to_from_user.required' => 'From is required.',
                'mail_send_send_from_user.email' => 'From must be a email.',
                'mail_send_to_user.required' => 'To is required.',
                'mail_send_send_to_user.email' => 'To must be a email.',
                'mail_send_cc_user.required' => 'CC is required.',
                'mail_send_send_cc_user.email' => 'CC must be a email.',
                'mail_send_reply_user.required' => 'Reply is required.',
                'mail_send_send_reply_user.email' => 'Reply must be a email.',

                'mail_send_event_for_activity_log.required' => 'Event is required.',
                'mail_send_event_for_activity_log.in' => 'Event must be one out of [All,Delete,Delete all].',
                'mail_send_send_for_activity_log.required' => 'Send is required.',
                'mail_send_send_for_activity_log.in' => 'Send must be one out of [Yes,No].',
                'mail_send_to_from_activity_log.required' => 'From is required.',
                'mail_send_send_from_activity_log.email' => 'From must be a email.',
                'mail_send_to_activity_log.required' => 'To is required.',
                'mail_send_send_to_activity_log.email' => 'To must be a email.',
                'mail_send_cc_activity_log.required' => 'CC is required.',
                'mail_send_send_cc_activity_log.email' => 'CC must be a email.',
                'mail_send_reply_activity_log.required' => 'Reply is required.',
                'mail_send_send_reply_activity_log.email' => 'Reply must be a email.',

                'mail_send_event_for_authentication_log.required' => 'Event is required.',
                'mail_send_event_for_authentication_log.in' => 'Event must be one out of [All,Delete,Delete all].',
                'mail_send_send_for_authentication_log.required' => 'Send is required.',
                'mail_send_send_for_authentication_log.in' => 'Send must be one out of [Yes,No].',
                'mail_send_to_from_authentication_log.required' => 'From is required.',
                'mail_send_send_from_authentication_log.email' => 'From must be a email.',
                'mail_send_to_authentication_log.required' => 'To is required.',
                'mail_send_send_to_authentication_log.email' => 'To must be a email.',
                'mail_send_cc_authentication_log.required' => 'CC is required.',
                'mail_send_send_cc_authentication_log.email' => 'CC must be a email.',
                'mail_send_reply_authentication_log.required' => 'Reply is required.',
                'mail_send_send_reply_authentication_log.email' => 'Reply must be a email.',

                'mail_send_event_for_project_contract.required' => 'Event is required.',
                'mail_send_event_for_project_contract.in' => 'Event must be one out of [All,Create,Update,Delete,Complete,Receiving payment,Complete receive payment].',
                'mail_send_send_for_project_contract.required' => 'Send is required.',
                'mail_send_send_for_project_contract.in' => 'Send must be one out of [Yes,No].',
                'mail_send_to_from_project_contract.required' => 'From is required.',
                'mail_send_send_from_project_contract.email' => 'From must be a email.',
                'mail_send_to_project_contract.required' => 'To is required.',
                'mail_send_send_to_project_contract.email' => 'To must be a email.',
                'mail_send_cc_project_contract.required' => 'CC is required.',
                'mail_send_send_cc_project_contract.email' => 'CC must be a email.',
                'mail_send_reply_project_contract.required' => 'Reply is required.',
                'mail_send_send_reply_project_contract.email' => 'Reply must be a email.',

                'mail_send_event_for_project_contract_project_journal.required' => 'Event is required.',
                'mail_send_event_for_project_contract_journal.in' => 'Event must be one out of [All,Create,Update,Delete].',
                'mail_send_send_for_project_contract_project_journal.required' => 'Send is required.',
                'mail_send_send_for_project_contract_project_journal.in' => 'Send must be one out of [Yes,No].',
                'mail_send_to_from_project_contract_project_journal.required' => 'From is required.',
                'mail_send_send_from_project_contract_project_journal.email' => 'From must be a email.',
                'mail_send_to_project_contract_project_journal.required' => 'To is required.',
                'mail_send_send_to_project_contract_project_journal.email' => 'To must be a email.',
                'mail_send_cc_project_contract_project_journal.required' => 'CC is required.',
                'mail_send_send_cc_project_contract_project_journal.email' => 'CC must be a email.',
                'mail_send_reply_project_contract_project_journal.required' => 'Reply is required.',
                'mail_send_send_reply_project_contract_project_journal.email' => 'Reply must be a email.',

                'mail_send_event_for_project_contract_project_payment.required' => 'Event is required.',
                'mail_send_event_for_project_contract_payment.in' => 'Event must be one out of [All,Create,Update,Delete].',
                'mail_send_send_for_project_contract_project_payment.required' => 'Send is required.',
                'mail_send_send_for_project_contract_project_payment.in' => 'Send must be one out of [Yes,No].',
                'mail_send_to_from_project_contract_project_payment.required' => 'From is required.',
                'mail_send_send_from_project_contract_project_payment.email' => 'From must be a email.',
                'mail_send_to_project_contract_project_payment.required' => 'To is required.',
                'mail_send_send_to_project_contract_project_payment.email' => 'To must be a email.',
                'mail_send_cc_project_contract_project_payment.required' => 'CC is required.',
                'mail_send_send_cc_project_contract_project_payment.email' => 'CC must be a email.',
                'mail_send_reply_project_contract_project_payment.required' => 'Reply is required.',
                'mail_send_send_reply_project_contract_project_payment.email' => 'Reply must be a email.',

                'mail_send_event_for_project_contract_project_payment_method.required' => 'Event is required.',
                'mail_send_event_for_project_contract_project_payment_method.in' => 'Event must be one out of [All,Create,Update,Trash,Restore].',
                'mail_send_send_for_project_contract_project_payment_method.required' => 'Send is required.',
                'mail_send_send_for_project_contract_project_payment_method.in' => 'Send must be one out of [Yes,No].',
                'mail_send_to_from_project_contract_project_payment_method.required' => 'From is required.',
                'mail_send_send_from_project_contract_project_payment_method.email' => 'From must be a email.',
                'mail_send_to_project_contract_project_payment_method.required' => 'To is required.',
                'mail_send_send_to_project_contract_project_payment_method.email' => 'To must be a email.',
                'mail_send_cc_project_contract_project_payment_method.required' => 'CC is required.',
                'mail_send_send_cc_project_contract_project_payment_method.email' => 'CC must be a email.',
                'mail_send_reply_project_contract_project_payment_method.required' => 'Reply is required.',
                'mail_send_send_reply_project_contract_project_payment_method.email' => 'Reply must be a email.',

                'mail_send_event_for_project_contract_category.required' => 'Event is required.',
                'mail_send_event_for_project_contract_category.in' => 'Event must be one out of [All,Create,Update,Trash,Restore].',
                'mail_send_send_for_project_contract_category.required' => 'Send is required.',
                'mail_send_send_for_project_contract_category.in' => 'Send must be one out of [Yes,No].',
                'mail_send_to_from_project_contract_category.required' => 'From is required.',
                'mail_send_send_from_project_contract_category.email' => 'From must be a email.',
                'mail_send_to_project_contract_category.required' => 'To is required.',
                'mail_send_send_to_project_contract_category.email' => 'To must be a email.',
                'mail_send_cc_project_contract_category.required' => 'CC is required.',
                'mail_send_send_cc_project_contract_category.email' => 'CC must be a email.',
                'mail_send_reply_project_contract_category.required' => 'Reply is required.',
                'mail_send_send_reply_project_contract_category.email' => 'Reply must be a email.',

                'mail_send_event_for_user_permission_group.required' => 'Event is required.',
                'mail_send_event_for_user_permission_group.in' => 'Event must be one out of [All,Create,Update,Trash,Restore].',
                'mail_send_send_for_user_permission_group.required' => 'Send is required.',
                'mail_send_send_for_user_permission_group.in' => 'Send must be one out of [Yes,No].',
                'mail_send_to_from_user_permission_group.required' => 'From is required.',
                'mail_send_send_from_user_permission_group.email' => 'From must be a email.',
                'mail_send_to_user_permission_group.required' => 'To is required.',
                'mail_send_send_to_user_permission_group.email' => 'To must be a email.',
                'mail_send_cc_user_permission_group.required' => 'CC is required.',
                'mail_send_send_cc_user_permission_group.email' => 'CC must be a email.',
                'mail_send_reply_user_permission_group.required' => 'Reply is required.',
                'mail_send_send_reply_user_permission_group.email' => 'Reply must be a email.',

                'mail_send_event_for_setting.required' => 'Event is required.',
                'mail_send_event_for_setting.in' => 'Event must be one out of [All,Update].',
                'mail_send_send_for_setting.required' => 'Send is required.',
                'mail_send_send_for_setting.in' => 'Send must be one out of [Yes,No].',
                'mail_send_to_from_setting.required' => 'From is required.',
                'mail_send_send_from_setting.email' => 'From must be a email.',
                'mail_send_to_setting.required' => 'To is required.',
                'mail_send_send_to_setting.email' => 'To must be a email.',
                'mail_send_cc_setting.required' => 'CC is required.',
                'mail_send_send_cc_setting.email' => 'CC must be a email.',
                'mail_send_reply_setting.required' => 'Reply is required.',
                'mail_send_send_reply_setting.email' => 'Reply must be a email.',

                'mail_send_event_for_report.required' => 'Event is required.',
                'mail_send_event_for_report.in' => 'Event must be one out of [All].',
                'mail_send_send_for_report.required' => 'Send is required.',
                'mail_send_send_for_report.in' => 'Send must be one out of [Yes,No].',
                'mail_send_to_from_report.required' => 'From is required.',
                'mail_send_send_from_report.email' => 'From must be a email.',
                'mail_send_to_report.required' => 'To is required.',
                'mail_send_send_to_report.email' => 'To must be a email.',
                'mail_send_cc_report.required' => 'CC is required.',
                'mail_send_send_cc_report.email' => 'CC must be a email.',
                'mail_send_reply_report.required' => 'Reply is required.',
                'mail_send_send_reply_report.email' => 'Reply must be a email.',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $statusInformation = array("status" => "errors","message" => collect());

        LogBatch::startBatch();
            $authenticationLogSetting = Setting::where("slug",$slug)->firstOrFail();
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

    private function sendEmail($event,$subject,Setting $setting ){
        $envelope = array();

        $emailSendSetting = Setting::where( 'code','EmailSendSetting')->firstOrFail()->fields_with_values;

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
