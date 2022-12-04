<?php

namespace Database\Seeders;

use App\Models\UserPermission;
use Illuminate\Database\Seeder;
use App\Utilities\SystemConstant;
use Illuminate\Support\Facades\DB;

class UserPermissionSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        UserPermission::truncate();
        DB::table('user_permission_group_has_user_permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // User permission.
            UserPermission::factory()->state([
                'name' => "View user.",
                'code' => "UMP01",
                'type' => "UserModulePermission",
                'description' => "The internal user can view user.",
                'slug' => SystemConstant::slugGenerator("View setting",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Create user (Owner).",
                'code' => "UMP02",
                'type' => "UserModulePermission",
                'description' => "The internal user can create user (Owner).",
                'slug' => SystemConstant::slugGenerator("Create user",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Create user (Subordinate).",
                'code' => "UMP03",
                'type' => "UserModulePermission",
                'description' => "The internal user can create user (Subordinate).",
                'slug' => SystemConstant::slugGenerator("Create user",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "View user details.",
                'code' => "UMP04",
                'type' => "UserModulePermission",
                'description' => "The internal user can view user details.",
                'slug' => SystemConstant::slugGenerator("View user details",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Update user (Owner).",
                'code' => "UMP05",
                'type' => "UserModulePermission",
                'description' => "The internal user can update user (Owner).",
                'slug' => SystemConstant::slugGenerator("Update user",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Update user (Subordinate).",
                'code' => "UMP06",
                'type' => "UserModulePermission",
                'description' => "The internal user can create user (Subordinate).",
                'slug' => SystemConstant::slugGenerator("Update user",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Trash user (Owner).",
                'code' => "UMP07",
                'type' => "UserModulePermission",
                'description' => "The internal user can trash user (Owner).",
                'slug' => SystemConstant::slugGenerator("Trash user",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Trash user (Subordinate).",
                'code' => "UMP08",
                'type' => "UserModulePermission",
                'description' => "The internal user can trash user (Subordinate).",
                'slug' => SystemConstant::slugGenerator("Trash user",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Restore user (Owner).",
                'code' => "UMP09",
                'type' => "UserModulePermission",
                'description' => "The internal user can restore user (Owner).",
                'slug' => SystemConstant::slugGenerator("Restore user",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Restore user (Subordinate).",
                'code' => "UMP10",
                'type' => "UserModulePermission",
                'description' => "The internal user can restore user (Subordinate).",
                'slug' => SystemConstant::slugGenerator("Restore user",200),
            ])->create();

        // Activity Log permission.
            UserPermission::factory()->state([
                'name' => "View activity log controller.",
                'code' => "ACLMP01",
                'type' => "ActivityLogModulePermission",
                'description' => "The internal user can view controller.",
                'slug' => SystemConstant::slugGenerator("View activity log controller",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "View activity log details.",
                'code' => "ACLMP02",
                'type' => "ActivityLogModulePermission",
                'description' => "The internal user can view activity log details.",
                'slug' => SystemConstant::slugGenerator("View activity log controller details",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Delete activity log.",
                'code' => "ACLMP03",
                'type' => "ActivityLogModulePermission",
                'description' => "The internal user can delete any activity log.",
                'slug' => SystemConstant::slugGenerator("Delete any activity log",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Delete all activity log.",
                'code' => "ACLMP04",
                'type' => "ActivityLogModulePermission",
                'description' => "The internal user can delete all activity log.",
                'slug' => SystemConstant::slugGenerator("Delete all activity log",200),
            ])->create();

        // Authentication log permission.
            UserPermission::factory()->state([
                'name' => "View authentication log.",
                'code' => "AULMP01",
                'type' => "AuthenticationLogModulePermission",
                'description' => "The internal user can view.",
                'slug' => SystemConstant::slugGenerator("View authentication log controller",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "View authentication log details.",
                'code' => "AULMP02",
                'type' => "AuthenticationLogModulePermission",
                'description' => "The internal user can view authentication log details.",
                'slug' => SystemConstant::slugGenerator("View authentication log  details.",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Delete any authentication log.",
                'code' => "AULMP03",
                'type' => "AuthenticationLogModulePermission",
                'description' => "The internal user can delete any authentication log.",
                'slug' => SystemConstant::slugGenerator("Delete any authentication log",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Delete all authentication log.",
                'code' => "AULMP04",
                'type' => "AuthenticationLogModulePermission",
                'description' => "The internal user can delete all authentication log.",
                'slug' => SystemConstant::slugGenerator("Delete all authentication log",200),
            ])->create();

        // Setting log permission.
            UserPermission::factory()->state([
                'name' => "Access setting.",
                'code' => "SMP01",
                'type' => "SettingModulePermission",
                'description' => "The internal user can access seeting.",
                'slug' => SystemConstant::slugGenerator("View setting",200),
            ])->create();

            // Business setting permissions.
                UserPermission::factory()->state([
                    'name' => "View business information.",
                    'code' => "SMP02.01",
                    'type' => "BusinessSettingModulePermission",
                    'description' => "The internal user can view",
                    'slug' => SystemConstant::slugGenerator("View business information",200),
                ])->create();
                UserPermission::factory()->state([
                    'name' => "View business information details.",
                    'code' => "SMP02.02",
                    'type' => "BusinessSettingModulePermission",
                    'description' => "The internal user can view",
                    'slug' => SystemConstant::slugGenerator("View business information details",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "Update business information.",
                    'code' => "SMP02.03",
                    'type' => "BusinessSettingModulePermission",
                    'description' => "The internal user can update business information.",
                    'slug' => SystemConstant::slugGenerator("Update business information",200),
                ])->create();

            // Activity log setting permissions.
                UserPermission::factory()->state([
                    'name' => "View activity log setting",
                    'code' => "SMP03.01",
                    'type' => "ActivityLogSettingModulePermission",
                    'description' => "The internal user can view",
                    'slug' => SystemConstant::slugGenerator("View activity log setting",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "View activity log setting details.",
                    'code' => "SMP03.02",
                    'type' => "ActivityLogSettingModulePermission",
                    'description' => "The internal user can view",
                    'slug' => SystemConstant::slugGenerator("View activity log setting details.",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "Update activity log setting.",
                    'code' => "SMP03.03",
                    'type' => "ActivityLogSettingModulePermission",
                    'description' => "The internal user can update",
                    'slug' => SystemConstant::slugGenerator("Update activity log setting.",200),
                ])->create();

            // Authentication log setting permissions.
                UserPermission::factory()->state([
                    'name' => "View authentication log setting",
                    'code' => "SMP04.01",
                    'type' => "AuthenticationLogSettingModulePermission",
                    'description' => "The internal user can view",
                    'slug' => SystemConstant::slugGenerator("View authentication log setting",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "View authentication log setting details.",
                    'code' => "SMP04.02",
                    'type' => "AuthenticationLogSettingModulePermission",
                    'description' => "The internal user can view",
                    'slug' => SystemConstant::slugGenerator("View authentication log setting details.",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "Update authentication log setting.",
                    'code' => "SMP04.03",
                    'type' => "AuthenticationLogSettingModulePermission",
                    'description' => "The internal user can update",
                    'slug' => SystemConstant::slugGenerator("Update authentication log setting.",200),
                ])->create();

        // Extra modul permission.
            // User permission.
                UserPermission::factory()->state([
                    'name' => "View user permission setting",
                    'code' => "UPMP01",
                    'type' => "UserPermissionModulePermission",
                    'description' => "The internal user can view",
                    'slug' => SystemConstant::slugGenerator("View user permission setting",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "View user permission details.",
                    'code' => "UPMP02",
                    'type' => "UserPermissionModulePermission",
                    'description' => "The internal user can view",
                    'slug' => SystemConstant::slugGenerator("View user permission details.",200),
                ])->create();

            // User permission group permission.
                UserPermission::factory()->state([
                    'name' => "View user permission group.",
                    'code' => "UPGMP01",
                    'type' => "UserPermissionGroupModulePermission",
                    'description' => "The internal user can view.",
                    'slug' => SystemConstant::slugGenerator("View user permission group",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "Create user permission group.",
                    'code' => "UPGMP02",
                    'type' => "UserPermissionGroupModulePermission",
                    'description' => "The internal user can create user permission group.",
                    'slug' => SystemConstant::slugGenerator("Create user permission group",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "View user permission group details.",
                    'code' => "UPGMP03",
                    'type' => "UserPermissionGroupModulePermission",
                    'description' => "The internal user can view authentication log details.",
                    'slug' => SystemConstant::slugGenerator("View user permission group  details.",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "Update user permission group.",
                    'code' => "UPGMP04",
                    'type' => "UserPermissionGroupModulePermission",
                    'description' => "The internal user can update user permission group.",
                    'slug' => SystemConstant::slugGenerator("Update user permission group",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "Delete user permission group.",
                    'code' => "UPGMP05",
                    'type' => "UserPermissionGroupModulePermission",
                    'description' => "The internal user can delete user permission group.",
                    'slug' => SystemConstant::slugGenerator("Delete user permission group",200),
                ])->create();

            // Project contract category permission.
                UserPermission::factory()->state([
                    'name' => "View the category of project contract.",
                    'code' => "PCCAMP01",
                    'type' => "ProjectContractCategoryModulePermission",
                    'description' => "The internal user can view.",
                    'slug' => SystemConstant::slugGenerator("View the category of project contract",200),
                ])->create();
                UserPermission::factory()->state([
                    'name' => "Create a category for the project contract.",
                    'code' => "PCCAMP02",
                    'type' => "ProjectContractCategoryModulePermission",
                    'description' => "The internal user can create.",
                    'slug' => SystemConstant::slugGenerator("Create a category for the project contract.",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "View the category details of the project contract..",
                    'code' => "PCCAMP03",
                    'type' => "ProjectContractCategoryModulePermission",
                    'description' => "The internal user can view.",
                    'slug' => SystemConstant::slugGenerator("View the category details of the project contract.",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "Update a category of the project contract.",
                    'code' => "PCCAMP04",
                    'type' => "ProjectContractCategoryModulePermission",
                    'description' => "The internal user can update.",
                    'slug' => SystemConstant::slugGenerator("Update a category of the project contract.",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "Trash a category of the project contract.",
                    'code' => "PCCAMP05",
                    'type' => "ProjectContractCategoryModulePermission",
                    'description' => "The internal user can trash.",
                    'slug' => SystemConstant::slugGenerator("Trash a category of the project contract.",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "Restore a category of the project contract.",
                    'code' => "PCCAMP06",
                    'type' => "ProjectContractCategoryModulePermission",
                    'description' => "The internal user can restore.",
                    'slug' => SystemConstant::slugGenerator("Restore category of project contract.",200),
                ])->create();

            // Project contract payment method permission.
                UserPermission::factory()->state([
                    'name' => "View the payment method of project contract.",
                    'code' => "PCPMMP01",
                    'type' => "ProjectContractPaymentMethodModulePermission",
                    'description' => "The internal user can view.",
                    'slug' => SystemConstant::slugGenerator("View the payment method of project contract",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "Create a payment method for the project contract.",
                    'code' => "PCPMMP02",
                    'type' => "ProjectContractPaymentMethodModulePermission",
                    'description' => "The internal user can create.",
                    'slug' => SystemConstant::slugGenerator("Create a payment method for the project contract.",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "View the payment method details of the project contract..",
                    'code' => "PCPMMP03",
                    'type' => "ProjectContractPaymentMethodModulePermission",
                    'description' => "The internal user can view.",
                    'slug' => SystemConstant::slugGenerator("View the payment method details of the project contract.",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "Update a payment method of the project contract.",
                    'code' => "PCPMMP04",
                    'type' => "ProjectContractPaymentMethodModulePermission",
                    'description' => "The internal user can update.",
                    'slug' => SystemConstant::slugGenerator("Update a payment method of the project contract.",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "Trash a payment method of the project contract.",
                    'code' => "PCPMMP05",
                    'type' => "ProjectContractPaymentMethodModulePermission",
                    'description' => "The internal user can trash.",
                    'slug' => SystemConstant::slugGenerator("Trash a payment method of the project contract.",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "Restore a payment method of the project contract.",
                    'code' => "PCPMMP06",
                    'type' => "ProjectContractPaymentMethodModulePermission",
                    'description' => "The internal user can restore.",
                    'slug' => SystemConstant::slugGenerator("Restore payment method of project contract.",200),
                ])->create();

            // Project contract client permission.
                UserPermission::factory()->state([
                    'name' => "View the client of project contract.",
                    'code' => "PCCLMP01",
                    'type' => "ProjectContractClientModulePermission",
                    'description' => "The internal user can view.",
                    'slug' => SystemConstant::slugGenerator("View the client of project contract",200),
                ])->create();
                UserPermission::factory()->state([
                    'name' => "Create a client for the project contract.",
                    'code' => "PCCLMP02",
                    'type' => "ProjectContractClientModulePermission",
                    'description' => "The internal user can create.",
                    'slug' => SystemConstant::slugGenerator("Create a client for the project contract.",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "View the client details of the project contract..",
                    'code' => "PCCLMP03",
                    'type' => "ProjectContractClientModulePermission",
                    'description' => "The internal user can view.",
                    'slug' => SystemConstant::slugGenerator("View the client details of the project contract.",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "Update a client of the project contract.",
                    'code' => "PCCLMP04",
                    'type' => "ProjectContractClientModulePermission",
                    'description' => "The internal user can update.",
                    'slug' => SystemConstant::slugGenerator("Update a client of the project contract.",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "Trash a client of the project contract.",
                    'code' => "PCCLMP05",
                    'type' => "ProjectContractClientModulePermission",
                    'description' => "The internal user can trash.",
                    'slug' => SystemConstant::slugGenerator("Trash a client of the project contract.",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "Restore a client of the project contract.",
                    'code' => "PCCLMP06",
                    'type' => "ProjectContractClientModulePermission",
                    'description' => "The internal user can restore.",
                    'slug' => SystemConstant::slugGenerator("Restore client of project contract.",200),
                ])->create();
           // Project contract category permission.
    }
}
