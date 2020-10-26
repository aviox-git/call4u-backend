<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventSelections extends Model
{
     protected $fillable = [
        'hebrew','ent_shabbat','city_id' ,'product_id','user_id'
    ];
      public $timestamps = true;
  //  table =event_selections
    protected $table = 'event_selections';
   
    public function eventdate()
    {
        return $this->hasMany('App\EventDescription' ,'event_selection_id');
    }
    public function annualevents()
    {
        return $this->hasMany('App\AnnualEventSelection' ,'event_selection_id', 'id');
    }
    public function calenderImages()
    {
        return $this->hasMany('App\UploadCalendarImages' ,'event_selection_id','id');
    }
    public function template()
    {
        return $this->belongsTo('App\Calendar','template_id','id');
    }
       public function product()
    {
        return $this->belongsTo('App\Product','product_id','id');
    }


}
