<?php

namespace Database\Seeders;

use App\Models\ProjectContract;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ProjectContractClient;
use App\Models\ProjectContractJournal;
use App\Models\ProjectContractPayment;
use App\Models\ProjectContractPaymentMethod;

class ProjectContractSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ProjectContract::truncate();
        ProjectContractClient::truncate();
        ProjectContractJournal::truncate();
        ProjectContractPayment::truncate();
        ProjectContractPaymentMethod::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
