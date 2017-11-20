<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Voucher_no_po extends Model
{
    protected $fillable = ['voucher_number','dt','coa_id','debit','credit'];

    public function account_item(){
    	return $this->hasOne('App\Chart_of_account','id','coa_id');
    }
}