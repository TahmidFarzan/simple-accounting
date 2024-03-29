<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OilAndGasPumpSell extends Model
{
    use HasFactory, LogsActivity;

    protected $guard = 'web';

    protected $table = 'oil_and_gas_pump_sells';

    protected $fillable = [
        'note',
        'date',
        'name',
        'slug',
        'status',
        'invoice',
        'customer',
        'mobile_no',
        'description',
        'customer_info',
        'created_by_id',
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
            'created_by_id','customer_info','customer',
        ])
        ->useLogName('Oil and gas pump sell')
        ->setDescriptionForEvent(fn(string $eventName) => "The record has been {$eventName}.")
        ->logOnlyDirty()
        ->logExcept(["id",'created_at',])
        ->dontSubmitEmptyLogs();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class,'created_by_id','id')->withTrashed();
    }

    public function oilAndGasPump()
    {
        return $this->belongsTo(OilAndGasPump::class,'oil_and_gas_pump_id','id');
    }

    public function oagpSellItems()
    {
        return $this->hasMany(OilAndGasPumpSellItem::class,'oagp_sell_id','id');
    }

    public function oagpSellPayments()
    {
        return $this->hasMany(OilAndGasPumpSellPayment::class,'oagp_sell_id','id');
    }

    public function totalPrice()
    {
        $totalPrice = 0.0;

        foreach($this->oagpSellItems as $perItem){
            $totalPrice = $totalPrice + ( $perItem->price * $perItem->quantity );
        }
        return $totalPrice;
    }

    public function totalPaid()
    {
        return $this->oagpSellPayments->sum("amount");
    }

    public function totalPayable()
    {
        return $this->totalPrice() ;
    }

    public function totalDue()
    {
        return ($this->totalPayable() - $this->totalPaid()) ;
    }

    public function totalIncome()
    {
        $income = 0;
        foreach($this->oagpSellItems as $perItem){
            $income = $income + $perItem->totalIncome();
        }
        return $income;
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
        return Activity::orderBy("id","desc")->where("subject_type","App\Models\OilAndGasPumpSell")->where("subject_id",$this->id)->get();
    }

    public function modifiedActivityLogs($limit){
        return Activity::orderBy("id","desc")->where("subject_type","App\Models\OilAndGasPumpSell")->where("subject_id",$this->id)->take($limit)->get();
    }
}
