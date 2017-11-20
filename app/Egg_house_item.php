<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Egg_house_item extends Model
{
    protected $fillable = [
    'delivery_receipt_number','house_id','house_qty','dt',
    ];

    public function house(){
        return $this->hasMany('App\Egg_house','id','house_id');
    }
}
