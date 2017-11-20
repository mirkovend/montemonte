<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = [
    	'item_id',
    	'amount_cost',
    	'ref_no',
    	'lm_quantity',
    	'dt',
        

    ];

    public function inv_items(){
    	 return $this->hasMany('App\Inventory_item','inventory_id','id');
    }

    public function item_account(){
        return $this->belongsTo('App\Item','item_id','id');
    }
}
