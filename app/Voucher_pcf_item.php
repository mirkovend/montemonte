<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Voucher_pcf_item extends Model
{
    protected $fillable = ['voucher_number','pcf_number','pcf_id','dt','supplier_id','account_item_id','pcf_payment'];

    public function account_item(){
    	return $this->hasOne('App\Item','id','account_item_id');
    }
}
