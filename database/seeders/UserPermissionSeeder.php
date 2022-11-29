<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\UserPermission;
use Illuminate\Database\Seeder;
use App\Utilities\SystemConstant;
use Illuminate\Support\Facades\DB;
use App\Models\UserPermissionGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;


class UserPermissionSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        UserPermission::truncate();
        DB::table('user_permission_group_has_user_permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
