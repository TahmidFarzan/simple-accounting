<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OilAndGasPumpSellItem extends Model
{
    use HasFactory, LogsActivity;

    protected $guard = 'web';

    protected $table = 'oil_and_gas_pump_sell_items';

    protected $fillable = [
        'slug',
        'price',
        'quantity',
        'oagp_sell_id',
        'created_by_id',
        'oagp_product_id',
        'product_inventory',
    ];

    protected $hidden = [
        'slug',
        'created_by_id',
    ];

    protected $casts = [];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly([
            'quantity','created_by_id',
            'price','oagp_product_id','oagp_sell_id',
            'product_inventory',
        ])
        ->useLogName('Oil and gas pump sell item')
        ->setDescriptionForEvent(fn(string $eventName) => "The record has been {$eventName}.")
        ->logOnlyDirty()
        ->logExcept(["id",'created_at',])
        ->dontSubmitEmptyLogs();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class,'created_by_id','id')->withTrashed();
    }

    public function oagpSell()
    {
        return $this->belongsTo(OilAndGasPumpSell::class,'oagp_sell_id','id');
    }

    public function product()
    {
        return $this->belongsTo(OilAndGasPumpProduct::class,'oagp_product_id','id');
    }

    public function totalSellPrice()
    {
        return $this->price * $this->quantity;
    }

    public function totalPurchasePrice()
    {
        return $this->product->inventory->purchase_price * $this->quantity;
    }

    public function totalIncome()
    {
        return $this->totalSellPrice() - $this->totalPurchasePrice();
    }

    public function updatedBy()
    {
        $causer = null;
        if(Activity::where("subject_id",$this->id)->get()->last()){
            $causer=Activity::where("subject_id",$this->id)->get()->last()->causer;
        }
        return $causer;
    }

    public function activityLogs(){
        return Activity::orderBy("id","desc")->where("subject_type","App\Models\OilAndGasPumpSellItem")->where("subject_id",$this->id)->get();
    }

    public function modifiedActivityLogs($limit){
        return Activity::orderBy("id","desc")->where("subject_type","App\Models\OilAndGasPumpSellItem")->where("subject_id",$this->id)->take($limit)->get();
    }
}
