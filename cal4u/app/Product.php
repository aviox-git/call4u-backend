<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function getImageAttribute($value = "") {
    	if(!empty($value)) {
    		return asset($value);
    	}
    	return $value;
    }
}
