<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Egg_size extends Model
{
    protected $fillable = [
    'size_code','size_name',
    ];

    public function cs_invoice(){
        return $this->hasMany('App\Cash_invoice_item','size_id','id');
    }

    public function ci_invoice(){
        return $this->hasMany('App\Charge_invoice_item','size_id','id');
    }
}
