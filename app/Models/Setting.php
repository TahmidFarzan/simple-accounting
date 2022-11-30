<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
    use HasFactory,LogsActivity;

    protected $table='settings';

    protected $guard = 'web';

    protected $fillable = [
        'name',
        'code',
        'created_by_id',
        'fields_with_values',
    ];

    protected $hidden = [
        'id',
        'name',
        'code',
        'slug',
        'created_by_id',
    ];

    protected $casts = [
        "fields_with_values" => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['fields_with_values','slug',"updated_at"])
        ->useLogName('Setting')
        ->setDescriptionForEvent(fn(string $eventName) => "The setting has been {$eventName}")
        ->logOnlyDirty()
        ->logExcept(["id",'name', 'code','created_by_id','created_at',"updated_at"])
        ->dontSubmitEmptyLogs();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class,'created_by_id','id')->withTrashed();
    }

    public function updatedBy()
    {
        return Activity::where("subject_id",$this->id)->get()->last()->causer;
    }

    public function activityLogs(){
        return Activity::orderBy("id","desc")->where("subject_type","App\Models\Setting")->where("subject_id",$this->id)->get();
    }

    public function modifiedActivityLogs($limit){
        return Activity::orderBy("id","desc")->where("subject_type","App\Models\Setting")->where("subject_id",$this->id)->take($limit)->get();
    }
}
