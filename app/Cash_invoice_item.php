<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cash_invoice_item extends Model
{
    protected $fillable = [
    'cash_invoice_number','unit_id','size_id','unit_price','amount','cash_invoice_qty','dt','dept'
    ];
    protected $table = "cash_invoice_items";
    public function unit(){
        return $this->hasMany('App\Egg_unit','id','unit_id');
    }

    public function size(){
        return $this->hasMany('App\Egg_size','id','size_id');
    }
    public function cash_inv(){
    	return $this->belongsTo('App\Cash_invoice','cash_invoice_number','cash_invoice_number');
    }

    public function item(){
        return $this->belongsTo('App\Item','size_id','id');
    }
}
