<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectContractPayment extends Model
{
    use HasFactory, LogsActivity;

    protected $guard = 'web';

    protected $table = 'project_contract_payments';

    protected $fillable = [
        'name',
        'slug',
        'note',
        'amount',
        'payment_date',
        'description',
        'created_by_id',
        'payment_method_id',
        'project_contract_id',
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
                'name','slug','note','amount','payment_date','description',
                'created_by_id','project_contract_id','payment_method_id',
        ])
        ->useLogName('Project contract payment')
        ->setDescriptionForEvent(fn(string $eventName) => "The record has been {$eventName}.")
        ->logOnlyDirty()
        ->logExcept(["id",'created_at',])
        ->dontSubmitEmptyLogs();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class,'created_by_id','id')->withTrashed();
    }

    public function projectContract()
    {
        return $this->belongsTo(ProjectContract::class,'project_contract_id','id');
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
        return Activity::orderBy("id","desc")->where("subject_type","App\Models\ProjectContractPayment")->where("subject_id",$this->id)->get();
    }

    public function modifiedActivityLogs($limit){
        return Activity::orderBy("id","desc")->where("subject_type","App\Models\ProjectContractPayment")->where("subject_id",$this->id)->take($limit)->get();
    }
}
