<?php

namespace App\Models;

use App\Vehicle;
use App\Model\User;
use App\Model\Restaurant;
use App\Model\WaDepartment;
use App\Model\WaChartsOfAccount;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WaPettyCashRequest extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'initial_approval' => 'boolean',
        'final_approval' => 'boolean',
        'rejected' => 'boolean',
        'initial_approval_date' => 'datetime',
        'final_approval_date' => 'datetime',
        'rejected_date' => 'datetime',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function department()
    {
        return $this->belongsTo(WaDepartment::class, 'wa_department_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function chartOfAccount()
    {
        return $this->belongsTo(WaChartsOfAccount::class, 'wa_charts_of_account_id');
    }

    public function initialApprover()
    {
        return $this->belongsTo(User::class, 'initial_approver');
    }

    public function finalApprover()
    {
        return $this->belongsTo(User::class, 'final_approver');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function pettyCashRequestItems()
    {
        return $this->hasMany(WaPettyCashRequestItem::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function pettyCashType()
    {
        return $this->belongsTo(WaPettyCashRequestType::class, 'type', 'slug');
    }

}
