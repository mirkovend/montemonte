<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coa_subitem extends Model
{
    protected $fillable = ['coa_sub_id','coa_item_name','balance'];

    public function chartofsubaccount(){
        return $this->hasOne('App\Coa_item','id','coa_sub_id');
    }
}
