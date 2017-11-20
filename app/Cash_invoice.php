<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cash_invoice extends Model
{
    protected $fillable = [
    'dt','cash_invoice_number','cash_invoice_to','status','dept',
    ];
    protected $table = "cash_invoices";
    public function invoice(){
        return $this->hasMany('App\Cash_invoice_item','cash_invoice_number','cash_invoice_number');
    }

    public function customers(){
        return $this->hasOne('App\Customer','id','cash_invoice_to');
    }
    
    public function customer_belong(){
        return $this->belongsTo('App\Customer','cash_invoice_to','id');
    }

    public function ci_item(){
        return $this->hasMany('App\Cash_invoice_item','cash_invoice_number','cash_invoice_number');
    }
}
