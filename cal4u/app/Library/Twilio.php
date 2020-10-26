<?php

namespace App\Library;
use Twilio\Rest\Client;
use App\User;

class Twilio
{
	private static $twilio_number = "";
	private static $account_sid = "";
	private static $auth_token= "";
	private static $length= 6;
	public function __construct()
	{
		
	}



	public static function sendMessage($message, $phoneNumber)
	{
		self::$account_sid = "AC3eef4df462d5df8bcb0ac46c539489c7";
		self::$auth_token = "3e67fa24265c4817603f9eb4003bc1b1";
		self::$twilio_number = '+13852157963';

		$response = [];
		$response['success'] = FALSE;
		$otp = mt_rand(pow(10,(self::$length-1)),pow(10,self::$length)-1);
		if(empty($message)){
			User::where('mobile', $phoneNumber)->update(['otp' => $otp]);
			$message = "Your varifiation code is ". $otp;
		}
		$client = new Client(self::$account_sid, self::$auth_token);
		try
		{
			if($client->messages->create($phoneNumber,['from' => self::$twilio_number, 'body' => $message] )){
				$response['success'] = TRUE;
				$response['message'] = "Sent Successfully";
			}

		}catch(\Exception $e){
			//print_r($e->getMessage()); die;
			$response['message'] = "We didn't able to send otp in your register number.Please connect to Admin for the same Thank you";
		}
		return $response;
	}
}