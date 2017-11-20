<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coa_item extends Model
{
    protected $fillable = ['coa_id','coa_item_name','balance'];

    public function chartofaccount(){
        return $this->hasOne('App\Chart_of_account','id','coa_id');
    }
}
