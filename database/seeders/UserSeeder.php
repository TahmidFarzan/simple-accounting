<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Utilities\SystemConstant;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        DB::table('user_permission_group_has_user_permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $tahmidFarzanUser=User::factory()->state([
            'name' => "Tahmid Farzan",
            'email' => "tfarzan007@gmail.com",
            'mobile_no' => '+8801671786285',
            'created_by_id' => 1,
            'user_role'=> "Owner", // Owner||Subordinate
            'slug' => SystemConstant::slugGenerator("Tahmid Farzan",200),
        ])->create();
    }
}
