<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Charge_invoice_item extends Model
{
    protected $fillable = [
    'charge_invoice_number','unit_id','size_id','unit_price','amount','charge_invoice_qty','dt','dept'
    ];
    protected $table = "charge_invoice_items";
    public function unit(){
        return $this->hasMany('App\Egg_unit','id','unit_id');
    }

    public function size(){
        return $this->hasMany('App\Egg_size','id','size_id');
    }
    public function ci(){
    	return $this->belongsTo('App\Charge_invoice','charge_invoice_number','charge_invoice_number');
    }
    public function item(){
        return $this->belongsTo('App\Item','size_id','id');
    }

    
}
