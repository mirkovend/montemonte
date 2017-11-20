<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chart_of_account extends Model
{
    protected $fillable = ['coa_title','detail_type_id','typical_balance','balance','is_sub'];

    public function detailtypes(){
        return $this->hasMany('App\Detail_type','id','detail_type_id');
    }
    public function detailtype(){
        return $this->belongsTo('App\Detail_type','detail_type_id','id');
    }

    public function sub(){
    	return $this->hasMany('App\Chart_of_account','is_sub','id');
    }


    public function parent()
    {
        return $this->belongsTo('App\Chart_of_account', 'is_sub','id');
    }

    public function children()
    {
        return $this->hasMany('App\Chart_of_account', 'is_sub','id');
    }

    public function transactions()
    {
        return $this->hasMany('App\Coa_transaction','coa_id','id');
    }
    public function transactions2()
    {
        return $this->hasMany('App\Coa_transaction','coa_id','id');
    }

    public function inventory(){
        return $this->hasMany('App\Item','coa_id','id');
    }   
}
