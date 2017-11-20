<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Detail_type extends Model
{
    protected $fillable = ['detail_type_name'];

    public function chartofaccount(){
    	return $this->hasMany('App\Chart_of_account','detail_type_id','id');
    }
}
