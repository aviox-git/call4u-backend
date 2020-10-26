<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Usercalender extends Model
{
    protected $table = 'user_calenders';
     public function calender()
    {
        return $this->belongsTo('App\Calendar');
    }
}
