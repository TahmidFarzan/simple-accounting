<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OilAndGasPumpPurchase extends Model
{
    use HasFactory, LogsActivity;

    protected $guard = 'web';

    protected $table = 'oil_and_gas_pump_purchases';

    protected $fillable = [
        'note',
        'date',
        'name',
        'slug',
        'status',
        'invoice',
        'mobile_no',
        'description',
        'created_by_id',
        'oagp_supplier_id',
    ];

    protected $hidden = [
        'slug',
        'invoice',
        'created_by_id',
    ];

    protected $casts = [
        'note' => "array",
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly([
            'note','date','name','slug',
            'invoice','mobile_no','description','status',
            'created_by_id','oagp_supplier_id',
        ])
        ->useLogName('Oil and gas pump purchase')
        ->setDescriptionForEvent(fn(string $eventName) => "The record has been {$eventName}.")
        ->logOnlyDirty()
        ->logExcept(["id",'created_at',])
        ->dontSubmitEmptyLogs();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class,'created_by_id','id')->withTrashed();
    }

    public function oagpSupplier()
    {
        return $this->belongsTo(OilAndGasPumpSupplier::class,'oagp_supplier_id','id');
    }

    public function oilAndGasPump()
    {
        return $this->belongsTo(OilAndGasPump::class,'oil_and_gas_pump_id','id');
    }

    public function oagpPurchaseItems()
    {
        return $this->hasMany(OilAndGasPumpPurchaseItem::class,'oagp_purchase_id','id');
    }

    public function oagpPurchasePayments()
    {
        return $this->hasMany(OilAndGasPumpPurchasePayment::class,'oagp_purchase_id','id');
    }

    public function oagpPurchaseTotalPrice()
    {
        $totalPrice = 0.0;

        foreach($this->oagpPurchaseItems as $perItem){
            $totalPrice = $totalPrice + ($perItem->purchase_price * $perItem->quantity );
        }
        return $totalPrice;
    }

    public function oagpPurchaseTotalPaidAmount()
    {
        return $this->oagpPurchasePayments->sum("amount");
    }

    public function oagpPurchasePayableAmount()
    {
        return $this->oagpPurchaseTotalPrice() ;
    }

    public function oagpPurchaseDueAmount()
    {
        return ($this->oagpPurchasePayableAmount() - $this->oagpPurchaseTotalPaidAmount()) ;
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
        return Activity::orderBy("id","desc")->where("subject_type","App\Models\OilAndGasPumpPurchase")->where("subject_id",$this->id)->get();
    }

    public function modifiedActivityLogs($limit){
        return Activity::orderBy("id","desc")->where("subject_type","App\Models\OilAndGasPumpPurchase")->where("subject_id",$this->id)->take($limit)->get();
    }
}
