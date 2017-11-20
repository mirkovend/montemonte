<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Signatory extends Model
{
    protected $fillable = ['fullname','position','report_type'];
}
