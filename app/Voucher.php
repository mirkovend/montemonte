<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = ['voucher_number','dt','bill_due','voucher_type','explanation','amount','supplier_id','isReconciled'];

    public function supplier(){
        return $this->hasMany('App\Supplier','id','supplier_id');
    }
    public function supplier_one(){
        return $this->hasOne('App\Supplier','id','supplier_id');
    }
    public function supplier_belong(){
        return $this->belongsTo('App\Supplier','supplier_id','id');
    }
    public function voucher_item(){
        return $this->hasMany('App\Voucher_item','voucher_number','voucher_number');
    }
    public function voucher_payment(){
        return $this->hasOne('App\Voucher_payment','voucher_number','voucher_number');
    }

    public function voucher_payments(){
        return $this->hasMany('App\Voucher_payment','voucher_number','voucher_number');
    }

    public function purchase_order_item(){
        return $this->hasOne('App\Purchase_order_item','voucher_number','voucher_number');
    }

}