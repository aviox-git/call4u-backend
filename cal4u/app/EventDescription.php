<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventDescription extends Model
{
     protected $fillable = [
        'category_id','event_selection_id'
    ];

}
