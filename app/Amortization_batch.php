<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Amortization_batch extends Model
{
    protected $fillable = ['batch_id','amortization_id','bb_value','amortization','netbook_value'];

    public function ams(){
        return $this->hasOne('App\Amortization','id','amortization_id');
    }
}
