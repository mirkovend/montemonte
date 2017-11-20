<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dr_item extends Model
{
    protected $fillable = [
    'delivery_receipt_number','unit_id','size_id','dt','delivery_receipt_qty',
    ];

    public function unit(){
        return $this->hasMany('App\Egg_unit','id','unit_id');
    }

    public function size(){
        return $this->hasMany('App\Egg_size','id','size_id');
    }

    public function dr(){
    	return $this->belongsTo('App\Delivery_receipt','delivery_receipt_number','delivery_receipt_number');
    }
}
