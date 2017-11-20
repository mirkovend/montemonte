<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Voucher_item extends Model
{
    protected $fillable = ['voucher_number','purchase_order_number','dt','supplier_id','account_item_id','item_rcv','purchase_order_id'];
    protected $table = "voucher_items";

    public function invoice(){
        return $this->hasMany('App\Purchase_order_item','purchase_order_number','purchase_order_number');
    }

    public function account_item(){
    	return $this->hasOne('App\Item','id','account_item_id');
    }
    public function item(){
        return $this->belongsTo('App\Item','account_item_id','id');
    }
    public function po_item(){
    	return $this->belongsTo('App\Purchase_order_item','purchase_order_id','id');
    }

    public function po(){
        return $this->belongsTo('App\Purchase_order','purchase_order_number','purchase_order_number');
    }

    public function voucher(){
        return $this->belongsTo('App\Voucher','voucher_number','voucher_number');
    }
}
