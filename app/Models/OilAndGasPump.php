<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OilAndGasPump extends Model
{
    use HasFactory, LogsActivity;

    protected $guard = 'web';

    protected $table = 'oil_and_gas_pumps';

    protected $fillable = [
        'name',
        'code',
        'slug',
        'note',
        'description',
        'created_by_id',
    ];

    protected $hidden = [
        'slug',
        'created_by_id',
    ];

    protected $casts = [
        'note' => 'array',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly([
                'name','code','slug','note','
                description','created_by_id',
        ])
        ->useLogName('Oil and gas pump')
        ->setDescriptionForEvent(fn(string $eventName) => "The record has been {$eventName}.")
        ->logOnlyDirty()
        ->logExcept(["id",'created_at',])
        ->dontSubmitEmptyLogs();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class,'created_by_id','id')->withTrashed();
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
        return Activity::orderBy("id","desc")->where("subject_type","App\Models\OilAndGasPump")->where("subject_id",$this->id)->get();
    }

    public function modifiedActivityLogs($limit){
        return Activity::orderBy("id","desc")->where("subject_type","App\Models\OilAndGasPump")->where("subject_id",$this->id)->take($limit)->get();
    }
}
