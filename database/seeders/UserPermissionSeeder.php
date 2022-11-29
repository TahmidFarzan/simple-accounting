<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\UserPermission;
use Illuminate\Database\Seeder;
use App\Utilities\SystemConstant;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

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
    }
}
