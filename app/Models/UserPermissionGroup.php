<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserPermissionGroup extends Model
{
    use HasFactory,LogsActivity;

    protected $guard = 'web';

    protected $table = 'user_permission_groups';

    protected $fillable = [
        'name',
        'code',
        'slug',
        'created_by_id',
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
            'name',
            'code',
            'slug',
            'created_by_id',
        ])
        ->useLogName('User permission group')
        ->setDescriptionForEvent(fn(string $eventName) => "The product category has been {$eventName}.")
        ->logOnlyDirty()
        ->logExcept(["id",'created_at',])
        ->dontSubmitEmptyLogs();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class,'created_by_id','id')->withTrashed();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_has_user_permission_groups', 'user_permission_group_id', 'user_id');
    }

    public function userPermissions()
    {
        return $this->belongsToMany(UserPermission::class, 'user_permission_group_has_user_permissions', 'user_permission_group_id','user_permission_id');
    }


    public function activityLogs(){
        return Activity::orderBy("id","desc")->where("subject_type","App\Models\UserPermissionGroup")->where("subject_id",$this->id)->get();
    }

    public function modifiedActivityLogs($limit){
        return Activity::orderBy("id","desc")->where("subject_type","App\Models\UserPermissionGroup")->where("subject_id",$this->id)->take($limit)->get();
    }
}
