<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mortality extends Model
{
    protected $fillable = ['month','year','weeksFrom','weeksTo','daysFrom','daysTo','mortality','batch_id','bv','heads_balance','amort','mortality_exp','preOP','bvBird'];

    public function weeks(){
        return $this->hasOne('App\Amortization','age_weeks','weeksTo');
    }
}
