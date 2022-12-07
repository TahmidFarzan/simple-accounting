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
                'name' => "View records.",
                'code' => "ACLMP01",
                'type' => "ActivityLogModulePermission",
                'description' => "The internal user can view records.",
                'slug' => SystemConstant::slugGenerator("View  records",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "View details of a record.",
                'code' => "ACLMP02",
                'type' => "ActivityLogModulePermission",
                'description' => "The internal user can view details of a record.",
                'slug' => SystemConstant::slugGenerator("View details of a record",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Delete a record.",
                'code' => "ACLMP03",
                'type' => "ActivityLogModulePermission",
                'description' => "The internal user can delete a record.",
                'slug' => SystemConstant::slugGenerator("Delete a record",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Delete records.",
                'code' => "ACLMP04",
                'type' => "ActivityLogModulePermission",
                'description' => "The internal user can delete records.",
                'slug' => SystemConstant::slugGenerator("Delete records",200),
            ])->create();

        // Authentication log permission.
            UserPermission::factory()->state([
                'name' => "View records.",
                'code' => "AULMP01",
                'type' => "AuthenticationLogModulePermission",
                'description' => "The internal user can view records.",
                'slug' => SystemConstant::slugGenerator("View records",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "View details of a record.",
                'code' => "AULMP02",
                'type' => "AuthenticationLogModulePermission",
                'description' => "The internal user can view details of a record.",
                'slug' => SystemConstant::slugGenerator("View details of a record.",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Delete a record.",
                'code' => "AULMP03",
                'type' => "AuthenticationLogModulePermission",
                'description' => "The internal user can delete a record.",
                'slug' => SystemConstant::slugGenerator("Delete a record",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Delete all record.",
                'code' => "AULMP04",
                'type' => "AuthenticationLogModulePermission",
                'description' => "The internal user can delete all record.",
                'slug' => SystemConstant::slugGenerator("Delete all record",200),
            ])->create();

        // Setting permission.
            UserPermission::factory()->state([
                'name' => "View settings.",
                'code' => "SMP01",
                'type' => "SettingModulePermission",
                'description' => "The internal user can view settings.",
                'slug' => SystemConstant::slugGenerator("View settings",200),
            ])->create();

            // Business setting permissions.
                UserPermission::factory()->state([
                    'name' => "View records.",
                    'code' => "SMP02.01",
                    'type' => "BusinessSettingModulePermission",
                    'description' => "The internal user can view records.",
                    'slug' => SystemConstant::slugGenerator("View records",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "View details of a record.",
                    'code' => "SMP02.02",
                    'type' => "BusinessSettingModulePermission",
                    'description' => "The internal user can view details of a record",
                    'slug' => SystemConstant::slugGenerator("View details of a record",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "Update a record.",
                    'code' => "SMP02.03",
                    'type' => "BusinessSettingModulePermission",
                    'description' => "The internal user can update a record.",
                    'slug' => SystemConstant::slugGenerator("Update a record",200),
                ])->create();

            // Activity log setting permissions.
                UserPermission::factory()->state([
                    'name' => "View records.",
                    'code' => "SMP03.01",
                    'type' => "ActivityLogSettingModulePermission",
                    'description' => "The internal user can view records.",
                    'slug' => SystemConstant::slugGenerator("View records",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "View details of a record.",
                    'code' => "SMP03.02",
                    'type' => "ActivityLogSettingModulePermission",
                    'description' => "The internal user can view details of a record.",
                    'slug' => SystemConstant::slugGenerator("View details of a record",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "Update a record.",
                    'code' => "SMP03.03",
                    'type' => "ActivityLogSettingModulePermission",
                    'description' => "The internal user can update a record.",
                    'slug' => SystemConstant::slugGenerator("Update a record",200),
                ])->create();

            // Authentication log setting permissions.
                UserPermission::factory()->state([
                    'name' => "View records",
                    'code' => "SMP04.01",
                    'type' => "AuthenticationLogSettingModulePermission",
                    'description' => "The internal user can view records",
                    'slug' => SystemConstant::slugGenerator("View records",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "View details of a record.",
                    'code' => "SMP04.02",
                    'type' => "AuthenticationLogSettingModulePermission",
                    'description' => "The internal user can view details of a record",
                    'slug' => SystemConstant::slugGenerator("View details of a record",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "Update a record.",
                    'code' => "SMP04.03",
                    'type' => "AuthenticationLogSettingModulePermission",
                    'description' => "The internal user can update a record.",
                    'slug' => SystemConstant::slugGenerator("Update a record.",200),
                ])->create();

    // -----------------------------------------------------------
        // Extra modul permission.
            // User permission.
                UserPermission::factory()->state([
                    'name' => "View user records",
                    'code' => "UPMP01",
                    'type' => "UserPermissionModulePermission",
                    'description' => "The internal user can view records",
                    'slug' => SystemConstant::slugGenerator("View user records",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "View details of a record.",
                    'code' => "UPMP02",
                    'type' => "UserPermissionModulePermission",
                    'description' => "The internal user can view details of a record",
                    'slug' => SystemConstant::slugGenerator("View details of a record.",200),
                ])->create();

            // User permission group permission.
                UserPermission::factory()->state([
                    'name' => "View records.",
                    'code' => "UPGMP01",
                    'type' => "UserPermissionGroupModulePermission",
                    'description' => "The internal user can view.",
                    'slug' => SystemConstant::slugGenerator("View records",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "Create a record.",
                    'code' => "UPGMP02",
                    'type' => "UserPermissionGroupModulePermission",
                    'description' => "The internal user can create a record.",
                    'slug' => SystemConstant::slugGenerator("Create a record",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "View details of a record.",
                    'code' => "UPGMP03",
                    'type' => "UserPermissionGroupModulePermission",
                    'description' => "The internal user can view authentication log details.",
                    'slug' => SystemConstant::slugGenerator("View details of a record",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "Update a record.",
                    'code' => "UPGMP04",
                    'type' => "UserPermissionGroupModulePermission",
                    'description' => "The internal user can update a record.",
                    'slug' => SystemConstant::slugGenerator("Update a record",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "Delete a record.",
                    'code' => "UPGMP05",
                    'type' => "UserPermissionGroupModulePermission",
                    'description' => "The internal user can delete a record.",
                    'slug' => SystemConstant::slugGenerator("Delete a record",200),
                ])->create();
    // -----------------------------------------------------------
        // Project contract category permission.
            UserPermission::factory()->state([
                'name' => "View the records.",
                'code' => "PCCAMP01",
                'type' => "ProjectContractCategoryModulePermission",
                'description' => "The internal user can view records.",
                'slug' => SystemConstant::slugGenerator("View the records",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Create record.",
                'code' => "PCCAMP02",
                'type' => "ProjectContractCategoryModulePermission",
                'description' => "The internal user can create.",
                'slug' => SystemConstant::slugGenerator("Create record.",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "View details of a record.",
                'code' => "PCCAMP03",
                'type' => "ProjectContractCategoryModulePermission",
                'description' => "The internal user can view.",
                'slug' => SystemConstant::slugGenerator("View details of a record",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Update a record.",
                'code' => "PCCAMP04",
                'type' => "ProjectContractCategoryModulePermission",
                'description' => "The internal user can update.",
                'slug' => SystemConstant::slugGenerator("Update a record.",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Trash a record.",
                'code' => "PCCAMP05",
                'type' => "ProjectContractCategoryModulePermission",
                'description' => "The internal user can trash.",
                'slug' => SystemConstant::slugGenerator("Trash a record.",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Restore a record.",
                'code' => "PCCAMP06",
                'type' => "ProjectContractCategoryModulePermission",
                'description' => "The internal user can restore.",
                'slug' => SystemConstant::slugGenerator("Restore record.",200),
            ])->create();

        // Project contract payment method permission.
            UserPermission::factory()->state([
                'name' => "View records.",
                'code' => "PCPMMP01",
                'type' => "ProjectContractPaymentMethodModulePermission",
                'description' => "The internal user can view records.",
                'slug' => SystemConstant::slugGenerator("View the records",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Create a record.",
                'code' => "PCPMMP02",
                'type' => "ProjectContractPaymentMethodModulePermission",
                'description' => "The internal user can create a record.",
                'slug' => SystemConstant::slugGenerator("Create a record.",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "View details of a record.",
                'code' => "PCPMMP03",
                'type' => "ProjectContractPaymentMethodModulePermission",
                'description' => "The internal user can view details of a record.",
                'slug' => SystemConstant::slugGenerator("View details of a record.",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Update a record.",
                'code' => "PCPMMP04",
                'type' => "ProjectContractPaymentMethodModulePermission",
                'description' => "The internal user can update a record.",
                'slug' => SystemConstant::slugGenerator("Update a record.",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Trash a record.",
                'code' => "PCPMMP05",
                'type' => "ProjectContractPaymentMethodModulePermission",
                'description' => "The internal user can trash a record.",
                'slug' => SystemConstant::slugGenerator("Trash a record.",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Restore a record.",
                'code' => "PCPMMP06",
                'type' => "ProjectContractPaymentMethodModulePermission",
                'description' => "The internal user can restore a record.",
                'slug' => SystemConstant::slugGenerator("Restore a record.",200),
            ])->create();

        // Project contract client permission.
            UserPermission::factory()->state([
                'name' => "View the records.",
                'code' => "PCCLMP01",
                'type' => "ProjectContractClientModulePermission",
                'description' => "The internal user can view records.",
                'slug' => SystemConstant::slugGenerator("View the records",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Create a record.",
                'code' => "PCCLMP02",
                'type' => "ProjectContractClientModulePermission",
                'description' => "The internal user can create a record.",
                'slug' => SystemConstant::slugGenerator("Create a record.",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "View the details of a record.",
                'code' => "PCCLMP03",
                'type' => "ProjectContractClientModulePermission",
                'description' => "The internal user can view details of a record.",
                'slug' => SystemConstant::slugGenerator("View the details of a record.",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Update a record.",
                'code' => "PCCLMP04",
                'type' => "ProjectContractClientModulePermission",
                'description' => "The internal user can update a record.",
                'slug' => SystemConstant::slugGenerator("Update a record.",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Trash a record.",
                'code' => "PCCLMP05",
                'type' => "ProjectContractClientModulePermission",
                'description' => "The internal user can trash a record.",
                'slug' => SystemConstant::slugGenerator("Trash a record.",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Restore a record.",
                'code' => "PCCLMP06",
                'type' => "ProjectContractClientModulePermission",
                'description' => "The internal user can restore a record.",
                'slug' => SystemConstant::slugGenerator("Restore a record.",200),
            ])->create();

        // Project contract  permission.
            UserPermission::factory()->state([
                'name' => "View the records.",
                'code' => "PCMP01",
                'type' => "ProjectContractModulePermission",
                'description' => "The internal user can view records.",
                'slug' => SystemConstant::slugGenerator("View the records",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Create a record.",
                'code' => "PCMP02",
                'type' => "ProjectContractModulePermission",
                'description' => "The internal user can create record.",
                'slug' => SystemConstant::slugGenerator("Create a record.",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "View a record detials.",
                'code' => "PCMP03",
                'type' => "ProjectContractModulePermission",
                'description' => "The internal user can view record details.",
                'slug' => SystemConstant::slugGenerator("View details of a record",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Update a record.",
                'code' => "PCMP04",
                'type' => "ProjectContractModulePermission",
                'description' => "The internal user can update a record.",
                'slug' => SystemConstant::slugGenerator("Update a record.",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Delete a record.",
                'code' => "PCMP05",
                'type' => "ProjectContractModulePermission",
                'description' => "The internal user can delete a record.",
                'slug' => SystemConstant::slugGenerator("Delete a record.",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Change status of a record.",
                'code' => "PCMP06",
                'type' => "ProjectContractModulePermission",
                'description' => "The internal user can change record status.",
                'slug' => SystemConstant::slugGenerator("Change status of a record.",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Change receivable status of a record.",
                'code' => "PCMP07",
                'type' => "ProjectContractModulePermission",
                'description' => "The internal user can change record receivable status.",
                'slug' => SystemConstant::slugGenerator("Change receivable status of a record.",200),
            ])->create();

        // Project contract journal  permission.
            UserPermission::factory()->state([
                'name' => "View the records.",
                'code' => "PCJMP01",
                'type' => "ProjectContractJournalModulePermission",
                'description' => "The internal user can view records.",
                'slug' => SystemConstant::slugGenerator("View the records",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Create a record.",
                'code' => "PCJMP02",
                'type' => "ProjectContractJournalModulePermission",
                'description' => "The internal user can create a record.",
                'slug' => SystemConstant::slugGenerator("Create a record.",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "View details of a record.",
                'code' => "PCJMP03",
                'type' => "ProjectContractJournalModulePermission",
                'description' => "The internal user can view details of a record.",
                'slug' => SystemConstant::slugGenerator("View details of a record.",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Update a record.",
                'code' => "PCJMP04",
                'type' => "ProjectContractJournalModulePermission",
                'description' => "The internal user can update a record.",
                'slug' => SystemConstant::slugGenerator("Update a record.",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Delete a record.",
                'code' => "PCJMP05",
                'type' => "ProjectContractJournalModulePermission",
                'description' => "The internal user can delete a record.",
                'slug' => SystemConstant::slugGenerator("Delete a record.",200),
            ])->create();

        // Project contract category permission
    }
}
