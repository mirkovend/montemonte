<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pettycash extends Model
{
    protected $fillable = ['pcf_number','pcf_to','dt','remarks','received_by'];

    public function invoice(){
        return $this->hasMany('App\Pcf_item','pcf_number','pcf_number');
    }

    public function supplier(){
        return $this->hasMany('App\Supplier','id','pcf_to');
    }
}
