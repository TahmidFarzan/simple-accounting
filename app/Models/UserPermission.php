<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    use HasFactory;

    protected $table='user_permissions';

    protected $fillable = [
        'name',
        'code',
        'type',
        'slug',
        'description',
    ];


    protected $hidden = [
        'slug',
    ];


    protected $casts = [];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function userPermissionGroups()
    {
        return $this->belongsToMany(UserPermissionGroup::class, 'user_permission_group_has_user_permissions', 'user_permission_id', 'user_permission_group_id');
    }
}
