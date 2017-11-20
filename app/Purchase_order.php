<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Purchase_order extends Model
{
    protected $fillable = [
    'purchase_order_number','dt','supplier_id','term_id','purchase_order_to','purchase_order_type','return_dt','status','remarks',
    ];

    public function invoice(){
        return $this->hasMany('App\Purchase_order_item','purchase_order_number','purchase_order_number');
    }

    public function supplier(){
        return $this->hasMany('App\Supplier','id','supplier_id');
    }

    public function term(){
    	return $this->hasMany('App\Term','id','term_id');
    }

    public function supplier_one(){
        return $this->belongsTo('App\Supplier','supplier_id','id');
    }
}