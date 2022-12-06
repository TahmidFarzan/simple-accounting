<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserPermissionGroupHasUserPermission extends Pivot
{
    use LogsActivity;

    protected $guard = 'web';

    protected $table = 'user_permission_group_has_user_permissions';

    protected $fillable = [
        'created_by_id',
        'user_permission_id',
        'user_permission_group_id',
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
            'created_by_id',
            'user_permission_id',
            'user_permission_group_id',
        ])
        ->useLogName('User permission group')
        ->setDescriptionForEvent(fn(string $eventName) => "The record has been {$eventName}.")
        ->logOnlyDirty()
        ->logExcept(["id",'created_at',])
        ->dontSubmitEmptyLogs();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class,'created_by_id','id')->withTrashed();
    }

    public function activityLogs(){
        return Activity::orderBy("id","desc")->where("subject_type","App\Models\UserPermissionGroupHasUserPermission")->where("subject_id",$this->id)->get();
    }

    public function modifiedActivityLogs($limit){
        return Activity::orderBy("id","desc")->where("subject_type","App\Models\UserPermissionGroupHasUserPermission")->where("subject_id",$this->id)->take($limit)->get();
    }
}
