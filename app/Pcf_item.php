<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pcf_item extends Model
{
    protected $fillable = ['pcf_number','dt','description','account_item_id','amount','qty','unit_cost','dept'];

    public function account_item(){
        return $this->hasMany('App\Item','id','account_item_id');
    }
    public function item(){
        return $this->belongsTo('App\Item','account_item_id','id');
    }
}
