<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Charge_invoice extends Model
{
    protected $fillable = [
    'dt','charge_invoice_number','customer_id','status','dept','total_amount',
    ];
    protected $table = "charge_invoices";
    public function invoice(){
        return $this->hasMany('App\Charge_invoice_item','charge_invoice_number','charge_invoice_number');
    }

    public function payment(){
    	return $this->hasOne('App\Payment','charge_invoice_number','charge_invoice_number');
    }

    public function payments(){
        return $this->hasMany('App\Payment','charge_invoice_number','charge_invoice_number');
    }

    public function customer(){
        return $this->hasMany('App\Customer','id','customer_id');
    }
    public function customer_belong(){
        return $this->belongsTo('App\Customer','customer_id','id');
    }
}
