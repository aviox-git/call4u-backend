<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Calendar;
use App\UploadCalendarImages;
use App\Usercalender;
use Log;
use Validator;

class CalendarController extends Controller
{
    public function getCalendar(Request $request){  
		$response = Calendar::getCalendar($request);
        return $response;
	}
	
	public function uploadCalendarImages(Request $request){  
        $response = [];
		$response['success'] = FALSE;
        try {

            $rules['product_id'] = 'required';
            $rules['file.*'] = 'required';
            $validator = Validator::make($request->all(), $rules);
	        $validatorResponse = checkValidateRequest($validator);
	            if ($validatorResponse['success'] === FALSE){
	                return $validatorResponse;
	            }
         
         
            foreach( $request->file('file') as $key => $val) {
            	
		        if ($request->file('file')) {
					$file = $request->file('file')[$key];
					$image_file = $request->file('file')[$key];
					$upload_path = '/image/calander/';
					$destinationPath = public_path() . $upload_path;
					$fileName = time() . '-' . $file->getClientOriginalName();
					$image_res = $request->file('file')[$key]->move($destinationPath, $fileName);
					if ($image_res) {
						$data[] = [
						    'user_id' =>$request->user()->id,
						    'order_id'=>1,
						    'image' => $upload_path . $fileName,
						    'product_id'=>$request->post('product_id'),
						    'created_at' => date('Y-m-d H:i:s')
						];  
					}
		        }
            } 
            UploadCalendarImages::insert($data);
            $data=UploadCalendarImages::where('user_id',$request->user()->id)->whereDate('created_at',date('Y-m-d'))->get();

            $response['message'] = 'Calendar images successfully added';
            $response['data'] =  $data;
            $response['success'] = API_SUCCESS;
            $response['status'] = API_STATUS_OK;     
            
        } catch (\Exception $e) {
            Log::error($e->getTraceAsString());
            $response = apiResponseServerError($e);
        }
        return $response;
    }
    public function updateCalendarImages(Request $request){  
        $response = [];
		$response['success'] = FALSE;
	    try {
            $rules['id'] = 'required';
            $rules['file'] = 'required';
            $validator = Validator::make($request->all(), $rules);
	        $validatorResponse = checkValidateRequest($validator);
	            if ($validatorResponse['success'] === FALSE){
	                return $validatorResponse;
	            }
            $id =$request->post('id');
            $image = UploadCalendarImages::find($id);
               
               if ($image) {
					$file = $request->file('file');
					$image_file = $request->file('file');
					$upload_path = '/image/calander/';
					$destinationPath = public_path() . $upload_path;
					$fileName = time() . '-' . $file->getClientOriginalName();
					$image_res = $request->file('file')->move($destinationPath, $fileName);
					if ($image_res) {
                        UploadCalendarImages::where('id', $id)
					          ->update(['image' => $upload_path . $fileName]);	 
					}
					$response['message'] = 'Calendar images updated successfully';
		            $response['success'] = API_SUCCESS;
		            $response['status'] = API_STATUS_OK; 

		        }else{

		        	$response['message'] = "Data doesn't exist";
		            $response['success'] = API_SUCCESS;
		            $response['status'] = API_STATUS_OK;

		        }

	    }catch (\Exception $e) {
            Log::error($e->getTraceAsString());
            $response = apiResponseServerError($e);
        }
        return $response;

    }
    public function destroyCalendarImages(Request $request){  
		$response = [];
		$response['success'] = FALSE;
	    try {
            $rules['id'] = 'required';
            $validator = Validator::make($request->all(), $rules);
	        $validatorResponse = checkValidateRequest($validator);
	            if ($validatorResponse['success'] === FALSE){
	                return $validatorResponse;
	            }
	      
            $id =$request->post('id');
            $image = UploadCalendarImages::find($id);
		    if($image){
		        $image->delete();
		        $response['message'] = 'Calendar images deleted successfully';
	            $response['success'] = API_SUCCESS;
	            $response['status'] = API_STATUS_OK; 
            }else{
            	$response['message'] = "Calendar images doesn't exist";
	            $response['success'] = API_SUCCESS;
	            $response['status'] = API_STATUS_OK; 
            }
	    }catch (\Exception $e) {
            Log::error($e->getTraceAsString());
            $response = apiResponseServerError($e);
        }
        return $response;
	}

	public function getFreeShipping(Request $request)
    	{

			$response = [];
			$response['success'] = FALSE;
			$template = Usercalender::with('calender')->where('user_id' , '>', $request->user()->id)->get();
			
			if($template){

				$response['success'] = TRUE;
				$response['message']='Data found';
				$response['data']=$template;
				return $response;
				
			}
			$response['message'] = "Coupons not exist";
			return $response;
	    }


}
