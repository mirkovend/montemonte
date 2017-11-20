<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Voucher_job_item extends Model
{
    protected $fillable = ['voucher_number','joborder_number','dt','supplier_id','account_item_id','joborder_payment','joborder_id'];
    protected $table = "voucher_job_items";
    public function account_item(){
    	return $this->hasOne('App\Item','id','account_item_id');
    }
    
    public function jo_item(){
        return $this->belongsTo('App\Joborder_item','joborder_number','joborder_number');
    }
    public function item(){
        return $this->belongsTo('App\Item','account_item_id','id');
    }
    public function jo(){
        return $this->belongsTo('App\Joborder','joborder_number','joborder_number');
    }

    public function voucher(){
        return $this->belongsTo('App\Voucher','voucher_number','voucher_number');
    }

}