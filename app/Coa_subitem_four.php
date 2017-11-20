<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coa_subitem_four extends Model
{
    protected $fillable = ['coa_sub3_id','coa_item_name','balance'];

    public function chartofsubaccount3(){
        return $this->hasOne('App\Coa_subitem_three','id','coa_sub3_id');
    }

}
