<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    public function getImageAttribute($value = "") {
    	if(!empty($value)) {
    		return asset($value);
    	}
    	return $value;
    }

    public static function getCalendar($request)
    {
    	$response = [];
		$response['success'] = FALSE;
		$calendar = Calendar::get();
		if($calendar){

			$response['success'] = TRUE;
			$response['message']='Data found';
			$response['data']=$calendar;
			return $response;
		}
		$response['message'] = "No calendar found";
		return $response;
    }


}
