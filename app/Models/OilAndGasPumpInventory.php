<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OilAndGasPumpInventory extends Model
{
    use HasFactory, LogsActivity;

    protected $guard = 'web';

    protected $table = 'oil_and_gas_pump_inventories';

    protected $fillable = [
        'slug',
        'quantity',
        'oagp_product_id',
        'sell_price',
        'created_by_id',
        'purchase_price',
        'old_quantity',
        'old_sell_price',
        'oil_and_gas_pump_id',
        'old_purchase_price'
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
            'slug','quantity','oagp_product_id','sell_price','created_by_id',
            'purchase_price','previous_quantiold_sell_priceprice',
            'oil_and_gas_pump_id','old_purchase_price'
        ])
        ->useLogName('Oil and gas pump product inventory')
        ->setDescriptionForEvent(fn(string $eventName) => "The record has been {$eventName}.")
        ->logOnlyDirty()
        ->logExcept(["id",'created_at',])
        ->dontSubmitEmptyLogs();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class,'created_by_id','id')->withTrashed();
    }

    public function oagpProduct()
    {
        return $this->hasOne(OilAndGasPumpProduct::class,'id','oagp_product_id');
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
        return Activity::orderBy("id","desc")->where("subject_type","App\Models\OilAndGasPumpInventory")->where("subject_id",$this->id)->get();
    }

    public function modifiedActivityLogs($limit){
        return Activity::orderBy("id","desc")->where("subject_type","App\Models\OilAndGasPumpInventory")->where("subject_id",$this->id)->take($limit)->get();
    }
}
