<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class WaPettyCashApprovals extends Model
{
    protected $table = 'wa_petty_cash_approvals';
    protected $guarded = [];

    public function petty_cash(){
        return $this->belongsTo(\App\Model\WaPettyCash::class,'petty_cash_id');
    }
    
    public function approver(){
        return $this->belongsTo(\App\User::class,'approver_id');
    }
}