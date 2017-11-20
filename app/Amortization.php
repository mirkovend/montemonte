<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Amortization extends Model
{
    public function ams(){
        return $this->hasOne('App\Amortization_batch','amortization_id','id');
    }
}
