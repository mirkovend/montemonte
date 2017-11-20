<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Voucher_payment extends Model
{
    protected $fillable = ['voucher_number','dt','cheque_number','account','payment'];

    public function voucher(){
        return $this->belongsTo('App\Voucher','voucher_number','voucher_number');
    }

    public function bankrecon_item(){
        return $this->hasOne('App\BankreconItem','payment_id','id');
    }
}
