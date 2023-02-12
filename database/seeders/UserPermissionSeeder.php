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
                'name' => "View users.",
                'code' => "UMP01",
                'type' => "UserModulePermission",
                'description' => "The internal user can view users.",
                'slug' => SystemConstant::slugGenerator("View users",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Create a user (Owner).",
                'code' => "UMP02",
                'type' => "UserModulePermission",
                'description' => "The internal user can create a user (Owner).",
                'slug' => SystemConstant::slugGenerator("Create a user",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Create a user (Subordinate).",
                'code' => "UMP03",
                'type' => "UserModulePermission",
                'description' => "The internal user can create a user (Subordinate).",
                'slug' => SystemConstant::slugGenerator("Create a user",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "View details of a user.",
                'code' => "UMP04",
                'type' => "UserModulePermission",
                'description' => "The internal user can view details of a user.",
                'slug' => SystemConstant::slugGenerator("View details of a user",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Update a user (Owner).",
                'code' => "UMP05",
                'type' => "UserModulePermission",
                'description' => "The internal user can update a user (Owner).",
                'slug' => SystemConstant::slugGenerator("Update a user",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Update a user (Subordinate).",
                'code' => "UMP06",
                'type' => "UserModulePermission",
                'description' => "The internal user can update a user (Subordinate).",
                'slug' => SystemConstant::slugGenerator("Update a user",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Trash a user (Owner).",
                'code' => "UMP07",
                'type' => "UserModulePermission",
                'description' => "The internal user can trash a user (Owner).",
                'slug' => SystemConstant::slugGenerator("Trash a user",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Trash a user (Subordinate).",
                'code' => "UMP08",
                'type' => "UserModulePermission",
                'description' => "The internal user can trash a user (Subordinate).",
                'slug' => SystemConstant::slugGenerator("Trash a user",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Restore a user (Owner).",
                'code' => "UMP09",
                'type' => "UserModulePermission",
                'description' => "The internal user can restore a user (Owner).",
                'slug' => SystemConstant::slugGenerator("Restore a user",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Restore a user (Subordinate).",
                'code' => "UMP10",
                'type' => "UserModulePermission",
                'description' => "The internal user can restore a user (Subordinate).",
                'slug' => SystemConstant::slugGenerator("Restore a user",200),
            ])->create();

        // Activity Log permission.
            UserPermission::factory()->state([
                'name' => "View activity Logs.",
                'code' => "ACLMP01",
                'type' => "ActivityLogModulePermission",
                'description' => "The internal user can view activity Logs.",
                'slug' => SystemConstant::slugGenerator("View  activity Logs",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "View details of a activity Log.",
                'code' => "ACLMP02",
                'type' => "ActivityLogModulePermission",
                'description' => "The internal user can view details of a activity Log.",
                'slug' => SystemConstant::slugGenerator("View details of a activity Log",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Delete a activity Log.",
                'code' => "ACLMP03",
                'type' => "ActivityLogModulePermission",
                'description' => "The internal user can delete a activity Log.",
                'slug' => SystemConstant::slugGenerator("Delete a activity Log",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Delete activity Logs.",
                'code' => "ACLMP04",
                'type' => "ActivityLogModulePermission",
                'description' => "The internal user can delete activity Logs.",
                'slug' => SystemConstant::slugGenerator("Delete activity Logs",200),
            ])->create();

        // Authentication log permission.
            UserPermission::factory()->state([
                'name' => "View authentication logs.",
                'code' => "AULMP01",
                'type' => "AuthenticationLogModulePermission",
                'description' => "The internal user can view authentication logs.",
                'slug' => SystemConstant::slugGenerator("View authentication logs",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "View details of a authentication log.",
                'code' => "AULMP02",
                'type' => "AuthenticationLogModulePermission",
                'description' => "The internal user can view details of a authentication log.",
                'slug' => SystemConstant::slugGenerator("View details of a authentication log.",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Delete a authentication log.",
                'code' => "AULMP03",
                'type' => "AuthenticationLogModulePermission",
                'description' => "The internal user can delete a authentication log.",
                'slug' => SystemConstant::slugGenerator("Delete a authentication log",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Delete all authentication log.",
                'code' => "AULMP04",
                'type' => "AuthenticationLogModulePermission",
                'description' => "The internal user can delete all aauthentication log.",
                'slug' => SystemConstant::slugGenerator("Delete all authentication log",200),
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
                    'name' => "View business settings.",
                    'code' => "SMP02.01",
                    'type' => "BusinessSettingModulePermission",
                    'description' => "The internal user can view business settings.",
                    'slug' => SystemConstant::slugGenerator("View business settings",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "View details of a business setting.",
                    'code' => "SMP02.02",
                    'type' => "BusinessSettingModulePermission",
                    'description' => "The internal user can view details of a business setting",
                    'slug' => SystemConstant::slugGenerator("View details of a business setting",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "Update a business setting.",
                    'code' => "SMP02.03",
                    'type' => "BusinessSettingModulePermission",
                    'description' => "The internal user can update a business setting.",
                    'slug' => SystemConstant::slugGenerator("Update a business setting",200),
                ])->create();

            // Activity log setting permissions.
                UserPermission::factory()->state([
                    'name' => "View activity log settings.",
                    'code' => "SMP03.01",
                    'type' => "ActivityLogSettingModulePermission",
                    'description' => "The internal user can view activity log settings.",
                    'slug' => SystemConstant::slugGenerator("View activity log settings",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "View details of a activity log setting.",
                    'code' => "SMP03.02",
                    'type' => "ActivityLogSettingModulePermission",
                    'description' => "The internal user can view details of a activity log setting.",
                    'slug' => SystemConstant::slugGenerator("View details of a activity log setting",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "Update a activity log setting.",
                    'code' => "SMP03.03",
                    'type' => "ActivityLogSettingModulePermission",
                    'description' => "The internal user can update a activity log setting.",
                    'slug' => SystemConstant::slugGenerator("Update a activity log setting",200),
                ])->create();

            // Authentication log setting permissions.
                UserPermission::factory()->state([
                    'name' => "View authentication log settings",
                    'code' => "SMP04.01",
                    'type' => "AuthenticationLogSettingModulePermission",
                    'description' => "The internal user can view authentication log settings",
                    'slug' => SystemConstant::slugGenerator("View authentication log settings",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "View details of a authentication log.",
                    'code' => "SMP04.02",
                    'type' => "AuthenticationLogSettingModulePermission",
                    'description' => "The internal user can view details of a authentication log",
                    'slug' => SystemConstant::slugGenerator("View details of a authentication log",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "Update a authentication log setting.",
                    'code' => "SMP04.03",
                    'type' => "AuthenticationLogSettingModulePermission",
                    'description' => "The internal user can update a authentication log setting.",
                    'slug' => SystemConstant::slugGenerator("Update a authentication log setting.",200),
                ])->create();

            // Email send setting permissions.
                UserPermission::factory()->state([
                    'name' => "View email send settings",
                    'code' => "SMP05.01",
                    'type' => "EmailSendSettingModulePermission",
                    'description' => "The internal user can view email send settings",
                    'slug' => SystemConstant::slugGenerator("View email send settings",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "View details of a email send setting.",
                    'code' => "SMP05.02",
                    'type' => "EmailSendSettingModulePermission",
                    'description' => "The internal user can view details of a email send setting",
                    'slug' => SystemConstant::slugGenerator("View details of a email send setting",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "Update a email send setting.",
                    'code' => "SMP05.03",
                    'type' => "EmailSendSettingModulePermission",
                    'description' => "The internal user can update a email send setting send setting.",
                    'slug' => SystemConstant::slugGenerator("Update a email send setting send setting.",200),
                ])->create();
    // -----------------------------------------------------------
        // Extra modul permission.
            // User permission.
                UserPermission::factory()->state([
                    'name' => "View user permissions",
                    'code' => "UPMP01",
                    'type' => "UserPermissionModulePermission",
                    'description' => "The internal user can view user permissions",
                    'slug' => SystemConstant::slugGenerator("View user user permissions",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "View details of a user permission.",
                    'code' => "UPMP02",
                    'type' => "UserPermissionModulePermission",
                    'description' => "The internal user can view details of a user permission",
                    'slug' => SystemConstant::slugGenerator("View details of a user permission.",200),
                ])->create();

            // User permission group permission.
                UserPermission::factory()->state([
                    'name' => "View user permission groups.",
                    'code' => "UPGMP01",
                    'type' => "UserPermissionGroupModulePermission",
                    'description' => "The internal user can view.",
                    'slug' => SystemConstant::slugGenerator("View user permission groups",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "Create a user permission group.",
                    'code' => "UPGMP02",
                    'type' => "UserPermissionGroupModulePermission",
                    'description' => "The internal user can create a user permission group.",
                    'slug' => SystemConstant::slugGenerator("Create a user permission group",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "View details of a user permission group.",
                    'code' => "UPGMP03",
                    'type' => "UserPermissionGroupModulePermission",
                    'description' => "The internal user can view details of a user permission group.",
                    'slug' => SystemConstant::slugGenerator("View details of a user permission group",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "Update a user permission group.",
                    'code' => "UPGMP04",
                    'type' => "UserPermissionGroupModulePermission",
                    'description' => "The internal user can update a user permission group.",
                    'slug' => SystemConstant::slugGenerator("Update a user permission group",200),
                ])->create();

                UserPermission::factory()->state([
                    'name' => "Delete a user permission group.",
                    'code' => "UPGMP05",
                    'type' => "UserPermissionGroupModulePermission",
                    'description' => "The internal user can delete a user permission group.",
                    'slug' => SystemConstant::slugGenerator("Delete a user permission group",200),
                ])->create();
    // -----------------------------------------------------------
        // Project contract category permission.
            UserPermission::factory()->state([
                'name' => "View the categories.",
                'code' => "PCCAMP01",
                'type' => "ProjectContractCategoryModulePermission",
                'description' => "The internal user can view categories.",
                'slug' => SystemConstant::slugGenerator("View the categories",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Create a category.",
                'code' => "PCCAMP02",
                'type' => "ProjectContractCategoryModulePermission",
                'description' => "The internal user can create a category.",
                'slug' => SystemConstant::slugGenerator("Create a category.",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "View details of a category.",
                'code' => "PCCAMP03",
                'type' => "ProjectContractCategoryModulePermission",
                'description' => "The internal user can view.",
                'slug' => SystemConstant::slugGenerator("View details of a category",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Update a category.",
                'code' => "PCCAMP04",
                'type' => "ProjectContractCategoryModulePermission",
                'description' => "The internal user can update a category.",
                'slug' => SystemConstant::slugGenerator("Update a category.",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Trash a category.",
                'code' => "PCCAMP05",
                'type' => "ProjectContractCategoryModulePermission",
                'description' => "The internal user can trash a category.",
                'slug' => SystemConstant::slugGenerator("Trash a category.",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Restore a category.",
                'code' => "PCCAMP06",
                'type' => "ProjectContractCategoryModulePermission",
                'description' => "The internal user can restore a category.",
                'slug' => SystemConstant::slugGenerator("Restore a category.",200),
            ])->create();

        // Project contract payment method permission.
            UserPermission::factory()->state([
                'name' => "View payment methods.",
                'code' => "PCPMMP01",
                'type' => "ProjectContractPaymentMethodModulePermission",
                'description' => "The internal user can view payment methods.",
                'slug' => SystemConstant::slugGenerator("View the payment methods",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Create a payment method.",
                'code' => "PCPMMP02",
                'type' => "ProjectContractPaymentMethodModulePermission",
                'description' => "The internal user can create a payment method.",
                'slug' => SystemConstant::slugGenerator("Create a payment method",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "View details of a payment method.",
                'code' => "PCPMMP03",
                'type' => "ProjectContractPaymentMethodModulePermission",
                'description' => "The internal user can view details of a payment method.",
                'slug' => SystemConstant::slugGenerator("View details of a payment method",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Update a payment method.",
                'code' => "PCPMMP04",
                'type' => "ProjectContractPaymentMethodModulePermission",
                'description' => "The internal user can update a payment method.",
                'slug' => SystemConstant::slugGenerator("Update a payment method",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Trash a payment method.",
                'code' => "PCPMMP05",
                'type' => "ProjectContractPaymentMethodModulePermission",
                'description' => "The internal user can trash a payment method.",
                'slug' => SystemConstant::slugGenerator("Trash a payment method",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Restore a payment method.",
                'code' => "PCPMMP06",
                'type' => "ProjectContractPaymentMethodModulePermission",
                'description' => "The internal user can restore a payment method.",
                'slug' => SystemConstant::slugGenerator("Restore a payment method",200),
            ])->create();

        // Project contract client permission.
            UserPermission::factory()->state([
                'name' => "View the clients.",
                'code' => "PCCLMP01",
                'type' => "ProjectContractClientModulePermission",
                'description' => "The internal user can view clients.",
                'slug' => SystemConstant::slugGenerator("View the clients",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Create a client.",
                'code' => "PCCLMP02",
                'type' => "ProjectContractClientModulePermission",
                'description' => "The internal user can create a client.",
                'slug' => SystemConstant::slugGenerator("Create a client.",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "View the details of a client.",
                'code' => "PCCLMP03",
                'type' => "ProjectContractClientModulePermission",
                'description' => "The internal user can view details of a client.",
                'slug' => SystemConstant::slugGenerator("View the details of a client.",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Update a client.",
                'code' => "PCCLMP04",
                'type' => "ProjectContractClientModulePermission",
                'description' => "The internal user can update a client.",
                'slug' => SystemConstant::slugGenerator("Update a client.",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Trash a client.",
                'code' => "PCCLMP05",
                'type' => "ProjectContractClientModulePermission",
                'description' => "The internal user can trash a client.",
                'slug' => SystemConstant::slugGenerator("Trash a client.",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Restore a client.",
                'code' => "PCCLMP06",
                'type' => "ProjectContractClientModulePermission",
                'description' => "The internal user can restore a client.",
                'slug' => SystemConstant::slugGenerator("Restore a client.",200),
            ])->create();

        // Project contract  permission.
            UserPermission::factory()->state([
                'name' => "View project contracts.",
                'code' => "PCMP01",
                'type' => "ProjectContractModulePermission",
                'description' => "The internal user can view project contracts.",
                'slug' => SystemConstant::slugGenerator("View project contracts",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Create a project contracts.",
                'code' => "PCMP02",
                'type' => "ProjectContractModulePermission",
                'description' => "The internal user can create a project contract.",
                'slug' => SystemConstant::slugGenerator("Create a project contract",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "View detials of a project contract.",
                'code' => "PCMP03",
                'type' => "ProjectContractModulePermission",
                'description' => "The internal user can view details of a project contract.",
                'slug' => SystemConstant::slugGenerator("View detials of a project contract",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Update a project contract.",
                'code' => "PCMP04",
                'type' => "ProjectContractModulePermission",
                'description' => "The internal user can update a project contract.",
                'slug' => SystemConstant::slugGenerator("Update a project contract",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Delete a project contract.",
                'code' => "PCMP05",
                'type' => "ProjectContractModulePermission",
                'description' => "The internal user can delete a project contract.",
                'slug' => SystemConstant::slugGenerator("Delete a project contract",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Complete a project contract.",
                'code' => "PCMP06",
                'type' => "ProjectContractModulePermission",
                'description' => "The internal user can complete a project contract.",
                'slug' => SystemConstant::slugGenerator("Complete a project contract",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Start receiving payment for project contract.",
                'code' => "PCMP07",
                'type' => "ProjectContractModulePermission",
                'description' => "The internal user can start receiving payment for project contract.",
                'slug' => SystemConstant::slugGenerator("Start receiving payment for project contract",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Complete receive payment for project contract.",
                'code' => "PCMP08",
                'type' => "ProjectContractModulePermission",
                'description' => "The internal user can complete receive payment for project contract.",
                'slug' => SystemConstant::slugGenerator("Complete receive payment for project contract",200),
            ])->create();

        // Project contract journal  permission.
            UserPermission::factory()->state([
                'name' => "View the journal.",
                'code' => "PCJMP01",
                'type' => "ProjectContractJournalModulePermission",
                'description' => "The internal user can view the journal.",
                'slug' => SystemConstant::slugGenerator("View the journal",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Create a journal entry.",
                'code' => "PCJMP02",
                'type' => "ProjectContractJournalModulePermission",
                'description' => "The internal user can create a a journal entry.",
                'slug' => SystemConstant::slugGenerator("Create a a journal entry.",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "View details of a journal entry.",
                'code' => "PCJMP03",
                'type' => "ProjectContractJournalModulePermission",
                'description' => "The internal user can view details of a journal entry.",
                'slug' => SystemConstant::slugGenerator("View details of a journal entry.",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Update a journal entry.",
                'code' => "PCJMP04",
                'type' => "ProjectContractJournalModulePermission",
                'description' => "The internal user can update a journal entry.",
                'slug' => SystemConstant::slugGenerator("Update a journal entry.",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Delete a journal entry.",
                'code' => "PCJMP05",
                'type' => "ProjectContractJournalModulePermission",
                'description' => "The internal user can delete a journal entry.",
                'slug' => SystemConstant::slugGenerator("Delete a journal entry.",200),
            ])->create();

        // Project contract payment  permission.
            UserPermission::factory()->state([
                'name' => "View payments.",
                'code' => "PCPMP01",
                'type' => "ProjectContractPaymentModulePermission",
                'description' => "The internal user can view payments.",
                'slug' => SystemConstant::slugGenerator("View payments",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Create a payment.",
                'code' => "PCPMP02",
                'type' => "ProjectContractPaymentModulePermission",
                'description' => "The internal user can create a payment.",
                'slug' => SystemConstant::slugGenerator("Create a payment.",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "View details of a payment.",
                'code' => "PCPMP03",
                'type' => "ProjectContractPaymentModulePermission",
                'description' => "The internal user can view details of a payment.",
                'slug' => SystemConstant::slugGenerator("View details of a payment.",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Update a payment.",
                'code' => "PCPMP04",
                'type' => "ProjectContractPaymentModulePermission",
                'description' => "The internal user can update a payment.",
                'slug' => SystemConstant::slugGenerator("Update a payment.",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Delete a payment.",
                'code' => "PCPMP05",
                'type' => "ProjectContractPaymentModulePermission",
                'description' => "The internal user can delete a payment.",
                'slug' => SystemConstant::slugGenerator("Delete a payment.",200),
            ])->create();

        // Oil and gas pump  permission.
            UserPermission::factory()->state([
                'name' => "View oil and gas pumps.",
                'code' => "OAGPMP01",
                'type' => "OilAndGasPumpModulePermission",
                'description' => "The internal user can view oil and gas pumps.",
                'slug' => SystemConstant::slugGenerator("View oil and gas pumps",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Create a oil and gas pumps.",
                'code' => "OAGPMP02",
                'type' => "OilAndGasPumpModulePermission",
                'description' => "The internal user can create a oil and gas pump.",
                'slug' => SystemConstant::slugGenerator("Create a oil and gas pump",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "View detials of a oil and gas pump.",
                'code' => "OAGPMP03",
                'type' => "OilAndGasPumpModulePermission",
                'description' => "The internal user can view details of a oil and gas pump.",
                'slug' => SystemConstant::slugGenerator("View detials of a oil and gas pump",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Update a oil and gas pump.",
                'code' => "OAGPMP04",
                'type' => "OilAndGasPumpModulePermission",
                'description' => "The internal user can update a oil and gas pump.",
                'slug' => SystemConstant::slugGenerator("Update a oil and gas pump",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Delete a oil and gas pump.",
                'code' => "OAGPMP05",
                'type' => "OilAndGasPumpModulePermission",
                'description' => "The internal user can delete a oil and gas pump.",
                'slug' => SystemConstant::slugGenerator("Delete a oil and gas pump",200),
            ])->create();

        // Oil and gas pump product permission.
            UserPermission::factory()->state([
                'name' => "View oil and gas pump products.",
                'code' => "OAGPPMP01",
                'type' => "OilAndGasPumpProductModulePermission",
                'description' => "The internal user can view oil and gas pumps.",
                'slug' => SystemConstant::slugGenerator("View oil and gas pumps",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Create a oil and gas pump product.",
                'code' => "OAGPPMP02",
                'type' => "OilAndGasPumpProductModulePermission",
                'description' => "The internal user can create a oil and gas pump.",
                'slug' => SystemConstant::slugGenerator("Create a oil and gas pump",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "View detials of a oil and gas pump product.",
                'code' => "OAGPPMP03",
                'type' => "OilAndGasPumpProductModulePermission",
                'description' => "The internal user can view details of a oil and gas pump.",
                'slug' => SystemConstant::slugGenerator("View detials of a oil and gas pump",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Update a oil and gas pump product.",
                'code' => "OAGPPMP04",
                'type' => "OilAndGasPumpProductModulePermission",
                'description' => "The internal user can update a oil and gas pump.",
                'slug' => SystemConstant::slugGenerator("Update a oil and gas pump",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Delete a oil and gas pump product.",
                'code' => "OAGPPMP05",
                'type' => "OilAndGasPumpProductModulePermission",
                'description' => "The internal user can delete a oil and gas pump.",
                'slug' => SystemConstant::slugGenerator("Delete a oil and gas pump",200),
            ])->create();

        // Oil and gas pump inventory permission.
            UserPermission::factory()->state([
                'name' => "View inventory.",
                'code' => "OAGPIMP01",
                'type' => "OilAndGasPumpInventoryModulePermission",
                'description' => "The internal user can view inventory.",
                'slug' => SystemConstant::slugGenerator("View inventory",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Add a oil or gas pump from inventory.",
                'code' => "OAGPIMP02",
                'type' => "OilAndGasPumpInventoryModulePermission",
                'description' => "The internal user can add a oil or gas pump from inventory.",
                'slug' => SystemConstant::slugGenerator("Add a oil or gas pump from inventory",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "View detials of a oil or gas pump from inventory.",
                'code' => "OAGPIMP03",
                'type' => "OilAndGasPumpInventoryModulePermission",
                'description' => "The internal user can view details of a oil or gas pump from inventory.",
                'slug' => SystemConstant::slugGenerator("View detials of a oil or gas pump from inventory",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Delete a product from inventory.",
                'code' => "OAGPIMP04",
                'type' => "OilAndGasPumpInventoryModulePermission",
                'description' => "The internal user can delete a product from inventory.",
                'slug' => SystemConstant::slugGenerator("Delete a product from inventory",200),
            ])->create();
        // Oil and gas pump supplier permission.
            UserPermission::factory()->state([
                'name' => "View oil and gas pump suppliers.",
                'code' => "OAGPSMP01",
                'type' => "OilAndGasPumpSupplierModulePermission",
                'description' => "The internal user can view suppliers.",
                'slug' => SystemConstant::slugGenerator("View suppliers",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Create a oil and gas pump supplier.",
                'code' => "OAGPSMP02",
                'type' => "OilAndGasPumpSupplierModulePermission",
                'description' => "The internal user can create a supplier.",
                'slug' => SystemConstant::slugGenerator("Create a supplier",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "View detials of a oil and gas pump supplier.",
                'code' => "OAGPSMP03",
                'type' => "OilAndGasPumpSupplierModulePermission",
                'description' => "The internal user can view details of a supplier.",
                'slug' => SystemConstant::slugGenerator("View detials of a supplier",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Update a oil and gas pump supplier.",
                'code' => "OAGPSMP04",
                'type' => "OilAndGasPumpSupplierModulePermission",
                'description' => "The internal user can update a supplier.",
                'slug' => SystemConstant::slugGenerator("Update a supplier",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Delete a oil and gas pump supplier.",
                'code' => "OAGPSMP05",
                'type' => "OilAndGasPumpSupplierModulePermission",
                'description' => "The internal user can delete a supplier.",
                'slug' => SystemConstant::slugGenerator("Delete a supplier",200),
            ])->create();

        // Oil and gas pump supplier purchase.
            UserPermission::factory()->state([
                'name' => "View oil and gas pump purchases.",
                'code' => "OAGPPUMP01",
                'type' => "OilAndGasPumpPurchaseModulePermission",
                'description' => "The internal user can view purchases.",
                'slug' => SystemConstant::slugGenerator("View purchases",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Create a oil and gas pump purchase.",
                'code' => "OAGPPUMP02",
                'type' => "OilAndGasPumpPurchaseModulePermission",
                'description' => "The internal user can create a purchase.",
                'slug' => SystemConstant::slugGenerator("Create a purchase",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "View detials of a oil and gas pump purchase.",
                'code' => "OAGPPUMP03",
                'type' => "OilAndGasPumpPurchaseModulePermission",
                'description' => "The internal user can view details of a purchase.",
                'slug' => SystemConstant::slugGenerator("View detials of a purchase",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Update a oil and gas pump purchase.",
                'code' => "OAGPPUMP04",
                'type' => "OilAndGasPumpPurchaseModulePermission",
                'description' => "The internal user can update a purchase.",
                'slug' => SystemConstant::slugGenerator("Update a purchase",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Delete a oil and gas pump purchase.",
                'code' => "OAGPPUMP05",
                'type' => "OilAndGasPumpPurchaseModulePermission",
                'description' => "The internal user can delete a purchase.",
                'slug' => SystemConstant::slugGenerator("Delete a purchase",200),
            ])->create();

            UserPermission::factory()->state([
                'name' => "Add payment to a oil and gas pump purchase.",
                'code' => "OAGPPUMP06",
                'type' => "OilAndGasPumpPurchaseModulePermission",
                'description' => "The internal user can add payment to a purchase.",
                'slug' => SystemConstant::slugGenerator("Add payment to a oil and gas pump purchase.",200),
            ])->create();
        // Oil and gas pump supplier permission.
    }
}
