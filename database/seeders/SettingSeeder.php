<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use App\Utilities\SystemConstant;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Setting::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Business setting
        Setting::factory()->state([
            'name' => "Business setting",
            'code' => 'BusinessSetting',
            'slug' => SystemConstant::slugGenerator("Business setting",200),
            'fields_with_values' => array(
                "name" => null,
                "short_name"=> null,
                "email" => null,
                "mobile_no" => null,
                "url" => null,
                "address" => null,
                "description" => null,
                "logo"=> null,
                "favicon"=> null,
            ),
            'created_at' =>  Carbon::now(),
            'updated_at' =>  null,
            'created_by_id' =>  1,
        ])->create();

        // Activity log setting
        Setting::factory()->state([
            'name' => "Activity log setting",
            'code' => 'ActivityLogSetting',
            'slug' => SystemConstant::slugGenerator("Activity log setting",200),
            'fields_with_values' => array(
                "delete_records_older_than" => null,
                "auto_delete" => null,
                "send_email_notification" => null,
            ),
            'created_at' =>  Carbon::now(),
            'updated_at' =>  null,
            'created_by_id' =>  1,
        ])->create();

        // Authentication log setting
        Setting::factory()->state([
            'name' => "Authentication log setting",
            'code' => 'AuthenticationLogSetting',
            'slug' => SystemConstant::slugGenerator("Authentication log setting",200),
            'fields_with_values' => array(
                "delete_records_older_than" => null,
                "auto_delete" => null,
                "send_email_notification" => null,
            ),
            'created_at' =>  Carbon::now(),
            'updated_at' =>  null,
            'created_by_id' =>  1,
        ])->create();

        // Country & currency setting
        Setting::factory()->state([
            'name' => "Country & currency setting",
            'code' => 'CountryAndCurrencySetting',
            'slug' => SystemConstant::slugGenerator("Country and currency setting",200),
            'fields_with_values' => array(
                "country_name" => null,
                "country_code" => null,
                "currency_name" => null,
                "currency_code" => null,
                "currency_symbol" => null,
            ),
            'created_at' =>  Carbon::now(),
            'updated_at' =>  null,
            'created_by_id' =>  1,
        ])->create();
    }
}
