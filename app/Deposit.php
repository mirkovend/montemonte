<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    protected $fillable = ['dt','deposit_to_id','reference_number','deposit_from_id','amount','cheque_number','deposit_memo','isReconciled'];

    public function deposit_to(){
        return $this->hasOne('App\Coa_subitem','id','deposit_to_id');
    }

    public function deposit_from(){
        return $this->hasOne('App\Coa_subitem','id','deposit_from_id');
    }

    public function bankrecon_item(){
        return $this->hasOne('App\BankreconItem','payment_id','id');
    }
}
