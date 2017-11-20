<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = ['supplier_name','supplier_address','supplier_contact','beginning_bal'];

    public function voucheritem(){
    	return $this->hasMany('App\Voucher_item','supplier_id','id');
    }
    public function voucherjobitem(){
    	return $this->hasMany('App\Voucher_job_item','supplier_id','id');
    }

    public function voucher(){
    	return $this->hasMany('App\Voucher','supplier_id','id');
    }

    public function voucher_old(){
    	return $this->hasMany('App\Voucher','supplier_id','id');
    }


}
