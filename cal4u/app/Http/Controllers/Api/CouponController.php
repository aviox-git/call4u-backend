<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Coupon;
use Validator;

class CouponController extends Controller
{
        public function getCoupon(Request $request)
    	{

			$response = [];
			$response['success'] = FALSE;
			$coupon = Coupon::where('expiry_date' , '>', date("Y/m/d"))->get();
			
			if($coupon){

				$response['success'] = TRUE;
				$response['message']='Data found';
				$response['data']=$coupon;
				return $response;
				
			}
			$response['message'] = "Coupons not exist";
			return $response;
	    }


    	public function checkCoupon(Request $request)
    	{

			$response = [];
			$response['success'] = FALSE;

			$rules['code'] = 'required';
            $validator = Validator::make($request->all(), $rules);
	        $validatorResponse = checkValidateRequest($validator);
	            if ($validatorResponse['success'] === FALSE){
	                return $validatorResponse;
	            }
	       
            $code=$request->post('code');

			$coupon = Coupon::where('code',$code)->where('expiry_date' , '>', date("Y/m/d"))->first();
			
			if($coupon){

				$response['success'] = TRUE;
				$response['message']='Data found';
				$response['data']=$coupon;
				return $response;
				
			}
			$response['message'] = "Coupon code is not valid";
			return $response;
	    }
}
