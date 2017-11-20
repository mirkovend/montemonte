<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $fillable = ['dt','batch_number','number_heads','total_cost','pullet_price'];

    public function amort_batch(){
    	return $this->hasOne('App\Amortization_batch','batch_id','id');	
    }
    
}
