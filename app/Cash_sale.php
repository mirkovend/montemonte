<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cash_sale extends Model
{
     protected $guarded = [];

     public function sr(){
    	return $this->belongsTo('App\DailySales','dailysales_id','id');
    }
}
