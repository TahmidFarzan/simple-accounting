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
        );

        $defaultCACSettingInfo = array(
            "country_name" => "Bangladesh",
            "country_code" => "BD",
            "currency_name" => "Bangladeshi Taka",
            "currency_code" => "BDT",
            "currency_symbol" => "Tk",
        );

        $dbBUSettingInfo = null;
        $dbCACSettingInfo = null;

        if (Schema::hasTable('settings')) {
            $dbBUSettingInfo = Setting::where("code","BusinessSetting")->first();
            $dbCACSettingInfo = Setting::where("code","CountryAndCurrencySetting")->first();
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
            ),
            "countryAndCurrencySetting"=>array(
                "country_name" => ($dbCACSettingInfo && (strlen($dbCACSettingInfo->fields_with_values["country_name"]) > 0) ) ? $dbCACSettingInfo->fields_with_values["country_name"] : $defaultCACSettingInfo["country_name"],
                "country_code" => ($dbCACSettingInfo && (strlen($dbCACSettingInfo->fields_with_values["country_code"]) > 0) ) ? $dbCACSettingInfo->fields_with_values["country_code"] : $defaultCACSettingInfo["country_code"],
                "currency_name" => ($dbCACSettingInfo && (strlen($dbCACSettingInfo->fields_with_values["currency_name"]) > 0) ) ? $dbCACSettingInfo->fields_with_values["currency_name"] : $defaultCACSettingInfo["currency_name"],
                "currency_code" => ($dbCACSettingInfo && (strlen($dbCACSettingInfo->fields_with_values["country_code"]) > 0) ) ? $dbCACSettingInfo->fields_with_values["currency_code"] : $defaultCACSettingInfo["currency_code"],
                "currency_symbol" => ($dbCACSettingInfo && (strlen($dbCACSettingInfo->fields_with_values["currency_symbol"]) > 0) ) ? $dbCACSettingInfo->fields_with_values["currency_symbol"] : $defaultCACSettingInfo["currency_symbol"],
            ),
        );
    }
}
