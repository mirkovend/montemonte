<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coa_subitem_three extends Model
{
    protected $fillable = ['coa_sub2_id','coa_item_name','balance'];

    public function chartofsubaccount2(){
        return $this->hasOne('App\Coa_subitem','id','coa_sub2_id');
    }
}
