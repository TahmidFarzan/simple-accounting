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
                "country" => null,
                "country_code" => null,
                "currency" => null,
                "currency_code" => null,
                "currency_symbol" => null,
            ),
            'created_at' =>  Carbon::now(),
            'updated_at' =>  null,
            'created_by_id' =>  1,
        ])->create();

        // OAGP setting
        Setting::factory()->state([
            'name' => "OAGP setting",
            'code' => 'OAGPSetting',
            'slug' => SystemConstant::slugGenerator("OAGP setting",200),
            'fields_with_values' => array(
                "oil_unit" => null,
                "gas_unit" => null,
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
            ),
            'created_at' =>  Carbon::now(),
            'updated_at' =>  null,
            'created_by_id' =>  1,
        ])->create();

        // Email send setting
        Setting::factory()->state([
            'name' => "Email send setting",
            'code' => 'EmailSendSetting',
            'slug' => SystemConstant::slugGenerator("Email send setting",200),
            'fields_with_values' => array(
                "from" => "tfarzan007@gmail.com",
                "to" => "tfarzan007@gmail.com",
                "cc" => "tfarzan007@gmail.com",
                "reply" => "tfarzan007@gmail.com",
                "module" => array(
                    "User" => array(
                        "event" => "All", // Create,Update,Trash,Restore.
                        "send" => true,
                    ),
                    "ActivityLog" => array(
                        "event" => "All", // View.
                        "send" => true,
                    ),
                    "AuthenticationLog" => array(
                        "event" => "All", // View.
                        "send" => true,
                    ),
                    "ProjectContract" => array(
                        "event" => "All", // Create,Update,Trash,Restore.
                        "send" => true,
                    ),
                    "ProjectContractJournal" => array(
                        "event" => "All", // Create,Update,Trash,Restore.
                        "send" => true,
                    ),
                    "ProjectContractPayment" => array(
                        "event" => "All", // Create,Update,Trash,Restore.
                        "send" => true,
                    ),
                    "ProjectContractPaymentMethod" => array(
                        "event" => "All", // Create,Update,Trash,Restore.
                        "send" => true,
                    ),
                    "ProjectContractCategory" => array(
                        "event" => "All", // Create,Update,Trash,Restore.
                        "send" => true,
                    ),
                    "UserPermissionGroup" => array(
                        "event" => "All", // Create,Update,Trash,Restore.
                        "send" => true,
                    ),
                    "OilAndGasPump" => array(
                        "event" => "All", // Create,Update,Trash,Restore.
                        "send" => true,
                    ),
                    "OilAndGasPumpProduct" => array(
                        "event" => "All", // Create,Update,Delete.
                        "send" => true,
                    ),
                    "OilAndGasPumpSupplier" => array(
                        "event" => "All", // Create,Update,Delete.
                        "send" => true,
                    ),
                    "OilAndGasPumpInventory" => array(
                        "event" => "All", // Add,Delete.
                        "send" => true,
                    ),
                    "Setting" => array(
                        "event" => "All", // Create,Update,Trash,Restore.
                        "send" => true,
                    ),
                    "Report" => array(
                        "event" => "All", // Create,Update,Trash,Restore.
                        "send" => true,
                        "frequency" => "Daily",
                    ),
                ),
            ),
            'created_at' =>  Carbon::now(),
            'updated_at' =>  null,
            'created_by_id' =>  1,
        ])->create();
    }
}
