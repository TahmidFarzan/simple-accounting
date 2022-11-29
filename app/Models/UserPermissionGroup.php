<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPermissionGroup extends Model
{
    use HasFactory;

    protected $table='user_permission_groups';

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
}
