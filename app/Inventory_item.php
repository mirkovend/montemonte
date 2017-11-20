<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventory_item extends Model
{
	protected $fillable = [
    	'item_id',
    	'credit_quantity',
    	'debit_quantity','ave_cost',

    ];

    public function item_account(){
        return $this->belongsTo('App\Item','item_id','id');
    }
    public function inventory(){
        return $this->belongsTo('App\Inventory','inventory_id','id');
    }

}
