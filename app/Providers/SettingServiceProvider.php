<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class SettingServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        view()->share("setting",$this->getSetting());
    }

    private function getSetting(){
        $defaultBuSettingInfo = array(
            "name" => "Simple accounting",
            "short_name" => "SA",
            "email" => "tfarzan007@gmail.com",
            "mobile_no" => "+8801671786285",
            "url" => "https://www.google.com/",
            "address" => "Village# Nischintopur,Thana#Kaligonj,Post#Naldanga-7350,District# Jhenaidah",
            "description" => "Software Engineer",
            "logo" => "default-logo.png",
            "favicon" => "default-favicon.ico",
            "country" => "Bangladesh",
            "country_code" => "BD",
            "currency" => "Bangladeshi Taka",
            "currency_code" => "BDT",
            "currency_symbol" => "Tk",
            "country" => "Bangladesh",
        );

        $dbBUSettingInfo = null;

        if (Schema::hasTable('settings')) {
            $dbBUSettingInfo = Setting::where("code","BusinessSetting")->first();
        }
        return array(
            "businessSetting"=>array(
                "name" => ($dbBUSettingInfo && (strlen($dbBUSettingInfo->fields_with_values["name"]) > 0)) ? $dbBUSettingInfo->fields_with_values["name"] : $defaultBuSettingInfo['name'],
                "short_name"=> ($dbBUSettingInfo && (strlen($dbBUSettingInfo->fields_with_values["short_name"]) > 0)) ? $dbBUSettingInfo->fields_with_values["short_name"] : $defaultBuSettingInfo['short_name'],
                "email" => ($dbBUSettingInfo && (strlen($dbBUSettingInfo->fields_with_values["email"]) > 0)) ? $dbBUSettingInfo->fields_with_values["email"] : $defaultBuSettingInfo['email'],
                "mobile_no" => ($dbBUSettingInfo && (strlen($dbBUSettingInfo->fields_with_values["mobile_no"]) > 0)) ?  $dbBUSettingInfo->fields_with_values["mobile_no"] : $defaultBuSettingInfo['mobile_no'],
                "url" => ($dbBUSettingInfo && (strlen($dbBUSettingInfo->fields_with_values["url"]) > 0)) ? $dbBUSettingInfo->fields_with_values["url"] : $defaultBuSettingInfo['url'],
                "address" => ($dbBUSettingInfo && (strlen($dbBUSettingInfo->fields_with_values["address"]) > 0)) ? $dbBUSettingInfo->fields_with_values["address"] : $defaultBuSettingInfo['address'],
                "description" => ($dbBUSettingInfo && (strlen($dbBUSettingInfo->fields_with_values["description"]) > 0)) ? $dbBUSettingInfo->fields_with_values["description"] : $defaultBuSettingInfo['description'],
                "logo" => ($dbBUSettingInfo && (strlen($dbBUSettingInfo->fields_with_values["logo"]) > 0)) ? $dbBUSettingInfo->fields_with_values["logo"] : $defaultBuSettingInfo['logo'],
                "favicon" => ($dbBUSettingInfo && (strlen($dbBUSettingInfo->fields_with_values["favicon"]) > 0)) ? $dbBUSettingInfo->fields_with_values["favicon"] : $defaultBuSettingInfo['favicon'],
                "country" => ($dbBUSettingInfo && (strlen($dbBUSettingInfo->fields_with_values["country"]) > 0) ) ? $dbBUSettingInfo->fields_with_values["country"] : $defaultBuSettingInfo["country"],
                "country_code" => ($dbBUSettingInfo && (strlen($dbBUSettingInfo->fields_with_values["country_code"]) > 0) ) ? $dbBUSettingInfo->fields_with_values["country_code"] : $defaultBuSettingInfo["country_code"],
                "currency" => ($dbBUSettingInfo && (strlen($dbBUSettingInfo->fields_with_values["currency"]) > 0) ) ? $dbBUSettingInfo->fields_with_values["currency"] : $defaultBuSettingInfo["currency"],
                "currency_code" => ($dbBUSettingInfo && (strlen($dbBUSettingInfo->fields_with_values["country_code"]) > 0) ) ? $dbBUSettingInfo->fields_with_values["currency_code"] : $defaultBuSettingInfo["currency_code"],
                "currency_symbol" => ($dbBUSettingInfo && (strlen($dbBUSettingInfo->fields_with_values["currency_symbol"]) > 0) ) ? $dbBUSettingInfo->fields_with_values["currency_symbol"] : $defaultBuSettingInfo["currency_symbol"],
            ),
        );
    }
}
