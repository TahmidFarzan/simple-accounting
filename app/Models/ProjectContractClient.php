<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectContractClient extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $guard = 'web';

    protected $table='project_contract_clients';

    protected $fillable = [
        'name',
        'slug',
        'note',
        'email',
        'gender',
        'address',
        'mobile_no',
        'description',
        'created_by_id',
    ];

    protected $hidden = [
        'id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['name','slug','note','email','gender',
                    'address','mobile_no','description','created_by_id',
        ])
        ->useLogName('Project contract client')
        ->setDescriptionForEvent(fn(string $eventName) => "The product has been {$eventName}.")
        ->logOnlyDirty()
        ->logExcept(["id"])
        ->dontSubmitEmptyLogs();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class,'created_by_id','id')->withTrashed();
    }

    public function updatedBy()
    {
        $causer=null;
        if(Activity::where("subject_id",$this->id)->get()->last()){
            $causer=Activity::where("subject_id",$this->id)->get()->last()->causer;
        }
        return $causer;
    }

    public function activityLogs(){
        return Activity::orderBy("id","desc")->where("subject_type","App\Models\ProjectContractClient")->where("subject_id",$this->id)->get();
    }

    public function modifiedActivityLogs($limit){
        return Activity::orderBy("id","desc")->where("subject_type","App\Models\ProjectContractClient")->where("subject_id",$this->id)->take($limit)->get();
    }
}
