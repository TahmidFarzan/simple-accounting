<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Utilities\ModelsRelationDependencyConstant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

class ContractCategory extends Model
{
    use HasFactory, SoftDeletes, LogsActivity, HasRecursiveRelationships;

    protected $guard = 'web';

    protected $table='contract_categories';

    protected $fillable = [
        'name',
        'code',
        'slug',
        'parent_id',
        'deleted_at',
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
        ->logOnly(['name','code','slug','parent_id','deleted_at', 'description','created_at','updated_at','created_by_id'])
        ->useLogName('Contract category')
        ->setDescriptionForEvent(fn(string $eventName) => "The contract category has been {$eventName}.")
        ->logOnlyDirty()
        ->logExcept(["id"])
        ->dontSubmitEmptyLogs();
    }

    public function allChildrens()
    {
        return $this->hasMany(ContractCategory::class,'parent_id')->withTrashed();
    }

    public function descendantsWithTrashed()
    {
        return $this->newDescendants(
            (new static())->newQuery(),
            $this,
            $this->getQualifiedParentKeyName(),
            $this->getLocalKeyName(),
            false
        )->withTrashed();
    }

    public function descendantsAndSelfWithTrashed()
    {
        return $this->newDescendants(
            (new static())->newQuery(),
            $this,
            $this->getQualifiedParentKeyName(),
            $this->getLocalKeyName(),
            true
        )->withTrashed();
    }

    public function ancestorsWithTrashed()
    {
        return $this->newAncestors(
            (new static())->newQuery(),
            $this,
            $this->getQualifiedParentKeyName(),
            $this->getLocalKeyName(),
            false
        )->withTrashed();
    }

    public function ancestorsAndSelfWithTrashed()
    {
        return $this->newAncestors(
            (new static())->newQuery(),
            $this,
            $this->getQualifiedParentKeyName(),
            $this->getLocalKeyName(),
            true
        )->withTrashed();
    }

    public function parentCategory()
    {
        return $this->belongsTo(ContractCategory::class,'parent_id')->withTrashed();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class,'created_by_id','id')->withTrashed();
    }

    public function updatedBy()
    {
        $causer = null;
        if(Activity::where("subject_id",$this->id)->get()->last()){
            $causer = Activity::where("subject_id",$this->id)->get()->last()->causer;
        }
        return $causer;
    }

    public function activityLogs(){
        return Activity::orderBy("id","desc")->where("subject_type","App\Models\ContractCategory")->where("subject_id",$this->id)->get();
    }

    public function modifiedActivityLogs($limit){
        return Activity::orderBy("id","desc")->where("subject_type","App\Models\ContractCategory")->where("subject_id",$this->id)->take($limit)->get();
    }

    public function dependencyNeedToTrashRecordsInfo(){
        return ModelsRelationDependencyConstant::contractCategoryDependencyNeedToTrashRecordsInfo($this->slug);
    }

    public function dependencyNeedToRestoreRecordsInfo(){
        return ModelsRelationDependencyConstant::contractCategoryDependencyNeedToRestoreRecordsInfo($this->slug,$this->deleted_at);
    }
}
