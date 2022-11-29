<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

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
