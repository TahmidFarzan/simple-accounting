<?php

namespace App\Utilities;

use Auth;
use Carbon\Carbon;
use App\Models\Setting;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class SystemConstant
{
    public static function slugGenerator($title,$maxLength){
        $defaultLengtn = ($maxLength > 0) ? $maxLength : 200;
        if (Str::of($title)->length() > $defaultLengtn) {
            $limitedTitleString = Str::of($title)->limit($defaultLengtn);
            $title=$limitedTitleString;
        }
        $slug = ($title == null) ? "title" : $title;
        $slug = Str::slug((str_replace(' ', '-',str_replace('.', '',$slug)))).'-'.Str::random(5).'-'.date('Ymdhis');
        return Str::lower((Auth::check() == true) ? $slug.'-'.Auth::user()->id : $slug);
    }

    public static function codeGenerator($title,$maxLength){
        $defaultLengtn = ($maxLength > 0) ? $maxLength : 200;
        if (Str::of($title)->length() > $defaultLengtn) {
            $limitedTitleString = Str::of($title)->limit($defaultLengtn);
            $title = $limitedTitleString;
        }

        $codeString = str_replace(' ', '',Str::title(str_replace('.', '',$title)));
        return $codeString;
    }

    public static function generateFileName($fileName,$fileExtention,$maxLength){
        if (Str::of($fileName)->length() > 200) {
            $limitFileName = Str::of($fileName)->limit($maxLength);
            $fileName = $limitFileName;
        }

        if($fileName == null){
            $fileName == "File";
        }

        $fileNameGenerate = (str_replace(' ', '-',str_replace('.', '',Str::studly($fileName)))).'-'.Str::random(5).'-'.date('Ymdhis');
        if (Auth::check() == true) {
            $fileNameGenerateAfterAuth = $fileNameGenerate.'-u'.Auth::user()->id;
            $fileNameGenerate = $fileNameGenerateAfterAuth;
        }
        return $fileNameGenerate.'.'.$fileExtention;
    }

    public static function generateExportFileName($fileName,$fileExtention,$maxLength){
        if (Str::of($fileName)->length() > 200) {
            $limitFileName = Str::of($fileName)->limit($maxLength);
            $fileName = $limitFileName;
        }

        if($fileName == null){
            $fileName = "File";
        }

        $fileNameGenerate = (Str::title($fileName)).' at '.date('YMdhisA');
        if (Auth::check() == true) {
            $fileNameGenerateAfterAuth = $fileNameGenerate.' by '.Auth::user()->name;
            $fileNameGenerate = $fileNameGenerateAfterAuth;
        }
        return $fileNameGenerate.'.'.$fileExtention;
    }

    public static function activityLogSetting(){
        $dbActivityLogSettingInfo = Setting::where("code","ActivityLogSetting")->first();

        $autoDelete = "Yes";
        $deleteRecordOlderThan = 10;
        $sendEmailNotification = "Yes";
        $autoDeleteSchedulerFrequency = "Daily";

        if($dbActivityLogSettingInfo){
            $activityLogSetting = $dbActivityLogSettingInfo->fields_with_values;
            $autoDelete = (!($activityLogSetting["auto_delete"] == null)) ? $activityLogSetting["auto_delete"] : $autoDelete;
            $deleteRecordOlderThan = ($activityLogSetting["delete_records_older_than"] > 0) ? $activityLogSetting["delete_records_older_than"] : $deleteRecordOlderThan;
            $sendEmailNotification = (!($activityLogSetting["send_email_notification"] == null)) ? $activityLogSetting["send_email_notification"] : $sendEmailNotification;
            $autoDeleteSchedulerFrequency = (!($activityLogSetting["auto_delete_scheduler_frequency"] == null)) ? $activityLogSetting["send_mobile_notification"] : $autoDeleteSchedulerFrequency;
        }
        return array(
            "auto_delete" => $autoDelete,
            "delete_records_older_than" => $deleteRecordOlderThan,
            "send_email_notification" => $sendEmailNotification,
            "auto_delete_scheduler_frequency" => $autoDeleteSchedulerFrequency,
        );
    }

    public static function authenticationLogSetting(){
        $dbAuthenticationLogSettingInfo = Setting::where("code","AuthenticationLogSetting")->first();

        $autoDelete = "Yes";
        $deleteRecordOlderThan = 10;
        $sendEmailNotification = "Yes";
        $sendMobileNotification = "Yes";
        $autoDeleteSchedulerFrequency = "Daily";

        if($dbAuthenticationLogSettingInfo){
            $authenticationLogSetting = $dbAuthenticationLogSettingInfo->fields_with_values;
            $autoDelete = (!($authenticationLogSetting["auto_delete"]==null)) ? $authenticationLogSetting["auto_delete"] : $autoDelete;
            $deleteRecordOlderThan = ($authenticationLogSetting["delete_records_older_than"] > 0) ? $authenticationLogSetting["delete_records_older_than"] : $deleteRecordOlderThan;
            $sendEmailNotification = (!($authenticationLogSetting["send_email_notification"] == null)) ? $authenticationLogSetting["send_email_notification"] : $sendEmailNotification;
            $sendMobileNotification = (!($authenticationLogSetting["send_mobile_notification"] == null)) ? $authenticationLogSetting["send_mobile_notification"] : $sendMobileNotification;
            $autoDeleteSchedulerFrequency = (!($authenticationLogSetting["auto_delete_scheduler_frequency"] == null)) ? $authenticationLogSetting["send_mobile_notification"] : $autoDeleteSchedulerFrequency;
        }
        return array(
            "auto_delete" => $autoDelete,
            "delete_records_older_than" => $deleteRecordOlderThan,
            "send_email_notification" => $sendEmailNotification,
            "send_mobile_notification" => $sendMobileNotification,
            "auto_delete_scheduler_frequency" => $autoDeleteSchedulerFrequency,
        );
    }
}
