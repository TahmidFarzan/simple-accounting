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
        'sulg',
        'status',
        'invoice',
        'discount',
        'mobile_no',
        'description',
        'paid_amount',
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
            'note','date','name','sulg',
            'invoice','mobile_no','description','status',
            'created_by_id','paid_amount','discount','oagp_supplier_id',
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

    public function oagpPurchaseItems()
    {
        return $this->hasMany(OilAndGasPumpPurchaseItem::class,'oagp_purchase_id','id');
    }

    public function oagpTotalPrice()
    {
        $totalQuentityPrice = $this->oagpPurchaseItems->sum('purchase_price') * $this->oagpPurchaseItems->sum('quantity');
        return ($totalQuentityPrice - ( $totalQuentityPrice * ($this->oagpPurchaseItems->sum('discount') / 100) )) ;
    }

    public function oagpPayableAmount()
    {
        return ($this->oagpTotalPrice() - ( $this->oagpTotalPrice() * ($this->discount / 100) )) ;
    }

    public function oagpDueAmount()
    {
        return ($this->oagpPayableAmount() - $this->paid_amount) ;
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
