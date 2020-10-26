<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Validator;
use App\Library\Twilio;
use Hash;

class UserController extends Controller
{
    public function signup(Request $request)
    {
        $response = User::signup($request);
        return $response;
    }

    public function login(Request $request)
    {   
       $response = User::login($request);
       return $response;
    }

    public function updateLatLng(Request $request) {
        $response = User::updateLatLng($request);
        return $response;
    }  

    public function updateMobile(Request $request) {
        $response = User::updateMobile($request);
        return $response;
    }

    public function forgotPassword(Request $request) {
        $response = User::forgotPassword($request);
        return $response;
    }

    public function socialLogin(Request $request) {
        $response = User::socialLogin($request);
        return $response;
    }

    public function sendOtp(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
      
        $phone = $request->get('mobile');
        $mobile = $phone;
                
        $message = $request->get('message');

        $rules['mobile'] = 'required|min:10|max:14';
        $validator = Validator::make($request->all(), $rules);
        $validatorResponse = checkValidateRequest($validator);
            if ($validatorResponse['success'] === FALSE){
                return $validatorResponse;
            }
        $response = Twilio::sendMessage($message=null, $mobile);
        return $response;
    }
    public function otpVerified(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $rules['mobile'] = 'required|min:10|max:14';
        $rules['otp'] = 'required|min:6|max:6';
        $validator = Validator::make($request->all(), $rules);
        $validatorResponse = checkValidateRequest($validator);
            if ($validatorResponse['success'] === FALSE){
                return $validatorResponse;
            }

        $phone = $request->get('mobile');
        $otp = $request->get('otp');

        $mobile =$phone;
                
           
        $user =User::where('mobile', $mobile)->first();
        
        if($user->otp == $otp){
            User::where('mobile', $phone)->update(['is_verified' => '1']);
            $response['success'] = True;
            $response['message'] = "Otp match";
            return $response;
        }

        $response['message'] = "Otp mismatch";
        return $response;
    }

  
   
}
