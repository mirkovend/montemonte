<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coa_transaction extends Model
{
    
	protected $guarded = [];
    public function coa(){
    	return $this->belongsTo('App\Voucher','ref','voucher_number');
    }
    public function coa_check(){
    	return $this->belongsTo('App\Voucher_payment','ref','cheque_number');
    }

    public function cashInvoice(){
    	return $this->belongsTo('App\Cash_invoice','ref','cash_invoice_number');
    }
    public function chargeInvoice(){
        return $this->belongsTo('App\Charge_invoice','ref','charge_invoice_number');
    }
    public function payment(){
    	return $this->belongsTo('App\Payment','ref','or_number');
    }
    public function coa_transaction_link(){
        return $this->hasMany('App\Coa_transaction_link','coa_transaction_id','id');
    }
}


