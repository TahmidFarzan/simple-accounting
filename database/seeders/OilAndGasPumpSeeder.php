<?php

namespace Database\Seeders;

use App\Models\OilAndGasPump;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\OilAndGasPumpProduct;
use App\Models\OilAndGasPumpSupplier;
use App\Models\OilAndGasPumpPurchase;
use App\Models\OilAndGasPumpInventory;
use App\Models\OilAndGasPumpPurchaseItem;

class OilAndGasPumpSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        OilAndGasPump::truncate();
        OilAndGasPumpProduct::truncate();
        OilAndGasPumpSupplier::truncate();
        OilAndGasPumpPurchase::truncate();
        OilAndGasPumpInventory::truncate();
        OilAndGasPumpPurchaseItem::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
