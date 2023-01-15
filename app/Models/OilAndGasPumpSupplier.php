<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OilAndGasPumpSupplier extends Model
{
    use HasFactory, LogsActivity;

    protected $guard = 'web';

    protected $table = 'oil_and_gas_pump_suppliers';

    protected $fillable = [
        'name',
        'slug',
        'note',
        'email',
        'mobile_no',
        'created_by_id',
        'payable_amount',
        'receviable_amount',
        'oil_and_gas_pump_id',
    ];

    protected $hidden = [
        'slug',
        'created_by_id',
    ];

    protected $casts = [
        'note' => 'array'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly([
            'name','slug','note','email','mobile_no',
            'created_by_id','payable_amount',
            'receviable_amount','oil_and_gas_pump_id',
        ])
        ->useLogName('Oil and gas pump supplier')
        ->setDescriptionForEvent(fn(string $eventName) => "The record has been {$eventName}.")
        ->logOnlyDirty()
        ->logExcept(["id",'created_at',])
        ->dontSubmitEmptyLogs();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class,'created_by_id','id')->withTrashed();
    }

    public function oagpInventory()
    {
        return $this->belongsTo(OilAndGasPumpInventory::class,'id','oagp_product_id');
    }

    public function oilAndGasPump()
    {
        return $this->belongsTo(OilAndGasPump::class,'oil_and_gas_pump_id','id');
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
        return Activity::orderBy("id","desc")->where("subject_type","App\Models\OilAndGasPumpSupplier")->where("subject_id",$this->id)->get();
    }

    public function modifiedActivityLogs($limit){
        return Activity::orderBy("id","desc")->where("subject_type","App\Models\OilAndGasPumpSupplier")->where("subject_id",$this->id)->take($limit)->get();
    }
}
