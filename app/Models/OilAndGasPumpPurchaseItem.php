<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OilAndGasPumpPurchaseItem extends Model
{
    use HasFactory, LogsActivity;

    protected $guard = 'web';

    protected $table = 'oil_and_gas_pump_purchase_items';

    protected $fillable = [
        'slug',
        'quantity',
        'sell_price',
        'created_by_id',
        'purchase_price',
        'oagp_product_id',
        'oagp_purchase_id',
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
            'quantity','sell_price','created_by_id',
            'purchase_price','oagp_product_id','oagp_purchase_id',
        ])
        ->useLogName('Oil and gas pump purchase item')
        ->setDescriptionForEvent(fn(string $eventName) => "The record has been {$eventName}.")
        ->logOnlyDirty()
        ->logExcept(["id",'created_at',])
        ->dontSubmitEmptyLogs();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class,'created_by_id','id')->withTrashed();
    }

    public function oagpPurchase()
    {
        return $this->belongsTo(OilAndGasPumpPurchase::class,'oagp_purchase_id','id');
    }

    public function product()
    {
        return $this->belongsTo(OilAndGasPumpProduct::class,'oagp_product_id','id');
    }

    public function totalPurchasePrice()
    {
        return $this->purchase_price * $this->quantity;
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
        return Activity::orderBy("id","desc")->where("subject_type","App\Models\OilAndGasPumpPurchaseItem")->where("subject_id",$this->id)->get();
    }

    public function modifiedActivityLogs($limit){
        return Activity::orderBy("id","desc")->where("subject_type","App\Models\OilAndGasPumpPurchaseItem")->where("subject_id",$this->id)->take($limit)->get();
    }
}
