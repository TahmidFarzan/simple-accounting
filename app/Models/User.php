<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Rappasoft\LaravelAuthenticationLog\Traits\AuthenticationLoggable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, LogsActivity, AuthenticationLoggable;

    protected $table='users';

    protected $fillable = [
        'name',
        'slug',
        'email',
        'user_id',
        'password',
        'user_role',
        'created_by_id',
        'default_password',
    ];

    protected $hidden = [
        'slug',
        'password',
        'user_role',
        'created_by_id',
        'remember_token',
        'default_password',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'email_verified_at',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly([
            'name',
            'slug',
            'email',
            'password',
            'mobile_no',
            'user_role',
            'created_at',
            'updated_at',
            'deleted_at',
            'created_by_id',
            'default_password',
            'email_verified_at',
            'is_default_password',
        ])
        ->useLogName('User')
        ->setDescriptionForEvent(fn(string $eventName) => "The product category has been {$eventName}.")
        ->logOnlyDirty()
        ->logExcept(["id",'created_by_id','created_at','remember_token',])
        ->dontSubmitEmptyLogs();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class,'created_by_id','id')->withTrashed();
    }

    public function createdUsers()
    {
        return $this->hasMany(User::class,'created_by_id','id')->withTrashed();
    }

    public function userPermissionGroups()
    {
        return $this->belongsToMany(UserPermissionGroup::class, 'user_has_user_permission_groups', 'user_id', 'user_permission_group_id');
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
        return Activity::orderBy("id","desc")->where("subject_type","App\Models\User")->where("subject_id",$this->id)->get();
    }

    public function modifiedActivityLogs($limit){
        return Activity::orderBy("id","desc")->where("subject_type","App\Models\User")->where("subject_id",$this->id)->take($limit)->get();
    }

    public function hasUserPermissionGroup($userpermissionGroupCodes)
    {
        $hasUserPermissionGroup = false;
        if($this->userPermissionGroups()->whereIn("code",$userpermissionGroupCodes)->count()>0){
            $hasUserPermissionGroup = true;
        }
        return $hasUserPermissionGroup;
    }

    public function hasUserPermission($userpermissionCodes)
    {
        $hasUserPermission = false;
        if($this->user_role == "Owner"){
            $hasUserPermission=true;
        }
        if(!($this->user_role == "Owner")){
            foreach($this->userpermissionGroups as $perUserPermissionGroup){
                if($perUserPermissionGroup->userPermissions()->whereIn("code",$userpermissionCodes)->count() > 0){
                    $hasUserPermission = true;
                }
            }
        }

        return $hasUserPermission;
    }


}
