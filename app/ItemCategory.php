<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemCategory extends Model
{
    protected $guarded = [];

    public function category(){
    	return $this->belongsTo('App\ItemCategory','is_sub','id');
    }
}
