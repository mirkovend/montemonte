<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item_type extends Model
{
    protected $fillable = ['item_type_name'];

    public function item_type(){
        return $this->hasMany('App\Item','item_type_id','id');
    }
}
