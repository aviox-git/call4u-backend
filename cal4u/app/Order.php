<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function calender()
    {
        return $this->hasMany('App\EventSelections' ,'order_id');
    }
 
}
