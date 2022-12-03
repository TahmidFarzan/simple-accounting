<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;
use Rappasoft\LaravelAuthenticationLog\Models\AuthenticationLog;

class LogSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Activity::truncate();
        AuthenticationLog::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
