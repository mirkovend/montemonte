<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bankrecon extends Model
{
    protected $fillable = [
    	'status',
    	'coa_id',
    	'dt',
    	'service_charge',
    	'interest_earned',
    	'service_account_id',
    	'interest_account_id',
    	'service_department',
    	'interest_department',
    	'service_date',
    	'interest_date',
    	'status',
    	'ending_balance',

    ];
    public function bankReconItem(){
        return $this->hasMany('App\BankreconItem','bankrecon_id','id');
    }
    public function coa(){
        return $this->belongsTo('App\Chart_of_account','coa_id','id');
    }
}
