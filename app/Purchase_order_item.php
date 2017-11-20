<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Purchase_order_item extends Model
{
    protected $fillable = [
    'purchase_order_number','dt','account_item_id','item_desc','item_label','item_qty','item_unit','item_price','item_total','rem_balance','rem_qty',
    ];

    public function account_item(){
    	return $this->hasMany('App\Item','id','account_item_id');
    }
    public function item(){
        return $this->belongsTo('App\Item','account_item_id','id');
    }

    public function voucher_item(){
    	return $this->hasMany('App\Voucher_item','purchase_order_id','id');
    }

    public function po(){
    	return $this->belongsTo('App\Purchase_order','purchase_order_number','purchase_order_number');
    }

}