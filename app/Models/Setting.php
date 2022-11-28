<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
    use HasFactory;

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

    public function createdBy()
    {
        return $this->belongsTo(User::class,'created_by_id','id')->withTrashed();
    }
}
