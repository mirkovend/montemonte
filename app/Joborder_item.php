<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Joborder_item extends Model
{
    protected $fillable = ['joborder_number','dt','account_item_id','description','amount'];

    public function account_item(){
        return $this->hasMany('App\Item','id','account_item_id');
    }

    public function item(){
        return $this->belongsTo('App\Item','account_item_id','id');
    }

    public function joborder(){
        return $this->belongsTo('App\Joborder','joborder_number','joborder_number');
    }
}
