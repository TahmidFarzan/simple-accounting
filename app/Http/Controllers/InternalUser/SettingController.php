<?php

namespace App\Http\Controllers\InternalUser;

use Carbon\Carbon;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Utilities\SystemConstant;
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
        $this->middleware(['user.user.permission.check:SMP01']);

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

        // User permissions setting permission.
        $this->middleware(['user.user.permission.check:UPMP01'])->only(["userPermissionIndex"]);
        $this->middleware(['user.user.permission.check:UPMP02'])->only(["userPermissionDetails"]);
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
        $statusInformation = array("status" => "errors","message" => array());
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
            $statusInformation["status"] = "status";
            array_push($statusInformation["message"], "All business setting successfully updated.");
        }
        else{
            array_push($statusInformation["message"], "Fail to update some field. Please update business setting again");
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
                'send_email_notification' => 'required|string|in:Yes,No',
                'auto_delete_scheduler_frequency' => 'required|string|in:Daily,Weekly,Monthly,Quarterly,Yearly',
            ],
            [
                'delete_records_older_than.required' => 'Delete records older than is required.',
                'delete_records_older_than.min' => 'Delete records older than must have min 1.',
                'delete_records_older_than.max' => 'Delete records older than must have max 365.',
                'delete_records_older_than.numeric' => 'Delete records older than must a numeric numeric.',

                'auto_delete.required' => 'Auto delete is required.',
                'auto_delete.string' => 'Auto delete must be a string.',
                'auto_delete.in' => 'Auto delete either Yes or No.',

                'send_email_notification.required' => 'Send email notification is required.',
                'send_email_notification.string' => 'Send email notification  must be a string.',
                'send_email_notification.in' => 'Send email notification either Yes or No.',

                'auto_delete_scheduler_frequency.required' => 'Auto delete scheduler frequency is required.',
                'auto_delete_scheduler_frequency.string' => 'Auto delete scheduler frequency must be a string.',
                'auto_delete_scheduler_frequency.in' => 'Auto delete scheduler frequency must one out of [Daily,Weekly,Monthly,Quarterly,Yearly].',
            ]
        );
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $statusInformation = array("status" => "errors","message" => array());

        LogBatch::startBatch();
            $activityLogSetting = Setting::where("slug",$slug)->firstOrFail();
            $activityLogSettingFieldsWithValues = $activityLogSetting->fields_with_values;

            $activityLogSettingFieldsWithValues['delete_records_older_than'] = $request->delete_records_older_than;
            $activityLogSettingFieldsWithValues['auto_delete'] = $request->auto_delete;
            $activityLogSettingFieldsWithValues['send_mobile_notification'] = $request->send_mobile_notification;
            $activityLogSettingFieldsWithValues['send_email_notification'] = $request->send_email_notification;
            $activityLogSettingFieldsWithValues['auto_delete_scheduler_frequency'] = $request->auto_delete_scheduler_frequency;

            $activityLogSetting->fields_with_values = $activityLogSettingFieldsWithValues;
            $activityLogSetting->updated_at = Carbon::now();
            $updateActivityLogSetting = $activityLogSetting->update();
        LogBatch::endBatch();

        if($updateActivityLogSetting){
            $statusInformation["status"] = "status";
            array_push($statusInformation["message"], "All activity log setting successfully updated.");
        }
        else{
            array_push($statusInformation["message"], "Fail to update some field. Please update activity log setting again");
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
                'send_email_notification' => 'required|string|in:Yes,No',
                'auto_delete_scheduler_frequency' => 'required|string|in:Daily,Weekly,Monthly,Quarterly,Yearly',
            ],
            [
                'delete_records_older_than.required' => 'Delete records older than is required.',
                'delete_records_older_than.min' => 'Delete records older than must have min 1.',
                'delete_records_older_than.max' => 'Delete records older than must have max 365.',
                'delete_records_older_than.numeric' => 'Delete records older than must a numeric numeric.',

                'auto_delete.required' => 'Auto delete is required.',
                'auto_delete.string' => 'Auto delete must be a string.',
                'auto_delete.in' => 'Auto delete either Yes or No.',

                'send_email_notification.required' => 'Send email notification is required.',
                'send_email_notification.string' => 'Send email notification  must be a string.',
                'send_email_notification.in' => 'Send email notification either Yes or No.',

                'auto_delete_scheduler_frequency.required' => 'Auto delete scheduler frequency is required.',
                'auto_delete_scheduler_frequency.string' => 'Auto delete scheduler frequency must be a string.',
                'auto_delete_scheduler_frequency.in' => 'Auto delete scheduler frequency must one out of [Daily,Weekly,Monthly,Quarterly,Yearly].',
            ]
        );
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $statusInformation = array("status" => "errors","message" => array());

        LogBatch::startBatch();
            $authenticationLogSetting = Setting::where("slug",$slug)->firstOrFail();
            $authenticationLogSettingFieldsWithValues = $authenticationLogSetting->fields_with_values;

            $authenticationLogSettingFieldsWithValues['delete_records_older_than'] = $request->delete_records_older_than;
            $authenticationLogSettingFieldsWithValues['auto_delete'] = $request->auto_delete;
            $authenticationLogSettingFieldsWithValues['send_mobile_notification'] = $request->send_mobile_notification;
            $authenticationLogSettingFieldsWithValues['send_email_notification'] = $request->send_email_notification;
            $authenticationLogSettingFieldsWithValues['auto_delete_scheduler_frequency'] = $request->auto_delete_scheduler_frequency;

            $authenticationLogSetting->fields_with_values = $authenticationLogSettingFieldsWithValues;
            $authenticationLogSetting->updated_at = Carbon::now();
            $updateActivityLogSetting = $authenticationLogSetting->update();
        LogBatch::endBatch();

        if($updateActivityLogSetting){
            $statusInformation["status"] = "status";
            array_push($statusInformation["message"], "All authentication log setting successfully updated.");
        }
        else{
            array_push($statusInformation["message"], "Fail to update some field. Please update authentication log setting again");
        }

        // Redirect logic.
        return redirect()->route("setting.authentication.log.setting.index")->with([$statusInformation["status"] => $statusInformation["message"]]);
    }
}
