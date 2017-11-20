<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coa_transaction_link extends Model
{
    protected $guarded = [];
    public function chartofaccount(){
    	return $this->belongsTo('App\Chart_of_account','coa_id','id');
    }
}
