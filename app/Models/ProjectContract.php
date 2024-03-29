<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectContract extends Model
{
    use HasFactory, LogsActivity;

    protected $guard = 'web';

    protected $table = 'project_contracts';

    protected $fillable = [
        'name',
        'code',
        'slug',
        'note',
        'status',
        'end_date',
        'start_date',
        'description',
        'created_by_id',
        'invested_amount',
        'receivable_status',
        'client_id',
        'category_id',
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
                'name','code','slug','note','status','description','end_date','start_date',
                'created_by_id','receivable_status','invested_amount',
                'client_id','category_id',
        ])
        ->useLogName('Project contract')
        ->setDescriptionForEvent(fn(string $eventName) => "The record has been {$eventName}.")
        ->logOnlyDirty()
        ->logExcept(["id",'created_at',])
        ->dontSubmitEmptyLogs();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class,'created_by_id','id')->withTrashed();
    }

    public function client()
    {
        return $this->belongsTo(ProjectContractClient::class,'client_id','id')->withTrashed();
    }

    public function category()
    {
        return $this->belongsTo(ProjectContractCategory::class,'category_id','id')->withTrashed();
    }

    public function journals()
    {
        return $this->hasMany(ProjectContractJournal::class,'project_contract_id','id');
    }

    public function payments()
    {
        return $this->hasMany(ProjectContractPayment::class,'project_contract_id','id');
    }

    public function updatedBy()
    {
        $causer = null;
        if(Activity::where("subject_id",$this->id)->get()->last()){
            $causer=Activity::where("subject_id",$this->id)->get()->last()->causer;
        }
        return $causer;
    }

    public function totalRevenue()
    {
        $totalRevenue = 0;

        $totalRevenue = ($this->journals->count() == 0 ) ? 0 : $this->journals()->where("entry_type","Revenue")->sum("amount");

        return $totalRevenue;
    }

    public function totalLoss()
    {
        $totalLoss = 0;

        $totalLoss = ($this->journals->count() == 0 ) ? 0 : $this->journals()->where("entry_type","Loss")->sum("amount");

        return $totalLoss;
    }

    public function totalReceivable()
    {
        return ($this->invested_amount + $this->totalRevenue()) - $this->totalLoss();
    }

    public function totalReceive()
    {
        $totalReceive = 0;

        $totalReceive = ($this->payments->count() == 0 ) ? 0 : $this->payments()->whereNotNull("id")->sum("amount");

        return $totalReceive;
    }

    public function totalDue()
    {
        return  $this->totalReceivable() - $this->totalReceive();
    }

    public function totalIncome()
    {

        return $this->totalReceivable() - $this->invested_amount;
    }

    public function activityLogs(){
        return Activity::orderBy("id","desc")->where("subject_type","App\Models\ProjectContract")->where("subject_id",$this->id)->get();
    }

    public function modifiedActivityLogs($limit){
        return Activity::orderBy("id","desc")->where("subject_type","App\Models\ProjectContract")->where("subject_id",$this->id)->take($limit)->get();
    }
}
