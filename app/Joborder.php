<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Joborder extends Model
{
    protected $fillable = ['joborder_number','joborder_to','dt','status','dept'];

    public function invoice(){
        return $this->hasMany('App\Joborder_item','joborder_number','joborder_number');
    }

    public function supplier(){
        return $this->hasMany('App\Supplier','id','joborder_to');
    }

    public function supplier_one(){
        return $this->belongsTo('App\Supplier','joborder_to','id');
    }
}