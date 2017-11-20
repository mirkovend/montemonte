<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['customer_id','dt','reference_number','or_number','payment','deposit_to','charge_invoice_number'];

    protected $table = "payments";
    public function customer(){
        return $this->hasOne('App\Customer','id','customer_id');
    }
    public function customer_belong(){
        return $this->belongsTo('App\Customer','customer_id','id');
    }


}