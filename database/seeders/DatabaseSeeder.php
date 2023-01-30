<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(LogSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(SettingSeeder::class);
        $this->call(OilAndGasPumpSeeder::class);
        $this->call(UserPermissionSeeder::class);
        $this->call(ProjectContractSeeder::class);
    }
}
