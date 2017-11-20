<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Delivery_receipt extends Model
{
    protected $fillable = [
    'dt','delivery_receipt_number','delivery_receipt_to',
    ];

    public function invoice(){
        return $this->hasMany('App\Dr_item','delivery_receipt_number','delivery_receipt_number');
    }
    public function egghouseitem(){
        return $this->hasMany('App\Egg_house_item','delivery_receipt_number','delivery_receipt_number');
    }
}
