<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
    'customer_name','customer_address','customer_contact','beginning_bal',
    ];

    public function ci(){
        return $this->hasMany('App\Cash_invoice','cash_invoice_to','id');
    }
    public function invoice(){
        return $this->hasMany('App\Charge_invoice','customer_id','id');
    }

    public function ci_old(){
        return $this->hasMany('App\Cash_invoice','cash_invoice_to','id');
    }


    public function old(){
        return $this->hasMany('App\Charge_invoice','customer_id','id');
    }
     public function payments(){
        return $this->hasMany('App\Payment','customer_id','id');
    }
}
